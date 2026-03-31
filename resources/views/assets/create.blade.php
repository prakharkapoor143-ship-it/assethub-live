@extends('layouts.app')

@section('title', 'New Asset - AssetHub')
@section('heading', 'Create Asset')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('assets.store') }}">
        @csrf
        @include('assets._form')
    </form>
</div>
@endsection
