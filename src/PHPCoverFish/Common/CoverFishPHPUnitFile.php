<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishPHPUnitFile, wrapper for all phpUnit testClass files
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.5
 */
final class CoverFishPHPUnitFile
{
    /**
     * @var string
     */
    private $coversDefaultClass;

    /**
     * @var array
     */
    private $usedClasses;

    /**
     * @var string
     */
    private $parentClass;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $classNameSpace;

    /**
     * @var string
     */
    private $className;

    /**
     * @var ArrayCollection
     */
    private $tests;

    /**
     * collector for cover annotation used in unit test class file class phpdoc
     *
     * @var ArrayCollection
     */
    private $classCovers;

    /**
     * @var \DateTime
     */
    private $scanStartAt;

    /**
     * @var \DateTime
     */
    private $scanFinishedAt;

    /**
     * @return ArrayCollection
     */
    public function getClassCovers()
    {
        return $this->classCovers;
    }

    /**
     * @param string $cover
     */
    public function addClassCover($cover)
    {
        $this->classCovers->add($cover);
    }

    /**
     * @param string $cover
     */
    public function removeClassCover($cover)
    {
        $this->classCovers->removeElement($cover);
    }

    /**
     * @return ArrayCollection
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @param CoverFishPHPUnitTest $test
     */
    public function addTest(CoverFishPHPUnitTest $test)
    {
        $this->tests->add($test);
    }

    /**
     * @param CoverFishPHPUnitTest $test
     */
    public function removeTest(CoverFishPHPUnitTest $test)
    {
        $this->tests->removeElement($test);
    }

    /**
     * remove all tests from testCollection
     */
    public function clearTests()
    {
        $this->tests->clear();
    }

    /**
     * @return string
     */
    public function getCoversDefaultClass()
    {
        return $this->coversDefaultClass;
    }

    /**
     * @param string $coversDefaultClass
     */
    public function setCoversDefaultClass($coversDefaultClass)
    {
        $this->coversDefaultClass = $coversDefaultClass;
    }

    /**
     * @return array
     */
    public function getUsedClasses()
    {
        return $this->usedClasses;
    }

    /**
     * @param array $usedClasses
     */
    public function setUsedClasses($usedClasses)
    {
        $this->usedClasses = $usedClasses;
    }

    /**
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @param string $parentClass
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getClassNameSpace()
    {
        return $this->classNameSpace;
    }

    /**
     * @param string $classNameSpace
     */
    public function setClassNameSpace($classNameSpace)
    {
        $this->classNameSpace = $classNameSpace;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return \DateTime
     */
    public function getScanStartAt()
    {
        return $this->scanStartAt;
    }

    /**
     * @param \DateTime $scanStartAt
     */
    public function setScanStartAt($scanStartAt)
    {
        $this->scanStartAt = $scanStartAt;
    }

    /**
     * @return \DateTime
     */
    public function getScanFinishedAt()
    {
        return $this->scanFinishedAt;
    }

    /**
     * @param \DateTime $scanFinishedAt
     */
    public function setScanFinishedAt($scanFinishedAt)
    {
        $this->scanFinishedAt = $scanFinishedAt;
    }

    /**
     * @return int
     */
    public function getTaskTime()
    {
        if ($this->scanFinishedAt !== null) {
            return $this->scanFinishedAt - $this->scanStartAt;
        }

        return -1;
    }

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->scanStartAt = new \DateTime();
        $this->tests = new ArrayCollection();
    }
}