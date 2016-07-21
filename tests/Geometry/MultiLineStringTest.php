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

namespace CrEOF\Geo\Obj\Tests\Geometry;

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Geometry\MultiLineString;
use CrEOF\Geo\Obj\Object;

/**
 * Class MultiLineStringTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Geometry\MultiLineString
 * @covers \CrEOF\Geo\Obj\Validator\Data\MultiLineStringValidator
 * @covers \CrEOF\Geo\Obj\Validator\Data\Traits\ValidatePointTrait
 * @covers \CrEOF\Geo\Obj\Validator\AbstractValidator::getExpectedDimension
 */
class MultiLineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testCountRings()
    {
        $multiLineString = new MultiLineString([[[0,0],[10,0],[10,10],[0,10],[0,0]],[[2,3],[4,5],[6,7]]]);

        static::assertCount(2, $multiLineString);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodMultiLineStringTestData
     */
    public function testGoodMultiLineString($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $multiLineString = new MultiLineString($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $multiLineString->$function());
        }
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider badMultiLineStringTestData
     */
    public function testBadMultiLineString($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException($expected['exception']);
            $this->expectExceptionMessage($expected['message']);
        } else {
            $this->setExpectedException($expected['exception'], $expected['message']);
        }

        new MultiLineString($value);
    }

    /**
     * @return array[]
     */
    public function goodMultiLineStringTestData()
    {
        return [
            'testGoodArrayMultiLineString' => [
                'value'      => [[[0,0],[10,0],[10,10],[0,10],[0,0]],[[2,3],[4,5],[6,7]]],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[[0,0],[10,0],[10,10],[0,10],[0,0]],[[2,3],[4,5],[6,7]]]
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badMultiLineStringTestData()
    {
        return [
            'testBadMultiLineStringWKTType' => [
                'value'      => 'LINESTRING(0 0,1 1)',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "LineString" for MultiLineString'
                ]
            ],
            'testBadMultiLineStringBadLineString' => [
                'value'      => [0, 0],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'MultiLineString value must be array of "array", "integer" found'
                ]
            ],
            'testBadArrayMultiLineString' => [
                'value'      => [[0,0],[10,0],[10,10],[0,10],[0,0]],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Bad linestring value in MultiLineString. LineString value must be array of "array", "integer" found'
                ]
            ]
        ];
    }
}
