<?php

namespace App\Domain\Day10;

use App\Domain\Day10\DTO\OperationDTO;
use Illuminate\Support\Collection;

class StateMachine
{
    private int $counter = 0;

    private int $register = 1;

    private Collection $counterReports;

    public function __construct(
        private Collection $counterReportsAt = new Collection(),
    ) {
        $this->counterReports = new Collection();
    }

    public function execute(OperationDTO $opDTO): void
    {
        $newCounter = $this->counter + $opDTO->operation->executionCycles();
        $newRegister = $this->register + $opDTO->count;

        $this
            ->getUnfilledReports()
            ->each(fn ($reportAt) => ($reportAt <= $newCounter ? $this->counterReports->put(strval($reportAt), $this->register) : null));

        $this->counter = $newCounter;
        $this->register = $newRegister;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function getRegister(): int
    {
        return $this->register;
    }

    public function calculateReportSum(): int
    {
        $this
            ->getUnfilledReports()
            ->each(fn ($reportAt) => $this->counterReports->put(strval($reportAt), $this->register));

        return $this
            ->counterReports
            ->reduce(fn ($sum, $signal, $report) => $sum + (intval($signal) * intval($report)), 0);
    }

    private function getUnfilledReports(): Collection
    {
        return $this
            ->counterReportsAt
            ->filter(fn ($reportAt) => ! $this->counterReports->has(strval($reportAt)));
    }
}
