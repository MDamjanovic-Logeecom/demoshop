<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration class for products table
 */
class CreateProducts
{
    /**
     * Create products table
     *
     * @return void
     */
    public static function up(): void
    {
        Capsule::schema()->create('products', function (Blueprint $table) {
            $table->string('SKU', 50)->primary();
            $table->string('Title', 100);
            $table->string('Brand', 50)->nullable();
            $table->string('Category', 20)->nullable();
            $table->string('Dscrptn', 100)->nullable();
            $table->longText('LDscrptn')->nullable();
            $table->longText('image')->nullable();
            $table->decimal('Price', 10, 2)->nullable();
            $table->boolean('Enabled')->default(true);

            $table->foreign('Category')
                ->references('code')
                ->on('categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Capsule::connection()->statement('ALTER TABLE products MODIFY Image LONGBLOB');
    }

    /**
     * Drop products table
     *
     * @return void
     */
    public static function down(): void
    {
        Capsule::schema()->dropIfExists('products');
    }
}
