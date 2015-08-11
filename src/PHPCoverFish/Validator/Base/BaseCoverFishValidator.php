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
 * @version   0.9.5
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
     * @param CoverFishMapping $coverMapping
     *
     * @return CoverFishMappingResult
     */
    public function validateMapping(CoverFishMapping $coverMapping)
    {
        /** @var CoverFishMappingResult $mappingResult */
        $mappingResult = new CoverFishMappingResult();
        $mappingResult->setPass(true);

        // 1 - check for classFQN/DefaultCoverClass mapping validation-error
        if (empty($coverMapping->getClassFQN()) && empty($coverMapping->getClass())) {
            $mappingResult->setPass(false);
            $mappingResult->addError(new CoverFishError(
                CoverFishError::PHPUNIT_VALIDATOR_MISSING_DEFAULT_COVER_CLASS_PROBLEM, null)
            );

            return $mappingResult;
        }

        // 2 - check for classFQN mapping validation-error
        if (empty($coverMapping->getClassFQN())) {
            $mappingResult->setPass(false);
            $mappingResult->addError(new CoverFishError(CoverFishError::PHPUNIT_REFLECTION_CLASS_NOT_DEFINED, null));

            return $mappingResult;
        }

        // 3 - check for invalid classFQN validation-error
        $classReflectionResult = $this->validateReflectionClass($coverMapping->getClassFQN());
        // @todo: mappingResult could be false, set special coverFishError here ...
        if ($classReflectionResult instanceof CoverFishError) {
            $mappingResult->setPass(false);
            $mappingResult->addError($classReflectionResult);

            return $mappingResult;
        }

        // 4 - check for invalid accessor validation-error
        $methodReflectionResult = $this->validateReflectionClassForAccessorVisibility($coverMapping->getClassFQN(), $coverMapping->getAccessor());
        if ($methodReflectionResult instanceof CoverFishError) {
            $mappingResult->setPass(false);
            $mappingResult->addError($methodReflectionResult);

            return $mappingResult;
        }

        // 5 - check for invalid method validation-error
        $methodReflectionResult = $this->validateReflectionMethod($coverMapping->getClassFQN(), $coverMapping->getMethod());
        if ($methodReflectionResult instanceof CoverFishError) {
            $mappingResult->setPass(false);
            $mappingResult->addError($methodReflectionResult);

            return $mappingResult;
        }

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
     * @param string $coversToken
     */
    public function __construct($coversToken)
    {
        $this->coversToken = $coversToken;
        $this->validatorCollection = new ArrayCollection();
        $this->coverFishHelper = new CoverFishHelper();
    }
}