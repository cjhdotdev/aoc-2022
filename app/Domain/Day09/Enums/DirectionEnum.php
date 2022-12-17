<?php

namespace App\Domain\Day09\Enums;

enum DirectionEnum
{
    case Up;
    case Down;
    case Left;
    case Right;
    case Unknown;

    public static function fromCode(string $code): self
    {
        return match ($code) {
            'U' => self::Up,
            'D' => self::Down,
            'L' => self::Left,
            'R' => self::Right,
            default => self::Unknown,
        };
    }
}
