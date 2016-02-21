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

namespace CrEOF\Geo\Obj\Tests;

use CrEOF\Geo\Obj\ObjectFactory;
use CrEOF\Geo\Obj\Point;

/**
 * Class ObjectFactoryTest
 *
 * @backupStaticAttributes
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultGeneratorWktHintWkt()
    {
        $expected = new Point(array(34.23, -87));

        $actual = ObjectFactory::getInstance()->create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'), 'wkb');

        self::assertEquals($expected, $actual);
    }

    public function testDefaultGeneratorWktNoHint()
    {
        $expected = new Point(array(34.23, -87));

        $actual = ObjectFactory::getInstance()->create(pack('H*', '01010000003D0AD7A3701D41400000000000C055C0'));

        self::assertEquals($expected, $actual);
    }
}
