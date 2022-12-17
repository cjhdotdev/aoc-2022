<?php

namespace App\Domain\Day10\Commands;

use App\Console\BaseCommand;
use App\Domain\Day10\Services\DayTenService;

class DayTenCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-10 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Ten';

    /**
     * Execute the console command.
     *
     * @param  DayTenService  $dayTenService
     * @return int
     */
    public function handle(DayTenService $dayTenService): int
    {
        $opList = $this->fetchFileContent();

        $this->output->info(sprintf('The total signal strength is: %d',
            $dayTenService->calculateSignalStrength($opList),
        ));

        return self::SUCCESS;
    }
}
