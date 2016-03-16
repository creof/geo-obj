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

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Point;
use CrEOF\Geo\Obj\ObjectInterface;
use CrEOF\Geo\Obj\Validator\GeographyValidator;
use CrEOF\Geo\Obj\Validator\DValidator;

/**
 * Class PointTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayPoint()
    {
        $point = new Point([0,0]);

        static::assertEquals([0,0], $point->getValue());
    }

    public function testWkbPoint()
    {
        $wkb   = pack('H*', '01010000003D0AD7A3701D41400000000000C055C0');
        $point = new Point($wkb);

        static::assertEquals([34.23, -87], $point->getValue());
    }

    public function testWktPoint()
    {
        $point = new Point('POINT(34.23 -87)');

        static::assertEquals([34.23, -87], $point->getValue());
    }

    public function testCountPoint()
    {
        $point = new Point([0,0]);

        static::assertCount(2, $point);
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Unsupported value of type "LINESTRING" for CrEOF\Geo\Obj\Point
     */
    public function testBadPointWktType()
    {
        new Point('LINESTRING(0 0,1 1)');
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Unsupported value of type "LINESTRING" for CrEOF\Geo\Obj\Point
     */
    public function testBadPointWkbType()
    {
        $wkb   = pack('H*', '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0');

        new Point($wkb);
    }

    /**
     * @expectedException        RangeException
     * @expectedExceptionMessage Point value count must be between 2 and 4.
     */
    public function testShortPoint()
    {
        new Point([0]);
    }

    /**
     * @expectedException        RangeException
     * @expectedExceptionMessage Point value count must be between 2 and 4.
     */
    public function testLongPoint()
    {
        new Point([0,0,0,0,0]);
    }

    public function testGoodPointValidatorStacking()
    {
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST));
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new DValidator(2));

        $point = new Point([20, 120]);

        static::assertEquals([20, 120], $point->getValue());
    }

    /**
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid size "3", size must be 2.
     */
    public function testBadLongPointSizeInnerValidator()
    {
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST));
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new DValidator(2));

        new Point([20, 10, 30]);
    }

    /**
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid size "3", size must be 4.
     */
    public function testBadShortPointSizeInnerValidator()
    {
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST));
        Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, new DValidator(4));

        new Point([20, 10, 30]);
    }
}
