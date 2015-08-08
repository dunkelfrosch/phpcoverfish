<?php

namespace DF\PHPCoverFish\Common\Base;

use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;

/**
 * Class CoverFishOutput
 *
 * @package    DF\PHPCoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since      class available since Release 0.9.2
 * @version    0.9.3
 */
abstract class CoverFishOutputBase
{
    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * @var CoverFishResult
     */
    protected $coverFishResult;

    /**
     * @var CoverFishPHPUnitFile
     */
    protected $coverFishUnitFile;

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * @var string
     */
    protected $outputFormat;

    /**
     * @var string
     */
    protected $outputLevel;

    /**
     * @var bool
     */
    protected $preventAnsiColors = false;

    /**
     * @var bool
     */
    protected $preventEcho = false;

    /**
     * @var bool
     */
    protected $outputFormatJson = false;

    /**
     * @var bool
     */
    protected $outputFormatText = true;

    /**
     * @var array
     */
    protected $jsonResult = array();

    /**
     * @var array
     */
    protected $jsonResults = array();

    /**
     * @var bool
     */
    protected $scanFailure;

    /**
     * @param $content
     *
     * @return null on json
     */
    protected function writeLine($content)
    {
        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        echo sprintf('%s%s', $content, PHP_EOL);
    }

    /**
     * @param $content
     *
     * @return null on json
     */
    protected function write($content)
    {
        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        echo sprintf('%s', $content);
    }

    /**
     * @param int    $count
     * @param string $char
     *
     * @return null|string
     */
    protected function setIndent($count, $char = ' ')
    {
        $outChar = null;
        for ($i = 1; $i <= $count; $i++) {
            $outChar .= $char;
        }

        return $outChar;
    }
}