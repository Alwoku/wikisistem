<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("name", length:  255);
            $table->string("email", length: 255)
                ->nullable();
            $table->timestamp("email_verified_at")
                    ->nullable();
            $table->string("password", length: 255);
            $table->string("remember_token", length: 100)
                    ->nullable();
            $table->string("telegram_chat_id", length: 255)
            ->nullable();
            $table->enum("panel_pin", ["objects", "favorites", "settings"])
                ->nullable()
                ->comment("сохранение открытого меню");
            $table->boolean("is_active")
                    ->nullable()
                    ->default(true)
                    ->comment("активность в системе");
            $table->boolean("dark_style")
                    ->default(false)
                    ->comment("выбор пользователем темной темы");
            $table->boolean("is_admin")
            ->nullable();
            $table->timestamps();
        });


        Schema::create('user_modal_notification', function (Blueprint $table) {
            $table->comment("Таблица уведомления пользователей об изменениях на сайте");
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean("read")
                    ->default(false)
                    ->comment("отслеживание прочтения инфо-ции об изменениях");

            $table->foreign('user_id')->references('id')->on('users');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_modal_notification');
        Schema::dropIfExists('users');
    }
};
