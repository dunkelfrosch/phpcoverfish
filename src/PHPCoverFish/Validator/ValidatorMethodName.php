<?php

namespace DF\PHPCoverFish\Validator;

use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Validator\Base\CoverFishValidator;

/**
 * Class ValidatorMethodName, validate that the annotated test method covers the specified method (Class::Method)
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
class ValidatorMethodName extends CoverFishValidator
{
    /**
     * @return array
     */
    private function execute()
    {
        preg_match_all('/^(?P<sep>\A\::{1})(?P<method>\w+)$/', $this->coversToken, $this->result, PREG_SET_ORDER);

        return $this->result;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return count($this->execute()) > 0;
    }

    /**
     * @return bool
     */
    public function validateResultKeys()
    {
        return array_key_exists('sep', $this->getResult())
            && array_key_exists('method', $this->getResult()
        );
    }

    /**
     * @param CoverFishPHPUnitFile $phpUnitFile
     *
     * @return array
     */
    public function getMapping(CoverFishPHPUnitFile $phpUnitFile)
    {
        $type = null;
        $method = $this->getResult()['method'];
        $classFQN = $phpUnitFile->getCoversDefaultClass();
        $class = $this->coverFishHelper->getClassNameFromClassFQN($classFQN);

        $mappingOptions = array(
            'coverToken' => $this->coversToken,
            'coverMethod' => $method,
            'coverAccessor' => null,
            'coverClass' => $class,
            'coverClassFQN' => $classFQN,
            'validatorMatch' => $this->getValidationTag(),
            'validatorClass' => get_class($this)
        );

        return $this->setMapping($mappingOptions);
    }

    /**
     * @return string
     */
    public function getValidationInfo()
    {
        return 'Specifies that the annotated test method covers the specified global function.';
    }

    /**
     * @return string
     */
    public function getValidationTag()
    {
        return '::methodName';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}