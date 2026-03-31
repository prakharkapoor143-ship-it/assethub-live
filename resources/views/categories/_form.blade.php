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
    <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required>
</div>
<div class="field">
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes" rows="4">{{ old('notes', $category->notes ?? '') }}</textarea>
</div>
<div class="row">
    <button type="submit" class="btn primary">Save</button>
    <a class="btn" href="{{ route('categories.index') }}">Cancel</a>
</div>
