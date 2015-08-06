<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishColor, color definition for cli output, based on the work on of "\Bart\EscapeColors Benjamin VanEvery"
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
final class CoverFishColor
{
    /**
     * @var array
     */
    private static $foreground = array(
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
    );

    /**
     * @var array
     */
    private static $background = array(
        'black' => '40',
        'red' => '41',
        'magenta' => '45',
        'yellow' => '43',
        'green' => '42',
        'blue' => '44',
        'cyan' => '46',
        'light_gray' => '47',
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
    public static function fg_color($color, $string)
    {
        if (!isset(self::$foreground[$color]))
        {
            throw new \Exception('Foreground color is not defined');
        }

        return "\033[" . self::$foreground[$color] . "m" . $string . "\033[0m";
    }

    /**
     * Make string appear with background color
     *
     * @param string $color
     * @param string $string
     *
     * @return string
     * @throws \Exception
     */
    public static function bg_color($color, $string)
    {
        if (!isset(self::$background[$color]))
        {
            throw new \Exception('Background color is not defined');
        }

        return "\033[" . self::$background[$color] . 'm' . $string . "\033[0m";
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplWhiteColor($content)
    {
        return self::fg_color('white', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplRedColor($content)
    {
        return self::fg_color('red', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplGreenColor($content)
    {
        return self::fg_color('green', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplDarkGrayColor($content)
    {
        return self::fg_color('dark_gray', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplYellowColor($content)
    {
        return self::fg_color('yellow', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplNormalColor($content)
    {
        return self::fg_color('gray', $content);
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public static function tplMarkFailure($content)
    {
        return self::fg_color('yellow', self::bg_color('red', $content));
    }
}