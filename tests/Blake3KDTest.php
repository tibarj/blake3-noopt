<?php

// phpunit --testdox tests --filter Blake3KD

declare(strict_types=1);

namespace Tibarj\Blake3Noopt\tests;

use PHPUnit\Framework\TestCase;
use Tibarj\Blake3Noopt\Blake3KD;

final class Blake3KDTest extends TestCase
{
    function data(): array
    {
        return [
            // #0
            [
                [''], // key context
                [''], // key material
                ['741011989511e0d6b52532320d9edb6c0def0ab7e832b99bcc1259591ce2d75b'], // expected
            ],
            // #1
            [
                ['vR9QU80DvJYl3HEOiVczu'], // key context
                [''], // key material
                ['bead6ededd97eedf228e93128d906e7fbc0f88777edd89677eefa7979bed7ed1'], // expected
            ],
            // #2
            [
                ['vR9QU80DvJYl3HEOiVczu'], // key context
                ['2mzE5NpCivH0ebEWn7WXG'], // key material
                ['5e91bc119c9f63f61601fd3a4bebf87515eaf227c5da70f4911ce8e09205fb30'], // expected
            ],
        ];
    }

    /**
     * @dataProvider data
     */
    function test(array $context, array $material, array $expected)
    {
        $blake3 = new Blake3KD();
        foreach ($context as $packet) {
            $blake3->absorb($packet);
        }
        $blake3->ratchet();
        foreach ($material as $packet) {
            $blake3->absorb($packet);
        }

        foreach ($expected as $packet) {
            $hash = $blake3->squeeze(strlen($packet) / 2);
            $this->assertEquals($packet, bin2hex($hash));
        }
    }
}
