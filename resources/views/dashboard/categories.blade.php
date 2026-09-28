@extends('layouts.admin')
@section('title', 'Categories')
@section('page-title', 'Category Management')
@section('content')
<div class="admin-heading"><div><h1>Categories</h1><p>Create, edit and organize newsroom topics safely.</p></div></div>

<div class="admin-stats compact">
    <div class="admin-stat"><i class="fas fa-folder-open"></i><div><b>{{ $categories->count() }}</b><small>Total categories</small></div></div>
    <div class="admin-stat"><i class="fas fa-newspaper"></i><div><b>{{ number_format($categories->sum('post_count')) }}</b><small>Assigned posts</small></div></div>
    <div class="admin-stat"><i class="fas fa-circle-check"></i><div><b>{{ number_format($categories->sum('published_count')) }}</b><small>Published posts</small></div></div>
    <div class="admin-stat"><i class="fas fa-toggle-on"></i><div><b>{{ $categories->where('status', 'active')->count() }}</b><small>Active categories</small></div></div>
</div>

<div class="admin-two-col category-layout">
    <section class="admin-card sticky-card">
        <div class="admin-card-title"><i class="fas fa-folder-plus"></i> Add New Category</div>
        <form class="admin-form" method="post" action="{{ route('dashboard.categories.store') }}">
            @csrf
            <label>Category Name *<input name="category_name" value="{{ old('category_name') }}" required maxlength="100" placeholder="e.g. Technology"></label>
            <label>Description<textarea name="description" maxlength="500" placeholder="Short topic description">{{ old('description') }}</textarea></label>
            <label>Status<select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
            <button class="admin-btn"><i class="fas fa-plus"></i>Add Category</button>
        </form>
    </section>

    <section class="admin-card">
        <div class="admin-card-title"><i class="fas fa-layer-group"></i> Existing Categories</div>
        <div class="category-admin-list">
            @forelse($categories as $category)
                <article class="category-admin-item">
                    <form method="post" action="{{ route('dashboard.categories.update', $category) }}" class="category-edit-form">
                        @csrf @method('put')
                        <div class="category-item-heading">
                            <span class="category-icon"><i class="fas fa-folder"></i></span>
                            <div><b>{{ $category->category_name }}</b><small>{{ $category->slug }}</small></div>
                            <span class="status status-{{ $category->status ?? 'active' }}">{{ $category->status ?? 'active' }}</span>
                        </div>
                        <div class="admin-form-grid category-fields">
                            <label>Name<input name="category_name" value="{{ $category->category_name }}" required maxlength="100"></label>
                            <label>Status<select name="status"><option value="active" @selected(($category->status ?? 'active') === 'active')>Active</option><option value="inactive" @selected($category->status === 'inactive')>Inactive</option></select></label>
                        </div>
                        <label>Description<textarea name="description" maxlength="500">{{ $category->description }}</textarea></label>
                        <div class="category-item-footer">
                            <span><b>{{ $category->post_count }}</b> posts · <b>{{ $category->published_count }}</b> published</span>
                            <button class="admin-btn" type="submit"><i class="fas fa-save"></i>Save changes</button>
                        </div>
                    </form>
                    <form method="post" action="{{ route('dashboard.categories.destroy', $category) }}" class="category-delete-form" onsubmit="return confirm('Delete this empty category permanently?')">
                        @csrf @method('delete')
                        <button class="delete-icon-btn" type="submit" @disabled($category->post_count > 0) title="{{ $category->post_count > 0 ? 'Move or delete its posts first' : 'Delete category' }}"><i class="fas fa-trash"></i></button>
                    </form>
                </article>
            @empty
                <div class="empty-admin">No categories created yet.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
