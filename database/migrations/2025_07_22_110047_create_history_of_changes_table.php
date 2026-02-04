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
        Schema::create('history_of_changes', function (Blueprint $table) {
            $table->comment("история изменений сайта");
            
            $table->id();
            $table->string("version");
            $table->longText("info")
                ->comment("информация для обычных пользователей");
            $table->longText("admin_info")
                ->comment("информация для администранторов сайта")
                ->nullable();
            $table->boolean("active")->comment("определяет актуальную версию");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_of_changes');
    }
};
