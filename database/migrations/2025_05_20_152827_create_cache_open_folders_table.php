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
        Schema::create('cache_open_folders', function (Blueprint $table) {
            $table->comment("кеш открытых пользователем папок");
            
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('folder_id');
            $table->boolean("condition")
                    ->comment("фиксирует состояние папки");
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('folder_id')->references('id')->on('objects')->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache_open_folders');
    }
};
