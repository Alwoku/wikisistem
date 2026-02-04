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
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->comment("Объекты в избранном у пользоватлей");
            
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("object_id");
            $table->timestamps();


            $table->foreign('object_id')->references('id')
                ->on('objects')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('user_id')->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
