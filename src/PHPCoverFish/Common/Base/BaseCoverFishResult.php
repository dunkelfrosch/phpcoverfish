<?php

namespace DF\PHPCoverFish\Common\Base;

/**
 * Class BaseCoverFishResult
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   0.9.9
 *
 * @codeCoverageIgnore
 */
class BaseCoverFishResult
{
    /**
     * @var bool
     */
    private $pass = false;


    /**
     * @var bool
     */
    private $stopOnError = false;

    /**
     * @var bool
     */
    private $stopOnFailure = false;

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
     * @var \DateTime
     */
    private $taskStartAt;

    /**
     * @var \DateTime
     */
    private $taskFinishedAt;

    /**
     * @return boolean
     */
    public function isStopOnError()
    {
        return $this->stopOnError;
    }

    /**
     * @param boolean $stopOnError
     */
    public function setStopOnError($stopOnError)
    {
        $this->stopOnError = $stopOnError;
    }

    /**
     * @return boolean
     */
    public function isStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * @param boolean $stopOnFailure
     */
    public function setStopOnFailure($stopOnFailure)
    {
        $this->stopOnFailure = $stopOnFailure;
    }

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
     * class constructor
     */
    public function __construct()
    {
        $this->taskStartAt = new \DateTime();
    }
}