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

/**
 * Class SimpleArray
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class SimpleArray implements GeneratorInterface
{
    /**
     * @param mixed       $value
     * @param null|string $typeHint
     *
     * @return array
     * @throws UnsupportedFormatException
     */
    public function generate($value, $typeHint = null)
    {
        if (! is_array($value)) {
            throw new UnsupportedFormatException();
        }

        if (! array_key_exists('dimension', $value) && array_key_exists('type', $value)) {
            $value = $this->getDimensionFromType($value);
        }

        $data = [
            'value' => array_key_exists('value', $value) ? $value['value'] : $value,
            'type'  => array_key_exists('type', $value) ? $value['type'] : $typeHint,
            'srid'  => array_key_exists('srid', $value) ? $value['srid'] : null
        ];

        $data['dimension'] = array_key_exists('dimension', $value) ? $this->getExistingDimension($value['dimension']) : $this->getDimensionFromValue($data['value']);

        return $data;
    }

    /**
     * @param null|string $dimension
     *
     * @return null|string
     */
    private function getExistingDimension($dimension)
    {
        return null !== $dimension ? strtoupper($dimension) : null;
    }

    /**
     * @param array $value
     *
     * @return null|string
     */
    private function getDimensionFromValue(array $value)
    {
        if (array_key_exists('type', $value)) {
            return $this->getDimensionFromValue($value['value']);
        }

        if (is_array($value[0])) {
            return $this->getDimensionFromValue($value[0]);
        }

        switch (count($value)) {
            case 3:
                return 'Z';
            case 4:
                return 'ZM';
        }

        return null;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    private function getDimensionFromType(array $value)
    {
        $matches = [];

        preg_match('/(\w+)(?:\s*)(z|m|zm)$/i', $value['type'], $matches);

        if (3 === count($matches)) {
            $value['type']      = $matches[1];
            $value['dimension'] = $matches[2];
        }

        return $value;
    }
}
