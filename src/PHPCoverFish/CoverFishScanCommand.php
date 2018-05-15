<?php

namespace DF\PHPCoverFish;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DF\PHPCoverFish\Exception\CoverFishFailExit;
use DF\PHPCoverFish\Common\CoverFishHelper;

/**
 * Class CoverFishScanCommand
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   1.0.2
 */
class CoverFishScanCommand extends Command
{
    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * additional options and arguments for our cli application
     */
    protected function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('scan phpunit test files for static code analysis')
            ->setHelp($this->getHelpOutput())
            ->addArgument(
                'phpunit-config',
                InputArgument::OPTIONAL,
                'the source path of your corresponding phpunit xml config file (e.g. ./tests/phpunit.xml)'
            )
            ->addOption(
                'phpunit-config-suite',
                null,
                InputOption::VALUE_OPTIONAL,
                'name of the target test suite inside your php config xml file, this test suite will be scanned'
            )
            ->addOption(
                'raw-scan-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'raw mode option: the source path of your corresponding phpunit test files or a specific testFile (e.g. tests/), this option will always override phpunit.xml settings!'
            )
            ->addOption(
                'raw-autoload-file',
                null,
                InputOption::VALUE_OPTIONAL,
                'raw-mode option: your application autoload file and path (e.g. ../app/autoload.php for running in symfony context), this option will always override phpunit.xml settings!'
            )
            ->addOption(
                'raw-exclude-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'raw-mode option: exclude a specific path from planned scan',
                null
            )
            ->addOption(
                'output-format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'output format of scan result (json|text)',
                'text'
            )
            ->addOption(
                'output-level',
                'l',
                InputOption::VALUE_OPTIONAL,
                'level of output information (0:minimal, 1: normal (default), 2: detailed)',
                1
            )
            ->addOption(
                'stop-on-error',
                null,
                InputOption::VALUE_OPTIONAL,
                'stop on first application error raises',
                false
            )
            ->addOption(
                'stop-on-failure',
                null,
                InputOption::VALUE_OPTIONAL,
                'stop on first detected coverFish failure raises',
                false
            )
        ;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getHelpOutput()
    {
        // print out some "phpUnit-Mode" runtime samples
        $help  = sprintf('%sscan by using your "phpunit.xml" config-file inside "Tests/" directory and using test suite "My Test Suite" directly with normal output-level and no ansi-colors:%s', PHP_EOL, PHP_EOL);
        $help .= sprintf('<comment>php</comment> <info>./bin/coverfish</info> <info>scan</info> <comment>./Tests/phpunit.xml</comment> --phpunit-config-suite "<comment>My Test Suite</comment>" --output-level <comment>1</comment> --no-ansi%s', PHP_EOL);
        $help .= sprintf('%sscan by using your "phpunit.xml" config-file inside "Tests/" directory without any given testSuite (so first suite will taken) with normal output-level and no ansi-colors:%s', PHP_EOL, PHP_EOL);
        $help .= sprintf('<comment>php</comment> <info>./bin/coverfish</info> <info>scan</info> <comment>./Tests/phpunit.xml</comment> --output-level <comment>1</comment> --no-ansi%s', PHP_EOL);
        $help .= sprintf('%ssame scan with maximum output-level and disabled ansi output:%s', PHP_EOL, PHP_EOL);
        $help .= sprintf('<comment>php</comment> <info>./bin/coverfish</info> <info>scan</info> <comment>./Tests/phpunit.xml</comment> --output-level <comment>2</comment>%s', PHP_EOL);

        // print out some "raw-Mode" runtime samples
        $help .= sprintf('%sscan by using raw-mode, using "Tests/" directory as base scan path, autoload file "vendor/autoload.php" and exclude "Tests/data/" with normal output-level and no ansi-colors:%s', PHP_EOL, PHP_EOL);
        $help .= sprintf('<comment>php</comment> <info>./bin/coverfish</info> <info>scan</info> --raw-scan-path <comment>./Tests/phpunit.xml</comment> --raw-autoload-file <comment>vendor/autoload.php</comment> --raw-exclude-path <comment>Tests/data</comment> --output-level <comment>1</comment> --no-ansi%s', PHP_EOL);

        return $help;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getVersion()
    {
        return sprintf('%s.%s.%s',
            CoverFishScanner::APP_VERSION_MAJOR,
            CoverFishScanner::APP_VERSION_MINOR,
            CoverFishScanner::APP_VERSION_BUILD
        );
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getLongVersion()
    {
        return sprintf('v%s [%s]', $this->getVersion(), CoverFishScanner::APP_RELEASE_STATE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function showExecTitle(InputInterface $input, OutputInterface $output)
    {
        /** @var string $outputLevelMsg */
        $outputLevelMsg = 'minimal';
        switch ((int) $input->getOption('output-level')) {
            case 1:
                $outputLevelMsg = 'moderate';
                break;
            case 2:
                $outputLevelMsg = 'maximum';
                break;
        }

        $output->writeln(
            sprintf('<info>%s</info> <comment>%s</comment>%sstart scan process using %s output level',
                CoverFishScanner::APP_RELEASE_NAME,
                $this->getLongVersion(),
                PHP_EOL,
                $outputLevelMsg
            )
        );
    }

    /**
     * exec command "scan"
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->showExecTitle($input, $output);
        $this->prepareExecute($input);

        $cliOptions = array(
            'sys_phpunit_config' => $input->getArgument('phpunit-config'),
            'sys_phpunit_config_test_suite' => $input->getOption('phpunit-config-suite'),
            'sys_stop_on_error' => $input->getOption('stop-on-error'),
            'sys_stop_on_failure' => $input->getOption('stop-on-failure'),
            'raw_scan_source' => $input->getOption('raw-scan-path'),
            'raw_scan_autoload_file' => $input->getOption('raw-autoload-file'),
            'raw_scan_exclude_path' => $input->getOption('raw-exclude-path'),
        );

        $outOptions = array(
            'out_verbose' => $input->getOption('verbose'),
            'out_format' => $input->getOption('output-format'),
            'out_level' => (int) $input->getOption('output-level'),
            'out_no_ansi' => $input->getOption('no-ansi'),
            'out_no_echo' => $input->getOption('quiet'),
        );

        try {
            $scanner = new CoverFishScanner($cliOptions, $outOptions, $output);
            $scanner->analysePHPUnitFiles();
        } catch (CoverFishFailExit $e) {
            return CoverFishFailExit::RETURN_CODE_SCAN_FAIL;
        }

        return 0;
    }

    /**
     * prepare exec of command "scan"
     *
     * @param InputInterface $input
     *
     * @throws \Exception
     */
    public function prepareExecute(InputInterface $input)
    {
        $this->coverFishHelper = new CoverFishHelper();

        $phpUnitConfigFile = $input->getArgument('phpunit-config');
        if (false === empty($phpUnitConfigFile) &&
            false === $this->coverFishHelper->checkFileOrPath($phpUnitConfigFile)) {
            throw new \Exception(sprintf('phpunit config file "%s" not found! please define your phpunit.xml config file to use (e.g. tests/phpunit.xml)', $phpUnitConfigFile));
        }

        $testPathOrFile = $input->getOption('raw-scan-path');
        if (false === empty($testPathOrFile) &&
            false === $this->coverFishHelper->checkFileOrPath($testPathOrFile)) {
            throw new \Exception(sprintf('test path/file "%s" not found! please define test file path (e.g. tests/)', $testPathOrFile));
        }
    }
}