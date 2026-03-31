@extends('layouts.app')
@section('title','Create User - AssetHub')
@section('heading','Create User')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('users.store') }}">@csrf @include('users._form')</form></div>@endsection
