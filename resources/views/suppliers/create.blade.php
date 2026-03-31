@extends('layouts.app')
@section('title','Create Supplier - AssetHub')
@section('heading','Create Supplier')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('suppliers.store') }}">@csrf @include('suppliers._form')</form></div>@endsection
