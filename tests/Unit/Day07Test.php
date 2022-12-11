<?php

namespace Tests\Unit;

use App\Domain\Day07\DTO\DirectoryDTO;
use App\Domain\Day07\Enums\LineEnum;
use App\Domain\Day07\Services\DaySevenService;

beforeEach(fn () => $this->service = new DaySevenService());

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
    $this->service->processCommandLine('$ cd folderOne');
    $this->service->processCommandLine('$ ls');
    $this->service->processCommandLine('1001 fileOne.txt');
    $this->service->processCommandLine('dir folderOneOne');
    $this->service->processCommandLine('dir folderOneTwo');
    $this->service->processCommandLine('$ cd folderOneOne');
    $this->service->processCommandLine('$ ls');
    $this->service->processCommandLine('101 fileOneOne.txt');
    $this->service->processCommandLine('202 fileOneTwo.txt');
    $this->service->processCommandLine('$ cd ..');
    $this->service->processCommandLine('$ cd folderOneTwo');
    $this->service->processCommandLine('$ ls');
    $this->service->processCommandLine('401 fileTwoOne.txt');
    $this->service->processCommandLine('$ cd ..');
    $this->service->processCommandLine('$ cd ..');
    $this->service->processCommandLine('$ cd folderTwo');
    $this->service->processCommandLine('$ ls');
    $this->service->processCommandLine('505 fileTwo.txt');

    expect($this->service->calculateDirectoriesUnder(1000))
        ->toBeInt()
        ->toEqual(1209);
});
