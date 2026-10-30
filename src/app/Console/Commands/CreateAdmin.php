<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email? : Email login} {--name= : Nama admin}';

    protected $description = 'Buat akun admin CMS (password diminta lewat prompt, tidak ditampilkan)';

    public function handle(): int
    {
        $email = $this->argument('email') ?: text('Email login', required: true);
        $name = $this->option('name') ?: text('Nama', default: 'Admin', required: true);
        $password = password('Password (minimal 10 karakter)', required: true);

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email', 'unique:users,email'], 'password' => ['required', 'min:10']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        $this->components->info("Admin {$email} dibuat. Masuk di /admin.");

        return self::SUCCESS;
    }
}
