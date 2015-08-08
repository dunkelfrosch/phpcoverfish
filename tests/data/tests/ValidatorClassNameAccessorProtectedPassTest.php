<?php

namespace DF\PHPCoverFish\Tests\Data\Tests;

use DF\PHPCoverFish\Tests\Data\Src\SampleClassOnlyProtectedMethods;

/**
 * Class ValidatorClassNameAccessorProtectedPassTest.php
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class ValidatorClassNameAccessorProtectedPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * check validator that specifies that the annotated test method covers all public methods
     * of a given existing class.
     *
     * @covers SampleClassOnlyProtectedMethods::<protected>
     */
    public function testCanCallDummyMethod()
    {
        $sampleClass = new SampleClassOnlyProtectedMethods();
        $this->assertTrue($sampleClass instanceof SampleClassOnlyProtectedMethods);
    }
}