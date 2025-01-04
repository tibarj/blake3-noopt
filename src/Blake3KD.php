<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

class Blake3KD
{
    private Blake3Hash $blake3;

    public function __construct()
    {
        $this->blake3 = new Blake3Hash(strategy: Strategy::DERIVE_KEY_CONTEXT);
    }

    public function absorb(string $input): static
    {
        $this->blake3->absorb($input);

        return $this;
    }
    /**
     * @return string Hash stream packet of $bytes bytes
     */
    public function squeeze(int $bytes = Blake3Hash::DIGEST_SIZE_BYTE): string
    {
        return $this->blake3->squeeze($bytes);
    }

    public function ratchet(): static
    {
        p(__METHOD__);

        $key = $this->blake3->squeeze(Blake3Hash::KEY_SIZE_BYTE);
        $this->blake3 = new Blake3Hash($key, Strategy::DERIVE_KEY_MATERIAL);

        return $this;
    }
}
