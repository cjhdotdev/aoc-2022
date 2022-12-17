<?php

namespace Tests\Unit;

use App\Domain\Day09\DTO\LocationDTO;
use App\Domain\Day09\DTO\MoveDTO;
use App\Domain\Day09\Enums\DirectionEnum;
use App\Domain\Day09\RopeSquare;
use App\Domain\Day09\Services\DayNineService;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DayNineService());

it('parses a move line', function (
    string $moveLine,
    DirectionEnum $expectedDirection,
    int $expectedMoves,
) {
    expect($this->service->parseMoveLine($moveLine))
        ->toBeInstanceOf(MoveDTO::class)
        ->direction->toBeInstanceOf(DirectionEnum::class)->toEqual($expectedDirection)
        ->moves->toBeInt()->toEqual($expectedMoves);
})->with([
    ['U 2', DirectionEnum::Up, 2],
    ['D 5', DirectionEnum::Down, 5],
    ['L 10', DirectionEnum::Left, 10],
    ['R 0', DirectionEnum::Right, 0],
    ['X', DirectionEnum::Unknown, 0],
]);

it('moves the head of the rope based on a given move', function (
    LocationDTO $startPosition,
    MoveDTO $moveToPerform,
    LocationDTO $expectedPosition,
) {
    $ropeSquare = $this->service->createRopeSquare($startPosition);
    expect($ropeSquare)
        ->toBeInstanceOf(RopeSquare::class)
        ->move($moveToPerform)->toBeInstanceOf(RopeSquare::class)
        ->expect($ropeSquare->getHeadLocation())
            ->toBeInstanceOf(LocationDTO::class)
            ->toEqual($expectedPosition);
})->with([
    [new LocationDTO(0, 0), new MoveDTO(DirectionEnum::Right, 2), new LocationDTO(2, 0)],
    [new LocationDTO(5, 5), new MoveDTO(DirectionEnum::Up, 5), new LocationDTO(5, 10)],
    [new LocationDTO(10, 10), new MoveDTO(DirectionEnum::Left, 10), new LocationDTO(0, 10)],
    [new LocationDTO(50, 50), new MoveDTO(DirectionEnum::Down, 100), new LocationDTO(50, -50)],
]);

it('moves the tail when the head of the rope is moved', function (
    LocationDTO $startHeadPosition,
    LocationDTO $startTailPosition,
    MoveDTO $moveToPerform,
    LocationDTO $expectedTailPosition,
) {
    $ropeSquare = $this->service->createRopeSquare($startHeadPosition, $startTailPosition);
    expect($ropeSquare)
        ->toBeInstanceOf(RopeSquare::class)
        ->move($moveToPerform)->toBeInstanceOf(RopeSquare::class)
        ->expect($ropeSquare->getTailLocation())
        ->toBeInstanceOf(LocationDTO::class)
        ->toEqual($expectedTailPosition);
})->with([
    [new LocationDTO(0, 0), new LocationDTO(0, 0), new MoveDTO(DirectionEnum::Right, 4), new LocationDTO(3, 0)],
    [new LocationDTO(4, 0), new LocationDTO(3, 0), new MoveDTO(DirectionEnum::Up, 4), new LocationDTO(4, 3)],
    [new LocationDTO(4, 4), new LocationDTO(4, 3), new MoveDTO(DirectionEnum::Left, 3), new LocationDTO(2, 4)],
    [new LocationDTO(1, 4), new LocationDTO(2, 4), new MoveDTO(DirectionEnum::Down, 1), new LocationDTO(2, 4)],
    [new LocationDTO(1, 3), new LocationDTO(2, 4), new MoveDTO(DirectionEnum::Right, 4), new LocationDTO(4, 3)],
    [new LocationDTO(5, 3), new LocationDTO(4, 3), new MoveDTO(DirectionEnum::Down, 1), new LocationDTO(4, 3)],
    [new LocationDTO(5, 2), new LocationDTO(4, 3), new MoveDTO(DirectionEnum::Left, 5), new LocationDTO(1, 2)],
    [new LocationDTO(0, 2), new LocationDTO(1, 2), new MoveDTO(DirectionEnum::Right, 2), new LocationDTO(1, 2)],
]);

it('calculates the number of squares the tail has visited', function (
    LocationDTO $startPosition,
    Collection $moves,
    int $expectedVisited,
) {
    $ropeSquare = $this->service->createRopeSquare($startPosition);
    $moves->each(fn ($move) => $ropeSquare->move($move));
    expect($ropeSquare->countTailLocations())
        ->toBeInt()
        ->toEqual($expectedVisited);
})->with([
    [
        new LocationDTO(0, 0),
        collect([
            new MoveDTO(DirectionEnum::Right, 4),
            new MoveDTO(DirectionEnum::Up, 4),
            new MoveDTO(DirectionEnum::Left, 3),
            new MoveDTO(DirectionEnum::Down, 1),
            new MoveDTO(DirectionEnum::Right, 4),
            new MoveDTO(DirectionEnum::Down, 1),
            new MoveDTO(DirectionEnum::Left, 5),
            new MoveDTO(DirectionEnum::Right, 2),
        ]),
        13,
    ],
]);

it('parses a string list of moves into a collection', function (
    string $ropeMoves,
    Collection $expectedMoves
) {
    expect($this->service->parseRopeMoves($ropeMoves))
        ->toBeInstanceOf(Collection::class)
        ->toMatchArray($expectedMoves);
})->with([
    [
        "R 4\nU 4\nL 3\nD 1\nR 4\nD 1\nL 5\nR 2",
        collect([
            new MoveDTO(DirectionEnum::Right, 4),
            new MoveDTO(DirectionEnum::Up, 4),
            new MoveDTO(DirectionEnum::Left, 3),
            new MoveDTO(DirectionEnum::Down, 1),
            new MoveDTO(DirectionEnum::Right, 4),
            new MoveDTO(DirectionEnum::Down, 1),
            new MoveDTO(DirectionEnum::Left, 5),
            new MoveDTO(DirectionEnum::Right, 2),
        ]),
    ],
]);
