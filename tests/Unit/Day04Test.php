<?php

namespace Tests\Unit;

use App\Domain\Day04\DTO\LeftRightRangesDTO;
use App\Domain\Day04\DTO\SectionRangeDTO;
use App\Domain\Day04\Services\DayFourService;

beforeEach(fn () => $this->service = new DayFourService());

it('splits a string into the correct start and end sections', function (
    string $range,
    int $expectedStartSection,
    int $expectedEndSection,
) {
    expect($this->service->splitRanges($range))
        ->toBeInstanceOf(SectionRangeDTO::class)
        ->startSection->toBeInt()->toEqual($expectedStartSection)
        ->endSection->toBeInt()->toEqual($expectedEndSection);
})->with([
    ['1-1', 1, 1],
    ['99-11', 99, 11],
    ['123-456', 123, 456],
]);

it('can detect a range wholly inside another range', function (
    int $containerStart,
    int $containerEnd,
    int $testStart,
    int $testEnd,
    bool $expectedOutcome,
) {
    $rangeContainer = new SectionRangeDTO($containerStart, $containerEnd);
    $rangeTesting = new SectionRangeDTO($testStart, $testEnd);
    expect($rangeContainer->contains($rangeTesting))
        ->toBeBool()
        ->toEqual($expectedOutcome);
})->with([
    [1, 10, 5, 8, true],
    [1, 10, 8, 12, false],
    [5, 10, 1, 6, false],
    [5, 10, 5, 5, true],
    [8, 15, 15, 15, true],
    [10, 20, 12, 12, true],
    [30, 80, 90, 95, false],
]);

it('finds the biggest range of two given ranges', function (
    int $leftStart,
    int $leftEnd,
    int $rightStart,
    int $rightEnd,
    int $expectedStart,
    int $expectedEnd,
) {
    $rangeLeft = new SectionRangeDTO($leftStart, $leftEnd);
    $rangeRight = new SectionRangeDTO($rightStart, $rightEnd);
    expect($this->service->findBiggestOf($rangeLeft, $rangeRight))
        ->toBeInstanceOf(SectionRangeDTO::class)
        ->startSection->toBeInt()->toEqual($expectedStart)
        ->endSection->toBeInt()->toEqual($expectedEnd);
})->with([
    [1, 10, 2, 9, 1, 10],
    [2, 9, 1, 10, 1, 10],
    [2, 2, 3, 3, 3, 3],
]);

it('finds the smallest range of two given ranges', function (
    int $leftStart,
    int $leftEnd,
    int $rightStart,
    int $rightEnd,
    int $expectedStart,
    int $expectedEnd,
) {
    $rangeLeft = new SectionRangeDTO($leftStart, $leftEnd);
    $rangeRight = new SectionRangeDTO($rightStart, $rightEnd);
    expect($this->service->findSmallestOf($rangeLeft, $rangeRight))
        ->toBeInstanceOf(SectionRangeDTO::class)
        ->startSection->toBeInt()->toEqual($expectedStart)
        ->endSection->toBeInt()->toEqual($expectedEnd);
})->with([
    [1, 10, 2, 9, 2, 9],
    [2, 9, 1, 10, 2, 9],
    [2, 2, 3, 3, 2, 2],
]);

it('splits two ranges into separate instances', function (
    string $ranges,
    int $expectedContainerStart,
    int $expectedContainerEnd,
    int $expectedRangeStart,
    int $expectedRangeEnd,
) {
    expect($this->service->splitMultipleRanges($ranges))
        ->toBeInstanceOf(LeftRightRangesDTO::class)
        ->leftRange->toBeInstanceOf(SectionRangeDTO::class)
        ->leftRange->startSection->toEqual($expectedContainerStart)
        ->leftRange->endSection->toEqual($expectedContainerEnd)
        ->rightRange->toBeInstanceOf(SectionRangeDTO::class)
        ->rightRange->startSection->toEqual($expectedRangeStart)
        ->rightRange->endSection->toEqual($expectedRangeEnd);
})->with([
    ['1-10,10-20', 1, 10, 10, 20],
    ['2-5,9-15', 2, 5, 9, 15],
]);

it('takes a list of ranges and calculates how many are contained', function (
    string $ranges,
    int $expectedCount,
) {
    expect($this->service->calculateContainedRanges($ranges))
        ->toBeInt()
        ->toEqual($expectedCount);
})->with([
    ['1-10,20-30', 0],
    ['2-5,3-4', 1],
    ["10-50,51-100\n20-30,25-26", 1],
    ["1-10,2-3\n11-20,12-12\n21-30,40-50", 2],
    ["1-10,2-9\n10-20,15-16\n30-40,35-41\n45-46,42-48\n100-200,175-275", 3],
    ['1-1,1-1', 1],
    ['1-1,2-2', 0],
    ["1-3,2-4\n4-6,5-7", 0],
]);

it('finds ranges with partial overlaps', function (
    int $leftStart,
    int $leftEnd,
    int $rightStart,
    int $rightEnd,
    int $expectedOutcome,
) {
    $rangeLeft = new SectionRangeDTO($leftStart, $leftEnd);
    $rangeRight = new SectionRangeDTO($rightStart, $rightEnd);
    expect($rangeLeft->overlaps($rangeRight))
        ->toBeBool()
        ->toEqual($expectedOutcome);
})->with([
    [1, 10, 2, 9, true],
    [4, 6, 5, 7, true],
    [10, 20, 20, 21, true],
    [30, 40, 41, 50, false],
    [51, 51, 51, 51, true],
    [52, 53, 54, 55, false],
]);

it('takes a list of ranges and calculates how many are overlapped', function (
    string $ranges,
    int $expectedCount,
) {
    expect($this->service->calculateOverlappedRanges($ranges))
        ->toBeInt()
        ->toEqual($expectedCount);
})->with([
    ['1-10,20-30', 0],
    ['2-5,3-4', 1],
    ["10-50,51-100\n20-30,25-26", 1],
    ["1-10,2-3\n11-20,12-12\n21-30,29-50", 3],
    ["1-10,2-9\n10-20,15-16\n30-40,35-41\n45-46,42-48\n100-200,175-275", 5],
    ['1-1,1-1', 1],
    ['1-1,2-2', 0],
    ["1-3,2-4\n4-6,5-7", 2],
]);
