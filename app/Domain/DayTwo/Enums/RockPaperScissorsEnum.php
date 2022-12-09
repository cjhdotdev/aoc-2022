<?php

namespace App\Domain\DayTwo\Enums;

enum RockPaperScissorsEnum
{
    case Rock;
    case Paper;
    case Scissors;
    case Unknown;

    public static function fromCode(string $code): static
    {
        return match ($code) {
            'A', 'X' => self::Rock,
            'B', 'Y' => self::Paper,
            'C', 'Z' => self::Scissors,
            default => self::Unknown,
        };
    }

    public function score(): int
    {
        return match ($this) {
            self::Rock => 1,
            self::Paper => 2,
            self::Scissors => 3,
            default => 0,
        };
    }

    public function isBeatenBy(): RockPaperScissorsEnum
    {
        return match ($this) {
            self::Rock => self::Paper,
            self::Paper => self::Scissors,
            self::Scissors => self::Rock,
            default => self::Unknown,
        };
    }
}
