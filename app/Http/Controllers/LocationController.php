<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $locations = Location::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%")->orWhere('notes', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('locations.index', ['locations' => $locations, 'filters' => ['q' => $q]]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');
        return view('locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:locations,name'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Location created.');
    }

    public function edit(Location $location): View
    {
        Gate::authorize('manage-inventory');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:locations,name,' . $location->id],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Location updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        Gate::authorize('admin-only');

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted.');
    }
}
