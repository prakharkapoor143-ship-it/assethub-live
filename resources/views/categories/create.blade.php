@extends('layouts.app')

@section('title', 'New Category - AssetHub')
@section('heading', 'Create Category')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories._form')
    </form>
</div>
@endsection
