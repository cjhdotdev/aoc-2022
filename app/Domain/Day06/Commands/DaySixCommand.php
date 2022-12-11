<?php

namespace App\Domain\Day06\Commands;

use App\Console\BaseCommand;
use App\Domain\Day06\Services\DaySixService;

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
        $this->output->info(sprintf('The position of the start-of-message marker is: %d',
            $daySixService->findFirstUniqueMessageBatch($messengerInput)
        ));

        return self::SUCCESS;
    }
}
