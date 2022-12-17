<?php

namespace App\Domain\Day10\Services;

use App\Domain\Day10\DTO\OperationDTO;
use App\Domain\Day10\Enums\OperationEnum;
use App\Domain\Day10\StateMachine;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayTenService
{
    public function calculateSignalStrength(string $opList): int
    {
        $stateMachine = $this->createStateMachine(collect([20, 60, 100, 140, 180, 220]));
        $this->parseOperationList($opList)->each(fn ($op) => $stateMachine->execute($op));

        return $stateMachine->calculateReportSum();
    }

    /**
     * @param  string  $opList
     * @return Collection<int, OperationDTO>
     */
    public function parseOperationList(string $opList): Collection
    {
        return Str::of($opList)
            ->explode(PHP_EOL)
            ->filter()
            ->map(fn ($operation) => $this->parseOperation(strval($operation)));
    }

    public function parseOperation(string $opLine): OperationDTO
    {
        return Str::of($opLine)
            ->explode(' ')
            ->pipe(fn ($operation) => new OperationDTO(
                OperationEnum::fromOpCode(strval($operation->first())),
                intval($operation->last()))
            );
    }

    public function createStateMachine(?Collection $reportsAt = null): StateMachine
    {
        if (! $reportsAt) {
            $reportsAt = new Collection();
        }

        return new StateMachine($reportsAt);
    }
}
