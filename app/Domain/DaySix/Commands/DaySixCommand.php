<?php

namespace App\Domain\DaySix\Commands;

use App\Console\BaseCommand;
use App\Domain\DaySix\Services\DaySixService;

class DaySixCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day-06 {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code 2022 - Day Six';

    /**
     * Execute the console command.
     *
     * @param  DaySixService  $daySixService
     * @return int
     */
    public function handle(DaySixService $daySixService): int
    {
        $messengerInput = $this->fetchFileContent();

        $this->output->info(sprintf('The position of the start-of-packet marker is: %d',
            $daySixService->findFirstUniqueBatch($messengerInput)
        ));

        return self::SUCCESS;
    }
}
