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

namespace CrEOF\Geo\Obj\Validator;

use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\InvalidArgumentException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;

/**
 * Class AbstractValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    private $expectedType;

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }

    /**
     * @param string $expectedType
     */
    public function setExpectedType($expectedType)
    {
        $this->setExpectedType($expectedType);
    }

    /**
     * @param array $value
     *
     * @throws ExceptionInterface
     */
    public function validate(array $value)
    {
        if (! array_key_exists('type', $value)) {
            throw new InvalidArgumentException('Missing "type" in value');
        }

        if (0 !== strcasecmp($this->expectedType, $value['type'])) {
            throw new UnexpectedValueException('Unsupported type "' . $value['type'] . '" for validator, expected "' . $this->expectedType . '"');
        }
    }
}
