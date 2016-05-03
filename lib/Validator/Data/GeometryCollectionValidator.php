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

namespace CrEOF\Geo\Obj\Validator\Data;

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\InvalidArgumentException;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Validator\AbstractValidator;

/**
 * Class GeometryCollectionValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeometryCollectionValidator extends AbstractValidator
{
    /**
     * GeometryCollectionValidator constructor
     */
    public function __construct()
    {
        $this->setExpectedType(Object::T_GEOMETRYCOLLECTION);
    }

    /**
     * @param array &$data
     *
     * @throws ExceptionInterface
     * @throws InvalidArgumentException
     * @throws RangeException
     * @throws UnexpectedValueException
     */
    public function validate(array &$data)
    {
        parent::validate($data);

        foreach ($data['value'] as $geometry) {
            $this->validateGeometry($geometry);
        }
    }

    /**
     * @param mixed $geometry
     *
     * @throws ExceptionInterface
     * @throws RangeException
     * @throws UnexpectedValueException
     */
    protected function validateGeometry($geometry)
    {
        if (! is_array($geometry)) {
            throw new UnexpectedValueException('Geometry value must be array of "array", "' . gettype($geometry) . '" found');
        }

        $geometry['dimension'] = $this->getExpectedDimension(); //TODO ???

        try {
            Configuration::getInstance()->getValidatorStack($geometry['type'])->validate($geometry);
        } catch (ExceptionInterface $e) {
            throw new RangeException('Bad geometry value in GeometryCollection. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
