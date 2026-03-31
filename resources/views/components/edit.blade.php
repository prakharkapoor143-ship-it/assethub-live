@extends('layouts.app')
@section('title','Edit Component - AssetHub')
@section('heading','Edit Component')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('components.update',$component) }}">@csrf @method('PUT') @include('components._form')</form></div>@endsection
