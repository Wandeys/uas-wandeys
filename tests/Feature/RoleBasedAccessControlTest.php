<?php

use App\Models\User;

test('guest is redirected to login from dashboard', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/');
});

test('mahasiswa can access dashboard and khs but not admin or dosen pages', function () {
    $mahasiswa = User::factory()->create(['role' => 'Mahasiswa']);

    // Can access
    $this->actingAs($mahasiswa)->get('/dashboard')->assertOk();
    $this->actingAs($mahasiswa)->get('/khs')->assertOk();

    // Cannot access
    $this->actingAs($mahasiswa)->get('/user')->assertStatus(401);
    $this->actingAs($mahasiswa)->get('/dosen/kelas')->assertStatus(401);
    $this->actingAs($mahasiswa)->get('/setting')->assertStatus(401);
});

test('dosen can access dashboard and kelas but not admin or student pages', function () {
    $dosen = User::factory()->create(['role' => 'Dosen']);

    // Can access
    $this->actingAs($dosen)->get('/dashboard')->assertOk();
    $this->actingAs($dosen)->get('/dosen/kelas')->assertOk();

    // Cannot access
    $this->actingAs($dosen)->get('/user')->assertStatus(401);
    $this->actingAs($dosen)->get('/khs')->assertStatus(401);
    $this->actingAs($dosen)->get('/setting')->assertStatus(401);
});

test('admin can access dashboard and user/settings but not dosen or student pages', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    // Can access
    $this->actingAs($admin)->get('/dashboard')->assertOk();
    $this->actingAs($admin)->get('/user')->assertOk();
    $this->actingAs($admin)->get('/setting')->assertOk();

    // Cannot access
    $this->actingAs($admin)->get('/dosen/kelas')->assertStatus(401);
    $this->actingAs($admin)->get('/khs')->assertStatus(401);
});

test('superadmin can access dashboard and user/settings but not dosen or student pages', function () {
    $superadmin = User::factory()->create(['role' => 'Superadmin']);

    // Can access
    $this->actingAs($superadmin)->get('/dashboard')->assertOk();
    $this->actingAs($superadmin)->get('/user')->assertOk();
    $this->actingAs($superadmin)->get('/setting')->assertOk();

    // Cannot access
    $this->actingAs($superadmin)->get('/dosen/kelas')->assertStatus(401);
    $this->actingAs($superadmin)->get('/khs')->assertStatus(401);
});
