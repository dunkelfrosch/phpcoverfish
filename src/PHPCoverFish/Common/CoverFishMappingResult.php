<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishMapping
 *
 * @package    DF\PHPCoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since      class available since Release 0.9.0
 * @version    0.9.5
 */
class CoverFishMappingResult
{
    /**
     * @var bool
     */
    private $pass = false;

    /**
     * @var ArrayCollection
     */
    private $errors;

    /**
     * @var ArrayCollection
     */
    private $warnings;

    /**
     * @return boolean
     */
    public function isPass()
    {
        return $this->pass;
    }

    /**
     * @param boolean $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return ArrayCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param CoverFishError $error
     */
    public function addError(CoverFishError $error)
    {
        $this->errors->add($error);
    }

    /**
     * @param CoverFishError $error
     */
    public function removeError(CoverFishError $error)
    {
        $this->errors->removeElement($error);
    }

    /**
     * clear all errors
     */
    public function clearErrors()
    {
        $this->errors->clear();
    }

    /**
     * @return ArrayCollection
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * clear all warnings
     */
    public function clearWarnings()
    {
        $this->warnings->clear();
    }

    /**
     * our class constructor
     */
    public function __construct()
    {
        $this->errors = new ArrayCollection();
        $this->warnings = new ArrayCollection();
    }
}