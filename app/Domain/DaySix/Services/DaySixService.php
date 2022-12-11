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

    public function findFirstUniqueMessageBatch(string $input): int
    {
        /** @var Collection<int, string> $firstUniqueBatch */
        $firstUniqueBatch = $this
            ->parseIntoMessageBatches($input)
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
     * @param  string  $input
     * @return Collection<int, Collection>
     */
    public function parseIntoMessageBatches(string $input): Collection
    {
        return Str::of($input)
            ->split('//')
            ->filter()
            ->sliding(14);
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
