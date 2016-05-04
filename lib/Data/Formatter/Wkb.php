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

    const WKB_MISMATCH_DROP           = 0;
    const WKB_MISMATCH_FAIL           = 1;

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
    private $mismatchAction;

    /**
     * @var string
     */
    private $value;

    /**
     * @var array[]
     */
    private $data;

    /**
     * @var int
     */
    private static $machineByteOrder;

    /**
     * Wkb constructor
     *
     * @param int $byteOrder
     * @param int $flags
     * @param int $mismatchAction
     *
     * @throws UnexpectedValueException
     */
    public function __construct($byteOrder = self::WKB_XDR, $flags = self::WKB_FLAG_NONE, $mismatchAction = self::WKB_MISMATCH_DROP)
    {
        if ($byteOrder !== self::WKB_XDR && $byteOrder !== self::WKB_NDR) {
            throw new UnexpectedValueException();
        }

        if (0 !== (~ (self::WKB_FLAG_SRID | self::WKB_FLAG_M | self::WKB_FLAG_Z) & $flags)) {
            throw new UnexpectedValueException();
        }

        if ($mismatchAction !== self::WKB_MISMATCH_DROP && $mismatchAction !== self::WKB_MISMATCH_FAIL) {
            throw new UnexpectedValueException();
        }

        $this->byteOrder      = $byteOrder;
        $this->flags          = $flags;
        $this->mismatchAction = $mismatchAction;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function format(array $data)
    {
        $this->data  = $data;
        $this->value = null;
        $typeName    = $this->data['type'];
        $flags       = $this->getFlags();

        $this->byteOrder();

        if (($flags & self::WKB_FLAG_SRID) === self::WKB_FLAG_SRID) {
            $this->appendLong($this->data['srid']);
        }

        $this->$typeName($this->data['value'], $flags);

        return $this->value;
    }

    private function byteOrder()
    {
        $this->value .= pack('C', $this->byteOrder);
    }

    /**
     * @param array $point
     * @param int   $flags
     */
    private function point(array $point, $flags)
    {
        $this->appendLong(self::WKB_TYPE_POINT | $flags);

        $this->appendFloats($point);
    }

    /**
     * @param array $points
     * @param int   $flags
     */
    private function lineString(array $points, $flags)
    {
        $this->appendLong(self::WKB_TYPE_LINESTRING | $flags);
        $this->appendCount($points);

        foreach ($points as $point) {
            $this->appendFloats($point);
        }
    }

    /**
     * @param array $rings
     * @param int   $flags
     */
    private function polygon(array $rings, $flags)
    {
        $this->appendLong(self::WKB_TYPE_POLYGON | $flags);
        $this->appendCount($rings);

        foreach ($rings as $ring) {
            $this->appendCount($ring);

            foreach ($ring as $point) {
                $this->appendFloats($point);
            }
        }
    }

    /**
     * @param array $points
     * @param int   $flags
     */
    private function multiPoint(array $points, $flags)
    {
        $this->appendLong(self::WKB_TYPE_MULTIPOINT | $flags);
        $this->appendCount($points);

        foreach ($points as $point) {
            $this->byteOrder();
            $this->point($point, $flags);
        }
    }

    /**
     * @param array $lineStrings
     * @param int   $flags
     */
    private function multiLineString(array $lineStrings, $flags)
    {
        $this->appendLong(self::WKB_TYPE_MULTILINESTRING | $flags);
        $this->appendCount($lineStrings);

        foreach ($lineStrings as $lineString) {
            $this->byteOrder();
            $this->lineString($lineString, $flags);
        }
    }

    /**
     * @param array $polygons
     * @param int   $flags
     */
    private function multiPolygon(array $polygons, $flags)
    {
        $this->appendLong(self::WKB_TYPE_MULTIPOLYGON | $flags);
        $this->appendCount($polygons);

        foreach ($polygons as $polygon) {
            $this->byteOrder();
            $this->polygon($polygon, $flags);
        }
    }

    /**
     * @return int
     */
    private function getFlags()
    {
        $flags = 0;

        if (null !== $this->data['dimension']) {
            $flags = $this->getDimensionFlags();
        }

        if (null !== $this->data['srid']) {
            $flags |= self::WKB_FLAG_SRID;
        }

        if (($flags & $this->flags) !== $flags) {
            throw new UnexpectedValueException(); //TODO mismatchAction?
//            if (self::WKB_MISMATCH_DROP === $this->mismatchAction && size($this->data['dimension'] > size($expected))) {
//            }

        }

        return $flags;
    }

    /**
     * @return int
     */
    private function getDimensionFlags()
    {
        $flags = 0;

        foreach (str_split($this->data['dimension']) as $dimension) {
            $flags |= constant('self::WKB_FLAG_' . strtoupper($dimension));
        }

        return $flags;
    }

    /**
     * @return bool
     */
    private function getMachineByteOrder()
    {
        if (null !== self::$machineByteOrder) {
            return self::$machineByteOrder;
        }

        self::$machineByteOrder = unpack('S', "\x01\x00")[1] === 1 ? self::WKB_NDR : self::WKB_XDR;

        return self::$machineByteOrder;
    }

    /**
     * @param float $float
     */
    private function appendFloat($float)
    {
        if ($this->getMachineByteOrder() === $this->byteOrder) {
            $this->value .= pack('d', $float);

            return;
        }

        $this->value .= strrev(pack('d', $float));
    }

    /**
     * @param float[] $floats
     */
    private function appendFloats(array $floats)
    {
        foreach ($floats as $float) {
            $this->appendFloat($float);
        }
    }

    /**
     * @param int $long
     */
    private function appendLong($long)
    {
        if (self::WKB_NDR === $this->byteOrder) {
            $this->value .= pack('V', $long);

            return;
        }

        $this->value .= pack('N', $long);
    }

    /**
     * @param array $array
     */
    private function appendCount(array $array)
    {
        $this->appendLong(count($array));
    }
}
