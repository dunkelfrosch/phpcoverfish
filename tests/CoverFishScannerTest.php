<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;
use DF\PHPCoverFish\Validator\ValidatorMethodName;

/**
 * Class CoverFishScannerTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.7
 */
class CoverFishScannerTest extends BaseCoverFishScannerTestCase
{
    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setConfigFromPHPUnitConfigFile
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getTestAutoloadPath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getTestExcludePath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getTestSourcePath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getAttributeFromXML
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getTestSuiteNodeFromXML
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getTestSuitePropertyFromXML
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getPathFromFileNameAndPath
     */
    public function testSetConfigFromUnitTestConfigFileUseFirstSuite()
    {
        $configArray = $this->getDefaultCLIOptions(null);
        $configArray['sys_phpunit_config'] = sprintf('%s/data/phpunit.xml', __DIR__);
        $configArray['sys_phpunit_config_test_suite'] = 'PHPCoverFishTestSuiteA';

        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($configArray, $this->getDefaultOutputOptions());
        $scanner->setConfigFromPHPUnitConfigFile();

        $this->assertEquals('vendor/autoload.php', str_replace($scanner->getPhpUnitConfigPath(), null, $scanner->getTestAutoloadPath()));
        $this->assertEquals('tests/exclude', str_replace($scanner->getPhpUnitConfigPath(), null, $scanner->getTestExcludePath()));
        $this->assertEquals('.', str_replace($scanner->getPhpUnitConfigPath(), null, $scanner->getTestSourcePath()));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setConfigFromPHPUnitConfigFile
     */
    public function testSetConfigFromUnitTestConfigFileUseSecondSuite()
    {
        $configArray = $this->getDefaultCLIOptions(null);
        $configArray['sys_phpunit_config'] = sprintf('%s/data/phpunit.xml', __DIR__);
        $configArray['sys_phpunit_config_test_suite'] = 'PHPCoverFishTestSuiteB';

        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($configArray, $this->getDefaultOutputOptions());
        $scanner->setConfigFromPHPUnitConfigFile();

        $this->assertEquals('tests/exclude2', str_replace($scanner->getPhpUnitConfigPath(), null, $scanner->getTestExcludePath()));
        $this->assertEquals('.', str_replace($scanner->getPhpUnitConfigPath(), null, $scanner->getTestSourcePath()));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::addValidator
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getValidatorCollection
     */
    public function testAddValidator()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(null, null),
            $this->getDefaultOutputOptions()
        );

        $scanner->addValidator(new ValidatorMethodName('::methodName'));
        $this->assertEquals(1, $scanner->getValidatorCollection()->count());
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setPHPUnitTestMetaData
     */
    public function testSetUnitFileTestMetaData()
    {
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/data/tests/ValidatorClassFQNameFailTest.php', __DIR__), null),
            $this->getDefaultOutputOptions()
        );

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
        $configArray = $this->getDefaultCLIOptions(null);
        $configArray['sys_phpunit_config'] = sprintf('%s/data/phpunit.xml', __DIR__);
        $configArray['sys_phpunit_config_test_suite'] = 'PHPCoverFishTestSuiteB';

        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($configArray, $this->getDefaultOutputOptions());

        $this->assertTrue($scanner->checkSourceAutoload($configArray['sys_phpunit_config']));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::scanFilesInPath
     */
    public function testScanFilesInPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(null, null),
            $this->getDefaultOutputOptions()
        );

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data/src', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }

    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getRegexPath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::removeExcludedPath
     */
    public function testScanFilesAndIgnoreExcludedPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(null, 'tests/data/tests'),
            $this->getDefaultOutputOptions()
        );

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }
}