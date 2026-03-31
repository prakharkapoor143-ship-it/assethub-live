@extends('layouts.app')

@section('title', 'Edit Category - AssetHub')
@section('heading', 'Edit Category')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('categories._form')
    </form>
</div>
@endsection
