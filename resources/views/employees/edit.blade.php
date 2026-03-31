@extends('layouts.app')
@section('title','Edit Employee - AssetHub')
@section('heading','Edit Employee')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('employees.update',$employee) }}">@csrf @method('PUT') @include('employees._form')</form></div>@endsection
