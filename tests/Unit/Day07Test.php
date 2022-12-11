<?php

namespace Tests\Unit;

use App\Domain\Day07\DTO\DirectoryDTO;
use App\Domain\Day07\Enums\LineEnum;
use App\Domain\Day07\Services\DaySevenService;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->service = new DaySevenService());

function prepopulateDirectories(DaySevenService $service)
{
    $service->processCommandLine('$ cd folderOne');
    $service->processCommandLine('$ ls');
    $service->processCommandLine('1001 fileOne.txt');
    $service->processCommandLine('dir folderOneOne');
    $service->processCommandLine('dir folderOneTwo');
    $service->processCommandLine('$ cd folderOneOne');
    $service->processCommandLine('$ ls');
    $service->processCommandLine('101 fileOneOne.txt');
    $service->processCommandLine('202 fileOneTwo.txt');
    $service->processCommandLine('$ cd ..');
    $service->processCommandLine('$ cd folderOneTwo');
    $service->processCommandLine('$ ls');
    $service->processCommandLine('401 fileTwoOne.txt');
    $service->processCommandLine('$ cd ..');
    $service->processCommandLine('$ cd ..');
    $service->processCommandLine('$ cd folderTwo');
    $service->processCommandLine('$ ls');
    $service->processCommandLine('505 fileTwo.txt');
}

it('creates a directory instance', function () {
    $directory = new DirectoryDTO('testing');
    expect($directory)
        ->toBeInstanceOf(DirectoryDTO::class)
        ->directoryName->toBeString()->toEqual('testing');
});

it('creates a child directory instance', function () {
    $directory = new DirectoryDTO('testing');
    $directory->addDirectory('child');
    expect($directory->findDirectory('child'))
        ->toBeInstanceOf(DirectoryDTO::class)
        ->directoryName->toBeString()->toEqual('child');
});

it('adds files to directories and reports the total size', function () {
    $directory = new DirectoryDTO('testing');
    $directory->addFile('file1.txt', 1024);
    $directory->addFile('file2.txt', 2048);
    expect($directory->getTotalSize())
        ->toBeInt()
        ->toEqual(3072);
});

it('calculates total size for child directories', function () {
    $directory = new DirectoryDTO('testing');
    $childDirOne = $directory->addDirectory('childDir1');
    $childDirOne->addFile('childFile1.txt', 123);
    $childDirOne->addFile('childFile2.txt', 345);
    $childDirTwo = $childDirOne->addDirectory('childDir2');
    $childDirTwo->addFile('childFile3.txt', 678);
    $childDirThree = $directory->addDirectory('childDir3');
    $childDirThree->addFile('childFile4.txt', 901);

    expect($directory->getTotalSize())
        ->toBeInt()
        ->toEqual(2047);
});

it('identifies the type of line being parsed', function (
    string $commandLine,
    LineEnum $expectedLine,
) {
    expect($this->service->identifyCommandLineType($commandLine))
        ->toBeInstanceOf(LineEnum::class)
        ->toEqual($expectedLine);
})->with([
    ['$ cd /', LineEnum::Command],
    ['$ ls', LineEnum::Command],
    ['dir abcd', LineEnum::Directory],
    ['12345 zxy.abc', LineEnum::File],
]);

it('performs a change directory command', function () {
    $this->service->processCommandLine('$ cd /');
    $this->service->processCommandLine('$ cd testing');
    $this->service->processCommandLine('$ cd gnitset');
    expect($this->service->currentDirectory)
        ->toBeInstanceOf(DirectoryDTO::class)
        ->directoryName->toEqual('gnitset');

    $this->service->processCommandLine('$ cd ..');
    expect($this->service->currentDirectory)
        ->toBeInstanceOf(DirectoryDTO::class)
        ->directoryName->toEqual('testing');

    $this->service->processCommandLine('$ cd ..');
    expect($this->service->currentDirectory)
        ->toBeInstanceOf(DirectoryDTO::class)
        ->directoryName->toEqual('');
});

it('performs correct actions during a directory listing', function () {
    $this->service->processCommandLine('$ cd testing');
    $this->service->processCommandLine('$ ls');
    $this->service->processCommandLine('dir childOne');
    $this->service->processCommandLine('1234 fileOne.txt');
    $this->service->processCommandLine('$ cd /');

    expect($this->service->currentDirectory)
        ->directoryName->toEqual('')
        ->parent->toBeNull()
        ->findDirectory('testing')->toBeInstanceOf(DirectoryDTO::class)
        ->findDirectory('testing')->findDirectory('childOne')->toBeInstanceOf(DirectoryDTO::class)
        ->findDirectory('testing')->findDirectory('childOne')->getTotalSize()->toBeInt()->toEqual(0)
        ->findDirectory('testing')->getTotalSize()->toBeInt()->toEqual(1234)
        ->getTotalSize()->toBeInt()->toEqual(1234);
});

it('can calculate the total size of all directories below a certain threshold', function () {
    prepopulateDirectories($this->service);

    expect($this->service->calculateDirectoriesUnder(1000))
        ->toBeInt()
        ->toEqual(1209);
});

it('finds all the folders total sizes', function () {
    prepopulateDirectories($this->service);

    expect($this->service->getAllDirectorySizes())
        ->toBeInstanceOf(Collection::class)
        ->sortDesc()->values()->toMatchArray([2210, 1705, 505, 401, 303]);
});

it('calculates remaining space', function () {
    prepopulateDirectories($this->service);

    expect($this->service->calculateFreeSpace())
        ->toBeInt()
        ->toEqual(DaySevenService::SPACE_TOTAL - 2210);
});

it('finds the size of the first directory under a given threshold', function () {
    prepopulateDirectories($this->service);

    expect($this->service)
        ->findFirstDirectoryBiggerThan(300)->toBeInt()->toEqual(303)
        ->findFirstDirectoryBiggerThan(500)->toBeInt()->toEqual(505)
        ->findFirstDirectoryBiggerThan(2000)->toBeInt()->toEqual(2210);
});
