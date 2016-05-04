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
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;

/**
 * Class ValidatorStack
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @method ValidatorInterface bottom()
 * @method ValidatorInterface current()
 * @method ValidatorInterface pop()
 * @method ValidatorInterface shift()
 * @method ValidatorInterface top()
 */

class ValidatorStack extends \SplDoublyLinkedList
{
    /**
     * @param array &$data
     *
     * @throws ExceptionInterface
     */
    public function validate(array &$data)
    {
        $this->rewind();

        while ($this->valid()) {
            $this->current()->validate($data);
            $this->next();
        }
    }

    /**
     * @param ValidatorInterface $validator
     *
     * @throws UnexpectedValueException
     */
    public function push($validator)
    {
        $this->validateValidator($validator);

        parent::push($validator);
    }

    /**
     * @param int                $index
     * @param ValidatorInterface $validator
     *
     * @throws UnexpectedValueException
     */
    public function add($index, $validator)
    {
        $this->validateValidator($validator);

        parent::add($index, $validator);
    }

    /**
     * @param mixed $validator
     *
     * @throws UnexpectedValueException
     */
    private function validateValidator($validator)
    {
        if (! ($validator instanceof ValidatorInterface)) {
            $parameterType = (is_object($validator) ? 'class "' . get_class($validator) : 'of type "' . gettype($validator)) . '"';

            throw new UnexpectedValueException('Invalid validator ' . $parameterType . '. Validators must implement ValidatorInterface.');
        }
    }
}
