<?php

namespace DF\PHPCoverFish;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoverFishScanCommand
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.2
 */
class CoverFishScanCommand extends Command
{
    /**
     * additional options and arguments for our cli application
     */
    protected function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('open source php code coverage preprocessor')
            ->setHelp($this->getHelpOutput())
            ->addArgument(
                'scan-path',
                InputArgument::REQUIRED,
                'the source path of your corresponding phpunit test files or a specific testFile (e.g. tests/ or tests/mySampleClassTest.php)'
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
                'output-prevent-echo',
                null,
                InputOption::VALUE_OPTIONAL,
                'prevent direct echo on output, return json object directly',
                false
            )
            ->addOption(
                'debug',
                'd',
                InputOption::VALUE_OPTIONAL,
                'output debug level information (not available in alpha)',
                false
            )
            ->addOption(
                'stop-on-error',
                null,
                InputOption::VALUE_OPTIONAL,
                'stop on first application error (not available in alpha)',
                false
            )
            ->addOption(
                'stop-on-failure',
                null,
                InputOption::VALUE_OPTIONAL,
                'stop on first detected coverFish failure (not available in alpha)',
                false
            )
            ->addOption(
                'warning-threshold-stop',
                null,
                InputOption::VALUE_OPTIONAL,
                'numbers of allowed warnings before scan will be stopped (not available in alpha)',
                99
            )
        ;
    }

    /**
     * @return string
     */
    public function getHelpOutput()
    {
        $help  = PHP_EOL.'The <comment>alpha</comment> version of <info>phpCoverFish</info> wont be as functional as the coming beta version.'.PHP_EOL;
        $help .= 'Specific commands like choosable output-detail-level and coverage warning features,'.PHP_EOL;
        $help .= 'including corresponding threshold break warnings, aren\'t functional yet. This version'.PHP_EOL;
        $help .= 'will be validate the three major coverage annotation usages: "<comment>ClassName::methodName</comment>",'.PHP_EOL;
        $help .= '"<comment>ClassName</comment>" and "<comment>::methodName</comment>", the beta version will handle all annotation set\'s'.PHP_EOL;
        $help .= 'provided in phpunit documentation.'. PHP_EOL . PHP_EOL;
        $help .= '';

        return $help;
    }

    /**
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
     * @return string
     */
    public function getLongVersion()
    {
        return sprintf('v%s [%s]', $this->getVersion(), CoverFishScanner::APP_RELEASE_STATE);
    }

    /**
     * @param OutputInterface $output
     */
    protected function showExecTitle(OutputInterface $output)
    {
        $output->writeln(sprintf('<info>%s</info> <comment>%s</comment>', CoverFishScanner::APP_RELEASE_NAME, $this->getLongVersion()));
        $output->writeln(sprintf('%s%s','*** developer preview ***', PHP_EOL));
    }

    /**
     * @param $input
     *
     * @return bool
     */
    protected function getBoolFromInput($input)
    {
        // define empty option argument as true flagged option
        if (true === empty($input)) {
            return true;
        }

        $result = array();
        preg_match_all('/\d+/',$input , $result, PREG_SET_ORDER);
        if (false === empty($result) ) {
            return $input === '1' ? true : false;
        }

        return $input === 'true' ? true : false;
    }

    /**
     * execute command "scan"
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->showExecTitle($output);

        $cliOptions = array(
            'sys_scan_source' => $input->getArgument('scan-path'),
            'sys_debug' => $input->getOption('debug'),
            'sys_stop_on_error' => $input->getOption('stop-on-error'),
            'sys_stop_on_failure' => $input->getOption('stop-on-failure'),
            'sys_warning_threshold' => (int) $input->getOption('warning-threshold-stop'),
        );

        $outOptions = array(
            'out_verbose' => $input->getOption('verbose'),
            'out_format' => $input->getOption('output-format'),
            'out_level' => (int) $input->getOption('output-level'),
            'out_no_ansi' => $input->getOption('no-ansi'),
            'out_no_echo' => $input->getOption('output-prevent-echo'),
        );

        if ($testPathOrFile = $input->getArgument('scan-path')) {
            $scanner = new CoverFishScanner($cliOptions, $outOptions);
            $scanner->analysePHPUnitFiles();
        }
    }
}