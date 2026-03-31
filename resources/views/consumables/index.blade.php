@extends('layouts.app')
@section('title','Consumables - AssetHub')
@section('heading','Consumables')
@section('top_actions')
@can('manage-inventory')
<a href="{{ route('consumables.create') }}" class="btn primary">Add Consumable</a>
@endcan
@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters">
        <div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, SKU"></div>
        <div class="field"><label>Location</label><select name="location_id"><option value="">All</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((int)$filters['location_id']===$location->id)>{{ $location->name }}</option>@endforeach</select></div>
        <div class="field"><label>Stock</label><select name="stock"><option value="">All</option><option value="low" @selected($filters['stock']==='low')>Low</option><option value="healthy" @selected($filters['stock']==='healthy')>Healthy</option></select></div>
        <div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('consumables.index') }}" class="btn">Reset</a></div>
    </div>
</form>

<div class="card" style="margin-bottom:12px;">
    <div class="row">
        <a class="btn" href="{{ route('consumables.export.csv', request()->query()) }}">Export CSV</a>
        @can('manage-inventory')
        <form method="POST" action="{{ route('consumables.import.csv') }}" enctype="multipart/form-data" class="row">
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
        <tr><th>Name</th><th>SKU</th><th>Location</th><th>Total</th><th>Consumed</th><th>Available</th><th>Status</th><th style="width:420px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($consumables as $consumable)
            <tr>
                <td>{{ $consumable->name }}</td><td>{{ $consumable->sku ?: '-' }}</td><td>{{ $consumable->location?->name ?? '-' }}</td><td>{{ $consumable->quantity }}</td><td>{{ $consumable->consumed }}</td><td>{{ $consumable->available_quantity }}</td><td>@if($consumable->available_quantity <= $consumable->min_quantity)<span class="pill maintenance">Low</span>@else<span class="pill available">Healthy</span>@endif</td><td><div class="row" style="flex-wrap:wrap; gap:6px;">@can('manage-inventory')<a class="btn" href="{{ route('consumables.edit',$consumable) }}">Edit</a><a class="btn" href="{{ route('consumables.consume.form',$consumable) }}">Consume</a><a class="btn" href="{{ route('consumables.restock.form',$consumable) }}">Restock</a>@endcan<a class="btn" href="{{ route('consumables.history',$consumable) }}">History</a>@can('admin-only')<form method="POST" action="{{ route('consumables.destroy',$consumable) }}" onsubmit="return confirm('Delete consumable?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>@endcan</div></td>
            </tr>
        @empty
            <tr><td colspan="8">No consumables found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $consumables->links() }}</div>
@endsection
