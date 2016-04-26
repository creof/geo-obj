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

use CrEOF\Geo\Obj\Exception\RuntimeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Exception\UnknownTypeException;
use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\Traits\Singleton;
use CrEOF\Geo\Obj\Data\DataFactory;

/**
 * Class ObjectFactory
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ObjectFactory implements ObjectFactoryInterface
{
    use Singleton;

    //TODO: add remaining types
    const C_OBJECT             = 'CrEOF\Geo\Obj\Object';
    const C_POINT              = 'CrEOF\Geo\Obj\Geometry\Point';
    const C_LINESTRING         = 'CrEOF\Geo\Obj\Geometry\LineString';
    const C_POLYGON            = 'CrEOF\Geo\Obj\Geometry\Polygon';
    const C_MULTIPOINT         = 'CrEOF\Geo\Obj\Geometry\MultiPoint';
    const C_MULTILINESTRING    = 'CrEOF\Geo\Obj\Geometry\MultiLineString';
    const C_MULTIPOLYGON       = 'CrEOF\Geo\Obj\Geometry\MultiPolygon';
    const C_CIRCULARSTRING     = 'CrEOF\Geo\Obj\Geometry\CircularString';
    const C_GEOMETRYCOLLECTION = 'CrEOF\Geo\Obj\GeometryCollection';
    const C_FEATURE            = 'CrEOF\Geo\Obj\Feature';

    /**
     * @var DataFactory
     */
    private $valueFactory;

    /**
     * @var string[]
     */
    private static $typeClassCache;

    /**
     * Singleton ObjectFactory constructor
     */
    private function __construct()
    {
        $this->valueFactory = DataFactory::getInstance();
    }

    /**
     * Take a standard format value and create object
     *
     * @param mixed       $value
     * @param null|string $formatHint
     *
     * @return mixed
     * @throws UnexpectedValueException
     * @throws UnknownTypeException
     * @throws UnsupportedFormatException
     * @throws RuntimeException
     */
    public function create($value, $formatHint = null)
    {
        $data        = $this->valueFactory->generate($value, $formatHint);
        $objectClass = self::getTypeClass($data['type']);

        try {
            return new $objectClass($data['value'], 'simplearray');
        } catch (UnsupportedFormatException $e) {
            throw new RuntimeException('ObjectFactory requires "simplearray" generator'); //TODO better message
        }
    }

    /**
     * Convert object to standard format
     *
     * @param mixed  $object
     * @param string $format
     *
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function convert($object, $format)
    {
        return $object->format($format);
    }

    /**
     * @param string $type
     *
     * @return string
     * @throws UnknownTypeException
     */
    public static function getTypeClass($type)
    {
        if (isset(self::$typeClassCache[$type])) {
            return self::$typeClassCache[$type];
        }

        if (class_exists($type) && is_subclass_of($type, self::C_OBJECT)) {
            self::$typeClassCache[$type] = $type;

            return $type;
        }

        try {
            $typeClass = constant('self::C_' . strtoupper($type));

            if (class_exists($typeClass) && is_subclass_of($typeClass, self::C_OBJECT)) {
                self::$typeClassCache[$type] = $typeClass;

                return $typeClass;
            }
        } catch (\Exception $e) {

        }

        throw new UnknownTypeException('Unknown type "' . $type . '"');
    }
}
