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
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.2
 */
class CoverFishScanner extends CoverFishScannerBase
{
    const APP_RELEASE_NAME = 'PHPCoverFish';
    const APP_RELEASE_STATE = 'alpha';

    const APP_VERSION_MAJOR = 0;
    const APP_VERSION_MINOR = 9;
    const APP_VERSION_BUILD = 2;

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
     * set all available cover validators
     *
     * @param $coverToken
     */
    private function setValidatorCollection($coverToken)
    {
        // covers ClassName::methodName
        $this->addValidator(new ValidatorClassNameMethodName($coverToken));
        // covers ::methodName
        $this->addValidator(new ValidatorMethodName($coverToken));
        // covers ClassName
        $this->addValidator(new ValidatorClassName($coverToken));
        // covers ClassName::<public|protected|private|!public|!protected|!private>
        $this->addValidator(new ValidatorClassNameMethodAccess($coverToken));
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
            $this->analyseClass($classData, $className, $file);
        }
    }

    /**
     * scan (test) class and add result to our coverFishResultCollection
     *
     * @param array  $classData
     * @param string $className
     * @param string $classFile
     *
     * @return array
     */
    public function analyseClass(array $classData, $className, $classFile)
    {
        // add class meta information firstly
        $this->setPHPUnitTestMetaDetails($className, $classData);

        // iterate through all available methods in testClass
        foreach ($classData['methods'] as $methodName => $methodData) {

            // ignore docBlock free testMethods!
            if (false === array_key_exists('docblock', $methodData)) {
                continue;
            }

            /** @var array $annotations */
            $annotations = $this->coverFishHelper->parseMethodDocBlock($methodData['docblock']);

            /** @var CoverFishPHPUnitTest $phpUnitTest */
            // @todo: source out and rename unitTest to more specific one
            $phpUnitTest = new CoverFishPHPUnitTest();
            $phpUnitTest->setName($methodName);
            $phpUnitTest->setVisibility($methodData['visibility']);
            $phpUnitTest->setLine($methodData['startLine']);
            $phpUnitTest->setSignature($methodData['signature']);
            $phpUnitTest->setFileAndPath($classFile);
            $phpUnitTest->setFile($this->coverFishHelper->getFileNameFromPath($classFile));
            $phpUnitTest->setLoc($this->coverFishHelper->getLocOfTestMethod($methodData));

            // clear validator collection before use in next method
            $this->validatorCollection->clear();

            /** @var string $cover */
            foreach ($annotations['covers'] as $cover) {
                // step through all cover annotations in scanned method, ignore empty covers
                if (true === empty($cover)) {
                    // @todo: add/handle additional coverFish warnings here!
                    continue;
                }

                // load all available validator classes and instantiate them with given coversTag
                $this->setValidatorCollection($cover);

                // add summarized cover collection for complete file
                $this->phpUnitFile->addCover($cover);
            }

            // write validation result to phpUnitTest directly
            $phpUnitTest = $this->validateAndReturnMapping($phpUnitTest);
            // add final test structure and result to phpUnitFile
            $this->phpUnitFile->addTest($phpUnitTest);
        }

        $this->coverFishResult->addUnit($this->phpUnitFile);
    }
}