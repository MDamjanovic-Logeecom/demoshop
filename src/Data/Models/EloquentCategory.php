<?php

namespace Demoshop\Local\Data\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentCategory extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'parent_id',
        'code',
        'description',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}