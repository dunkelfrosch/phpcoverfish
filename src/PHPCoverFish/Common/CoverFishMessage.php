<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishMessage, code coverage error definition
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   1.0.0
 */
class CoverFishMessage
{
    /**
     * @var array
     */
    public $messageTokens;

    /**
     * @var int
     */
    private $messageCode = 0;

    /**
     * @var string
     */
    private $messageTitle = null;

    /**
     * @var string
     */
    private $messageToken = null;

    /**
     * @var string
     */
    private $exceptionMessage = null;

    /**
     * @param int         $messageCode
     * @param null|string $exceptionMessage
     *
     * @throws \Exception
     */
    public function __construct($messageCode = 0, $exceptionMessage = null)
    {
        $this->messageCode = $messageCode;
        $this->exceptionMessage = $exceptionMessage;
        $this->messageToken = 'Unknown Message-Code!';

        if ($messageCode !== 0) {
            if (!isset($this->messageTokens[$messageCode])) {
                throw new \Exception(sprintf(
                    'MessageCode found but no title for type "%s" declared. Did you define this specific message code in your message token?',
                    $messageCode
                ));
            }

            $this->messageTitle = $this->messageTokens[$messageCode];
            $this->messageToken = $this->messageTitle;
        }
    }

    /**
     * @return int
     */
    public function getMessageCode()
    {
        return $this->messageCode;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $messageCode
     */
    public function setMessageCode($messageCode)
    {
        $this->messageCode = $messageCode;
    }

    /**
     * @return string
     */
    public function getMessageTitle()
    {
        return $this->messageTitle;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $messageTitle
     */
    public function setMessageTitle($messageTitle)
    {
        $this->messageTitle = $messageTitle;
    }

    /**
     * @return string
     */
    public function getMessageToken()
    {
        return $this->messageToken;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $messageToken
     */
    public function setMessageToken($messageToken)
    {
        $this->messageToken = $messageToken;
    }

    /**
     * @return array
     */
    public function getMessageTokens()
    {
        return $this->messageTokens;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $exceptionMessage
     */
    public function setExceptionMessage($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
    }
}