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

use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Value\ValueFactory;

/**
 * Abstract geo object
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractObject implements ObjectInterface, \Countable
{
    /**
     * @var array
     */
    protected $value;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var ValueFactory
     */
    protected static $valueFactory;

    public function __construct()
    {
        self::$valueFactory = new ValueFactory();

        $this->properties = array();
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // toWkt, toWkb, toGeoJson, etc.
        if (0 === strpos($name, 'to') && 1 === count($arguments)) {
            return self::$valueFactory->generate($arguments[0], substr($name, 2));
        }

        if (0 === strpos($name, 'get') && 0 === count($arguments)) {
            return $this->getProperty(substr($name, 3));
        }

        if (0 === strpos($name, 'set') && 1 === count($arguments)) {
            return $this->setProperty(substr($name, 3), $arguments[0]);
        }

        // TODO use better exception
        throw new RangeException();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->value);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        if (! array_key_exists($name, $this->properties)) {
            // TODO more specific exception
            throw new RangeException();
        }

        return $this->properties[$name];
    }

    /**
     * Return self to allow chaining
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    protected function validate(array $value)
    {
        $validator = Configuration::getConfiguration()->getValidator(get_class($this));

        null !== $validator && $validator->validate($value);
    }

}
