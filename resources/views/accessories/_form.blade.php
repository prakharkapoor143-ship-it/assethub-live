@if ($errors->any())
    <div class="errors">
        <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="field">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $accessory->name ?? '') }}" required>
</div>
<div class="field">
    <label for="model_number">Model Number</label>
    <input id="model_number" name="model_number" type="text" value="{{ old('model_number', $accessory->model_number ?? '') }}">
</div>
<div class="field">
    <label for="sku">SKU</label>
    <input id="sku" name="sku" type="text" value="{{ old('sku', $accessory->sku ?? '') }}">
</div>
<div class="field">
    <label for="location_id">Location</label>
    <select id="location_id" name="location_id">
        <option value="">Select location</option>
        @foreach($locations as $location)
            <option value="{{ $location->id }}" @selected((string) old('location_id', $accessory->location_id ?? '') === (string) $location->id)>{{ $location->name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="quantity">Total Quantity</label>
    <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $accessory->quantity ?? 0) }}" required>
</div>
<div class="field">
    <label for="checked_out">Checked Out</label>
    <input id="checked_out" name="checked_out" type="number" min="0" value="{{ old('checked_out', $accessory->checked_out ?? 0) }}" required>
</div>
<div class="field">
    <label for="min_quantity">Minimum Quantity Alert</label>
    <input id="min_quantity" name="min_quantity" type="number" min="0" value="{{ old('min_quantity', $accessory->min_quantity ?? 0) }}" required>
</div>
<div class="field">
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes" rows="4">{{ old('notes', $accessory->notes ?? '') }}</textarea>
</div>
<div class="row">
    <button type="submit" class="btn primary">Save</button>
    <a class="btn" href="{{ route('accessories.index') }}">Cancel</a>
</div>
