@extends('layouts.app')
@section('title','Create Consumable - AssetHub')
@section('heading','Create Consumable')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('consumables.store') }}">@csrf @include('consumables._form')</form></div>@endsection
