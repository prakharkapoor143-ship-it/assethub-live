@extends('layouts.app')
@section('title','Components - AssetHub')
@section('heading','Components')
@section('top_actions')
@can('manage-inventory')
<a href="{{ route('components.create') }}" class="btn primary">Add Component</a>
@endcan
@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters">
        <div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, SKU, model"></div>
        <div class="field"><label>Location</label><select name="location_id"><option value="">All</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((int)$filters['location_id']===$location->id)>{{ $location->name }}</option>@endforeach</select></div>
        <div class="field"><label>Stock</label><select name="stock"><option value="">All</option><option value="low" @selected($filters['stock']==='low')>Low</option><option value="healthy" @selected($filters['stock']==='healthy')>Healthy</option></select></div>
        <div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('components.index') }}" class="btn">Reset</a></div>
    </div>
</form>

<div class="card" style="margin-bottom:12px;">
    <div class="row">
        <a class="btn" href="{{ route('components.export.csv', request()->query()) }}">Export CSV</a>
        @can('manage-inventory')
        <form method="POST" action="{{ route('components.import.csv') }}" enctype="multipart/form-data" class="row">
            @csrf
            <input type="file" name="csv" accept=".csv,text/csv" required>
            <button class="btn" type="submit">Import CSV</button>
        </form>
        @endcan
    </div>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Name</th><th>SKU</th><th>Location</th><th>Total</th><th>Allocated</th><th>Available</th><th>Status</th><th style="width:420px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($components as $component)
            <tr>
                <td>{{ $component->name }}</td><td>{{ $component->sku ?: '-' }}</td><td>{{ $component->location?->name ?? '-' }}</td><td>{{ $component->quantity }}</td><td>{{ $component->allocated }}</td><td>{{ $component->available_quantity }}</td><td>@if($component->available_quantity <= $component->min_quantity)<span class="pill maintenance">Low</span>@else<span class="pill available">Healthy</span>@endif</td>
                <td><div class="row" style="flex-wrap:wrap; gap:6px;">@can('manage-inventory')<a class="btn" href="{{ route('components.edit',$component) }}">Edit</a><a class="btn" href="{{ route('components.allocate.form',$component) }}">Allocate</a><a class="btn" href="{{ route('components.release.form',$component) }}">Release</a>@endcan<a class="btn" href="{{ route('components.history',$component) }}">History</a>@can('admin-only')<form method="POST" action="{{ route('components.destroy',$component) }}" onsubmit="return confirm('Delete component?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>@endcan</div></td>
            </tr>
        @empty
            <tr><td colspan="8">No components found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $components->links() }}</div>
@endsection
