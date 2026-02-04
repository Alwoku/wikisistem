<?php

namespace App\Console\Commands;

use App\Models\ModalNotification;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateModalNotification extends Command
{
    protected $signature = 'basis:update-modal-notification';

    protected $description = 'Обновляет модель для вывода пользователю модального окна с информацией об изменениях на сайте';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $this->info("Начало команды обновления");
        // забираем список активных пользователей
        $users = User::where("is_active", true)->get();

        foreach ($users as $user) {
            // обновляем существующую запись
            // либо создаем новую
            $notification = ModalNotification::firstOrNew([
                "user_id" => $user->getKey(),
            ]);

            // выставляем параметр для информации не прочитанным
            $notification->read = false;

            $notification->save();

            $this->info("{$user->name} синхронизирован");
        }


        $this->info("Завершено");
    }
}
