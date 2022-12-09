<?php

namespace App\Domain\DayTwo\DTO;

use App\Domain\DayTwo\Enums\RockPaperScissorsEnum;

final class HandDTO
{
    public function __construct(
        public readonly RockPaperScissorsEnum $opponentsHand,
        public readonly RockPaperScissorsEnum $yourHand,
    ) {
        //
    }

    public function hasUnknown(): bool
    {
        return $this->opponentsHand === RockPaperScissorsEnum::Unknown || $this->yourHand === RockPaperScissorsEnum::Unknown;
    }
}
