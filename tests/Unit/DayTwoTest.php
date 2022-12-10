<?php

namespace Tests\Unit;

use App\Domain\DayTwo\DTO\HandDTO;
use App\Domain\DayTwo\DTO\HandOutcomeDTO;
use App\Domain\DayTwo\Enums\RockPaperScissorsEnum;
use App\Domain\DayTwo\Enums\WinLoseDrawEnum;
use App\Domain\DayTwo\Services\DayTwoService;

beforeEach(fn () => $this->service = new DayTwoService());

it('correctly identifies rock, paper and scissors from the code', function (
    string $code,
    RockPaperScissorsEnum $sign
) {
    expect(RockPaperScissorsEnum::fromCode($code))
        ->toEqual($sign);
})->with([
    ['A', RockPaperScissorsEnum::Rock],
    ['B', RockPaperScissorsEnum::Paper],
    ['C', RockPaperScissorsEnum::Scissors],
    ['X', RockPaperScissorsEnum::Rock],
    ['Y', RockPaperScissorsEnum::Paper],
    ['Z', RockPaperScissorsEnum::Scissors],
    ['D', RockPaperScissorsEnum::Unknown],
    ['1', RockPaperScissorsEnum::Unknown],
]);

it('parses the code into two hands', function (
    string $inputCodes,
    RockPaperScissorsEnum $expectedOpponentsHand,
    RockPaperScissorsEnum $expectedYourHand
) {
    expect($this->service->parseHandsCodes($inputCodes))
        ->toBeInstanceOf(HandDTO::class)
        ->opponentsHand->toEqual($expectedOpponentsHand)
        ->yourHand->toEqual($expectedYourHand);
})->with([
    ['A X', RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock],
    ['A Y', RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Paper],
    ['A Z', RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Scissors],
    ['B X', RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Rock],
    ['B Y', RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper],
    ['B Z', RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Scissors],
    ['C X', RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Rock],
    ['C Y', RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Paper],
    ['C Z', RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors],
    ['D X', RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Rock],
    ['X D', RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Unknown],
    ['D 1', RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Unknown],
]);

it('can pick the outcome of the hand', function (
    RockPaperScissorsEnum $opponentsHand,
    RockPaperScissorsEnum $yourHand,
    WinLoseDrawEnum $expectedOutcome
) {
    $handDTO = new HandDTO($opponentsHand, $yourHand);
    expect($this->service->findOutcome($handDTO))
        ->toEqual($expectedOutcome);
})->with([
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Win],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Lose],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Lose],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Win],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Win],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Lose],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Draw],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Draw],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Draw],
    [RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Unknown],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Unknown],
    [RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Unknown],
]);

it('calculates hand two score based on winning scenario', function (
    RockPaperScissorsEnum $opponentsHand,
    RockPaperScissorsEnum $yourHand,
    int $expectedScore
) {
    $handDTO = new HandDTO($opponentsHand, $yourHand);
    expect($this->service->calculateScoreFromHand($handDTO))
        ->toBeInt()
        ->toEqual($expectedScore);
})->with([
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Paper, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Rock, RockPaperScissorsEnum::Unknown, 0],
    [RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Rock, 0],
    [RockPaperScissorsEnum::Unknown, RockPaperScissorsEnum::Unknown, 0],
]);

it('calculates the total scores for a list of hands', function () {
    expect($this->service->calculateScoreFromHandList("A X\nA Y\nA Z\nB X\nB Y\nB Z\nC X\nC Y\nC Z"))
        ->toBeInt()
        ->toEqual(45);
});

it('identifies the correct code for win, lose or draw', function (
    string $code,
    WinLoseDrawEnum $expectedStatus
) {
    expect(WinLoseDrawEnum::fromCode($code))
        ->toEqual($expectedStatus);
})->with([
    ['X', WinLoseDrawEnum::Lose],
    ['Y', WinLoseDrawEnum::Draw],
    ['Z', WinLoseDrawEnum::Win],
    ['D', WinLoseDrawEnum::Unknown],
]);

it('picks a second hand according to first hand and win, lose or draw status', function (
    RockPaperScissorsEnum $opponentsHand,
    WinLoseDrawEnum $handOutcome,
    RockPaperScissorsEnum $expectedHand
) {
    expect($this->service->findHandForOutcome($opponentsHand, $handOutcome))
        ->toEqual($expectedHand);
})->with([
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Paper],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Rock],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Scissors],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Scissors],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Paper],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Rock],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Rock],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Scissors],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Paper],
    [RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Unknown],
    [RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Unknown],
    [RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Unknown],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Unknown, RockPaperScissorsEnum::Unknown],
]);

it('parses the code into a hand and outcome', function (
    string $inputCodes,
    RockPaperScissorsEnum $expectedOpponentsHand,
    WinLoseDrawEnum $expectedOutcome
) {
    expect($this->service->parseHandOutcomeCodes($inputCodes))
        ->toBeInstanceOf(HandOutcomeDTO::class)
        ->opponentsHand->toEqual($expectedOpponentsHand)
        ->handOutcome->toEqual($expectedOutcome);
})->with([
    ['A X', RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Lose],
    ['A Y', RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Draw],
    ['A Z', RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Win],
    ['B X', RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Lose],
    ['B Y', RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Draw],
    ['B Z', RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Win],
    ['C X', RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Lose],
    ['C Y', RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Draw],
    ['C Z', RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Win],
    ['D X', RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Lose],
    ['X D', RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Unknown],
    ['D E', RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Unknown],
]);

it('calculates the score based on hand one and a winning scenario', function (
    RockPaperScissorsEnum $opponentsHand,
    WinLoseDrawEnum $handOutcome,
    int $expectedScore
) {
    $handOutcomeDTO = new HandOutcomeDTO($opponentsHand, $handOutcome);
    expect($this->service->calculateScoreFromHandOutcome($handOutcomeDTO))
        ->toBeInt()
        ->toEqual($expectedScore);
})->with([
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Paper, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Win, RockPaperScissorsEnum::Rock->score() + WinLoseDrawEnum::Win->score()],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Draw, RockPaperScissorsEnum::Scissors->score() + WinLoseDrawEnum::Draw->score()],
    [RockPaperScissorsEnum::Scissors, WinLoseDrawEnum::Lose, RockPaperScissorsEnum::Paper->score() + WinLoseDrawEnum::Lose->score()],
    [RockPaperScissorsEnum::Unknown, WinLoseDrawEnum::Win, 0],
    [RockPaperScissorsEnum::Rock, WinLoseDrawEnum::Unknown, 0],
]);

it('calculates the total scores for a list of hands and outcomes', function () {
    expect($this->service->calculateScoreFromHandAndOutcomeList("A X\nA Y\nA Z\nB X\nB Y\nB Z\nC X\nC Y\nC Z"))
        ->toBeInt()
        ->toEqual(45);
});
