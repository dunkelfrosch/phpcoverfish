<?php

namespace DF\PHPCoverFish\Base;

use DF\PHPCoverFish\Common\ArrayCollection;
use DF\PHPCoverFish\Common\CoverFishOutput;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Validator\Base\BaseCoverFishValidatorInterface as CoverFishValidatorInterface;
use SebastianBergmann\FinderFacade\FinderFacade;

/**
 * Class CoverFishScanner
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.7
 */
class BaseCoverFishScanner
{
    /**
     * @var string
     */
    protected $phpUnitConfigPath;

    /**
     * @var string
     */
    protected $phpUnitConfigFile;

    /**
     * @var string
     */
    protected $phpUnitConfigTestSuite;

    /**
     * @var string
     */
    protected $testSourcePath;

    /**
     * @var String
     */
    protected $testExcludePath;

    /**
     * @var String
     */
    protected $testAutoloadPath;

    /**
     * @var bool
     */
    protected $stopOnError = false;

    /**
     * @var bool
     */
    protected $stopOnFailure = false;

    /**
     * @var int
     */
    protected $warningThreshold = 99;

    /**
     * @var bool
     */
    protected $passes;

    /**
     * @var array
     */
    protected $usedClasses;

    /**
     * @var CoverFishPHPUnitFile
     */
    protected $phpUnitFile;

    /**
     * @var CoverFishPHPUnitTest
     */
    protected $phpUnitTest;

    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * @var CoverFishResult
     */
    protected $coverFishResult;

    /**
     * @var CoverFishOutput
     */
    protected $coverFishOutput;

    /**
     * @var ArrayCollection
     */
    protected $validatorCollection;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $baseFilePattern = '*.php';

    /**
     * @var array
     */
    protected $baseFilePatternExclude = array(
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
     * @return ArrayCollection
     */
    public function getValidatorCollection()
    {
        return $this->validatorCollection;
    }

    /**
     * @return string
     */
    public function getPhpUnitConfigPath()
    {
        return $this->phpUnitConfigPath;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getPhpUnitConfigFile()
    {
        return $this->phpUnitConfigFile;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getPhpUnitConfigTestSuite()
    {
        return $this->phpUnitConfigTestSuite;
    }

    /**
     * @todo rename this variable to (get)TestSourcePathOrFile
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getTestSourcePath()
    {
        return $this->testSourcePath;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return String
     */
    public function getTestExcludePath()
    {
        return $this->testExcludePath;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return String
     */
    public function getTestAutoloadPath()
    {
        return $this->testAutoloadPath;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return CoverFishPHPUnitFile
     */
    public function getPhpUnitFile()
    {
        return $this->phpUnitFile;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return CoverFishPHPUnitTest
     */
    public function getPhpUnitTest()
    {
        return $this->phpUnitTest;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return CoverFishHelper
     */
    public function getCoverFishHelper()
    {
        return $this->coverFishHelper;
    }

    /**
     * @param CoverFishPHPUnitTest $phpUnitTest
     *
     * @codeCoverageIgnore
     */
    public function setPhpUnitTest($phpUnitTest)
    {
        $this->phpUnitTest = $phpUnitTest;
    }

    /**
     * @param array $cliOptions
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $cliOptions)
    {
        $this->debug = $cliOptions['sys_debug'];

        $this->phpUnitConfigFile = $cliOptions['sys_phpunit_config'];
        $this->phpUnitConfigTestSuite = $cliOptions['sys_phpunit_config_test_suite'];

        // fetch all necessary coverfish parameter by optional given raw-data first
        $this->testAutoloadPath = $cliOptions['raw_scan_autoload_file'];
        $this->testSourcePath = $cliOptions['raw_scan_source'];
        $this->testExcludePath = $cliOptions['raw_scan_exclude_path'];

        $this->stopOnError = $cliOptions['sys_stop_on_error'];
        $this->stopOnFailure = $cliOptions['sys_stop_on_failure'];
        $this->warningThreshold = $cliOptions['sys_warning_threshold'];

        $this->validatorCollection = new ArrayCollection();
        $this->coverFishHelper = new CoverFishHelper();
        $this->coverFishResult = new CoverFishResult();

        if (true === $this->coverFishHelper->checkFileExist($this->phpUnitConfigFile)) {
            $this->setConfigFromPHPUnitConfigFile();
        }

        if (true === $this->checkSourceAutoload($this->testAutoloadPath)) {
            include(sprintf('%s', $this->testAutoloadPath));
        }
    }

    /**
     * check existence of given autoload file (raw/psr-0/psr-4)
     *
     * @param string $autoloadFile
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkSourceAutoload($autoloadFile)
    {
        if (false === $this->coverFishHelper->checkFileExist($autoloadFile)) {
            throw new \Exception(sprintf('autoload file "%s" not found! please define your autoload.php file to use (e.g. ../app/autoload.php in symfony)', $autoloadFile));
        }

        return true;
    }

    /**
     * get a testSuite main attribute of given phpunit xml file (like name, bootstrap ...)
     *
     * @param string            $attribute
     * @param \SimpleXMLElement $xmlDocument
     *
     * @return bool|string
     */
    public function getAttributeFromXML($attribute, \SimpleXMLElement $xmlDocument)
    {
        /** @var \SimpleXMLElement $value */
        foreach ($xmlDocument->attributes() as $key => $value) {
            /** @var \SimpleXMLElement $attribute */
            if ($attribute === $key) {
                return (string) ($this->xmlToArray($value)[0]);
            }
        }

        return false;
    }

    /**
     * function will return the first testSuite directory found in testSuites node-block.
     * if no "$this->phpUnitConfigTestSuite" provided, first testSuite will be returned!
     *
     * @param \SimpleXMLElement $xmlDocumentNodes
     *
     * @return bool|\SimpleXMLElement
     */
    public function getTestSuiteNodeFromXML(\SimpleXMLElement $xmlDocumentNodes) {

        /** @var \SimpleXMLElement $suite */
        foreach ($xmlDocumentNodes->testsuites->testsuite as $suite) {

            if (false === $suiteName = $this->getAttributeFromXML('name', $suite)) {
                continue;
            }

            if ((true === empty($this->phpUnitConfigTestSuite)) || ($suiteName === $this->phpUnitConfigTestSuite)) {
                return $suite;
            }
        }

        return false;
    }

    /**
     * get a specific property from named testSuite node (like "exclude" or "directory")
     *
     * @param string            $property
     * @param \SimpleXMLElement $xmlDocumentNodes
     *
     * @return bool|string
     */
    public function getTestSuitePropertyFromXML($property, \SimpleXMLElement $xmlDocumentNodes)
    {
        /** @var \SimpleXMLElement $suite */
        $suite = $this->getTestSuiteNodeFromXML($xmlDocumentNodes);
        if (false === empty($suite) && property_exists($suite, $property)) {
            return (string) ($this->xmlToArray($suite->$property)[0]);
        }

        return false;
    }

    /**
     * @param \SimpleXMLElement|array $xmlObject
     * @param array                   $output
     *
     * @return array
     */
    public function xmlToArray($xmlObject, $output = array())
    {
        foreach ((array) $xmlObject as $index => $node) {
            $output[$index] = ($node instanceof \SimpleXMLElement || is_array($node))
                ? $this->xmlToArray($node)
                : $node;
        }

        return $output;
    }

    /**
     * update/set configuration using phpunit xml file
     */
    public function setConfigFromPHPUnitConfigFile()
    {
        try {

            /** @var \SimpleXMLElement $xmlDocument */
            $xmlDocument = simplexml_load_file($this->phpUnitConfigFile);

            $this->phpUnitConfigPath = $this->coverFishHelper->getPathFromFileNameAndPath($this->phpUnitConfigFile);
            $this->testAutoloadPath = sprintf('%s%s', $this->phpUnitConfigPath, $this->getAttributeFromXML('bootstrap', $xmlDocument));
            $this->testSourcePath = sprintf('%s%s', $this->phpUnitConfigPath, $this->getTestSuitePropertyFromXML('directory', $xmlDocument));
            $this->testExcludePath = sprintf('%s%s', $this->phpUnitConfigPath, $this->getTestSuitePropertyFromXML('exclude', $xmlDocument));

            /*echo sprintf('using phpunit config file "%s"%s%s', $this->phpUnitConfigFile, PHP_EOL, PHP_EOL);
            echo sprintf('- autoload file: %s%s', $this->testAutoloadPath, PHP_EOL);
            echo sprintf('- test source path for scan: %s%s', $this->testSourcePath, PHP_EOL);
            echo sprintf('- exclude test source path: %s%s%s', $this->testExcludePath, PHP_EOL, PHP_EOL);*/

        } catch (\Exception $e) {

            echo sprintf('parse error loading phpunit config file "%s"!%s -> message: %s',
                $this->phpUnitConfigFile,
                PHP_EOL,
                $e->getMessage());
        }
    }

    /**
     * @param CoverFishValidatorInterface $validator
     *
     * @return ArrayCollection
     */
    public function addValidator(CoverFishValidatorInterface $validator)
    {
        $this->validatorCollection->add($validator);
    }

    /**
     * @param CoverFishPHPUnitTest $phpUnitTest
     *
     * @return CoverFishPHPUnitTest
     */
    public function validateAndReturnMapping(CoverFishPHPUnitTest $phpUnitTest)
    {
        /** @var CoverFishValidatorInterface $validator */
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
     * @param array $methodData
     *
     * @return CoverFishPHPUnitTest
     */
    public function setPHPUnitTestData(array $methodData)
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
     * workaround for missing/buggy path exclude in symfony finder class
     *
     * @param array  $files
     * @param string $excludePath
     *
     * @return array
     */
    public function removeExcludedPath(array $files, $excludePath)
    {
        $finalPath = array();

        if (true === empty($excludePath)) {
            return $files;
        }

        foreach ($files as $filePath) {
            preg_match_all($this->coverFishHelper->getRegexPath($excludePath), $filePath, $result, PREG_SET_ORDER);
            if (true === empty($result)) {
                $finalPath[] = $filePath;
            }
        }

        return $finalPath;
    }

    /**
     * scan all files by given path recursively, if one php file will be provided within given path,
     * this file will be returned in finder format
     *
     * @param string $sourcePath
     *
     * @return array
     */
    public function scanFilesInPath($sourcePath)
    {
        $filePattern = $this->baseFilePattern;
        if (strpos($sourcePath, str_replace('*', null, $filePattern))) {
            $filePattern = $this->coverFishHelper->getFileNameFromPath($sourcePath);
        }

        $facade = new FinderFacade(
            array($sourcePath),
            $this->baseFilePatternExclude,
            array($filePattern),
            array(),
            array()
        );

        return $this->removeExcludedPath($facade->findFiles(), $this->testExcludePath);
    }
}