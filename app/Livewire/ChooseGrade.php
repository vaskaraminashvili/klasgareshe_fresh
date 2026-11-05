<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.kidzio')]
#[Title('აირჩიე კლასი')]
class ChooseGrade extends Component
{
    public ?int $grade = null;

    public function mount(): void
    {
        $this->grade = Auth::user()->grade;
    }

    /**
     * Placeholder curriculum until real subject lists per grade exist.
     *
     * @return list<string>
     */
    public static function subjectsFor(?int $grade): array
    {
        return match (true) {
            $grade === null => [],
            $grade <= 4 => ['მათემატიკა', 'ქართული', 'ბუნება', 'ინგლისური'],
            $grade <= 6 => ['მათემატიკა', 'ქართული', 'ბუნება', 'ინგლისური', 'ისტორია', 'გეოგრაფია'],
            default => ['მათემატიკა', 'ქართული', 'ინგლისური', 'ისტორია', 'გეოგრაფია', 'ფიზიკა', 'ქიმია', 'ბიოლოგია'],
        };
    }

    public function save(): void
    {
        $data = $this->validate(['grade' => ['required', 'integer', 'between:1,12']], ['required' => 'აირჩიე კლასი.']);

        Auth::user()->update($data);

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('livewire.choose-grade', ['subjects' => self::subjectsFor($this->grade)]);
    }
}
