<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\Common\ArrayCollection;

/**
 * Class ArrayCollectionTest, Tests for {@see \DF\PHPCoverFish\Common\ArrayCollection}
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.7
 * @version   0.9.7
 *
 * @covers \DF\PHPCoverFish\Common\ArrayCollection
 */
class ArrayCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::toArray
     */
    public function testCheckToArray($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame($elements, $collection->toArray());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::first
     */
    public function testCheckFirst($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(reset($elements), $collection->first());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::last
     */
    public function testCheckLast($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(end($elements), $collection->last());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::key
     */
    public function testCheckKey($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(key($elements), $collection->key());

        next($elements);
        $collection->next();

        $this->assertSame(key($elements), $collection->key());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::next
     */
    public function testCheckNext($elements)
    {
        $collection = new ArrayCollection($elements);

        while (true) {
            $collectionNext = $collection->next();
            $arrayNext = next($elements);

            if(!$collectionNext || !$arrayNext) {
                break;
            }

            $this->assertSame($arrayNext,         $collectionNext,        "Returned value of ArrayCollection::next() and next() not match");
            $this->assertSame(key($elements),     $collection->key(),     "Keys not match");
            $this->assertSame(current($elements), $collection->current(), "Current values not match");
        }
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::current
     */
    public function testCheckCurrent($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(current($elements), $collection->current());

        next($elements);
        $collection->next();

        $this->assertSame(current($elements), $collection->current());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::getKeys
     */
    public function testCheckGetKeys($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_keys($elements), $collection->getKeys());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::getValues
     */
    public function testCheckGetValues($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_values($elements), $collection->getValues());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::count
     */
    public function testCheckCount($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(count($elements), $collection->count());
    }

    /**
     * @dataProvider provideDifferentElements
     *
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::getIterator
     */
    public function testCheckIterator($elements)
    {
        $collection = new ArrayCollection($elements);

        $iterations = 0;
        foreach($collection->getIterator() as $key => $item) {
            $this->assertSame($elements[$key], $item, "Item {$key} not match");
            $iterations++;
        }

        $this->assertEquals(count($elements), $iterations, "Number of iterations not match");
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::remove
     */
    public function testCheckRemove()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3);
        $collection = new ArrayCollection($elements);

        $this->assertEquals(1, $collection->remove(0));
        unset($elements[0]);

        $this->assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);

        $this->assertEquals(2, $collection->remove(1));
        unset($elements[1]);

        $this->assertEquals('a', $collection->remove('A'));
        unset($elements['A']);

        $this->assertEquals($elements, $collection->toArray());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::removeElement
     */
    public function testCheckRemoveElement()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->removeElement(1));
        unset($elements[0]);

        $this->assertFalse($collection->removeElement('non-existent'));

        $this->assertTrue($collection->removeElement('a'));
        unset($elements['A']);

        $this->assertTrue($collection->removeElement('a'));
        unset($elements['A2']);

        $this->assertEquals($elements, $collection->toArray());
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::containsKey
     */
    public function testCheckContainsKey()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->containsKey(0),               "Contains index 0");
        $this->assertTrue($collection->containsKey('A'),             "Contains key \"A\"");
        $this->assertTrue($collection->containsKey('null'),          "Contains key \"null\", with value null");
        $this->assertFalse($collection->containsKey('non-existent'), "Doesn't contain key");
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::isEmpty
     */
    public function testCheckEmpty()
    {
        $collection = new ArrayCollection();
        $this->assertTrue($collection->isEmpty(), "Empty collection");

        $collection->add(1);
        $this->assertFalse($collection->isEmpty(), "Not empty collection");
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::contains
     */
    public function testCheckContains()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->contains(0),               "Contains Zero");
        $this->assertTrue($collection->contains('a'),             "Contains \"a\"");
        $this->assertTrue($collection->contains(null),            "Contains Null");
        $this->assertFalse($collection->contains('non-existent'), "Doesn't contain an element");
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::exists
     */
    public function testCheckExists()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->exists(function($key, $element) {
            return $key == 'A' && $element == 'a';
        }), "Element exists");

        $this->assertFalse($collection->exists(function($key, $element) {
            return $key == 'non-existent' && $element == 'non-existent';
        }), "Element not exists");
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::indexOf
     */
    public function testCheckIndexOf()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_search(2,              $elements, true), $collection->indexOf(2),              'Index of 2');
        $this->assertSame(array_search(null,           $elements, true), $collection->indexOf(null),           'Index of null');
        $this->assertSame(array_search('non-existent', $elements, true), $collection->indexOf('non-existent'), 'Index of non existent');
    }

    /**
     * @covers \DF\PHPCoverFish\Common\ArrayCollection::get
     */
    public function testCheckGet()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(2,    $collection->get(1),              'Get element by index');
        $this->assertSame('a',  $collection->get('A'),            'Get element by name');
        $this->assertSame(null, $collection->get('non-existent'), 'Get non existent element');
    }

    /**
     * @return array
     */
    public function provideDifferentElements()
    {
        return array(
            'indexed'     => array(array(1, 2, 3, 4, 5)),
            'associative' => array(array('A' => 'a', 'B' => 'b', 'C' => 'c')),
            'mixed'       => array(array('A' => 'a', 1, 'B' => 'b', 2, 3)),
        );
    }
}
