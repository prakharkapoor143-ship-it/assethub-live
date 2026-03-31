@extends('layouts.app')

@section('title', 'Edit Accessory - AssetHub')
@section('heading', 'Edit Accessory')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('accessories.update', $accessory) }}">
        @csrf
        @method('PUT')
        @include('accessories._form')
    </form>
</div>
@endsection
