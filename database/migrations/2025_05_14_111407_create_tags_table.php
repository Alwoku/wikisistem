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
        Schema::create('tags', function (Blueprint $table) {
            $table->comment("теги в статьях");
            
            $table->id();
            $table->string("name");
            $table->timestamps();
        });


        Schema::create('object_tags', function (Blueprint $table) {
            $table->comment("статьи в которых содержаться теги");
            
            $table->id();
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('object_id')->references('id')
                ->on('objects')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('tag_id')->references('id')
                ->on('tags')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('object_tags');
        Schema::dropIfExists('tags');
    }
};
