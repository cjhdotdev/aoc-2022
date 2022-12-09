<?php

namespace App\Domain\DayTwo\Services;

use App\Domain\DayTwo\DTO\HandDTO;
use App\Domain\DayTwo\DTO\HandOutcomeDTO;
use App\Domain\DayTwo\Enums\RockPaperScissorsEnum;
use App\Domain\DayTwo\Enums\WinLoseDrawEnum;
use Illuminate\Support\Str;

class DayTwoService
{
    /**
     * @param  string  $handList
     * @return int
     */
    public function calculateScoreFromHandList(string $handList): int
    {
        return intval(
            Str::of($handList)
                ->explode(PHP_EOL)
                ->filter()
                ->map(fn ($hand) => $this->calculateScoreFromHand($this->parseHandsCodes(strval($hand))))
                ->sum()
        );
    }

    /**
     * @param  string  $handOutcomeList
     * @return int
     */
    public function calculateScoreFromHandAndOutcomeList(string $handOutcomeList): int
    {
        return intval(
            Str::of($handOutcomeList)
                ->explode(PHP_EOL)
                ->filter()
                ->map(fn ($hand) => $this->calculateScoreFromHandOutcome($this->parseHandOutcomeCodes(strval($hand))))
                ->sum()
        );
    }

    /**
     * @param  string  $inputCodes
     * @return HandDTO
     */
    public function parseHandsCodes(string $inputCodes): HandDTO
    {
        return Str::of($inputCodes)
            ->explode(' ')
            ->pipe(fn ($collection) => new HandDTO(
                opponentsHand: RockPaperScissorsEnum::fromCode(strval($collection->first())),
                yourHand: RockPaperScissorsEnum::fromCode(strval($collection->last())),
            ));
    }

    /**
     * @param  string  $inputCodes
     * @return HandOutcomeDTO
     */
    public function parseHandOutcomeCodes(string $inputCodes): HandOutcomeDTO
    {
        return Str::of($inputCodes)
            ->explode(' ')
            ->pipe(fn ($collection) => new HandOutcomeDTO(
                opponentsHand: RockPaperScissorsEnum::fromCode(strval($collection->first())),
                handOutcome: WinLoseDrawEnum::fromCode(strval($collection->last())),
            ));
    }

    /**
     * @param  HandDTO  $hand
     * @return int
     */
    public function calculateScoreFromHand(HandDTO $hand): int
    {
        if ($hand->hasUnknown()) {
            return 0;
        }

        return $this->findOutcome($hand)->score() + $hand->yourHand->score();
    }

    /**
     * @param  HandOutcomeDTO  $handOutcome
     * @return int
     */
    public function calculateScoreFromHandOutcome(HandOutcomeDTO $handOutcome): int
    {
        return $this->calculateScoreFromHand(new HandDTO(
            opponentsHand: $handOutcome->opponentsHand,
            yourHand: $this->findHandForOutcome($handOutcome->opponentsHand, $handOutcome->handOutcome)
        ));
    }

    /**
     * @param  HandDTO  $hand
     * @return WinLoseDrawEnum
     */
    public function findOutcome(HandDTO $hand): WinLoseDrawEnum
    {
        if ($hand->hasUnknown()) {
            return WinLoseDrawEnum::Unknown;
        }

        if ($hand->opponentsHand === $hand->yourHand) {
            return WinLoseDrawEnum::Draw;
        }

        if ($hand->opponentsHand->isBeatenBy() === $hand->yourHand) {
            return WinLoseDrawEnum::Win;
        }

        if ($hand->yourHand->isBeatenBy() === $hand->opponentsHand) {
            return WinLoseDrawEnum::Lose;
        }

        return WinLoseDrawEnum::Unknown;
    }

    /**
     * @param  RockPaperScissorsEnum  $opponentsHand
     * @param  WinLoseDrawEnum  $expectedOutcome
     * @return RockPaperScissorsEnum
     */
    public function findHandForOutcome(RockPaperScissorsEnum $opponentsHand, WinLoseDrawEnum $expectedOutcome): RockPaperScissorsEnum
    {
        return match ($expectedOutcome) {
            WinLoseDrawEnum::Win => $opponentsHand->isBeatenBy(),
            WinLoseDrawEnum::Draw => $opponentsHand,
            WinLoseDrawEnum::Lose => $opponentsHand->isBeatenBy()->isBeatenBy(),
            default => RockPaperScissorsEnum::Unknown,
        };
    }
}
