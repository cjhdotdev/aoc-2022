<?php

namespace App\Domain\Day02\Enums;

enum WinLoseDrawEnum
{
    case Win;
    case Lose;
    case Draw;
    case Unknown;

    public static function fromCode(string $code): static
    {
        return match ($code) {
            'X' => self::Lose,
            'Y' => self::Draw,
            'Z' => self::Win,
            default => self::Unknown
        };
    }

    public function score(): int
    {
        return match ($this) {
            self::Win => 6,
            self::Draw => 3,
            default => 0
        };
    }
}
