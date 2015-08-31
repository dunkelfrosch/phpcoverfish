<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\Common\CoverFishPHPUnitTest;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;
use DF\PHPCoverFish\Validator\ValidatorMethodName;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class CoverFishScannerTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.9
 */
class CoverFishScannerTest extends BaseCoverFishScannerTestCase
{
    public function setUp()
    {
        $this->output = new ConsoleOutput();
        parent::setUp();
    }

    /**
     * @covers DF\PHPCoverFish\CoverFishScanner::validateCodeCoverage
     */
    public function testCheckValidateCodeCoverage()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner();

        $testFile = 'ValidatorGlobalMethodPassTest.php';
        $methodData = $this->getSampleClassMethodData(sprintf('%s/data/tests/%s', __DIR__, $testFile));
        /** @var CoverFishPHPUnitTest $unitTest */
        $unitTest = $scanner->setPHPUnitTestByMethodData($methodData);
        $scanner->setPhpUnitTest($unitTest);

        $coverToken = '::myTestMethod';
        $scanner->validateCodeCoverage($coverToken);
        $this->assertGreaterThanOrEqual(4, $scanner->getValidatorCollection()->count());
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseScanner::xmlToArray
     */
    public function testCheckXmlToArray()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getPHPUnitCoverFishScannerAlpha();

        /** @var string $file */
        $file = sprintf('%s/data/phpunit.xml', __DIR__);
        /** @var \SimpleXMLElement $xmlDocument */
        $xmlDocument = simplexml_load_file($file);
        /** @var array $result */
        $result = $scanner->xmlToArray($xmlDocument);

        $this->assertArrayHasKey('@attributes', $result);
        $this->assertArrayHasKey('testsuites', $result);
        $this->assertArrayHasKey('backupGlobals', $result['@attributes']);
        $this->assertArrayHasKey('bootstrap', $result['@attributes']);
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseScanner::getTestAutoloadPath
     * @covers DF\PHPCoverFish\Base\BaseScanner::getTestExcludePath
     * @covers DF\PHPCoverFish\Base\BaseScanner::getTestSourcePath
     * @covers DF\PHPCoverFish\Base\BaseScanner::getAttributeFromXML
     * @covers DF\PHPCoverFish\Base\BaseScanner::getTestSuiteNodeFromXML
     * @covers DF\PHPCoverFish\Base\BaseScanner::getPhpUnitXMLPath
     * @covers DF\PHPCoverFish\Base\BaseScanner::getPhpUnitXMLFile
     * @covers DF\PHPCoverFish\Base\BaseScanner::getPhpUnitTestSuite
     * @covers DF\PHPCoverFish\Base\BaseScanner::getTestSuitePropertyFromXML
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setConfigFromPHPUnitConfigFile
     */
    public function testCheckSetConfigFromUnitTestConfigFileUseFirstSuite()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getPHPUnitCoverFishScannerAlpha();
        $scanner->setConfigFromPHPUnitConfigFile();

        $this->assertEquals('../data/vendor/autoload.php', str_replace($scanner->getPhpUnitXMLPath(), null, $scanner->getTestAutoloadPath()));
        $this->assertEquals('tests/exclude', str_replace($scanner->getPhpUnitXMLPath(), null, $scanner->getTestExcludePath()));
        $this->assertEquals('.', str_replace($scanner->getPhpUnitXMLPath(), null, $scanner->getTestSourcePath()));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setConfigFromPHPUnitConfigFile
     */
    public function testCheckSetConfigFromUnitTestConfigFileUseSecondSuite()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getPHPUnitCoverFishScannerBeta();
        $scanner->setConfigFromPHPUnitConfigFile();

        $this->assertEquals('tests/exclude2', str_replace($scanner->getPhpUnitXMLPath(), null, $scanner->getTestExcludePath()));
        $this->assertEquals('.', str_replace($scanner->getPhpUnitXMLPath(), null, $scanner->getTestSourcePath()));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::addValidator
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getValidatorCollection
     */
    public function testCheckAddValidator()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner();

        $scanner->addValidator(new ValidatorMethodName('::methodName', new CoverFishResult()));
        $this->assertEquals(1, $scanner->getValidatorCollection()->count());
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setPHPUnitTestByMethodData
     */
    public function testCheckSetPHPUnitTestData()
    {
        $testFile = 'ValidatorGlobalMethodPassTest.php';
        $methodData = $this->getSampleClassMethodData(sprintf('%s/data/tests/%s', __DIR__, $testFile));

        $scanner = $this->getDefaultCoverFishScanner();

        /** @var CoverFishPHPUnitTest $unitTestFile */
        $unitTestFile = $scanner->setPHPUnitTestByMethodData($methodData);

        $this->assertTrue($unitTestFile instanceof CoverFishPHPUnitTest);
        $this->assertEquals($testFile, $unitTestFile->getFile());
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setPHPUnitTestMetaData
     */
    public function testCheckSetUnitFileTestMetaData()
    {
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassFQNameFailTest.php', __DIR__));
        $scanner->analyseClassesInFile($scanner->getTestSourcePath());

        $this->assertEquals($scanner->getPhpUnitFile()->getFile(), $scanner->getTestSourcePath());
        $this->assertEquals($scanner->getPhpUnitFile()->getClassNameSpace(), 'DF\PHPCoverFish\Tests\Data\Tests');
        $this->assertEquals($scanner->getPhpUnitFile()->getUsedClasses(), array('DF\PHPCoverFish\Tests\Data\Src\SampleClass'));
        $this->assertEquals($scanner->getPhpUnitFile()->getParentClass(), '\PHPUnit_Framework_TestCase');
        $this->assertNull($scanner->getPhpUnitFile()->getCoversDefaultClass());
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::checkSourceAutoload
     */
    public function testCheckSourceAutoloadUsingUnitConfigFile()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getPHPUnitCoverFishScannerBeta();
        $this->assertTrue($scanner->checkSourceAutoload(sprintf('%s/data/vendor/autoload.php', __DIR__)));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::scanFilesInPath
     */
    public function testCheckScanFilesInPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner();

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data/src', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::removeExcludedPath
     */
    public function testCheckScanFilesAndIgnoreExcludedPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(null, 'tests/data/tests');

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }
}