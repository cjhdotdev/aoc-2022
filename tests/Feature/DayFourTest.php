<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-04');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-04 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the priorities of the rucksack lists', function () {
    Storage::fake('local');
    Storage::put('sectors.txt', "1-10,2-9\n10-20,12-12\n20-30,29-40\n100-150,110-111\n200-300,250-350\n2-3,1-10\n4-4,1-20");

    expect($this->artisan('aoc:day-04 sectors.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The number of sectors contained in other sectors is: 5')
        ->expectsOutputToContain('The number of overlapped sectors is: 7');
});
