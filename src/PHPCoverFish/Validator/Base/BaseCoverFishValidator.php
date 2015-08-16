<?php

namespace DF\PHPCoverFish\Validator\Base;

use DF\PHPCoverFish\Common\ArrayCollection;
use DF\PHPCoverFish\Common\CoverFishError;
use DF\PHPCoverFish\Common\CoverFishHelper;
use DF\PHPCoverFish\Common\CoverFishMapping;
use DF\PHPCoverFish\Common\CoverFishMappingResult;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;

/**
 * Class BaseCoverFishValidator
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.7
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
     * @return string
     */
    public function getValidationInfo()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getValidationTag()
    {
        return null;
    }

    /**
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
     * @param CoverFishMapping       $coverMapping
     * @param CoverFishMappingResult $mappingResult
     *
     * @return CoverFishMappingResult
     */
    public function validateDefaultCoverClassMapping(CoverFishMapping $coverMapping, CoverFishMappingResult $mappingResult)
    {
        if (empty($coverMapping->getClassFQN())) {
            $mappingResult = $this->setValidationError(
                $mappingResult,
                empty($coverMapping->getClass())
                ? CoverFishError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM
                : CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED
            );
        }

        return $mappingResult;
    }

    /**
     * @todo: mappingResult could be false, set special coverFishError here ...
     *
     * @param CoverFishMapping       $coverMapping
     * @param CoverFishMappingResult $mappingResult
     *
     * @return CoverFishMappingResult
     */
    public function validateClassFQNMapping(CoverFishMapping $coverMapping, CoverFishMappingResult $mappingResult)
    {
        $classReflectionResult = $this->validateReflectionClass($coverMapping->getClassFQN());
        if ($classReflectionResult instanceof CoverFishError) {
            $mappingResult = $this->setValidationError(
                $mappingResult,
                $classReflectionResult->getErrorCode()
            );
        }

        return $mappingResult;
    }

    /**
     * @param CoverFishMapping       $coverMapping
     * @param CoverFishMappingResult $mappingResult
     *
     * @return CoverFishMappingResult
     */
    public function validateClassAccessorVisibility(CoverFishMapping $coverMapping, CoverFishMappingResult $mappingResult)
    {
        $methodReflectionResult = $this->validateReflectionClassForAccessorVisibility($coverMapping->getClassFQN(), $coverMapping->getAccessor());
        if ($methodReflectionResult instanceof CoverFishError) {
            $mappingResult = $this->setValidationError(
                $mappingResult,
                $methodReflectionResult->getErrorCode()
            );
        }

        return $mappingResult;
    }

    /**
     * @param CoverFishMapping       $coverMapping
     * @param CoverFishMappingResult $mappingResult
     *
     * @return CoverFishMappingResult
     */
    public function validateClassMethod(CoverFishMapping $coverMapping, CoverFishMappingResult $mappingResult)
    {
        $methodReflectionResult = $this->validateReflectionMethod($coverMapping->getClassFQN(), $coverMapping->getMethod());
        if ($methodReflectionResult instanceof CoverFishError) {
            $mappingResult = $this->setValidationError(
                $mappingResult,
                $methodReflectionResult->getErrorCode()
            );
        }

        return $mappingResult;
    }

    /**
     * main validator mapping "engine", if any of our cover validator checks will fail,
     * return corresponding result immediately ...
     *
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishMappingResult
     */
    public function validateMapping(CoverFishMapping $coverMapping)
    {
        /** @var CoverFishMappingResult $mappingResult */
        $mappingResult = new CoverFishMappingResult();
        $mappingResult = $this->clearValidationErrors($mappingResult);

        // 01: check for classFQN/DefaultCoverClass existence/mapping validation-error
        $mappingResult = $this->validateDefaultCoverClassMapping($coverMapping, $mappingResult);
        // 02: check for invalid classFQN validation-error
        $mappingResult = $this->validateClassFQNMapping($coverMapping, $mappingResult);
        // 03: check for invalid accessor validation-error
        $mappingResult = $this->validateClassAccessorVisibility($coverMapping, $mappingResult);
        // 04: check for invalid method validation-error
        $mappingResult = $this->validateClassMethod($coverMapping, $mappingResult);

        return $mappingResult;
    }

    /**
     * @param string $classFQN
     *
     * @return CoverFishError|\ReflectionClass
     */
    public function validateReflectionClass($classFQN)
    {
        return $this->getReflectionClass($classFQN);
    }

    /**
     * @param string $classFQN
     * @param string $method
     *
     * @return CoverFishError|\ReflectionMethod|false
     */
    public function validateReflectionMethod($classFQN, $method)
    {
        if (null === $classFQN || null === $method) {
            return false;
        }

        try {
            $reflectionMethod = new \ReflectionMethod($classFQN, $method);
        } catch (\ReflectionException $re) {
            return new CoverFishError(CoverFishError::PHPUNIT_REFLECTION_METHOD_NOT_FOUND, $method);
        }

        return $reflectionMethod;
    }

    /**
     * @param string $classFQN
     * @param string $accessor
     *
     * @return bool|CoverFishError
     */
    public function validateReflectionClassForAccessorVisibility($classFQN, $accessor)
    {
        if (null === $classFQN || null === $accessor) {
            return false;
        }

        $reflectionClass = $this->getReflectionClass($classFQN);
        if ($reflectionClass instanceof CoverFishError) {
            return $reflectionClass;
        }

        switch ($accessor) {
            case 'public':
                $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_PUBLIC_METHODS_FOUND, null
                    );
                }

                break;

            case 'protected':
                $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED);
                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_PROTECTED_METHODS_FOUND, null
                    );
                }

                break;

            case 'private':
                $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_PRIVATE_METHODS_FOUND, null
                    );
                }

                break;

            case '!public':
                $methods = array_merge(
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE),
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED)
                );

                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PUBLIC_METHODS_FOUND, null
                    );
                }

                break;

            case '!protected':
                $methods = array_merge(
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE),
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC)
                );

                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PROTECTED_METHODS_FOUND, null
                    );
                }

                break;

            case '!private':
                $methods = array_merge(
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED),
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC)
                );

                if (empty($methods)) {
                    return new CoverFishError(
                        CoverFishError::PHPUNIT_REFLECTION_NO_NOT_PRIVATE_METHODS_FOUND, null
                    );
                }

                break;

            default:
                return false;
        }

        return true;
    }

    /**
     * @param string $classFQN
     *
     * @return CoverFishError|\ReflectionClass
     */
    public function getReflectionClass($classFQN)
    {
        try {

            $reflectionClass = new \ReflectionClass($classFQN);

        } catch (\ReflectionException $re) {
            return new CoverFishError(CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_FOUND, $classFQN);
        }

        return $reflectionClass;
    }

    /**
     * check if class got fully qualified name
     *
     * @param string $class
     *
     * @return bool
     */
    public function checkClassHasFQN($class)
    {
        preg_match_all('/(\\\\+)/', $class, $result, PREG_SET_ORDER);

        return count($result) > 0;
    }

    /**
     * @param CoverFishMappingResult $mappingResult
     *
     * @return CoverFishMappingResult
     */
    public function clearValidationErrors(CoverFishMappingResult $mappingResult)
    {
        $mappingResult->setPass(true);
        $mappingResult->clearErrors();

        return $mappingResult;
    }

    /**
     * @param CoverFishMappingResult $mappingResult
     * @param int                    $errorCode
     * @param string|null            $errorMessage
     *
     * @return CoverFishMappingResult
     */
    public function setValidationError(CoverFishMappingResult $mappingResult, $errorCode, $errorMessage = null)
    {
        // skip validation if incoming mapping result is already invalid!
        if (false === $mappingResult->isPass()) {
            return $mappingResult;
        }

        $mappingResult->setPass(false);
        $mappingResult->addError(new CoverFishError($errorCode, $errorMessage));

        return $mappingResult;
    }

    /**
     * @param string $coversToken
     */
    public function __construct($coversToken)
    {
        $this->coversToken = $coversToken;
        $this->validatorCollection = new ArrayCollection();
        $this->coverFishHelper = new CoverFishHelper();
    }
}