<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Subscriber extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['email'];
}
