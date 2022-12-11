<?php

namespace App\Domain\Day02\DTO;

use App\Domain\Day02\Enums\RockPaperScissorsEnum;
use App\Domain\Day02\Enums\WinLoseDrawEnum;

final class HandOutcomeDTO
{
    public function __construct(
        public readonly RockPaperScissorsEnum $opponentsHand,
        public readonly WinLoseDrawEnum $handOutcome,
    ) {
    }
}
