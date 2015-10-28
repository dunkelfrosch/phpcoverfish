<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishPHPUnitTest, wrapper for all phpUnit testClass files
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   1.0.0
 *
 * @codeCoverageIgnore
 */
class CoverFishPHPUnitTest
{
    /**
     * @var bool
     */
    private $fromClass = false;

    /**
     * @var bool
     */
    private $fromMethod = false;

    /**
     * @var string
     */
    private $docBlock;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var int
     */
    private $line;

    /**
     * @var int
     */
    private $loc;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $fileAndPath;

    /**
     * @var ArrayCollection
     */
    private $coverAnnotations;

    /**
     * @var ArrayCollection
     */
    private $coverMappings;

    /**
     * @return string
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @param string $docBlock
     */
    public function setDocBlock($docBlock)
    {
        $this->docBlock = $docBlock;
    }

    /**
     * @deprecated in version 0.9.3, signature will be used instead
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @deprecated in version 0.9.3, signature will be used instead
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param int $loc
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * @return ArrayCollection
     */
    public function getCoverMappings()
    {
        return $this->coverMappings;
    }

    /**
     * @param CoverFishMapping $coverMapping
     */
    public function addCoverMapping(CoverFishMapping $coverMapping)
    {
        $this->coverMappings->add($coverMapping);
    }

    /**
     * @param CoverFishMapping $coverMapping
     */
    public function removeCoverMapping(CoverFishMapping $coverMapping)
    {
        $this->coverMappings->removeElement($coverMapping);
    }

    /*
     * clear all defined coverMappings
     */
    public function clearCoverMappings()
    {
        $this->coverMappings->clear();
    }

    /**
     * @return ArrayCollection
     */
    public function getCoverAnnotations()
    {
        return $this->coverAnnotations;
    }

    /**
     * @param string $annotation
     */
    public function addCoverAnnotation($annotation)
    {
        $this->coverAnnotations->add($annotation);
    }

    /**
     * @param string $annotation
     */
    public function removeCoverAnnotation($annotation)
    {
        $this->coverAnnotations->removeElement($annotation);
    }

    /*
     * clear all defined annotations
     */
    public function clearCoverAnnotation()
    {
        $this->coverAnnotations->clear();
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
    public function getFileAndPath()
    {
        return $this->fileAndPath;
    }

    /**
     * @param string $fileAndPath
     */
    public function setFileAndPath($fileAndPath)
    {
        $this->fileAndPath = $fileAndPath;
    }

    /**
     * @return boolean
     */
    public function isFromClass()
    {
        return $this->fromClass;
    }

    /**
     * @param boolean $fromClass
     */
    public function setFromClass($fromClass)
    {
        $this->fromClass = $fromClass;
    }

    /**
     * @return boolean
     */
    public function isFromMethod()
    {
        return $this->fromMethod;
    }

    /**
     * @param boolean $fromMethod
     */
    public function setFromMethod($fromMethod)
    {
        $this->fromMethod = $fromMethod;
    }

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->coverMappings = new ArrayCollection();
        $this->coverAnnotations = new ArrayCollection();
    }
}