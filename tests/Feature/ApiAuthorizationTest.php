<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access protected API endpoints', function () {
    $this->getJson('/api/user')
        ->assertUnauthorized();

    $this->getJson('/api/assets')
        ->assertUnauthorized();
});

test('users without asset permissions cannot list assets', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token', ['server:read']);

    $this->withToken($token->plainTextToken)
        ->getJson('/api/assets')
        ->assertForbidden();
});

test('users with asset permissions can list assets', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('view_any_asset', 'web');

    $user->givePermissionTo('view_any_asset');

    $token = $user->createToken('test-token', ['server:read']);

    $this->withToken($token->plainTextToken)
        ->getJson('/api/assets')
        ->assertOk()
        ->assertJsonStructure([
            'data',
        ]);
});

test('users cannot list assets without the required token ability', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('view_any_asset', 'web');

    $user->givePermissionTo('view_any_asset');

    $token = $user->createToken('test-token', []);

    $this->withToken($token->plainTextToken)
        ->getJson('/api/assets')
        ->assertForbidden();
});

test('asset policy denies users without permission', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Asset::class))->toBeFalse();
});

test('asset policy allows users with permission', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('view_any_asset', 'web');

    $user->givePermissionTo('view_any_asset');

    expect($user->can('viewAny', Asset::class))->toBeTrue();
});