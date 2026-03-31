@extends('layouts.app')
@section('title','Edit User - AssetHub')
@section('heading','Edit User')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('users.update',$user) }}">@csrf @method('PUT') @include('users._form')</form></div>@endsection
