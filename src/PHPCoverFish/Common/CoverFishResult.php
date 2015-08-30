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
 * @version   0.9.9
 *
 * @codeCoverageIgnore
 */
class CoverFishResult
{
    /**
     * @var bool
     */
    private $pass = false;

    /**
     * @var int
     */
    private $passCount = 0;

    /**
     * @var int
     */
    private $failureCount = 0;

    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $warningCount = 0;

    /**
     * @var int
     */
    private $testCount = 0;

    /**
     * @var int
     */
    private $fileCount = 0;

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
     * @var \DateTime
     */
    private $taskStartAt;

    /**
     * @var \DateTime
     */
    private $taskFinishedAt;

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
     * @return int
     */
    public function getWarningCount()
    {
        return $this->warningCount;
    }

    /**
     * @param int $warningCount
     */
    public function setWarningCount($warningCount)
    {
        $this->warningCount = $warningCount;
    }

    /**
     * @return int
     */
    public function addWarningCount()
    {
        return $this->warningCount++;
    }

    /**
     * @return boolean
     */
    public function isPass()
    {
        return $this->pass;
    }

    /**
     * @param boolean $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return int
     */
    public function getPassCount()
    {
        return $this->passCount;
    }

    /**
     * @param int $passCount
     */
    public function setPassCount($passCount)
    {
        $this->passCount = $passCount;
    }

    /**
     * @return int
     */
    public function addPassCount()
    {
        return $this->passCount++;
    }

    /**
     * @return int
     */
    public function getFailureCount()
    {
        return $this->failureCount;
    }

    /**
     * @param int $failureCount
     */
    public function setFailureCount($failureCount)
    {
        $this->failureCount = $failureCount;
    }

    /**
     * @return int
     */
    public function addFailureCount()
    {
        return $this->failureCount++;
    }

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }

    /**
     * @param int $errorCount
     */
    public function setErrorCount($errorCount)
    {
        $this->errorCount = $errorCount;
    }

    /**
     * @return int
     */
    public function addErrorCount()
    {
        return $this->errorCount++;
    }

    /**
     * @return int
     */
    public function getTestCount()
    {
        return $this->testCount;
    }

    /**
     * @param int $testCount
     */
    public function setTestCount($testCount)
    {
        $this->testCount = $testCount;
    }

    /**
     * @return int
     */
    public function addTestCount()
    {
        return $this->testCount++;
    }

    /**
     * @return int
     */
    public function getFileCount()
    {
        return $this->fileCount;
    }

    /**
     * @param int $fileCount
     */
    public function setFileCount($fileCount)
    {
        $this->fileCount = $fileCount;
    }

    /**
     * @return int
     */
    public function addFileCount()
    {
        return $this->fileCount++;
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
     * @return \DateTime
     */
    public function getTaskStartAt()
    {
        return $this->taskStartAt;
    }

    /**
     * @param \DateTime $taskStartAt
     */
    public function setTaskStartAt($taskStartAt)
    {
        $this->taskStartAt = $taskStartAt;
    }

    /**
     * @return \DateTime
     */
    public function getTaskFinishedAt()
    {
        return $this->taskFinishedAt;
    }

    /**
     * @param \DateTime $taskFinishedAt
     */
    public function setTaskFinishedAt($taskFinishedAt)
    {
        $this->taskFinishedAt = $taskFinishedAt;
    }

    /**
     * @return int
     */
    public function getTaskTime()
    {
        if ($this->taskFinishedAt !== null) {
            return $this->taskFinishedAt - $this->taskStartAt;
        }

        return -1;
    }

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
        $this->taskStartAt = new \DateTime();
        $this->warnings = new ArrayCollection();
        $this->errors = new ArrayCollection();
        $this->units = new ArrayCollection();
    }
}