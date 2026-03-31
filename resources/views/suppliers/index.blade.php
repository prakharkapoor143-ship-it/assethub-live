@extends('layouts.app')
@section('title','Suppliers - AssetHub')
@section('heading','Suppliers')
@section('top_actions')
@can('manage-inventory')
<a href="{{ route('suppliers.create') }}" class="btn primary">Add Supplier</a>
@endcan
@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;">
    <div class="filters" style="grid-template-columns: 1fr auto;">
        <div class="field">
            <label>Search</label>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, contact, email">
        </div>
        <div class="actions">
            <button class="btn primary" type="submit">Apply</button>
            <a href="{{ route('suppliers.index') }}" class="btn">Reset</a>
        </div>
    </div>
</form>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr><th>Name</th><th>Contact</th><th>Email</th><th>Phone</th><th style="width:170px;">Actions</th></tr>
        </thead>
        <tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->contact_name ?: '-' }}</td>
                <td>{{ $supplier->email ?: '-' }}</td>
                <td>{{ $supplier->phone ?: '-' }}</td>
                <td>
                    <div class="row">
                        @can('manage-inventory')
                        <a class="btn" href="{{ route('suppliers.edit',$supplier) }}">Edit</a>
                        @endcan
                        @can('admin-only')
                        <form method="POST" action="{{ route('suppliers.destroy',$supplier) }}" onsubmit="return confirm('Delete supplier?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn danger" type="submit">Delete</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No suppliers found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrap">{{ $suppliers->links() }}</div>
@endsection
