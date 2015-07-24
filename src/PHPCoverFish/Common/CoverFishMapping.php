<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishMapping
 *
 * @package    DF\PHP\CoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since      class available since Release 0.9.0
 * @version    0.9.0
 */
class CoverFishMapping
{
    /**
     * @var string
     */
    private $annotation;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $classFQN;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $accessor;

    /**
     * @var string
     */
    private $validatorMatch;

    /**
     * @var string
     */
    private $validatorClass;

    /**
     * @var CoverFishMappingResult
     */
    private $validatorResult;

    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClassFQN()
    {
        return $this->classFQN;
    }

    /**
     * @param string $classFQN
     */
    public function setClassFQN($classFQN)
    {
        $this->classFQN = $classFQN;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getValidatorMatch()
    {
        return $this->validatorMatch;
    }

    /**
     * @param string $validatorMatch
     */
    public function setValidatorMatch($validatorMatch)
    {
        $this->validatorMatch = $validatorMatch;
    }

    /**
     * @return string
     */
    public function getValidatorClass()
    {
        return $this->validatorClass;
    }

    /**
     * @param string $validatorClass
     */
    public function setValidatorClass($validatorClass)
    {
        $this->validatorClass = $validatorClass;
    }

    /**
     * @return CoverFishMappingResult
     */
    public function getValidatorResult()
    {
        return $this->validatorResult;
    }

    /**
     * @param CoverFishMappingResult $validatorResult
     */
    public function setValidatorResult($validatorResult)
    {
        $this->validatorResult = $validatorResult;
    }

    /**
     * @return string
     */
    public function getAccessor()
    {
        return $this->accessor;
    }

    /**
     * @param string $accessor
     */
    public function setAccessor($accessor)
    {
        $this->accessor = $accessor;
    }
}