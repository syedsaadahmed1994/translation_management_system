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
        Schema::create('translation_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('translation_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign(columns: 'translation_id')->references('id')->on('translations')->onDelete('cascade');
            $table->foreign(columns: 'tag_id')->references('id')->on('tags')->onDelete('cascade');

            $table->primary(['translation_id','tag_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_tags');
    }
};
