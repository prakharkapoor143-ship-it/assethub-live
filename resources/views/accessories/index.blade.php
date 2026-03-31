@extends('layouts.app')

@section('title', 'Accessories - AssetHub')
@section('heading', 'Accessories')

@section('top_actions')
@can('manage-inventory')
<a href="{{ route('accessories.create') }}" class="btn primary">Add Accessory</a>
@endcan
@endsection

@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters">
        <div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, SKU, model"></div>
        <div class="field"><label>Location</label><select name="location_id"><option value="">All</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((int)$filters['location_id']===$location->id)>{{ $location->name }}</option>@endforeach</select></div>
        <div class="field"><label>Stock</label><select name="stock"><option value="">All</option><option value="low" @selected($filters['stock']==='low')>Low</option><option value="healthy" @selected($filters['stock']==='healthy')>Healthy</option></select></div>
        <div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('accessories.index') }}" class="btn">Reset</a></div>
    </div>
</form>

<div class="row" style="margin-bottom:12px;">
    <a class="btn" href="{{ route('accessories.export.csv', request()->query()) }}">Export CSV</a>
    @can('manage-inventory')
    <form method="POST" action="{{ route('accessories.import.csv') }}" enctype="multipart/form-data" class="row">@csrf <input type="file" name="csv" accept=".csv,text/csv" required><button class="btn" type="submit">Import CSV</button></form>
    @endcan
</div>
<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Name</th><th>SKU</th><th>Location</th><th>Total</th><th>Checked Out</th><th>Available</th><th style="width:220px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($accessories as $accessory)
            <tr>
                <td>{{ $accessory->name }}</td><td>{{ $accessory->sku ?: '-' }}</td><td>{{ $accessory->location?->name ?? '-' }}</td><td>{{ $accessory->quantity }}</td><td>{{ $accessory->checked_out }}</td><td>{{ $accessory->available_quantity }}</td>
                <td>
                    <div class="row" style="flex-wrap: wrap; gap:6px;">
                        @can('manage-inventory')
                        <a class="btn" href="{{ route('accessories.edit', $accessory) }}">Edit</a>
                        @endcan
                        @can('admin-only')
                        <form action="{{ route('accessories.destroy', $accessory) }}" method="POST" onsubmit="return confirm('Delete accessory?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No accessories found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $accessories->links() }}</div>
@endsection
