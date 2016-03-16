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

use CrEOF\Geo\Obj\ObjectInterface;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Validator\TypeValidator;

/**
 * Class TypeValidatorTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class TypeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testSetGoodType()
    {
        $exception = null;

        try {
            $validator = new TypeValidator();

            $validator->setType(ObjectInterface::T_POINT);
        } catch (UnexpectedValueException $e) {
        }

        self::assertNull($exception, 'Unexpected UnexpectedValueException');
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Unknown type "Configuration"
     */
    public function testSetBadType()
    {
        $validator = new TypeValidator();

        $validator->setType('Configuration');
    }

    /**
     */
    public function testValidateType()
    {
        $exception = null;

        try {
            $validator = new TypeValidator();

            $validator->setType(ObjectInterface::T_POINT);

            $value = [
                'type'  => 'point',
                'value' => null
            ];

            $validator->validate($value);
        } catch (UnexpectedValueException $e) {
        }

        self::assertNull($exception, 'Unexpected UnexpectedValueException');
    }
}
