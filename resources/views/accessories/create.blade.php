@extends('layouts.app')

@section('title', 'New Accessory - AssetHub')
@section('heading', 'Create Accessory')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('accessories.store') }}">
        @csrf
        @include('accessories._form')
    </form>
</div>
@endsection
