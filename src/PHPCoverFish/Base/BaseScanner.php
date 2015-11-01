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
 * Class BaseScanner
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   1.0.0
 */
class BaseScanner
{
    /**
     * @var string
     */
    protected $phpUnitXMLPath;

    /**
     * @var string
     */
    protected $phpUnitXMLFile;

    /**
     * @var string
     */
    protected $phpUnitTestSuite;

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
    protected $stopOnError = false;

    /**
     * @var bool
     */
    protected $stopOnFailure = false;

    /**
     * @var string
     */
    protected $filePattern = '*.*';

    /**
     * @var array
     */
    protected $filePatternExclude = array(
        '*.log',
        '*.txt',
        '*.md'
    );

    /**
     * @return boolean
     */
    public function isStopOnError()
    {
        return $this->stopOnError;
    }

    /**
     * @return boolean
     */
    public function isStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * @return string
     */
    public function getPhpUnitXMLPath()
    {
        return $this->phpUnitXMLPath;
    }

    /**
     * @return string
     */
    public function getPhpUnitXMLFile()
    {
        return $this->phpUnitXMLFile;
    }

    /**
     * @return string
     */
    public function getPhpUnitTestSuite()
    {
        return $this->phpUnitTestSuite;
    }

    /**
     * @return ArrayCollection
     */
    public function getValidatorCollection()
    {
        return $this->validatorCollection;
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
     * return the excluded path, remove the last backslash before return
     *
     * @codeCoverageIgnore
     *
     * @return String
     */
    public function getTestExcludePath()
    {
        return rtrim($this->testExcludePath, '/');
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
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->validatorCollection = new ArrayCollection();
        $this->coverFishHelper = new CoverFishHelper();
        $this->coverFishResult = new CoverFishResult();
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
     * if no "$this->phpUnitTestSuite" provided, first testSuite will be returned!
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

            if ((true === empty($this->phpUnitTestSuite)) || ($suiteName === $this->phpUnitTestSuite)) {
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
     * @param CoverFishValidatorInterface $validator
     *
     * @return ArrayCollection
     */
    public function addValidator(CoverFishValidatorInterface $validator)
    {
        $this->validatorCollection->add($validator);
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
        $result = $includedFiles = array();

        if (true === empty($excludePath)) {
            return $files;
        }

        foreach ($files as $filePath) {
            preg_match_all($this->coverFishHelper->getRegexPath($excludePath), $filePath, $result, PREG_SET_ORDER);
            if (true === empty($result)) {
                $includedFiles[] = $filePath;
            }
        }

        return $includedFiles;
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
        $filePattern = $this->filePattern;
        if (strpos($sourcePath, str_replace('*', null, $filePattern))) {
            $filePattern = $this->coverFishHelper->getFileNameFromPath($sourcePath);
        }

        $facade = new FinderFacade(
            array($sourcePath),
            $this->filePatternExclude,
            array($filePattern)
        );

        return $this->removeExcludedPath($facade->findFiles(), $this->testExcludePath);
    }
}