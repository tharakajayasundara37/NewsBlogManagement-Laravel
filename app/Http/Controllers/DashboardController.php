<?php

namespace App\Http\Controllers;

use App\Models\{Category, Comment, ContactMessage, Post, User};

class DashboardController extends Controller
{
    public function index()
    {
        $posts = Post::when(auth()->user()->role !== 'admin', fn ($q) => $q->where('author_id', (string) auth()->id()))->latest()->take(8)->get();
        return view('dashboard.index', ['posts' => $posts, 'postCount' => Post::count(), 'categoryCount' => Category::count(), 'commentCount' => Comment::count(), 'userCount' => User::count()]);
    }
}
