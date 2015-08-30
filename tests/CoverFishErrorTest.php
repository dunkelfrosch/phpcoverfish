<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\Common\CoverFishMessageError;
use DF\PHPCoverFish\Common\CoverFishMapping;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishErrorTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.7
 * @version   0.9.9
 */
class CoverFishErrorTest extends BaseCoverFishScannerTestCase
{
    /**
     * @covers DF\PHPCoverFish\Common\CoverFishMessageError::getErrorStreamTemplate
     */
    public function testCheckGetErrorStreamTemplate()
    {
        $coverMapping = new CoverFishMapping();

        $coverMapping->setClassFQN('ACME\SampleApp\Tests\ClassFQNameFailTest');
        $coverMapping->setClass('ClassFQNameFailTest');
        $coverMapping->setMethod('amazingTestMethod()');

        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest', $coverFishError->getErrorStreamTemplate($coverMapping, true));

        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::amazingTestMethod()', $coverFishError->getErrorStreamTemplate($coverMapping, true));

        $coverMapping->setAccessor('protected');
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<protected>', $coverFishError->getErrorStreamTemplate($coverMapping, true));
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<protected>', $coverFishError->getErrorStreamTemplate($coverMapping, true));
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<protected>', $coverFishError->getErrorStreamTemplate($coverMapping, true));

        $coverMapping->setAccessor('public');
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<public>', $coverFishError->getErrorStreamTemplate($coverMapping, true));
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<public>', $coverFishError->getErrorStreamTemplate($coverMapping, true));
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND);
        $this->assertEquals('@covers ACME\SampleApp\Tests\ClassFQNameFailTest::<public>', $coverFishError->getErrorStreamTemplate($coverMapping, true));

        $coverMapping->setAccessor('public');
        $coverFishError = $this->createCoverFishErrorByCode(CoverFishMessageError::PHPUNIT_VALIDATOR_PROBLEM);
        $this->assertNull($coverFishError->getErrorStreamTemplate($coverMapping, true));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\CoverFishMessage::__construct
     * @covers \DF\PHPCoverFish\Common\CoverFishMessage::getMessageCode
     * @covers \DF\PHPCoverFish\Common\CoverFishMessage::getMessageToken
     * @covers \DF\PHPCoverFish\Common\CoverFishMessage::getMessageTokens
     */
    public function testCheckErrorPhpUnitReflectionClassNotFound()
    {
        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_VALIDATOR_PROBLEM
        ));

        $this->assertTrue($this->validateCoverFishErrorByCode(
            CoverFishMessageError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM
        ));
    }

    /**
     * @param int $errorCode
     *
     * @return bool
     */
    public function validateCoverFishErrorByCode($errorCode)
    {
        $myExceptionMessage = sprintf('exceptionTestMessage%s', $errorCode);

        $coverFishError = $this->createCoverFishErrorByCode($errorCode, $myExceptionMessage);

        $valid  = $myExceptionMessage === $coverFishError->getExceptionMessage();
        $valid &= $errorCode === $coverFishError->getMessageCode();
        $valid &= $coverFishError->getMessageTokens()[$errorCode] === $coverFishError->getMessageToken();

        return (bool) $valid;
    }

    /**
     * @param int    $errorCode
     * @param string $errorMessage
     *
     * @return CoverFishMessageError
     */
    public function createCoverFishErrorByCode($errorCode, $errorMessage = null)
    {
        return new CoverFishMessageError(
            $errorCode,
            $errorMessage
        );
    }
}