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
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Geometry\Point;
use CrEOF\Geo\Obj\Object;
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
     * @dataProvider pointTestData
     */
    public function testPoint($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        if ($expected instanceof ExceptionInterface) {
            $this->setExpectedException(get_class($expected), $expected->getMessage());
        }

        $point = new Point($value);

        if (! array_key_exists('coordinates', $expected)) {
            self::assertEquals($expected, $point->getCoordinates());
        } else {
            foreach ($expected as $property => $expectedValue) {
                $function = 'get' . ucfirst($property);

                self::assertEquals($expectedValue, $point->$function());
            }
        }
    }

    public function testPointToWkt()
    {
        $point    = new Point(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'));
        $expected = 'POINT(34.23 -87)';

        self::assertEquals($expected, $point->toWkt());
    }

    /**
     * @return array[]
     */
    public function pointTestData()
    {
        return [
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
                'expected'   => [0,0]
            ],
            'testGoodValueArrayUppercasePoint' => [
                'value'      => [
                    'value' => [0,0],
                    'type'  => 'POINT'
                ],
                'validators' => null,
                'expected'   => [0,0]
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
            'testGoodWkbPoint' => [
                'value'      => pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'),
                'validators' => null,
                'expected'   => [34.23, -87]
            ],
            'testGoodWkbPointZ' => [
                'value'      => pack('H*', '0101000080000000000000F03F00000000000000400000000000000840'),
                'validators' => null,
                'expected'   => [
                    'coordinates' => [1,2,3],
                    'dimension'   => 'Z'
                ]
            ],
            'testGoodStringCoordArrayLongitudeFirst' => [
                'value'      => '79:56:55W 40:26:46N',
                'validators' => null,
                'expected'   => [-79.948611111111, 40.446111111111]
            ],
            'testGoodStringCoordArrayLatitudeFirst' => [
                'value'      => '40:26:46N 79:56:55W',
                'validators' => null,
                'expected'   => [40.446111111111, -79.948611111111]
            ],
            'testGoodPointValidatorStacking' => [
                'value'      => [20, 120],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [20, 120]
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
            ],
            'testBadPointWktType' => [
                'value'      => 'LINESTRING(0 0,1 1)',
                'validators' => null,
                'expected'   => new UnexpectedValueException('Unsupported value of type "LINESTRING" for Point')
            ],
            'testBadPointWkbType' => [
                'value'      => pack('H*', '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0'),
                'validators' => null,
                'expected'   => new UnexpectedValueException('Unsupported value of type "LINESTRING" for Point')
            ],
            'testBadShortPoint' => [
                'value'      => [0],
                'validators' => null,
                'expected'   => new RangeException('Point value count must be between 2 and 4.')
            ],
            'testBadLongPoint' => [
                'value'      => [0,0,0,0,0],
                'validators' => null,
                'expected'   => new RangeException('Point value count must be between 2 and 4.')
            ],
            'testBadLongPointDValidator' => [
                'value'      => [20, 10, 30],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => new RangeException('Invalid size "3", size must be 2.')
            ],
            'testBadShortPointDValidator' => [
                'value'      => [20, 10, 30],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(4)
                ],
                'expected'   => new RangeException('Invalid size "3", size must be 4.')
            ]
        ];
    }
}