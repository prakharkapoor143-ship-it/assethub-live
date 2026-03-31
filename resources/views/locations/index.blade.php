@extends('layouts.app')

@section('title', 'Locations - AssetHub')
@section('heading', 'Locations')

@section('top_actions')
@can('manage-inventory')
<a href="{{ route('locations.create') }}" class="btn primary">Add Location</a>
@endcan
@endsection

@section('content')
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, address, notes"></div><div></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('locations.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Name</th><th>Address</th><th>Notes</th><th style="width:170px;">Actions</th></tr></thead><tbody>
@forelse($locations as $location)
<tr><td>{{ $location->name }}</td><td>{{ $location->address ?: '-' }}</td><td>{{ $location->notes ?: '-' }}</td><td><div class="row">@can('manage-inventory')<a class="btn" href="{{ route('locations.edit', $location) }}">Edit</a>@endcan @can('admin-only')<form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete location?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>@endcan</div></td></tr>
@empty <tr><td colspan="4">No locations found.</td></tr> @endforelse
</tbody></table></div><div class="pagination-wrap">{{ $locations->links() }}</div>
@endsection
