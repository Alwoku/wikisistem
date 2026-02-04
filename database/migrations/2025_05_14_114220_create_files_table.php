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
        Schema::create('files', function (Blueprint $table) {
            $table->comment("данные загруженных файлов");
            
            $table->id();
            $table->string("name");
            $table->longText("path")
                    ->comment("путь к файлу на сервере");
            $table->enum("type", ['image', 'archive', 'application']);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('expansion_files', function (Blueprint $table) {

            $table->id();
            $table->enum("type", ['image', 'archive', 'application']);
            $table->string("expansion");
            $table->boolean("upload");
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('expansion_files');
    }
};
