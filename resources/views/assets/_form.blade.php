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
    <label for="asset_tag">Asset Tag</label>
    <input id="asset_tag" name="asset_tag" type="text" value="{{ old('asset_tag', $asset->asset_tag ?? '') }}" required>
</div>
<div class="field">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $asset->name ?? '') }}" required>
</div>
<div class="field">
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id">
        <option value="">Select category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $asset->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="location_id">Location</label>
    <select id="location_id" name="location_id">
        <option value="">Select location</option>
        @foreach($locations as $location)
            <option value="{{ $location->id }}" @selected((string) old('location_id', $asset->location_id ?? '') === (string) $location->id)>{{ $location->name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="employee_id">Assigned Person</label>
    <select id="employee_id" name="employee_id">
        <option value="">Unassigned</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected((string) old('employee_id', $asset->employee_id ?? '') === (string) $employee->id)>{{ $employee->name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="status">Status</label>
    <select id="status" name="status" required>
        @foreach(['available', 'assigned', 'maintenance', 'retired'] as $status)
            <option value="{{ $status }}" @selected(old('status', $asset->status ?? 'available') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="purchase_date">Purchase Date</label>
    <input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date', $asset->purchase_date ?? '') }}">
</div>
<div class="field">
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes" rows="4">{{ old('notes', $asset->notes ?? '') }}</textarea>
</div>
<div class="row">
    <button type="submit" class="btn primary">Save</button>
    <a class="btn" href="{{ route('assets.index') }}">Cancel</a>
</div>
