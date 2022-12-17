<?php

namespace App\Domain\Day09;

use App\Domain\Day09\DTO\LocationDTO;
use App\Domain\Day09\DTO\MoveDTO;
use App\Domain\Day09\Enums\DirectionEnum;
use Illuminate\Support\Collection;

class RopeSquare
{
    public function __construct(
        private int $headX = 0,
        private int $headY = 0,
        private int $tailX = 0,
        private int $tailY = 0,
        private readonly Collection $tailLocations = new Collection(),
    ) {
        //
    }

    public function move(MoveDTO $move): self
    {
        $this->headY = match ($move->direction) {
            DirectionEnum::Up => $this->headY + $move->moves,
            DirectionEnum::Down => $this->headY - $move->moves,
            default => $this->headY
        };
        $this->headX = match ($move->direction) {
            DirectionEnum::Right => $this->headX + $move->moves,
            DirectionEnum::Left => $this->headX - $move->moves,
            default => $this->headX,
        };

        $this->moveTail($move);

        return $this;
    }

    public function getHeadLocation(): LocationDTO
    {
        return new LocationDTO($this->headX, $this->headY);
    }

    public function getTailLocation(): LocationDTO
    {
        return new LocationDTO($this->tailX, $this->tailY);
    }

    public function countTailLocations(): int
    {
        return $this->tailLocations->count();
    }

    private function moveTail(MoveDTO $move): void
    {
        $this->tailLocations->put(sprintf('%d-%d', $this->tailX, $this->tailY), true);

        if ($this->tailIsWithinOne()) {
            return;
        }

        $diffX = $this->tailDifferenceX();
        $diffY = $this->tailDifferenceY();

        if ($diffX > 0 && $diffY > 0) {
            $this->moveTailX();
            $this->moveTailY();
            $this->moveTail($move);

            return;
        }

        if ($diffX > 0) {
            $this->moveTailX($move);
            $this->moveTail($move);

            return;
        }

        $this->moveTailY($move);
        $this->moveTail($move);
    }

    private function tailIsWithinOne(): bool
    {
        return $this->tailDifferenceX() <= 1
            && $this->tailDifferenceY() <= 1;
    }

    private function tailDifferenceX(): int
    {
        return $this->tailX > $this->headX ? $this->tailX - $this->headX : $this->headX - $this->tailX;
    }

    private function tailDifferenceY(): int
    {
        return $this->tailY > $this->headY ? $this->tailY - $this->headY : $this->headY - $this->tailY;
    }

    private function moveTailX(?MoveDTO $move = null): void
    {
        if ($move) {
            $this->tailX = match ($move->direction) {
                DirectionEnum::Right => $this->tailX + 1,
                DirectionEnum::Left => $this->tailX - 1,
                default => $this->tailX,
            };
        } else {
            $this->tailX = ($this->tailX > $this->headX ? $this->tailX - 1 : $this->tailX + 1);
        }
    }

    private function moveTailY(?MoveDTO $move = null): void
    {
        if ($move) {
            $this->tailY = match ($move->direction) {
                DirectionEnum::Up => $this->tailY + 1,
                DirectionEnum::Down => $this->tailY - 1,
                default => $this->tailY,
            };
        } else {
            $this->tailY = ($this->tailY > $this->headY ? $this->tailY - 1 : $this->tailY + 1);
        }
    }
}
