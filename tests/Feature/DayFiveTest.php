<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;

it('requires a filename input', function () {
    $this->artisan('aoc:day-05');
})->throws(\RuntimeException::class, 'Not enough arguments (missing: "filename").');

it('throws an exception if the file does not exist', function () {
    expect($this->artisan('aoc:day-05 not-exists.txt'))
        ->assertFailed();
})->throws(\RuntimeException::class, 'The specified file does not exist');

it('finds the crates on the top of each stack after movements', function () {
    Storage::fake('local');
    Storage::put('crates.txt', "[A]     [C] [D]     [F]     [H]\n[I] [J] [K] [L]     [M]     [O]\n".
        "[P] [Q] [R] [S] [T] [U]     [W]\n[X] [Y] [Z] [A] [B] [C] [D] [E]\n 1   2   3   4   5   6   7   8 \n\n".
        "move 2 from 1 to 5\nmove 2 from 8 to 2\nmove 1 from 6 to 3\nmove 3 from 4 to 7");

    expect($this->artisan('aoc:day-05 crates.txt'))
        ->assertSuccessful()
        ->expectsOutputToContain('The crates on the top of the stacks are: POFAIMSW');
});
