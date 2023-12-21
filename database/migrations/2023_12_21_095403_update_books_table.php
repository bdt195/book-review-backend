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
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['isbn']);
            $table->string('isbn')->nullable()->change();
            $table->string('title_complete')->nullable()->change();
            $table->integer('publisher_id')->nullable()->change();
            $table->integer('publish_date')->nullable()->change();
            $table->string('image_url')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->integer('num_pages')->nullable()->change();
            $table->string('language')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->nullable(false)->change();
            $table->string('title_complete')->nullable(false)->change();
            $table->integer('publisher_id')->nullable(false)->change();
            $table->integer('publish_date')->nullable(false)->change();
            $table->string('image_url')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->integer('num_pages')->nullable(false)->change();
            $table->string('language')->nullable(false)->change();
            $table->unique('isbn');
        });
    }
};
