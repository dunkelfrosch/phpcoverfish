<?php

namespace DF\PHPCoverFish\Tests\Base;

use DF\PHPCoverFish\Common\CoverFishHelper;

/**
 * Class BaseCoverFishScannerTestCase
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.4
 */
class BaseCoverFishScannerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * @return CoverFishHelper
     */
    public function getCoverFishHelper()
    {
        $this->coverFishHelper = new CoverFishHelper();

        return $this->coverFishHelper;
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
            'sys_debug' => false,
            'sys_stop_on_error' => false,
            'sys_stop_on_failure' => false,
            'sys_warning_threshold' => 99,
            'sys_phpunit_config' => null,
            'sys_phpunit_config_test_suite' => null,
        );
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

        return in_array('SampleClass.php', $fileNames)
        && in_array('SampleClassNoNotPublicMethods.php', $fileNames)
        && in_array('SampleClassNoPrivateMethods.php', $fileNames)
        && in_array('SampleClassNoProtectedMethods.php', $fileNames)
        && in_array('SampleClassNoPublicMethods.php', $fileNames)
        && in_array('SampleClassOnlyPrivateMethods.php', $fileNames)
        && in_array('SampleClassOnlyProtectedMethods.php', $fileNames)
        && in_array('SampleClassOnlyPublicMethods.php', $fileNames);
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

    public function getSampleClassMethodData($file)
    {
        $classData = $this->getSampleClassData($file);
        foreach ($classData['methods'] as $methodName => $methodData) {
            // test class contained only one file, so leave iterator after first method found
            $methodData['classFile'] = (string) $classData['classFile'];
            return $methodData;
        }

        return null;
    }
}