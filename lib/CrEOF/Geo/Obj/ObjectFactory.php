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

namespace CrEOF\Geo\Obj;

use CrEOF\Geo\Obj\Value\ValueFactory;

/**
 * Class ObjectFactory
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ObjectFactory implements ObjectFactoryInterface
{
    /**
     * @var ValueFactory
     */
    private static $valueFactory;

    public function __construct()
    {
        self::$valueFactory = new ValueFactory();
    }

    /**
     * Take a standard format value and create object
     *
     * @param mixed       $value
     * @param null|string $formatHint
     *
     * @return AbstractObject
     */
    public function create($value, $formatHint = null)
    {
        $val = self::$valueFactory->generate($value, $formatHint);

        $objectClass = 'CrEOF\Geo\Obj\\' . $val['type'];

        return new $objectClass($val['value']);
    }

    /**
     * Convert object to standard format
     *
     * @param        $value
     * @param string $format
     *
     * @return mixed
     */
    public function convert($value, $format)
    {
        return self::$valueFactory->convert($value, $format);
    }
}
