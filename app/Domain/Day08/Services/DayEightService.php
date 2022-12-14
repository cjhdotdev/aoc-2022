<?php

namespace App\Domain\Day08\Services;

use App\Domain\Day08\Collections\TreeSquareCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayEightService
{
    public function calculateVisibleTreesFromSquare(string $treeSquare): int
    {
        return $this
            ->parseTreeSquare($treeSquare)
            ->countVisible();
    }

    /**
     * @param  string  $trees
     * @return Collection<int, int>
     */
    public function parseTreeString(string $trees): Collection
    {
        return Str::of($trees)
            ->split('//')
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->map(fn ($tree) => intval($tree));
    }

    /**
     * @param  string  $treeSquare
     * @return TreeSquareCollection<int, Collection<int, int>>
     */
    public function parseTreeSquare(string $treeSquare): TreeSquareCollection
    {
        return Str::of($treeSquare)
            ->explode(PHP_EOL)
            ->filter()
            ->map(fn ($treeLine) => $this->parseTreeString(strval($treeLine)))
            ->pipe(fn ($treeSquare) => new TreeSquareCollection($treeSquare));
    }
}
