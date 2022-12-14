<?php

namespace Tests\Unit;

use App\Domain\Day08\Collections\TreeSquareCollection;
use App\Domain\Day08\Services\DayEightService;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DayEightService());

it('parses a tree string into a collection', function () {
    expect($this->service->parseTreeString('123456789'))
        ->toBeInstanceOf(Collection::class)
        ->toMatchArray([1, 2, 3, 4, 5, 6, 7, 8, 9]);
});

it('parses a tree square into collections', function () {
    expect($this->service->parseTreeSquare("1234\n5678\n9012\n3456"))
        ->toBeInstanceOf(TreeSquareCollection::class)
        ->toMatchArray([[1, 2, 3, 4], [5, 6, 7, 8], [9, 0, 1, 2], [3, 4, 5, 6]]);
});

it('can determine if a tree is visible given x and y co-ordinates', function (
    string $treeSquare,
    int $positionX,
    int $positionY,
    bool $expectedVisibility,
) {
    $square = $this->service->parseTreeSquare($treeSquare);
    expect($square->isVisible($positionX, $positionY))
        ->toBeBool()
        ->toEqual($expectedVisibility);
})->with([
    ["1111\n1291\n1921\n1111", 2, 1, true],
    ["9999\n9119\n9119\n9999", 2, 1, false],
    ["8888\n8998\n8998\n8998", 0, 0, true],
    ["1911\n9119\n1111\n1911", 1, 1, false],
    ["0000\n0000\n0000\n0000", 1, 1, false],
]);

it('counts the number of visible trees in a tree square', function (
    string $treeSquare,
    int $expectedVisibleTrees,
) {
    $square = $this->service->parseTreeSquare($treeSquare);
    expect($square->countVisible())
        ->toBeInt()
        ->toEqual($expectedVisibleTrees);
})->with([
    ["9999\n9119\n9119\n9999", 12],
    ["1111\n1991\n1991\n1111", 16],
    ["9199\n9219\n9119\n9999", 13],
]);

it('calculates the scenic score for a given tree', function (
    string $treeSquare,
    int $positionX,
    int $positionY,
    int $expectedScore,
) {
    $square = $this->service->parseTreeSquare($treeSquare);
    expect($square->calculateScenicScore($positionX, $positionY))
        ->toBeInt()
        ->toEqual($expectedScore);
})->with([
    ["30373\n25512\n65332\n33549\n35390", 2, 1, 4],
    ["30373\n25512\n65332\n33549\n35390", 2, 3, 8],
]);

it('finds the best scenic score in a tree square', function (
    string $treeSquare,
    int $expectedScore,
) {
    $square = $this->service->parseTreeSquare($treeSquare);
    expect($square->findBestScore())
        ->toBeInt()
        ->toEqual($expectedScore);
})->with([
    ["30373\n25512\n65332\n33549\n35390", 8],
]);
