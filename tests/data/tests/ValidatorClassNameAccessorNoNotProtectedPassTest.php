<?php

namespace DF\PHPCoverFish\Tests\Data\Tests;

use DF\PHPCoverFish\Tests\Data\Src\SampleClassOnlyPublicMethods;

/**
 * Class ValidatorClassNameAccessorNoNotProtectedPassTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.1
 * @version   0.9.1
 */
class ValidatorClassNameAccessorNoNotProtectedPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SampleClassOnlyPublicMethods::<!protected>
     */
    public function testCanCallDummyMethod()
    {
        $sampleClass = new SampleClassOnlyPublicMethods();
        $this->assertTrue($sampleClass instanceof SampleClassOnlyPublicMethods);
    }
}