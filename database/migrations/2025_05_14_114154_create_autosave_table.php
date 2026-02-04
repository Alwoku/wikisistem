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
        Schema::create('autosave', function (Blueprint $table) {
            $table->comment("автосохранение папок и статей");
            
            $table->id();
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('user_id');
            $table->string("name");
            $table->longText("content");
            $table->unsignedBigInteger("parent_id")
                ->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('object_id')->references('id')
                ->on('objects')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('parent_id')->references('id')
                ->on('objects')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autosave');
    }
};
