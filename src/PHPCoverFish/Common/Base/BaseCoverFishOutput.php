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
     * initializer for json result set in write progress method
     */
    private function resetJsonResult()
    {
        $this->jsonResult['skipped'] = false;
        $this->jsonResult['pass'] = false;
        $this->jsonResult['failure'] = false;
        $this->jsonResult['error'] = false;
        $this->jsonResult['unknown'] = false;
    }

    protected function writeLine($content)
    {
        if (false === $this->outputFormatJson) {
            $this->output->writeln($content);
        }
    }

    /**
     * @param string $content
     */
    protected function write($content)
    {
        if (false === $this->outputFormatJson) {
            $this->output->write($content);
        }
    }

    /**
     * @codeCoverageIgnore
     *
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
     * @codeCoverageIgnore
     *
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
     * @param string $colorCode
     * @param string $charMinimal
     * @param string $charDetailed
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getProgressTemplate($colorCode, $charMinimal, $charDetailed)
    {
        $output = ($this->outputLevel > 1)
            ? $charDetailed // detailed output required?
            : $charMinimal  // otherwise "normal" progress output will be provided
        ;

        return (false === $this->preventAnsiColors)
            ? $output = Color::setColor($colorCode, $output)
            : $output;
    }

    /**
     * main progress output rendering function
     *
     * @param int $status
     *
     * @return null
     */
    protected function writeProgress($status)
    {
        $this->resetJsonResult();

        switch ($status) {
            case self::MACRO_SKIPPED:
                $this->jsonResult['skipped'] = true;
                $output = $this->getProgressTemplate('green', '_', 'S');

                break;

            case self::MACRO_PASS:
                $this->jsonResult['pass'] = true;
                $output = $this->getProgressTemplate('green', '.', '+');

                break;

            case self::MACRO_FAILURE:

                $this->jsonResult['failure'] = true;
                $output = $this->getProgressTemplate('bg_red_fg_yellow', 'f', 'F');

                break;

            case self::MACRO_ERROR:

                $output = $this->getProgressTemplate('bg_red_fg_white', 'e', 'E');
                $this->jsonResult['error'] = true;

                break;

            default:

                $this->jsonResult['unknown'] = true;
                $output = $output = $this->getProgressTemplate('bg_yellow_fg_black', '?', '?');

                break;
        }

        $this->write($output);
    }

    /**
     * write scan pass results
     *
     * @param CoverFishResult $coverFishResult
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

        $this->write($scanResult);
    }

    /**
     * write scan fail result
     *
     * @param CoverFishResult $coverFishResult
     *
     * @throws CoverFishFailExit
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

        $this->write($scanResult);

        throw new CoverFishFailExit();
    }

    /**
     * handle scanner output by default/parametric output format settings
     *
     * @param CoverFishResult $coverFishResult
     *
     * @return null
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