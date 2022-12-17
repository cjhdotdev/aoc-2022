<?php

namespace App\Domain\Day09\Services;

use App\Domain\Day09\DTO\LocationDTO;
use App\Domain\Day09\DTO\MoveDTO;
use App\Domain\Day09\Enums\DirectionEnum;
use App\Domain\Day09\RopeSquare;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayNineService
{
    public function calculateTailLocations(string $ropeMoves): int
    {
        $ropeSquare = $this->createRopeSquare(new LocationDTO(0, 0));
        $this->parseRopeMoves($ropeMoves)->each(fn ($move) => $ropeSquare->move($move));

        return $ropeSquare->countTailLocations();
    }

    /**
     * @param  string  $ropeMoves
     * @return Collection<int, MoveDTO>
     */
    public function parseRopeMoves(string $ropeMoves): Collection
    {
        return Str::of($ropeMoves)
            ->explode(PHP_EOL)
            ->map(fn ($move) => $this->parseMoveLine(strval($move)));
    }

    public function parseMoveLine(string $moveLine): MoveDTO
    {
        return Str::of($moveLine)
            ->explode(' ')
            ->pipe(fn ($move) => new MoveDTO(
                DirectionEnum::fromCode(strval($move->first())),
                intval($move->last()),
            ));
    }

    public function createRopeSquare(LocationDTO $headPosition, ?LocationDTO $tailPosition = null): RopeSquare
    {
        return new RopeSquare(
            headX: $headPosition->x,
            headY: $headPosition->y,
            tailX: ($tailPosition ? $tailPosition->x : $headPosition->x),
            tailY: ($tailPosition ? $tailPosition->y : $headPosition->y),
        );
    }
}
