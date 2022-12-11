<?php

namespace App\Domain\Day07\Commands;

use App\Console\BaseCommand;
use App\Domain\Day07\Services\DaySevenService;

class DaySevenCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-07 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Seven';

    /**
     * Execute the console command.
     *
     * @param  DaySevenService  $daySevenService
     * @return int
     */
    public function handle(DaySevenService $daySevenService): int
    {
        $commandLine = $this->fetchFileContent();

        $this->output->info(sprintf('Total size of all directories under 100000 is: %d',
            $daySevenService->parseInputAndCalculateUnder($commandLine, 100000),
        ));

        $this->output->info(sprintf('Size of smallest directory to free up space: %d',
            $daySevenService->parseInputAndFindFreeSpaceRemoval($commandLine)
        ));

        return self::SUCCESS;
    }
}
