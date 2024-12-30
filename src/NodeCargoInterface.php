<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

interface NodeCargoInterface
{
    public function getRemainingCapacity(): int;
    public function ingest(string $payload): void;
}
