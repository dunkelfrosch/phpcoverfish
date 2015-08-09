<?php

namespace DF\PHPCoverFish\Validator\Base;

use DF\PHPCoverFish\Common\CoverFishPHPUnitFile;
use DF\PHPCoverFish\Common\CoverFishMapping;

/**
 * Interface BaseCoverFishValidatorInterface
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.4
 */
interface BaseCoverFishValidatorInterface {

    /**
     * @return bool
     */
    public function validate();

    /**
     * @return string
     */
    public function getValidationInfo();

    /**
     * @return string
     */
    public function getValidationTag();

    /**
     * @return array
     */
    public function getResult();

    /**
     * @param CoverFishPHPUnitFile $phpUnitFile
     *
     * @return CoverFishMapping
     */
    public function getMapping(CoverFishPHPUnitFile $phpUnitFile);

    /**
     * @param array $mappingOptions
     *
     * @return CoverFishMapping
     */
    public function setMapping(array $mappingOptions);
}