@extends('layouts.app')
@section('title','Create Employee - AssetHub')
@section('heading','Create Employee')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('employees.store') }}">@csrf @include('employees._form')</form></div>@endsection
