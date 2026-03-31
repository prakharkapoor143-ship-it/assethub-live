@extends('layouts.app')

@section('title', 'Edit Location - AssetHub')
@section('heading', 'Edit Location')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('locations.update', $location) }}">
        @csrf
        @method('PUT')
        @include('locations._form')
    </form>
</div>
@endsection
