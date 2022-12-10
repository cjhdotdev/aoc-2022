<?php

namespace App\Domain\DayFive\Collections;

use App\Domain\DayFive\DTO\MovementDTO;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, Collection>
 */
class ColumnsCollection extends Collection
{
    /**
     * @param  Collection<int, string>  $row
     * @return $this
     */
    public function addRow(Collection $row): self
    {
        $row
            ->filter(fn ($item) => $item !== ' ')
            ->each(fn ($item, $key) => $this->get($key) ? $this->get($key)->push($item) : $this->put($key, collect([$item])));

        return $this;
    }

    public function transformWithMovement(MovementDTO $movement): self
    {
        /** @var Collection<int, Collection<int, string>> $cratesToMove */
        $cratesToMove = $this->get($movement->fromColumn - 1);
        if ($cratesToMove->isEmpty()) {
            return $this;
        }

        $cratesToMove = $cratesToMove->shift($movement->count);
        if (! $cratesToMove instanceof Collection) {
            $cratesToMove = collect([$cratesToMove]);
        }

        if (! $movement->multiCrateCrane) {
            $cratesToMove = $cratesToMove->reverse();
        }

        /** @var Collection<int, Collection<int, string>> $existingColumn */
        $existingColumn = $this->get($movement->toColumn - 1);

        $this->put(
            $movement->toColumn - 1,
            $cratesToMove
                ->concat($existingColumn)
                ->values()
        );

        return $this;
    }

    /**
     * @return Collection<int, string>
     */
    public function findTopCrates(): Collection
    {
        return $this
            ->sortKeys()
            ->map(fn ($column) => $column->first());
    }
}
