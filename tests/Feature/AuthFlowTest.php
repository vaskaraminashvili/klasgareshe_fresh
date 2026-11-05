<?php

use App\Livewire\Auth\Login;
use App\Livewire\ChooseGrade;
use App\Livewire\Auth\Profile;
use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
});

it('shows the info screen to guests and sends signed-in users to the games', function () {
    $this->get('/')->assertOk()->assertSee('კლასგარეშე');

    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('home'));
});

it('sends a signed-in user without a grade to pick one before the games', function () {
    $user = User::factory()->create(['grade' => null]);

    $this->actingAs($user)->get(route('home'))->assertRedirect(route('grade'));

    Livewire::actingAs($user)->test(ChooseGrade::class)
        ->call('save')->assertHasErrors('grade')
        ->set('grade', 5)
        ->assertSee('ისტორია')
        ->call('save')->assertRedirect(route('home'));

    expect($user->fresh()->grade)->toBe(5);
    $this->actingAs($user->fresh())->get(route('home'))->assertOk()->assertDontSee('ყველა კლასი');
});

it('registers a user, logs them in and sends them home', function () {
    Livewire::test(Register::class)
        ->set('name', 'ნინო')
        ->set('username', 'nino_7')
        ->set('email', 'nino@example.com')
        ->set('password', 'secret-pass')
        ->set('gender', 'female')
        ->set('grade', 4)
        ->set('avatar', 'avatar-2')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $user = User::firstWhere('username', 'nino_7');

    expect($user)->grade->toBe(4)->avatar->toBe('avatar-2')->rank->toBe(0);
    $this->assertAuthenticatedAs($user);
});

it('registers without a grade or an avatar', function () {
    Livewire::test(Register::class)
        ->set('name', 'ლუკა')
        ->set('username', 'luka')
        ->set('email', 'luka@example.com')
        ->set('password', 'secret-pass')
        ->set('gender', 'male')
        ->call('register')
        ->assertHasNoErrors();

    expect(User::firstWhere('username', 'luka'))->grade->toBeNull()->avatar->toBeNull();
});

it('rejects a taken username, a bad gender, an unknown avatar and a short password', function () {
    User::factory()->create(['username' => 'taken']);

    Livewire::test(Register::class)
        ->set('name', 'x')
        ->set('username', 'taken')
        ->set('email', 'x@example.com')
        ->set('password', 'short')
        ->set('gender', 'other')
        ->set('avatar', 'not-in-list')
        ->call('register')
        ->assertHasErrors(['username', 'password', 'gender', 'avatar']);

    expect(User::count())->toBe(1);
});

it('logs in with a username or an email', function (string $field) {
    $user = User::factory()->create(['password' => 'secret-pass']);

    Livewire::test(Login::class)
        ->set('login', $user->{$field})
        ->set('password', 'secret-pass')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
})->with(['username', 'email']);

it('refuses a wrong password and locks out after five tries', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    foreach (range(1, 6) as $_) {
        Livewire::test(Login::class)
            ->set('login', $user->username)
            ->set('password', 'wrong')
            ->call('authenticate')
            ->assertHasErrors('login');
    }

    Livewire::test(Login::class)
        ->set('login', $user->username)
        ->set('password', 'secret-pass')
        ->call('authenticate')
        ->assertHasErrors('login');

    $this->assertGuest();
});

it('keeps guests out of the profile and signed-in users out of the forms', function () {
    $this->get('/profile')->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create());
    $this->get('/login')->assertRedirect();
    $this->get('/register')->assertRedirect();
    $this->get('/profile')->assertOk()->assertSee('გასვლა');
});

it('updates the profile and logs out', function () {
    $user = User::factory()->create(['grade' => null, 'avatar' => null]);
    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', 'ახალი')
        ->set('grade', 6)
        ->set('avatar', 'avatar-3')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh())->name->toBe('ახალი')->grade->toBe(6)->avatar->toBe('avatar-3');

    Livewire::test(Profile::class)->call('logout')->assertRedirect(route('welcome'));
    $this->assertGuest();
});
