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

namespace CrEOF\Geo\Obj\Value\Generator;

use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\ObjectInterface;

/**
 * Class SimpleArray
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class SimpleArray implements ValueGeneratorInterface
{
    /**
     * @param mixed                $value
     * @param null|ObjectInterface $object
     *
     * @return array
     * @throws UnsupportedFormatException
     */
    public function generate($value, ObjectInterface $object = null)
    {
        if (! is_array($value)) {
            throw new UnsupportedFormatException();
        }

        return [
            'value'     => array_key_exists('value', $value) ? $value['value'] : $value,
            'type'      => array_key_exists('type', $value) ? $value['type'] : $object->getType(),
            'srid'      => array_key_exists('srid', $value) ? $value['srid'] : null,
            'dimension' => array_key_exists('dimension', $value) ? $value['dimension'] : null
        ];
    }
}
