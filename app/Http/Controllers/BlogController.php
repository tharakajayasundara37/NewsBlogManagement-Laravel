<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Subscriber;
use App\Support\DatabaseBootstrap;
use App\Support\LegacyContent;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        if (! $this->databaseConfigured()) {
            $posts = LegacyContent::posts($request->q, $request->category);

            return view('blog.index', [
                'posts' => $posts,
                'popular' => $posts->getCollection()->sortByDesc('views')->take(5),
                'categories' => LegacyContent::categories(),
                'demoMode' => true,
            ]);
        }
        DatabaseBootstrap::run();
        $query = Post::with(['category', 'author'])->where('status', 'published');
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')->orWhere('content', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        return view('blog.index', [
            'posts' => $query->latest('published_at')->paginate(9)->withQueryString(),
            'popular' => Post::with(['category', 'author'])->where('status', 'published')->orderBy('views', 'desc')->take(5)->get(),
            'categories' => Category::where('status', 'active')->orderBy('category_name')->get(),
        ]);
    }

    public function archive(Request $request)
    {
        if (! $this->databaseConfigured()) {
            return view('blog.archive', ['posts' => LegacyContent::posts($request->q, $request->category, 12), 'categories' => LegacyContent::categories()]);
        }
        DatabaseBootstrap::run();
        $query = Post::with(['category', 'author'])->where('status', 'published');
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')->orWhere('content', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        return view('blog.archive', ['posts' => $query->latest('published_at')->paginate(12)->withQueryString(), 'categories' => Category::where('status', 'active')->orderBy('category_name')->get()]);
    }

    public function show(string $post)
    {
        if (! $this->databaseConfigured()) {
            $item = LegacyContent::post($post);
            abort_unless($item, 404);
            $related = LegacyContent::posts(null, (string) $item->category_id, 4)->getCollection()
                ->reject(fn ($relatedPost) => (string) $relatedPost->_id === (string) $item->_id)
                ->take(3);

            return view('blog.show', ['post' => $item, 'comments' => collect(), 'related' => $related, 'demoMode' => true]);
        }
        $post = Post::findOrFail($post);
        abort_unless($post->status === 'published' || $this->canPreview($post), 404);
        $post->increment('views');
        $related = Post::with(['category', 'author'])
            ->where('status', 'published')
            ->where('category_id', $post->category_id)
            ->where('_id', '!=', (string) $post->_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', [
            'post' => $post->load(['category', 'author']),
            'comments' => Comment::where('post_id', (string) $post->_id)->where('status', 'approved')->latest()->get(),
            'related' => $related,
        ]);
    }

    public function comment(Request $request, string $post)
    {
        if (! $this->databaseConfigured()) {
            return back()->withErrors(['database' => 'Comments are read-only until a MongoDB database is connected.']);
        }
        $post = Post::findOrFail($post);
        $data = $request->validate(['user_name' => 'required|max:100', 'user_email' => 'required|email|max:150', 'comment' => 'required|min:10|max:2000']);
        $data['post_id'] = (string) $post->_id;
        $data['status'] = 'pending';
        Comment::create($data);

        return back()->with('success', 'Comment submitted for review.');
    }

    public function contact(Request $request)
    {
        if (! $this->databaseConfigured()) {
            return back()->withErrors(['database' => 'Messages are disabled until a MongoDB database is connected.']);
        }
        $data = $request->validate(['name' => 'required|max:100', 'email' => 'required|email', 'subject' => 'required|max:200', 'message' => 'required|max:3000']);
        $data['status'] = 'pending';
        ContactMessage::create($data);

        return back()->with('success', 'Message sent successfully.');
    }

    public function subscribe(Request $request)
    {
        if (! $this->databaseConfigured()) {
            return back()->withErrors(['database' => 'Subscriptions are disabled until a MongoDB database is connected.']);
        }
        $data = $request->validate(['email' => 'required|email|max:150']);
        Subscriber::firstOrCreate($data);

        return back()->with('success', 'Thanks for subscribing!');
    }

    private function databaseConfigured(): bool
    {
        return DatabaseBootstrap::configured();
    }

    private function canPreview(Post $post): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return in_array(auth()->user()->role, ['admin', 'editor'], true)
            || $post->author_id === (string) auth()->id();
    }
}
