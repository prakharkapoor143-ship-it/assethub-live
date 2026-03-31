@extends('layouts.app')
@section('title','Reports - AssetHub')
@section('heading','Reports')
@section('content')
<div class="stats">@foreach($kpis as $label => $value)<div class="card stat"><div class="label">{{ $label }}</div><div class="value">{{ $value }}</div></div>@endforeach</div>
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Category Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Filter by category name"></div><div></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('reports.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Category</th><th>Assets</th></tr></thead><tbody>
@forelse($assetsByCategory as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2">No category-based asset data yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $assetsByCategory->links() }}</div>
@endsection
