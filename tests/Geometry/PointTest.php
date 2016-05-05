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
use CrEOF\Geo\Obj\Geometry\Point;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Validator\GeographyValidator;
use CrEOF\Geo\Obj\Validator\DValidator;

/**
 * Class PointTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Geometry\Point
 * @covers \CrEOF\Geo\Obj\Validator\Data\PointValidator
 * @covers \CrEOF\Geo\Obj\Validator\AbstractValidator::getExpectedDimension
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testCountPoint()
    {
        $point = new Point([0,0]);

        static::assertCount(2, $point);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodPointTestData
     */
    public function testGoodPoint($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $point = new Point($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $point->$function());
        }
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider badPointTestData
     */
    public function testBadPoint($value, $validators, $expected)
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

        new Point($value);
    }

    public function testPointToWKT()
    {
        $point    = new Point(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'));
        $expected = 'POINT(34.23 -87)';

        self::assertSame($expected, $point->toWKT());
    }

    /**
     * @return array[]
     */
    public function goodPointTestData()
    {
        return [
            'testGoodEmptyPoint' => [
                'value'      => null,
                'validators' => null,
                'expected'   => [
                    'coordinates' => null,
                    'dimension'   => null,
                    'srid'        => null
                ]
            ],
            'testGoodArrayPoint' => [
                'value'      => [0,0],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0],
                    'dimension'   => null
                ]
            ],
            'testGoodValueArrayLowercasePoint' => [
                'value'      => [
                    'value' => [0,0],
                    'type'  => 'point'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0],
                    'dimension'   => null
                ]
            ],
            'testGoodValueArrayLowercasePointWithNullDimension' => [
                'value'      => [
                    'value'     => [0,0],
                    'type'      => 'point',
                    'dimension' => null
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0],
                    'dimension'   => null
                ]
            ],
            'testGoodValueArrayUppercasePoint' => [
                'value'      => [
                    'value' => [0,0],
                    'type'  => 'POINT'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0],
                    'dimension'   => null
                ]
            ],
            'testGoodValueArrayPointZ' => [
                'value'      => [
                    'value' => [0,0,0],
                    'type'  => 'POINT'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0,0],
                    'dimension'   => 'Z'
                ]
            ],
            'testGoodValueArrayPointM' => [
                'value'      => [
                    'value' => [0,0,0],
                    'type'  => 'POINTM'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0,0],
                    'dimension'   => 'M'
                ]
            ],
            'testGoodValueArrayPointSpaceM' => [
                'value'      => [
                    'value' => [0,0,0],
                    'type'  => 'POINT M'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0,0],
                    'dimension'   => 'M'
                ]
            ],
            'testGoodValueArrayPointSpaceZM' => [
                'value'      => [
                    'value' => [0,0,0,0],
                    'type'  => 'POINT ZM'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0,0,0],
                    'dimension'   => 'ZM'
                ]
            ],
            'testGoodValueArrayPointWithDimensionM' => [
                'value'      => [
                    'value'     => [0,0,0],
                    'type'      => 'POINT',
                    'dimension' => 'M'
                ],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [0,0,0],
                    'dimension'   => 'M'
                ]
            ],
            'testGoodWKBPoint' => [
                'value'      => pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'),
                'validators' => null,
                'expected'   => [
                    'coordinates' => [34.23, -87.0],
                    'dimension'   => null
                ]
            ],
            'testGoodWKTPoint' => [
                'value'      => 'POINT(2 3)',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [2, 3],
                    'dimension'   => null
                ]
            ],
            'testGoodWKTPointWithSRID' => [
                'value'      => 'SRID=4326;POINT(2 3)',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [2, 3],
                    'dimension'   => null,
                    'srid'        => 4326
                ]
            ],
            'testGoodWKBPointZ' => [
                'value'      => pack('H*', '0101000080000000000000F03F00000000000000400000000000000840'),
                'validators' => null,
                'expected'   => [
                    'coordinates' => [1.0, 2.0, 3.0],
                    'dimension'   => 'Z'
                ]
            ],
            'testGoodStringCoordArrayLongitudeFirst' => [
                'value'      => '79:56:55W 40:26:46N',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [-79.948611111111106, 40.446111111111108],
                    'dimension'   => null
                ]
            ],
            'testGoodStringCoordArrayLatitudeFirst' => [
                'value'      => '40:26:46N 79:56:55W',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [40.446111111111108, -79.948611111111106],
                    'dimension'   => null
                ]
            ],
            'testGoodPointValidatorStacking' => [
                'value'      => [20, 120],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [
                    'coordinates' => [20, 120],
                    'dimension'   => null
                ]
            ],
            'testGoodPointZGeographyValidator' => [
                'value'      => [20, 120, 10],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST)
                ],
                'expected'   => [
                    'coordinates' => [20, 120, 10],
                    'dimension'   => 'Z'
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badPointTestData()
    {
        return [
            'testBadPointWKTType' => [
                'value'      => 'LINESTRING(0 0,1 1)',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "LineString" for Point'
                ]
            ],
            'testBadPointWKBType' => [
                'value'      => pack('H*', '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0'),
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "LineString" for Point'
                ]
            ],
            'testBadShortPoint' => [
                'value'      => [0],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Point value count must be between 2 and 4.'
                ]
            ],
            'testBadPointDimensionMismatch' => [
                'value'      => [
                    'value'     => [0,0],
                    'type'      => 'point',
                    'dimension' => 'Z'
                ],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Dimension mismatch'
                ]
            ],
            'testBadPointBadValue' => [
                'value'      => [[0],[0]],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Point value must be array containing "integer" or "float", "array" found'
                ]
            ],
            'testBadLongPoint' => [
                'value'      => [0,0,0,0,0],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Point value count must be between 2 and 4.'
                ]
            ],
            'testBadLongPointDValidator' => [
                'value'      => [20, 10, 30],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Invalid size "3", size must be 2.'
                ]
            ],
            'testBadShortPointDValidator' => [
                'value'      => [20, 10, 30],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(4)
                ],
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Invalid size "3", size must be 4.'
                ]
            ]
        ];
    }
}
