<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $ownPosts = Post::where('author_id', (string) $user->_id);
        $posts = ($user->role === 'author' ? clone $ownPosts : Post::query())
            ->latest()
            ->take(8)
            ->get();

        if ($user->role === 'author') {
            $postIds = (clone $ownPosts)->get()->map(fn (Post $post) => (string) $post->_id)->all();
            $stats = [
                ['newspaper', 'Total Posts', (clone $ownPosts)->count()],
                ['circle-check', 'Published', (clone $ownPosts)->where('status', 'published')->count()],
                ['file-pen', 'Drafts', (clone $ownPosts)->where('status', 'draft')->count()],
                ['comments', 'Comments', $postIds ? Comment::whereIn('post_id', $postIds)->count() : 0],
            ];
        } elseif ($user->role === 'editor') {
            $stats = [
                ['newspaper', 'Total Posts', Post::count()],
                ['circle-check', 'Published', Post::where('status', 'published')->count()],
                ['file-pen', 'Drafts', Post::where('status', 'draft')->count()],
                ['comments', 'Comments', Comment::count()],
            ];
        } else {
            $stats = [
                ['newspaper', 'Total Posts', Post::count()],
                ['folder', 'Categories', Category::count()],
                ['comments', 'Comments', Comment::count()],
                ['users', 'Users', User::count()],
            ];
        }

        return view('dashboard.index', compact('posts', 'stats'));
    }
}
