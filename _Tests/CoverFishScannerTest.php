<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;

/**
 * Class CoverageScanner
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.2
 */
class CoverFishScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $testFile
     *
     * @return array
     */
    public function getDefaultCLIOptions($testFile)
    {
        return array(
            'sys_scan_source' => $testFile,
            'sys_debug' => false,
            'sys_stop_on_error' => false,
            'sys_stop_on_failure' => false,
            'sys_warning_threshold' => 99,
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
     * check for covered className (FQN) annotation "class missing"
     */
    public function testCoverClassFullyQualifiedNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassFQNameFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassFQNamePassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNamePassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorDefaultCoverClassFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorDefaultCoverClassPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorGlobalMethodFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorGlobalMethodPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameMethodNameFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameMethodNamePassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameMethodNameClassFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorPrivateFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorPublicFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorPublicPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorProtectedFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorProtectedPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotPublicFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotPublicPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<!protected> annotation "no not protected methods", cover is invalid!
     */
    public function testCoverClassNameAccessorNoNotProtectedMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotProtectedFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
            $this->assertEquals(2005, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!protected> annotation "no not protected methods found", cover is valid!
     */
    public function testCoverClassNameAccessorNoNotProtectedMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotProtectedPassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }

    /**
     * check for covered ClassName::<!private> annotation "no not private methods", cover is invalid!
     */
    public function testCoverClassNameAccessorNoNotPrivateMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotPrivateFailTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

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
            $this->assertEquals(2006, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!private> annotation "no not private methods found", cover is valid!
     */
    public function testCoverClassNameAccessorNoNotPrivateMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(sprintf('%s/Data/Tests/ValidatorClassNameAccessorNoNotPrivatePassTest.php', __DIR__)),
            $this->getDefaultOutputOptions()
        );

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }
}