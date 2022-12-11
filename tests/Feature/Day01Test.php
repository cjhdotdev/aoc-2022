<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-01');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-01 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the highest elf calories from an input file', function () {
    Storage::fake('local');
    Storage::put('calories.txt', "1\n2\n3\n\n5\n6\n\n10\n20\n\n100\n\n90\n50\n30\n20\n\n9\n8");

    expect($this->artisan('aoc:day-01 calories.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The most amount of calories consumed by an elf is: 190')
        ->expectsOutputToContain('The total of the top three elves calories is: 320');
});
