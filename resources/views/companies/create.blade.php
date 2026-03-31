@extends('layouts.app')
@section('title','Create Company - AssetHub')
@section('heading','Create Company')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('companies.store') }}">@csrf @include('companies._form')</form></div>@endsection
