<?php

namespace App\Domain\Day10\Enums;

enum OperationEnum
{
    case NoOp;
    case AddX;
    case Unknown;

    public static function fromOpCode(string $code): self
    {
        return match ($code) {
            'noop' => self::NoOp,
            'addx' => self::AddX,
            default => self::Unknown,
        };
    }

    public function executionCycles(): int
    {
        return match ($this) {
            self::NoOp => 1,
            self::AddX => 2,
            default => 0,
        };
    }
}
