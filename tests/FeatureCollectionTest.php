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
use CrEOF\Geo\Obj\FeatureCollection;
use CrEOF\Geo\Obj\Object;

/**
 * Class FeatureCollectionTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\FeatureCollection
 * @covers \CrEOF\Geo\Obj\Validator\Data\FeatureCollectionValidator
 */
class FeatureCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider goodFeatureCollectionTestData
     */
    public function testGoodFeatureCollection($value, $validators, $expected)
    {
        if (null !== $validators) {
            foreach ($validators as $validator) {
                Configuration::getInstance()->pushValidator(Object::T_POINT, $validator);
            }
        }

        $featureCollection = new FeatureCollection($value);

        foreach ($expected as $property => $expectedValue) {
            $function = 'get' . ucfirst($property);

            self::assertSame($expectedValue, $featureCollection->$function());
        }
    }

    /**
     * @param $value
     * @param $validators
     * @param $expected
     *
     * @dataProvider badFeatureCollectionTestData
     */
    public function testBadFeatureCollection($value, $validators, $expected)
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

        new FeatureCollection($value);
    }

    /**
     * @return array[]
     */
    public function goodFeatureCollectionTestData()
    {
        return [
            'testFeatureCollectionPoints' => [
                'value'      => '{"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Point","coordinates":[0,0], "properties":{"name":"null spot"}}},{"type":"Feature","geometry":{"type":"Point","coordinates":[1,1], "properties":{"name":"some spot"}}}]}',
                'validators' => null,
                'expected'   => [
                    'features' => [
                        [
                            'type'  => 'Feature',
                            'geometry' => [
                                'type'        => 'Point',
                                'coordinates' => [0, 0],
                                'properties'  => [
                                    'name' => 'null spot'
                                ]
                            ]
                        ],
                        [
                            'type'  => 'Feature',
                            'geometry' => [
                                'type'        => 'Point',
                                'coordinates' => [1, 1],
                                'properties'  => [
                                    'name' => 'some spot'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badFeatureCollectionTestData()
    {
        return [
            'testBadFeatureCollectionArray' => [
                'value'      => '{"type":"FeatureCollection","features":["feature1"]}',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => 'Feature value must be array of "array", "string" found'
                ]
            ],
            'testBadFeatureCollectionArrayFeature' => [
                'value'      => '{"type":"FeatureCollection","features":[{"type":"waypoint"}]}',
                'validators' => null,
                'expected'   => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\RangeException',
                    'message'   => 'Bad feature value in FeatureCollection. Unknown type "waypoint"'
                ]
            ]
        ];
    }
}
