@extends('layouts.app')
@section('title','Alerts - AssetHub')
@section('heading','Alerts')
@section('content')
<div class="stats">
<div class="card stat"><div class="label">Low Accessories</div><div class="value">{{ $lowAccessories->count() }}</div></div>
<div class="card stat"><div class="label">Low Components</div><div class="value">{{ $lowComponents->count() }}</div></div>
<div class="card stat"><div class="label">Low Consumables</div><div class="value">{{ $lowConsumables->count() }}</div></div>
</div>
<div class="card" style="margin-bottom:12px;"><strong>Expiring Licenses (30 days)</strong><div style="margin-top:8px; color:#6b7280;">{{ $expiringLicenses->count() }} found</div></div>
@endsection
