<?php

namespace App\Livewire\Auth;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.kidzio')]
#[Title('რეგისტრაცია')]
class Register extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $gender = '';

    public ?int $grade = null;

    public ?string $avatar = null;

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[\p{L}\p{N}_-]+$/u', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'grade' => ['nullable', 'integer', 'between:1,12'],
            'avatar' => ['nullable', Rule::in(config('avatars'))],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'required' => 'შეავსე ეს ველი.',
            'username.min' => 'მეტსახელი მინიმუმ 3 სიმბოლოა.',
            'username.regex' => 'გამოიყენე ასოები, ციფრები, _ ან -.',
            'username.unique' => 'ეს მეტსახელი დაკავებულია. სცადე სხვა.',
            'email.email' => 'ჩაწერე სწორი ელფოსტა.',
            'email.unique' => 'ამ ელფოსტით ანგარიში უკვე არსებობს. სცადე შესვლა.',
            'password.min' => 'პაროლი მინიმუმ 8 სიმბოლოა.',
            'gender.required' => 'აირჩიე ერთი.',
            'gender.enum' => 'აირჩიე ერთი.',
        ];
    }

    public function register(): void
    {
        $user = User::create($this->validate());

        Auth::login($user, true);
        session()->regenerate();

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('livewire.auth.register', ['avatars' => config('avatars')]);
    }
}
