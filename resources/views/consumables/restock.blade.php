@extends('layouts.app')
@section('title','Restock Item - AssetHub')
@section('heading','Restock: ' . $consumable->name)
@section('content')
<div class="card form-card">
@if ($errors->any())<div class="errors"><ul style="margin:0; padding-left:18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('consumables.restock',$consumable) }}">@csrf
<div class="field"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity',1) }}" required></div>
<div class="field"><label for="counterparty">Vendor/Source (optional)</label><input id="counterparty" name="counterparty" type="text" value="{{ old('counterparty') }}"></div>
<div class="field"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea></div>
<div class="row"><button class="btn primary" type="submit">Confirm Restock</button><a class="btn" href="{{ route('consumables.index') }}">Cancel</a></div></form></div>
@endsection
