<?php

namespace DF\PHPCoverFish\Common\Base;

use DF\PHPCoverFish\CoverFishScanner;
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
 * @version    0.9.9
 */
abstract class BaseCoverFishOutput
{
    /**
     * @const MACRO_DETAIL_LINE_INDENT set line indent for detailed error message block
     */
    const MACRO_CONFIG_DETAIL_LINE_INDENT = 3;

    /**
     * @const MACRO_SKIPPED code for skipped/coverage missing testFunctions
     */
    const MACRO_SKIPPED = 0;

    /**
     * @const MACRO_PASS code for passed testFunctions
     */
    const MACRO_PASS = 1;

    /**
     * @const MACRO_FAILURE code for failing testFunctions
     */
    const MACRO_FAILURE = 2;

    /**
     * @const MACRO_FAILURE code for error/exception thrown testFunctions
     */
    const MACRO_ERROR = 3;

    /**
     * @const FILE_PASS code for finally successfully closed single test file scan
     */
    const FILE_PASS = 10;

    /**
     * @const FILE_PASS code for tragically failed single test file scan exit
     */
    const FILE_FAILURE = 20;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var CoverFishScanner
     */
    protected $scanner;

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
    protected function resetJsonResult()
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
     * @param string      $colorCode
     * @param string      $statusMinimal
     * @param string      $statusDetailed
     * @param null|string $streamMessage
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getFileResultTemplate($colorCode, $statusMinimal, $statusDetailed, $streamMessage = null)
    {
        $output = ($this->outputLevel > 1)
            ? $statusDetailed
            : $statusMinimal;

        return sprintf('%s%s %s%s%s',
            ($this->outputLevel > 1)
                ? PHP_EOL
                : null
            ,
            ($this->outputLevel > 1)
                ? '=>'
                : null
            ,
            (false === $this->preventAnsiColors)
                ? Color::setColor($colorCode, $output)
                : $output
            ,
            ($this->outputLevel > 1)
                ? PHP_EOL
                : null
            ,
            $streamMessage
        );
    }

    /**
     * main progress output rendering function
     *
     * @param int    $status
     * @param string $message
     *
     * @return null
     */
    protected function writeFileResult($status, $message)
    {
        $output = null;

        switch ($status) {

            case self::FILE_FAILURE:
                $output = $this->getFileResultTemplate('bg_red_fg_white', 'FAIL', 'file/test FAILURE', $message);
                break;

            case self::FILE_PASS:
                $output = $this->getFileResultTemplate('green', 'OK', 'file/test OK', $message);
                break;

            default: break;
        }

        $this->writeLine($output);
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
                $this->jsonResult['error'] = true;
                $output = $this->getProgressTemplate('bg_red_fg_white', 'e', 'E');

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
     * @param CoverFishResult $coverFishResult
     */
    protected function writeScanWarningStatistic(CoverFishResult $coverFishResult)
    {
        $thresholdPercent = 0;
        if ($coverFishResult->getWarningCount() > 0 && $this->scanner->getWarningThreshold() > 0) {
            $thresholdPercent = round($coverFishResult->getWarningCount() * 100 / $this->scanner->getWarningThreshold(), 2);
        }

        $warningStatistic = '%s warning(s) found, %s%% of warning threshold (>=%s) reached.';
        $warningStatistic = sprintf(
            $warningStatistic,
            $coverFishResult->getWarningCount(),
            $thresholdPercent,
            $this->scanner->getWarningThreshold()
        );

        $this->writeLine($warningStatistic);
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
     * @param CoverFishPHPUnitFile $coverFishUnitFile
     *
     * @return null
     */
    protected function writeFileName(CoverFishPHPUnitFile $coverFishUnitFile)
    {
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
            return null;
        }

        $file = $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile());
        $fileNameLine = sprintf('%s%s%s',
            (false === $this->preventAnsiColors)
                ? Color::tplNormalColor(($this->outputLevel > 1) ? 'scan file ' : null)
                : 'scan file '
            ,
            (false === $this->preventAnsiColors)
                ? Color::tplYellowColor($file)
                : $file
            ,
            ($this->outputLevel > 1) ? PHP_EOL : ' '
        );

        $this->write($fileNameLine);
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

            $this->writeScanWarningStatistic($coverFishResult);

            return null;
        }

        if (true === $this->preventEcho) {
            return json_encode($this->jsonResults);
        }

        $this->output->write(json_encode($this->jsonResults));

        return null;
    }
}