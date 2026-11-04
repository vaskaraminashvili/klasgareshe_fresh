<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Container\Attributes\Scoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/** Anonymous identity: a random token in a long-lived cookie, no login and no personal data. */
#[Scoped]
class PlayerResolver
{
    public const COOKIE = 'kidzio_player';

    private ?Player $player = null;

    public function __construct(private readonly Request $request) {}

    public function resolve(): Player
    {
        if ($this->player) {
            return $this->player;
        }

        $token = $this->request->cookie(self::COOKIE);
        $player = is_string($token) && Str::isUuid($token) ? Player::where('token', $token)->first() : null;

        if (! $player) {
            $player = Player::create(['token' => (string) Str::uuid()]);
            Cookie::queue(self::COOKIE, $player->token, 60 * 24 * 365 * 5);
        }

        return $this->player = $player;
    }
}
