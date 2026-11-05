<?php

namespace App\Livewire;

use App\Enums\SessionStatus;
use App\Models\GameSession;
use App\Services\GameSessionService;
use App\Services\PlayerResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Thin shell around GameSessionService. Public properties are only a display cache for the
 * browser; every action reloads the session from the database and checks it belongs to the player.
 */
#[Layout('layouts.kidzio')]
class Play extends Component
{
    #[Locked]
    public string $uuid = '';

    /** @var array<string, mixed> */
    #[Locked]
    public array $question = [];

    #[Locked]
    public int $total = 0;

    #[Locked]
    public bool $finished = false;

    /** @var array<string, mixed> */
    #[Locked]
    public array $summary = [];

    public function mount(GameSession $session, PlayerResolver $players, GameSessionService $sessions): void
    {
        abort_unless($session->player_id === $players->resolve()->id, 404);

        $this->uuid = $session->uuid;
        $this->total = $session->total_questions;
        $this->load($session, $sessions);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{hit: bool, extra: array<string, mixed>}
     */
    public function step(array $input, GameSessionService $sessions): array
    {
        $result = $sessions->step($this->ownSession(), $input);
        $this->question = $result['state'];

        return ['hit' => $result['hit'], 'extra' => $result['extra']];
    }

    public function revealClue(GameSessionService $sessions): void
    {
        $this->question = $sessions->revealClue($this->ownSession());
    }

    /** @return array<string, mixed> */
    public function check(mixed $input, GameSessionService $sessions): array
    {
        return $sessions->check($this->ownSession(), is_int($input) ? $input : null);
    }

    public function next(GameSessionService $sessions): void
    {
        $this->load($this->ownSession(), $sessions);
    }

    public function playAgain(GameSessionService $sessions, PlayerResolver $players): void
    {
        $this->startNew($sessions, $players, 0);
    }

    public function playNext(GameSessionService $sessions, PlayerResolver $players): void
    {
        $this->startNew($sessions, $players, 1);
    }

    public function render(): View
    {
        return view('livewire.play');
    }

    private function startNew(GameSessionService $sessions, PlayerResolver $players, int $levelStep): void
    {
        $old = $this->ownSession();
        $session = $sessions->start($players->resolve(), $old->game, min(10, $old->difficulty + $levelStep), $old->subject, $old->grade);

        $this->redirectRoute('play', ['session' => $session->uuid]);
    }

    private function ownSession(): GameSession
    {
        return GameSession::where('uuid', $this->uuid)
            ->where('player_id', app(PlayerResolver::class)->resolve()->id)
            ->with('game')
            ->firstOrFail();
    }

    private function load(GameSession $session, GameSessionService $sessions): void
    {
        $item = $sessions->currentItem($session);

        if ($item) {
            $this->question = $sessions->questionState($item);

            return;
        }

        if ($session->status === SessionStatus::Active) {
            $sessions->complete($session);
        }

        $session->refresh();
        $this->finished = true;
        $this->question = [];
        $this->summary = [
            'score' => $session->score,
            'correct' => $session->correct_count,
            'total' => $session->total_questions,
            'accuracy' => $session->accuracyPercent(),
            'stars' => $session->stars(),
            'duration' => $session->duration_seconds,
            'streak' => $session->player->streak(),
            'game' => $session->game?->name ?? 'დღევანდელი მისია',
            'level' => min(10, $session->difficulty + 1),
        ];
    }
}
