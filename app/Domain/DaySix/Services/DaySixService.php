<?php

namespace App\Domain\DaySix\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DaySixService
{
    public function findFirstUniqueBatch(string $input): int
    {
        /** @var Collection<int, string> $firstUniqueBatch */
        $firstUniqueBatch = $this
            ->parseIntoBatches($input)
            ->skipUntil(fn ($batch) => $this->hasUniqueCharacters($batch))
            ->first();

        return intval(
            $firstUniqueBatch
                ->keys()
                ->last()
        );
    }

    /**
     * @param  string  $input
     * @return Collection<int, Collection>
     */
    public function parseIntoBatches(string $input): Collection
    {
        return Str::of($input)
            ->split('//')
            ->filter()
            ->sliding(4);
    }

    /**
     * @param  Collection<int, string>  $batch
     * @return bool
     */
    public function hasUniqueCharacters(Collection $batch): bool
    {
        return $batch->unique()->count() === $batch->count();
    }
}
