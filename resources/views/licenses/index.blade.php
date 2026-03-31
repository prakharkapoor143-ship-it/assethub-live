@extends('layouts.app')
@section('title','Licenses - AssetHub')
@section('heading','Licenses')
@section('top_actions')
@can('manage-inventory')
<a href="{{ route('licenses.create') }}" class="btn primary">Add License</a>
@endcan
@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters" style="grid-template-columns: 1fr 220px auto;">
        <div class="field">
            <label>Search</label>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, key, company">
        </div>
        <div class="field">
            <label>Expires</label>
            <select name="expires">
                <option value="">Any</option>
                <option value="30" @selected($filters['expires']==='30')>Within 30 days</option>
            </select>
        </div>
        <div class="actions">
            <button class="btn primary" type="submit">Apply</button>
            <a href="{{ route('licenses.index') }}" class="btn">Reset</a>
        </div>
    </div>
</form>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Name</th><th>Company</th><th>Supplier</th><th>Seats Used/Total</th><th>Expires</th><th style="width:170px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($licenses as $license)
            <tr>
                <td>{{ $license->name }}</td>
                <td>{{ $license->company?->name ?? '-' }}</td>
                <td>{{ $license->supplier?->name ?? '-' }}</td>
                <td>{{ $license->seats_used }}/{{ $license->seats_total }}</td>
                <td>{{ $license->expires_at?->format('Y-m-d') ?? '-' }}</td>
                <td>
                    <div class="row">
                        @can('manage-inventory')
                        <a class="btn" href="{{ route('licenses.edit',$license) }}">Edit</a>
                        @endcan
                        @can('admin-only')
                        <form method="POST" action="{{ route('licenses.destroy',$license) }}" onsubmit="return confirm('Delete license?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn danger" type="submit">Delete</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No licenses found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $licenses->links() }}</div>
@endsection
