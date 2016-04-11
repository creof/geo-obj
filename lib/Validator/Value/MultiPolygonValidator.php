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

namespace CrEOF\Geo\Obj\Validator\Value;

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Validator\AbstractValidator;

/**
 * Class MultiPolygonValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class MultiPolygonValidator extends AbstractValidator
{
    use Traits\ValidatePointTrait;

    /**
     * MultiPolygonValidator constructor
     */
    public function __construct()
    {
        $this->setExpectedType(Object::T_MULTIPOLYGON);
    }

    /**
     * @param array $value
     *
     * @throws ExceptionInterface
     */
    public function validate(array &$value)
    {
        parent::validate($value);

        foreach ($value['value'] as $polygon) {
            $this->validatePolygon($polygon);
        }
    }

    /**
     * @param mixed $polygon
     *
     * @throws ExceptionInterface
     */
    protected function validatePolygon($polygon)
    {
        if (! is_array($polygon)) {
            throw new UnexpectedValueException('MultiPolygon value must be array of "array", "' . gettype($polygon) . '" found');
        }

        try {
            foreach ($polygon as $ring) {
                $this->validateRing($ring);
            }
        } catch (ExceptionInterface $e) {
            throw new RangeException('Bad polygon value in MultiPolygon. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param mixed $ring
     *
     * @throws ExceptionInterface
     */
    protected function validateRing($ring)
    {
        if (! is_array($ring)) {
            throw new UnexpectedValueException('Polygon value must be array of "array", "' . gettype($ring) . '" found');
        }

        try {
            foreach ($ring as $point) {
                $this->validatePoint($point, 'Ring');
            }
        } catch (ExceptionInterface $e) {
            throw new RangeException('Bad ring value in Polygon. ' . $e->getMessage(), $e->getCode(), $e);
        }

        //TODO rings must be closed
    }
}
