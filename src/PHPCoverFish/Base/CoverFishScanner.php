<?php

namespace DF\PHPCoverFish\Base;

use DF\PHPCoverFish\Common\ArrayCollection;
use DF\PHPCoverFish\Common\CoverFishOutput;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Validator\Base\CoverFishValidatorInterface;
use DF\PHPCoverFish\Common\CoverFishHelper;

use SebastianBergmann\FinderFacade\FinderFacade;

/**
 * Class CoverFishScanner
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.3
 */
class CoverFishScanner
{
    /**
     * @var string
     */
    protected $testSourcePath;

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
        $this->testSourcePath = $cliOptions['sys_scan_source'];
        $this->debug = $cliOptions['sys_debug'];
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

        $this->phpUnitFile
            ->setUsedClasses($this->coverFishHelper
                ->getUsedClassesInClass($this->phpUnitFile->getFile())
            );

        $this->phpUnitFile
            ->setParentClass($this->coverFishHelper
                ->getAttributeByKey('parent', $classData)
            );

        $this->phpUnitFile
            ->setCoversDefaultClass($this->coverFishHelper->getCoversDefaultClassUsable(
                $this->coverFishHelper
                    ->getAnnotationByKey($classData['docblock'], 'coversDefaultClass')
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
        $this->phpUnitTest = new CoverFishPHPUnitTest();
        $this->phpUnitTest->setSignature($methodData['signature']);
        $this->phpUnitTest->setName(str_replace('()', null, $this->phpUnitTest->getSignature()));
        $this->phpUnitTest->setVisibility($methodData['visibility']);
        $this->phpUnitTest->setLine($methodData['startLine']);
        $this->phpUnitTest->setFileAndPath($methodData['classFile']);
        $this->phpUnitTest->setFile($this->coverFishHelper->getFileNameFromPath($methodData['classFile']));
        $this->phpUnitTest->setLoc($this->coverFishHelper->getLocOfTestMethod($methodData));

        return $this->phpUnitTest;
    }

    /**
     * scan all files by given path recursively, if one php file will be provided within given path,
     * this file will be returned in finder format
     *
     * @param string $sourcePath
     *
     * @return array
     */
    protected function scanFilesInPath($sourcePath)
    {
        $filePattern = $this->baseFilePattern;
        if (strpos($sourcePath, str_replace('*', null, $filePattern))) {
            $filePattern = $this->coverFishHelper->getFileNameFromPath($sourcePath);
        }

        $facade = new FinderFacade(
            array($sourcePath),
            $this->baseFilePatternExclude,
            array($filePattern),
            array()
        );

        return $facade->findFiles();
    }
}