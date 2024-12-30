<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

const INT32_BIT_SIZE = 32;
const INT32_BYTE_SIZE = 4;
const INT32_MAX = 4294967296; // 2^32
const INT64_BIT_SIZE = 64;
const INT64_BYTE_SIZE = 8;

if(!function_exists('rrotate') ) {
    /**
     * Right rotation of $value by $n bit
     * This is an unsafe function that does not handle the cases
     *   - $n < 0
     *   - $n >= INT32_BIT_SIZE
     *
     * @param int $value int32
     * @param int $n [0,32)
     */
    function rrotate(int $value, int $n): int
    {
        return ($value >> $n) | (($value << (INT32_BIT_SIZE  - $n)) & 0xffffffff);
    }
}

if(!function_exists('dd') ) {
    function dd(...$args) {
        if (count($args)) {
            var_dump(...$args);
        }
        die;
    }
}

if(!function_exists('dump') ) {
    function dump(...$args) {
        var_dump(...$args);

        return $args;
    }
}

if(!function_exists('p') ) {
    function p(...$args) {
        if ($_ENV['BLAKE3_NOOPT_DEBUG'] ?? false) {
            fwrite(STDOUT, count($args) > 1 ? json_encode($args) : $args[0]);
            fwrite(STDOUT, PHP_EOL);
        }

        return $args;
    }
}
