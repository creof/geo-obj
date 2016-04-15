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

namespace CrEOF\Geo\Obj\Data;

use CrEOF\Geo\Obj\Data\Converter;
use CrEOF\Geo\Obj\Data\Generator;
use CrEOF\Geo\Obj\Exception\RuntimeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\Traits\Singleton;

/**
 * Class DataFactory
 *
 * The DataFactory class converts from/to standard values in various formats
 * to/from Object Data Array structure using converter and generator classes.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class DataFactory
{
    use Singleton;

    /**
     * @var Generator\DataGeneratorInterface[]
     */
    private $generators;

    /**
     * @var Converter\DataConverterInterface[]
     */
    private $converters;

    /**
     * DataFactory constructor
     */
    private function __construct()
    {
        $this->addDefaultGenerators();
        $this->addDefaultConverters();
    }

    /**
     * @param mixed       $value
     * @param null|string $formatHint
     * @param null|string $typeHint
     *
     * @return array
     * @throws UnexpectedValueException
     * @throws UnsupportedFormatException
     */
    public function generate($value, $formatHint = null, $typeHint = null)
    {
        if (null !== $formatHint) {
            return $this->getGenerator($formatHint)->generate($value, $typeHint);
        }

        foreach ($this->generators as $type => $generator) {
            try {
                return $generator->generate($value, $typeHint);
            } catch (UnsupportedFormatException $e) {
                // Try next generator
            }
        }

        throw new UnexpectedValueException();
    }

    /**
     * @param array  $value
     * @param string $type
     *
     * @return mixed
     * @throws UnsupportedFormatException
     */
    public function convert(array $value, $type)
    {
        if (! array_key_exists($type, $this->converters)) {
            throw new UnsupportedFormatException('message'); //TODO
        }

        return $this->converters[$type]->convert($value);
    }

    /**
     * @param Generator\DataGeneratorInterface $generator ValueGeneratorInterface instance
     * @param string                           $format    Format supported by adapter
     *
     * @throws RuntimeException
     */
    public function addGenerator(Generator\DataGeneratorInterface $generator, $format)
    {
        if (array_key_exists($format, $this->generators)) {
            throw new RuntimeException();
        }

        $this->generators[$format] = $generator;
    }

    /**
     * @param Converter\DataConverterInterface $converter ValueGeneratorInterface
     * @param string                           $format    Format supported by generator
     *
     * @throws RuntimeException
     */
    public function addConverter(Converter\DataConverterInterface $converter, $format)
    {
        if (array_key_exists($format, $this->converters)) {
            throw new RuntimeException();
        }

        $this->converters[$format] = $converter;
    }

    /**
     * @param $format
     *
     * @return Generator\DataGeneratorInterface
     * @throws UnsupportedFormatException
     */
    private function getGenerator($format)
    {
        if (array_key_exists($format, $this->generators)) {
            return $this->generators[$format];
        }

        throw new UnsupportedFormatException('message'); //TODO
    }

    private function addDefaultGenerators()
    {
        $this->generators = [
            'wkt'         => new Generator\Wkt(),
            'wkb'         => new Generator\Wkb(),
            'geojson'     => new Generator\GeoJson(),
            'geostring'   => new Generator\GeoString(),
            'simplearray' => new Generator\SimpleArray()
        ];
    }

    private function addDefaultConverters()
    {
        $this->converters = [
            'wkt'     => new Converter\Wkt(),
            'wkb'     => new Converter\Wkb(),
            'geojson' => new Converter\GeoJson()
        ];
    }
}
