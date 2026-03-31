<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $suppliers = Supplier::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('contact_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('suppliers.index', ['suppliers' => $suppliers, 'filters' => ['q' => $q]]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');
        return view('suppliers.create');
    }

    public function edit(Supplier $supplier): View
    {
        Gate::authorize('manage-inventory');
        return view('suppliers.edit', compact('supplier'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        Supplier::create($this->validateData($request, null));

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $supplier->update($this->validateData($request, $supplier->id));

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        Gate::authorize('admin-only');

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }

    private function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name' . ($id ? ',' . $id : '')],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
