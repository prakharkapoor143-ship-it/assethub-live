@extends('layouts.app')
@section('title','Timeline - AssetHub')
@section('heading','Timeline')
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Module, item, counterparty"></div><div></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('timeline.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>When</th><th>Module</th><th>Action</th><th>Item</th><th>Qty</th><th>Counterparty</th></tr></thead><tbody>
@forelse($items as $item)
<tr><td>{{ $item['when'] ? \Carbon\Carbon::parse($item['when'])->format('Y-m-d H:i') : '-' }}</td><td>{{ $item['module'] }}</td><td>{{ $item['action'] }}</td><td>{{ $item['item'] ?: '-' }}</td><td>{{ $item['quantity'] }}</td><td>{{ $item['counterparty'] ?: '-' }}</td></tr>
@empty <tr><td colspan="6">No timeline activity yet.</td></tr> @endforelse
</tbody></table></div><div class="pagination-wrap">{{ $items->links() }}</div>
@endsection
