<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-03');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-03 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the priorities of the rucksack lists', function () {
    Storage::fake('local');
    Storage::put('rucksacks.txt', "abcdca\ndaffin\nghabhp\nhElolsXQ\nHElOBOrS\nHELoKHvR\nTestingBQaseHk\ntEStINGSlINKyD\ntESTiNgWKNPdXM");

    expect($this->artisan('aoc:day-03 rucksacks.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The priorities for the duplicated items was: 192')
        ->expectsOutputToContain('The priorities for the badged groups was: 52');
});
