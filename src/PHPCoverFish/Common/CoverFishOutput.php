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

            $this->jsonResult['pass'] = false;
            $this->jsonResult['file'] = $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile());
            $this->jsonResult['fileFQN'] = $coverFishUnitFile->getFile();
            $this->write(sprintf('check file -> %s ', $this->coverFishHelper->getFileNameFromPath($coverFishUnitFile->getFile())));

            /** @var CoverFishPHPUnitTest $coverFishTest */
            foreach ($coverFishUnitFile->getTests() as $coverFishTest) {

                /** @var CoverFishMapping $coverMappings */
                foreach ($coverFishTest->getCoverMappings() as $coverMappings) {
                    if (false === $coverMappings->getValidatorResult()->isPass()) {
                        $this->writeFailure();
                        $this->writeFailureStream($coverFishResult, $coverFishTest, $coverMappings);
                    } else {
                        $this->writeProgress();
                    }
                }
            }

            // handle failure tests, output additional info from corresponding failure stream directly
            $lineStatus = sprintf(' [fail]%s%s', PHP_EOL, $coverFishResult->getFailureStream());
            if (false === $this->preventAnsiColors) {
                $lineStatus = sprintf(' [%s]%s%s', Color::tplRedColor('fail'), PHP_EOL, $coverFishResult->getFailureStream());
            }

            // test passed? so print out short result info
            if (0 === $coverFishResult->getFailureCount()) {
                $this->jsonResult['pass'] = true;
                $lineStatus = sprintf(' [pass]');
                if (false === $this->preventAnsiColors) {
                    $lineStatus = sprintf(' [%s]', Color::tplGreenColor('pass'));
                }
            }

            $this->writeLine($lineStatus);
            $this->jsonResults[] = $this->jsonResult;
        }

        if (true === $this->outputFormatJson) {

            if (true === $this->preventEcho) {
                return json_encode($this->jsonResults);
            }

            echo json_encode($this->jsonResults);
        }

        return null;
    }

    /**
     * @param $content
     *
     * @return null on json
     */
    public function writeLine($content)
    {
        if (true === $this->outputFormatJson) {
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
        if (true === $this->outputFormatJson) {
            return null;
        }

        echo sprintf('%s', $content);
    }

    /**
     *
     *
     * @param CoverFishResult      $coverFishResult
     * @param CoverFishPHPUnitTest $unitTest
     * @param CoverFishMapping     $coverMapping
     */
    public function writeFailureStream(CoverFishResult $coverFishResult, CoverFishPHPUnitTest $unitTest, CoverFishMapping $coverMapping)
    {
        /** @var CoverFishError $mappingError */
        foreach ($coverMapping->getValidatorResult()->getErrors() as $mappingError) {
            $coverFishResult->addFailureCount();
            $coverLine = $mappingError->getErrorStreamTemplate($coverMapping, $this->preventAnsiColors);

            $this->jsonResult['error'] = $coverFishResult->getFailureCount();
            $this->jsonResult['errorMessage'] = $mappingError->getTitle();
            $this->jsonResult['errorCode'] = $mappingError->getErrorCode();
            $this->jsonResult['cover'] = $coverLine;
            $this->jsonResult['method'] = $unitTest->getName();
            $this->jsonResult['line'] = $unitTest->getLine();
            $this->jsonResult['file'] = $unitTest->getFile();

            $coverFishResult->addFailureToStream(PHP_EOL);
            $lineInfo = sprintf('Error #%s in method "%s", Line ~%s (File: %s)%s', $coverFishResult->getFailureCount(), $unitTest->getName(), $unitTest->getLine(), $unitTest->getFile(), PHP_EOL);
            $lineAnnotation = sprintf('Annotation: %s%s', $coverLine, PHP_EOL);
            $lineMessage = sprintf('Message: %s (ErrorCode: %s) %s',$mappingError->getTitle(), $mappingError->getErrorCode(), PHP_EOL);
            if (false === $this->preventAnsiColors) {
                $lineInfo = sprintf('Error #%s in method "%s", Line ~%s (File: %s)%s', Color::tplWhiteColor($coverFishResult->getFailureCount()), Color::tplWhiteColor($unitTest->getName()), Color::tplWhiteColor($unitTest->getLine()), $unitTest->getFile(), PHP_EOL);
                $lineAnnotation = sprintf('%s: %s%s',Color::tplDarkGrayColor('Annotation'), $coverLine, PHP_EOL);
                $lineMessage = sprintf('%s: %s (ErrorCode: %s) %s',Color::tplDarkGrayColor('Message'), $mappingError->getTitle(), $mappingError->getErrorCode(), PHP_EOL);
            }

            $coverFishResult->addFailureToStream($lineInfo);
            $coverFishResult->addFailureToStream($lineAnnotation);
            $coverFishResult->addFailureToStream($lineMessage);
        }
    }

    /**
     * write (colored) progress dot
     *
     * @return null on json
     */
    public function writeProgress()
    {
        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = '.';
        if (false === $this->preventAnsiColors) {
            $output = "\033[32;40m.\033[0m";
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
        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = 'F';
        if (false === $this->preventAnsiColors) {
            $output = "\033[33;41mF\033[0m";
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
        if (true === $this->outputFormatJson) {
            return null;
        }

        $output = 'E';
        if (false === $this->preventAnsiColors) {
            $output = "\033[30;43mE\033[0m";
        }

        echo $output;
    }
}