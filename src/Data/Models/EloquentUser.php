<?php

namespace Demoshop\Local\Data\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'password'];

    public $timestamps = false;
}
