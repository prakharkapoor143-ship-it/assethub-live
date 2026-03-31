@extends('layouts.app')
@section('title','Employees - AssetHub')
@section('heading','Employees')
@section('top_actions')<a href="{{ route('employees.create') }}" class="btn primary">Add Employee</a>@endsection
@section('content')
<form method="GET" class="card" style="margin-bottom:12px;"><div class="filters"><div class="field"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Name, email, department"></div><div></div><div></div><div class="actions"><button class="btn primary" type="submit">Apply</button><a href="{{ route('employees.index') }}" class="btn">Reset</a></div></div></form>
<div class="card" style="padding:0; overflow:hidden;"><table><thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Phone</th><th style="width:170px;">Actions</th></tr></thead><tbody>
@forelse($employees as $employee)
<tr><td>{{ $employee->name }}</td><td>{{ $employee->email ?: '-' }}</td><td>{{ $employee->department ?: '-' }}</td><td>{{ $employee->phone ?: '-' }}</td><td><div class="row"><a class="btn" href="{{ route('employees.edit',$employee) }}">Edit</a> @can('admin-only')<form method="POST" action="{{ route('employees.destroy',$employee) }}" onsubmit="return confirm('Delete employee?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>@endcan</div></td></tr>
@empty <tr><td colspan="5">No employees found.</td></tr> @endforelse
</tbody></table></div><div class="pagination-wrap">{{ $employees->links() }}</div>
@endsection
