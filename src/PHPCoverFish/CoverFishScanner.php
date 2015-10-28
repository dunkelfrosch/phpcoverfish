<?php

namespace DF\PHPCoverFish;

use DF\PHPCoverFish\Base\BaseCoverFishScanner;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Validator\ValidatorClassName;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess;
use DF\PHPCoverFish\Validator\ValidatorClassNameMethodName;
use DF\PHPCoverFish\Validator\ValidatorMethodName;
use DF\PHPCoverFish\Common\CoverFishOutput;
use Symfony\Component\Console\Output\OutputInterface;
use \PHP_Token_Stream;

/**
 * Class CoverFishScanner
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   1.0.0
 */
class CoverFishScanner extends BaseCoverFishScanner
{
    const APP_RELEASE_NAME = 'PHPCoverFish';
    const APP_RELEASE_STATE = 'stable';

    const APP_VERSION_MAJOR = 1;
    const APP_VERSION_MINOR = 0;
    const APP_VERSION_BUILD = 0;

    /**
     * @param array           $cliOptions
     * @param array           $outputOptions
     * @param OutputInterface $output
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $cliOptions, array $outputOptions, OutputInterface $output)
    {
        parent::__construct($cliOptions);
        $this->coverFishOutput = new CoverFishOutput($outputOptions, $output, $this);
    }

    /**
     * init all available cover validator classes use incoming coverToken as parameter
     *
     * @param $coverToken
     */
    public function addCoverageValidatorPoolForCover($coverToken)
    {
        // covers ClassName::methodName
        $this->addValidator(new ValidatorClassNameMethodName($coverToken));
        // covers ::methodName
        $this->addValidator(new ValidatorMethodName($coverToken));
        // covers ClassName
        $this->addValidator(new ValidatorClassName($coverToken));
        // covers ClassName::accessor (for public, protected, private, !public, !protected, !private)
        $this->addValidator(new ValidatorClassNameMethodAccess($coverToken));
    }

    /**
     * scan all unit-test files inside specific path
     *
     * @return string
     */
    public function analysePHPUnitFiles()
    {
        $testFiles = $this->scanFilesInPath($this->testSourcePath);
        foreach ($testFiles as $file) {
            $this->analyseClassesInFile($file);
        }

        return $this->coverFishOutput->writeResult($this->coverFishResult);
    }

    /**
     * scan all annotations inside one single unit test file
     *
     * @param array                $phpDocBlock
     * @param CoverFishPHPUnitTest $phpUnitTest
     */
    public function analyseCoverAnnotations($phpDocBlock, CoverFishPHPUnitTest $phpUnitTest)
    {
        $this->validatorCollection->clear();
        $phpUnitTest->clearCoverMappings();
        $phpUnitTest->clearCoverAnnotation();

        /** @var string $cover */
        foreach ($phpDocBlock['covers'] as $cover) {
            $phpUnitTest->addCoverAnnotation($cover);
            $this->addCoverageValidatorPoolForCover($cover);
        }

        $phpUnitTest = $this->validateAndReturnMapping($phpUnitTest);
        $this->phpUnitFile->addTest($phpUnitTest);
    }

    /**
     * scan all classes inside one defined unit test file
     *
     * @param string $file
     *
     * @return array
     */
    public function analyseClassesInFile($file)
    {
        $ts = new PHP_Token_Stream($file);
        $this->phpUnitFile = new CoverFishPHPUnitFile();

        foreach ($ts->getClasses() as $className => $classData) {
            $fqnClass = sprintf('%s\\%s',
                $this->coverFishHelper->getAttributeByKey('namespace', $classData['package']),
                $className
            );

            if (false === $this->coverFishHelper->isValidTestClass($fqnClass)) {
                continue;
            }

            $classData['className'] = $className;
            $classData['classFile'] = $file;
            $this->analyseClass($classData);
        }
    }

    /**
     * scan (test) class and add result to our coverFishResultCollection
     *
     * @param array  $classData
     *
     * @return array
     */
    public function analyseClass(array $classData)
    {
        // scan main test class annotation
        $this->analyseCoverAnnotations(
            $this->coverFishHelper->parseCoverAnnotationDocBlock($classData['docblock']),
            $this->setPHPUnitTestByClassData($classData)
        );

        // iterate through all available methods in give test class ignore all "non-test" methods
        foreach ($classData['methods'] as $methodName => $methodData) {

            // ignore all non-test- and docblock free methods for deep scan process
            if (false === $this->getCoverFishHelper()->isValidTestMethod($methodName) ||
                false === array_key_exists('docblock', $methodData)) {
                continue;
            }

            $methodData['classFile'] = (string) $classData['classFile'];
            // scan unit test method annotation
            $this->analyseCoverAnnotations(
                $this->coverFishHelper->parseCoverAnnotationDocBlock($methodData['docblock']),
                $this->setPHPUnitTestByMethodData($methodData)
            );
        }

        // add final phpUnitFile structure including mapping result to our coverFishResult
        $this->coverFishResult->addUnit($this->phpUnitFile);
    }
}