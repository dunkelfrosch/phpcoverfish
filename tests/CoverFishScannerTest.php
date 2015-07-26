<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;

/**
 * Class CoverageScanner
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
class CoverFishScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $testFile
     *
     * @return array
     */
    public function getDefaultScannerOptions($testFile)
    {
        return array(
            'arg_test_file_src' => $testFile,
            'opt_mode_debug' => false,
            'opt_mode_verbose' => false,
            'opt_stop_on_error' => false,
            'opt_stop_on_failure' => false,
            'opt_warning_threshold' => 99,
            'opt_output_format' => 'json',
            'opt_output_no_echo' => true,
            'opt_output_level' => 1,
            'opt_no_ansi' => true
        );
    }

    /**
     * check for covered className (FQN) annotation "class missing"
     */
    public function testCoverClassFullyQualifiedNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassFQNameFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(1000, $errorCode);
        }
    }

    /**
     * check for covered className (FQN) annotation "class found", cover is valid!
     */
    public function testCoverClassFullyQualifiedNameValidatorCheckForValidClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassFQNamePassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered className annotation "class missing"
     */
    public function testCoverClassNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(1000, $errorCode);
        }
    }

    /**
     * check for covered className annotation "class found", cover is valid!
     */
    public function testCoverClassNameValidatorCheckForValidClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNamePassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ::method annotation "defaultCoverClass not found", cover is invalid!
     */
    public function testCoverGlobalMethodNameValidatorCheckForDefaultCoverClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorDefaultCoverClassFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(9001, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "defaultCoverClass found", cover is invalid!
     */
    public function testCoverGlobalMethodNameValidatorCheckForDefaultCoverClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorDefaultCoverClassPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ::method annotation "method not found", cover is invalid!
     */
    public function testCoverGlobalMethodNameValidatorCheckMethodFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorGlobalMethodFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2000, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "method found", cover is valid!
     */
    public function testCoverGlobalMethodNameValidatorCheckMethodPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorGlobalMethodPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::method annotation "method not found", cover is invalid!
     */
    public function testCoverClassNameValidatorCheckMethodNameFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameMethodNameFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {

           if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2000, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "method found", cover is valid!
     */
    public function testCoverClassNameValidatorCheckMethodNamePass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameMethodNamePassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::method annotation "class not found", cover is invalid!
     */
    public function testCoverClassNameValidatorCheckClassNameFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameMethodNameClassFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {

            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(1000, $errorCode);
        }
    }

    /**
     * check for covered ClassName::method annotation "class found", cover is invalid!
     */
    public function testCoverClassNameValidatorCheckClassNamePass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<private> annotation "no private methods", cover is invalid!
     */
    public function testCoverClassNameAccessorPrivateMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorPrivateFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2003, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<private> annotation "private methods found", cover is valid!
     */
    public function testCoverClassNameAccessorPrivateMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<public> annotation "no public methods", cover is invalid!
     */
    public function testCoverClassNameAccessorPublicMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorPublicFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2001, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<public> annotation "public methods found", cover is valid!
     */
    public function testCoverClassNameAccessorPublicMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorPublicPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<protected> annotation "no protected methods", cover is invalid!
     */
    public function testCoverClassNameAccessorProtectedMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorProtectedFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2002, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<protected> annotation "protected methods found", cover is valid!
     */
    public function testCoverClassNameAccessorProtectedMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorProtectedPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<!public> annotation "no not public methods", cover is invalid!
     */
    public function testCoverClassNameAccessorNoNotPublicMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPublicFailTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {

            if (true === (bool) $result->pass) {
                continue;
            }

            $errorCode = (int) $result->errorCode;
            $error = (bool) $result->error;

            $this->assertTrue($error);
            $this->assertEquals(2004, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!public> annotation "no not public methods found", cover is valid!
     */
    public function testCoverClassNameAccessorNoNotPublicMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner($this->getDefaultScannerOptions(
            sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPublicPassTest.php', __DIR__)
        ));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }
}