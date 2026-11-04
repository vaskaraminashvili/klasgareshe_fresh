<?php

use App\Enums\GameType;
use App\Models\Question;
use Database\Seeders\BookQuestionSeeder;
use Database\Seeders\GameSeeder;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->seed(GameSeeder::class);
    $this->directory = storage_path('framework/testing/books-'.uniqid());
    File::ensureDirectoryExists($this->directory);
});

afterEach(function () {
    File::deleteDirectory($this->directory);
});

/** @param  list<array<string, mixed>>  $rows */
function runBookSeeder(string $directory, array $rows): void
{
    File::put($directory.'/book.json', json_encode($rows, JSON_UNESCAPED_UNICODE));
    $seeder = app(BookQuestionSeeder::class);
    $seeder->directory = $directory;
    $seeder->run();
}

/** @return list<array<string, mixed>> */
function bookRows(): array
{
    return [
        ['type' => 'multiple', 'difficulty' => 2, 'lesson' => '§2', 'prompt' => 'რამდენია 24 · 3?', 'payload' => ['options' => ['72', '62', '63'], 'answer' => 0], 'praise' => 'სწორია! 24 · 3 = 72.', 'hint' => 'გაამრავლე.'],
        ['type' => 'gap', 'difficulty' => 3, 'lesson' => '§2', 'prompt' => 'ჩაწერე რიცხვი:', 'payload' => ['before' => '4 ყუთში სულ', 'after' => 'ფანქარია.', 'words' => ['448', '438'], 'answer' => '448'], 'praise' => 'სწორია!', 'hint' => '112 · 4.'],
        ['type' => 'truefalse', 'difficulty' => 14, 'lesson' => '§1', 'prompt' => '7 · 8 = 54', 'payload' => ['answer' => false], 'praise' => 'სწორია!', 'hint' => '7 · 8 = 56.'],
    ];
}

test('imports every question type from book files into the matching game', function () {
    runBookSeeder($this->directory, bookRows());

    expect(Question::count())->toBe(3);

    $gap = Question::whereHas('game', fn ($query) => $query->where('type', GameType::Gap->value))->sole();
    expect($gap->payload['answer'])->toBe('448')
        ->and(Question::where('prompt', '7 · 8 = 54')->value('difficulty'))->toBe(10);
});

test('running the import again does not duplicate questions', function () {
    runBookSeeder($this->directory, bookRows());
    runBookSeeder($this->directory, bookRows());

    expect(Question::count())->toBe(3);
});

test('a file with an unplayable question is rejected and imports nothing', function () {
    $rows = bookRows();
    $rows[1]['payload']['answer'] = '999';

    expect(fn () => runBookSeeder($this->directory, $rows))
        ->toThrow(InvalidArgumentException::class, 'book.json row 1: invalid gap payload.');
    expect(Question::count())->toBe(0);
});
