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

namespace CrEOF\Geo\Obj\Tests\Value;

use CrEOF\Geo\Obj\Value\Converter\Wkt;
use CrEOF\Geo\Obj\Value\ValueFactory;

/**
 * Class WktTest
 *
 * TODO: need tests for bad values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class WktTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider wktTestData
     */
    public function testWkbConvertor($value, $expected)
    {
        $converter = new Wkt();
        $actual    = $converter->convert($value);

        self::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider wktTestData
     */
    public function testWkbValueFactoryConvert($value, $expected)
    {
        $actual = ValueFactory::getInstance()->convert($value, 'wkt');

        self::assertEquals($expected, $actual);
    }

    public function wktTestData()
    {
        return [
            'testGoodPoint' => [
                'value' => [
                    'type'  => 'point',
                    'value' => [0, 0],
                    'srid'  => null
                ],
                'expected'   => 'POINT(0 0)'
            ],
            'testGoodLineString' => [
                'value' => [
                    'type'  => 'linestring',
                    'value' => [[34.23, -87], [45.3, -92]],
                    'srid'  => null
                ],
                'expected' => 'LINESTRING(34.23 -87,45.3 -92)'
            ] ,
            'testGoodPolygon' => [
                'value' => [
                    'type' => 'polygon',
                    'value' => [
                        [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]],
                        [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]]
                    ],
                    'srid' => null
                ],
                'expected' => 'POLYGON((0 0,10 0,10 10,0 10,0 0),(0 0,10 0,10 10,0 10,0 0))'
            ],
            'testGoodMultiLineString' => [
                'value' => [
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING',
                    'value' => [
                        [[0, 0], [10, 0], [10, 10], [0, 10]],
                        [[5, 5], [7, 5], [7, 7], [5, 7]]
                    ]
                ],
                'expected' => 'MULTILINESTRING((0 0,10 0,10 10,0 10),(5 5,7 5,7 7,5 7))'
            ],
            'testGoodMultiPolygon' => [
                'value' => [
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON',
                    'value' => [
                        [
                            [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]],
                            [[5, 5], [7, 5], [7, 7], [5, 7], [5, 5]]
                        ],
                        [
                            [[1, 1], [3, 1], [3, 3], [1, 3], [1, 1]]
                        ]
                    ]
                ],
                'expected' => 'MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),((1 1,3 1,3 3,1 3,1 1)))'
            ]
        ];
    }
}
