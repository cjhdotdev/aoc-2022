<?php

namespace App\Domain\Day05\DTO;

final readonly class MovementDTO
{
    public function __construct(
        public int $count,
        public int $fromColumn,
        public int $toColumn,
        public bool $multiCrateCrane = false,
    ) {
        //
    }
}
