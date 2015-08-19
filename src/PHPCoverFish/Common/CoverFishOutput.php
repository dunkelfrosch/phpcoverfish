<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\Base\BaseCoverFishOutput;
use DF\PHPCoverFish\Common\CoverFishColor as Color;
use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Exception\CoverFishFailExit;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoverFishOutput
 *
 * @package    DF\PHPCoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since      class available since Release 0.9.0
 * @version    0.9.8
 */
class CoverFishOutput extends BaseCoverFishOutput
{
    /**
     * @const MACRO_DETAIL_LINE_INDENT set line indent for detailed error message block
     */
    const MACRO_DETAIL_LINE_INDENT = 3;

    /**
     * @var CoverFishScanner
     */
    protected $scanner;

    /**
     * @param array            $outputOptions
     * @param OutputInterface  $output
     * @param CoverFishScanner $scanner
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $outputOptions, OutputInterface $output, CoverFishScanner $scanner)
    {
        $this->output = $output;
        $this->scanner = $scanner;
        $this->coverFishHelper = new CoverFishHelper();

        $this->initOutputConfig($outputOptions);
    }

    /**
     * @param array $outputOptions
     *
     * @codeCoverageIgnore
     */
    private function initOutputConfig(array $outputOptions)
    {
        $this->scanFailure = false;
        $this->verbose = $outputOptions['out_verbose'];
        $this->outputFormat = $outputOptions['out_format'];
        $this->outputLevel = $outputOptions['out_level'];
        $this->preventAnsiColors = $outputOptions['out_no_ansi'];
        $this->preventEcho = $outputOptions['out_no_echo'];

        if ($this->outputFormat === 'json') {
            $this->outputFormatText = false;
            $this->outputFormatJson = true;
            $this->preventAnsiColors = true;
        }

        if ($this->outputLevel > 0) {
            $this->writeResultHeadlines();
        }
    }

    /**
     * @return bool
     */
    private function writeResultHeadlines()
    {
        if ($this->outputFormat === 'json') {
            return false;
        }

        if ($this->coverFishHelper->checkParamNotEmpty($this->scanner->getPhpUnitConfigFile())) {
            $this->output->writeln(sprintf('using phpunit scan mode, phpunit-config file "%s"', $this->scanner->getPhpUnitConfigFile()));
        } else {
            $this->output->writeln('using raw scan mode, reading parameter ...');
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('- autoload file: %s', $this->scanner->getTestAutoloadPath()));
        $this->output->writeln(sprintf('- test source path for scan: %s', $this->scanner->getTestSourcePath()));
        $this->output->writeln(sprintf('- exclude test source path: %s', $this->scanner->getTestExcludePath()));
        $this->output->writeln('');

        return true;
    }

    /**
     * @param CoverFishPHPUnitFile $coverFishUnitFile
     * @param CoverFishResult      $coverFishResult
     */
    private function writeSingleTestResult(
        CoverFishPHPUnitFile $coverFishUnitFile,
        CoverFishResult $coverFishResult
    ) {

        $this->writeFileName($coverFishUnitFile);
        $this->writeJsonResult($coverFishUnitFile);

        $coverFishResult->addFileCount();

        /** @var CoverFishPHPUnitTest $coverFishTest */
        foreach ($coverFishUnitFile->getTests() as $coverFishTest) {

            if ($this->outputLevel > 1) {
                $this->write(sprintf('%s-> %s %s : ',
                    PHP_EOL,
                    (false === $this->preventAnsiColors)
                        ? Color::tplDarkGrayColor($coverFishTest->getVisibility())
                        : $coverFishTest->getVisibility()
                    ,
                    $coverFishTest->getSignature()));
            }

            $this->writeSingleMappingResult($coverFishTest, $coverFishResult);
        }
    }

    /**
     * output mapping/scanning result of each scanned file
     *
     * @param CoverFishPHPUnitTest $coverFishTest
     * @param CoverFishResult      $coverFishResult
     */
    private function writeSingleMappingResult(
        CoverFishPHPUnitTest $coverFishTest,
        CoverFishResult $coverFishResult
    ) {
        /** @var CoverFishMapping $coverMappings */
        foreach ($coverFishTest->getCoverMappings() as $coverMappings) {
            $coverFishResult->addTestCount();
            if (false === $coverMappings->getValidatorResult()->isPass()) {
                $this->scanFailure = true;
                $this->writeFailureStream($coverFishResult, $coverFishTest, $coverMappings);
                $this->writeFailure();
            } else {
                $coverFishResult->addPassCount();
                $this->writePass();
            }
        }

        if (count($coverFishTest->getCoverMappings()) === 0) {
            $this->writeNoCoverFound();
        }
    }

    /**
     * alpha/basic implementation of minimal reporting output
     *
     * @param CoverFishResult $coverFishResult
     *
     * @return null|string
     */
    public function writeResult(CoverFishResult $coverFishResult)
    {
        /** @var CoverFishPHPUnitFile $coverFishUnitFile */
        foreach ($coverFishResult->getUnits() as $coverFishUnitFile) {
            $this->scanFailure = false;
            $coverFishResult->setFailureStream(null);

            $this->writeSingleTestResult($coverFishUnitFile, $coverFishResult);
            $this->writeFinalCheckResults($coverFishResult);
        }

        return $this->outputResult($coverFishResult);
    }

    /**
     * @param CoverFishResult $coverFishResult
     */
    private function writeFinalCheckResults(CoverFishResult $coverFishResult)
    {
        if (false === $this->scanFailure) {
            $this->writeFilePass();
        } else {
            $this->writeFileFail($coverFishResult);
        }

        $this->jsonResults[] = $this->jsonResult;
    }

    /**
     * write single json error line
     *
     * @param CoverFishResult      $coverFishResult
     * @param CoverFishPHPUnitTest $unitTest
     * @param CoverFishError       $mappingError
     * @param $coverLine
     */
    private function writeJsonFailureStream(
        CoverFishResult $coverFishResult,
        CoverFishPHPUnitTest $unitTest,
        CoverFishError $mappingError,
        $coverLine
    ) {
        $this->jsonResult['error'] = $coverFishResult->getFailureCount();
        $this->jsonResult['errorMessage'] = $mappingError->getTitle();
        $this->jsonResult['errorCode'] = $mappingError->getErrorCode();
        $this->jsonResult['cover'] = $coverLine;
        $this->jsonResult['method'] = $unitTest->getSignature();
        $this->jsonResult['line'] = $unitTest->getLine();
        $this->jsonResult['file'] = $unitTest->getFile();
    }

    /**
     * @param CoverFishPHPUnitFile $coverFishUnitFile
     */
    private function writeJsonResult(CoverFishPHPUnitFile $coverFishUnitFile)
    {
        $this->jsonResult['pass'] = false;
        $this->jsonResult['file'] = $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile());
        $this->jsonResult['fileFQN'] = $coverFishUnitFile->getFile();
    }

    /**
     * message block macro, line 01, message title
     *
     * @param int                  $failureCount
     * @param CoverFishPHPUnitTest $unitTest
     *
     * @return string
     */
    private function getMacroLineInfo($failureCount, CoverFishPHPUnitTest $unitTest)
    {
        $lineInfoMacro = '%sError #%s in method "%s" (L:~%s)';
        if ($this->outputLevel > 1) {
            $lineInfoMacro = '%sError #%s in method "%s", Line ~%s';
        }

        return sprintf($lineInfoMacro,
            $this->setIndent(self::MACRO_DETAIL_LINE_INDENT),
            (false === $this->preventAnsiColors)
                ? Color::tplWhiteColor($failureCount)
                : $failureCount,
            (false === $this->preventAnsiColors)
                ? Color::tplWhiteColor($unitTest->getSignature())
                : $unitTest->getSignature(),
            (false === $this->preventAnsiColors)
                ? Color::tplWhiteColor($unitTest->getLine())
                : $unitTest->getLine(),
            PHP_EOL
        );
    }

    /**
     * message block macro, line 02, cover/annotation line
     *
     * @param CoverFishPHPUnitTest $unitTest
     *
     * @return string
     */
    private function getMacroFileInfo(CoverFishPHPUnitTest $unitTest)
    {
        $fileInfoMacro = '%s%s%s: %s';
        return sprintf($fileInfoMacro,
            PHP_EOL,
            $this->setIndent(self::MACRO_DETAIL_LINE_INDENT),
            (false === $this->preventAnsiColors)
                ? Color::tplDarkGrayColor('File')
                : 'File'
            ,
            $unitTest->getFileAndPath()
        );
    }

    /**
     * message block macro, line 03, cover/annotation line
     *
     * @param string $coverLine
     * @return string
     */
    private function getMacroCoverInfo($coverLine)
    {
        $lineCoverMacro = '%s%s%s: %s%s';
        return sprintf($lineCoverMacro,
            PHP_EOL,
            $this->setIndent(self::MACRO_DETAIL_LINE_INDENT),
            (false === $this->preventAnsiColors)
                ? Color::tplDarkGrayColor('Annotation')
                : 'Annotation'
            ,
            $coverLine,
            PHP_EOL
        );
    }

    /**
     * message block macro, line 04, error message
     *
     * @param CoverFishError $mappingError
     *
     * @return string
     */
    private function getMacroCoverErrorMessage(CoverFishError $mappingError)
    {
        $lineMessageMacro = '%s%s %s ';
        if ($this->outputLevel > 1) {
            $lineMessageMacro = '%s%s %s (ErrorCode: %s)';
        }

        return sprintf($lineMessageMacro,
            $this->setIndent(self::MACRO_DETAIL_LINE_INDENT),
            (false === $this->preventAnsiColors)
                ? Color::tplDarkGrayColor('Message')
                : 'Message',

            $mappingError->getTitle(),
            $mappingError->getErrorCode()
        );
    }

    /**
     * @param CoverFishResult      $coverFishResult
     * @param CoverFishPHPUnitTest $unitTest
     * @param CoverFishMapping     $coverMapping
     *
     * @return null
     */
    private function writeFailureStream(
        CoverFishResult $coverFishResult,
        CoverFishPHPUnitTest $unitTest,
        CoverFishMapping $coverMapping
    )
    {
        /** @var CoverFishError $mappingError */
        foreach ($coverMapping->getValidatorResult()->getErrors() as $mappingError) {

            $coverFishResult->addFailureCount();

            $coverLine = $mappingError->getErrorStreamTemplate($coverMapping, $this->preventAnsiColors);
            $this->writeJsonFailureStream($coverFishResult, $unitTest, $mappingError, $coverLine);

            if (0 === $this->outputLevel)
            {
                continue;
            }

            $coverFishResult->addFailureToStream(PHP_EOL);

            $lineInfo = $this->getMacroLineInfo($coverFishResult->getFailureCount(), $unitTest);
            $fileInfo = $this->getMacroFileInfo($unitTest);
            $lineCover = $this->getMacroCoverInfo($coverLine);
            $lineMessage = $this->getMacroCoverErrorMessage($mappingError);

            $coverFishResult->addFailureToStream($lineInfo);

            if ($this->outputLevel > 1) {
                $coverFishResult->addFailureToStream($fileInfo);
            }

            $coverFishResult->addFailureToStream($lineCover);
            $coverFishResult->addFailureToStream($lineMessage);
            $coverFishResult->addFailureToStream(PHP_EOL);
        }
    }

    /**
     * write (colored) progress for no cover found "n"|"N"
     *
     * @return null
     */
    private function writeNoCoverFound()
    {
        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = 'n';
        if ($this->outputLevel > 1) {
            $output = 'N';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[33;40m$output\033[0m";
        }

        $this->output->write($output);
    }

    /**
     * write (colored) progress dot
     *
     * @return null on json
     */
    private function writePass()
    {
        $this->jsonResult['pass'] = true;

        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = '.';
        if ($this->outputLevel > 1) {
            $output = '+';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[32;40m$output\033[0m";
        }

        $this->output->write($output);
    }

    /**
     * write (colored) failure 'F'
     *
     * @return null on json
     */
    private function writeFailure()
    {
        $this->jsonResult['pass'] = false;

        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = 'F';
        if ($this->outputLevel > 1) {
            $output = 'fail';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[33;41m$output\033[0m";
        }

        $this->output->write($output);
    }

    /**
     * write (colored) error/exception 'E', not used yet
     *
     * @return null on json
     */
    private function writeError()
    {
        $this->jsonResult['pass'] = false;

        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = 'E';
        if ($this->outputLevel > 1) {
            $output = 'Error';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[30;43m$output\033[0m";
        }

        $this->output->write($output);
    }

    /**
     * @param CoverFishPHPUnitFile $coverFishUnitFile
     *
     * @return null
     */
    private function writeFileName(CoverFishPHPUnitFile $coverFishUnitFile)
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
     * @param CoverFishResult $coverFishResult
     *
     * @return null
     */
    private function writeFileFail(CoverFishResult $coverFishResult)
    {
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
            return null;
        }

        $output = 'FAIL';
        if ($this->outputLevel > 1) {
            $output = 'file/test failure';
        }

        $fileResultMacro = '%s%s %s%s%s';
        $fileResult = sprintf($fileResultMacro,
            ($this->outputLevel > 1)
                ? PHP_EOL
                : null
            ,
            ($this->outputLevel > 1)
                ? '=>'
                : null
            ,
            (false === $this->preventAnsiColors)
                ? Color::tplRedColor($output)
                : $output
            ,
            PHP_EOL,
            $coverFishResult->getFailureStream()
        );

        $this->writeLine($fileResult);
    }

    /**
     * @return null
     */
    private function writeFilePass()
    {
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
            return null;
        }

        $output = 'OK';
        if ($this->outputLevel > 1) {
            $output = 'cover test(s) succeeded';
        }

        $fileResultMacro = '%s%s %s%s';
        $fileResult = sprintf($fileResultMacro,
            ($this->outputLevel > 1)
                ? PHP_EOL
                : null
            ,
            ($this->outputLevel > 1)
                ? '=>'
                : null
            ,
            (false === $this->preventAnsiColors)
                ? Color::tplGreenColor($output)
                : $output
            ,
            ($this->outputLevel > 1)
                ? PHP_EOL
                : null
        );

        $this->writeLine($fileResult);
    }

    /**
     * @todo: print out more detailed information about the final scan failure result
     *
     * write scan pass results
     */
    private function writeScanPassStatistic(CoverFishResult $coverFishResult)
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
    private function writeScanFailStatistic(CoverFishResult $coverFishResult)
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
     * @param CoverFishResult $coverFishResult
     *
     * @return string
     */
    private function getScanFailPassStatistic(CoverFishResult $coverFishResult)
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
     * handle scanner output by default/parametric output format settings
     *
     * @param CoverFishResult $coverFishResult
     *
     * @return null|string
     *
     * @throws CoverFishFailExit
     */
    private function outputResult(CoverFishResult $coverFishResult)
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