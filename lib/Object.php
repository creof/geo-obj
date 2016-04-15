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

use CrEOF\Geo\Obj\Data\DataFactory;
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Exception\UnknownTypeException;
use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;

/**
 * Abstract object
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
    const T_TYPE = null;

    /**
     * Object Data Array containing object's value and properties
     *
     * @var array
     */
    protected $data;

    /**
     * ValueFactory instance
     *
     * @var DataFactory
     */
    private static $valueFactory;

    /**
     * Object constructor
     *
     * @param             $value
     * @param null|string $typeHint
     *
     * @throws ExceptionInterface
     * @throws UnexpectedValueException
     * @throws UnknownTypeException
     * @throws UnsupportedFormatException
     */
    public function __construct($value, $typeHint = null)
    {
        if (null === self::$valueFactory) {
            self::$valueFactory = DataFactory::getInstance();
        }

        $data = $this->generate($value, $typeHint);

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
            return $this->getProperty(strtolower(substr($name, 3)));
        }

        if (0 === strpos($name, 'set') && 1 === count($arguments)) {
            return $this->setProperty(strtolower(substr($name, 3)), $arguments[0]);
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
     * @return string|null
     */
    public function getDimension()
    {
        return $this->data['dimension'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return static::T_TYPE;
    }

    /**
     * Validate value array with configured validators for type
     *
     * @param array $value
     *
     * @throws ExceptionInterface
     * @throws UnknownTypeException
     */
    private function validate(array &$value)
    {
        Configuration::getInstance()->getValidatorStack(static::T_TYPE)->validate($value);
    }

    /**
     * Generate value array from input
     *
     * @param mixed       $value
     * @param null|string $formatHint
     *
     * @return array
     * @throws UnexpectedValueException
     * @throws UnsupportedFormatException
     */
    private function generate($value, $formatHint = null)
    {
        $value = self::$valueFactory->generate($value, $formatHint, static::T_TYPE);

        //TODO is this necessary? yes? wkb and wkt don't included properties currently
        return [
            'type'       => $value['type'],
            'value'      => $value['value'],
            'srid'       => array_key_exists('srid', $value) ? $value['srid'] : null,
            'dimension'  => array_key_exists('dimension', $value) ? $value['dimension'] : null,
            'properties' => array_key_exists('properties', $value) ? $value['properties'] : null
        ];
    }
}
