@extends('layouts.app')

@section('title', 'Dashboard - AssetHub')
@section('heading', 'Dashboard')

@section('content')
<div class="stats">
    <div class="card stat">
        <div class="label">Total Assets</div>
        <div class="value">{{ $assetCount }}</div>
    </div>
    <div class="card stat">
        <div class="label">Categories</div>
        <div class="value">{{ $categoryCount }}</div>
    </div>
    <div class="card stat">
        <div class="label">Locations</div>
        <div class="value">{{ $locationCount }}</div>
    </div>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
        <tr>
            <th>Asset Tag</th>
            <th>Name</th>
            <th>Category</th>
            <th>Location</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($recentAssets as $asset)
            <tr>
                <td>{{ $asset->asset_tag }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->category?->name ?? '-' }}</td>
                <td>{{ $asset->location?->name ?? '-' }}</td>
                <td><span class="pill {{ $asset->status }}">{{ ucfirst($asset->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="5">No assets yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
