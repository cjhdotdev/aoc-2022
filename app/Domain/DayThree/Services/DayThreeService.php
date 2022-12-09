<?php

namespace App\Domain\DayThree\Services;

use App\Domain\DayThree\DTO\PackagingDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayThreeService
{
    public function calculatePriorityForRucksacks(string $rucksacks): int
    {
        return intval(
            Str::of($rucksacks)
                ->explode(PHP_EOL)
                ->map(fn ($compartments) => $this->splitPackagingList(strval($compartments)))
                ->map(fn ($packagingList) => $this->findDuplicatedItem($packagingList))
                ->reduce(fn ($sum, $item) => $sum + $this->findPriorityForItem($item), 0)
        );
    }

    public function calculatePriorityForGroups(string $rucksacks): int
    {
        return intval(
            $this->splitRucksacksIntoGroups($rucksacks)
                 ->map(fn ($group) => $this->findCommonItemInGroup($group))
                 ->reduce(fn ($priority, $badge) => $priority + $this->findPriorityForItem($badge))
        );
    }

    public function splitPackagingList(string $packagingList): PackagingDTO
    {
        $packagingLength = Str::of($packagingList)->length();

        return new PackagingDTO(
            $this->splitCompartmentList(Str::of($packagingList)->substr(0, $packagingLength / 2)),
            $this->splitCompartmentList(Str::of($packagingList)->substr($packagingLength / 2)),
        );
    }

    /**
     * @param  string  $compartmentList
     * @return Collection<int, string>
     */
    public function splitCompartmentList(string $compartmentList): Collection
    {
        return Str::of($compartmentList)
            ->split('//')
            ->filter()
            ->map(fn ($list) => strval($list))
            ->values();
    }

    /**
     * @param  string  $rucksacks
     * @return Collection<int, Collection<int, string>>
     */
    public function splitRucksacksIntoGroups(string $rucksacks): Collection
    {
        return Str::of($rucksacks)
            ->explode(PHP_EOL)
            ->reduce(function ($groups, $rucksack) {
                if ($groups->last()->count() === 3) {
                    $groups->push(new Collection());
                }
                $groups->last()->push($rucksack);

                return $groups;
            }, (new Collection())->push(new Collection()));
    }

    public function findPriorityForItem(string $item): int
    {
        $char = ord($item);

        return match (true) {
            $char >= 65 && $char <= 90 => (($char) - 64) + 26,
            $char >= 97 && $char <= 122 => ($char - 96),
            default => 0,
        };
    }

    public function findDuplicatedItem(PackagingDTO $packagingDTO): string
    {
        return strval(
            $packagingDTO
                ->compartmentOne
                ->intersect($packagingDTO->compartmentTwo)
                ->first()
        );
    }

    /**
     * @param  Collection<int, string>  $group
     * @return string
     */
    public function findCommonItemInGroup(Collection $group): string
    {
        return strval(
            $group
                ->map(fn ($rucksack) => Str::of($rucksack)->split('//')->filter()->unique())
                ->reduce(function ($existingItems, $rucksackItems) {
                    $rucksackItems->each(
                        fn ($item) => $existingItems->get($item)
                            ? $existingItems->put($item, $existingItems->get($item) + 1)
                            : $existingItems->put($item, 1)
                    );

                    return $existingItems;
                }, new Collection())
                ->filter(fn ($count) => $count === 3)
                ->map(fn ($item, $key) => $key)
                ->first()
        );
    }
}
