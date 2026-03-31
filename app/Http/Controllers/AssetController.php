<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = $this->filteredQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'locations' => Location::query()->orderBy('name')->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'status' => $request->string('status')->toString(),
                'location_id' => $request->integer('location_id'),
                'employee_id' => $request->integer('employee_id'),
            ],
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');

        return view('assets.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateAsset($request, null);
        Asset::create($data);

        return redirect()->route('assets.index')->with('success', 'Asset created.');
    }

    public function edit(Asset $asset): View
    {
        Gate::authorize('manage-inventory');

        return view('assets.edit', [
            'asset' => $asset,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateAsset($request, $asset->id);
        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Asset updated.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        Gate::authorize('admin-only');

        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['asset_tag', 'name', 'category', 'location', 'assigned_employee', 'status', 'purchase_date', 'notes']);

            foreach ($rows as $asset) {
                fputcsv($handle, [
                    $asset->asset_tag,
                    $asset->name,
                    $asset->category?->name,
                    $asset->location?->name,
                    $asset->employee?->name,
                    $asset->status,
                    $asset->purchase_date,
                    $asset->notes,
                ]);
            }

            fclose($handle);
        }, 'assets-export.csv', ['Content-Type' => 'text/csv']);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->withErrors(['csv' => 'Unable to read uploaded CSV file.']);
        }

        $header = fgetcsv($handle);
        if (!is_array($header)) {
            fclose($handle);
            return back()->withErrors(['csv' => 'CSV header row is missing.']);
        }

        $map = array_flip(array_map(static fn ($col) => strtolower(trim((string) $col)), $header));
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $assetTag = $this->csvValue($row, $map, 'asset_tag');
            $name = $this->csvValue($row, $map, 'name');
            if ($assetTag === '' || $name === '') {
                continue;
            }

            $categoryName = $this->csvValue($row, $map, 'category');
            $locationName = $this->csvValue($row, $map, 'location');
            $employeeName = $this->csvValue($row, $map, 'assigned_employee');

            $categoryId = $categoryName !== ''
                ? Category::query()->firstOrCreate(['name' => $categoryName], ['notes' => null])->id
                : null;
            $locationId = $locationName !== ''
                ? Location::query()->firstOrCreate(['name' => $locationName], ['address' => null, 'notes' => null])->id
                : null;
            $employeeId = $employeeName !== ''
                ? Employee::query()->firstOrCreate(['name' => $employeeName], ['email' => null, 'phone' => null, 'department' => null, 'notes' => null])->id
                : null;

            Asset::query()->updateOrCreate(
                ['asset_tag' => $assetTag],
                [
                    'name' => $name,
                    'category_id' => $categoryId,
                    'location_id' => $locationId,
                    'employee_id' => $employeeId,
                    'status' => $this->normalizeStatus($this->csvValue($row, $map, 'status')),
                    'purchase_date' => $this->nullableValue($this->csvValue($row, $map, 'purchase_date')),
                    'notes' => $this->nullableValue($this->csvValue($row, $map, 'notes')),
                ]
            );

            $imported++;
        }

        fclose($handle);

        return redirect()->route('assets.index')->with('success', "Imported {$imported} asset rows.");
    }

    private function filteredQuery(Request $request)
    {
        $q = trim($request->string('q')->toString());
        $status = trim($request->string('status')->toString());
        $locationId = $request->integer('location_id');
        $employeeId = $request->integer('employee_id');

        return Asset::query()
            ->with(['category', 'location', 'employee'])
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('asset_tag', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('notes', 'like', "%{$q}%")
                        ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($locationId > 0, fn ($query) => $query->where('location_id', $locationId))
            ->when($employeeId > 0, fn ($query) => $query->where('employee_id', $employeeId));
    }

    private function validateAsset(Request $request, ?int $assetId): array
    {
        $uniqueRule = 'unique:assets,asset_tag';
        if ($assetId !== null) {
            $uniqueRule .= ',' . $assetId;
        }

        return $request->validate([
            'asset_tag' => ['required', 'string', 'max:60', $uniqueRule],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'status' => ['required', 'in:available,assigned,maintenance,retired'],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function csvValue(array $row, array $map, string $key): string
    {
        $index = $map[$key] ?? null;
        if (!is_int($index)) {
            return '';
        }

        return trim((string) ($row[$index] ?? ''));
    }

    private function normalizeStatus(string $status): string
    {
        $normalized = strtolower(trim($status));
        return in_array($normalized, ['available', 'assigned', 'maintenance', 'retired'], true)
            ? $normalized
            : 'available';
    }

    private function nullableValue(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
