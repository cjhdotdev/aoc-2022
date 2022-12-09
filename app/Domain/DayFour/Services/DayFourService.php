<?php

namespace App\Domain\DayFour\Services;

use App\Domain\DayFour\DTO\LeftRightRangesDTO;
use App\Domain\DayFour\DTO\SectionRangeDTO;
use Illuminate\Support\Str;

class DayFourService
{
    public function calculateContainedRanges(string $ranges): int
    {
        return intval(
            Str::of($ranges)
                ->explode(PHP_EOL)
                ->map(fn ($ranges) => $this->splitMultipleRanges(strval($ranges)))
                ->filter(fn ($ranges) => $this
                    ->findBiggestOf($ranges->leftRange, $ranges->rightRange)
                    ->contains(
                        $this->findSmallestOf($ranges->leftRange, $ranges->rightRange)
                    )
                )
                ->count()
        );
    }

    public function calculateOverlappedRanges(string $ranges): int
    {
        return intval(
            Str::of($ranges)
                ->explode(PHP_EOL)
                ->map(fn ($ranges) => $this->splitMultipleRanges(strval($ranges)))
                ->filter(function ($ranges) {
                    return $this
                        ->findBiggestOf($ranges->leftRange, $ranges->rightRange)
                        ->overlaps($this->findSmallestOf($ranges->leftRange, $ranges->rightRange));
                })
                ->count()
        );
    }

    /**
     * @param  string  $range
     * @return SectionRangeDTO
     */
    public function splitRanges(string $range): SectionRangeDTO
    {
        return Str::of($range)
            ->explode('-')
            ->pipe(fn ($range) => new SectionRangeDTO(
                intval($range->first()),
                intval($range->last()))
            );
    }

    public function splitMultipleRanges(string $ranges): LeftRightRangesDTO
    {
        return Str::of($ranges)
            ->explode(',')
            ->map(fn ($range) => $this->splitRanges(strval($range)))
            ->pipe(fn ($ranges) => new LeftRightRangesDTO(
                $ranges->first() ?? new SectionRangeDTO(0, 0),
                $ranges->last() ?? new SectionRangeDTO(0, 0)
            ));
    }

    public function findBiggestOf(SectionRangeDTO $rangeLeft, SectionRangeDTO $rangeRight): SectionRangeDTO
    {
        return $rangeLeft->isBiggerThan($rangeRight) ? $rangeLeft : $rangeRight;
    }

    public function findSmallestOf(SectionRangeDTO $rangeLeft, SectionRangeDTO $rangeRight): SectionRangeDTO
    {
        return ! $rangeLeft->isBiggerThan($rangeRight) ? $rangeLeft : $rangeRight;
    }
}
