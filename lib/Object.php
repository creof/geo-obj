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

use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Value\ValueFactory;

/**
 * Abstract geo object
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @method string toWkt()
 * @method string toWkb()
 * @method string toGeoJson()
 */
abstract class Object implements ObjectInterface, \Countable
{
    const T_TYPE = 'Object';

    /**
     * Array containing object's value and properties
     *
     * @var array
     */
    protected $data;

    /**
     * ValueFactory instance
     *
     * @var ValueFactory
     */
    protected static $valueFactory;

    /**
     * Object constructor
     *
     * @param       $value
     * @param array $properties
     *
     * @throws UnexpectedValueException
     * @throws ExceptionInterface
     */
    public function __construct($value, array $properties = [])
    {
        if (null === self::$valueFactory) {
            self::$valueFactory = ValueFactory::getInstance();
        }

        $data               = $this->generate($value);
        $data['properties'] = $properties;

        $this->validate($data);

        $this->data = $data;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     * @throws RangeException
     */
    public function __call($name, $arguments)
    {
        // toWkt, toWkb, toGeoJson, etc.
        if (0 === strpos($name, 'to') && 0 === count($arguments)) {
            return self::$valueFactory->convert($this->data, strtolower(substr($name, 2)));
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
     * Get count of coordinates, points, rings, etc.
     *
     * @return int
     */
    public function count()
    {
        return count($this->data['value']);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws RangeException
     */
    public function getProperty($name)
    {
        if (! array_key_exists($name, $this->data['properties'])) {
            // TODO more specific exception
            throw new RangeException();
        }

        return $this->data['properties'][$name];
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
        $this->data['properties'][$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->data['value'];
    }

    /**
     * Validate value array with configured validators for type
     *
     * @param array $value
     *
     * @throws ExceptionInterface
     */
    protected function validate(array $value)
    {
        Configuration::getInstance()->getValidators(static::T_TYPE)->validate($value);
    }

    /**
     * Generate value array from input
     *
     * @param mixed $value
     *
     * @return array
     * @throws UnexpectedValueException
     */
    protected function generate($value)
    {
        if (! is_array($value)) {
            return self::$valueFactory->generate($value);
        }

        return [
            'value' => array_key_exists('value', $value) ? $value['value'] : $value,
            'type'  => array_key_exists('type', $value) ? $value['type'] : static::T_TYPE,
            'srid'  => array_key_exists('srid', $value) ? $value['type'] : null
        ];
    }
}
