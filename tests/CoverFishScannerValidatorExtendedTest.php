<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Common\CoverFishMessageError;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishScannerValidatorExtendedTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.9
 * @version   0.9.9
 */
class CoverFishScannerValidatorExtendedTest extends BaseCoverFishScannerTestCase
{
    /**
     * check for covered ClassName::<private> annotation "no private methods", cover is invalid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::setValidationError
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::clearValidationErrors
     *
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorNotPrivate
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<private> annotation "private methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorPrivate
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<public> annotation "public methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorPublic
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND, $errorCode);
        }
    }

    /**
     * check for covered ClassName::<protected> annotation "protected methods found", cover is valid!
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassNameMethodAccess::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorProtected
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
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorNotPublic
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND, $errorCode);
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
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorNotProtected
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND, $errorCode);
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
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClassForAccessorNotPrivate
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND, $errorCode);
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