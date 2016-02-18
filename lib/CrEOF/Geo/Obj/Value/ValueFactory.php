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

namespace CrEOF\Geo\Obj\Value;

use CrEOF\Geo\Obj\Value\Adapter;
use CrEOF\Geo\Obj\Value\Generator;
use CrEOF\Geo\Obj\Exception\UnsupportedTypeException;

/**
 * Class ValueFactory
 *
 * The singleton class ValueFactory converts from/to input and output values in various data formats
 * to/from internal type representation using adapter classes.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
final class ValueFactory
{
    /**
     * @var Adapter\ValueAdapterInterface[]
     */
    private static $adapters;

    /**
     * @var Generator\ValueGeneratorInterface[]
     */
    private static $generators;

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct()
    {

    }

    /**
     * @param             $value
     * @param null|string $formatHint
     *
     * @return mixed
     * @throws UnsupportedTypeException
     */
    public static function process($value, $formatHint = null)
    {
        if (null !== self::$adapters) {
            self::addDefaultAdapters();
        }

        if (null !== $formatHint) {
            return self::$adapters[$formatHint]->process($value);
        }

        foreach (self::$adapters as $type => $adapter) {
            try {
                return $adapter->process($value);
            } catch (UnsupportedTypeException $e) {
                // Try next adapter
            }
        }

        throw new UnsupportedTypeException();
    }

    /**
     * @param        $value
     * @param string $type
     *
     * @return mixed
     * @throws UnsupportedTypeException
     */
    public static function generate($value, $type)
    {
        if (null !== self::$generators) {
            self::addDefaultGenerators();
        }

        if (! array_key_exists($type, self::$generators)) {
            throw new UnsupportedTypeException();
        }

        return self::$generators[$type]->generate($value);
    }

    /**
     * @return Adapter\ValueAdapterInterface[]
     */
    public static function getAdapters()
    {
        return self::$adapters;
    }

    /**
     * @param Adapter\ValueAdapterInterface $adapter ValueAdapterInterface instance
     * @param string                $format  Format supported by adapter
     */
    public static function addAdapter(Adapter\ValueAdapterInterface $adapter, $format)
    {
        self::$adapters[$format] = $adapter;
    }

    /**
     * @return Generator\ValueGeneratorInterface[]
     */
    public static function getGenerators()
    {
        return self::$generators;
    }

    /**
     * @param Generator\ValueGeneratorInterface $generator ValueGeneratorInterface
     * @param string                  $format    Format supported by generator
     */
    public static function addGenerator(Generator\ValueGeneratorInterface $generator, $format)
    {
        self::$generators[$format] = $generator;
    }

    private static function addDefaultAdapters()
    {
        self::$adapters = array(
            'wkt'     => new Adapter\Wkt(),
            'wkb'     => new Adapter\Wkb(),
            'geojson' => new Adapter\GeoJson(),
            'kml'     => new Adapter\Kml()
        );
    }

    private static function addDefaultGenerators()
    {
        self::$generators = array(
            'wkt'     => new Generator\Wkt(),
            'wkb'     => new Generator\Wkb(),
            'geojson' => new Generator\GeoJson(),
            'kml'     => new Generator\Kml()
        );
    }
}
