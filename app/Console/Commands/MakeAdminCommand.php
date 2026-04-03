<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:make-admin {email?}')]
#[Description('Create or promote a user to admin')]
class MakeAdminCommand extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?? text(
            label: 'Email address',
            required: true,
            validate: ['email' => 'required|email'],
        );

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['is_admin' => true]);
            $this->info("User [{$user->name}] has been promoted to admin.");

            return self::SUCCESS;
        }

        $name = text(label: 'Name', required: true);
        $pw = password(label: 'Password', required: true);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $pw,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Admin user [{$user->name}] created successfully.");

        return self::SUCCESS;
    }
}
