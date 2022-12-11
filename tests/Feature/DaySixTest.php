<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-06');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-06 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('finds the crates on the top of each stack after movements', function () {
    Storage::fake('local');
    Storage::put('messenger.txt', 'abababababababdebabababacdefghijklmnopabababab');

    expect($this->artisan('aoc:day-06 messenger.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The position of the start-of-packet marker is: 16')
        ->expectsOutputToContain('The position of the start-of-message marker is: 36');
});
