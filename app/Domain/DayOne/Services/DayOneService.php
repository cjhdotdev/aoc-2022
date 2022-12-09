<?php

namespace App\Domain\DayOne\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayOneService
{
    public function findHighest(string $elfCalories, int $positionsToCount = 1): int
    {
        return intval($this->calculateMultipleElves($elfCalories)
            ->sortDesc()
            ->take($positionsToCount)
            ->sum());
    }

    public function calculateForElf(string $elfCalories): int
    {
        return intval(Str::of($elfCalories)
            ->explode(PHP_EOL)
            ->map(fn ($value) => intval($value))
            ->sum());
    }

    /**
     * @param  string  $multipleElfCalories
     * @return Collection<int, int>
     */
    public function calculateMultipleElves(string $multipleElfCalories): Collection
    {
        return Str::of($multipleElfCalories)
            ->explode(PHP_EOL.PHP_EOL)
            ->map(fn ($elfCalories) => $this->calculateForElf(strval($elfCalories)));
    }
}
