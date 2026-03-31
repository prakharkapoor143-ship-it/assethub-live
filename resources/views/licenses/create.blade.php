@extends('layouts.app')
@section('title','Create License - AssetHub')
@section('heading','Create License')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('licenses.store') }}">@csrf @include('licenses._form')</form></div>@endsection
