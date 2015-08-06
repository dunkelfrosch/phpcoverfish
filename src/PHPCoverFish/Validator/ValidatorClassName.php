<?php

namespace DF\PHPCoverFish\Validator;

use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Validator\Base\CoverFishValidator;

/**
 * Class ValidatorClassName, validate that the annotated Class exists (Class)
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
class ValidatorClassName extends CoverFishValidator
{
    /**
     * @return array
     */
    private function execute()
    {
        preg_match_all("/(?P<class>(^(([\\\\])|([A-Z]))([A-Za-z0-9_\\\\]+)$))/", $this->coversToken, $this->result, PREG_SET_ORDER);

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
        return array_key_exists('class', $this->getResult());
    }

    /**
     * @param CoverFishPHPUnitFile $phpUnitFile
     *
     * @return array
     */
    public function getMapping(CoverFishPHPUnitFile $phpUnitFile)
    {
        $type = null;
        $method = null;
        $class = $this->getResult()['class'];
        // fqn detected? fully qualified classNames will be used directly without any kind of counterCheck
        // against use statement(s) - otherwise classFQN will be taken from use statement directly.
        if (true === $this->checkClassHasFQN($class)) {
            $classFQN = $class;
            $class = $this->coverFishHelper->getClassNameFromClassFQN($classFQN);
        } else {
            $classFQN = $this->coverFishHelper->getClassFromUse($class, $phpUnitFile->getUsedClasses());
        }

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
        return 'Specifies that the annotated class covers all methods in this class.';
    }

    /**
     * @return string
     */
    public function getValidationTag()
    {
        return 'ClassName';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}