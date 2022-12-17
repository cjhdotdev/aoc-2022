<?php

namespace App\Domain\Day09\DTO;

use App\Domain\Day09\Enums\DirectionEnum;

final readonly class MoveDTO
{
    public function __construct(
        public DirectionEnum $direction,
        public int $moves,
    ) {
        //
    }
}
