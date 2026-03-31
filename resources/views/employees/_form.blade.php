@if ($errors->any())
<div class="errors"><ul style="margin:0; padding-left:18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="field"><label for="name">Name</label><input id="name" name="name" type="text" value="{{ old('name', $employee->name ?? '') }}" required></div>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $employee->email ?? '') }}"></div>
<div class="field"><label for="department">Department</label><input id="department" name="department" type="text" value="{{ old('department', $employee->department ?? '') }}"></div>
<div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="text" value="{{ old('phone', $employee->phone ?? '') }}"></div>
<div class="field"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="4">{{ old('notes', $employee->notes ?? '') }}</textarea></div>
<div class="row"><button type="submit" class="btn primary">Save</button><a class="btn" href="{{ route('employees.index') }}">Cancel</a></div>
