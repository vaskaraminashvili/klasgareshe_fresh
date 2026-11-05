<?php

namespace App\Livewire;

use App\Exceptions\NoQuestionsAvailable;
use App\Models\Game;
use App\Models\Question;
use App\Services\GameSessionService;
use App\Services\PlayerResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kidzio')]
class Home extends Component
{
    /** Georgian chip labels for the subject keys gen.mjs writes; unknown keys show as they are. */
    private const SUBJECT_LABELS = [
        'math' => 'მათემატიკა',
        'history' => 'ისტორია',
        'nature' => 'ბუნება',
        'physics' => 'ფიზიკა',
        'chemistry' => 'ქიმია',
        'biology' => 'ბიოლოგია',
        'geography' => 'გეოგრაფია',
        'georgian' => 'ქართული',
        'english' => 'ინგლისური',
    ];

    /**
     * Starts a session for one game, or the mixed "daily" mission when `$type` is null.
     * A null subject or grade means "all".
     *
     * @return array{error?: string}
     */
    public function start(?string $type, int $level, GameSessionService $sessions, PlayerResolver $players, ?string $subject = null, ?int $grade = null): array
    {
        $game = $type ? Game::where('type', $type)->where('is_active', true)->firstOrFail() : null;
        $grade = auth()->user()?->grade ?? ($grade !== null && $grade >= 1 && $grade <= 12 ? $grade : null);

        try {
            $session = $sessions->start($players->resolve(), $game, $level, $subject ? mb_substr($subject, 0, 50) : null, $grade);
        } catch (NoQuestionsAvailable $e) {
            return ['error' => $e->getMessage()];
        }

        $this->redirectRoute('play', ['session' => $session->uuid]);

        return [];
    }

    public function mount(): void
    {
        if (auth()->user()?->grade === null && auth()->check()) {
            $this->redirectRoute('grade');
        }
    }

    public function render(PlayerResolver $players): View
    {
        $player = $players->resolve();

        return view('livewire.home', [
            'config' => [
                'name' => auth()->user()?->name ?? 'მეგობარო',
                'streak' => $player->streak(),
                'lit' => $player->activeDays(7),
                'subjects' => Question::where('is_active', true)->whereNotNull('subject')->distinct()->orderBy('subject')->pluck('subject')
                    ->map(fn (string $subject) => ['value' => $subject, 'label' => self::SUBJECT_LABELS[$subject] ?? $subject])->all(),
                'grade' => auth()->user()?->grade,
                'games' => Game::where('is_active', true)->orderBy('sort')->get()
                    ->map(fn (Game $game) => [
                        'type' => $game->type->value,
                        'name' => $game->name,
                        'icon' => $game->config['icon'] ?? 'i-star',
                        'accent' => $game->config['accent'] ?? 'a1',
                    ])->all(),
            ],
        ]);
    }
}
