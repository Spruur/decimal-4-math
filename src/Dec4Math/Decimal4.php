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



    /**
     * Creates a new decimal which is a sum of $a and $b
     *
     * @param Decimal4 $a
     * @param Decimal4 $b
     * @return Decimal4
     */
    public static function plus(Decimal4 $a, Decimal4 $b) {
        $return = new Decimal4("0");

        $return->mills = gmp_strval(
            gmp_add(
                gmp_init($a->mills, 10),
                gmp_init($b->mills, 10)
            )
        );

        return $return;
    }

    /**
     * Creates a new decimal which is a difference of the $a and $b.
     *
     * @param Decimal4 $a
     * @param Decimal4 $b
     * @return Decimal4
     */
    public static function minus(Decimal4 $a, Decimal4 $b) {
        $return = new Decimal4("0");

        $return->mills = gmp_strval(
            gmp_add(
                gmp_init($a->mills, 10),
                gmp_neg(gmp_init($b->mills, 10))
            )
        );

        return $return;
    }


    /**
     * Creates a new decimal which is a result of a multiplication of the passed decimal by the
     * passed integer factor.
     *
     * @param Decimal4 $decimal
     * @param integer $int
     * @return Decimal4
     */
    public static function multiply(Decimal4 $decimal, $int) {
        $return = new Decimal4("0");

        $return->mills = gmp_strval(
            gmp_mul(
                gmp_init($decimal->mills, 10),
                gmp_init($int, 10)
            )
        );

        return $return;
    }

    /**
     * Creates a new decimal which is a result of a multiplication of the passed decimals.
     *
     * @param Decimal4 $a
     * @param Decimal4 $b
     * @return Decimal4
     */
    public static function mul(Decimal4 $a, Decimal4 $b) {
        $return = new Decimal4("0");

        $return->mills = gmp_strval(
            gmp_div_q(
                gmp_mul(
                    gmp_init($a->mills, 10),
                    gmp_init($b->mills, 10)
                ),
                10000
            )
        );

        return $return;
    }


    /**
     * Creates a new decimal which is a result of a division of $a by $b.
     *
     * @param Decimal4 $a
     * @param Decimal4 $b
     * @return Decimal4
     */
    public static function div(Decimal4 $a, Decimal4 $b) {
        $signA = (strval($a)[0] === '-') ? -1 : 1;
        $signB = (strval($b)[0] === '-') ? -1 : 1;

        $return = new Decimal4('0');
        $return->mills = gmp_strval(
            gmp_div_q(
                gmp_add(
                    gmp_mul(
                        gmp_abs(gmp_init($a->mills, 10)),
                        10000
                    ),
                    5000
                ),
                gmp_abs(gmp_init($b->mills, 10)),
                GMP_ROUND_ZERO
            )
        );

        if ($signA * $signB < 0) {
            $return->mills = gmp_strval(
                gmp_neg(gmp_init($return->mills, 10))
            );
        }

        return $return;
    }
}