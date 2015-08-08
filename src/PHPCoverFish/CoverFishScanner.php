<?php

namespace DF\PHPCoverFish;

use DF\PHPCoverFish\Base\CoverFishScanner as CoverFishScannerBase;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Validator\ValidatorClassName;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodName;
use DF\PHPCoverFish\Validator\ValidatorMethodName;
use DF\PHPCoverFish\Common\CoverFishOutput;
use \PHP_Token_Stream;

/**
 * Class CoverFishScanner
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.3
 */
class CoverFishScanner extends CoverFishScannerBase
{
    const APP_RELEASE_NAME = 'PHPCoverFish';
    const APP_RELEASE_STATE = 'alpha';

    const APP_VERSION_MAJOR = 0;
    const APP_VERSION_MINOR = 9;
    const APP_VERSION_BUILD = 3;

    /**
     * @param array $cliOptions
     * @param array $outputOptions
     */
    public function __construct(array $cliOptions, array $outputOptions)
    {
        parent::__construct($cliOptions);
        $this->coverFishOutput = new CoverFishOutput($outputOptions);
    }

    /**
     * init all available cover validator classes use incoming coverToken as parameter
     *
     * @param $coverToken
     */
    private function validateCodeCoverage($coverToken)
    {
        // covers ClassName::methodName
        $this->addValidator(new ValidatorClassNameMethodName($coverToken));
        // covers ::methodName
        $this->addValidator(new ValidatorMethodName($coverToken));
        // covers ClassName
        $this->addValidator(new ValidatorClassName($coverToken));
        // covers ClassName::<public|protected|private|!public|!protected|!private>
        $this->addValidator(new ValidatorClassNameMethodAccess($coverToken));
        // save used coverToken inside cover annotation collection for each phpUnitTest
        $this->phpUnitTest->addCoverAnnotation($coverToken);
    }

    /**
     * @return string
     */
    public function analysePHPUnitFiles()
    {
        $testFiles = $this->scanFilesInPath($this->testSourcePath);
        foreach ($testFiles as $file) {
            $this->analyseClassesInFile($file);
        }

        return $this->coverFishOutput->writeResult($this->coverFishResult);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function analyseClassesInFile($file)
    {
        $ts = new PHP_Token_Stream($file);
        $this->phpUnitFile = new CoverFishPHPUnitFile();
        foreach ($ts->getClasses() as $className => $classData) {
            $classData['className'] = $className;
            $classData['classFile'] = $file;
            $this->analyseClass($classData);
        }
    }

    /**
     * scan (test) class and add result to our coverFishResultCollection
     *
     * @param array  $classData
     *
     * @return array
     */
    public function analyseClass(array $classData)
    {
        // add class meta information firstly
        $this->setPHPUnitTestMetaData($classData['className'], $classData);

        // iterate through all available methods in testClass
        foreach ($classData['methods'] as $methodName => $methodData) {
            if (false === array_key_exists('docblock', $methodData)) {
                continue;
            }

            $this->validatorCollection->clear();

            // transfer classFile information to methodData
            $methodData['classFile'] = $classData['classFile'];
            // generate our phpUnitTest data structure
            $this->phpUnitTest = $this->setPHPUnitTestData($methodData);
            /** @var array $annotations */
            $annotations = $this->coverFishHelper->parseMethodDocBlock($methodData['docblock']);

            /** @var string $cover */
            foreach ($annotations['covers'] as $cover) {
                if (true === empty($cover)) {
                    continue;
                }

               $this->validateCodeCoverage($cover);
            }

            // add final test structure and result to phpUnitFile
            $this->phpUnitFile->addTest($this->validateAndReturnMapping($this->phpUnitTest));
        }

        // add final phpUnitFile structure including mapping result to our coverFishResult
        $this->coverFishResult->addUnit($this->phpUnitFile);
    }
}