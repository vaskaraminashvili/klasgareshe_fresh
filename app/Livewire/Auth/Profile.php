<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.kidzio')]
#[Title('პროფილი')]
class Profile extends Component
{
    public string $name = '';

    public ?int $grade = null;

    public ?string $avatar = null;

    public bool $saved = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->grade = $user->grade;
        $this->avatar = $user->avatar;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:60'],
            'grade' => ['nullable', 'integer', 'between:1,12'],
            'avatar' => ['nullable', Rule::in(config('avatars'))],
        ], ['required' => 'შეავსე ეს ველი.']);

        Auth::user()->update($data);
        $this->saved = true;
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('welcome');
    }

    public function render(): View
    {
        return view('livewire.auth.profile', ['user' => Auth::user(), 'avatars' => config('avatars')]);
    }
}
