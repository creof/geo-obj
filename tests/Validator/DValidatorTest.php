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

use CrEOF\Geo\Obj\Validator\DValidator;

/**
 * Class DValidatorTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class DValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\DValidator::__construct
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Size must be between 2 and 4.
     */
    public function testBadLargeSize()
    {
        new DValidator(5);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\DValidator::__construct
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Size must be between 2 and 4.
     */
    public function testBadSmallSize()
    {
        new DValidator(-1);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Validator\DValidator::__construct
     */
    public function testGoodSize()
    {
        $validator2 = new DValidator(2);
        $validator3 = new DValidator(3);
        $validator4 = new DValidator(4);

        self::assertInstanceOf('CrEOF\Geo\Obj\Validator\DValidator', $validator2);
        self::assertInstanceOf('CrEOF\Geo\Obj\Validator\DValidator', $validator3);
        self::assertInstanceOf('CrEOF\Geo\Obj\Validator\DValidator', $validator4);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Validator\DValidator::validate
     */
    public function testValidateGoodData()
    {
        $exception = null;

        try {
            $validator = new DValidator(2);

            $data = [
                'type'      => 'point',
                'value'     => [0, 0],
                'dimension' => null
            ];

            $validator->validate($data);
        } catch (\Exception $e) {
            $exception = $e;
        }

        self::assertNull($exception, 'Unexpected Exception');
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\DValidator::validate
     * @expectedException        \CrEOF\Geo\Obj\Exception\RangeException
     * @expectedExceptionMessage Invalid size "3", size must be 2.
     */
    public function testValidateBadData()
    {
        $validator = new DValidator(2);

        $data = [
            'type'      => 'point',
            'value'     => [0, 0, 0],
            'dimension' => 'Z'
        ];

        $validator->validate($data);
    }
}
