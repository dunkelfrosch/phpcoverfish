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
 * @version    0.9.0
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
    protected $preventAnsiColors = false;

    /**
     * prevent echo of json result, return serialized object directly
     *
     * @var bool
     */
    protected $preventEcho = false;

    /**
     * @var string
     */
    protected $outputFormat;

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
     * @var string
     */
    protected $outputLevel;

    /**
     * @var bool
     */
    protected $scanFailure;

    /**
     * @param string     $outputFormat
     * @param int        $outputLevel
     * @param bool|false $preventAnsiColors
     * @param bool|false $preventEcho
     */
    public function __construct($outputFormat = 'text', $outputLevel = 1, $preventAnsiColors = false, $preventEcho = false)
    {
        $this->coverFishHelper = new CoverFishHelper();
        $this->preventAnsiColors = $preventAnsiColors;
        $this->outputFormat = $outputFormat;
        $this->outputLevel = $outputLevel;
        $this->scanFailure = false;

        if ($this->outputFormat === 'json') {
            $this->outputFormatText = false;
            $this->outputFormatJson = true;
            $this->preventAnsiColors = true;
            $this->preventEcho = $preventEcho;
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

            // write additional json result
            $this->writeJsonResult($coverFishUnitFile);

            // show scanFile info line
            // @todo:refactor!
            if (false === $this->preventAnsiColors) {
                $this->write(sprintf('%s%s%s',
                    Color::tplNormalColor(($this->outputLevel > 1) ? 'scan file ' : null),
                    Color::tplYellowColor($this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile())),
                    ($this->outputLevel > 1) ? PHP_EOL : ' '
                ));
            } else {
                $this->write(sprintf('%s%s%s',
                    ($this->outputLevel > 1) ? 'scan file ' : null,
                    $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile()),
                    ($this->outputLevel > 1) ? PHP_EOL : ' '
                ));
            }

            /** @var CoverFishPHPUnitTest $coverFishTest */
            foreach ($coverFishUnitFile->getTests() as $coverFishTest) {
                // show scanMethod info line
                if ($this->outputLevel > 1) {
                    $this->write(sprintf('-> %s %s : ', Color::tplDarkGrayColor($coverFishTest->getVisibility()), $coverFishTest->getSignature()));
                }
                    /** @var CoverFishMapping $coverMappings */
                foreach ($coverFishTest->getCoverMappings() as $coverMappings) {
                    if (false === $coverMappings->getValidatorResult()->isPass()) {
                        // collection detailed error messages
                        $this->writeFailureStream($coverFishResult, $coverFishTest, $coverMappings);
                        $this->writeFailure();
                    } else {
                        $this->writePass();
                    }
                }

                if ($this->outputLevel > 1) {
                    $this->write(PHP_EOL);
                }
            }

            if ($coverFishResult->getFailureCount() > 0) {
                $this->writeFileFail($coverFishResult);
            } else {
                $this->writeFilePass();
            }

            // summarize json results
            $this->jsonResults[] = $this->jsonResult;
        }

        return $this->outputResult();
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
    public function writeJsonResult(CoverFishPHPUnitFile $coverFishUnitFile) {
        $this->jsonResult['pass'] = false;
        $this->jsonResult['file'] = $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile());
        $this->jsonResult['fileFQN'] = $coverFishUnitFile->getFile();
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
            $lineInfoMacro = 'Error #%s in method "%s" (L:~%s)'.PHP_EOL;
            if ($this->outputLevel > 1) {
                $lineInfoMacro = 'Error #%s in method "%s", Line ~%s (File: %s)%s';
            }

            $lineInfo = sprintf($lineInfoMacro,
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($coverFishResult->getFailureCount()) // colored version
                    : $coverFishResult->getFailureCount(), // normal (no-ansi) version
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($unitTest->getName())
                    : $unitTest->getName(),
                (false === $this->preventAnsiColors)
                    ? Color::tplWhiteColor($unitTest->getLine())
                    : $unitTest->getLine(),
                $unitTest->getFile(),
                PHP_EOL
            );

            // message block, line 02, cover/annotation line
            $lineCoverMacro = '%s: %s%s';
            $lineCover = sprintf($lineCoverMacro,
                (false === $this->preventAnsiColors)
                    ? Color::tplDarkGrayColor('Annotation')
                    : 'Annotation'
                ,
                $coverLine,
                PHP_EOL
            );

            // message block, line 03, error message
            $lineMessageMacro = '%s %s ';
            if ($this->outputLevel > 1) {
                $lineMessageMacro = '%s %s (ErrorCode: %s)';
            }

            $lineMessage = sprintf($lineMessageMacro,
                (false === $this->preventAnsiColors)
                    ? Color::tplDarkGrayColor('Message')
                    : 'Message',

                $mappingError->getTitle(),
                $mappingError->getErrorCode()
            );

            $coverFishResult->addFailureToStream($lineInfo);
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
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
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
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
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
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
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
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
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
        if (true === $this->outputFormatJson || 0 === $this->outputLevel) {
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
     * print out fail testFile EOL message
     *
     * @param CoverFishResult $coverFishResult
     */
    private function writeFileFail(CoverFishResult $coverFishResult)
    {
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

        $this->scanFailure = true;
        $this->writeLine($fileResult);
    }

    /**
     * print out pass testFile EOL message
     */
    private function writeFilePass()
    {
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
            PHP_EOL
        );

        $this->writeLine($fileResult);
    }

    private function writeScanPass()
    {
        $output = 'scan succeeded, no problems found.';

        $scanResultMacro = '%s';
        $scanResult = sprintf($scanResultMacro,
            (false === $this->preventAnsiColors)
                ? Color::tplGreenColor($output)
                : $output
        );

        echo $scanResult.PHP_EOL;
    }

    private function writeScanFail()
    {
        $output = 'scan failed, coverage problems found!';

        $scanResultMacro = '%s';
        $scanResult = sprintf($scanResultMacro,
            (false === $this->preventAnsiColors)
                ? Color::tplRedColor($output)
                : $output
        );

        echo $scanResult.PHP_EOL;
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