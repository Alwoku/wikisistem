<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateRootUser extends Command
{
    protected $signature = 'app:create-root-user';

    protected $description = 'Создает рутового пользователя';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Начало создания пользователя");

        $login = Str::random(10);
        $passwd = Str::random(16);

        $this->info("Ваш логин:{$login} пароль:{$passwd}");

        User::create([
            "name" => $login,
            "password" => Hash::make($passwd),
            "is_admin" => true
        ]);

        $this->info("Завершено");
    }
}
