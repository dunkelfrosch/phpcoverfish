<?php

namespace DF\PHPCoverFish\Tests\Data\Tests;

use DF\PHPCoverFish\Tests\Data\Src\SampleClassOnlyPrivateMethods;

/**
 * Class ValidatorClassNameAccessorPrivatePassTest
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class ValidatorClassNameAccessorPrivatePassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * check validator that specifies that the annotated test method covers all public methods
     * of a given existing class.
     *
     * @covers SampleClassOnlyPrivateMethods::<private>
     */
    public function testCanCallDummyMethod()
    {
        $sampleClass = new SampleClassOnlyPrivateMethods();
        $this->assertTrue($sampleClass instanceof SampleClassOnlyPrivateMethods);
    }
}