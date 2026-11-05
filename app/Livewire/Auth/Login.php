<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.kidzio')]
#[Title('შესვლა')]
class Login extends Component
{
    private const MAX_ATTEMPTS = 5;

    /** Username or email. */
    public string $login = '';

    public string $password = '';

    public function authenticate(): void
    {
        $this->validate(
            ['login' => ['required', 'string', 'max:255'], 'password' => ['required', 'string']],
            ['required' => 'შეავსე ეს ველი.'],
        );

        $throttleKey = Str::lower($this->login).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $this->addError('login', 'ბევრჯერ სცადე. დაელოდე '.RateLimiter::availableIn($throttleKey).' წამს.');

            return;
        }

        $field = str_contains($this->login, '@') ? 'email' : 'username';

        if (! Auth::attempt([$field => $this->login, 'password' => $this->password], true)) {
            RateLimiter::hit($throttleKey);
            $this->addError('login', 'მეტსახელი ან პაროლი არასწორია. სცადე ისევ.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        $this->redirectIntended(route('home'));
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
