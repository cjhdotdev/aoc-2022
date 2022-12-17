<?php

namespace Tests\Unit;

use App\Domain\Day10\DTO\OperationDTO;
use App\Domain\Day10\Enums\OperationEnum;
use App\Domain\Day10\Services\DayTenService;
use App\Domain\Day10\StateMachine;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DayTenService());

it('parses an operation line', function (
    string $opLine,
    OperationDTO $expectedOperation
) {
    expect($this->service->parseOperation($opLine))
        ->toBeInstanceOf(OperationDTO::class)
        ->toEqual($expectedOperation);
})->with([
    ['noop', new OperationDTO(OperationEnum::NoOp, 0)],
    ['addx 10', new OperationDTO(OperationEnum::AddX, 10)],
]);

it('creates a new state machine', function () {
    expect($this->service->createStateMachine())
        ->toBeInstanceOf(StateMachine::class);
});

it('applies a new operation to the state machine', function (
    OperationDTO $operationToExecute,
    int $expectedCounter,
    int $expectedRegister,
) {
    $stateMachine = $this->service->createStateMachine();
    $stateMachine->execute($operationToExecute);
    expect($stateMachine)
        ->getCounter()->toBeInt()->toEqual($expectedCounter)
        ->getRegister()->toBeInt()->toEqual($expectedRegister);
})->with([
    [new OperationDTO(OperationEnum::AddX, 5), 2, 6],
    [new OperationDTO(OperationEnum::NoOp, 0), 1, 1],
]);

it('creates a counter report at given numbers', function (
    Collection $operations,
    Collection $reportsAt,
    int $expectedReportSum,
) {
    $stateMachine = $this->service->createStateMachine($reportsAt);
    $operations->each(fn ($op) => $stateMachine->execute($op));
    expect($stateMachine->calculateReportSum())
        ->toBeInt()
        ->toEqual($expectedReportSum);
})->with([
    [
        collect([
            new OperationDTO(OperationEnum::AddX, 10),
            new OperationDTO(OperationEnum::AddX, 20),
            new OperationDTO(OperationEnum::AddX, 30),
            new OperationDTO(OperationEnum::AddX, 40),
        ]),
        collect([2, 3, 6, 8]),
        709,
    ],
]);

it('parses a list of operations and calculates a report sum', function (
    string $opList,
    Collection $reportsAt,
    int $expectedReportSum,
) {
    $stateMachine = $this->service->createStateMachine($reportsAt);
    $this->service->parseOperationList($opList)->each(fn ($op) => $stateMachine->execute($op));
    expect($stateMachine->calculateReportSum())
        ->toBeInt()
        ->toEqual($expectedReportSum);
})->with([
    [
        "addx 15\naddx -11\naddx 6\naddx -3\naddx 5\naddx -1\naddx -8\naddx 13\naddx 4\nnoop\naddx -1\naddx 5\naddx -1\naddx 5\n".
        "addx -1\naddx 5\naddx -1\naddx 5\naddx -1\naddx -35\naddx 1\naddx 24\naddx -19\naddx 1\naddx 16\naddx -11\nnoop\nnoop\n".
        "addx 21\naddx -15\nnoop\nnoop\naddx -3\naddx 9\naddx 1\naddx -3\naddx 8\naddx 1\naddx 5\nnoop\nnoop\nnoop\nnoop\nnoop\n".
        "addx -36\nnoop\naddx 1\naddx 7\nnoop\nnoop\nnoop\naddx 2\naddx 6\nnoop\nnoop\nnoop\nnoop\nnoop\naddx 1\nnoop\nnoop\n".
        "addx 7\naddx 1\nnoop\naddx -13\naddx 13\naddx 7\nnoop\naddx 1\naddx -33\nnoop\nnoop\nnoop\naddx 2\nnoop\nnoop\nnoop\n".
        "addx 8\nnoop\naddx -1\naddx 2\naddx 1\nnoop\naddx 17\naddx -9\naddx 1\naddx 1\naddx -3\naddx 11\nnoop\nnoop\naddx 1\n".
        "noop\naddx 1\nnoop\nnoop\naddx -13\naddx -19\naddx 1\naddx 3\naddx 26\naddx -30\naddx 12\naddx -1\naddx 3\naddx 1\nnoop\n".
        "noop\nnoop\naddx -9\naddx 18\naddx 1\naddx 2\nnoop\nnoop\naddx 9\nnoop\nnoop\nnoop\naddx -1\naddx 2\naddx -37\naddx 1\n".
        "addx 3\nnoop\naddx 15\naddx -21\naddx 22\naddx -6\naddx 1\nnoop\naddx 2\naddx 1\nnoop\naddx -10\nnoop\nnoop\naddx 20\n".
        "addx 1\naddx 2\naddx 2\naddx -6\naddx -11\nnoop\nnoop\nnoop",
        collect([20, 60, 100, 140, 180, 220]),
        13140,
    ],
]);
