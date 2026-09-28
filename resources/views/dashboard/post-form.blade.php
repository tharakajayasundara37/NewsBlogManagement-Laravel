@extends('layouts.admin')
@section('title', $post->exists ? 'Edit Post' : 'Create Post')
@section('page-title', $post->exists ? 'Edit Post' : 'Create New Post')
@section('content')
<div class="admin-heading">
    <div><h1>{{ $post->exists ? 'Edit Article' : 'Create New Article' }}</h1><p>Write, illustrate and publish a polished newsroom story.</p></div>
    <a class="admin-btn dark" href="{{ route('dashboard.posts.index') }}"><i class="fas fa-arrow-left"></i>Back</a>
</div>

<form class="post-editor-layout" method="post" enctype="multipart/form-data" action="{{ $post->exists ? route('dashboard.posts.update', $post) : route('dashboard.posts.store') }}">
    @csrf
    @if($post->exists) @method('put') @endif
    <section class="admin-card post-editor-main">
        <div class="admin-card-title"><i class="fas fa-pen-nib"></i> Story content</div>
        <div class="admin-form">
            <label>Post Title *<input name="title" value="{{ old('title', $post->title) }}" required maxlength="255" placeholder="Enter a clear, compelling headline"></label>
            <label>Article Content *<textarea id="article-content" name="content" class="article-editor" required placeholder="Write your article content...">{{ old('content', $post->content) }}</textarea><small class="field-note">Use blank lines to separate paragraphs. Reader-facing output is escaped for security.</small></label>
        </div>
    </section>

    <aside class="post-editor-side">
        <section class="admin-card">
            <div class="admin-card-title"><i class="fas fa-paper-plane"></i> Publishing</div>
            <div class="admin-form">
                <label>Category *<select name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->_id }}" @selected(old('category_id', $post->category_id) === (string) $category->_id)>{{ $category->category_name }}</option>@endforeach</select></label>
                <label>Status<select name="status"><option value="draft" @selected(old('status', $post->status ?: 'draft') === 'draft')>Draft</option><option value="pending" @selected(old('status', $post->status) === 'pending')>Pending review</option><option value="published" @selected(old('status', $post->status) === 'published')>Published</option></select></label>
                <button class="admin-btn editor-save" type="submit"><i class="fas fa-save"></i>{{ $post->exists ? 'Update Story' : 'Save Story' }}</button>
            </div>
        </section>

        <section class="admin-card">
            <div class="admin-card-title"><i class="fas fa-image"></i> Featured image</div>
            <div class="admin-form">
                <div class="image-drop" id="image-drop">
                    <input type="file" id="image-file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                    <i class="fas fa-cloud-arrow-up"></i><b>Choose an image</b><span>JPG, PNG, GIF or WEBP · max 4 MB</span>
                </div>
                <img id="image-preview" class="editor-image-preview {{ $post->image ? '' : 'is-hidden' }}" src="{{ $post->image ? App\Support\PostImage::url($post->image) : '' }}" alt="Featured image preview">
                @if($post->image)<label class="check-label"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>@endif
                <small class="field-note"><i class="fas fa-database"></i> New uploads are stored with the post in MongoDB, so they remain available on Vercel.</small>
            </div>
        </section>
    </aside>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('image-file')?.addEventListener('change', event => {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('image-preview');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('is-hidden');
});
</script>
@endpush
