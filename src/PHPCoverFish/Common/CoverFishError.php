<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\CoverFishColor as Color;

/**
 * Class CoverFishErrors, code coverage error definition
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
final class CoverFishError
{
    // reflection problem, cover class is not available during reflection using annotation defined namespace
    const PHPUNIT_REFLECTION_CLASS_NOT_FOUND = 1000;
    // reflection problem, cover class method is not available / not found during reflection using annotation defined namespace
    const PHPUNIT_REFLECTION_METHOD_NOT_FOUND = 2000;
    // reflection problem, class exists but no public methods available
    const PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND = 2001;
    // reflection problem, class exists but no protected methods available
    const PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND = 2002;
    // reflection problem, class exists but no protected methods available
    const PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND = 2003;
    // reflection problem, class exists but no not public methods available
    const PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND = 2004;
    // reflection problem, class exists but no not protected methods available
    const PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND = 2005;
    // reflection problem, class exists but no not private methods available
    const PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND = 2006;
    // class does not provide any of defined methods in corresponding visibility
    const PHPUNIT_REFLECTION_CLASS_NOT_DEFINED = 4000;
    // annotation problem, cover class not found or class part in 'class::method' not available
    const PHPUNIT_VALIDATOR_PROBLEM = 9000;
    // annotation problem, defaultCoverClass not found during global method validation
    const PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM = 9001;

    /** @var array */
    private static $errorMessageTokens = array(
        self::PHPUNIT_REFLECTION_CLASS_NOT_FOUND => 'Class not found!', // annotation defined coverClass is not available during reflection, may be the class is not available system wide!
        self::PHPUNIT_REFLECTION_METHOD_NOT_FOUND => 'Method not found!', // annotation defined method is not available during reflection of corresponding coverClass!
        self::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND => 'no public methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND => 'no protected methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND => 'no private methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND => 'no not public methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND => 'no not protected methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND => 'no not private methods in class!', // method-access/-visibility problem!
        self::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED => 'Class not defined!', // class not defined, not found in use statement
        self::PHPUNIT_VALIDATOR_PROBLEM => 'cover Annotation problem!', // cover annotation spelling/validation error
        self::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM => 'defaultCoverClass Annotation missing!', // defaultCoverClass annotation spelling/validation error
    );

    /**
     * @var int
     */
    private $errorCode = null;

    /**
     * @var string
     */
    private $title = null;

    /**
     * @var string
     */
    private $errorMessageToken = null;

    /**
     * @var
     */
    private $exceptionMessage = null;

    /**
     * @param null $errorCode
     * @param null $exceptionMessage
     *
     * @throws \Exception
     */
    public function __construct($errorCode = null, $exceptionMessage = null)
    {
        $this->errorCode = $errorCode;
        $this->exceptionMessage = $exceptionMessage;

        if ($errorCode === null) {
            $this->type = 'about:blank';
            $this->errorMessageToken = 'Unknown Error-Code!';
        } else {
            if (!isset(self::$errorMessageTokens[$errorCode])) {
                throw new \Exception(sprintf(
                    'ErrorCode found but no title for type "%s". Did you define this specific error code in your message token?',
                    $errorCode
                ));
            }

            $this->title = self::$errorMessageTokens[$errorCode];
        }
    }

    public function getErrorStreamTemplate(CoverFishMapping $coverMapping, $noAnsiColors = false)
    {
        $coverLine = null;
        switch ($this->errorCode) {
            case self::PHPUNIT_REFLECTION_CLASS_NOT_FOUND:
                $coverLine = sprintf('@covers %s::%s',$coverMapping->getClassFQN(), $coverMapping->getMethod());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('@covers ');
                    $coverLine .= Color::tplMarkFailure($coverMapping->getClassFQN());
                    $coverLine .= Color::tplYellowColor('::'.$coverMapping->getMethod());
                }

                if (null === $coverMapping->getMethod()) {
                    $coverLine = str_replace('::', null, $coverLine);
                }

                break;

            case self::PHPUNIT_REFLECTION_METHOD_NOT_FOUND:
                $coverLine = sprintf('@covers %s::%s',$coverMapping->getClassFQN(), $coverMapping->getMethod());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('@covers ');
                    $coverLine .= Color::tplYellowColor($coverMapping->getClassFQN().'::');
                    $coverLine .= Color::tplMarkFailure($coverMapping->getMethod());
                }

                break;

            case self::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED:
                $coverLine = sprintf('@covers %s', $coverMapping->getClassFQN());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('@covers ');
                    $coverLine .= Color::tplMarkFailure($coverMapping->getClassFQN());
                }

                break;

            case self::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND:
            case self::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND:
            case self::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND:

                $coverLine = sprintf('@covers %s::<%s>', $coverMapping->getClassFQN(), $coverMapping->getAccessor());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('@covers ');
                    $coverLine .= Color::tplYellowColor($coverMapping->getClassFQN().'::<');
                    $coverLine .= Color::tplMarkFailure($coverMapping->getAccessor());
                    $coverLine .= Color::tplYellowColor('>');
                }

                break;

            case self::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM:
                break;

            case self::PHPUNIT_VALIDATOR_PROBLEM:
                break;

            default;
                break;
        }

        return $coverLine;
    }


    /**
     * @return string
     */
    public function getErrorMessageToken()
    {
        return $this->errorMessageToken;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }

    /**
     * @param mixed $exceptionMessage
     */
    public function setExceptionMessage($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
    }
}