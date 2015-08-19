<?php

namespace DF\PHPCoverFish\Common\Base;

use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCoverFishOutput
 *
 * @package    DF\PHPCoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since      class available since Release 0.9.2
 * @version    0.9.8
 */
abstract class BaseCoverFishOutput
{
    /**
     * @var OutputInterface
     */
    protected $output;

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
     * @var int
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
     * @param string $content
     *
     * @return null on json
     */
    protected function writeLine($content)
    {
        if (true === $this->outputFormatJson) {
            return null;
        }

        $this->output->writeln($content);
    }

    /**
     * @param string $content
     *
     * @return null on json
     */
    protected function write($content)
    {
        if (true === $this->outputFormatJson) {
            return null;
        }

        $this->output->write($content);
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