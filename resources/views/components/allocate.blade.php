@extends('layouts.app')
@section('title','Allocate Component - AssetHub')
@section('heading','Allocate: ' . $component->name)
@section('content')
<div class="card form-card"><p style="margin-top:0; color:#6b7280;">Available right now: <strong>{{ $component->available_quantity }}</strong></p>
@if ($errors->any())<div class="errors"><ul style="margin:0; padding-left:18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('components.allocate',$component) }}">@csrf
<div class="field"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="1" max="{{ $component->available_quantity }}" value="{{ old('quantity',1) }}" required></div>
<div class="field"><label for="counterparty">Allocated To</label><input id="counterparty" name="counterparty" type="text" value="{{ old('counterparty') }}" required></div>
<div class="field"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea></div>
<div class="row"><button class="btn primary" type="submit">Confirm Allocation</button><a class="btn" href="{{ route('components.index') }}">Cancel</a></div></form></div>
@endsection
