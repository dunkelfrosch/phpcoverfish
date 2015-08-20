<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishColor, color definition for cli output, based on the work on of "\Bart\EscapeColors Benjamin VanEvery"
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.6
 *
 * @codeCoverageIgnore
 */
final class CoverFishColor
{
    /**
     * @var array
     */
    private static $colorTable = array(
        'gray' => '0;0',
        'black' => '0;30',
        'dark_gray' => '1;30',
        'red' => '0;31',
        'bold_red' => '1;31',
        'green' => '0;32',
        'bold_green' => '1;32',
        'brown' => '0;33',
        'yellow' => '1;33',
        'blue' => '0;34',
        'bold_blue' => '1;34',
        'purple' => '0;35',
        'bold_purple' => '1;35',
        'cyan' => '0;36',
        'bold_cyan' => '1;36',
        'white' => '1;37',
        'bold_gray' => '0;37',
        'bg_black' => '40',
        'bg_red' => '41',
        'bg_magenta' => '45',
        'bg_yellow' => '43',
        'bg_green' => '42',
        'bg_blue' => '44',
        'bg_cyan' => '46',
        'bg_light_gray' => '47',
        'bg_red_fg_yellow' => '33;41',
        'bg_red_fg_white' => '37;41',
        'bg_yellow_fg_black' => '30;43',
    );

    /**
     * Make string appear in color
     *
     * @param string $color
     * @param string $string
     *
     * @return string
     * @throws \Exception
     */
    public static function setColor($color, $string)
    {
        if (!isset(self::$colorTable[$color]))
        {
            throw new \Exception('ansi color is not defined');
        }

        return sprintf("\033[%sm%s\033[0m", self::$colorTable[$color], $string);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplWhiteColor($content)
    {
        return self::setColor('white', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplRedColor($content)
    {
        return self::setColor('red', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplGreenColor($content)
    {
        return self::setColor('green', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplDarkGrayColor($content)
    {
        return self::setColor('dark_gray', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplYellowColor($content)
    {
        return self::setColor('yellow', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplNormalColor($content)
    {
        return self::setColor('gray', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplMarkFailure($content)
    {
        return self::setColor('yellow', self::setColor('red', $content));
    }
}