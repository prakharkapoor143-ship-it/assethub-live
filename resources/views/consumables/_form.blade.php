@if ($errors->any())<div class="errors"><ul style="margin:0; padding-left:18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="field"><label for="name">Name</label><input id="name" name="name" type="text" value="{{ old('name', $consumable->name ?? '') }}" required></div>
<div class="field"><label for="sku">SKU</label><input id="sku" name="sku" type="text" value="{{ old('sku', $consumable->sku ?? '') }}"></div>
<div class="field"><label for="location_id">Location</label><select id="location_id" name="location_id"><option value="">Select location</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) old('location_id',$consumable->location_id ?? '') === (string) $location->id)>{{ $location->name }}</option>@endforeach</select></div>
<div class="field"><label for="quantity">Total Quantity</label><input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $consumable->quantity ?? 0) }}" required></div>
<div class="field"><label for="consumed">Consumed</label><input id="consumed" name="consumed" type="number" min="0" value="{{ old('consumed', $consumable->consumed ?? 0) }}" required></div>
<div class="field"><label for="min_quantity">Minimum Quantity Alert</label><input id="min_quantity" name="min_quantity" type="number" min="0" value="{{ old('min_quantity', $consumable->min_quantity ?? 0) }}" required></div>
<div class="field"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="4">{{ old('notes', $consumable->notes ?? '') }}</textarea></div>
<div class="row"><button type="submit" class="btn primary">Save</button><a class="btn" href="{{ route('consumables.index') }}">Cancel</a></div>
