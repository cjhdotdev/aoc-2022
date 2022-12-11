<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-07');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-07 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates all directories under 10000', function () {
    Storage::fake('local');
    Storage::put('filesystem.txt', "$ cd testing\ndir folderOne\ndir folderTwo\n100001 fileOne\n$ cd folderOne\n$ ls\n1001 fileTwo\n1003 fileThree\ndir folderThree\n$ cd folderThree\n$ ls\n1004 fileFour\n$ cd ..\n$ cd ..\n$ cd folderTwo\n$ ls\n29100001 fileFive");

    expect($this->artisan('aoc:day-07 filesystem.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('Total size of all directories under 100000 is: 4012')
        ->expectsOutputToContain('Size of smallest directory to free up space: 29100001');
});
