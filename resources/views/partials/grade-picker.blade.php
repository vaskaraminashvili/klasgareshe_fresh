{{-- Needs the component's public `$grade`; click again on the chosen one to clear it. --}}
<div class="grid gap-2">
    <p class="field-label" id="grade-label">კლასი <span class="font-semibold text-onbg-soft">(არასავალდებულო)</span></p>
    <div class="chip-row" role="group" aria-labelledby="grade-label">
        @foreach (range(1, 12) as $g)
            <button type="button" class="chip" aria-pressed="{{ $grade === $g ? 'true' : 'false' }}"
                    wire:click="$set('grade', {{ $grade === $g ? 'null' : $g }})">{{ $g }}</button>
        @endforeach
    </div>
    @error('grade') <p class="field-error" role="alert">{{ $message }}</p> @enderror
</div>
