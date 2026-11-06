<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class BootstrapAdmin extends Command
{
    protected $signature = 'lendtrack:bootstrap-admin';

    protected $description = 'Create the first LendTrack administrator from deployment secrets';

    public function handle(): int
    {
        $email = env('BOOTSTRAP_ADMIN_EMAIL');
        $password = env('BOOTSTRAP_ADMIN_PASSWORD');

        if (! $email && ! $password) {
            return self::SUCCESS;
        }

        if (! $email || ! $password) {
            $this->error('Set both bootstrap admin secrets, or remove both.');

            return self::FAILURE;
        }

        if (User::query()->exists()) {
            $this->warn('Users already exist; no bootstrap administrator was created.');

            return self::SUCCESS;
        }

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', Password::min(12)],
            ],
        );

        if ($validator->fails()) {
            $this->error('Bootstrap credentials are invalid. Use a valid email and a password of at least 12 characters.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => 'Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info('Initial administrator created. Remove the bootstrap secrets from the hosting environment now.');

        return self::SUCCESS;
    }
}
