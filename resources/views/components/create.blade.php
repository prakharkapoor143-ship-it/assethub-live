@extends('layouts.app')
@section('title','Create Component - AssetHub')
@section('heading','Create Component')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('components.store') }}">@csrf @include('components._form')</form></div>@endsection
