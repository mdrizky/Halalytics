<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthAndAdminUserFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_api_register_persists_user_and_returns_success_payload(): void
    {
        $email = 'register_' . Str::lower(Str::random(8)) . '@example.com';

        $response = $this->postJson('/api/register', [
            'name' => 'Register Flow User',
            'email' => $email,
            'password' => 'supersecret123',
            'password_confirmation' => 'supersecret123',
            'phone_number' => '081234567890',
            'blood_type' => 'A',
            'allergies' => 'Seafood',
            'medical_history' => 'GERD ringan',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Akun berhasil dibuat! Silakan login.');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'full_name' => 'Register Flow User',
            'phone' => '081234567890',
            'blood_type' => 'A',
            'allergy' => 'Seafood',
            'medical_history' => 'GERD ringan',
        ]);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_api_login_accepts_email_and_username(): void
    {
        $user = User::factory()->create([
            'username' => 'login_flow_user',
            'email' => 'login_flow_user@example.com',
            'password' => bcrypt('supersecret123'),
            'role' => 'user',
            'active' => true,
        ]);

        $emailLogin = $this->postJson('/api/login', [
            'login' => $user->email,
            'password' => 'supersecret123',
        ]);

        $emailLogin
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', $user->email);

        $this->assertNotEmpty($emailLogin->json('token'));

        $usernameLogin = $this->postJson('/api/login', [
            'login' => $user->username,
            'password' => 'supersecret123',
        ]);

        $usernameLogin
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.username', $user->username);

        $this->assertNotEmpty($usernameLogin->json('token'));
    }

    public function test_admin_store_route_creates_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'active' => true,
        ]);

        $email = 'admin_store_' . Str::lower(Str::random(8)) . '@example.com';

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.user.store'), [
                'full_name' => 'Created From Admin',
                'email' => $email,
                'password' => 'supersecret123',
                'role' => 'user',
                'phone' => '089999999999',
                'blood_type' => 'O+',
                'allergy' => 'Peanut',
                'medical_history' => 'Asma',
            ]);

        $response
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'full_name' => 'Created From Admin',
            'phone' => '089999999999',
            'blood_type' => 'O+',
            'allergy' => 'Peanut',
            'medical_history' => 'Asma',
            'role' => 'user',
        ]);
    }

    public function test_admin_update_route_updates_user_without_deleting_record(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'active' => true,
        ]);

        $user = User::factory()->create([
            'full_name' => 'Before Update',
            'username' => 'before_update_user',
            'email' => 'before_update_user@example.com',
            'role' => 'user',
            'active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.user.update', $user->id_user), [
                'full_name' => 'After Update',
                'username' => 'after_update_user',
                'email' => 'after_update_user@example.com',
                'phone' => '087777777777',
                'blood_type' => 'AB',
                'allergy' => 'Dust',
                'medical_history' => 'Hipertensi',
                'role' => 'user',
                'active' => 1,
            ]);

        $response
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $user->id_user,
            'full_name' => 'After Update',
            'username' => 'after_update_user',
            'email' => 'after_update_user@example.com',
            'phone' => '087777777777',
            'blood_type' => 'AB',
            'allergy' => 'Dust',
            'medical_history' => 'Hipertensi',
            'role' => 'user',
            'active' => 1,
        ]);

        $this->assertTrue(User::query()->whereKey($user->id_user)->exists());
    }
}
