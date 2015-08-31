<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Common\Base\BaseCoverFishOutput;
use DF\PHPCoverFish\Common\CoverFishColor as Color;
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
 * @version    0.9.9
 */
class CoverFishOutput extends BaseCoverFishOutput
{
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

        if ($this->coverFishHelper->checkParamNotEmpty($this->scanner->getPhpUnitXMLFile())) {
            $this->output->writeln(sprintf('using phpunit scan mode, phpunit-config file "%s"', $this->scanner->getPhpUnitXMLFile()));
        } else {
            $this->output->writeln('using raw scan mode, reading necessary parameters ...');
        }

        $this->output->write(PHP_EOL);
        $this->output->writeln(sprintf('- autoload file: %s', $this->scanner->getTestAutoloadPath()));
        $this->output->writeln(sprintf('- test source path for scan: %s', $this->scanner->getTestSourcePath()));
        $this->output->writeln(sprintf('- exclude test source path: %s', $this->scanner->getTestExcludePath()));
        $this->output->write(PHP_EOL);

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
                $this->writeProgress(self::MACRO_FAILURE);
            } else {
                $coverFishResult->addPassCount();
                $this->writeProgress(self::MACRO_PASS);
            }
        }

        if (count($coverFishTest->getCoverMappings()) === 0) {
            $this->writeProgress(self::MACRO_SKIPPED);
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
            $this->writeFileResult(self::FILE_PASS, null);
        } else {
            $this->writeFileResult(self::FILE_FAILURE, $coverFishResult->getFailureStream());
        }

        $this->jsonResults[] = $this->jsonResult;
    }

    /**
     * write single json error line
     *
     * @param CoverFishResult       $coverFishResult
     * @param CoverFishPHPUnitTest  $unitTest
     * @param CoverFishMessageError $mappingError
     * @param $coverLine
     */
    private function writeJsonFailureStream(
        CoverFishResult $coverFishResult,
        CoverFishPHPUnitTest $unitTest,
        CoverFishMessageError $mappingError,
        $coverLine
    ) {
        $this->jsonResult['errorCount'] = $coverFishResult->getFailureCount();
        $this->jsonResult['errorMessage'] = $mappingError->getMessageTitle();
        $this->jsonResult['errorCode'] = $mappingError->getMessageCode();
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
    protected function getMacroLineInfo($failureCount, CoverFishPHPUnitTest $unitTest)
    {
        $lineInfoMacro = '%sError #%s in method "%s" (L:~%s)';
        if ($this->outputLevel > 1) {
            $lineInfoMacro = '%sError #%s in method "%s", Line ~%s';
        }

        return sprintf($lineInfoMacro,
            $this->setIndent(self::MACRO_CONFIG_DETAIL_LINE_INDENT),
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
    protected function getMacroFileInfo(CoverFishPHPUnitTest $unitTest)
    {
        $fileInfoMacro = '%s%s%s: %s';
        return sprintf($fileInfoMacro,
            PHP_EOL,
            $this->setIndent(self::MACRO_CONFIG_DETAIL_LINE_INDENT),
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
    protected function getMacroCoverInfo($coverLine)
    {
        $lineCoverMacro = '%s%s%s: %s%s';
        return sprintf($lineCoverMacro,
            PHP_EOL,
            $this->setIndent(self::MACRO_CONFIG_DETAIL_LINE_INDENT),
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
     * @param CoverFishMessageError $mappingError
     *
     * @return string
     */
    protected function getMacroCoverErrorMessage(CoverFishMessageError $mappingError)
    {
        $lineMessageMacro = '%s%s %s ';
        if ($this->outputLevel > 1) {
            $lineMessageMacro = '%s%s %s (ErrorCode: %s)';
        }

        return sprintf($lineMessageMacro,
            $this->setIndent(self::MACRO_CONFIG_DETAIL_LINE_INDENT),
            (false === $this->preventAnsiColors)
                ? Color::tplDarkGrayColor('Message')
                : 'Message',

            $mappingError->getMessageTitle(),
            $mappingError->getMessageCode()
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
        /** @var CoverFishMessageError $mappingError */
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
}