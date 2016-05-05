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
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Object;

/**
 * Class DValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class DValidator extends AbstractValidator
{
    /**
     * @var int
     */
    private $size = 2;

    /**
     * @param int $size
     *
     * @throws RangeException
     */
    public function __construct($size)
    {
        $this->setExpectedType(Object::T_POINT);

        if ($size < 2 || $size > 4) {
            throw new RangeException('Size must be between 2 and 4.');
        }

        $this->size = $size;
    }

    /**
     * @param array &$data
     *
     * @throws ExceptionInterface
     * @throws RangeException
     *
     * @TODO compare with expectedDimension also?
     */
    public function validate(array &$data)
    {
        parent::validate($data);

        $size = count($data['value']);

        if ($this->size !== $size) {
            throw new RangeException('Invalid size "' . $size . '", size must be '. $this->size . '.');
        }
    }
}
