<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-08');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-08 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the number of trees visible', function () {
    Storage::fake('local');
    Storage::put('trees.txt', "98789\n78987\n92129\n76967\n98789");

    expect($this->artisan('aoc:day-08 trees.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The total number of visible trees is: 20')
        ->expectsOutputToContain('The best scenic score for a tree is: 8');
});
