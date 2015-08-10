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
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.4
 */
class BaseCoverFishScanner
{
    /**
     * @var string
     */
    protected $testSourcePath;

    /**
     * @var String
     */
    protected $testExcludePath;

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
     * @param array $cliOptions
     */
    public function __construct(array $cliOptions)
    {
        $this->debug = $cliOptions['sys_debug'];

        $this->testSourcePath = $cliOptions['sys_scan_source'];
        $this->testExcludePath = $cliOptions['sys_exclude_path'];

        $this->stopOnError = $cliOptions['sys_stop_on_error'];
        $this->stopOnFailure = $cliOptions['sys_stop_on_failure'];
        $this->warningThreshold = $cliOptions['sys_warning_threshold'];

        $this->coverFishHelper = new CoverFishHelper();
        $this->coverFishResult = new CoverFishResult();
        $this->validatorCollection = new ArrayCollection();
    }

    /**
     * @param CoverFishValidatorInterface $validator
     *
     * @return ArrayCollection
     */
    protected function addValidator(CoverFishValidatorInterface $validator)
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
    protected function setPHPUnitTestMetaData($className, $classData)
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

        $this->phpUnitFile
            ->setCoversDefaultClass($this->coverFishHelper->getCoversDefaultClassUsable(
                $this->coverFishHelper->getAnnotationByKey($classData['docblock'], 'coversDefaultClass')
            ));

        return $this->phpUnitFile;
    }

    /**
     * @param array $methodData
     *
     * @return CoverFishPHPUnitTest
     */
    protected function setPHPUnitTestData(array $methodData)
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
     * @param string $inputPath
     *
     * @return string
     */
    private function getRegexPath($inputPath)
    {
        $path = str_replace('/', '\/', $inputPath);

        return sprintf('/%s/', $path);
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

        if (true === empty($excludePath) || false === $this->coverFishHelper->checkPath($excludePath)) {
            return $files;
        }

        foreach ($files as $filePath) {
            preg_match_all($this->getRegexPath($excludePath), $filePath, $result, PREG_SET_ORDER);
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