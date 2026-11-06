<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BootstrapAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_first_admin_from_deployment_secrets(): void
    {
        $this->setBootstrapSecrets('admin@example.com', 'a-long-test-password');

        try {
            $this->artisan('lendtrack:bootstrap-admin')->assertSuccessful();

            $this->assertDatabaseHas('users', [
                'email' => 'admin@example.com',
                'role' => 'admin',
            ]);
            $this->assertTrue(Hash::check('a-long-test-password', User::query()->firstOrFail()->password));
        } finally {
            $this->clearBootstrapSecrets();
        }
    }

    public function test_it_does_not_create_an_admin_if_users_already_exist(): void
    {
        User::factory()->create();
        $this->setBootstrapSecrets('admin@example.com', 'a-long-test-password');

        try {
            $this->artisan('lendtrack:bootstrap-admin')->assertSuccessful();

            $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
            $this->assertDatabaseCount('users', 1);
        } finally {
            $this->clearBootstrapSecrets();
        }
    }

    private function setBootstrapSecrets(string $email, string $password): void
    {
        putenv("BOOTSTRAP_ADMIN_EMAIL=$email");
        putenv("BOOTSTRAP_ADMIN_PASSWORD=$password");
    }

    private function clearBootstrapSecrets(): void
    {
        putenv('BOOTSTRAP_ADMIN_EMAIL');
        putenv('BOOTSTRAP_ADMIN_PASSWORD');
    }
}
