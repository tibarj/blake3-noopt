<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

use OverflowException;

class NodeCargo implements NodeCargoInterface
{
    private int $t0, $t1;
    private int $inputSize = 0;
    private string $input = '';

    /**
     * @param ?string $input Constaint of size is given by
     *                  chunk:  <= 1024 bytes | 256 uint32
     *                  parent: ==   64 bytes |  16 uint32
     * @param ?int[] $output Always  64 bytes |  16 uint32
     */
    public function __construct(
        private int $capacity,
        private int $t = 0,
    ) {
        p(__METHOD__ . " with capacity of $capacity and t=$t");

        $this->updateCounter();
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function ingest(string $payload): void
    {
        $size = strlen($payload);

        p(__METHOD__ . " $size bytes at cargo offset {$this->inputSize}");
        p('payload:' . PHP_EOL . $payload . PHP_EOL);

        if ($size > $this->capacity) {
            throw new OverflowException(
                "Cannot ingest $size with only "
                . ($this->capacity - $this->inputSize) . ' remaining'
            );
        }

        $this->input .= $payload;
        $this->inputSize += $size;
    }

    public function getRemainingCapacity(): int
    {
        return $this->capacity - $this->inputSize;
    }

    public function t(): int
    {
        return $this->t;
    }

    public function t0(): int
    {
        return $this->t0;
    }

    public function t1(): int
    {
        return $this->t1;
    }

    public function incrementCounter(): void
    {
        $this->t++;
        $this->updateCounter();
    }

    private function updateCounter(): void
    {
        $this->t0 = $this->t & 0x00000000ffffffff; //  low order (uint32)
        $this->t1 = $this->t & 0xffffffff00000000; // high order (uint32)
    }
}
