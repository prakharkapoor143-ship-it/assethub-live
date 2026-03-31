@extends('layouts.app')
@section('title','Edit Company - AssetHub')
@section('heading','Edit Company')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('companies.update',$company) }}">@csrf @method('PUT') @include('companies._form')</form></div>@endsection
