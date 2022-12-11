<?php

namespace App\Domain\Day05\DTO;

use App\Domain\Day05\Collections\ColumnsCollection;
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
