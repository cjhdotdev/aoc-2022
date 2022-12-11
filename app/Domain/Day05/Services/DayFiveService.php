<?php

namespace App\Domain\Day05\Services;

use App\Domain\Day05\Collections\ColumnsCollection;
use App\Domain\Day05\DTO\ColumnsMovesDTO;
use App\Domain\Day05\DTO\MovementDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DayFiveService
{
    private bool $isMultiCrateCrane = false;

    public function findTopCratesForInput(string $input, bool $isMultiCrateCrane = false): string
    {
        $this->isMultiCrateCrane = $isMultiCrateCrane;

        $columnsMoves = $this->parseColumnsAndMoves($input);
        $columnsMoves->movements->each(fn ($move) => $columnsMoves->columns->transformWithMovement($move));

        return $columnsMoves->columns->findTopCrates()->implode('');
    }

    public function parseColumnsAndMoves(string $input): ColumnsMovesDTO
    {
        $columns = new ColumnsCollection();
        $moves = new Collection();

        Str::of($input)
            ->explode(PHP_EOL)
            ->filter(fn ($str) => Str::of(strval($str))->isEmpty() || Str::of(strval($str))->test('/(move|\[)/'))
            ->each(fn ($line) => Str::of(strval($line))->test('/move/')
                ? $moves->push($this->parseMoveLine(strval($line)))
                : $columns->addRow($this->parseStackLine(strval($line)))
            );

        return new ColumnsMovesDTO(
            $columns,
            $moves,
        );
    }

    /**
     * @param  string  $stackLine
     * @return Collection<int, string>
     */
    public function parseStackLine(string $stackLine): Collection
    {
        return Str::of($stackLine)
            ->split('//')
            ->filter()
            ->chunk(4)
            ->transform(fn ($codes) => $codes->splice(1, 1))
            ->flatten()
            ->map(fn ($code) => strval($code));
    }

    public function parseMoveLine(string $moveLine): MovementDTO
    {
        return Str::of($moveLine)
            ->matchAll('/([0-9]+)/')
            ->pipe(fn ($matches) => new MovementDTO(
                intval($matches->get(0)),
                intval($matches->get(1)),
                intval($matches->get(2)),
                $this->isMultiCrateCrane,
            ));
    }
}
