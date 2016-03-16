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
use CrEOF\Geo\Obj\LineString;
use CrEOF\Geo\Obj\ObjectInterface;
use CrEOF\Geo\Obj\Validator\GeographyValidator;
use CrEOF\Geo\Obj\Validator\DValidator;

/**
 * Class LineStringTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class LineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testCountPoint()
    {
        $lineString = new LineString([[0,0,0,0], [1,1,1,1]]);

        static::assertCount(2, $lineString);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider lineStringTestData
     */
    public function testLineString($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(ObjectInterface::T_POINT, $validator);
            }
        }

        try {
            $actual = (new LineString($value))->getValue();
        } catch (\Exception $e) {
            $actual = $e;
        }

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function lineStringTestData()
    {
        return [
            'testGoodArrayLineString' => [
                'value'      => [[0,0,0,0],[1,1,1,1]],
                'validators' => null,
                'expected'   => [[0,0,0,0],[1,1,1,1]]
            ],
            'testGoodWkbLineString' => [
                'value'      => pack('H*', '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0'),
                'validators' => null,
                'expected'   => [[34.23, -87], [45.3, -92]]
            ],
            'testGoodWktLineString' => [
                'value'      => 'LINESTRING(34.23 -87, 45.3 -92)',
                'validators' => null,
                'expected'   => [[34.23,-87],[45.3,-92]]
            ],
            'testBadLineStringWktType' => [
                'value'      => 'POLYGON((0 0),(1 1))',
                'validators' => null,
                'expected'   => new UnexpectedValueException('Unsupported value of type "POLYGON" for CrEOF\Geo\Obj\LineString')
            ],
            'testBadLineStringWkbType' => [
                'value'      => pack('H*', '010300000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000'),
                'validators' => null,
                'expected'   => new UnexpectedValueException('Unsupported value of type "POLYGON" for CrEOF\Geo\Obj\LineString')
            ],
            'testBadShortPointInLineString' => [
                'value'      => [[0,0],[1,1],[0]],
                'validators' => null,
                'expected'   => new RangeException( //TODO: This pattern will become unwieldy in higher order types
                    'Bad Point value in LineString. Point value count must be between 2 and 4.',
                    0,
                    new RangeException('Point value count must be between 2 and 4.')
                )
            ],
            'testGoodGeometryLineString' => [
                'value'      => [[37.235142, -115.800834],[37.236620, -115.801573],[37.239059, -115.802904]],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => [[37.235142, -115.800834],[37.236620, -115.801573],[37.239059, -115.802904]]
            ],
            'testBadPointInGeometryLineString' => [
                'value'      => [[37.235142, -115.800834],[37.236620, -115.801573],[137.239059, -115.802904]],
                'validators' => [
                    new GeographyValidator(GeographyValidator::CRITERIA_LATITUDE_FIRST),
                    new DValidator(2)
                ],
                'expected'   => new RangeException(
                    'Bad Point value in LineString. Invalid latitude value "137.239059", must be in range -90 to 90.',
                    0,
                    new RangeException('Invalid latitude value "137.239059", must be in range -90 to 90.')
                )
            ],
        ];
    }
}
