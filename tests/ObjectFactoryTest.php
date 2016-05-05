<?php
/**
 * Copyright (C) 2016 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Geo\Obj\Tests;

use CrEOF\Geo\Obj\Data\DataFactory;
use CrEOF\Geo\Obj\ObjectFactory;
use CrEOF\Geo\Obj\Geometry\Point;

/**
 * Class ObjectFactoryTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::__construct
     */
    public function testConstructor()
    {
        self::assertAttributeEmpty('instance', 'CrEOF\Geo\Obj\ObjectFactory');

        $objectFactory = ObjectFactory::getInstance();

        self::assertAttributeInstanceOf('CrEOF\Geo\Obj\ObjectFactory', 'instance', 'CrEOF\Geo\Obj\ObjectFactory');
        self::assertAttributeEquals($objectFactory, 'instance', 'CrEOF\Geo\Obj\ObjectFactory');
        self::assertAttributeEquals(DataFactory::getInstance(), 'dataFactory', $objectFactory);
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::create
     */
    public function testCreateWKTHintWKT()
    {
        $expected = new Point([34.23, -87.0]);

        $actual = ObjectFactory::getInstance()->create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'), 'wkb');

        self::assertEquals($expected, $actual);
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::create
     */
    public function testCreateWKTNoHint()
    {
        $expected = new Point([34.23, -87.0]);

        $actual = ObjectFactory::getInstance()->create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'));

        self::assertEquals($expected, $actual);
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::getTypeClass
     */
    public function testGetTypeClassFullClass()
    {
        $actual = ObjectFactory::getTypeClass('CrEOF\Geo\Obj\Geometry\Point');

        self::assertEquals($actual, 'CrEOF\Geo\Obj\Geometry\Point');
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::getTypeClass
     */
    public function testGetTypeClassTypeName()
    {
        $actual = ObjectFactory::getTypeClass('point');

        self::assertEquals($actual, 'CrEOF\Geo\Obj\Geometry\Point');
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::getTypeClass
     */
    public function testGetTypeClassCache()
    {
        $actual = ObjectFactory::getTypeClass('point');

        self::assertEquals($actual, 'CrEOF\Geo\Obj\Geometry\Point');

        $actual = ObjectFactory::getTypeClass('point');

        self::assertEquals($actual, 'CrEOF\Geo\Obj\Geometry\Point');
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\ObjectFactory::getTypeClass
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnknownTypeException
     * @expectedExceptionMessage Unknown type "bad"
     */
    public function testGetTypeBadType()
    {
        ObjectFactory::getTypeClass('bad');
    }

    /**
     * @covers \CrEOF\Geo\Obj\ObjectFactory::format
     */
    public function testObjectFormat()
    {
        $object = new Point([34.23, -87.0]);

        $actual = ObjectFactory::getInstance()->format($object, 'wkt');

        self::assertEquals('POINT(34.23 -87)', $actual);
    }
}
