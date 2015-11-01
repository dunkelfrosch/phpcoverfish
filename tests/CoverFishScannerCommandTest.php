<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use DF\PHPCoverFish\CoverFishScanCommand;

use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishScannerCommandTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.8
 * @version   1.0.0
 */
class CoverFishScannerCommandTest extends BaseCoverFishScannerTestCase
{

    /**
     * @return array
     */
    public function getDefaultRawCLIOptions()
    {
        return [
            '--raw-scan-path' => sprintf('%s/', __DIR__),
            '--raw-autoload-file' => sprintf('%s/../vendor/autoload.php', __DIR__),
            '--raw-exclude-path' => sprintf('%s/data/', __DIR__),
            '--no-ansi' => true,
        ];
    }

    /**
     * @return array
     */
    public function getDefaultPHPUnitCLIOptions()
    {
        return [
            'phpunit-config' => sprintf('%s/phpunit.xml', __DIR__),
            '--no-ansi' => true,
        ];
    }

    /**
     * @covers DF\PHPCoverFish\CoverFishScanCommand::execute
     * @covers DF\PHPCoverFish\CoverFishScanCommand::prepareExecute
     * @covers DF\PHPCoverFish\CoverFishScanCommand::configure
     * @covers DF\PHPCoverFish\CoverFishScanCommand::showExecTitle
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeResult
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeResultHeadlines
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeSingleMappingResult
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeFinalCheckResults
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeFileName
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::getFileResultTemplate
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeFileResult
     * @covers DF\PHPCoverFish\Common\Base\BaseCoverFishOutput::writeScanPassStatistic
     * @covers DF\PHPCoverFish\Common\Base\BaseCoverFishOutput::getProgressTemplate
     * @covers DF\PHPCoverFish\Common\Base\BaseCoverFishOutput::writeProgress
     * @covers DF\PHPCoverFish\Common\Base\BaseCoverFishOutput::outputResult
     */
    public function testScanByPhpUnitCommandOutputLevelOne()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultPHPUnitCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 1,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateConfigInfoScanModePHPUnit($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelOne($commandTester->getDisplay());
        $this->validateCoverFishAppFooterInfo($commandTester->getDisplay());
    }

    public function testScanByPhpUnitCommandOutputLevelTwo()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultPHPUnitCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 2,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelTwo($commandTester->getDisplay());
        $this->validateConfigInfoScanModePHPUnit($commandTester->getDisplay());
    }

    public function testScanByPhpUnitCommandOutputLevelZero()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultPHPUnitCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 0,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelZero($commandTester->getDisplay());
    }

    public function testScanByRawCommandOutputLevelOne()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultRawCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 1,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateConfigInfoScanModeRaw($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelOne($commandTester->getDisplay());
        $this->validateCoverFishAppFooterInfo($commandTester->getDisplay());
    }

    public function testScanByRawCommandOutputLevelTwo()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultRawCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 2,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateConfigInfoScanModeRaw($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelTwo($commandTester->getDisplay());
        $this->validateCoverFishAppFooterInfo($commandTester->getDisplay());
    }

    public function testScanByRawCommandOutputLevelZero()
    {
        $application = new Application();
        $application->add(new CoverFishScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                $this->getDefaultRawCLIOptions(),
                [
                    'command' => $command->getName(),
                    '--output-level' => 0,
                ]
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelZero($commandTester->getDisplay());
    }

    /**
     * @param $output
     */
    public function validateCoverFishSelfTestLevelZero($output)
    {
        $this->assertRegExp('/[_]+[.]+/', $output);
    }

    /**
     * @param $output
     */
    public function validateCoverFishSelfTestLevelOne($output)
    {
        $this->assertRegExp('/(scan file CoverFishArrayCollectionTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishCollectionTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishErrorTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishHelperTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerCommandTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerCommandTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerValidatorBaseTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerValidatorExtendedTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
    }

    /**
     * @param $output
     */
    public function validateCoverFishSelfTestLevelTwo($output)
    {
        $this->assertRegExp('/(scan file)([ ]{1})(CoverFishArrayCollectionTest.php)/', $output);
        $this->assertRegExp('/(->[ ]{1}public[ ]{1})(testCheckToArray)/', $output);
        $this->assertRegExp('/(=>[ ]{1})(file\/test[ ]{1}OK)/', $output);
    }

    /**
     * @param $output
     */
    public function validateConfigInfoScanModeRaw($output)
    {
        $this->assertRegExp('/(switch in raw scan mode, using commandline parameters)/', $output);
        $this->assertRegExp('/(test source path for scan:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(exclude test source path:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(autoload file:)([ ]{1})(.+)(autoload.php)/', $output);

        $this->validateCoverFishAppFooterInfo($output);
    }

    /**
     * @param $output
     */
    public function validateConfigInfoScanModePHPUnit($output)
    {
        $this->assertRegExp('/(switch in phpunit-config scan mode, using phpunit-config file)([ ]{1}["]{1})(.+)(phpunit.xml)(["]{1})/', $output);
        $this->assertRegExp('/(test source path for scan:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(exclude test source path:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(autoload file:)([ ]{1})(.+)(bootstrap.php)/', $output);

        $this->validateCoverFishAppFooterInfo($output);
    }

    /**
     * @param $output
     */
    public function validateCoverFishAppTitle($output)
    {
        $this->assertRegExp(sprintf('/(%s[ ]{1}v[0-9]+.[0-9]+.[0-9]+[ ])/', CoverFishScanner::APP_RELEASE_NAME), $output);
    }

    /**
     * @param $output
     */
    public function validateCoverFishAppFooterInfo($output)
    {
        $this->assertRegExp('/([0-9]+[ ]{1})(file[(]s[)] and )([0-9]+)( method[(]s[)] scanned, scan succeeded)/', $output);
    }
}