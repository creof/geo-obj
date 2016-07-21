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

namespace CrEOF\Geo\Obj\Tests\Validator;

use CrEOF\Geo\Obj\Validator\AbstractValidator;

/**
 * Class AbstractValidatorTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class AbstractValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \CrEOF\Geo\Obj\Validator\AbstractValidator::setExpectedType
     */
    public function testSetExpectedType()
    {
        $validator = new TestValidator();

        self::assertInstanceOf('CrEOF\Geo\Obj\Validator\ValidatorInterface', $validator);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\AbstractValidator::validate
     * @expectedException        \CrEOF\Geo\Obj\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing "type" in data
     */
    public function testMissingType()
    {
        $validator = new TestValidator();

        $data = [];

        $validator->validate($data);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\AbstractValidator::validate
     * @covers                   \CrEOF\Geo\Obj\Validator\AbstractValidator::getExpectedType
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     * @expectedExceptionMessage Unsupported type "wrong" for validator, expected "type"
     */
    public function testWrongType()
    {
        $validator = new TestValidator();

        $data = ['type' => 'wrong'];

        $validator->validate($data);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Validator\AbstractValidator::validate
     * @expectedException        \CrEOF\Geo\Obj\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing "dimension" in data
     */
    public function testMissingDimension()
    {
        $validator = new TestValidator();

        $data = ['type' => 'type'];

        $validator->validate($data);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Validator\AbstractValidator::validate
     */
    public function testValidate()
    {
        $exception = null;

        try {
            $validator = new TestValidator();

            $data = [
                'type'      => 'type',
                'dimension' => null
            ];

            $validator->validate($data);
        } catch (\Exception $e) {
            $exception = $e;
        }

        self::assertNull($exception, 'Unexpected Exception');
    }
}
