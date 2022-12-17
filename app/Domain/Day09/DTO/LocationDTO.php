<?php

namespace App\Domain\Day09\DTO;

final readonly class LocationDTO
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
        //
    }
}
