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

    public function findBestScore(): int
    {
        return $this->reduce(
            fn ($score, $row, $y) => (($newScore = intval(
                $row->reduce(
                    fn ($rowScore, $column, $x) => (($newRowScore = $this->calculateScenicScore($x, $y)) > $rowScore ? $newRowScore : $rowScore)
                )
            )) > $score ? $newScore : $score)
        );
    }

    public function calculateScenicScore(int $x, int $y): int
    {
        return $this->calculateScenicScoreFrom($this->getRow($y), $x) * $this->calculateScenicScoreFrom($this->getColumn($x), $y);
    }

    public function calculateScenicScoreFrom(Collection $treeList, int $position): int
    {
        $treeHeight = $treeList->get($position);
        [$treesBefore, $treesAfter] = $this->getTreesBeforeAfter($treeList, $position);

        $scoreCalculator = fn ($state, $tree) => [
            'under' => $state['under'] && $tree < $treeHeight,
            'score' => $state['under'] ? $state['score'] + 1 : $state['score'],
        ];
        $initialScore = ['under' => true, 'score' => 0];

        $beforeCalc = $treesBefore
            ->reverse()
            ->reduce($scoreCalculator, $initialScore);

        $afterCalc = $treesAfter
            ->reduce($scoreCalculator, $initialScore);

        return $beforeCalc['score'] * $afterCalc['score'];
    }

    private function isVisibleIn(Collection $treeList, int $position): bool
    {
        $treeHeight = $treeList->get($position);
        [$treesBefore, $treesAfter] = $this->getTreesBeforeAfter($treeList, $position);

        return
            $treesBefore->isEmpty()
            || $treesAfter->isEmpty()
            || $treesBefore->max() < $treeHeight
            || $treesAfter->max() < $treeHeight;
    }

    /**
     * @param  Collection  $treeList
     * @param  int  $position
     * @return array<int, Collection>
     */
    private function getTreesBeforeAfter(Collection $treeList, int $position): array
    {
        return [
            $treeList->slice(0, $position)->values(),
            $treeList->slice($position + 1, $treeList->count() - $position - 1)->values(),
        ];
    }

    private function getRow(int $y): Collection
    {
        return $this->get($y) ?? new Collection();
    }

    private function getColumn(int $x): Collection
    {
        return $this
            ->map(fn ($row) => $row->get($x))
            ->flatten()
            ->values();
    }
}
