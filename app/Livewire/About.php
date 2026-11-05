<?php

namespace App\Livewire;

use App\Enums\GameType;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.kidzio')]
#[Title('რა არის კლასგარეშე')]
class About extends Component
{
    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirectRoute('home');
        }
    }

    public function render(): View
    {
        return view('livewire.about', [
            'gameLabels' => array_map(fn (GameType $type): string => $type->label(), GameType::cases()),
        ]);
    }
}
