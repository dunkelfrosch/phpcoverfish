<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\Base\BaseCoverFishResult;

/**
 * Class CoverFishResult
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.9
 *
 * @codeCoverageIgnore
 */
class CoverFishResult extends BaseCoverFishResult
{
    /**
     * @var ArrayCollection
     */
    private $warnings;

    /**
     * @var ArrayCollection
     */
    private $errors;

    /**
     * @var ArrayCollection
     */
    private $units;

    /**
     * @var string
     */
    private $errorStream = null;

    /**
     * @var string
     */
    private $failureStream = null;

    /**
     * @var string
     */
    private $infoStream = null;

    /**
     * @var string
     */
    private $warningStream = null;

    /**
     * @return string
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }

    /**
     * @param string $errorStream
     */
    public function setErrorStream($errorStream)
    {
        $this->errorStream = $errorStream;
    }

    /**
     * @return string
     */
    public function getFailureStream()
    {
        return $this->failureStream;
    }

    /**
     * @param string $failureStream
     */
    public function setFailureStream($failureStream)
    {
        $this->failureStream = $failureStream;
    }

    /**
     * @param string $content
     */
    public function addFailureToStream($content)
    {
        $this->failureStream .= $content;
    }

    /**
     * @param string $content
     */
    public function addInfoToStream($content)
    {
        $this->infoStream .= $content;
    }

    /**
     * @param string $content
     */
    public function addWarningToStream($content)
    {
        $this->warningStream .= $content;
    }

    /**
     * @return string
     */
    public function getWarningStream()
    {
        return $this->warningStream;
    }

    /**
     * @param string $warningStream
     */
    public function setWarningStream($warningStream)
    {
        $this->warningStream = $warningStream;
    }

    /**
     * @return ArrayCollection
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param CoverFishMessageWarning $warning
     */
    public function addWarning(CoverFishMessageWarning $warning)
    {
        $this->warnings->add($warning);
    }

    /**
     * @param CoverFishMessageWarning $warning
     */
    public function removeWarning(CoverFishMessageWarning $warning)
    {
        $this->warnings->removeElement($warning);
    }

    /**
     * clear all warnings
     */
    public function clearWarnings()
    {
        $this->warnings->clear();
    }

    /**
     * @return ArrayCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param CoverFishMessageError $error
     */
    public function addError(CoverFishMessageError $error)
    {
        $this->errors->add($error);
    }

    /**
     * @param CoverFishMessageError $error
     */
    public function removeError(CoverFishMessageError $error)
    {
        $this->errors->removeElement($error);
    }

    /**
     * @return ArrayCollection
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param CoverFishPHPUnitFile $file
     */
    public function addUnit(CoverFishPHPUnitFile $file)
    {
        $this->units->add($file);
    }

    /**
     * @param CoverFishPHPUnitFile $file
     */
    public function removeUnit(CoverFishPHPUnitFile $file)
    {
        $this->units->removeElement($file);
    }

    /**
     * remove all tests from testCollection
     */
    public function clearUnits()
    {
        $this->units->clear();
    }

    /**
     * clear all errors
     */
    public function clearErrors()
    {
        $this->errors->clear();
    }

    /**
     * class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->warnings = new ArrayCollection();
        $this->errors = new ArrayCollection();
        $this->units = new ArrayCollection();
    }
}