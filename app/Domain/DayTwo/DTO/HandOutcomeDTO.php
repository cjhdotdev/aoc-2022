<?php

namespace App\Domain\DayTwo\DTO;

use App\Domain\DayTwo\Enums\RockPaperScissorsEnum;
use App\Domain\DayTwo\Enums\WinLoseDrawEnum;

final class HandOutcomeDTO
{
    public function __construct(
        public readonly RockPaperScissorsEnum $opponentsHand,
        public readonly WinLoseDrawEnum $handOutcome,
    ) {
    }
}
