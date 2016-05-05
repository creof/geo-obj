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
use CrEOF\Geo\Obj\Geometry\Polygon;
use CrEOF\Geo\Obj\Object;

/**
 * Class PolygonTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Geometry\Polygon
 */
class PolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testCountRings()
    {
        $polygon = new Polygon([[[0,0],[10,0],[10,10],[0,10],[0,0]]]);

        static::assertCount(1, $polygon);
    }

    public function testIterator()
    {
        $rings = [
            [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]],
            [[5, 5], [7, 5], [7, 7], [5, 7], [5, 5]]
        ];

        $polygon = new Polygon($rings);
        $index   = 0;

        foreach ($polygon as $ring) {
            self::assertSame($rings[$index++], $ring);
        }

        self::assertSame(count($rings), $index);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodPolygonTestData
     */
    public function testGoodPolygon($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $polygon = new Polygon($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $polygon->$function());
        }
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider badPolygonTestData
     */
    public function testBadPolygon($value, $validators, $expected)
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

        new Polygon($value);
    }

    /**
     * @return array[]
     */
    public function goodPolygonTestData()
    {
        return [
            'testGoodArrayPolygon' => [
                'value'      => [[[0,0],[10,0],[10,10],[0,10],[0,0]]],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[[0,0],[10,0],[10,10],[0,10],[0,0]]]
                ]
            ],
            'testGoodWKTPolygon' => [
                'value'      => 'POLYGON((0 0,10 0,10 10,0 10,0 0))',
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[[0,0],[10,0],[10,10],[0,10],[0,0]]]
                ]
            ],
            'testGoodWKBPolygon' => [
                'value'      => pack('H*', '010300000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000'),
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[[0.0, 0.0], [10.0 ,0.0], [10.0 ,10.0], [0.0, 10.0], [0.0 ,0.0]]]
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badPolygonTestData()
    {
        return [
            'testBadPolygonWKTType' => [
                'value'      => 'LINESTRING(0 0,1 1)',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Unsupported value of type "LineString" for Polygon'
                ]
            ],
            'testBadArrayPolygon' => [
                'value'      => [[0,0],[10,0],[10,10],[0,10],[0,0]],
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Bad ring value in Polygon. Ring value must be array of "array", "integer" found'
                ]
            ]
        ];
    }
}
