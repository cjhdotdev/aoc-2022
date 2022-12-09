<?php

namespace App\Domain\DayFour\DTO;

class LeftRightRangesDTO
{
    public function __construct(
        public readonly SectionRangeDTO $leftRange,
        public readonly SectionRangeDTO $rightRange,
    ) {
        //
    }
}
