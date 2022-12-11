<?php

namespace App\Domain\Day04\DTO;

class SectionRangeDTO
{
    public function __construct(
        public readonly int $startSection,
        public readonly int $endSection,
    ) {
        //
    }

    public function contains(SectionRangeDTO $range): bool
    {
        return
            $range->startSection >= $this->startSection
            && $range->endSection <= $this->endSection;
    }

    public function isBiggerThan(SectionRangeDTO $range): bool
    {
        return ($this->endSection - $this->startSection) > ($range->endSection - $range->startSection);
    }

    public function overlaps(SectionRangeDTO $range): bool
    {
        return
            $this->contains($range)
            ||
            ($range->startSection >= $this->startSection && $range->startSection <= $this->endSection)
            ||
            ($range->endSection <= $this->endSection && $range->endSection >= $this->startSection);
    }
}
