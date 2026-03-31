@extends('layouts.app')

@section('title', 'Login - AssetHub')
@section('heading', 'Sign In')

@section('content')
<div class="card form-card">
    <div class="login-brand">AssetHub</div>
    <h2 class="login-title">Welcome Back</h2>
    <p class="login-subtitle">Sign in to continue managing your inventory.</p>

    @if($errors->any())
        <div class="errors">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="login-form">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="btn primary login-submit" type="submit">Sign In</button>
    </form>
</div>
@endsection
