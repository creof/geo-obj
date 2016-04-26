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

use CrEOF\Geo\Obj\Exception\UnexpectedValueException;

/**
 * Class Wkb
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Wkb implements FormatterInterface
{
    const WKB_XDR                     = 0;
    const WKB_NDR                     = 1;

    const WKB_TYPE_GEOMETRY           = 0;
    const WKB_TYPE_POINT              = 1;
    const WKB_TYPE_LINESTRING         = 2;
    const WKB_TYPE_POLYGON            = 3;
    const WKB_TYPE_MULTIPOINT         = 4;
    const WKB_TYPE_MULTILINESTRING    = 5;
    const WKB_TYPE_MULTIPOLYGON       = 6;
    const WKB_TYPE_GEOMETRYCOLLECTION = 7;
    const WKB_TYPE_CIRCULARSTRING     = 8;
    const WKB_TYPE_COMPOUNDCURVE      = 9;
    const WKB_TYPE_CURVEPOLYGON       = 10;
    const WKB_TYPE_MULTICURVE         = 11;
    const WKB_TYPE_MULTISURFACE       = 12;
    const WKB_TYPE_CURVE              = 13;
    const WKB_TYPE_SURFACE            = 14;
    const WKB_TYPE_POLYHEDRALSURFACE  = 15;
    const WKB_TYPE_TIN                = 16;
    const WKB_TYPE_TRIANGLE           = 17;

    const WKB_FLAG_NONE               = 0x00000000;
    const WKB_FLAG_SRID               = 0x20000000;
    const WKB_FLAG_M                  = 0x40000000;
    const WKB_FLAG_Z                  = 0x80000000;

    const WKB_UNSUPPORTED_DROP        = 0;
    const WKB_UNSUPPORTED_FAIL        = 1;

    /**
     * @var int
     */
    private $byteOrder;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var int
     */
    private $unsupportedAction;

    private $value;

    /**
     * Wkb constructor
     *
     * @param int $byteOrder
     * @param int $flags
     * @param int $unsupportedAction
     *
     * @throws UnexpectedValueException
     */
    public function __construct($byteOrder = self::WKB_XDR, $flags = self::WKB_FLAG_NONE, $unsupportedAction = self::WKB_UNSUPPORTED_DROP)
    {
        if ($byteOrder !== self::WKB_XDR && $byteOrder !== self::WKB_NDR) {
            throw new UnexpectedValueException();
        }

        if (0 !== (self::WKB_FLAG_SRID & self::WKB_FLAG_M & self::WKB_FLAG_Z) ^ $flags) {
            throw new UnexpectedValueException();
        }

        if ($unsupportedAction !== self::WKB_UNSUPPORTED_DROP && $unsupportedAction !== self::WKB_UNSUPPORTED_FAIL) {
            throw new UnexpectedValueException();
        }

        $this->byteOrder         = $byteOrder;
        $this->flags             = $flags;
        $this->unsupportedAction = $unsupportedAction;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function format(array $data)
    {
        $this->value = pack('C', $this->byteOrder);

        $type = constant('self::WKB_TYPE_' . strtoupper($data['type']));

        // Convert value to format
        return $data;
    }
}
