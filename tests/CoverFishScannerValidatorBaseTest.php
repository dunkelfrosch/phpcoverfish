<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Common\CoverFishMessageError;
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
 * @version   0.9.9
 */
class CoverFishScannerValidatorTest extends BaseCoverFishScannerTestCase
{
    /**
     * check for covered className (FQN) annotation "class missing"
     *
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::execute
     * @covers DF\PHPCoverFish\Validator\ValidatorClassName::<public>
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::__construct
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::getResult
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::setMapping
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateMapping
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::validateReflectionClass
     * @covers DF\PHPCoverFish\Validator\Base\BaseCoverFishValidator::clearValidationErrors
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseClass
     * @covers DF\PHPCoverFish\CoverFishScanner::analysePHPUnitFiles
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseClassesInFile
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseMethodPHPDocAnnotation
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::validateAndReturnMapping
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setPHPUnitTestByMethodData
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
        }
    }

    /**
     * test new class phpdoc cover annotation placement validator (fail)
     *
     * @covers DF\PHPCoverFish\CoverFishScanner::analyseClassPHPDocAnnotation
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::setPHPUnitTestByClassData
     */
    public function testCoverCompleteClassFullyQualifiedNameValidatorCheckForValidClassFail()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorCompleteClassFQNameFailTest.php', __DIR__));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $errorCode = (int) $result->errorCode;
            $this->assertFalse($pass);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
        }
    }

    /**
     * test new class phpdoc cover annotation placement validator (pass)
     */
    public function testCoverCompleteClassFullyQualifiedNameValidatorCheckForValidClassPass()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = $this->getDefaultCoverFishScanner(sprintf('%s/data/tests/ValidatorCompleteClassFQNamePassTest.php', __DIR__));

        /** @var array $jsonResult */
        $jsonResult = json_decode($scanner->analysePHPUnitFiles());

        /** @var \stdClass $jsonResult */
        foreach ($jsonResult as $result) {
            $pass = (bool) $result->pass;
            $failure = (bool) $result->failure;
            $this->assertTrue($pass);
            $this->assertFalse($failure);

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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM, $errorCode);
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $errorCode);
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $errorCode);
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
            $error = (bool) $result->failure;

            $this->assertTrue($error);
            $this->assertEquals(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $errorCode);
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
}