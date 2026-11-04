<?php

namespace Database\Seeders;

use App\Enums\GameType;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

/**
 * Imports questions generated from textbooks (book-extract/gen.mjs) from database/seeders/books/*.json.
 * Safe to re-run: questions already in the database (same game, prompt and payload) are skipped.
 */
class BookQuestionSeeder extends Seeder
{
    public string $directory = __DIR__.'/books';

    public function run(): void
    {
        $gameIds = Game::pluck('id', 'type')->mapWithKeys(fn ($id, $type) => [$type instanceof GameType ? $type->value : $type => $id])->all();

        $existing = Question::whereIn('game_id', $gameIds)->get(['game_id', 'prompt', 'payload'])
            ->mapWithKeys(fn (Question $question) => [$this->key($question->game_id, $question->prompt, $question->payload) => true])
            ->all();

        foreach (File::glob($this->directory.'/*.json') as $file) {
            $rows = json_decode(File::get($file), true, flags: JSON_THROW_ON_ERROR);
            $imported = 0;

            // Check the whole file first so a bad file imports nothing rather than half of itself.
            foreach ($rows as $index => $row) {
                $where = basename($file).' row '.$index;
                $this->validate($row, $where);
                $gameIds[$row['type']] ?? throw new InvalidArgumentException("$where: no game of type {$row['type']}; run GameSeeder first.");
            }

            foreach ($rows as $row) {
                $gameId = $gameIds[$row['type']];
                $key = $this->key($gameId, $row['prompt'], $row['payload']);

                if (isset($existing[$key])) {
                    continue;
                }

                Question::create([
                    'game_id' => $gameId,
                    'difficulty' => max(1, min(10, (int) $row['difficulty'])),
                    'prompt' => $row['prompt'],
                    'payload' => $row['payload'],
                    'praise' => $row['praise'],
                    'hint' => $row['hint'],
                ]);
                $existing[$key] = true;
                $imported++;
            }

            $this->command?->info(basename($file).": $imported new questions");
        }
    }

    /**
     * Rejects rows the game handlers could not play, so a bad file never reaches children.
     *
     * @param  array{type?: string, prompt?: string, praise?: string, hint?: string, difficulty?: int, payload?: array<string, mixed>}  $row
     */
    private function validate(array $row, string $where): void
    {
        foreach (['type', 'prompt', 'praise', 'hint', 'difficulty', 'payload'] as $field) {
            if (! isset($row[$field])) {
                throw new InvalidArgumentException("$where: missing $field.");
            }
        }

        $payload = $row['payload'];
        $isValid = match ($row['type']) {
            'multiple' => is_array($payload['options'] ?? null) && count($payload['options']) >= 2 && isset($payload['options'][$payload['answer'] ?? -1]),
            'gap' => is_array($payload['words'] ?? null) && in_array($payload['answer'] ?? null, $payload['words'], true) && isset($payload['before'], $payload['after']),
            'truefalse' => is_bool($payload['answer'] ?? null),
            default => false,
        };

        if (! $isValid) {
            throw new InvalidArgumentException("$where: invalid {$row['type']} payload.");
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function key(int $gameId, string $prompt, array $payload): string
    {
        return $gameId.'|'.$prompt.'|'.json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
