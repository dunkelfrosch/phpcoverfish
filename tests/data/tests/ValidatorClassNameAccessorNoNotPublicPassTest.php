<?php

namespace DF\PHPCoverFish\Tests\Data\Tests;

use DF\PHPCoverFish\Tests\Data\Src\SampleClassOnlyProtectedMethods;

/**
 * Class ValidatorClassNameAccessorNoNotPublicFailTest
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class ValidatorClassNameAccessorNoNotPublicPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SampleClassOnlyProtectedMethods::<!public>
     */
    public function testCanCallDummyMethod()
    {
        $sampleClass = new SampleClassOnlyProtectedMethods();
        $this->assertTrue($sampleClass instanceof SampleClassOnlyProtectedMethods);
    }
}