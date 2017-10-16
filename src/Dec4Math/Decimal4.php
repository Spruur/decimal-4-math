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

    /**
     * @var string
     */
    private $mills;

    /**
     * @param string $string Fraction part must be separated from integer part with
     * the dot (.) symbol.
     */
    public function __construct($string)
    {
        if (strpos($string, self::SEPARATOR) === false) {
            $this->mills = gmp_strval(
                gmp_mul(
                    gmp_init($string,10),
                10000
                )
            );
            return;
        }

        $parts = explode(self::SEPARATOR, $string);

        while (strlen(trim($parts[1])) < 4) {
            $parts[1] .= '0';
        }

        $this->mills = gmp_strval(
            gmp_add(
                gmp_mul(
                    gmp_abs(gmp_init($parts[0], 10)),
                    10000
                ),
                self::roundMills(trim($parts[1]))
            )
        );

        if (trim($string[0]) === '-') {
            $this->mills = gmp_strval(gmp_neg(gmp_init($this->mills)));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        $zeros = str_repeat('0', 4 - strlen(abs($this->fractionValue())));
        return $this->integerValue().self::SEPARATOR.$zeros.(abs($this->fractionValue()));
    }

    /**
     * The integer part of this amount (without mills).
     *
     * @return integer Or string, if the number is too big.
     */
    public function integerValue() {
        $sign = (
            gmp_cmp(
                gmp_init($this->mills, 10),
                0
            ) < 0
        ) ? '-' : '';

        return $sign.gmp_strval(
            gmp_div_q(
                gmp_abs(
                    gmp_init($this->mills, 10)
                ),
                10000
            ),
            10
        );
    }


    /**
     * Returns the amount of mills in this decimal fraction part.
     *
     * @return integer
     */
    public function fractionValue() {
        $return = gmp_div_r(
            gmp_abs(
                gmp_init($this->mills, 10)
            ),
            10000
        );

        if (gmp_cmp(gmp_init($this->mills, 10), 0) < 0)
            $return = gmp_neg($return);

        return gmp_intval($return);
    }


    /**
     * Returns entire amount in mills.
     *
     * @return integer Or string, if the number is too big.
     */
    public function millsValue() {
        if (gmp_cmp(gmp_abs(gmp_init($this->mills, 10)), PHP_INT_MAX) > 0) {
            return $this->mills;
        }
        return gmp_intval(gmp_init($this->mills, 10));
    }

    private static function roundMills($num) {
        $digitsAmount = strlen($num);

        if ($digitsAmount < 5) return gmp_init($num, 10);

        $divider = gmp_pow(10, $digitsAmount - 4);
        $addition = gmp_mul(5, gmp_pow(10, $digitsAmount - 5));
        return gmp_div_q(
            gmp_add(gmp_init($num, 10), $addition),
            $divider,
            GMP_ROUND_ZERO);
    }
}