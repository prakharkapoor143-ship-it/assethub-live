@if ($errors->any())<div class="errors"><ul style="margin:0; padding-left:18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="field"><label for="name">Name</label><input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required></div>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required></div>
<div class="field"><label for="role">Role</label><select id="role" name="role" required>@foreach(['admin','manager','viewer'] as $role)<option value="{{ $role }}" @selected(old('role', $user->role ?? 'viewer')===$role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
<div class="field"><label for="password">Password {{ isset($user) ? '(leave empty to keep existing)' : '' }}</label><input id="password" name="password" type="password" {{ isset($user) ? '' : 'required' }}></div>
<div class="row"><button type="submit" class="btn primary">Save</button><a class="btn" href="{{ route('users.index') }}">Cancel</a></div>
