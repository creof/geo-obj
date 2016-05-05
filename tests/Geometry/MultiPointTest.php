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
use CrEOF\Geo\Obj\Geometry\MultiPoint;
use CrEOF\Geo\Obj\Object;

/**
 * Class MultiPointTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Geometry\MultiPoint
 * @covers \CrEOF\Geo\Obj\Validator\Data\MultiPointValidator
 * @covers \CrEOF\Geo\Obj\Validator\Data\Traits\ValidatePointTrait
 */
class MultiPointTest extends \PHPUnit_Framework_TestCase
{
    public function testCountPoints()
    {
        $polygon = new MultiPoint([[0,0],[10,0],[10,10],[0,10]]);

        static::assertCount(4, $polygon);
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodMultiPointTestData
     */
    public function testGoodMultiPoint($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $multiPoint = new MultiPoint($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $multiPoint->$function());
        }
    }

    /**
     * @return array[]
     */
    public function goodMultiPointTestData()
    {
        return [
            'testGoodArrayMultiPoint' => [
                'value'      => [[0,0],[10,0],[10,10],[0,10]],
                'validators' => null,
                'expected'   => [
                    'coordinates' => [[0,0],[10,0],[10,10],[0,10]]
                ]
            ],
        ];
    }
}
