<?php

namespace Dec4Math;

/*
 * An unimaginably immense decimal number which has 4-digit fraction part.
 * 2.6734
 * 150.0000
 * 999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999.9999
 */
class Decimal4 {
    const SEPARATOR = '.';

    private $mill;

    public function __construct($string)
    {

        if (strpos($string, self::SEPARATOR) === false) {
            $this->mill = gmp_strval(
                gmp_mul(
                    gmp_init($string,10),
                100
                )
            );
            echo $this->mill;
            return;
        }
    }


}