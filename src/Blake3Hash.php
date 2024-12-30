<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

use Exception;
use InvalidArgumentException;
use LogicException;

class Blake3Hash extends AbstractBlake3
{
    private ?BinaryNode $root = null;
    private ?NodeCargo $cargo = null; // pending cargo
    private ?int $chunkIndex = 0;
    private ?int $nodeIndex = 0;
    private int $absorbed = 0; // bytes
    private int $squeezed = 0; // bytes
    private string $hash = '';
    /** @var int[] */ private readonly array $k; // 16 uint32 words
    private readonly int $flag; // default flag

    /**
     * @throws InvalidArgumentException when $key is not 32 bytes long
     */
    public function __construct(?string $key = null)
    {
        if (null !== $key) {
            if (strlen($key) != static::KEY_SIZE_BYTE) {
                throw new InvalidArgumentException(
                    'Key is not ' . static::KEY_SIZE_BYTE . ' bytes long'
                );
            }
            $this->flag = static::FLAG_KEYED_HASH;
            $this->k = static::unpack($key, static::KEY_SIZE_WORD);
        } else {
            $this->flag = 0;
            $this->k = [
                static::IV0, static::IV1, static::IV2, static::IV3,
                static::IV4, static::IV5, static::IV6, static::IV7,
            ];
        }
    }

    public function absorb(string $input): static
    {
        p(__METHOD__ . ' ' . strlen($input) . ' bytes');

        if ($this->squeezed) {
            throw new Exception('Cannot call absorb after squeeze');
        }
        if (!strlen($input)) {
            return $this;
        }

        while (strlen($input)) {
            p(strlen($input) . ' bytes remaining');
            if (!$this->cargo) {
                $this->cargo = $this->createChunkCargo();
            }
            $remaining = $this->cargo->getRemainingCapacity();
            $delta = min($remaining, strlen($input));
            p("loading cargo with $delta bytes");
            $packet = substr($input, 0, $delta);
            $input = substr($input, $delta);
            $this->cargo->ingest($packet);
            $this->absorbed += $delta;

            if (!$this->cargo->getRemainingCapacity()) {
                $this->shipCargo();
                $this->processTree();
            }
        }

        return $this;
    }

    /**
     * @return string Hash stream packet of $bytes bytes
     */
    public function squeeze(int $bytes = self::DIGEST_SIZE_BYTE): string
    {
        p(__METHOD__);

        // process the last chunk
        if (!$this->root && !$this->cargo) {
            $this->cargo = $this->createChunkCargo();
        }
        if ($this->cargo) {
            $this->shipCargo();
        }
        $cargo = static::getNodeCargo($this->root);

        $stream = $this->hash;
        $squeezed = strlen($stream); // bytes
        while ($squeezed < $bytes) {
            $output = $this->processTree(force: true);

            // package the root output
            $stream .= static::pack($output, static::OUTPUT_SIZE_WORD);
            $cargo->incrementCounter();
            $squeezed += self::BLOCK_SIZE_BYTE;
            $this->squeezed += self::BLOCK_SIZE_BYTE;
        }

        $packet = substr($stream, 0, $bytes);
        $this->hash = substr($stream, $bytes);

        return $packet;
    }

    /**
      * @param BinaryNode $node with [0, 1024] bytes chunk
      * @return int[] chaining values as 16 uint32
     */
    private function processNode(BinaryNode $node): array
    {
        p(__METHOD__ . ' ' . $node);

        $cargo = static::getNodeCargo($node);
        $chunkSize = strlen($cargo->getInput());
        $h = $this->k;
        $k = [static::IV0, static::IV1, static::IV2, static::IV3];
        $blockOffset = 0;
        $it = 0;
        while (true) {
            $nextBlockOffset = $blockOffset + static::BLOCK_SIZE_BYTE;
            $isLastBlock = $nextBlockOffset >= $chunkSize;
            $block = substr($cargo->getInput(), $blockOffset, static::BLOCK_SIZE_BYTE);
            $v = [
                ...$h, //v0..v7
                ...$k, // v8...v11
                $cargo->t0(), // v12
                $cargo->t1(), // v13
                $b = strlen($block), // v14, [0, 64] bytes
                $this->flag, // v15
            ];
            if ($node->isParent()) {
                $v[15] |= static::FLAG_PARENT;
            } else {
                if (!$blockOffset) {
                    $v[15] |= static::FLAG_CHUNK_START;
                }
                if ($isLastBlock) {
                    $v[15] |= static::FLAG_CHUNK_END;
                }
            }
            if ($isLastBlock) {
                if ($node->isRoot()) {
                    $v[15] |= static::FLAG_ROOT;
                }
                if ($b < static::BLOCK_SIZE_BYTE) {
                    p('pad message with ' . (static::BLOCK_SIZE_BYTE - $b) . ' bytes');
                    $block = str_pad($block, static::BLOCK_SIZE_BYTE, chr(0));
                }
            }
            p("Compress Node $node, Block $it" . PHP_EOL);
            $vv = static::compress($block, $v);
            $it++;
            if ($isLastBlock) {
                p("fill cargo output of node $node");
                return $vv;
            }
            $h = array_slice($vv, 0, static::CHAIN_SIZE_WORD);
            $blockOffset = $nextBlockOffset;
        }
        throw new LogicException('Process failed');
    }

    /**
     * Ship cargo into a new node
     */
    private function shipCargo(): void
    {
        p(__METHOD__);

        $this->root = $this->root
            ? $this->root->grow($this->cargo, $this->nodeIndex)
            : new BinaryNode($this->nodeIndex++, $this->cargo);
        $this->cargo = null;
    }

    /**
     * @return ?int[] root node output as 16 uint32
     */
    private function processTree($force = false): ?array
    {
        p(__METHOD__);

        $result = null;
        $trash = [];
        foreach ($this->root->traverse() as $node) {
            p('traversing ' . $node);
            if ($node->parent && $node->parent->isEven() || $force) {
                $output = $this->processNode($node);
                if ($node->parent) {
                    p("fill cargo input of node {$node->parent} from output of node $node");
                    static::getNodeCargo($node->parent)->ingest(
                        static::pack(
                            array_slice($output, 0, static::CHAIN_SIZE_WORD),
                            static::CHAIN_SIZE_WORD
                        )
                    );
                    $trash[] = $node;
                } else {
                    $result = $output;
                }
            }
        }
        foreach ($trash as $node) {
            $node->destroy();
        }

        return $result;
    }

    private function createChunkCargo(): NodeCargo
    {
        return new NodeCargo(static::CHUNK_SIZE_BYTE, $this->chunkIndex++);
    }

    /**
     * polymorphism + on demand (parent) NodeCargo instantiation
     */
    private static function getNodeCargo(BinaryNode $node): NodeCargo
    {
        return $node->cargo ??= new NodeCargo(static::BLOCK_SIZE_BYTE);
    }
}
