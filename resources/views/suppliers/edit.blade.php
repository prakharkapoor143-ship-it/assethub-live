@extends('layouts.app')
@section('title','Edit Supplier - AssetHub')
@section('heading','Edit Supplier')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('suppliers.update',$supplier) }}">@csrf @method('PUT') @include('suppliers._form')</form></div>@endsection
