@extends('layouts.app')

@section('title', 'Assets - AssetHub')
@section('heading', 'Assets')

@section('top_actions')
@can('manage-inventory')
<a href="{{ route('assets.create') }}" class="btn primary">Add Asset</a>
@endcan
@endsection

@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters" style="grid-template-columns: repeat(5, minmax(0, 1fr));">
        <div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tag, name, notes, person"></div>
        <div class="field"><label>Status</label><select name="status"><option value="">All</option>@foreach(['available','assigned','maintenance','retired'] as $status)<option value="{{ $status }}" @selected($filters['status']===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="field"><label>Location</label><select name="location_id"><option value="">All</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((int)$filters['location_id']===$location->id)>{{ $location->name }}</option>@endforeach</select></div>
        <div class="field"><label>Assigned Person</label><select name="employee_id"><option value="">All</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((int)$filters['employee_id']===$employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
        <div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('assets.index') }}" class="btn">Reset</a></div>
    </div>
</form>

<div class="row" style="margin-bottom:12px;">
    <a class="btn" href="{{ route('assets.export.csv', request()->query()) }}">Export CSV</a>
    @can('manage-inventory')
    <form method="POST" action="{{ route('assets.import.csv') }}" enctype="multipart/form-data" class="row">
        @csrf
        <input type="file" name="csv" accept=".csv,text/csv" required>
        <button class="btn" type="submit">Import CSV</button>
    </form>
    @endcan
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Asset Tag</th><th>Name</th><th>Category</th><th>Location</th><th>Assigned To</th><th>Status</th><th style="width:170px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($assets as $asset)
            <tr>
                <td>{{ $asset->asset_tag }}</td><td>{{ $asset->name }}</td><td>{{ $asset->category?->name ?? '-' }}</td><td>{{ $asset->location?->name ?? '-' }}</td><td>{{ $asset->employee?->name ?? '-' }}</td><td><span class="pill {{ $asset->status }}">{{ ucfirst($asset->status) }}</span></td>
                <td><div class="row">
                    @can('manage-inventory')<a class="btn" href="{{ route('assets.edit', $asset) }}">Edit</a>@endcan
                    @can('admin-only')
                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete asset?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
                    @endcan
                </div></td>
            </tr>
        @empty
            <tr><td colspan="7">No assets found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $assets->links() }}</div>
@endsection
