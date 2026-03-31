<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\ComponentTransaction;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ComponentController extends Controller
{
    public function index(Request $request): View
    {
        $components = $this->filteredQuery($request)->latest()->paginate(15)->withQueryString();

        return view('components.index', [
            'components' => $components,
            'locations' => Location::query()->orderBy('name')->get(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'location_id' => $request->integer('location_id'),
                'stock' => $request->string('stock')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');

        return view('components.create', ['locations' => Location::query()->orderBy('name')->get()]);
    }

    public function edit(Component $component): View
    {
        Gate::authorize('manage-inventory');

        return view('components.edit', ['component' => $component, 'locations' => Location::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        Component::create($this->validateData($request, null));

        return redirect()->route('components.index')->with('success', 'Component created.');
    }

    public function update(Request $request, Component $component): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateData($request, $component->id);
        if ($data['allocated'] > $data['quantity']) {
            return back()->withErrors(['allocated' => 'Allocated cannot exceed total quantity.'])->withInput();
        }

        $component->update($data);

        return redirect()->route('components.index')->with('success', 'Component updated.');
    }

    public function destroy(Component $component): RedirectResponse
    {
        Gate::authorize('admin-only');

        $component->delete();

        return redirect()->route('components.index')->with('success', 'Component deleted.');
    }

    public function allocateForm(Component $component): View
    {
        Gate::authorize('manage-inventory');

        return view('components.allocate', compact('component'));
    }

    public function releaseForm(Component $component): View
    {
        Gate::authorize('manage-inventory');

        return view('components.release', compact('component'));
    }

    public function history(Component $component): View
    {
        return view('components.history', [
            'component' => $component,
            'transactions' => $component->transactions()->paginate(15)->withQueryString(),
        ]);
    }

    public function allocate(Request $request, Component $component): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['quantity'] > $component->available_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity exceeds available stock.'])->withInput();
        }

        $component->increment('allocated', $data['quantity']);

        ComponentTransaction::create([
            'component_id' => $component->id,
            'type' => 'allocate',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'],
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('components.index')->with('success', 'Component allocated.');
    }

    public function release(Request $request, Component $component): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['quantity'] > $component->allocated) {
            return back()->withErrors(['quantity' => 'Release quantity cannot exceed allocated amount.'])->withInput();
        }

        $component->decrement('allocated', $data['quantity']);

        ComponentTransaction::create([
            'component_id' => $component->id,
            'type' => 'release',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'] ?? null,
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('components.index')->with('success', 'Component released.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'model_number', 'sku', 'location', 'quantity', 'allocated', 'min_quantity', 'notes']);

            foreach ($rows as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->model_number,
                    $item->sku,
                    $item->location?->name,
                    $item->quantity,
                    $item->allocated,
                    $item->min_quantity,
                    $item->notes,
                ]);
            }

            fclose($handle);
        }, 'components-export.csv', ['Content-Type' => 'text/csv']);
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
            $name = $this->csvValue($row, $map, 'name');
            if ($name === '') {
                continue;
            }

            $locationName = $this->csvValue($row, $map, 'location');
            $locationId = $locationName !== ''
                ? Location::query()->firstOrCreate(['name' => $locationName], ['address' => null, 'notes' => null])->id
                : null;

            $attributes = [
                'name' => $name,
                'model_number' => $this->nullableValue($this->csvValue($row, $map, 'model_number')),
                'sku' => $this->nullableValue($this->csvValue($row, $map, 'sku')),
                'location_id' => $locationId,
                'quantity' => max(0, (int) $this->csvValue($row, $map, 'quantity')),
                'allocated' => max(0, (int) $this->csvValue($row, $map, 'allocated')),
                'min_quantity' => max(0, (int) $this->csvValue($row, $map, 'min_quantity')),
                'notes' => $this->nullableValue($this->csvValue($row, $map, 'notes')),
            ];

            if ($attributes['allocated'] > $attributes['quantity']) {
                $attributes['allocated'] = $attributes['quantity'];
            }

            $sku = $attributes['sku'];
            if ($sku !== null) {
                Component::query()->updateOrCreate(['sku' => $sku], $attributes);
            } else {
                Component::query()->create($attributes);
            }

            $imported++;
        }

        fclose($handle);

        return redirect()->route('components.index')->with('success', "Imported {$imported} component rows.");
    }

    private function filteredQuery(Request $request)
    {
        $q = trim($request->string('q')->toString());
        $locationId = $request->integer('location_id');
        $stock = trim($request->string('stock')->toString());

        return Component::query()
            ->with('location')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('model_number', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('notes', 'like', "%{$q}%");
                });
            })
            ->when($locationId > 0, fn ($query) => $query->where('location_id', $locationId))
            ->when($stock === 'low', fn ($query) => $query->whereRaw('quantity - allocated <= min_quantity'))
            ->when($stock === 'healthy', fn ($query) => $query->whereRaw('quantity - allocated > min_quantity'));
    }

    private function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model_number' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('components', 'sku')->ignore($id)],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'allocated' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
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

    private function nullableValue(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
