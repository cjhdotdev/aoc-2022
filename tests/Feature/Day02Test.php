<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-02');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-02 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('calculates the score of the rock-paper-scissors hands played', function () {
    Storage::fake('local');
    Storage::put('hands.txt', "A X\nA Y\nA Z\nB X\nB Y\nB Z\nC X\nC Y\nC Z");

    expect($this->artisan('aoc:day-02 hands.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The score from playing all the rock-paper-scissors hands was: 45')
        ->expectsOutputToContain('The score from playing the hands with outcomes listed was: 45');
});
