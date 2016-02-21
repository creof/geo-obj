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

use CrEOF\Geo\Obj\Value\Generator;
use CrEOF\Geo\Obj\Value\Converter;
use CrEOF\Geo\Obj\Exception\UnsupportedTypeException;

/**
 * Class ValueFactory
 *
 * The ValueFactory class converts from/to standard values in various formats
 * to/from internally used structure using converter and generator classes.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ValueFactory
{
    private static $valueFactory;

    /**
     * @var Generator\ValueGeneratorInterface[]
     */
    private $generators;

    /**
     * @var Converter\ValueConverterInterface[]
     */
    private $converters;

    private function __construct()
    {
        $this->addDefaultGenerators();
        $this->addDefaultConverters();
    }

    /**
     * @param mixed       $value
     * @param null|string $formatHint
     *
     * @return array
     * @throws UnsupportedTypeException
     */
    public function generate($value, $formatHint = null)
    {
        if (null !== $formatHint) {
            return $this->generators[$formatHint]->generate($value);
        }

        foreach ($this->generators as $type => $generator) {
            try {
                return $generator->generate($value);
            } catch (UnsupportedTypeException $e) {
                // Try next generator
            }
        }

        throw new UnsupportedTypeException();
    }

    /**
     * @param array  $value
     * @param string $type
     *
     * @return mixed
     * @throws UnsupportedTypeException
     */
    public function convert(array $value, $type)
    {
        if (! array_key_exists($type, $this->converters)) {
            throw new UnsupportedTypeException();
        }

        return $this->converters[$type]->convert($value);
    }

    /**
     * @param Generator\ValueGeneratorInterface $generator ValueGeneratorInterface instance
     * @param string                            $format    Format supported by adapter
     */
    public function addGenerator(Generator\ValueGeneratorInterface $generator, $format)
    {
        $this->generators[$format] = $generator;
    }

    /**
     * @param Converter\ValueConverterInterface $converter ValueGeneratorInterface
     * @param string                            $format    Format supported by generator
     */
    public function addConverter(Converter\ValueConverterInterface $converter, $format)
    {
        $this->converters[$format] = $converter;
    }

    /**
     * @return ValueFactory
     */
    public static function getValueFactory()
    {
        if (null === self::$valueFactory) {
            self::$valueFactory = new self();
        }

        return self::$valueFactory;
    }

    private function addDefaultGenerators()
    {
        $this->generators = array(
            'wkt'     => new Generator\Wkt(),
            'wkb'     => new Generator\Wkb(),
            'geojson' => new Generator\GeoJson(),
            'kml'     => new Generator\Kml()
        );
    }

    private function addDefaultConverters()
    {
        $this->converters = array(
            'wkt'     => new Converter\Wkt(),
            'wkb'     => new Converter\Wkb(),
            'geojson' => new Converter\GeoJson(),
            'kml'     => new Converter\Kml()
        );
    }
}
