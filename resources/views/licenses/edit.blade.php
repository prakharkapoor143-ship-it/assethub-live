@extends('layouts.app')
@section('title','Edit License - AssetHub')
@section('heading','Edit License')
@section('content')<div class="card form-card"><form method="POST" action="{{ route('licenses.update',$license) }}">@csrf @method('PUT') @include('licenses._form')</form></div>@endsection
