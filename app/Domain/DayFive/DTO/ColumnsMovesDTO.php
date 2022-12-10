<?php

namespace App\Domain\DayFive\DTO;

use App\Domain\DayFive\Collections\ColumnsCollection;
use Illuminate\Support\Collection;

final readonly class ColumnsMovesDTO
{
    /**
     * @param  ColumnsCollection  $columns
     * @param  Collection<int, MovementDTO>  $movements
     */
    public function __construct(
        public ColumnsCollection $columns,
        public Collection $movements,
    ) {
        //
    }
}
