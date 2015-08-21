<?php

namespace DF\PHPCoverFish\Tests;

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
 * @version   0.9.8
 */
class CoverFishScannerCommandTest extends BaseCoverFishScannerTestCase
{
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                'phpunit-config' => __DIR__.'/phpunit.xml',
                '--output-level' => 1,
                '--no-ansi' => true
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                'phpunit-config' => __DIR__ . '/phpunit.xml',
                '--output-level' => 2,
                '--no-ansi' => true
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                'phpunit-config' => __DIR__ . '/phpunit.xml',
                '--output-level' => 0,
                '--no-ansi' => true
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                '--raw-scan-path' => __DIR__.'/',
                '--raw-autoload-file' => __DIR__.'/../vendor/autoload.php',
                '--raw-exclude-path' => __DIR__.'/data/',
                '--output-level' => 1,
                '--no-ansi' => true
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                '--raw-scan-path' => __DIR__.'/',
                '--raw-autoload-file' => __DIR__.'/../vendor/autoload.php',
                '--raw-exclude-path' => __DIR__.'/data/',
                '--output-level' => 2,
                '--no-ansi' => true
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
        $commandTester->execute(array(
                'command' => $command->getName(),
                '--raw-scan-path' => __DIR__.'/',
                '--raw-autoload-file' => __DIR__.'/../vendor/autoload.php',
                '--raw-exclude-path' => __DIR__.'/data/',
                '--output-level' => 0,
                '--no-ansi' => true
            )
        );

        $this->validateCoverFishAppTitle($commandTester->getDisplay());
        $this->validateCoverFishSelfTestLevelZero($commandTester->getDisplay());
    }

    public function validateCoverFishSelfTestLevelZero($output)
    {
        $this->assertRegExp('/[_]+[.]+/', $output);
    }

    public function validateCoverFishSelfTestLevelOne($output)
    {
        $this->assertRegExp('/(scan file BaseCoverFishScannerTestCase.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CollectionTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishArrayCollectionTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishErrorTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishHelperTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerCommandTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);

        $this->assertRegExp('/(scan file CoverFishScannerCommandTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
        $this->assertRegExp('/(scan file CoverFishScannerValidatorTest.php)([ ]{1})(.+)([ ]{1})(OK)/', $output);
    }

    public function validateCoverFishSelfTestLevelTwo($output)
    {
        $this->assertRegExp('/(scan file)([ ]{1})(BaseCoverFishScannerTestCase.php)/', $output);
        $this->assertRegExp('/(->[ ]{1}public[ ]{1})(setUp[(]{1}[)]{1})([ ]{1}[:]{1}[ ]{1})([S]{1})/', $output);
        $this->assertRegExp('/(=>[ ]{1}cover test[(]s[)]{1} succeeded)/', $output);
    }

    public function validateConfigInfoScanModeRaw($output)
    {
        $this->assertRegExp('/(using raw scan mode, reading parameter ...)/', $output);
        $this->assertRegExp('/(test source path for scan:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(exclude test source path:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(autoload file:)([ ]{1})(.+)(autoload.php)/', $output);

        $this->validateCoverFishAppFooterInfo($output);
    }

    public function validateConfigInfoScanModePHPUnit($output)
    {
        $this->assertRegExp('/(using phpunit scan mode, phpunit-config file)([ ]{1}["]{1})(.+)(phpunit.xml)(["]{1})/', $output);
        $this->assertRegExp('/(test source path for scan:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(exclude test source path:)([ ]{1})(.+)(.{1})/', $output);
        $this->assertRegExp('/(autoload file:)([ ]{1})(.+)(bootstrap.php)/', $output);

        $this->validateCoverFishAppFooterInfo($output);
    }

    public function validateCoverFishAppTitle($output)
    {
        $this->assertRegExp('/(PHPCoverFish[ ]{1}v[0-9]+.[0-9]+.[0-9]+[ ])/', $output);
    }

    public function validateCoverFishAppFooterInfo($output)
    {
        $this->assertRegExp('/([0-9]+[ ]{1})(file[(]s[)] and )([0-9]+)( method[(]s[)] scanned, scan succeeded, no problems found.)/', $output);
    }
}