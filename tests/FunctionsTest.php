<?php

// phpunit --testdox tests

declare(strict_types=1);

namespace Tibarj\Blake3Noopt\tests;

use Generator;
use PHPUnit\Framework\TestCase;

use const Tibarj\Blake3Noopt\INT32_BIT_SIZE;

use function Tibarj\Blake3Noopt\rrotate;

final class FunctionsTest extends TestCase
{
    function data(): Generator
    {
        yield ["11110000000111000000001100000010",  0, "11110000000111000000001100000010"];
        yield ["11110000000111000000001100000010",  1, "01111000000011100000000110000001"];
        yield ["11110000000111000000001100000010",  2, "10111100000001110000000011000000"];
        yield ["11110000000111000000001100000010",  4, "00101111000000011100000000110000"];
        yield ["11110000000111000000001100000010",  8, "00000010111100000001110000000011"];
        yield ["11110000000111000000001100000010", 16, "00000011000000101111000000011100"];
        yield ["11110000000111000000001100000010", 31, "11100000001110000000011000000101"];
        yield ["11110000000111000000001100000010", 32, "11110000000111000000001100000010"];
    }

    /**
     * @dataProvider data
     */
    function test(string $input, int $n, string $expected)
    {
        $result = str_pad(decbin(
            rrotate(bindec($input), $n)
        ), INT32_BIT_SIZE, '0', STR_PAD_LEFT);
        $this->assertEquals($expected, $result, "$input >>> $n => $result does not equals expected $expected");
    }
}
