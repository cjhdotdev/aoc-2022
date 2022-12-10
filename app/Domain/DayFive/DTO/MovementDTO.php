<?php

namespace App\Domain\DayFive\DTO;

final readonly class MovementDTO
{
    public function __construct(
        public int $count,
        public int $fromColumn,
        public int $toColumn,
    ) {
        //
    }
}
