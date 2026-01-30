<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'solicitante']);
    Role::firstOrCreate(['name' => 'admin']);
});

afterEach(function () {
    Mockery::close();
});

function fakeGoogleUser(string $email, string $id = 'google-123', string $name = 'Test User'): void
{
    $providerUser = Mockery::mock(ProviderUser::class);
    $providerUser->shouldReceive('getId')->andReturn($id);
    $providerUser->shouldReceive('getEmail')->andReturn($email);
    $providerUser->shouldReceive('getName')->andReturn($name);
    $providerUser->shouldReceive('getAvatar')->andReturn('http://example.com/avatar.png');

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($providerUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn($provider);
}

test('existing user keeps current roles on google login', function () {
    $user = User::factory()->create(['email' => 'admin@gptservices.com']);
    $user->assignRole('admin');

    fakeGoogleUser('admin@gptservices.com', 'google-existing');

    $response = get(route('google.callback'));

    $response->assertRedirect(route('home'));
    expect(Auth::id())->toBe($user->id);

    $user->refresh();
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('solicitante'))->toBeFalse();
});

test('creates new user and assigns solicitante role on google login', function () {
    fakeGoogleUser('newuser@gptservices.com', 'google-new');

    $response = get(route('google.callback'));

    $response->assertRedirect(route('home'));

    $user = User::where('email', 'newuser@gptservices.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('solicitante'))->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

test('rejects login for non gptservices domain', function () {
    fakeGoogleUser('intruder@example.com', 'google-invalid');

    $response = get(route('google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors();

    expect(Auth::check())->toBeFalse();
    expect(User::where('email', 'intruder@example.com')->exists())->toBeFalse();
});
