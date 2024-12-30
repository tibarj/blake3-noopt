<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

abstract class AbstractBlake3
{
    protected final const IV0 = 0x6a09e667;
    protected final const IV1 = 0xbb67ae85;
    protected final const IV2 = 0x3c6ef372;
    protected final const IV3 = 0xa54ff53a;
    protected final const IV4 = 0x510e527f;
    protected final const IV5 = 0x9b05688c;
    protected final const IV6 = 0x1f83d9ab;
    protected final const IV7 = 0x5be0cd19;

    protected final const FLAG_CHUNK_START         =  1; // 2^0
    protected final const FLAG_CHUNK_END           =  2; // 2^1
    protected final const FLAG_PARENT              =  4; // 2^2
    protected final const FLAG_ROOT                =  8; // 2^3
    protected final const FLAG_KEYED_HASH          = 16; // 2^4
    protected final const FLAG_DERIVE_KEY_CONTEXT  = 32; // 2^5
    protected final const FLAG_DERIVE_KEY_MATERIAL = 64; // 2^6

    protected final const KEY_SIZE_WORD    = 8;    //  8 uint32 words = 32 bytes =  256 bits
    protected final const KEY_SIZE_BYTE    = 32;   //  8 uint32 words = 32 bytes =  256 bits
    protected final const CHUNK_SIZE_BYTE  = 1024; //                 1024 bytes = 8192 bits = 16 blocks [MAX SIZE]
    protected final const BLOCK_SIZE_BYTE  = 64;   // 16 uint32 words = 64 bytes =  512 bits
    protected final const CHAIN_SIZE_BYTE  = 32;   //  8 uint32 words = 32 bytes =  256 bits
    protected final const CHAIN_SIZE_WORD  = 8;    //  8 uint32 words = 32 bytes =  256 bits
    protected final const OUTPUT_SIZE_WORD = 16;   // 16 uint32 words = 64 bytes =  512 bits
    protected final const DIGEST_SIZE_BYTE = 32;   //  8 uint32 words = 32 bytes =  256 bits

    /**
     * @param int $count Number of words to pack, 0 => auto
     */
    protected static function pack(array $words, int $count = 0): string
    {
        p(__METHOD__);

        return pack('V' . ($count ?: '*'), ...$words);
    }

    /**
     * @param int $count Number of words to unpack, 0 => auto
     */
    protected static function unpack(string $str, int $count = 0): array
    {
        p(__METHOD__);

        return array_values(unpack('V' . ($count ?: '*'), $str));
    }

    /**
     * @param string $block strictly 64 bytes
     * @param int[] $v (16 uint32)
     * @return int[] v' (16 uint32)
     */
    protected static function compress(string $block, array $v): array
    {
        if ($_ENV['BLAKE3_NOOPT_DEBUG'] ?? false) {
            p('t0: ' . $v[12]);
            p('t1: ' . $v[13]);
            p('b: ' . $v[14]);
            self::printFlags($v[15]);
            self::printMatrix('m', str_split($block, 4), 'bin2hex');
            self::printMatrix('v', $v, 'dechex');
        }

        $m = static::unpack($block, self::OUTPUT_SIZE_WORD); // 16 uint32 (little endian byte order)
        $h = array_slice($v, 0, self::CHAIN_SIZE_WORD);

        // the 7 rounds
        self::round($m, $v);
        self::round($m = self::permute($m), $v);
        self::round($m = self::permute($m), $v);
        self::round($m = self::permute($m), $v);
        self::round($m = self::permute($m), $v);
        self::round($m = self::permute($m), $v);
        self::round($m = self::permute($m), $v);

        return [
            $v[ 0] ^ $v[ 8],    $v[ 1] ^ $v[ 9],    $v[ 2] ^ $v[10],    $v[ 3] ^ $v[11],
            $v[ 4] ^ $v[12],    $v[ 5] ^ $v[13],    $v[ 6] ^ $v[14],    $v[ 7] ^ $v[15],

            $h[ 0] ^ $v[ 8],    $h[ 1] ^ $v[ 9],    $h[ 2] ^ $v[10],    $h[ 3] ^ $v[11],
            $h[ 4] ^ $v[12],    $h[ 5] ^ $v[13],    $h[ 6] ^ $v[14],    $h[ 7] ^ $v[15],
        ];
    }

    /**
     * @param int[] $m (16 uint32)
     * @param int[] &$v (16 uint32)
     */
    private static function round(array $m, array &$v): void
    {
        self::quarterRound($m[ 0], $m[ 1], $v[0], $v[4], $v[ 8], $v[12]);
        self::quarterRound($m[ 2], $m[ 3], $v[1], $v[5], $v[ 9], $v[13]);
        self::quarterRound($m[ 4], $m[ 5], $v[2], $v[6], $v[10], $v[14]);
        self::quarterRound($m[ 6], $m[ 7], $v[3], $v[7], $v[11], $v[15]);

        //                                   rot0   rot1   rot2    rot3
        self::quarterRound($m[ 8], $m[ 9], $v[0], $v[5], $v[10], $v[15]);
        self::quarterRound($m[10], $m[11], $v[1], $v[6], $v[11], $v[12]);
        self::quarterRound($m[12], $m[13], $v[2], $v[7], $v[ 8], $v[13]);
        self::quarterRound($m[14], $m[15], $v[3], $v[4], $v[ 9], $v[14]);
    }

    /**
     * All params are uint32
     */
    private static function quarterRound(
        int $m0, int $m1,
        int &$a, int &$b, int &$c, int &$d
    ): void {
        $a = ($a + $b + $m0) % INT32_MAX;
        $d = rrotate($d ^ $a, 16);
        $c = ($c + $d) % INT32_MAX;
        $b = rrotate($b ^ $c, 12);
        $a = ($a + $b + $m1) % INT32_MAX;
        $d = rrotate($d ^ $a,  8);
        $c = ($c + $d) % INT32_MAX;
        $b = rrotate($b ^ $c,  7);
    }

    /**
     * @param int[] $m (16 uint32)
     * @return int[] m' (16 uint32)
     */
    private static function permute($m): array
    {
        return [
            $m[ 2], $m[ 6], $m[ 3], $m[10],
            $m[ 7], $m[ 0], $m[ 4], $m[13],
            $m[ 1], $m[11], $m[12], $m[ 5],
            $m[ 9], $m[14], $m[15], $m[ 8],
        ];
    }

    private static function printMatrix(
        string $name,
        array $values,
        string $transform,
    ): void {
        $values = array_map(
            static fn ($value): string => str_pad(
                strtoupper($transform($value)),
                8,
                '0',
                STR_PAD_LEFT
            ),
            $values
        );
        $lines = array_chunk($values, 4);
        $lines = array_map(
            static fn ($line): string => implode(' ', $line),
            $lines
        );

        p($name . ': ' . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL);
    }

    private static function printFlags(int $flag): void
    {
        $out = [];
        if ($flag & self::FLAG_CHUNK_START)         $out[] = 'CHUNK_START';
        if ($flag & self::FLAG_CHUNK_END)           $out[] = 'CHUNK_END';
        if ($flag & self::FLAG_PARENT)              $out[] = 'PARENT';
        if ($flag & self::FLAG_ROOT)                $out[] = 'ROOT';
        if ($flag & self::FLAG_KEYED_HASH)          $out[] = 'KEYED_HASH';
        if ($flag & self::FLAG_DERIVE_KEY_CONTEXT)  $out[] = 'DERIVE_KEY_CONTEXT';
        if ($flag & self::FLAG_DERIVE_KEY_MATERIAL) $out[] = 'DERIVE_KEY_MATERIAL';

        p('flags: ' . implode(' ', $out) . PHP_EOL);
    }
}
