<?php

namespace CrEOF\Spatial\PHP\Types;

/**
 * Abstract LineString object for LINESTRING spatial types
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class AbstractLineString extends AbstractMultiPoint
{
    /**
     * @return string
     */
    public function getType()
    {
        return self::LINESTRING;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->points[0] === $this->points[count($this->points) - 1];
    }
}
