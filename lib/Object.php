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
 * @method string toWKT()
 * @method string toWKB()
 * @method string toGeoJson()
 */
abstract class Object implements \Countable, \Iterator
{
    const T_POINT              = 'Point';
    const T_LINESTRING         = 'LineString';
    const T_POLYGON            = 'Polygon';
    const T_MULTIPOINT         = 'MultiPoint';
    const T_MULTILINESTRING    = 'MultiLineString';
    const T_MULTIPOLYGON       = 'MultiPolygon';
    const T_GEOMETRYCOLLECTION = 'GeometryCollection';
    const T_CIRCULARSTRING     = 'CircularString';
    const T_FEATURE            = 'Feature';
    const T_FEATURECOLLECTION  = 'FeatureCollection';

    const T_TYPE = null;

    /**
     * Object Data Array containing object's value and properties
     *
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    private $position;

    /**
     * DataFactory instance
     *
     * @var DataFactory
     */
    private static $dataFactory;

    /**
     * @var string[]
     */
    private static $typeNameCache;

    /**
     * Object constructor
     *
     * @param             $value
     * @param null|string $formatHint
     *
     * @throws ExceptionInterface
     * @throws UnexpectedValueException
     * @throws UnknownTypeException
     * @throws UnsupportedFormatException
     */
    public function __construct($value = null, $formatHint = null)
    {
        if (null === self::$dataFactory) {
            self::$dataFactory = DataFactory::getInstance();
        }

        if (null !== $value) {
            $this->position = 0;

            $data = $this->generate($value, $formatHint);

            $this->validate($data);

            $this->data = $data;
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->data['value'][$this->position];
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->data['value'][$this->position]);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws RangeException
     * @throws UnexpectedValueException
     * @throws UnsupportedFormatException
     */
    public function __call($name, $arguments)
    {
        // toWkt, toWkb, toGeoJson, etc.
        if (0 === strpos($name, 'to') && 0 === count($arguments)) {
            return $this->format(strtolower(substr($name, 2)));
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
     * @param string $format
     *
     * @return mixed
     * @throws UnsupportedFormatException
     */
    public function format($format)
    {
        return self::$dataFactory->format($this->data, $format);
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
        if (! array_key_exists($name, $this->data['properties'])) { //TODO check for null
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
     * @param string $type
     *
     * @return string
     * @throws UnknownTypeException
     */
    public static function getProperTypeName($type)
    {
        if (isset(self::$typeNameCache[$type])) {
            return self::$typeNameCache[$type];
        }

        try {
            self::$typeNameCache[$type] = constant('self::T_' . strtoupper($type));

            return self::$typeNameCache[$type];
        } catch (\Exception $e) {

        }

        throw new UnknownTypeException('Unknown type "' . $type . '"');
    }

    /**
     * @param mixed       $value
     * @param null|string $formatHint
     *
     * @return Object
     * @throws UnexpectedValueException
     * @throws UnsupportedFormatException
     * @throws UnknownTypeException
     */
    public static function create($value, $formatHint = null)
    {
        $data        = self::$dataFactory->generate($value, $formatHint);
        $objectClass = ObjectFactory::getTypeClass($data['type']);

        /** @var Object $object */
        $object = new $objectClass();

        $object->validate($data);

        $object->data = $data;

        return $object;
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
        return self::$dataFactory->generate($value, $formatHint, static::T_TYPE);
    }
}
