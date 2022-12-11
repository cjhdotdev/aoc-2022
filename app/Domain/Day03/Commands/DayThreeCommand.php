<?php

namespace App\Domain\Day03\Commands;

use App\Console\BaseCommand;
use App\Domain\Day03\Services\DayThreeService;

class DayThreeCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-03 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Three';

    /**
     * Execute the console command.
     *
     * @param  DayThreeService  $dayThreeService
     * @return int
     */
    public function handle(DayThreeService $dayThreeService): int
    {
        $rucksackList = $this->fetchFileContent();

        $this->output->info(sprintf('The priorities for the duplicated items was: %d',
            $dayThreeService->calculatePriorityForRucksacks($rucksackList)
        ));
        $this->output->info(sprintf('The priorities for the badged groups was: %d',
            $dayThreeService->calculatePriorityForGroups($rucksackList)
        ));

        return self::SUCCESS;
    }
}
