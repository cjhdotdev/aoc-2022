<?php

namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

abstract class BaseCommand extends Command
{
    protected function fetchFileContent(string $argumentName = 'filename'): string
    {
        $inputFile = strval($this->argument($argumentName));

        if (! Storage::exists($inputFile)) {
            throw new \RuntimeException('The specified file does not exist');
        }

        return Storage::get($inputFile) ?? '';
    }
}
