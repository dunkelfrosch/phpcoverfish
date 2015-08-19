<?php

namespace DF\PHPCoverFish\Common\Base;

use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishColor as Color;
use DF\PHPCoverFish\Exception\CoverFishFailExit;
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
    const MACRO_SKIPPED = 0;

    const MACRO_PASS = 1;

    const MACRO_FAILURE = 2;

    const MACRO_ERROR = 3;

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

    /**
     * @param CoverFishResult $coverFishResult
     *
     * @return string
     */
    protected function getScanFailPassStatistic(CoverFishResult $coverFishResult)
    {
        $errorPercent = round($coverFishResult->getTestCount() / 100 * $coverFishResult->getFailureCount(), 2);
        $passPercent = 100 - $errorPercent;
        $errorStatistic = '%s %% failure rate%s%s %% pass rate%s';
        $errorStatistic = sprintf($errorStatistic,
            $errorPercent,
            PHP_EOL,
            $passPercent,
            PHP_EOL
        );

        $scanResultMacro = '%s';
        $scanResult = sprintf($scanResultMacro,
            (false === $this->preventAnsiColors)
                ? Color::tplRedColor($errorStatistic)
                : $errorStatistic
        );

        return $scanResult;
    }

    /**
     * main progress output rendering function
     *
     * @param int $status
     *
     * @return null|string
     */
    protected function writeProgress($status)
    {
        $this->jsonResult['skipped'] = false;
        $this->jsonResult['pass'] = false;
        $this->jsonResult['failure'] = false;
        $this->jsonResult['error'] = false;
        $this->jsonResult['unknown'] = false;

        switch ($status) {
            case self::MACRO_SKIPPED:

                $this->jsonResult['skipped'] = true;
                $this->jsonResult['pass'] = true;
                $output = ($this->outputLevel > 1)
                    ? 'N' // detailed output required?
                    : 'n' // otherwise "normal" progress output will be provided
                ;

                // @todo: implementation of ansi code clearance script required ;)
                $output = (false === $this->preventAnsiColors)
                    ? $output = "\033[1;30m$output\033[0m"
                    : $output
                ;

                break;

            case self::MACRO_PASS:

                $this->jsonResult['pass'] = true;
                $output = ($this->outputLevel > 1)
                    ? '+'
                    : '.'
                ;

                $output = (false === $this->preventAnsiColors)
                    ? $output = "\033[0;32m$output\033[0m"
                    : $output
                ;

                break;

            case self::MACRO_FAILURE:

                $this->jsonResult['failure'] = true;
                $output = ($this->outputLevel > 1)
                    ? 'fail'
                    : 'F'
                ;

                $output = (false === $this->preventAnsiColors)
                    ? $output = "\033[33;41m$output\033[0m"
                    : $output
                ;

                break;

            case self::MACRO_ERROR:

                $this->jsonResult['error'] = true;
                $output = ($this->outputLevel > 1)
                    ? 'error'
                    : 'E'
                ;

                $output = (false === $this->preventAnsiColors)
                    ? $output = "\033[30;43m$output\033[0m"
                    : $output
                ;
                break;

            default:

                $this->jsonResult['unknown'] = true;
                $output = ($this->outputLevel > 1)
                    ? 'unknown'
                    : '?'
                ;

                break;
        }

        // prevent any output on json output format
        if (true === $this->outputFormatJson) {
            return null;
        }

        $this->output->write($output);
    }

    /**
     * @todo: print out more detailed information about the final scan failure result
     *
     * write scan pass results
     */
    protected function writeScanPassStatistic(CoverFishResult $coverFishResult)
    {
        $passStatistic = '%s file(s) and %s method(s) scanned, scan succeeded, no problems found.%s';
        $passStatistic = sprintf($passStatistic,
            $coverFishResult->getFileCount(),
            $coverFishResult->getTestCount(),
            PHP_EOL
        );

        $scanResultMacro = '%s%s%s';
        $scanResult = sprintf($scanResultMacro,
            ($this->outputLevel === 0)
                ? PHP_EOL
                : null,
            PHP_EOL,
            (false === $this->preventAnsiColors)
                ? Color::tplGreenColor($passStatistic)
                : $passStatistic
        );

        $this->output->write($scanResult);
    }

    /**
     * write scan fail result
     */
    protected function writeScanFailStatistic(CoverFishResult $coverFishResult)
    {
        $errorStatistic = '%s file(s) and %s method(s) scanned, coverage failed: %s cover annotation problem(s) found!%s';
        $errorStatistic = sprintf($errorStatistic,
            $coverFishResult->getFileCount(),
            $coverFishResult->getTestCount(),
            $coverFishResult->getFailureCount(),
            PHP_EOL
        );

        $scanResultMacro = '%s%s%s%s';
        $scanResult = sprintf($scanResultMacro,
            ($this->outputLevel === 0)
                ? PHP_EOL
                : null,
            PHP_EOL,
            (false === $this->preventAnsiColors)
                ? Color::tplRedColor($errorStatistic)
                : $errorStatistic,
            $this->getScanFailPassStatistic($coverFishResult)
        );

        $this->output->write($scanResult);

        throw new CoverFishFailExit();
    }

    /**
     * handle scanner output by default/parametric output format settings
     *
     * @param CoverFishResult $coverFishResult
     *
     * @return null|string
     *
     * @throws CoverFishFailExit
     */
    protected function outputResult(CoverFishResult $coverFishResult)
    {
        if (false === $this->outputFormatJson) {

            if ($coverFishResult->getFailureCount() > 0) {
                $this->writeScanFailStatistic($coverFishResult);
            } else {
                $this->writeScanPassStatistic($coverFishResult);
            }

            return null;
        }

        if (true === $this->preventEcho) {
            return json_encode($this->jsonResults);
        }

        $this->output->write(json_encode($this->jsonResults));

        return null;
    }
}