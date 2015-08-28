<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\CoverFishColor as Color;

/**
 * Class CoverFishWarning, code coverage warning definition
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9-beta2
 * @version   0.9.9
 *
 * - no coverage found
 * - double covers in methods
 * - double covers in class and methods
 * - obsolete multi-type coverage
 *
 */
final class CoverFishWarning
{
    const PHPUNIT_NO_COVERAGE_FOR_METHOD = 1000;
    
    /** @var array */
    private static $warningMessageTokens = array(
        self::PHPUNIT_NO_COVERAGE_FOR_METHOD => 'no coverage for this method!',
    );

    /**
     * @var int
     */
    private $warningCode = null;

    /**
     * @var string
     */
    private $title = null;

    /**
     * @var string
     */
    private $warningMessageToken = null;

    /**
     * @var string
     */
    private $exceptionMessage = null;

    /**
     * @return array
     */
    public static function getWarningMessageTokens()
    {
        return self::$warningMessageTokens;
    }

    /**
     * @param null        $warningCode
     * @param null|string $exceptionMessage
     *
     * @throws \Exception
     */
    public function __construct($warningCode = null, $exceptionMessage = null)
    {
        $this->warningCode = $warningCode;
        $this->exceptionMessage = $exceptionMessage;

        if ($warningCode === null) {
            $this->warningMessageToken = 'Unknown Warning-Code!';
        } else {
            if (!isset(self::$warningMessageTokens[$warningCode])) {
                throw new \Exception(sprintf(
                    'WarningCode found but no title for type "%s". Did you define this specific warning code in your message token?',
                    $warningCode
                ));
            }

            $this->title = self::$warningMessageTokens[$warningCode];
            $this->warningMessageToken = $this->title;
        }
    }

    /**
     * @param CoverFishMapping $coverMapping
     * @param bool|false       $noAnsiColors
     *
     * @return null|string
     */
    public function getWarningStreamTemplate(CoverFishMapping $coverMapping, $noAnsiColors = false)
    {
        $coverLine = null;
        switch ($this->warningCode) {
            case self::PHPUNIT_NO_COVERAGE_FOR_METHOD:
                $coverLine = sprintf('no @covers annotation for %s::%s', $coverMapping->getClassFQN(), $coverMapping->getMethod());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('no @covers annotation for ');
                    $coverLine .= Color::tplYellowColor($coverMapping->getClassFQN());
                    $coverLine .= Color::tplYellowColor('::' . $coverMapping->getMethod());
                }

                if (null === $coverMapping->getMethod()) {
                    $coverLine = str_replace('::', null, $coverLine);
                }

                break;

            default:
                break;
        }

        return $coverLine;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getWarningMessageToken()
    {
        return $this->warningMessageToken;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return int
     */
    public function getWarningCode()
    {
        return $this->warningCode;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }

    /**
     * @param mixed $exceptionMessage
     *
     * @codeCoverageIgnore
     */
    public function setExceptionMessage($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
    }
}