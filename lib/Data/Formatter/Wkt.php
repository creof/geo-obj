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

namespace CrEOF\Geo\Obj\Data\Formatter;

/**
 * Class Wkt
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Wkt implements FormatterInterface
{
    /**
     * @param array $data
     *
     * @return mixed
     */
    public function format(array $data)
    {
        // TODO: MultiGeometry case
        $result = strtoupper($data['type']) . '(' . $this->getValueString($data['value']) . ')';

        return $result;
    }

    /**
     * @param array $value
     * @param int   $depth
     *
     * @return string
     */
    private function getValueString(array $value, $depth = 0)
    {
        if (! is_array($value[0])) {
            return implode(' ', $value);
        }

        $results = [];
        $depth++;

        foreach ($value as $item) {
            $results[] = $this->getValueString($item, $depth);
        }

        $result = implode(',', $results);

        if (2 > $depth) {
            return $result;
        }

        return '(' . $result . ')';
    }
}
