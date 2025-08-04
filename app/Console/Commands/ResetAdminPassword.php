<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email?}';
    protected $description = 'Réinitialise le mot de passe administrateur';

    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@bloodbank.com';
        $user = User::where('email', $email)->where('role', 'admin')->first();

        if (!$user) {
            $this->error("Aucun administrateur trouvé avec l'email: {$email}");
            return 1;
        }

        $password = 'password123';
        $user->password = Hash::make($password);
        $user->save();

        $this->info("Le mot de passe de l'administrateur {$email} a été réinitialisé à : {$password}");
        return 0;
    }
}
