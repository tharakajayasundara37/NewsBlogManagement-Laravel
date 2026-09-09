<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['legacy_id', 'category_name', 'slug', 'description', 'status'];
}
