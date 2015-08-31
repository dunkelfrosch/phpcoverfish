<?php

namespace DF\PHPCoverFish\Validator\Base;

use DF\PHPCoverFish\Common\ArrayCollection;
use DF\PHPCoverFish\Common\CoverFishMessageError;
use DF\PHPCoverFish\Common\CoverFishResult;
use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Common\CoverFishMapping;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Exception\CoverFishFailExit;

/**
 * Class BaseCoverFishValidator
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.9
 */
class BaseCoverFishValidator implements BaseCoverFishValidatorInterface
{
    /**
     * @var ArrayCollection
     */
    protected $validatorCollection;

    /**
     * @var string
     */
    protected $coversToken = null;

    /**
     * @var CoverFishResult
     */
    protected $coverFishResult = null;

    /**
     * @var array
     */
    protected $result = array();

    /**
     * @var CoverFishHelper
     */
    protected $coverFishHelper;

    /**
     * @return array
     */
    public function getResult()
    {
        if (count($this->result) > 0) {
            return $this->result[0];
        }

        return null;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getValidationInfo()
    {
        return null;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getValidationTag()
    {
        return null;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param CoverFishPHPUnitFile $phpUnitFile
     *
     * @return CoverFishMapping
     */
    public function getMapping(CoverFishPHPUnitFile $phpUnitFile)
    {
        return new CoverFishMapping();
    }

    /**
     * @param array $mappingOptions
     *
     * @return CoverFishMapping
     */
    public function setMapping(array $mappingOptions)
    {
        $coverMapping = new CoverFishMapping();
        $coverMapping->setAnnotation($mappingOptions['coverToken']);
        $coverMapping->setMethod($mappingOptions['coverMethod']);
        $coverMapping->setAccessor($mappingOptions['coverAccessor']);
        $coverMapping->setClass($mappingOptions['coverClass']);
        $coverMapping->setClassFQN($mappingOptions['coverClassFQN']);
        $coverMapping->setValidatorMatch($mappingOptions['validatorMatch']);
        $coverMapping->setValidatorClass($mappingOptions['validatorClass']);
        $coverMapping->setValidatorResult($this->validateMapping($coverMapping));

        return $coverMapping;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return false;
    }

    /**
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishResult
     */
    public function validateDefaultCoverClassMapping(CoverFishMapping $coverMapping)
    {
        if (empty($coverMapping->getClassFQN())) {
            $this->coverFishResult = $this->setValidationError(
                empty($coverMapping->getClass())
                ? CoverFishMessageError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM
                : CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED
            );
        }

        return $this->coverFishResult;
    }

    /**
     * @todo: mappingResult could be false, set special coverFishError here ...
     *
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishResult
     */
    public function validateClassFQNMapping(CoverFishMapping $coverMapping)
    {
        $classReflectionResult = $this->validateReflectionClass($coverMapping->getClassFQN());
        if ($classReflectionResult instanceof CoverFishMessageError) {
            $this->coverFishResult = $this->setValidationError(
                $classReflectionResult->getMessageCode()
            );
        }

        return $this->coverFishResult;
    }

    /**
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishResult
     */
    public function validateClassAccessorVisibility(CoverFishMapping $coverMapping)
    {
        $methodReflectionResult = $this->validateReflectionClassForAccessorVisibility($coverMapping->getClassFQN(), $coverMapping->getAccessor());
        if ($methodReflectionResult instanceof CoverFishMessageError) {
            $this->coverFishResult = $this->setValidationError(
                $methodReflectionResult->getMessageCode()
            );
        }

        return $this->coverFishResult;
    }

    /**
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishResult
     */
    public function validateClassMethod(CoverFishMapping $coverMapping)
    {
        $methodReflectionResult = $this->validateReflectionMethod($coverMapping->getClassFQN(), $coverMapping->getMethod());
        if ($methodReflectionResult instanceof CoverFishMessageError) {
            $this->coverFishResult = $this->setValidationError(
                $methodReflectionResult->getMessageCode()
            );
        }

        return $this->coverFishResult;
    }

    /**
     * main validator mapping "engine", if any of our cover validator checks will fail,
     * return corresponding result immediately ...
     *
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishResult
     */
    public function validateMapping(CoverFishMapping $coverMapping)
    {
        // cleanUp validation mapping result for current scan
        $coverFishResult = $this->clearValidationErrors();
        // 01: check for classFQN/DefaultCoverClass existence/mapping validation-error
        $coverFishResult = $this->validateDefaultCoverClassMapping($coverMapping);
        // 02: check for invalid classFQN validation-error
        $coverFishResult = $this->validateClassFQNMapping($coverMapping);
        // 03: check for invalid accessor validation-error
        $coverFishResult = $this->validateClassAccessorVisibility($coverMapping);
        // 04: check for invalid method validation-error
        $coverFishResult = $this->validateClassMethod($coverMapping);

       // var_dump($coverFishResult->isStopOnFailure());die;

        return $coverFishResult;
    }

    /**
     * @param string $classFQN
     *
     * @return CoverFishMessageError|\ReflectionClass
     */
    public function validateReflectionClass($classFQN)
    {
        return $this->getReflectionClass($classFQN);
    }

    /**
     * @param string $classFQN
     * @param string $method
     *
     * @return CoverFishMessageError|\ReflectionMethod|false
     */
    public function validateReflectionMethod($classFQN, $method)
    {
        if (null === $classFQN || null === $method) {
            return false;
        }

        try {
            $reflectionMethod = new \ReflectionMethod($classFQN, $method);
        } catch (\ReflectionException $re) {
            return new CoverFishMessageError(CoverFishMessageError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $method);
        }

        return $reflectionMethod;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorPublic(\ReflectionClass $reflectionClass)
    {
        if (empty($methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC))) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorNotPublic(\ReflectionClass $reflectionClass)
    {
        $methods = array_merge(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE),
            $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED)
        );

        if (empty($methods)) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorProtected(\ReflectionClass $reflectionClass)
    {
        if (empty($methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED))) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorNotProtected(\ReflectionClass $reflectionClass)
    {
        $methods = array_merge(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE),
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        if (empty($methods)) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorPrivate(\ReflectionClass $reflectionClass)
    {
        if (empty($methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE))) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorNotPrivate(\ReflectionClass $reflectionClass)
    {
        $methods = array_merge(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED),
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        if (empty($methods)) {
            return new CoverFishMessageError(
                CoverFishMessageError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND, null
            );
        }

        return true;
    }

    /**
     * @param string $classFQN
     * @param string $accessor
     *
     * @return bool|CoverFishMessageError
     */
    public function validateReflectionClassForAccessorVisibility($classFQN, $accessor)
    {
        $reflectionClass = $this->getReflectionClass($classFQN);
        if ($reflectionClass instanceof CoverFishMessageError) {
            return $reflectionClass;
        }

        $accessorResult = null;

        switch ($accessor) {

            case 'public':
                $accessorResult = $this->validateReflectionClassForAccessorPublic($reflectionClass);

                break;

            case 'protected':
                $accessorResult = $this->validateReflectionClassForAccessorProtected($reflectionClass);

                break;

            case 'private':
                $accessorResult = $this->validateReflectionClassForAccessorPrivate($reflectionClass);

                break;

            case '!public':
                $accessorResult = $this->validateReflectionClassForAccessorNotPublic($reflectionClass);

                break;

            case '!protected':
                $accessorResult = $this->validateReflectionClassForAccessorNotProtected($reflectionClass);

                break;

            case '!private':
                $accessorResult = $this->validateReflectionClassForAccessorNotPrivate($reflectionClass);

                break;

            default:
                return false;
        }

        if ($accessorResult instanceof CoverFishMessageError) {
            return $accessorResult;
        }

        return true;
    }

    /**
     * @param string $classFQN
     *
     * @return CoverFishMessageError|\ReflectionClass
     */
    public function getReflectionClass($classFQN)
    {
        try {

            $reflectionClass = new \ReflectionClass($classFQN);

        } catch (\ReflectionException $re) {
            return new CoverFishMessageError(CoverFishMessageError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $classFQN);
        }

        return $reflectionClass;
    }

    /**
     * @return CoverFishResult
     */
    public function clearValidationErrors()
    {
        $this->coverFishResult->setPass(true);
        $this->coverFishResult->clearErrors();

        return $this->coverFishResult;
    }

    /**
     * @param int             $errorCode
     * @param string|null     $errorMessage
     *
     * @return CoverFishResult
     *
     * @throws CoverFishFailExit
     */
    public function setValidationError($errorCode, $errorMessage = null)
    {
        // skip validation if incoming mapping result is already invalid ...
        if (false === $this->coverFishResult->isPass()) {
            return $this->coverFishResult;
        }

        $this->coverFishResult->setPass(false);
        $this->coverFishResult->addError(new CoverFishMessageError($errorCode, $errorMessage));

        return $this->coverFishResult;
    }

    /**
     * @param string $coversToken
     */
    public function __construct($coversToken, CoverFishResult $coverFishResult)
    {
        $this->coversToken = $coversToken;
        $this->coverFishHelper = new CoverFishHelper();
        $this->coverFishResult = $coverFishResult;
        $this->validatorCollection = new ArrayCollection();
    }
}