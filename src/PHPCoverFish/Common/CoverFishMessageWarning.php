<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\CoverFishColor as Color;

/**
 * Class CoverFishMessageWarning, code coverage warning definition - used in feature version of coverFish
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   1.0.0
 *
 * - no coverage found (implemented)
 * - double covers in methods
 * - double covers in class and methods
 * - obsolete multi-type coverage
 *
 */
class CoverFishMessageWarning extends CoverFishMessage
{
    // no coverage found
    const PHPUNIT_NO_COVERAGE_FOR_METHOD = 1000;
    const PHPUNIT_NO_DOCBLOCK_FOR_METHOD = 1001;
    
    /** @var array */
    public $messageTokens = array(
        self::PHPUNIT_NO_COVERAGE_FOR_METHOD => 'no coverage for this method!',
        self::PHPUNIT_NO_DOCBLOCK_FOR_METHOD => 'no phpdoc block for this method!',
    );

    /**
     * @param CoverFishMapping $coverMapping
     * @param bool|false       $noAnsiColors
     *
     * @return null|string
     */
    public function getWarningStreamTemplate(CoverFishMapping $coverMapping, $noAnsiColors = false)
    {
        $coverLine = null;
        switch ($this->getMessageCode()) {
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

            case self::PHPUNIT_NO_DOCBLOCK_FOR_METHOD:
                $coverLine = sprintf('no phpdoc block for %s::%s', $coverMapping->getClassFQN(), $coverMapping->getMethod());
                if (!$noAnsiColors) {
                    $coverLine  = Color::tplNormalColor('no phpdoc block for ');
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
}