<?php

use Illuminate\Support\Facades\Route;

test('the home page redirects to the admin panel', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});

test('the admin panel requires authentication', function () {
    $response = $this->get('/admin');

    $response->assertRedirect();

    expect(Route::has('filament.admin.auth.login'))->toBeTrue();
});