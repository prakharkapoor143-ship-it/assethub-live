@extends('layouts.app')

@section('title', 'Edit Asset - AssetHub')
@section('heading', 'Edit Asset')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('assets.update', $asset) }}">
        @csrf
        @method('PUT')
        @include('assets._form')
    </form>
</div>
@endsection
