<?php

namespace App\Domain\Day08\Collections;

use Illuminate\Support\Collection;

/**
 * @extends Collection<int, Collection>
 */
class TreeSquareCollection extends Collection
{
    public function isVisible(int $x, int $y): bool
    {
        return
            $this->isVisibleIn($this->getRow($y), $x)
            || $this->isVisibleIn($this->getColumn($x), $y);
    }

    public function countVisible(): int
    {
        return $this->reduce(
            fn ($count, $row, $y) => intval($count) + intval(
                $row->reduce(
                    fn ($rowCount, $column, $x) => $rowCount + ($this->isVisible($x, $y) ? 1 : 0)
                )
            )
        );
    }

    private function isVisibleIn(Collection $treeList, int $position): bool
    {
        $treesBefore = $treeList->slice(0, $position)->values();
        $treesAfter = $treeList->slice($position + 1, $treeList->count() - $position - 1)->values();
        $treeHeight = $treeList->get($position);

        return
            $treesBefore->isEmpty()
            || $treesAfter->isEmpty()
            || $treesBefore->max() < $treeHeight
            || $treesAfter->max() < $treeHeight;
    }

    public function getRow(int $y): Collection
    {
        return $this->get($y) ?? new Collection();
    }

    public function getColumn(int $x): Collection
    {
        return $this
            ->map(fn ($row) => $row->get($x))
            ->flatten()
            ->values();
    }
}
