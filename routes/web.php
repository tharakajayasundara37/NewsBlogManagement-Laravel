<?php

use App\Http\Controllers\{AdminController, AuthController, BlogController, DashboardController, PostController};
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('home');
Route::get('/postsme', [BlogController::class, 'archive'])->name('posts.index');
Route::get('/posts', [BlogController::class, 'archive']);
Route::get('/posts/{post}', [BlogController::class, 'show'])->name('posts.show');
Route::post('/posts/{post}/comments', [BlogController::class, 'comment'])->name('comments.store');
Route::view('/about', 'blog.about')->name('about');
Route::view('/contact', 'blog.contact')->name('contact');
Route::post('/contact', [BlogController::class, 'contact'])->name('contact.store');
Route::post('/subscribe', [BlogController::class, 'subscribe'])->name('subscribe');
Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/posts/bulk', [PostController::class, 'bulk'])->name('dashboard.posts.bulk');
    Route::resource('/posts', PostController::class)->except('show')->names('dashboard.posts');
    Route::middleware('role:admin')->group(function () {
        Route::get('/categories', [AdminController::class, 'categories'])->name('dashboard.categories');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('dashboard.categories.store');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('dashboard.categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('dashboard.categories.destroy');
        Route::get('/comments', [AdminController::class, 'comments'])->name('dashboard.comments');
        Route::patch('/comments/{comment}', [AdminController::class, 'commentStatus'])->name('dashboard.comments.update');
        Route::delete('/comments/{comment}', [AdminController::class, 'deleteComment'])->name('dashboard.comments.destroy');
        Route::get('/users', [AdminController::class, 'users'])->name('dashboard.users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('dashboard.users.store');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('dashboard.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('dashboard.users.destroy');
    });
});
