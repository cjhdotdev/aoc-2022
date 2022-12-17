<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-09');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-09 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the number of trees visible', function () {
    Storage::fake('local');
    Storage::put('moves.txt', "R 4\nU 4\nL 3\nD 1\nR 4\nD 1\nL 5\nR 2");

    expect($this->artisan('aoc:day-09 moves.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The locations visited by the tail is: 13');
});
