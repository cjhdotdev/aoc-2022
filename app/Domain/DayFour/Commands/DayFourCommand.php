<?php

namespace App\Domain\DayFour\Commands;

use App\Console\BaseCommand;
use App\Domain\DayFour\Services\DayFourService;

class DayFourCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-04 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Four';

    /**
     * Execute the console command.
     *
     * @param  DayFourService  $dayFourService
     * @return int
     */
    public function handle(DayFourService $dayFourService): int
    {
        $assignments = $this->fetchFileContent();

        $this->output->info(sprintf('The number of sectors contained in other sectors is: %d',
            $dayFourService->calculateContainedRanges($assignments)
        ));
        $this->output->info(sprintf('The number of overlapped sectors is: %d',
            $dayFourService->calculateOverlappedRanges($assignments)
        ));

        return self::SUCCESS;
    }
}
