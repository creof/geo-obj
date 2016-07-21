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

use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Geometry\Point;
use CrEOF\Geo\Obj\Geometry\LineString;
use CrEOF\Geo\Obj\Geometry\Polygon;

/**
 * Class ObjectTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \CrEOF\Geo\Obj\Object::create
     */
    public function testCreateWKTHintWKT()
    {
        $expected = new Point([34.23, -87.0]);

        $actual = Object::create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'), 'wkb');

        self::assertEquals($expected, $actual);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Object::create
     */
    public function testCreateWKTNoHint()
    {
        $expected = new Point([34.23, -87.0]);

        $actual = Object::create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'));

        self::assertEquals($expected, $actual);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Object::getProperTypeName
     */
    public function testGetProperTypeName()
    {
        $actual = Object::getProperTypeName('POINT');

        self::assertEquals('Point', $actual);

        $actual = Object::getProperTypeName('POINT');

        self::assertEquals('Point', $actual);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Object::getProperTypeName
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnknownTypeException
     * @expectedExceptionMessage Unknown type "bad"
     */
    public function testGetProperTypeNameBadType()
    {
        Object::getProperTypeName('bad');
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Object::getProperty
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage
     */
    public function testGetBadProperty()
    {
        $point = new Point([0, 0]);

        $point->getProperty('bad');
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Object::__call
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage
     */
    public function testBadMagicCall()
    {
        $point = new Point([0, 0]);

        $point->isStargate();
    }
}
