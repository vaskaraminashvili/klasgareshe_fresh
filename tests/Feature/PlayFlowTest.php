<?php

use App\Enums\GameType;
use App\Livewire\Home;
use App\Livewire\Play;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\Player;
use App\Services\GameSessionService;
use Database\Seeders\GameSeeder;
use Database\Seeders\QuestionSeeder;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([GameSeeder::class, QuestionSeeder::class]);
    $this->withoutVite();
    $this->player = Player::create(['token' => (string) Str::uuid()]);
});

it('opens without login and hands out an anonymous player cookie', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('სირთულის დონე')
        ->assertCookie('kidzio_player');

    expect(Player::count())->toBe(2);
});

it('starts a session from the home screen and redirects to it', function () {
    Livewire::withCookie('kidzio_player', $this->player->token)
        ->test(Home::class)
        ->call('start', 'multiple', 4)
        ->assertRedirect(route('play', GameSession::first()));

    expect(GameSession::first())
        ->player_id->toBe($this->player->id)
        ->difficulty->toBe(4);
});

it('plays a whole session through the Livewire component and saves a short name', function () {
    $session = app(GameSessionService::class)->start($this->player, Game::where('type', GameType::TrueFalse->value)->first(), 3);

    $component = Livewire::withCookie('kidzio_player', $this->player->token)
        ->test(Play::class, ['session' => $session]);

    expect($component->get('question'))->not->toHaveKey('answer');

    $total = $component->get('total');
    for ($i = 0; $i < $total; $i++) {
        $item = app(GameSessionService::class)->currentItem($session);
        $component->call('check', $item->question->payload['answer'] ? 0 : 1)->call('next');
    }

    $component->assertSet('finished', true)
        ->assertSet('summary.correct', $total)
        ->assertSet('summary.stars', 3)
        ->call('saveName', 'ნინო')
        ->assertSet('playerName', 'ნინო');

    expect($this->player->fresh()->name)->toBe('ნინო');
});

it('rejects names longer than nine letters', function () {
    $session = app(GameSessionService::class)->start($this->player, Game::where('type', 'multiple')->first(), 1);

    Livewire::withCookie('kidzio_player', $this->player->token)
        ->test(Play::class, ['session' => $session])
        ->call('saveName', 'ძალიანგრძელისახელი')
        ->assertSet('playerName', '');

    expect($this->player->fresh()->name)->toBeNull();
});

it('keeps the same questions after a page refresh', function () {
    $session = app(GameSessionService::class)->start($this->player, Game::where('type', 'multiple')->first(), 6);
    $cookie = ['kidzio_player' => $this->player->token];

    $first = Livewire::withCookies($cookie)->test(Play::class, ['session' => $session])->get('question');
    $second = Livewire::withCookies($cookie)->test(Play::class, ['session' => $session])->get('question');

    expect($second)->toBe($first);
});

it('hides a session from other players', function () {
    $session = app(GameSessionService::class)->start($this->player, Game::where('type', 'multiple')->first(), 1);
    $stranger = Player::create(['token' => (string) Str::uuid()]);

    $this->withUnencryptedCookie('kidzio_player', $stranger->token)
        ->get(route('play', $session))
        ->assertNotFound();
});
