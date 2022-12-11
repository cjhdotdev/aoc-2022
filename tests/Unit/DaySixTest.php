<?php

namespace Tests\Unit;

use App\Domain\DaySix\Services\DaySixService;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DaySixService());

it('slides along a string in batches of 4 characters', function (string $input, array $expectedSlides) {
    expect($this->service->parseIntoBatches($input))
        ->toBeInstanceOf(Collection::class)
        ->toMatchArray($expectedSlides);
})->with([
    ['abcdefgh', [
        [1 => 'a', 2 => 'b', 3 => 'c', 4 => 'd'],
        [2 => 'b', 3 => 'c', 4 => 'd', 5 => 'e'],
        [3 => 'c', 4 => 'd', 5 => 'e', 6 => 'f'],
        [4 => 'd', 5 => 'e', 6 => 'f', 7 => 'g'],
        [5 => 'e', 6 => 'f', 7 => 'g', 8 => 'h'],
    ]],
]);

it('identifies a batch with completely unique letters', function (Collection $batch, bool $expectedMatch) {
    expect($this->service->hasUniqueCharacters($batch))
        ->toBeBool()
        ->toEqual($expectedMatch);
})->with([
    [collect(['a', 'b', 'c', 'd']), true],
    [collect(['a', 'a', 'b', 'c']), false],
]);

it('finds the position of the first set of four unique characters in a string', function (string $input, int $expectedPosition) {
    expect($this->service->findFirstUniqueBatch($input))
        ->toBeInt()
        ->toEqual($expectedPosition);
})->with([
    ['aabbccdeffgghh', 9],
    ['abcddeeffgghhii', 4],
    ['ababcacdadefghf', 12],
]);
