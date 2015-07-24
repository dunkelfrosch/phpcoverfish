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
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
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
    protected $verbose = false;

    /**
     * @var bool
     */
    protected $debug = false;

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
     * @var string
     */
    protected $outputFormat;

    /**
     * @var string
     */
    protected $outputLevel;

    /**
     * @var bool
     */
    protected $preventAnsiColors = false;

    /**
     * @var bool
     */
    protected $preventEcho = false;


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
     * our base class constructor
     */
    public function __construct()
    {
        $this->phpUnitFile = new CoverFishPHPUnitFile();
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
    protected function setPHPUnitTestMetaDetails($className, $classData)
    {
        $this->phpUnitFile
            ->setClassName($className);

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
            )
            );

        return $this->phpUnitFile;
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
        $filePattern = '*.php';
        if (strpos($sourcePath, '.php')) {
            $filePattern = $this->coverFishHelper->getFileNameFromPath($sourcePath);
        }

        $facade = new FinderFacade(
            array($sourcePath),
            array('*.log','*.js','*.html','*.twig','*.css','*.scss','*.less','*.txt','*.md','*.yml','*.xml'),
            array($filePattern),
            array()
        );

        return $facade->findFiles();
    }
}