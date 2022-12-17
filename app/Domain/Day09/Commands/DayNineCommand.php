<?php

namespace App\Domain\Day09\Commands;

use App\Console\BaseCommand;
use App\Domain\Day09\Services\DayNineService;

class DayNineCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-09 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Nine';

    /**
     * Execute the console command.
     *
     * @param  DayNineService  $dayNineService
     * @return int
     */
    public function handle(DayNineService $dayNineService): int
    {
        $ropeMoves = $this->fetchFileContent();

        $this->output->info(sprintf('The locations visited by the tail is: %d',
            $dayNineService->calculateTailLocations($ropeMoves),
        ));

        return self::SUCCESS;
    }
}
