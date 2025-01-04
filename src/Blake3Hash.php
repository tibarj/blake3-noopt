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
    /** @var int[] */ private array $k; // 8 uint32 words
    private int $flag; // default flag

    /**
     * @throws InvalidArgumentException when
     *            - $strategy and $key are incompatible
     *            - non-null $key is not 32 bytes long
     */
    public function __construct(
        ?string $key = null,
        Strategy $strategy = Strategy::HASH,
    ) {
        if (null !== $key) {
            if ($strategy == Strategy::DERIVE_KEY_CONTEXT) {
                throw new InvalidArgumentException('$key and $strategy are incompatible');
            }
            $this->k = self::unpack($key, self::KEY_SIZE_WORD);
            $this->flag = Strategy::HASH == $strategy
                ? self::FLAG_KEYED_HASH
                : self::FLAG_DERIVE_KEY_MATERIAL;
        } else {
            if (
                $strategy == Strategy::DERIVE_KEY_MATERIAL
            ) {
                throw new InvalidArgumentException('$key and $strategy are incompatible');
            }
            $this->k = [
                self::IV0, self::IV1, self::IV2, self::IV3,
                self::IV4, self::IV5, self::IV6, self::IV7,
            ];
            $this->flag = Strategy::DERIVE_KEY_CONTEXT == $strategy
                ? self::FLAG_DERIVE_KEY_CONTEXT
                : 0;
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
            p("Loading cargo with $delta bytes");
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
        $cargo = self::getNodeCargo($this->root);

        $stream = $this->hash;
        $squeezed = strlen($stream); // bytes
        while ($squeezed < $bytes) {
            $output = $this->processTree(force: true);

            // package the root output
            $stream .= static::pack($output, self::OUTPUT_SIZE_WORD);
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

        $cargo = self::getNodeCargo($node);
        $chunkSize = strlen($cargo->getInput());
        $h = $this->k;
        $k = [self::IV0, self::IV1, self::IV2, self::IV3];
        $blockOffset = 0;
        $it = 0;
        while (true) {
            $nextBlockOffset = $blockOffset + self::BLOCK_SIZE_BYTE;
            $isLastBlock = $nextBlockOffset >= $chunkSize;
            $block = substr($cargo->getInput(), $blockOffset, self::BLOCK_SIZE_BYTE);
            $v = [
                ...$h, //v0..v7
                ...$k, // v8...v11
                $cargo->t0(), // v12
                $cargo->t1(), // v13
                $b = strlen($block), // v14, [0, 64] bytes
                $this->flag, // v15
            ];
            if ($node->isParent()) {
                $v[15] |= self::FLAG_PARENT;
            } else {
                if (!$blockOffset) {
                    $v[15] |= self::FLAG_CHUNK_START;
                }
                if ($isLastBlock) {
                    $v[15] |= self::FLAG_CHUNK_END;
                }
            }
            if ($isLastBlock) {
                if ($node->isRoot()) {
                    $v[15] |= self::FLAG_ROOT;
                }
                if ($b < self::BLOCK_SIZE_BYTE) {
                    p('Pad message with ' . (self::BLOCK_SIZE_BYTE - $b) . ' bytes');
                    $block = str_pad($block, self::BLOCK_SIZE_BYTE, chr(0));
                }
            }
            p("Compress Node $node, Block $it" . PHP_EOL);
            $vv = static::compress($block, $v);
            $it++;
            if ($isLastBlock) {
                p("Return output of node $node");
                return $vv;
            }
            $h = array_slice($vv, 0, self::CHAIN_SIZE_WORD);
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
            p("Traversing tree: $node");
            if ($node->parent && $node->parent->isEven() || $force) {
                $output = $this->processNode($node);
                if ($node->parent) {
                    p("Fill cargo input of node {$node->parent} from output of node $node");
                    self::getNodeCargo($node->parent)->ingest(
                        static::pack(
                            array_slice($output, 0, self::CHAIN_SIZE_WORD),
                            self::CHAIN_SIZE_WORD
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
        return new NodeCargo(self::CHUNK_SIZE_BYTE, $this->chunkIndex++);
    }

    /**
     * polymorphism + on demand (parent) NodeCargo instantiation
     */
    private static function getNodeCargo(BinaryNode $node): NodeCargo
    {
        return $node->cargo ??= new NodeCargo(self::BLOCK_SIZE_BYTE);
    }
}
