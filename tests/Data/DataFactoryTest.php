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

namespace CrEOF\Geo\Obj\Tests\Data;

use CrEOF\Geo\Obj\Data\DataFactory;

/**
 * Class DataFactoryTest
 *
 * @backupStaticAttributes
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class DataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultWkbGenerator()
    {
        $expected = [
            'type'      => 'POINT',
            'srid'      => null,
            'value'     => array(34.23, -87.0),
            'dimension' => null
        ];

        $actual = DataFactory::getInstance()->generate(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'), 'wkb');

        self::assertSame($expected, $actual);
    }

    /**
     * @param string      $value
     * @param string      $outFormat
     * @param null|string $inFormatHint
     * @param string      $expected
     *
     * @dataProvider goodConvertData
     */
    public function testGoodConvert($value, $outFormat, $inFormatHint, $expected)
    {
        $actual = DataFactory::getInstance()->convert($value, $outFormat, $inFormatHint);

        self::assertSame($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function goodConvertData()
    {
        return [
            'testWktPointToWkb'      => [
                'value'        => 'POINT(0 0)',
                'outFormat'    => 'wkb',
                'inFormatHint' => null,
                'expected'     => pack('H*', '000000000100000000000000000000000000000000')
            ],
            'testWkbPointToWkt'      => [
                'value'        => pack('H*', '000000000100000000000000000000000000000000'),
                'outFormat'    => 'wkt',
                'inFormatHint' => null,
                'expected'     => 'POINT(0 0)'
            ]
        ];
    }
}
