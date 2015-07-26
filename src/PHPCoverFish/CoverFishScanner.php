<?php

namespace DF\PHPCoverFish;

use DF\PHPCoverFish\Base\CoverFishScanner as CoverFishScannerBase;
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
 * @version   0.9.0
 */
class CoverFishScanner extends CoverFishScannerBase
{
    const APP_RELEASE_NAME = 'PHPCoverFish';
    const APP_RELEASE_STATE = 'alpha';

    const APP_VERSION_MAJOR = 0;
    const APP_VERSION_MINOR = 9;
    const APP_VERSION_BUILD = 1;

    const APP_LICENSE = 'http://www.opensource.org/licenses/MIT';
    const APP_SOURCE = 'http://github.com/dunkelfrosch/dfphpcoverfish/tree';

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct();

        $this->testSourcePath = $options['arg_test_file_src'];
        $this->verbose = $options['opt_mode_verbose'];
        $this->debug = $options['opt_mode_debug'];
        $this->stopOnError = $options['opt_stop_on_error'];
        $this->stopOnFailure = $options['opt_stop_on_failure'];
        $this->warningThreshold = $options['opt_warning_threshold'];
        $this->outputFormat = $options['opt_output_format'];
        $this->outputLevel = $options['opt_output_level'];
        $this->preventAnsiColors = $options['opt_no_ansi'];
        $this->preventEcho = $options['opt_output_no_echo'];

        $this->coverFishOutput = new CoverFishOutput(
            $this->outputFormat,
            $this->outputLevel,
            $this->preventAnsiColors,
            $this->preventEcho
        );
    }

    /**
     * set all used cover validator classes
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
        // covers ClassName::<public>
        $this->addValidator(new ValidatorClassNameMethodAccess($coverToken));
        // (...)
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

        $this->coverFishResult->setTaskFinishedAt(new \DateTime());

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
                /**
                 * @todo: add/handle coverFish warnings here!
                 *
                 * - testMethod without covers
                 * - testMethod without annotation
                 *
                 */
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
            $phpUnitTest->setFile($this->coverFishHelper->getFileNameFromPath($classFile));
            $phpUnitTest->setLoc($this->coverFishHelper->getLocOfTestMethod($methodData));

            // clear validator collection before use in next method
            $this->validatorCollection->clear();

            /** @var string $cover */
            foreach ($annotations['covers'] as $cover) {
                // step through all cover annotations in scanned method, ignore empty covers
                if (true === empty($cover)) {
                    // @todo: add/handle coverFish warnings here!
                    continue;
                }

                // load all available validator classes and instantiate them with given coversTag
                $this->setValidatorCollection($cover);

                // add summarized cover collection for complete file
                $this->phpUnitFile->addCover($cover);
            }

            $this->validateAndReturnMapping($phpUnitTest);

            $this->phpUnitFile->addTest($phpUnitTest);
            // @todo: not necessary!
            $this->phpUnitFile->setScanFinishedAt(new \DateTime());
        }

        $this->coverFishResult->addUnit($this->phpUnitFile);
    }
}