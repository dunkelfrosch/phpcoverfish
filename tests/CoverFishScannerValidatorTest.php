<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Common\CoverFishError;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishScannerValidatorTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.8
 */
class CoverFishScannerValidatorTest extends BaseCoverFishScannerTestCase
{
    /**
     * check for covered className (FQN) annotation "class missing"
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::setMapping
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateMapping
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClass
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::checkClassHasFQN
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::validateAndReturnMapping
     * @covers DF\PHPCoverFish\CoverFishScanner::analysePHPUnitFiles
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseClassesInFile
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseClass
     * @covers DF\PHPCoverFish\Common\CoverFishOutput::writeJsonFailureStream
     */
    public function testCoverClassFullyQualifiedNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassFQNameFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
        }
    }

    /**
     * check for covered className (FQN) annotation "class found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::<public>
     */
    public function testCoverClassFullyQualifiedNameValidatorCheckForValidClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassFQNamePassTest.php', __DIR__));

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
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::getReflectionClass
     */
    public function testCoverClassNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
        }
    }

    /**
     * check for covered className annotation "class found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::<public>
     */
    public function testCoverClassNameValidatorCheckForValidClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNamePassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionMethod
     */
    public function testCoverGlobalMethodNameValidatorCheckForDefaultCoverClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorDefaultCoverClassFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "defaultCoverClass found", cover is invalid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::<public>
     */
    public function testCoverGlobalMethodNameValidatorCheckForDefaultCoverClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorDefaultCoverClassPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::<public>
     */
    public function testCoverGlobalMethodNameValidatorCheckMethodFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorGlobalMethodFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "method found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorMethodName::<public>
     */
    public function testCoverGlobalMethodNameValidatorCheckMethodPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorGlobalMethodPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::<public>
     */
    public function testCoverClassNameValidatorCheckMethodNameFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameMethodNameFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ::method annotation "method found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::<public>
     */
    public function testCoverClassNameValidatorCheckMethodNamePass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameMethodNamePassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::<public>
     */
    public function testCoverClassNameValidatorCheckClassNameFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameMethodNameClassFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::method annotation "class found", cover is invalid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodName::<public>
     */
    public function testCoverClassNameValidatorCheckClassNamePass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorVisibility
     */
    public function testCoverClassNameAccessorPrivateMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorPrivateFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<private> annotation "private methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorPrivateMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameMethodNameClassPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorPublicMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorPublicFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<public> annotation "public methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorPublicMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorPublicPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorProtectedMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorProtectedFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<protected> annotation "protected methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorProtectedMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorProtectedPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotPublicMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPublicFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!public> annotation "no not public methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotPublicMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPublicPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotProtectedMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotProtectedFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!protected> annotation "no not protected methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotProtectedMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotProtectedPassTest.php', __DIR__));

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
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotPrivateMethodsFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPrivateFailTest.php', __DIR__));

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
            $this->assertEquals(CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<!private> annotation "no not private methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     */
    public function testCoverClassNameAccessorNoNotPrivateMethodsPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorClassNameAccessorNoNotPrivatePassTest.php', __DIR__));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());
        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $this->assertTrue($pass);
        }
    }
}