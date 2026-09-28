<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->scopedPosts()->with(['category', 'author']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%"));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('author') && auth()->user()->role !== 'author') {
            $query->where('author_id', $request->author);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        [$sort, $direction] = match ($request->input('sort')) {
            'oldest' => ['created_at', 'asc'],
            'title_asc' => ['title', 'asc'],
            'title_desc' => ['title', 'desc'],
            'views' => ['views', 'desc'],
            default => ['created_at', 'desc'],
        };

        $statsQuery = $this->scopedPosts();
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'published' => (clone $statsQuery)->where('status', 'published')->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'views' => (int) (clone $statsQuery)->sum('views'),
        ];

        return view('dashboard.posts', [
            'posts' => $query->orderBy($sort, $direction)->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('category_name')->get(),
            'authors' => User::whereIn('role', ['admin', 'author', 'editor'])->orderBy('name')->get(),
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return view('dashboard.post-form', [
            'post' => new Post,
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Post::create($this->data($request) + ['author_id' => (string) auth()->id(), 'views' => 0]);

        return redirect()->route('dashboard.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        $this->authorizeManagement($post);

        return view('dashboard.post-form', [
            'post' => $post,
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizeManagement($post);
        $post->update($this->data($request, $post));

        return redirect()->route('dashboard.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorizeDeletion($post);
        $this->deletePost($post);

        return back()->with('success', 'Post deleted successfully.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'post_ids' => 'required|array|min:1',
            'post_ids.*' => 'required|string',
            'bulk_action' => 'required|in:publish,draft,delete',
        ]);

        $updated = 0;
        foreach ($data['post_ids'] as $id) {
            $post = Post::find($id);
            if (! $post || ! $this->canManage($post)) {
                continue;
            }
            if ($data['bulk_action'] === 'delete' && ! $this->canDelete($post)) {
                continue;
            }

            if ($data['bulk_action'] === 'delete') {
                $this->deletePost($post);
            } else {
                $status = $data['bulk_action'] === 'publish' ? 'published' : 'draft';
                $post->update([
                    'status' => $status,
                    'published_at' => $status === 'published' ? ($post->published_at ?: now()) : null,
                ]);
            }
            $updated++;
        }

        return back()->with('success', "Bulk action completed for {$updated} post(s).");
    }

    private function data(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|string',
            'status' => 'required|in:draft,pending,published',
            'image_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'remove_image' => 'nullable|boolean',
        ]);

        if (! Category::find($data['category_id'])) {
            throw ValidationException::withMessages(['category_id' => 'Please choose a valid category.']);
        }

        unset($data['image_file'], $data['remove_image']);

        if (! $post || $post->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        }

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $data['image'] = 'data:'.$file->getMimeType().';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        } elseif ($request->boolean('remove_image')) {
            $data['image'] = null;
        } elseif ($post) {
            unset($data['image']);
        } else {
            $data['image'] = null;
        }

        $data['published_at'] = $data['status'] === 'published' ? ($post?->published_at ?: now()) : null;

        return $data;
    }

    private function scopedPosts()
    {
        $query = Post::query();
        if (auth()->user()->role === 'author') {
            $query->where('author_id', (string) auth()->id());
        }

        return $query;
    }

    private function canManage(Post $post): bool
    {
        return in_array(auth()->user()->role, ['admin', 'editor'], true)
            || $post->author_id === (string) auth()->id();
    }

    private function canDelete(Post $post): bool
    {
        return auth()->user()->role === 'admin' || $post->author_id === (string) auth()->id();
    }

    private function authorizeManagement(Post $post): void
    {
        abort_unless($this->canManage($post), 403);
    }

    private function authorizeDeletion(Post $post): void
    {
        abort_unless($this->canDelete($post), 403);
    }

    private function deletePost(Post $post): void
    {
        Comment::where('post_id', (string) $post->_id)->delete();
        $post->delete();
    }
}
