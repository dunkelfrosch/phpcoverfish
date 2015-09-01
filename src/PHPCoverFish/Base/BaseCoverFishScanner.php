<?php

namespace DF\PHPCoverFish\Base;

use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Validator\Base\BaseCoverFishValidatorInterface;

/**
 * Class BaseCoverFishScanner
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   0.9.9
 */
class BaseCoverFishScanner extends BaseScanner
{
    /**
     * @var string
     */
    protected $filePattern = '*.php';

    /**
     * @var array
     */
    protected $filePatternExclude = array(
        '*.log',
        '*.js',
        '*.html',
        '*.twig',
        '*.css',
        '*.scss',
        '*.less',
        '*.txt',
        '*.md',
        '*.yml',
        '*.xml'
    );

    /**
     * @param array $cliOptions
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $cliOptions)
    {
        parent::__construct();
        $this->initCoverFishScanner($cliOptions);

        if (true === $this->coverFishHelper->checkFileExist($this->phpUnitXMLFile)) {
            $this->setConfigFromPHPUnitConfigFile();
        }

        if (true === $this->checkSourceAutoload($this->testAutoloadPath)) {
            include(sprintf('%s', $this->testAutoloadPath));
        }
    }

    /**
     * @param array $cliOptions
     *
     * @codeCoverageIgnore
     */
    private function initCoverFishScanner(array $cliOptions)
    {
        // fetch all necessary coverfish parameter by optional given raw-data first
        $this->testAutoloadPath = $cliOptions['raw_scan_autoload_file'];
        $this->testSourcePath = $cliOptions['raw_scan_source'];
        $this->testExcludePath = $cliOptions['raw_scan_exclude_path'];

        // fetch additional system/app parameter
        $this->debug = $cliOptions['sys_debug'];
        $this->warningThreshold = $cliOptions['sys_warning_threshold'];
        $this->phpUnitXMLFile = $cliOptions['sys_phpunit_config'];
        $this->phpUnitTestSuite = $cliOptions['sys_phpunit_config_test_suite'];

        $this->coverFishResult->setStopOnFailure((bool) $cliOptions['sys_stop_on_failure']);
        $this->coverFishResult->setStopOnError((bool) $cliOptions['sys_stop_on_error']);
    }

    /**
     * update/set configuration using phpunit xml file
     */
    public function setConfigFromPHPUnitConfigFile()
    {
        try {

            /** @var \SimpleXMLElement $xmlDocument */
            $xmlDocument = simplexml_load_file($this->phpUnitXMLFile);

            $this->phpUnitXMLPath = $this->coverFishHelper->getPathFromFileNameAndPath($this->phpUnitXMLFile);
            $this->testAutoloadPath = sprintf('%s%s', $this->phpUnitXMLPath, $this->getAttributeFromXML('bootstrap', $xmlDocument));
            $this->testSourcePath = sprintf('%s%s', $this->phpUnitXMLPath, $this->getTestSuitePropertyFromXML('directory', $xmlDocument));
            $this->testExcludePath = sprintf('%s%s', $this->phpUnitXMLPath, $this->getTestSuitePropertyFromXML('exclude', $xmlDocument));

        } catch (\Exception $e) {

            echo (sprintf('parse error loading phpunit config file "%s"!', $this->phpUnitXMLFile));
            echo (sprintf('-> message: %s', $e->getMessage()));

        }
    }

    /**
     * @param CoverFishPHPUnitTest $phpUnitTest
     *
     * @return CoverFishPHPUnitTest
     */
    public function validateAndReturnMapping(CoverFishPHPUnitTest $phpUnitTest)
    {
        /** @var BaseCoverFishValidatorInterface $validator */
        foreach ($this->validatorCollection as $validator) {

            if (false === $validator->validate()) {
                /**
                 * @todo: set 'special' validation error for covers annotation mismatch by exotic/unsupported covers
                 */
                continue;
            }

            $phpUnitTest->addCoverMapping($validator->getMapping($this->phpUnitFile));
        }

        return $phpUnitTest;
    }

    /**
     * @param string $className
     * @param array  $classData
     *
     * @return CoverFishPHPUnitFile
     */
    public function setPHPUnitTestMetaData($className, $classData)
    {
        $this->phpUnitFile->setClassName($className);

        $this->phpUnitFile
            ->setFile($this->coverFishHelper
                ->getAttributeByKey('file', $classData)
            );

        $this->phpUnitFile
            ->setClassNameSpace($this->coverFishHelper
                ->getAttributeByKey('namespace', $classData['package'])
            );

        $usedClassesInFile = $this->coverFishHelper->getUsedClassesInClass($this->phpUnitFile->getFile());
        if (is_array($usedClassesInFile)) {
            $this->phpUnitFile->setUsedClasses($usedClassesInFile);
        }

        $this->phpUnitFile
            ->setParentClass($this->coverFishHelper
                ->getAttributeByKey('parent', $classData)
            );

        $coversDefaultClass = $this->coverFishHelper->getAnnotationByKey($classData['docblock'], 'coversDefaultClass');
        if (is_array($coversDefaultClass)) {
            $this->phpUnitFile
                ->setCoversDefaultClass($this->coverFishHelper->getCoversDefaultClassUsable(
                    $coversDefaultClass
                ));
        }

        return $this->phpUnitFile;
    }

    /**
     * fetch all relevant unit test data from incoming methodDataBlock array
     *
     * @param array $methodData
     *
     * @return CoverFishPHPUnitTest
     */
    public function setPHPUnitTestByMethodData(array $methodData)
    {
        /** @var string $classFileAndPath */
        $classFileAndPath = $methodData['classFile'];

        $this->phpUnitTest = new CoverFishPHPUnitTest();
        $this->phpUnitTest->setSignature($methodData['signature']);
        $this->phpUnitTest->setVisibility($methodData['visibility']);
        $this->phpUnitTest->setLine($methodData['startLine']);
        $this->phpUnitTest->setFileAndPath($classFileAndPath);
        $this->phpUnitTest->setFile($this->coverFishHelper->getFileNameFromPath($classFileAndPath));
        $this->phpUnitTest->setLoc($this->coverFishHelper->getLocOfTestMethod($methodData));

        return $this->phpUnitTest;
    }

    /**
     * @param array $classData
     *
     * @return CoverFishPHPUnitTest
     */
    public function setPHPUnitTestByClassData($classData)
    {
        $this->setPHPUnitTestMetaData($classData['className'], $classData);
        $classFileAndPath = $classData['classFile'];
        $this->phpUnitTest = new CoverFishPHPUnitTest();
        $this->phpUnitTest->setFileAndPath($classFileAndPath);
        $this->phpUnitTest->setFile($this->coverFishHelper->getFileNameFromPath($classFileAndPath));

        return $this->phpUnitTest;
    }
}