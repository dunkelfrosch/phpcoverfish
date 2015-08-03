<?php

namespace DF\PHPCoverFish\Common;

use DF\PHPCoverFish\Common\CoverFishColor as Color;

/**
 * Class CoverFishOutput
 *
 * @package    DF\PHP\CoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since      class available since Release 0.9.0
 * @version    0.9.2
 */
class CoverFishOutput
{
    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

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
     * @param array $outputOptions
     */
    public function __construct(array $outputOptions)
    {
        $this->coverFishHelper = new CoverFishHelper();
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
    }

    /**
     * alpha implementation of minimal reporting output
     *
     * @param CoverFishResult $coverFishResult
     *
     * @return null|string
     */
    public function writeResult(CoverFishResult $coverFishResult)
    {
        /** @var CoverFishPHPUnitFile $coverFishUnitFile */
        foreach ($coverFishResult->getUnits() as $coverFishUnitFile) {
            // reset failureStream and failureCount for current scanned file
            $coverFishResult->setFailureStream(null);
            $coverFishResult->setFailureCount(0);

            $this->writeFileName($coverFishUnitFile);
            $this->writeJsonResult($coverFishUnitFile);

            /** @var CoverFishPHPUnitTest $coverFishTest */
            foreach ($coverFishUnitFile->getTests() as $coverFishTest) {

                if ($this->outputLevel > 1) {
                    $this->write(sprintf('-> %s %s : ',
                        (false === $this->preventAnsiColors)
                            ? Color::tplDarkGrayColor($coverFishTest->getVisibility())
                            : $coverFishTest->getVisibility()
                        ,
                        $coverFishTest->getSignature()));
                }

                /** @var CoverFishMapping $coverMappings */
                foreach ($coverFishTest->getCoverMappings() as $coverMappings) {

                    if (true === $coverMappings->getValidatorResult()->isPass()) {
                        $this->writePass();
                    } else {
                        $this->writeFailureStream($coverFishResult, $coverFishTest, $coverMappings);
                        $this->writeFailure();
                    }
                }

                if ($this->outputLevel > 1) {
                    $this->write(PHP_EOL);
                }
            }

            $this->writeFinalCheckResults($coverFishResult);

        }

        return $this->outputResult();
    }

    /**
     * @param CoverFishResult $coverFishResult
     */
    private function writeFinalCheckResults(CoverFishResult $coverFishResult)
    {
        if ($coverFishResult->getFailureCount() > 0) {
            $this->writeFileFail($coverFishResult);
        } else {
            $this->writeFilePass();
        }

        $this->jsonResults[] = $this->jsonResult;
    }


    /**
     * @param CoverFishResult      $coverFishResult
     * @param CoverFishPHPUnitTest $unitTest
     * @param CoverFishError       $mappingError
     * @param $coverLine
     */
    public function writeJsonFailureStream(
        CoverFishResult $coverFishResult,
        CoverFishPHPUnitTest $unitTest,
        CoverFishError $mappingError,
        $coverLine
    ) {
        $this->jsonResult['error'] = $coverFishResult->getFailureCount();
        $this->jsonResult['errorMessage'] = $mappingError->getTitle();
        $this->jsonResult['errorCode'] = $mappingError->getErrorCode();
        $this->jsonResult['cover'] = $coverLine;
        $this->jsonResult['method'] = $unitTest->getName();
        $this->jsonResult['line'] = $unitTest->getLine();
        $this->jsonResult['file'] = $unitTest->getFile();
    }

    /**
     * @param CoverFishPHPUnitFile $coverFishUnitFile
     */
    public function writeJsonResult(CoverFishPHPUnitFile $coverFishUnitFile)
    {
        $this->jsonResult['pass'] = false;
        $this->jsonResult['file'] = $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile());
        $this->jsonResult['fileFQN'] = $coverFishUnitFile->getFile();
    }

    /**
     * @param int    $count
     * @param string $char
     *
     * @return null|string
     */
    private function setIndent($count, $char = ' ')
    {
        $outChar = null;
        for ($i = 1; $i <= $count; $i++) {
            $outChar .= $char;
        }

        return $outChar;
    }

    /**
     * @param CoverFishResult      $coverFishResult
     * @param CoverFishPHPUnitTest $unitTest
     * @param CoverFishMapping     $coverMapping
     *
     * @return null
     */
    public function writeFailureStream
    (
        CoverFishResult $coverFishResult,
        CoverFishPHPUnitTest $unitTest,
        CoverFishMapping $coverMapping
    )
    {
        /** @var int $lineIndent */
        $lineIndent = 3;
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

            // message block, line 01, message title
            $lineInfoMacro = '%sError #%s in method "%s" (L:~%s)';
            if ($this->outputLevel > 1) {
                $lineInfoMacro = '%sError #%s in method "%s", Line ~%s';
            }

            $lineInfo = sprintf($lineInfoMacro,
                $this->setIndent($lineIndent),
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($coverFishResult->getFailureCount()) // colored version
                    : $coverFishResult->getFailureCount(), // normal (no-ansi) version
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($unitTest->getName())
                    : $unitTest->getName(),
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($unitTest->getLine())
                    : $unitTest->getLine(),
                PHP_EOL
            );


            // message block, line 02, cover/annotation line
            $fileInfoMacro = '%s%s%s: %s';
            $fileInfo = sprintf($fileInfoMacro,
                PHP_EOL,
                $this->setIndent($lineIndent),
                (false === $this->preventAnsiColors)
                    ? Color::tplDarkGrayColor('File')
                    : 'File'
                ,
                $unitTest->getFileAndPath()
            );

            // message block, line 02, cover/annotation line
            $lineCoverMacro = '%s%s%s: %s%s';
            $lineCover = sprintf($lineCoverMacro,
                PHP_EOL,
                $this->setIndent($lineIndent),
                (false === $this->preventAnsiColors)
                    ? Color::tplDarkGrayColor('Annotation')
                    : 'Annotation'
                ,
                $coverLine,
                PHP_EOL
            );

            // message block, line 03, error message
            $lineMessageMacro = '%s%s %s ';
            if ($this->outputLevel > 1) {
                $lineMessageMacro = '%s%s %s (ErrorCode: %s)';
            }

            $lineMessage = sprintf($lineMessageMacro,
                $this->setIndent($lineIndent),
                (false === $this->preventAnsiColors)
                    ? Color::tplDarkGrayColor('Message')
                    : 'Message',

                $mappingError->getTitle(),
                $mappingError->getErrorCode()
            );

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
     * @param $content
     *
     * @return null on json
     */
    public function writeLine($content)
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
    public function write($content)
    {
        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        echo sprintf('%s', $content);
    }

    /**
     * write (colored) progress dot
     *
     * @return null on json
     */
    public function writePass()
    {
        $this->jsonResult['pass'] = true;

        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        $output = '.';
        if ($this->outputLevel > 1) {
            $output = 'pass';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[32;40m$output\033[0m";
        }

        echo $output;
    }

    /**
     * write (colored) failure 'F'
     *
     * @return null on json
     */
    public function writeFailure()
    {
        $this->jsonResult['pass'] = false;

        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        $output = 'F';
        if ($this->outputLevel > 1) {
            $output = 'fail';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[33;41m$output\033[0m";
        }

        echo $output;
    }

    /**
     * write (colored) error/exception 'E'
     *
     * @return null on json
     */
    public function writeError()
    {
        $this->jsonResult['pass'] = false;

        if (true === $this->outputFormatJson || -1 === $this->outputLevel) {
            return null;
        }

        $output = 'E';
        if ($this->outputLevel > 1) {
            $output = 'Error';
        }

        if (false === $this->preventAnsiColors) {
            $output = "\033[30;43m$output\033[0m";
        }

        echo $output;
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

        $fileNameLine = sprintf('%s%s%s',
            (false === $this->preventAnsiColors)
                ? Color::tplNormalColor(($this->outputLevel > 1) ? 'scan file ' : null)
                : 'scan file'
            ,
            (false === $this->preventAnsiColors)
                ? Color::tplYellowColor($this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile()))
                : $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile())
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
        $this->scanFailure = true;

        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
            return null;
        }

        $output = 'FAIL';
        if ($this->outputLevel > 1) {
            $output = 'file/test failure';
        }

        $fileResultMacro = '%s %s%s%s';
        $fileResult = sprintf($fileResultMacro,
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
            $output = 'file/test okay';
        }

        $fileResultMacro = '%s %s%s';
        $fileResult = sprintf($fileResultMacro,
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
    private function writeScanPass()
    {
        $output = 'scan succeeded, no problems found.';

        $scanResultMacro = '%s%s%s%s';
        $scanResult = sprintf($scanResultMacro,
            PHP_EOL,
            PHP_EOL,
            (false === $this->preventAnsiColors)
                ? Color::tplGreenColor($output)
                : $output,
            PHP_EOL
        );

        echo $scanResult;
    }

    /**
     * @todo: print out more detailed information about the final scan success result
     *
     * write scan fail result
     */
    private function writeScanFail()
    {
        $output = 'scan failed, coverage problems found!';

        $scanResultMacro = '%s%s%s%s';
        $scanResult = sprintf($scanResultMacro,
            PHP_EOL,
            PHP_EOL,
            (false === $this->preventAnsiColors)
                ? Color::tplRedColor($output)
                : $output,
            PHP_EOL
        );

        echo $scanResult;
    }

    /**
     * handle scanner output by default/parametric output format settings
     *
     * @return null|string
     */
    public function outputResult()
    {
        if (false === $this->outputFormatJson) {

            if ($this->outputLevel === 0 && (bool)$this->scanFailure === true) {
                $this->writeScanFail();
            }

            if ($this->outputLevel === 0 && (bool)$this->scanFailure === false) {
                $this->writeScanPass();
            }

            return null;
        }

        if (true === $this->preventEcho) {
            return json_encode($this->jsonResults);
        }

        echo json_encode($this->jsonResults);

        return null;
    }
}