<?php

namespace App\Domain\DayThree\DTO;

use Illuminate\Support\Collection;

final class PackagingDTO
{
    /**
     * @param  Collection<int, string>  $compartmentOne
     * @param  Collection<int, string>  $compartmentTwo
     */
    public function __construct(
        public readonly Collection $compartmentOne,
        public readonly Collection $compartmentTwo,
    ) {
        //
    }
}
