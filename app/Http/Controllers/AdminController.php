<?php

namespace App\Http\Controllers;

use App\Models\{Category, Comment, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function categories() { return view('dashboard.categories', ['categories'=>Category::orderBy('category_name')->get()]); }
    public function storeCategory(Request $r) { $d=$r->validate(['category_name'=>'required|max:80','description'=>'nullable|max:500']); $d+=['slug'=>Str::slug($d['category_name']),'status'=>'active']; Category::create($d); return back()->with('success','Category created.'); }
    public function deleteCategory(Category $category) { $category->delete(); return back()->with('success','Category deleted.'); }
    public function comments() { return view('dashboard.comments', ['comments'=>Comment::with('post')->latest()->paginate(25)]); }
    public function commentStatus(Request $r, Comment $comment) { $comment->update($r->validate(['status'=>'required|in:pending,approved,rejected'])); return back()->with('success','Comment updated.'); }
    public function users() { return view('dashboard.users', ['users'=>User::latest()->get()]); }
    public function storeUser(Request $r) { $d=$r->validate(['name'=>'required|max:100','email'=>'required|email','password'=>'required|min:8','role'=>'required|in:admin,author']); $d['password']=Hash::make($d['password']); User::create($d); return back()->with('success','User created.'); }
}
