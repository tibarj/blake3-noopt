<?php

declare(strict_types=1);

namespace Tibarj\Blake3Noopt;

use Generator;
use InvalidArgumentException;
use LogicException;

class BinaryNode
{
    public ?self $parent = null;
    public int $weight = 1;

    /**
     * @throws InvalidArgumentException when $l and $r are not paired
     */
    public function __construct(
        public readonly int $index, // node counter
        public ?NodeCargoInterface $cargo = null,
        public ?self $l = null,
        public ?self $r = null,
    ) {
        if ((bool) $l ^ (bool) $r) {
            throw new InvalidArgumentException('(bool) $l ^ (bool) $r');
        }
        if ($l) {
            $l->parent = $this;
            $r->parent = $this;
            $this->updateWeight();
        }
        p(__METHOD__ . " $this " . ($l ? "above $l and $r" : 'as leaf'));
    }

    public function __toString(): string
    {
        return "Node #{$this->index} of weight {$this->weight}";
    }

    public function destroy(): void
    {
        p(__METHOD__ . ' ' . $this);

        if ($this->isRoot()) {
            throw new LogicException('Cannot destroy a root node');
        }
        $this->parent->{$this->isRight() ? 'r' : 'l'} = null;
        $this->parent = null;
    }

    /**
     * @return BinaryNode root
     */
    public function grow(NodeCargoInterface $cargo, int &$index): BinaryNode
    {
        p(__METHOD__ .' tree of current weight '.$this->getRoot()->weight);

        $rightest = $this->getRightest();

        // climb the tree unless the node is out of balance
        $sub = $rightest;
        while ($sub->parent && $sub->parent->isBalanced()) {
            $sub = $sub->parent;
        }
        $super = $sub->parent;

        // pile up the parent
        $leaf = new BinaryNode($index++, $cargo);
        $guest = new BinaryNode($index++, l: $sub, r: $leaf);
        if ($super) {
            // rewire the parent with the target parent
            $super->hang($guest);
        }

        return $guest->getRoot();
    }

    public function hang(BinaryNode $child): void
    {
        p(__METHOD__ . " $child under $this");

        $this->r = $child;
        $child->parent = $this;
        $this->updateWeight();
    }

    /**
     * @return BinaryNode[]
     */
    public function traverse(): Generator
    {
        p(__METHOD__ . " from $this");

        $node = $this;
        $route = [];
        while ($node) {
            $route[] = $node->index;
            if ($node->l && !in_array($node->l->index, $route)) {
                $node = $node->l;
                continue;
            }
            if ($node->r && !in_array($node->r->index, $route)) {
                $node = $node->r;
                continue;
            }
            p("Yield $node");
            yield $node; // yielding only when moving up
            $node = $node->parent;
        }
    }

    public function isRoot(): bool
    {
        return !$this->parent;
    }

    public function isParent(): bool
    {
        return 1 != $this->weight;
    }

    public function isRight(): bool
    {
        return $this->parent && $this === $this->parent->r;
    }

    public function getRoot(): BinaryNode
    {
        $root = $this;
        while ($root->parent) {
            $root = $root->parent;
        }

        return $root;
    }

    public function isBalanced(): bool
    {
        return $this->l
            ? $this->l->weight == $this->r->weight
            : false; // no children is considered unbalanced
    }

    /**
     * @return BinaryNode leaf|parent node
     */
    private function getRightest(): BinaryNode
    {
        $node = $this->getRoot();
        while ($node->r) {
            $node = $node->r;
        }

        return $node;
    }

    private function updateWeight(): void
    {
        $this->weight = $this->l->weight + $this->r->weight + 1;
    }
}
