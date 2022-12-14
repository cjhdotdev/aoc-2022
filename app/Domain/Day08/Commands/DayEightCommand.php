<?php

namespace App\Domain\Day08\Commands;

use App\Console\BaseCommand;
use App\Domain\Day08\Services\DayEightService;

class DayEightCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-08 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Eight';

    /**
     * Execute the console command.
     *
     * @param  DayEightService  $dayEightService
     * @return int
     */
    public function handle(DayEightService $dayEightService): int
    {
        $treeSquare = $this->fetchFileContent();

        $this->output->info(sprintf('The total number of visible trees is: %d',
            $dayEightService->calculateVisibleTreesFromSquare($treeSquare),
        ));

        $this->output->info(sprintf('The best scenic score for a tree is: %d',
            $dayEightService->calculateBestScoreFromSquare($treeSquare),
        ));

        return self::SUCCESS;
    }
}
