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
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Feature;
use CrEOF\Geo\Obj\Object;

/**
 * Class FeatureTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class FeatureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider featureTestData
     */
    public function testFeature($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        if ($expected instanceof ExceptionInterface) {
            $this->setExpectedException(get_class($expected), $expected->getMessage());
        }

        $feature = new Feature($value);

        if (! array_key_exists('value', $expected)) {
            self::assertEquals($expected, $feature->getValue());
        } else {
            foreach ($expected as $property => $expectedValue) {
                $function = 'get' . ucfirst($property);

                self::assertEquals($expectedValue, $feature->$function());
            }
        }
    }

    /**
     * @return array[]
     */
    public function featureTestData()
    {
        return [
            'testFeaturePoint' => [
                'value'      => '{"type":"Feature","geometry":{"type":"Point","coordinates":[0,0]},"properties":{"name":"null spot"}}',
                'validators' => null,
                'expected'   => [
                    'value' => [
                        'type'  => 'point',
                        'value' => [0,0]
                    ],
                    'name'  => 'null spot'
                ]
            ],
        ];
    }
}
