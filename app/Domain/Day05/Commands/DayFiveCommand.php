<?php

namespace App\Domain\Day05\Commands;

use App\Console\BaseCommand;
use App\Domain\Day05\Services\DayFiveService;

class DayFiveCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-05 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Five';

    /**
     * Execute the console command.
     *
     * @param  DayFiveService  $dayFiveService
     * @return int
     */
    public function handle(DayFiveService $dayFiveService): int
    {
        $columnsAndMoves = $this->fetchFileContent();

        $this->output->info(sprintf('The crates on the top of the stacks are: %s',
            $dayFiveService->findTopCratesForInput($columnsAndMoves)
        ));
        $this->output->info(sprintf('The crates on the top using the multi crate crane are: %s',
            $dayFiveService->findTopCratesForInput($columnsAndMoves, true)
        ));

        return self::SUCCESS;
    }
}
