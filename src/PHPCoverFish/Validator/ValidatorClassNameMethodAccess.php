<?php

namespace DF\PHPCoverFish\Validator;

use DF\PHPCoverFish\Common\CoverFishMapping;
use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Validator\Base\CoverFishValidator;

/**
 * Class ValidatorClassNameMethodAccess, validate that the annotated test method covers methods visibility
 * in given class (Class::<public>, Class::<protected>, Class::<private>)
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class ValidatorClassNameMethodAccess extends CoverFishValidator
{
    /**
     * @return array
     */
    private function execute()
    {
        preg_match_all('/^(?P<class>(^(([\\\\])|([A-Z]))([A-Za-z0-9_\\\\]+)))(?P<sep>::{1})((<{1})(?P<accessor>[!\w]+)(>{1})\s*\Z)$/', $this->coversToken, $this->result, PREG_SET_ORDER);

        return $this->result;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return (count($this->execute()) > 0) && $this->validateResultKeys();
    }

    /**
     * @return bool
     */
    public function validateResultKeys()
    {
        return array_key_exists('class', $this->getResult())
            && array_key_exists('sep', $this->getResult())
            && array_key_exists('accessor', $this->getResult()
        );
    }

    /**
     * @param CoverFishPHPUnitFile $phpUnitFile
     *
     * @return CoverFishMapping
     */
    public function getMapping(CoverFishPHPUnitFile $phpUnitFile)
    {
        $accessor = $this->getResult()['accessor'];
        $class = $this->getResult()['class'];
        $classFQN = $this->coverFishHelper->getClassFromUse($class, $phpUnitFile->getUsedClasses());

        $mappingOptions = array(
            'coverToken' => $this->coversToken,
            'coverMethod' => null,
            'coverAccessor' => $accessor,
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
        return 'Specifies that the annotated test method covers the specified method.';
    }

    /**
     * @return string
     */
    public function getValidationTag()
    {
        return 'ClassName::<public|protected|private|!public|!protected|!private>';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}