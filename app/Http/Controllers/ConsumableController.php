<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableTransaction;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ConsumableController extends Controller
{
    public function index(Request $request): View
    {
        $consumables = $this->filteredQuery($request)->latest()->paginate(15)->withQueryString();

        return view('consumables.index', [
            'consumables' => $consumables,
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

        return view('consumables.create', ['locations' => Location::query()->orderBy('name')->get()]);
    }

    public function edit(Consumable $consumable): View
    {
        Gate::authorize('manage-inventory');

        return view('consumables.edit', ['consumable' => $consumable, 'locations' => Location::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        Consumable::create($this->validateData($request, null));

        return redirect()->route('consumables.index')->with('success', 'Consumable created.');
    }

    public function update(Request $request, Consumable $consumable): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateData($request, $consumable->id);
        if ($data['consumed'] > $data['quantity']) {
            return back()->withErrors(['consumed' => 'Consumed cannot exceed total quantity.'])->withInput();
        }

        $consumable->update($data);

        return redirect()->route('consumables.index')->with('success', 'Consumable updated.');
    }

    public function destroy(Consumable $consumable): RedirectResponse
    {
        Gate::authorize('admin-only');

        $consumable->delete();

        return redirect()->route('consumables.index')->with('success', 'Consumable deleted.');
    }

    public function consumeForm(Consumable $consumable): View
    {
        Gate::authorize('manage-inventory');

        return view('consumables.consume', compact('consumable'));
    }

    public function restockForm(Consumable $consumable): View
    {
        Gate::authorize('manage-inventory');

        return view('consumables.restock', compact('consumable'));
    }

    public function history(Consumable $consumable): View
    {
        return view('consumables.history', [
            'consumable' => $consumable,
            'transactions' => $consumable->transactions()->paginate(15)->withQueryString(),
        ]);
    }

    public function consume(Request $request, Consumable $consumable): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['quantity'] > $consumable->available_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity exceeds available stock.'])->withInput();
        }

        $consumable->increment('consumed', $data['quantity']);

        ConsumableTransaction::create([
            'consumable_id' => $consumable->id,
            'type' => 'consume',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'],
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('consumables.index')->with('success', 'Consumable consumed.');
    }

    public function restock(Request $request, Consumable $consumable): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $consumable->increment('quantity', $data['quantity']);

        ConsumableTransaction::create([
            'consumable_id' => $consumable->id,
            'type' => 'restock',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'] ?? null,
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('consumables.index')->with('success', 'Consumable restocked.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'sku', 'location', 'quantity', 'consumed', 'min_quantity', 'notes']);

            foreach ($rows as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->sku,
                    $item->location?->name,
                    $item->quantity,
                    $item->consumed,
                    $item->min_quantity,
                    $item->notes,
                ]);
            }

            fclose($handle);
        }, 'consumables-export.csv', ['Content-Type' => 'text/csv']);
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
                'sku' => $this->nullableValue($this->csvValue($row, $map, 'sku')),
                'location_id' => $locationId,
                'quantity' => max(0, (int) $this->csvValue($row, $map, 'quantity')),
                'consumed' => max(0, (int) $this->csvValue($row, $map, 'consumed')),
                'min_quantity' => max(0, (int) $this->csvValue($row, $map, 'min_quantity')),
                'notes' => $this->nullableValue($this->csvValue($row, $map, 'notes')),
            ];

            if ($attributes['consumed'] > $attributes['quantity']) {
                $attributes['consumed'] = $attributes['quantity'];
            }

            $sku = $attributes['sku'];
            if ($sku !== null) {
                Consumable::query()->updateOrCreate(['sku' => $sku], $attributes);
            } else {
                Consumable::query()->create($attributes);
            }

            $imported++;
        }

        fclose($handle);

        return redirect()->route('consumables.index')->with('success', "Imported {$imported} consumable rows.");
    }

    private function filteredQuery(Request $request)
    {
        $q = trim($request->string('q')->toString());
        $locationId = $request->integer('location_id');
        $stock = trim($request->string('stock')->toString());

        return Consumable::query()
            ->with('location')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('notes', 'like', "%{$q}%");
                });
            })
            ->when($locationId > 0, fn ($query) => $query->where('location_id', $locationId))
            ->when($stock === 'low', fn ($query) => $query->whereRaw('quantity - consumed <= min_quantity'))
            ->when($stock === 'healthy', fn ($query) => $query->whereRaw('quantity - consumed > min_quantity'));
    }

    private function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('consumables', 'sku')->ignore($id)],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'consumed' => ['required', 'integer', 'min:0'],
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
