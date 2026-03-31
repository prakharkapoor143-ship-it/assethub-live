@extends('layouts.app')
@section('title','Consumable History - AssetHub')
@section('heading','History: ' . $consumable->name)
@section('content')<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Date</th><th>Type</th><th>Quantity</th><th>Counterparty</th><th>Notes</th></tr></thead><tbody>@forelse($transactions as $item)<tr><td>{{ $item->transacted_at?->format('Y-m-d H:i') }}</td><td>{{ ucfirst($item->type) }}</td><td>{{ $item->quantity }}</td><td>{{ $item->counterparty ?: '-' }}</td><td>{{ $item->notes ?: '-' }}</td></tr>@empty<tr><td colspan="5">No transaction history yet.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $transactions->links() }}</div>@endsection
