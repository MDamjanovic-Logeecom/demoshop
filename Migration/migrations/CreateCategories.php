<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration class of categories table
 */
class CreateCategories
{
    /**
     * Create categories table
     *
     * @return void
     */
    public static function up(): void
    {
        Capsule::schema()->create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Drops categories table if exists
     *
     * @return void
     */
    public static function down(): void
    {
        Capsule::schema()->dropIfExists('categories');
    }
}
