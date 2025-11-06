<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration class for users table
 */
class CreateUsers
{
    /**
     * Create users table
     *
     * @return void
     */
    public static function up(): void
    {
        Capsule::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
        });
    }

    /**
     * Drop users table if exists
     *
     * @return void
     */
    public static function down(): void
    {
        Capsule::schema()->dropIfExists('users');
    }
}
