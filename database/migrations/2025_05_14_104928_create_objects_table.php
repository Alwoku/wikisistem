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
        Schema::create('objects', function (Blueprint $table) {
            $table->comment("данные папок и статей");

            $table->id();
            $table->string("name");
            $table->unsignedBigInteger('parent_id')
                    ->nullable();
            $table->enum("type", ["folder", "note"])
                ->comment("типы объектов");
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                    ->noActionOnDelete();
            $table->foreign('parent_id')->references('id')->on('objects')
                    ->nullOnDelete();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objects');
        Schema::dropIfExists('objects_pivot');
    }
};
