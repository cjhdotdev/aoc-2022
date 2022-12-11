<?php

namespace Tests\Unit;

use App\Domain\Day01\Services\DayOneService;

beforeEach(fn () => $this->service = new DayOneService());

it('calculates the calories for a single elf', function () {
    expect($this->service->calculateForElf("10\n20\n30"))
        ->toBeInt()
        ->toEqual(60);
});

it('handles non-integer values in the provided elf calories', function () {
    expect($this->service->calculateForElf("5\ninvalid\ndata\n10\n15"))
        ->toBeInt()
        ->toEqual(30);
});

it('calculates the calories for multiple elves', function () {
    expect($this->service->calculateMultipleElves("10\n15\n25\n\n50\n60\n70\n\n1\n2\n\n5"))
        ->toBeCollection()
        ->toMatchArray([50, 180, 3, 5]);
});

it('finds the elf with the highest calories', function () {
    expect($this->service->findHighest("1\n2\n3\n\n20\n50\n80\n125\n\n100\n150\n\n10\n20\n30\n\n45\n60"))
        ->toBeInt()
        ->toEqual(275);
});

it('finds the top three calories for given elves', function () {
    expect($this->service->findHighest("10\n35\n\n20\n20\n25\n\n30\n30\n\n50\n90\n\n150\n\n250\n\n1\n2", 3))
        ->toBeInt()
        ->toEqual(540);
});
