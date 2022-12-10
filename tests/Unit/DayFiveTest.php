<?php

namespace Tests\Unit;

use App\Domain\DayFive\Collections\ColumnsCollection;
use App\Domain\DayFive\DTO\ColumnsMovesDTO;
use App\Domain\DayFive\DTO\MovementDTO;
use App\Domain\DayFive\Services\DayFiveService;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DayFiveService());

it('can parse a stack input line into columns', function (
    string $stackInput,
    array $expectedColumns,
) {
    expect($this->service->parseStackLine($stackInput))
        ->toBeInstanceOf(Collection::class)
        ->toMatchArray($expectedColumns);
})->with([
    [
        '[A] [B] [C] [D] [E]',
        ['A', 'B', 'C', 'D', 'E'],
    ],
    [
        '    [A]     [B] [C]',
        [' ', 'A', ' ', 'B', 'C'],
    ],
    [
        '                [A]',
        [' ', ' ', ' ', ' ', 'A'],
    ],
    [
        '[A]                ',
        ['A', ' ', ' ', ' ', ' '],
    ],
    [
        '                   ',
        [' ', ' ', ' ', ' ', ' '],
    ],
]);

it('identifies the required moves in a move line', function (
    string $moveLine,
    int $expectedCount,
    int $expectedFromColumn,
    int $expectedToColumn,
) {
    expect($this->service->parseMoveLine($moveLine))
        ->toBeInstanceOf(MovementDTO::class)
        ->count->toBeInt()->toEqual($expectedCount)
        ->fromColumn->toBeInt()->toEqual($expectedFromColumn)
        ->toColumn->toBeInt()->toEqual($expectedToColumn);
})->with([
    ['move 1 from 2 to 3', 1, 2, 3],
    ['move 10 from 8 to 7', 10, 8, 7],
    ['move 2 from 2 to 3', 2, 2, 3],
    ['move 5 from 4 to 5', 5, 4, 5],
]);

it('calculates columns from provided rows', function (
    Collection $rows,
    array $expectedColumns,
) {
    $columnsCollection = new ColumnsCollection();
    $rows->each(fn ($row) => $columnsCollection->addRow($row));
    expect($columnsCollection)
        ->toBeInstanceOf(ColumnsCollection::class)
        ->toMatchArray($expectedColumns);
})->with([
    [
        collect([
            new Collection(['A', 'B', 'C', 'D', 'E']),
            new Collection(['F', 'G', 'H', 'I', 'J']),
        ]),
        [
            ['A', 'F'],
            ['B', 'G'],
            ['C', 'H'],
            ['D', 'I'],
            ['E', 'J'],
        ],
    ],
    [
        collect([
            new Collection(['A', ' ', ' ', ' ', 'E', 'F', ' ', 'H']),
            new Collection(['Z', 'Y', 'X', ' ', 'V', 'U', ' ', 'S']),
            new Collection(['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P']),
        ]),
        [
            ['A', 'Z', 'I'],
            ['Y', 'J'],
            ['X', 'K'],
            ['L'],
            ['E', 'V', 'M'],
            ['F', 'U', 'N'],
            ['O'],
            ['H', 'S', 'P'],
        ],
    ],
]);

it('alters the column collection based on some movements', function (
    Collection $rows,
    Collection $movements,
    array $expectedColumns,
) {
    $columnsCollection = new ColumnsCollection();

    $rows->each(fn ($row) => $columnsCollection->addRow($row));
    $movements->each(fn ($movement) => $columnsCollection->transformWithMovement($movement));

    expect($columnsCollection)
        ->toBeInstanceOf(ColumnsCollection::class)
        ->toMatchArray($expectedColumns);
})->with([
    [
        collect([
            new Collection([' ', 'B', 'C', ' ']),
            new Collection(['E', 'F', 'G', ' ']),
            new Collection(['I', 'J', 'K', 'L']),
            new Collection(['M', 'N', 'O', 'P']),
        ]),
        collect([
            new MovementDTO(2, 2, 4),
        ]),
        [
            ['E', 'I', 'M'],
            ['J', 'N'],
            ['C', 'G', 'K', 'O'],
            ['F', 'B', 'L', 'P'],
        ],
    ],
    [
        collect([
            new Collection(['A', ' ', ' ', ' ']),
            new Collection(['E', ' ', ' ', ' ']),
            new Collection(['I', ' ', 'K', ' ']),
            new Collection(['M', 'N', 'O', ' ']),
            new Collection(['Q', 'R', 'S', ' ']),
            new Collection(['U', 'V', 'W', 'X']),
        ]),
        collect([
            new MovementDTO(5, 1, 4),
            new MovementDTO(2, 2, 4),
            new MovementDTO(1, 3, 4),
        ]),
        [
            ['U'],
            ['V'],
            ['O', 'S', 'W'],
            ['K', 'R', 'N', 'Q', 'M', 'I', 'E', 'A', 'X'],
        ],
    ],
]);

it('can identify the crate on the top of each column', function (
    ColumnsCollection $columns,
    array $expectedTopCrates,
) {
    expect($columns->findTopCrates())
        ->toBeInstanceOf(Collection::class)
        ->toMatchArray($expectedTopCrates);
})->with([
    [
        (new ColumnsCollection())
            ->addRow(new Collection(['A', ' ', 'C', ' ']))
            ->addRow(new Collection(['E', 'F', 'G', ' ']))
            ->addRow(new Collection(['I', 'J', 'K', 'L'])),
        ['A', 'F', 'C', 'L'],
    ],
    [
        (new ColumnsCollection())
            ->addRow(new Collection([' ', ' ', 'C', ' ', ' ', ' ', ' ']))
            ->addRow(new Collection([' ', ' ', 'G', ' ', ' ', ' ', 'Z']))
            ->addRow(new Collection(['I', ' ', 'K', ' ', 'Y', ' ', 'X']))
            ->addRow(new Collection(['M', ' ', 'O', 'P', 'V', ' ', 'U']))
            ->addRow(new Collection(['Q', 'R', 'S', 'T', 'A', 'B', 'C'])),
        ['I', 'R', 'C', 'P', 'Y', 'B', 'Z'],
    ],
]);

it('can parse a full input into columns and movements', function (
    string $input,
    array $expectedColumns,
    array $expectedMovements,
) {
    expect($this->service->parseColumnsAndMoves($input))
        ->toBeInstanceOf(ColumnsMovesDTO::class)
        ->columns->toBeInstanceOf(ColumnsCollection::class)->toMatchArray($expectedColumns)
        ->movements->toBeInstanceOf(Collection::class)->toMatchArray($expectedMovements);
})->with([
    [
        "[A] [B]     [D]\n[E] [F]     [H]\n[I] [J] [K] [L]\n\nmove 1 from 1 to 3\nmove 2 from 4 to 1",
        [['A', 'E', 'I'], ['B', 'F', 'J'], ['K'], ['D', 'H', 'L']],
        [
            new MovementDTO(1, 1, 3),
            new MovementDTO(2, 4, 1),
        ],
    ],
]);
