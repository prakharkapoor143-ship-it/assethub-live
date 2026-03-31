<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $companies = Company::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', ['companies' => $companies, 'filters' => ['q' => $q]]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');
        return view('companies.create');
    }

    public function edit(Company $company): View
    {
        Gate::authorize('manage-inventory');
        return view('companies.edit', compact('company'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        Company::create($this->validateData($request, null));

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $company->update($this->validateData($request, $company->id));

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        Gate::authorize('admin-only');

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }

    private function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name' . ($id ? ',' . $id : '')],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
