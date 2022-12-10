<?php

namespace Tests\Unit;

use App\Domain\DayThree\DTO\PackagingDTO;
use App\Domain\DayThree\Services\DayThreeService;
use Illuminate\Support\Collection;

beforeEach(fn() => $this->service = new DayThreeService());

it('splits the packing list into equal parts', function () {
    expect($this->service->splitPackagingList('abcdefzyxwvu'))
        ->toBeInstanceOf(PackagingDTO::class)
        ->compartmentOne->toBeInstanceOf(Collection::class)->toMatchArray(['a', 'b', 'c', 'd', 'e', 'f'])
        ->compartmentTwo->toBeInstanceOf(Collection::class)->toMatchArray(['z', 'y', 'x', 'w', 'v', 'u']);
});

it('finds items duplicated in each compartment', function () {
    $packagingDTO = new PackagingDTO(
        collect(['a', 'b', 'c', 'd', 'e', 'f']),
        collect(['z', 'y', 'x', 'c', 'v', 'w'])
    );
    expect($this->service->findDuplicatedItem($packagingDTO))
        ->toBeString()
        ->toHaveLength(1)
        ->toEqual('c');
})->with([
    [collect(['a', 'b', 'c', 'd']), collect(['d', 'z', 'y', 'x']), 'd'],
    [collect(['G', 'R', 'U', 'B']), collect(['F', 'R', 'E', 'E']), 'R'],
]);

it('calculates the priority of an item', function (
    string $item,
    int $expectedPriority,
) {
    expect($this->service->findPriorityForItem($item))
        ->toBeInt()
        ->toEqual($expectedPriority);
})->with([
    ['a', 1],
    ['p', 16],
    ['z', 26],
    ['A', 27],
    ['L', 38],
    ['Z', 52],
]);

it('calculates priorities for multiple rucksacks', function () {
    $rucksacks = "abczbx\nTHEEND\nHELLOWORLD\ntEsTiNgPinGeRS";
    expect($this->service->calculatePriorityForRucksacks($rucksacks))
        ->toBeInt()
        ->toEqual(80);
});

it('gets the rucksacks in batches of three', function () {
    $rucksacks = "abc\ndef\nghi\njkl\nmno\npqr\nstu\nvwx\nyza";
    $groups = $this->service->splitRucksacksIntoGroups($rucksacks);
    expect($groups)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(3)
        ->toMatchArray([
            ['abc', 'def', 'ghi'],
            ['jkl', 'mno', 'pqr'],
            ['stu', 'vwx', 'yza'],
        ]);
});

it('finds the common item amongst a group of rucksacks', function (
    Collection $group,
    string $expectedItem
) {
    expect($this->service->findCommonItemInGroup($group))
        ->toBeString()
        ->toEqual($expectedItem);
})->with([
    [collect(['abc', 'daf', 'gha']), 'a'],
    [collect(['qErFdVgHPkFsc', 'VRSQlOBfE', 'FSVThqAXoK']), 'V'],
]);

it('calculates the priority for groups of rucksacks', function () {
    expect($this->service->calculatePriorityForGroups("abc\ndag\ntra\nsZrTGbd\nlPtgZBD\nStgZeQdk"))
        ->toBeInt()
        ->toEqual(53);
});
