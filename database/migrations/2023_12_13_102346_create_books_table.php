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
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('title');
            $table->string('isbn')->unique();
            $table->string('author_id');
            $table->string('genres_id');
            $table->integer('publisher_id');
            $table->integer('publish_date');
            $table->string('image_url');
            $table->text('description');
            $table->integer('num_pages');
            $table->string('language');
            $table->string('url_key')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
