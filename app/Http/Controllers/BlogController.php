<?php

namespace App\Http\Controllers;

use App\Models\{Category, Comment, ContactMessage, Post, Subscriber};
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'author'])->where('status', 'published');
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')->orWhere('content', 'like', '%'.$request->q.'%'));
        if ($request->filled('category')) $query->where('category_id', $request->category);
        return view('blog.index', ['posts' => $query->latest('published_at')->paginate(9)->withQueryString(), 'categories' => Category::where('status', 'active')->orderBy('category_name')->get()]);
    }

    public function show(Post $post)
    {
        abort_unless($post->status === 'published' || auth()->check(), 404);
        $post->increment('views');
        return view('blog.show', ['post' => $post->load(['category', 'author']), 'comments' => Comment::where('post_id', (string) $post->_id)->where('status', 'approved')->latest()->get()]);
    }

    public function comment(Request $request, Post $post)
    {
        $data = $request->validate(['user_name' => 'required|max:100', 'user_email' => 'required|email|max:150', 'comment' => 'required|max:2000']);
        $data['post_id'] = (string) $post->_id; $data['status'] = 'pending'; Comment::create($data);
        return back()->with('success', 'Comment submitted for review.');
    }

    public function contact(Request $request)
    {
        $data = $request->validate(['name' => 'required|max:100', 'email' => 'required|email', 'subject' => 'required|max:200', 'message' => 'required|max:3000']);
        $data['status'] = 'pending'; ContactMessage::create($data);
        return back()->with('success', 'Message sent successfully.');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:150']);
        Subscriber::firstOrCreate($data); return back()->with('success', 'Thanks for subscribing!');
    }
}
