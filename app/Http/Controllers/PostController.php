<?php

namespace App\Http\Controllers;

use App\Models\{Category, Comment, Post};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index() { return view('dashboard.posts', ['posts' => Post::with(['category','author'])->latest()->paginate(20), 'categories' => Category::orderBy('category_name')->get()]); }
    public function create() { return view('dashboard.post-form', ['post' => new Post, 'categories' => Category::orderBy('category_name')->get()]); }
    public function store(Request $request) { Post::create($this->data($request) + ['author_id' => (string) auth()->id()]); return redirect()->route('dashboard.posts.index')->with('success','Post created.'); }
    public function edit(Post $post) { $this->own($post); return view('dashboard.post-form', ['post' => $post, 'categories' => Category::orderBy('category_name')->get()]); }
    public function update(Request $request, Post $post) { $this->own($post); $post->update($this->data($request)); return redirect()->route('dashboard.posts.index')->with('success','Post updated.'); }
    public function destroy(Post $post) { $this->own($post); $post->delete(); Comment::where('post_id',(string)$post->_id)->delete(); return back()->with('success','Post deleted.'); }
    private function data(Request $request): array { $d=$request->validate(['title'=>'required|max:255','content'=>'required','image'=>'nullable|max:255','category_id'=>'nullable','status'=>'required|in:draft,pending,published']); $d['slug']=Str::slug($d['title']).'-'.Str::lower(Str::random(5)); if($d['status']==='published')$d['published_at']=now(); return $d; }
    private function own(Post $post): void { abort_unless(auth()->user()->role==='admin' || $post->author_id===(string)auth()->id(),403); }
}
