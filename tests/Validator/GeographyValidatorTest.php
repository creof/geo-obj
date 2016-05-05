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

namespace CrEOF\Geo\Obj\Tests\Validator;

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Geometry\Point;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Validator\GeographyValidator;

/**
 * GeographyValidatorTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeographyValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validate
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLongitude
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Invalid longitude value "300", must be in range -180 to 180.
     */
    public function testPointBadLongitudeLongitudeFirst()
    {
        $validator = new GeographyValidator(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

        Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);

        new Point([300, 20]);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validate
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLongitude
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Invalid longitude value "300", must be in range -180 to 180.
     */
    public function testPointBadLongitudeLatitudeFirst()
    {
        $validator = new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);

        new Point([20, 300]);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validate
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLatitude
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Invalid latitude value "300", must be in range -90 to 90.
     */
    public function testPointBadLatitudeLongitudeFirst()
    {
        $validator = new GeographyValidator(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

        Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);

        new Point([20, 300]);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validate
     * @covers                   \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLatitude
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Invalid latitude value "300", must be in range -90 to 90.
     */
    public function testPointBadLatitudeLatitudeFirst()
    {
        $validator = new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);

        new Point([300, 20]);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Validator\GeographyValidator::validate
     * @covers \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLongitude
     * @covers \CrEOF\Geo\Obj\Validator\GeographyValidator::validateLatitude
     */
    public function testValidateGoodPoint()
    {
        $exception = null;

        try {
            $validator = new GeographyValidator(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

            Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);

            new Point([0, 0]);
        } catch (\Exception $e) {
        }

        self::assertNull($exception, 'Unexpected Exception');
    }

    /**
     * @covers            \CrEOF\Geo\Obj\Validator\GeographyValidator::__construct
     * @expectedException \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     */
    public function testConstructorBadOrder()
    {
        new GeographyValidator(3);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Validator\GeographyValidator::__construct
     */
    public function testConstructor()
    {
        $validator = new GeographyValidator(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

        self::assertInstanceOf('CrEOF\Geo\Obj\Validator\GeographyValidator', $validator);
    }
}
