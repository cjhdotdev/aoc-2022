<?php

namespace App\Domain\Day07\DTO;

use Illuminate\Support\Collection;

class DirectoryDTO
{
    /**
     * @param  string  $directoryName
     * @param  DirectoryDTO|null  $parent
     * @param  Collection<string, DirectoryDTO>  $childDirectories
     * @param  Collection<string, int>  $childFiles
     */
    public function __construct(
        public readonly string $directoryName,
        public readonly ?DirectoryDTO $parent = null,
        public readonly Collection $childDirectories = new Collection(),
        public readonly Collection $childFiles = new Collection(),
    ) {
        //
    }

    public function addDirectory(string $directoryName): DirectoryDTO
    {
        if (! ($childDir = $this->findDirectory($directoryName))) {
            $childDir = new DirectoryDTO($directoryName, $this);
            $this->childDirectories->put($directoryName, $childDir);
        }

        return $childDir;
    }

    public function findDirectory(string $directoryName): ?DirectoryDTO
    {
        return $this->childDirectories->get($directoryName);
    }

    public function addFile(string $filename, int $size): void
    {
        if (! $this->findFile($filename)) {
            $this->childFiles->put($filename, $size);
        }
    }

    public function findFile(string $filename): ?int
    {
        return $this->childFiles->get($filename);
    }

    public function getTotalSize(): int
    {
        return $this->getChildDirectoriesSize() + ($this->childFiles->sum() ?? 0);
    }

    private function getChildDirectoriesSize(): int
    {
        return intval(
            $this
                ->childDirectories
                ->reduce(fn ($total, $directory) => intval($total) + intval($directory->getTotalSize()), 0)
        );
    }
}
