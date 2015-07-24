<?php

namespace DF\PHPCoverFish\Tests\Data\Tests;

use DF\PHPCoverFish\Tests\Data\Src\SampleClass;

/**
 * Class ValidatorGlobalMethodFailTest
 *
 * @package   DF\PHP\CoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/dfphpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.0
 *
 * @coversDefaultClass \DF\PHPCoverFish\Tests\Data\Src\SampleClass
 */
class ValidatorGlobalMethodPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::add
     */
    public function testCanCallAddMethod()
    {
        $sampleClass = new SampleClass();
        $this->assertEquals(2, $sampleClass->add(1, 1));
    }
}