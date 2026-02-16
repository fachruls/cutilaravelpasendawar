<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// WAJIB: Gunakan RefreshDatabase agar tabel dibuat ulang otomatis
uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    // 1. Buat user dummy (UserFactory akan otomatis generate NIP acak)
    $user = User::factory()->create([
        'role' => 'pegawai',
        'password' => bcrypt('password'), 
    ]);

    // 2. Coba Login
    // Gunakan 'nip' sebagai key sesuai logika LoginRequest Abang
    $response = $this->post('/login', [
        'nip' => $user->nip,       
        'password' => 'password',
    ]);

    // 3. Cek hasil
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    // Gunakan 'nip' di sini juga
    $this->post('/login', [
        'nip' => $user->nip,      
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    
    // PERBAIKAN FINAL DISINI:
    // Harapannya redirect ke '/login', bukan '/' lagi
    $response->assertRedirect('/login');
});