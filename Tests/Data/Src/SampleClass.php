<?php

namespace DF\PHPCoverFish\Tests\Data\Src;

/**
 * Class SampleClass
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 */
class SampleClass
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

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function add($a, $b)
    {
        return $a + $b;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function sub($a, $b)
    {
        return $a - $b;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function multiply($a, $b)
    {
        return $a * $b;
    }
}