<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['legacy_id', 'title', 'slug', 'content', 'image', 'category_id', 'author_id', 'status', 'views', 'published_at'];
    protected $casts = ['published_at' => 'datetime', 'views' => 'integer'];

    public function category() { return $this->belongsTo(Category::class); }
    public function author() { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(Comment::class); }
}
