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

namespace CrEOF\Geo\Obj\Validator;

use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Object;

/**
 * Class GeographyValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeographyValidator extends AbstractValidator
{
    const CRITERIA_LONGITUDE_FIRST = 0;
    const CRITERIA_LATITUDE_FIRST  = 1;

    /**
     * @var int
     */
    private $order;

    /**
     * GeographyValidator constructor
     *
     * @param int $order
     */
    public function __construct($order)
    {
        $this->setExpectedType(Object::T_POINT);
        $this->order        = $order;
    }

    /**
     * @param array $value
     *
     * @throws ExceptionInterface
     */
    public function validate(array $value)
    {
        parent::validate($value);

        $this->validateLongitude($value['value'][$this->order]);
        $this->validateLatitude($value['value'][$this->order ? 0 : 1]);
    }

    /**
     * @param int|float $value
     *
     * @throws RangeException
     */
    protected function validateLatitude($value)
    {
        if ($value < -90 || $value > 90) {
            throw new RangeException('Invalid latitude value "' . $value . '", must be in range -90 to 90.');
        }
    }

    /**
     * @param int|float $value
     *
     * @throws RangeException
     */
    protected function validateLongitude($value)
    {
        if ($value < -180 || $value > 180) {
            throw new RangeException('Invalid longitude value "' . $value . '", must be in range -180 to 180.');
        }
    }
}
