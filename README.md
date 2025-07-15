Mersenne Twister written in PHP
===============================

This library contains pure PHP implementations of the Mersenne Twister pseudo-random number generation algorithm.

It started off as a fork from [lt/PHP-MT19937](https://github.com/lt/PHP-MT19937) but is both heavily shrunk and
slightly extended.

Compared to the original, this library has only one class, `MLD/MersenneTwister`, which mostly is a copy of the upstream
`MersenneTwister\MT`.

It is also extended with three methods:

* The `getSeed()` method, which returns the current seed value
* The `rand($min, $max)` method, which returns a random integer in the range of `min` to `max`, inclusive
* The `randFloat($min, $max)` method, which returns a random float in the range of `min` to `max`, inclusive

### `MersenneTwister\MT`

This is the Mersenne Twister algorithm as defined by the algorithm authors.

It works on both 32 and 64 bit builds of PHP and outputs 32 bit integers.

```php
$mt = new MLD\MersenneTwister\MersenneTwister();
$mt->init(1234); // mt_srand(1234);
$mt->int31();    // int31() per mt19937ar.c, positive values only
$mt->int32();    // int32() per mt19937ar.c, high bit sets sign
$mt->rand(1,10); // works like the php rand() function
$mt->randFloat(1.0, 10.0); // works like $mt->rand(), but returns a float

```
