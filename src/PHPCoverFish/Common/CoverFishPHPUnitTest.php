<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishPHPUnitTest, wrapper for all phpUnit testClass files
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
class CoverFishPHPUnitTest
{
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
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
        $this->coverMappings->remove($coverMapping);
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
        $this->coverAnnotations->remove($annotation);
    }

    /*
     * clear all defined annotations
     */
    public function clearCoverAnnotation()
    {
        $this->coverAnnotations->clear();
    }

    /**
     * class constructor
     */
    public function __construct()
    {
 //       $this->coverAnnotations = new ArrayCollection();
        $this->coverMappings = new ArrayCollection();
    }
}