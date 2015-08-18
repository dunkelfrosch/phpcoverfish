<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\Common\ArrayCollection;
use DF\PHPCoverFish\Common\Collection;

/**
 * Class CollectionTest, Tests for {@see \DF\PHPCoverFish\Common\Collection}
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.7
 * @version   0.9.7
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    protected function setUp()
    {
        $this->collection = new ArrayCollection();
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::add
     */
    public function testCheckIssetAndUnset()
    {
        $this->assertFalse(isset($this->collection[0]));
        $this->collection->add('testing');
        $this->assertTrue(is_string((string) $this->collection));
        $this->assertTrue(isset($this->collection[0]));
        unset($this->collection[0]);
        $this->assertFalse(isset($this->collection[0]));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::remove
     */
    public function testCheckRemovingNonExistentEntryReturnsNull()
    {
        $this->assertEquals(null, $this->collection->remove('testing_does_not_exist'));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::exists
     */
    public function testCheckExists()
    {
        $this->collection->add("one");
        $this->collection->add("two");
        $exists = $this->collection->exists(function($k, $e) { return $e == "one"; });
        $this->assertTrue($exists);
        $exists = $this->collection->exists(function($k, $e) { return $e == "other"; });
        $this->assertFalse($exists);
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::map
     */
    public function testCheckMap()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->map(function($e) { return $e * 2; });
        $this->assertEquals(array(2, 4), $res->toArray());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::filter
     */
    public function testCheckFilter()
    {
        $this->collection->add(1);
        $this->collection->add("foo");
        $this->collection->add(3);
        $res = $this->collection->filter(function($e) { return is_numeric($e); });
        $this->assertEquals(array(0 => 1, 2 => 3), $res->toArray());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::first
     * @covers \DF\PHPCoverFish\Common\Collection::last
     */
    public function testCheckFirstAndLast()
    {
        $this->collection->add('one');
        $this->collection->add('two');

        $this->assertEquals($this->collection->first(), 'one');
        $this->assertEquals($this->collection->last(), 'two');
    }

    public function testCheckArrayAccess()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertEquals($this->collection[0], 'one');
        $this->assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        $this->assertEquals($this->collection->count(), 1);
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::containsKey
     */
    public function testCheckContainsKey()
    {
        $this->collection[5] = 'five';
        $this->assertTrue($this->collection->containsKey(5));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::contains
     */
    public function testCheckContains()
    {
        $this->collection[0] = 'test';
        $this->assertTrue($this->collection->contains('test'));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::indexOf
     */
    public function testCheckSearch()
    {
        $this->collection[0] = 'test';
        $this->assertEquals(0, $this->collection->indexOf('test'));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::get
     */
    public function testCheckGet()
    {
        $this->collection[0] = 'test';
        $this->assertEquals('test', $this->collection->get(0));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::getKeys
     */
    public function testCheckGetKeys()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(array(0, 1), $this->collection->getKeys());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::getValues
     */
    public function testCheckGetValues()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(array('one', 'two'), $this->collection->getValues());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::count
     */
    public function testCheckCount()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->count(), 2);
        $this->assertEquals(count($this->collection), 2);
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::forAll
     */
    public function testCheckForAll()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->forAll(function($k, $e) { return is_string($e); }), true);
        $this->assertEquals($this->collection->forAll(function($k, $e) { return is_array($e); }), false);
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::partition
     */
    public function testCheckPartition()
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition = $this->collection->partition(function($k, $e) { return $e == true; });
        $this->assertEquals($partition[0][0], true);
        $this->assertEquals($partition[1][0], false);
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::isEmpty
     * @covers \DF\PHPCoverFish\Common\Collection::clear
     */
    public function testCheckClear()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        $this->assertEquals($this->collection->isEmpty(), true);
    }

    public function testCheckRemove()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el = $this->collection->remove(0);

        $this->assertEquals('one', $el);
        $this->assertEquals($this->collection->contains('one'), false);
        $this->assertNull($this->collection->remove(0));
    }

    public function testCheckRemoveElement()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertTrue($this->collection->removeElement('two'));
        $this->assertFalse($this->collection->contains('two'));
        $this->assertFalse($this->collection->removeElement('two'));
    }

    /**
     * @covers \DF\PHPCoverFish\Common\Collection::slice
     */
    public function testCheckSlice()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        $this->assertInternalType('array', $slice);
        $this->assertEquals(array('one'), $slice);

        $slice = $this->collection->slice(1);
        $this->assertEquals(array(1 => 'two', 2 => 'three'), $slice);

        $slice = $this->collection->slice(1, 1);
        $this->assertEquals(array(1 => 'two'), $slice);
    }

    public function fillCheckMatchingFixture()
    {
        $std1 = new \stdClass();
        $std1->foo = "bar";
        $this->collection[] = $std1;

        $std2 = new \stdClass();
        $std2->foo = "baz";
        $this->collection[] = $std2;
    }

    public function testCheckCanRemoveNullValuesByKey()
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        $this->assertTrue($this->collection->isEmpty());
    }

    public function testCheckCanVerifyExistingKeysWithNullValues()
    {
        $this->collection->set('key', null);
        $this->assertTrue($this->collection->containsKey('key'));
    }
}
