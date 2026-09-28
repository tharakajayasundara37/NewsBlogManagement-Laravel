<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Support\PasswordHash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function categories()
    {
        $categories = Category::orderBy('category_name')->get();
        $categories->each(function (Category $category) {
            $category->post_count = Post::where('category_id', (string) $category->_id)->count();
            $category->published_count = Post::where('category_id', (string) $category->_id)->where('status', 'published')->count();
        });

        return view('dashboard.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $this->categoryData($request);
        $data['slug'] = $this->uniqueSlug($data['category_name']);
        Category::create($data);

        return back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $this->categoryData($request, $category);
        if ($category->category_name !== $data['category_name']) {
            $data['slug'] = $this->uniqueSlug($data['category_name'], $category);
        }
        $category->update($data);

        return back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(Category $category)
    {
        if (Post::where('category_id', (string) $category->_id)->exists()) {
            return back()->withErrors(['category' => 'This category still contains posts. Move or delete those posts first.']);
        }
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    public function comments(Request $request)
    {
        $query = Comment::with('post')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($q) => $q->where('comment', 'like', "%{$search}%")
                ->orWhere('user_name', 'like', "%{$search}%")
                ->orWhere('user_email', 'like', "%{$search}%"));
        }

        return view('dashboard.comments', [
            'comments' => $query->paginate(25)->withQueryString(),
            'stats' => [
                'all' => Comment::count(),
                'pending' => Comment::where('status', 'pending')->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'rejected' => Comment::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function commentStatus(Request $request, Comment $comment)
    {
        $comment->update($request->validate(['status' => 'required|in:pending,approved,rejected']));

        return back()->with('success', 'Comment status updated.');
    }

    public function deleteComment(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted permanently.');
    }

    public function users(Request $request)
    {
        $query = User::query()->latest();
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }
        $users = $query->get();
        $users->each(fn (User $user) => $user->post_count = Post::where('author_id', (string) $user->_id)->count());

        return view('dashboard.users', [
            'users' => $users,
            'stats' => [
                'all' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'editor' => User::where('role', 'editor')->count(),
                'author' => User::where('role', 'author')->count(),
            ],
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,editor,author',
        ]);
        $this->ensureUniqueEmail($data['email']);
        $data['email'] = Str::lower($data['email']);
        $data['password'] = PasswordHash::make($data['password']);
        unset($data['password_confirmation']);
        User::create($data);

        return back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'role' => 'required|in:admin,editor,author',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $this->ensureUniqueEmail($data['email'], $user);
        $this->protectLastAdmin($user, $data['role']);
        $data['email'] = Str::lower($data['email']);
        if (! empty($data['password'])) {
            $data['password'] = PasswordHash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation']);
        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ((string) $user->_id === (string) auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }
        $this->protectLastAdmin($user, 'author');

        Post::where('author_id', (string) $user->_id)->get()->each(function (Post $post) {
            Comment::where('post_id', (string) $post->_id)->delete();
            $post->delete();
        });
        $user->delete();

        return back()->with('success', 'User and their posts were deleted successfully.');
    }

    private function categoryData(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive',
        ]);
        $duplicate = Category::where('category_name', $data['category_name'])->first();
        if ($duplicate && (! $category || (string) $duplicate->_id !== (string) $category->_id)) {
            throw ValidationException::withMessages(['category_name' => 'A category with this name already exists.']);
        }
        $data['status'] = $data['status'] ?? 'active';

        return $data;
    }

    private function uniqueSlug(string $name, ?Category $except = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $counter = 1;
        while ($match = Category::where('slug', $slug)->first()) {
            if ($except && (string) $match->_id === (string) $except->_id) {
                break;
            }
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function ensureUniqueEmail(string $email, ?User $except = null): void
    {
        $match = User::where('email', Str::lower($email))->first();
        if ($match && (! $except || (string) $match->_id !== (string) $except->_id)) {
            throw ValidationException::withMessages(['email' => 'That email address is already in use.']);
        }
    }

    private function protectLastAdmin(User $user, string $newRole): void
    {
        if ($user->role === 'admin' && $newRole !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            throw ValidationException::withMessages(['role' => 'The system must keep at least one administrator.']);
        }
    }
}
