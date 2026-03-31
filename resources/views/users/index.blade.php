@extends('layouts.app')
@section('title','Users - AssetHub')
@section('heading','Users')
@section('top_actions')<a href="{{ route('users.create') }}" class="btn primary">Add User</a>@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name or email"></div><div class="field"><label>Role</label><select name="role"><option value="">All</option>@foreach(['admin','manager','viewer'] as $role)<option value="{{ $role }}" @selected($filters['role']===$role)>{{ ucfirst($role) }}</option>@endforeach</select></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('users.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th style="width:170px;">Actions</th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ ucfirst($user->role) }}</td><td>{{ $user->created_at?->format('Y-m-d') }}</td><td><div class="row"><a class="btn" href="{{ route('users.edit',$user) }}">Edit</a><form method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form></div></td></tr>
@empty <tr><td colspan="5">No users yet.</td></tr> @endforelse
</tbody></table></div><div class="pagination-wrap">{{ $users->links() }}</div>
@endsection
