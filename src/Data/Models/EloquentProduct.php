<?php

namespace Demoshop\Local\Data\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for Product
 */
class EloquentProduct extends Model
{
    /**
     * @var string table Model is part of
     */
    protected $table = 'products';
    /**
     * @var string primary key
     */
    protected $primaryKey = 'SKU';
    /**
     * @var bool no incrementing
     */
    public $incrementing = false;
    /**
     * @var bool no timestamps
     */
    public $timestamps = false;

    /**
     * @var string[] which columns are mass-assignable
     */
    protected $fillable = [
        'SKU',
        'Title',
        'Brand',
        'Category',
        'Dscrptn',
        'LDscrptn',
        'Price',
        'Image',
        'Enabled',
    ];

    /**
     * @var string[] To cast types automatically
     */
    protected $casts = [
        'Price' => 'float',
        'Enabled' => 'boolean',
        'Image' => 'string',
    ];
}
