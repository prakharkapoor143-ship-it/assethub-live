<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\LicenseRecord;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class LicenseController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());
        $expires = trim($request->string('expires')->toString());

        $licenses = LicenseRecord::query()
            ->with(['company', 'supplier'])
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('license_key', 'like', "%{$q}%")
                        ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($expires === '30', fn ($query) => $query->whereNotNull('expires_at')->whereDate('expires_at', '<=', now()->addDays(30)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('licenses.index', [
            'licenses' => $licenses,
            'filters' => ['q' => $q, 'expires' => $expires],
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');

        return view('licenses.create', ['companies' => Company::query()->orderBy('name')->get(), 'suppliers' => Supplier::query()->orderBy('name')->get()]);
    }

    public function edit(LicenseRecord $license): View
    {
        Gate::authorize('manage-inventory');

        return view('licenses.edit', ['license' => $license, 'companies' => Company::query()->orderBy('name')->get(), 'suppliers' => Supplier::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateData($request);
        if ($data['seats_used'] > $data['seats_total']) {
            return back()->withErrors(['seats_used' => 'Used seats cannot exceed total seats.'])->withInput();
        }

        LicenseRecord::create($data);
        return redirect()->route('licenses.index')->with('success', 'License created.');
    }

    public function update(Request $request, LicenseRecord $license): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateData($request);
        if ($data['seats_used'] > $data['seats_total']) {
            return back()->withErrors(['seats_used' => 'Used seats cannot exceed total seats.'])->withInput();
        }

        $license->update($data);
        return redirect()->route('licenses.index')->with('success', 'License updated.');
    }

    public function destroy(LicenseRecord $license): RedirectResponse
    {
        Gate::authorize('admin-only');

        $license->delete();
        return redirect()->route('licenses.index')->with('success', 'License deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'seats_total' => ['required', 'integer', 'min:0'],
            'seats_used' => ['required', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
