<?php

namespace App\Domain\Day07\Services;

use App\Domain\Day07\DTO\DirectoryDTO;
use App\Domain\Day07\Enums\LineEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DaySevenService
{
    const SPACE_TOTAL = 70000000;

    const SPACE_REQUIRED = 30000000;

    public function __construct(
        public DirectoryDTO $currentDirectory = new DirectoryDTO(''),
    ) {
        //
    }

    public function parseInputAndFindFreeSpaceRemoval(string $commandLines): int
    {
        $this->parseCommandLinesInput($commandLines);

        return $this->findFirstDirectoryBiggerThan(self::SPACE_REQUIRED - $this->calculateFreeSpace());
    }

    public function parseInputAndCalculateUnder(string $commandLines, int $threshold): int
    {
        $this->parseCommandLinesInput($commandLines);

        return $this->calculateDirectoriesUnder($threshold);
    }

    public function calculateDirectoriesUnder(int $threshold): int
    {
        return $this->calculateChildDirectoriesUnder($this->identifyDirectory('/'), $threshold);
    }

    /**
     * @return Collection<int, int>
     */
    public function getAllDirectorySizes(): Collection
    {
        return $this
            ->getDirectorySize($this->identifyDirectory('/'), new Collection())
            ->flatten()
            ->values()
            ->map(fn ($value) => intval($value));
    }

    public function calculateFreeSpace(): int
    {
        return intval(self::SPACE_TOTAL - $this->getAllDirectorySizes()->sortDesc()->first());
    }

    public function findFirstDirectoryBiggerThan(int $threshold): int
    {
        $directories = $this->getAllDirectorySizes()->sort();

        return intval(
            $directories
                ->skipUntil(fn ($value) => $value >= $threshold)
                ->first()
        );
    }

    public function processCommandLine(string $commandLine): void
    {
        match ($this->identifyCommandLineType($commandLine)) {
            LineEnum::Command => $this->handleCommand($commandLine),
            LineEnum::Directory => $this->handleDirectory($commandLine),
            LineEnum::File => $this->handleFile($commandLine),
            default => null,
        };
    }

    public function identifyCommandLineType(string $commandLine): LineEnum
    {
        $line = Str::of($commandLine);

        return match (true) {
            $line->test('/^\$ /') => LineEnum::Command,
            $line->test('/^dir /') => LineEnum::Directory,
            default => LineEnum::File,
        };
    }

    private function handleCommand(string $commandLine): void
    {
        $command = Str::of($commandLine);
        match (true) {
            $command->test('/\$ cd/') => $this->handleChangeDirectory(strval($command->explode(' ')->last())),
            default => null,
        };
    }

    private function handleDirectory(string $commandLine): void
    {
        $this->identifyDirectory(
            Str::of($commandLine)->remove('dir ')
        );
    }

    private function handleFile(string $commandLine): void
    {
        [$fileSize, $fileName] = Str::of($commandLine)->explode(' ');
        $this->currentDirectory->addFile($fileName, $fileSize);
    }

    private function handleChangeDirectory(string $newDirectory): void
    {
        $this->currentDirectory = $this->identifyDirectory($newDirectory);
    }

    private function identifyDirectory(string $directoryName): DirectoryDTO
    {
        if ($directoryName === '..') {
            return $this->currentDirectory->parent ?? $this->currentDirectory;
        }

        if ($directoryName === '/') {
            $directory = $this->currentDirectory;
            while ($directory->parent !== null) {
                $directory = $directory->parent;
            }

            return $directory;
        }

        if (($directory = $this->currentDirectory->findDirectory($directoryName))) {
            return $directory;
        }

        return $this->currentDirectory->addDirectory($directoryName);
    }

    private function getDirectorySize(DirectoryDTO $directory, Collection $sizeCollection): Collection
    {
        if ($directory->childDirectories->isNotEmpty()) {
            $sizeCollection = $sizeCollection->union($directory->childDirectories->map(fn ($dir
            ) => $this->getDirectorySize($dir, $sizeCollection)));
        }

        return $sizeCollection->union([$directory->directoryName => $directory->getTotalSize()]);
    }

    private function parseCommandLinesInput(string $commandLines): void
    {
        Str::of($commandLines)
            ->explode(PHP_EOL)
            ->filter()
            ->each(fn ($line) => $this->processCommandLine(strval($line)));
    }

    private function calculateChildDirectoriesUnder(DirectoryDTO $directory, int $threshold): int
    {
        $totalSize = 0;

        if ($directory->childDirectories->isNotEmpty()) {
            $totalSize = $directory->childDirectories->reduce(
                fn ($total, $dir) => $total + $this->calculateChildDirectoriesUnder($dir, $threshold),
                0
            );
        }

        return $totalSize + ($directory->getTotalSize() < $threshold ? $directory->getTotalSize() : 0);
    }
}
