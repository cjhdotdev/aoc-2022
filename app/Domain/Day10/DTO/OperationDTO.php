<?php

namespace App\Domain\Day10\DTO;

use App\Domain\Day10\Enums\OperationEnum;

final readonly class OperationDTO
{
    public function __construct(
        public OperationEnum $operation,
        public int $count,
    ) {
        //
    }
}
