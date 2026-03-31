@extends('layouts.app')

@section('title', 'Check In Accessory - AssetHub')
@section('heading', 'Check In: ' . $accessory->name)

@section('content')
<div class="card form-card">
    <p style="margin-top:0; color:#6b7280;">Currently checked out: <strong>{{ $accessory->checked_out }}</strong></p>

    @if ($errors->any())
        <div class="errors">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('accessories.checkin', $accessory) }}">
        @csrf
        <div class="field">
            <label for="quantity">Quantity</label>
            <input id="quantity" name="quantity" type="number" min="1" max="{{ $accessory->checked_out }}" value="{{ old('quantity', 1) }}" required>
        </div>
        <div class="field">
            <label for="counterparty">Returned From (optional)</label>
            <input id="counterparty" name="counterparty" type="text" value="{{ old('counterparty') }}">
        </div>
        <div class="field">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
        </div>
        <div class="row">
            <button type="submit" class="btn primary">Confirm Check In</button>
            <a class="btn" href="{{ route('accessories.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
