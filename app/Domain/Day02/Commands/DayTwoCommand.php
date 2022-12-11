<?php

namespace App\Domain\Day02\Commands;

use App\Console\BaseCommand;
use App\Domain\Day02\Services\DayTwoService;

class DayTwoCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-02 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Two';

    /**
     * Execute the console command.
     *
     * @param  DayTwoService  $dayTwoService
     * @return int
     */
    public function handle(DayTwoService $dayTwoService): int
    {
        $handsList = $this->fetchFileContent();

        $this->output->info(sprintf('The score from playing all the rock-paper-scissors hands was: %d',
            $dayTwoService->calculateScoreFromHandList($handsList)
        ));
        $this->output->info(sprintf('The score from playing the hands with outcomes listed was: %d',
            $dayTwoService->calculateScoreFromHandAndOutcomeList($handsList)
        ));

        return self::SUCCESS;
    }
}
