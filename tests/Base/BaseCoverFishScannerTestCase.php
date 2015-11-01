<?php

namespace DF\PHPCoverFish\Tests\Base;

use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\CoverFishScanner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCoverFishScannerTestCase
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   1.0.0
 */
class BaseCoverFishScannerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function setUp()
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * @return CoverFishHelper
     */
    public function getCoverFishHelper()
    {
        $this->coverFishHelper = new CoverFishHelper();

        return $this->coverFishHelper;
    }

    /**
     * @param null|string $testSource
     * @param null|string $excludePath
     *
     * @return CoverFishScanner
     */
    public function getDefaultCoverFishScanner($testSource = null, $excludePath = null)
    {
        return new CoverFishScanner(
            $this->getDefaultCLIOptions($testSource, $excludePath),
            $this->getDefaultOutputOptions(),
            $this->output
        );
    }

    /**
     * @return CoverFishScanner
     */
    public function getPHPUnitCoverFishScannerAlpha()
    {
        return new CoverFishScanner(
            $this->getPHPUnitCLIOptionsAlpha(null),
            $this->getDefaultOutputOptions(),
            $this->output
        );
    }

    /**
     * @return CoverFishScanner
     */
    public function getPHPUnitCoverFishScannerBeta()
    {
        return new CoverFishScanner(
            $this->getPHPUnitCLIOptionsBeta(null),
            $this->getDefaultOutputOptions(),
            $this->output
        );
    }

    /**
     * @param string      $testSource
     * @param string|null $excludePath
     *
     * @return array
     */
    public function getDefaultCLIOptions($testSource, $excludePath = null)
    {
        return array(
            'raw_scan_source' => $testSource,
            'raw_scan_autoload_file' => 'vendor/autoload.php',
            'raw_scan_exclude_path' => $excludePath,
            'sys_stop_on_error' => false,
            'sys_stop_on_failure' => false,
            'sys_phpunit_config' => null,
            'sys_phpunit_config_test_suite' => null,
        );
    }

    /**
     * @param string $testSource
     * @param null   $excludePath
     *
     * @return array
     */
    public function getPHPUnitCLIOptionsAlpha($testSource, $excludePath = null)
    {
        $configArray = $this->getDefaultCLIOptions($testSource, $excludePath);
        $configArray['sys_phpunit_config'] = sprintf('%s/../data/phpunit.xml', __DIR__);
        $configArray['sys_phpunit_config_test_suite'] = 'PHPCoverFishTestSuiteA';

        return $configArray;
    }

    /**
     * @param string $testSource
     * @param null   $excludePath
     *
     * @return array
     */
    public function getPHPUnitCLIOptionsBeta($testSource, $excludePath = null)
    {
        $configArray = $this->getDefaultCLIOptions($testSource, $excludePath);
        $configArray['sys_phpunit_config'] = sprintf('%s/../data/phpunit.xml', __DIR__);
        $configArray['sys_phpunit_config_test_suite'] = 'PHPCoverFishTestSuiteB';

        return $configArray;
    }

    /**
     * @return array
     */
    public function getDefaultOutputOptions()
    {
        return array(
            'out_verbose' => false,
            'out_format' => 'json',
            'out_level' => 1,
            'out_no_ansi' => true,
            'out_no_echo' => true,
        );
    }

    /**
     * @param array $files
     *
     * @return bool
     */
    public function validateTestsDataSrcFixturePathContent($files)
    {
        $fileNames = array();
        /** @var string $file */
        foreach ($files as $file) {
            $fileNames[] = $this->getCoverFishHelper()->getFileNameFromPath($file);
        }

        return
            in_array('SampleClass.php', $fileNames) &&
            in_array('SampleClassNoNotPublicMethods.php', $fileNames) &&
            in_array('SampleClassNoPrivateMethods.php', $fileNames) &&
            in_array('SampleClassNoProtectedMethods.php', $fileNames) &&
            in_array('SampleClassNoPublicMethods.php', $fileNames) &&
            in_array('SampleClassOnlyPrivateMethods.php', $fileNames) &&
            in_array('SampleClassOnlyProtectedMethods.php', $fileNames) &&
            in_array('SampleClassOnlyPublicMethods.php', $fileNames);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function getSampleClassData($file)
    {
        $classData = array();
        $ts = new \PHP_Token_Stream($file);
        foreach ($ts->getClasses() as $className => $classData) {
            $classData['className'] = $className;
            $classData['classFile'] = $file;
        }

        return $classData;
    }

    /**
     * @param $file
     * @return null
     */
    public function getSampleClassMethodData($file)
    {
        $classData = $this->getSampleClassData($file);
        foreach ($classData['methods'] as $methodName => $methodData) {
            // test if class contains only one file, leave iterator after first method found
            $methodData['classFile'] = (string) $classData['classFile'];
            return $methodData;
        }

        return null;
    }
}