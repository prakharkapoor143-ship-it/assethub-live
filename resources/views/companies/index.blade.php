@extends('layouts.app')
@section('title','Companies - AssetHub')
@section('heading','Companies')
@section('top_actions')
@can('manage-inventory')
<a href="{{ route('companies.create') }}" class="btn primary">Add Company</a>
@endcan
@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters" style="grid-template-columns: 1fr auto;">
        <div class="field">
            <label>Search</label>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, email, phone">
        </div>
        <div class="actions">
            <button class="btn primary" type="submit">Apply</button>
            <a href="{{ route('companies.index') }}" class="btn">Reset</a>
        </div>
    </div>
</form>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th style="width:170px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($companies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->email ?: '-' }}</td>
                <td>{{ $company->phone ?: '-' }}</td>
                <td>{{ $company->address ?: '-' }}</td>
                <td>
                    <div class="row">
                        @can('manage-inventory')
                        <a class="btn" href="{{ route('companies.edit',$company) }}">Edit</a>
                        @endcan
                        @can('admin-only')
                        <form method="POST" action="{{ route('companies.destroy',$company) }}" onsubmit="return confirm('Delete company?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn danger" type="submit">Delete</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No companies found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $companies->links() }}</div>
@endsection
