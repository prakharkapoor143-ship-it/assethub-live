@extends('layouts.app')

@section('title', 'Categories - AssetHub')
@section('heading', 'Categories')

@section('top_actions')
@can('manage-inventory')
<a href="{{ route('categories.create') }}" class="btn primary">Add Category</a>
@endcan
@endsection

@section('content')
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name or notes"></div><div></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('categories.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Name</th><th>Notes</th><th style="width:170px;">Actions</th></tr></thead><tbody>
@forelse($categories as $category)
<tr><td>{{ $category->name }}</td><td>{{ $category->notes ?: '-' }}</td><td><div class="row">@can('manage-inventory')<a class="btn" href="{{ route('categories.edit', $category) }}">Edit</a>@endcan @can('admin-only')<form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete category?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>@endcan</div></td></tr>
@empty <tr><td colspan="3">No categories found.</td></tr> @endforelse
</tbody></table></div><div class="pagination-wrap">{{ $categories->links() }}</div>
@endsection
