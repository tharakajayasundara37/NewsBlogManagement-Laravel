<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Comment extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['legacy_id', 'post_id', 'user_name', 'user_email', 'comment', 'status'];
    public function post() { return $this->belongsTo(Post::class); }
}
