<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

enum Strategy
{
    case HASH; // for hash and keyed_hash modes
    case DERIVE_KEY_CONTEXT;
    case DERIVE_KEY_MATERIAL;
}
