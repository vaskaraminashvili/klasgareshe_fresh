<?php

namespace App\Livewire;

use App\Exceptions\NoQuestionsAvailable;
use App\Models\Game;
use App\Services\GameSessionService;
use App\Services\PlayerResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kidzio')]
class Home extends Component
{
    /**
     * Starts a session for one game, or the mixed "daily" mission when `$type` is null.
     *
     * @return array{error?: string}
     */
    public function start(?string $type, int $level, GameSessionService $sessions, PlayerResolver $players): array
    {
        $game = $type ? Game::where('type', $type)->where('is_active', true)->firstOrFail() : null;

        try {
            $session = $sessions->start($players->resolve(), $game, $level);
        } catch (NoQuestionsAvailable $e) {
            return ['error' => $e->getMessage()];
        }

        $this->redirectRoute('play', ['session' => $session->uuid]);

        return [];
    }

    public function render(PlayerResolver $players): View
    {
        $player = $players->resolve();

        return view('livewire.home', [
            'config' => [
                'name' => $player->name ?? 'მეგობარო',
                'streak' => $player->streak(),
                'lit' => $player->activeDays(7),
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
