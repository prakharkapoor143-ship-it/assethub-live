@extends('layouts.app')

@section('title', 'New Location - AssetHub')
@section('heading', 'Create Location')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('locations.store') }}">
        @csrf
        @include('locations._form')
    </form>
</div>
@endsection
