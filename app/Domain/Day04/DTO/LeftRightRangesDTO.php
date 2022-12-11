<?php

namespace App\Domain\Day04\DTO;

class LeftRightRangesDTO
{
    public function __construct(
        public readonly SectionRangeDTO $leftRange,
        public readonly SectionRangeDTO $rightRange,
    ) {
        //
    }
}
