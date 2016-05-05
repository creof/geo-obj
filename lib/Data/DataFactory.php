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

use CrEOF\Geo\Obj\Data\Formatter;
use CrEOF\Geo\Obj\Data\Generator;
use CrEOF\Geo\Obj\Exception\RuntimeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Exception\UnknownTypeException;
use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Traits\Singleton;

/**
 * Class DataFactory
 *
 * The DataFactory class converts from/to standard values in various formats
 * to/from Object Data Array structure using GeneratorInterface and FormatterInterface instances.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class DataFactory
{
    use Singleton;

    /**
     * @var Generator\GeneratorInterface[]
     */
    private $generators;

    /**
     * @var Formatter\FormatterInterface[]
     */
    private $formatters;

    /**
     * DataFactory constructor
     */
    private function __construct()
    {
        $this->addDefaultGenerators();
        $this->addDefaultFormatters();
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
            return $this->normalizeObjectData($this->getGenerator($formatHint)->generate($value, $typeHint));
        }

        foreach ($this->generators as $generator) {
            try {
                return $this->normalizeObjectData($generator->generate($value, $typeHint));
            } catch (UnsupportedFormatException $e) {
                // Try next generator
            }
        }

        throw new UnexpectedValueException();
    }

    /**
     * @param array  $value
     * @param string $format
     *
     * @return mixed
     * @throws UnsupportedFormatException
     */
    public function format(array $value, $format)
    {
        if (! array_key_exists($format, $this->formatters)) {
            throw new UnsupportedFormatException('message'); //TODO
        }

        return $this->formatters[$format]->format($value);
    }

    /**
     * @param string      $value
     * @param string      $outFormat
     * @param null|string $inFormatHint
     *
     * @return mixed
     */
    public function convert($value, $outFormat, $inFormatHint = null)
    {
        return $this->format($this->generate($value, $inFormatHint), $outFormat);
    }

    /**
     * @param Generator\GeneratorInterface $generator ValueGeneratorInterface instance
     * @param string                       $format    Format supported by adapter
     *
     * @throws RuntimeException
     */
    public function addGenerator(Generator\GeneratorInterface $generator, $format)
    {
        if (array_key_exists($format, $this->generators)) {
            throw new RuntimeException();
        }

        $this->generators[$format] = $generator;
    }

    /**
     * @param Formatter\FormatterInterface $formatter ValueGeneratorInterface
     * @param string                       $format    Format supported by generator
     *
     * @throws RuntimeException
     */
    public function addFormatter(Formatter\FormatterInterface $formatter, $format)
    {
        if (array_key_exists($format, $this->formatters)) {
            throw new RuntimeException();
        }

        $this->formatters[$format] = $formatter;
    }

    /**
     * @param $format
     *
     * @return Generator\GeneratorInterface
     * @throws UnsupportedFormatException
     */
    private function getGenerator($format)
    {
        if (array_key_exists($format, $this->generators)) {
            return $this->generators[$format];
        }

        throw new UnsupportedFormatException('message'); //TODO
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws UnknownTypeException
     */
    private function normalizeObjectData(array $data)
    {
        return [
            'type'       => Object::getProperTypeName($data['type']),
            'value'      => $data['value'],
            'srid'       => array_key_exists('srid', $data) ? $data['srid'] : null,
            'dimension'  => array_key_exists('dimension', $data) ? $data['dimension'] : null,
            'properties' => array_key_exists('properties', $data) ? $data['properties'] : []
        ];
    }

    private function addDefaultGenerators()
    {
        $this->generators = [
            'wkt'         => new Generator\WKT(),
            'wkb'         => new Generator\WKB(),
            'geojson'     => new Generator\GeoJson(),
            'geostring'   => new Generator\GeoString(),
            'simplearray' => new Generator\SimpleArray()
        ];
    }

    private function addDefaultFormatters()
    {
        $this->formatters = [
            'wkt'     => new Formatter\WKT(),
            'wkb'     => new Formatter\WKB(),
            'geojson' => new Formatter\GeoJson()
        ];
    }
}
