<?php

namespace App\Domain\DayOne\Commands;

use App\Console\BaseCommand;
use App\Domain\DayOne\Services\DayOneService;

class DayOneCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-01 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day One';

    /**
     * Execute the console command.
     *
     * @param  DayOneService  $dayOneService
     * @return int
     */
    public function handle(DayOneService $dayOneService): int
    {
        $caloriesInput = $this->fetchFileContent();

        $this->output->info(sprintf('The most amount of calories consumed by an elf is: %d',
            $dayOneService->findHighest($caloriesInput)
        ));
        $this->output->info(sprintf('The total of the top three elves calories is: %d',
            $dayOneService->findHighest($caloriesInput, 3)
        ));

        return self::SUCCESS;
    }
}
