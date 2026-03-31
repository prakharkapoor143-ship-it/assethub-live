@extends('layouts.app')
@section('title','Edit Consumable - AssetHub')
@section('heading','Edit Consumable')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('consumables.update',$consumable) }}">@csrf @method('PUT') @include('consumables._form')</form></div>@endsection
