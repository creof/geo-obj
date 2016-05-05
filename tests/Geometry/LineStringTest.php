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
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Geometry\LineString;
use CrEOF\Geo\Obj\Validator\GeographyValidator;
use CrEOF\Geo\Obj\Validator\DValidator;

/**
 * Class LineStringTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Geometry\LineString
 * @covers \CrEOF\Geo\Obj\Validator\Data\LineStringValidator
 * @covers \CrEOF\Geo\Obj\Validator\Data\Traits\ValidatePointTrait
 * @covers \CrEOF\Geo\Obj\Validator\AbstractValidator::getExpectedDimension
 */
class LineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testCountPoint()
    {
        $lineString = new LineString([[0,0,0,0], [1,1,1,1]]);

        static::assertCount(2, $lineString);
    }

    public function testIterator()
    {
        $points = [
            [2,2],
            [0,0],
            [1,1],
            [9,3],
            [10, -20]
        ];

        $lineString = new LineString($points);
        $index      = 0;

        foreach ($lineString as $point) {
            self::assertSame($points[$index++], $point);
        }

        self::assertSame(count($points), $index);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodLineStringTestData
     */
    public function testGoodLineString($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $lineString = new LineString($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $lineString->$function());
        }
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider badLineStringTestData
     */
    public function testBadLineString($value, $validators, $expected)
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

        new LineString($value);
    }

    /**
     * @return array[]
     */
    public function goodLineStringTestData()
    {
        return [
            'testGoodArrayLineString' => [
                'value'      => [[0,0],[1,1]],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[0,0],[1,1]],
                    'dimension'   => null
                ]
            ],
            'testGoodArrayLineStringZM' => [
                'value'      => [[0,0,0,0],[1,1,1,1]],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[0,0,0,0],[1,1,1,1]],
                    'dimension'   => 'ZM'
                ]
            ],
            'testGoodArrayLineStringM' => [
                'value'      => [
                    'value' => [[0,0,0],[1,1,1]],
                    'type'  => 'linestringm'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[0,0,0],[1,1,1]],
                    'dimension'   => 'M'
                ]
            ],
            'testGoodWKBLineString' => [
                'value'      => pack('H*', '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0'),
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[34.23, -87.0], [45.3, -92.0]]
                ]
            ],
            'testGoodWKTLineString' => [
                'value'      => 'LINESTRING(34.23 -87, 45.3 -92)',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[34.23, -87],[45.3, -92]]
                ]
            ],
            'testGoodGeometryLineString' => [
                'value'      => [[37.235142, -115.800834],[37.236620, -115.801573],[37.239059, -115.802904]],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [
                    'coordinates' => [[37.235142, -115.800834],[37.236620, -115.801573],[37.239059, -115.802904]]
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badLineStringTestData()
    {
        return [
            'testBadLineStringWKTType' => [
                'value'      => 'POLYGON((0 0),(1 1))',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "Polygon" for LineString'
                ]
            ],
            'testBadLineStringWKBType' => [
                'value'      => pack('H*', '010300000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000'),
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "Polygon" for LineString'
                ]
            ],
            'testBadShortPointInLineString' => [
                'value'      => [[0,0],[1,1],[0]],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Bad point value in LineString. Point value count must be between 2 and 4.'
                ]
            ],
            'testBadPointInGeometryLineString' => [
                'value'      => [[37.235142, -115.800834],[37.236620, -115.801573],[137.239059, -115.802904]],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Bad point value in LineString. Invalid latitude value "137.239059", must be in range -90 to 90.'
                ]
            ]
        ];
    }
}
