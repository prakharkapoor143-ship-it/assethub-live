<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());
        $role = trim($request->string('role')->toString());

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->when(in_array($role, ['admin', 'manager', 'viewer'], true), fn ($query) => $query->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', ['users' => $users, 'filters' => ['q' => $q, 'role' => $role]]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'manager', 'viewer'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'viewer'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (User::count() <= 1) {
            return back()->withErrors(['user' => 'At least one user must remain in the system.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
