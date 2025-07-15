<?php

namespace MLD\MersenneTwister;


class MersenneTwister
{
    /**
     * Mersenne Twister implementation in PHP.
     *
     * This class provides methods to initialize the generator with a seed,
     * generate 32-bit unsigned integers, and generate unsigned 31-bit integers.
     *
     * Extended from the original PHP-MT19937 library by Leigh T.
     * This implementation adds a method to generate random integers within a specified range,
     * and to get the seed used for initialization,
     *
     * @see https://en.wikipedia.org/wiki/Mersenne_Twister
     * @see https://github.com/lt/PHP-MT19937/blob/master/lib/MT.php
     * @license https://opensource.org/licenses/MIT Copyright (c) 2016 Leigh T
     */


    /**
     * @param int|null $seed
     */
    public function __construct($seed = null)
    {
        $this->seed = ($seed === null) ? rand() : intval($seed);
        $this->init($this->seed);
    }

    /**
     * @var int The seed used to initialize the generator.
     */
    protected $seed = 0;

    /**
     * @var int[] The internal state of the Mersenne Twister generator.
     */
    protected $state = [];

    /**
     * @var int The current index in the internal state array.
     */
    protected $index = 625;

    /**
     * Initializes the Mersenne Twister generator with a seed.
     *
     * @param int $seed The seed value to initialize the generator.
     * @return void
     */
    public function init($seed)
    {
        $state = [$seed & 0xffffffff];

        $int0 = $seed & 0xffff;
        $int1 = ($seed >> 16) & 0xffff;

        for ($i = 1; $i < 624; $i++) {
            // This is a 32-bit safe version of:
            // $state[$i] = (1812433253 * ($state[$i - 1] ^ ($state[$i - 1] >> 30)) + $i) & 0xffffffff;
            $int0 ^= $int1 >> 14;

            $carry = (0x8965 * $int0) + $i;
            $int1 = ((0x8965 * $int1) + (0x6C07 * $int0) + ($carry >> 16)) & 0xffff;
            $int0 = $carry & 0xffff;

            $state[$i] = ($int1 << 16) | $int0;
        }

        $this->state = $state;
        $this->index = $i;
    }

    /**
     * @param int $m
     * @param int $u
     * @param int $v
     * @return int
     */
    protected function twist($m, $u, $v)
    {
        $y = ($u & 0x80000000) | ($v & 0x7fffffff);
        return $m ^ (($y >> 1) & 0x7fffffff) ^ (0x9908b0df * ($v & 1));
    }

    /**
     * Generates a random 32-bit unsigned integer.
     *
     * @return int A random 32-bit unsigned integer.
     */
    public function int32()
    {
        if ($this->index >= 624) {
            if ($this->index === 625) {
                $this->init(5489);
            }

            $state = $this->state;
            for ($i = 0; $i < 227; $i++) {
                $state[$i] = $this->twist($state[$i + 397], $state[$i], $state[$i + 1]);
            }

            for (; $i < 623; $i++) {
                $state[$i] = $this->twist($state[$i - 227], $state[$i], $state[$i + 1]);
            }

            $state[623] = $this->twist($state[396], $state[623], $state[0]);
            $this->state = $state;

            $this->index = 0;
        }

        $y = $this->state[$this->index++];

        $y ^= ($y >> 11) & 0x001fffff;
        $y ^= ($y << 7) & 0x9d2c5680;
        $y ^= ($y << 15) & 0xefc60000;

        return $y ^ $y >> 18 & 0x00003fff;
    }

    /**
     * Generates a random 31-bit unsigned integer.
     *
     * @return int A random 31-bit unsigned integer.
     */
    public function int31()
    {
        return ($this->int32() >> 1) & 0x7fffffff;
    }

    /**
     * Generates a random integer between $min and $max (inclusive).
     *
     * @param int $min Minimum value (default is 0).
     * @param int $max Maximum value (default is 2^32-1).
     * @return int Random integer between $min and $max.
     * @throws \InvalidArgumentException If $min is greater than $max.
     */
    public function rand($min = 0, $max = 0xffffffff)
    {
        if ($min > $max) {
            throw new \InvalidArgumentException("Minimum value cannot be greater than maximum value.");
        }

        return $min + ($this->int32() % ($max - $min + 1));
    }

    /**
     * Generates a random floating-point number between $min and $max (inclusive).
     * @param float $min Minimum value (default is 0.0).
     * @param float $max Maximum value (default is 1.0).
     * @return float Random floating-point number between $min and $max.
     * @throws \InvalidArgumentException If $min is greater than $max.
     */
    public function randFloat($min = 0.0, $max = 1.0)
    {
        if ($min > $max) {
            throw new \InvalidArgumentException("Minimum value cannot be greater than maximum value.");
        }

        return $min + ($this->int32() / 0xffffffff) * ($max - $min);
    }

    /**
     * Returns the seed used to initialize the generator.
     *
     * @return int The seed value.
     */
    public function getSeed()
    {
        return $this->seed;
    }
}