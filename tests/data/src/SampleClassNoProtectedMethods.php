<?php

namespace DF\PHPCoverFish\Tests\Data\Src;

/**
 * Class SampleClassNoProtectedMethods
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class SampleClassNoProtectedMethods
{
    /**
     * @var int
     */
    protected $propertyIntAlpha;

    /**
     * @var string
     */
    protected $propertyStringBeta;

    /**
     * @return bool
     */
    public function dummy()
    {
        return true;
    }
}