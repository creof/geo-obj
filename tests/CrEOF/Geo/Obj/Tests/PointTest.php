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
use CrEOF\Geo\Obj\Point;
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
    public function testPoint1()
    {
        $point = new Point([0,0]);

        static::assertEquals([0,0], $point->getValue());
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

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid longitude value "300", must be in range -180 to 180.
     */
    public function testPointGeographyValidatorLongitudeFirstBadLongitude()
    {
        $validator = new GeographyValidator();

        $validator->setOrder(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([300, 20]);
    }

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid latitude value "300", must be in range -90 to 90.
     */
    public function testPointGeographyValidatorLongitudeFirstBadLatitude()
    {
        $validator = new GeographyValidator();

        $validator->setOrder(GeographyValidator::CRITERIA_LONGITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([20, 300]);
    }

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid latitude value "300", must be in range -90 to 90.
     */
    public function testPointGeographyValidatorLatitudeFirstBadLatitude()
    {
        $validator = new GeographyValidator();

        $validator->setOrder(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([300, 20]);
    }

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid longitude value "300", must be in range -180 to 180.
     */
    public function testPointGeographyValidatorLatitudeFirstBadLongitude()
    {
        $validator = new GeographyValidator();

        $validator->setOrder(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([20, 300]);
    }

    /**
     * @backupStaticAttributes
     */
    public function testGoodPointValidatorStacking()
    {
        $validator = new GeographyValidator();

        $validator->setOrder(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        $validator = new DValidator($validator);

        $validator->setSize(2);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        $point = new Point([20, 300]);

        static::assertEquals([20, 300], $point->getValue());
    }

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid size "3", size must be 2.
     */
    public function testBadLongPointSizeInnerValidator()
    {
        $validator = new DValidator();

        $validator->setSize(2);

        $validator = new GeographyValidator($validator);

        $validator->setOrder(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([20, 10, 30]);
    }

    /**
     * @backupStaticAttributes
     *
     * @expectedException        RangeException
     * @expectedExceptionMessage Invalid size "3", size must be 4.
     */
    public function testBadShortPointSizeInnerValidator()
    {
        $validator = new DValidator();

        $validator->setSize(4);

        $validator = new GeographyValidator($validator);

        $validator->setOrder(GeographyValidator::CRITERIA_LATITUDE_FIRST);

        Configuration::getInstance()->setValidator('CrEOF\Geo\Obj\Point', $validator);

        new Point([20, 10, 30]);
    }
}
