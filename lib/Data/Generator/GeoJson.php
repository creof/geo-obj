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

namespace CrEOF\Geo\Obj\Data\Generator;

use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Object;

/**
 * Class GeoJson
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeoJson implements GeneratorInterface
{
    /**
     * @param mixed       $value
     * @param null|string $typeHint
     *
     * @return array
     * @throws UnexpectedValueException
     * @throws UnsupportedFormatException
     */
    public function generate($value, $typeHint = null)
    {
        if (! is_string($value) || '{' !== $value[0]) {
            throw new UnsupportedFormatException();
        }

        $data = json_decode($value, true);

        if (null === $data) {
            throw new UnexpectedValueException($this->getJsonError());
        }

        switch ($data['type']) {
            case Object::T_POINT:
                // no break
            case Object::T_LINESTRING:
                //no break
            case Object::T_POLYGON:
                //no break
            case Object::T_MULTIPOINT:
                //no break
            case Object::T_MULTILINESTRING:
                // no break
            case Object::T_MULTIPOLYGON:
                $key = 'coordinates';
                break;
            case Object::T_FEATURE:
                $key = 'geometry';
                break;
            case Object::T_FEATURECOLLECTION:
                $key = 'features';
                break;
            default:
                throw new UnexpectedValueException();
        }

        return [
            'type'       => strtolower($data['type']),
            'value'      => 'geometry' === $key ? $this->getValueFromGeometry($data[$key]) : $data[$key],
            'properties' => array_key_exists('properties', $data) ? $data['properties'] : null
        ];
    }

    /**
     * @param array $geometry
     *
     * @return array
     */
    private function getValueFromGeometry(array $geometry)
    {
        return [
            'type'  => strtolower($geometry['type']),
            'value' => $geometry['coordinates']
        ];
    }

    /**
     * @return string
     */
    private function getJsonError()
    {
        $error = json_last_error();

        switch ($error) {
            case JSON_ERROR_NONE:
                return 'No errors';
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
        }

        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            switch ($error) {
                case JSON_ERROR_RECURSION:
                    return 'Recursive references found';
                case JSON_ERROR_INF_OR_NAN:
                    return 'Value includes Inf or NaN';
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    return 'Unsupported type';
            }
        }

        return 'Unknown error';
    }
}
