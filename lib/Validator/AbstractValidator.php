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
     * @var null|string
     */
    private $expectedDimension;

    /**
     * @param array &$data
     *
     * @throws ExceptionInterface
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function validate(array &$data)
    {
        if (! array_key_exists('type', $data)) {
            throw new InvalidArgumentException('Missing "type" in data');
        }

        if (0 !== strcasecmp($this->getExpectedType(), $data['type'])) {
            throw new UnexpectedValueException('Unsupported type "' . $data['type'] . '" for validator, expected "' . $this->getExpectedType() . '"');
        }

        if (! array_key_exists('dimension', $data)) {
            throw new InvalidArgumentException('Missing "dimension" in data');
        }

        $this->expectedDimension = $data['dimension'];
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return $this->expectedType;
    }

    /**
     * @param string $expectedType
     */
    protected function setExpectedType($expectedType)
    {
        $this->expectedType = $expectedType;
    }

    /**
     * @return null|string
     */
    protected function getExpectedDimension()
    {
        return $this->expectedDimension;
    }
}
