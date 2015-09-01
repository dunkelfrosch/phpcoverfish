<?php

namespace DF\PHPCoverFish;

use DF\PHPCoverFish\Base\BaseCoverFishScanner;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishMessageWarning;
use DF\PHPCoverFish\Validator\ValidatorClassName;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodName;
use DF\PHPCoverFish\Validator\ValidatorMethodName;
use DF\PHPCoverFish\Common\CoverFishOutput;
use Symfony\Component\Console\Output\OutputInterface;
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
 * @version   0.9.9
 */
class CoverFishScanner extends BaseCoverFishScanner
{
    const APP_RELEASE_NAME = 'PHPCoverFish';
    const APP_RELEASE_STATE = 'beta1';

    const APP_VERSION_MAJOR = 0;
    const APP_VERSION_MINOR = 9;
    const APP_VERSION_BUILD = 9;

    /**
     * @param array           $cliOptions
     * @param array           $outputOptions
     * @param OutputInterface $output
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $cliOptions, array $outputOptions, OutputInterface $output)
    {
        parent::__construct($cliOptions);

        $this->coverFishOutput = new CoverFishOutput($outputOptions, $output, $this);
    }

    /**
     * init all available cover validator classes use incoming coverToken as parameter
     *
     * @param $coverToken
     */
    public function validateCodeCoverage($coverToken)
    {
        // covers ClassName::methodName
        $this->addValidator(new ValidatorClassNameMethodName($coverToken));
        // covers ::methodName
        $this->addValidator(new ValidatorMethodName($coverToken));
        // covers ClassName
        $this->addValidator(new ValidatorClassName($coverToken));
        // covers ClassName::accessor (for public, protected, private, !public, !protected, !private)
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
     * @todo move this to our coverFishHelper class
     *
     * @param string $methodSignature
     *
     * @return bool
     */
    public function isValidTestMethod($methodSignature)
    {
        $result = array();

        if (preg_match_all('/(?P<prefix>^test)/', $methodSignature, $result)
        && (array_key_exists('prefix', $result) && false === empty($result['prefix']))) {
            return true;
        }

        return false;
    }

    /**
     * @param array $coverAnnotations
     */
    public function analyseCoverAnnotations($coverAnnotations)
    {
        // add warning for empty
        if (!array_key_exists('covers', $coverAnnotations) || empty($coverAnnotations['covers']) ) {
            if ($this->isValidTestMethod($this->phpUnitTest->getSignature())) {
                $this->coverFishResult->addWarningCount();
                $this->coverFishResult->addWarning(new CoverFishMessageWarning(CoverFishMessageWarning::PHPUNIT_NO_COVERAGE_FOR_METHOD));
            }
        }

        /** @var string $cover */
        foreach ($coverAnnotations['covers'] as $cover) {
            if (true === empty($cover)) {
                // @todo: add new empty cover error instead of step over!
                continue;
            }

            $this->validateCodeCoverage($cover);
        }

        $this->phpUnitFile->addTest($this->validateAndReturnMapping($this->phpUnitTest));
    }

    /**
     * scan class doc annotation block
     *
     * @param array $classData
     */
    public function analyseClassPHPDocAnnotation(array $classData)
    {
        $this->phpUnitTest = $this->setPHPUnitTestByClassData($classData);
        // scan class cover annotation
        $this->analyseCoverAnnotations($this->coverFishHelper
            ->parseCoverAnnotationDocBlock($classData['docblock'])
        );
    }

    /**
     * scan method doc annotation block
     *
     * @param array $methodData
     */
    public function analyseMethodPHPDocAnnotation(array $methodData)
    {
        $this->phpUnitTest = $this->setPHPUnitTestByMethodData($methodData);
        // scan method cover annotation
        $this->analyseCoverAnnotations($this->coverFishHelper
            ->parseCoverAnnotationDocBlock($methodData['docblock'])
        );
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
        $this->analyseClassPHPDocAnnotation($classData);
        // iterate through all available methods in give test class
        foreach ($classData['methods'] as $methodName => $methodData) {
            $this->validatorCollection->clear();
            if (false === array_key_exists('docblock', $methodData)) {
                continue;
            }

            $methodData['classFile'] = (string) $classData['classFile'];
            $this->analyseMethodPHPDocAnnotation($methodData);
        }

        // add final phpUnitFile structure including mapping result to our coverFishResult
        $this->coverFishResult->addUnit($this->phpUnitFile);
    }
}