@extends('layouts.admin')
@section('title', 'Manage Posts')
@section('page-title', 'Manage Posts')
@section('content')
<div class="admin-heading">
    <div><h1>{{ auth()->user()->role === 'author' ? 'My Posts' : 'All Posts' }}</h1><p>Search, filter, publish and manage newsroom content.</p></div>
    <a class="admin-btn" href="{{ route('dashboard.posts.create') }}"><i class="fas fa-plus"></i>New Post</a>
</div>

<div class="admin-stats compact">
    @foreach([['newspaper','All posts',$stats['total']],['circle-check','Published',$stats['published']],['file-pen','Drafts',$stats['draft']],['eye','Total views',$stats['views']]] as $stat)
        <div class="admin-stat"><i class="fas fa-{{ $stat[0] }}"></i><div><b>{{ number_format($stat[2]) }}</b><small>{{ $stat[1] }}</small></div></div>
    @endforeach
</div>

<section class="admin-card">
    <form class="admin-filters" method="get" action="{{ route('dashboard.posts.index') }}">
        <label class="filter-search"><i class="fas fa-search"></i><input name="search" value="{{ request('search') }}" placeholder="Search title or content"></label>
        <select name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->_id }}" @selected(request('category') === (string) $category->_id)>{{ $category->category_name }}</option>@endforeach</select>
        <select name="status"><option value="">All statuses</option>@foreach(['published','pending','draft'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
        @if(auth()->user()->role !== 'author')
            <select name="author"><option value="">All authors</option>@foreach($authors as $author)<option value="{{ $author->_id }}" @selected(request('author') === (string) $author->_id)>{{ $author->name }}</option>@endforeach</select>
        @endif
        <input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="From date">
        <input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="To date">
        <select name="sort"><option value="">Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option><option value="title_asc" @selected(request('sort') === 'title_asc')>Title A–Z</option><option value="title_desc" @selected(request('sort') === 'title_desc')>Title Z–A</option><option value="views" @selected(request('sort') === 'views')>Most viewed</option></select>
        <button class="admin-btn" type="submit"><i class="fas fa-sliders"></i>Filter</button>
        @if(request()->hasAny(['search','category','status','author','date_from','date_to','sort']))<a class="filter-clear" href="{{ route('dashboard.posts.index') }}">Clear</a>@endif
    </form>
</section>

<form method="post" action="{{ route('dashboard.posts.bulk') }}" id="bulk-post-form">
    @csrf
    <section class="admin-card">
        <div class="bulk-bar">
            <span><i class="fas fa-layer-group"></i> <b id="selected-count">0</b> selected</span>
            <select name="bulk_action" required><option value="">Bulk action</option><option value="publish">Publish</option><option value="draft">Move to draft</option><option value="delete">Delete permanently</option></select>
            <button type="submit" class="admin-btn dark">Apply</button>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th><input type="checkbox" id="select-all-posts" aria-label="Select all posts"></th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td><input class="post-check" type="checkbox" name="post_ids[]" value="{{ $post->_id }}" aria-label="Select {{ $post->title }}"></td>
                        <td><b>{{ Str::limit($post->title, 52) }}</b></td>
                        <td>{{ $post->category?->category_name ?? 'Uncategorized' }}</td>
                        <td>{{ $post->author?->name ?? 'News Blog' }}</td>
                        <td><span class="status status-{{ $post->status }}">{{ $post->status }}</span></td>
                        <td>{{ number_format($post->views ?? 0) }}</td>
                        <td>{{ optional($post->published_at ?? $post->created_at)->format('M d, Y') }}</td>
                        <td><div class="admin-actions">
                            @if($post->status === 'published')<a href="{{ route('posts.show', $post) }}" target="_blank" title="View"><i class="fas fa-eye"></i></a>@endif
                            <a href="{{ route('dashboard.posts.edit', $post) }}"><i class="fas fa-edit"></i>Edit</a>
                            @if(auth()->user()->role === 'admin' || $post->author_id === (string) auth()->id())
                                <button class="delete row-delete" type="button" data-delete-url="{{ route('dashboard.posts.destroy', $post) }}"><i class="fas fa-trash"></i></button>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-admin">No posts match your filters.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $posts->links() }}</div>
    </section>
</form>

<form method="post" id="single-delete-form" hidden>@csrf @method('delete')</form>
@endsection

@push('scripts')
<script>
(() => {
    const checks = [...document.querySelectorAll('.post-check')];
    const all = document.getElementById('select-all-posts');
    const count = document.getElementById('selected-count');
    const update = () => count.textContent = checks.filter(item => item.checked).length;
    all?.addEventListener('change', () => { checks.forEach(item => item.checked = all.checked); update(); });
    checks.forEach(item => item.addEventListener('change', update));
    document.getElementById('bulk-post-form')?.addEventListener('submit', event => {
        if (!checks.some(item => item.checked)) { event.preventDefault(); alert('Select at least one post.'); return; }
        if (event.currentTarget.bulk_action.value === 'delete' && !confirm('Delete the selected posts permanently?')) event.preventDefault();
    });
    document.querySelectorAll('.row-delete').forEach(button => button.addEventListener('click', () => {
        if (!confirm('Delete this post permanently?')) return;
        const form = document.getElementById('single-delete-form');
        form.action = button.dataset.deleteUrl;
        form.submit();
    }));
})();
</script>
@endpush
