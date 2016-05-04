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

use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Validator\ValidatorStack;

/**
 * Class ValidatorStackTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ValidatorStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Invalid validator of type "string". Validators must implement ValidatorInterface.
     */
    public function testPushBadValidatorType()
    {
        $validatorStack = new ValidatorStack();

        $validatorStack->push('validator');
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Invalid validator class "CrEOF\Geo\Obj\Validator\ValidatorStack". Validators must implement ValidatorInterface.
     */
    public function testPushBadValidatorClass()
    {
        $validatorStack = new ValidatorStack();

        $validatorStack->push(new ValidatorStack());
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Invalid validator of type "string". Validators must implement ValidatorInterface.
     */
    public function testAddBadValidatorType()
    {
        $validatorStack = new ValidatorStack();

        $validatorStack->add(0, 'validator');
    }

    /**
     * @expectedException        UnexpectedValueException
     * @expectedExceptionMessage Invalid validator class "CrEOF\Geo\Obj\Validator\ValidatorStack". Validators must implement ValidatorInterface.
     */
    public function testAddBadValidatorClass()
    {
        $validatorStack = new ValidatorStack();

        $validatorStack->add(0, new ValidatorStack());
    }
}
