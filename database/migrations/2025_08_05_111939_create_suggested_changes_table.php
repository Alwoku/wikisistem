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
        Schema::create('suggested_changes', function (Blueprint $table) {
            $table->comment("предлагаемые изменения для статей");
            
            $table->id();
            $table->unsignedBigInteger("object_id");
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->string("name")->comment("наименование статьи");
            $table->longText("text")->comment("измененный контент статьи");
            $table->longText("comment")
                ->nullable()
                ->comment("Комментарий к изменениям");
            $table->unsignedBigInteger("user_id");
            $table->softDeletes();
            $table->timestamps();



            $table->foreign('object_id')->references('id')
                ->on('objects')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            
            $table->foreign('parent_id')->references('id')
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
        Schema::dropIfExists('suggested_changes');
    }
};
