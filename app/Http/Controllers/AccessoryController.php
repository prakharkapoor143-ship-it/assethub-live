<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AccessoryTransaction;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AccessoryController extends Controller
{
    public function index(Request $request): View
    {
        $accessories = $this->filteredQuery($request)->latest()->paginate(15)->withQueryString();

        return view('accessories.index', [
            'accessories' => $accessories,
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

        return view('accessories.create', [
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateAccessory($request, null);
        Accessory::create($data);

        return redirect()->route('accessories.index')->with('success', 'Accessory created.');
    }

    public function edit(Accessory $accessory): View
    {
        Gate::authorize('manage-inventory');

        return view('accessories.edit', [
            'accessory' => $accessory,
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Accessory $accessory): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $this->validateAccessory($request, $accessory->id);

        if ($data['checked_out'] > $data['quantity']) {
            return back()->withErrors(['checked_out' => 'Checked out cannot exceed total quantity.'])->withInput();
        }

        $accessory->update($data);

        return redirect()->route('accessories.index')->with('success', 'Accessory updated.');
    }

    public function destroy(Accessory $accessory): RedirectResponse
    {
        Gate::authorize('admin-only');

        $accessory->delete();

        return redirect()->route('accessories.index')->with('success', 'Accessory deleted.');
    }

    public function checkoutForm(Accessory $accessory): View
    {
        Gate::authorize('manage-inventory');

        return view('accessories.checkout', ['accessory' => $accessory]);
    }

    public function checkout(Request $request, Accessory $accessory): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['quantity'] > $accessory->available_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity exceeds available stock.'])->withInput();
        }

        $accessory->increment('checked_out', $data['quantity']);

        AccessoryTransaction::create([
            'accessory_id' => $accessory->id,
            'type' => 'checkout',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'],
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('accessories.index')->with('success', 'Accessory checked out.');
    }

    public function checkinForm(Accessory $accessory): View
    {
        Gate::authorize('manage-inventory');

        return view('accessories.checkin', ['accessory' => $accessory]);
    }

    public function checkin(Request $request, Accessory $accessory): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['quantity'] > $accessory->checked_out) {
            return back()->withErrors(['quantity' => 'Check-in quantity cannot exceed checked-out amount.'])->withInput();
        }

        $accessory->decrement('checked_out', $data['quantity']);

        AccessoryTransaction::create([
            'accessory_id' => $accessory->id,
            'type' => 'checkin',
            'quantity' => $data['quantity'],
            'counterparty' => $data['counterparty'] ?? null,
            'notes' => $data['notes'] ?? null,
            'transacted_at' => now(),
        ]);

        return redirect()->route('accessories.index')->with('success', 'Accessory checked in.');
    }

    public function history(Accessory $accessory): View
    {
        return view('accessories.history', [
            'accessory' => $accessory,
            'transactions' => $accessory->transactions()->paginate(15)->withQueryString(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->filteredQuery($request)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'model_number', 'sku', 'location', 'quantity', 'checked_out', 'min_quantity', 'notes']);

            foreach ($rows as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->model_number,
                    $item->sku,
                    $item->location?->name,
                    $item->quantity,
                    $item->checked_out,
                    $item->min_quantity,
                    $item->notes,
                ]);
            }

            fclose($handle);
        }, 'accessories-export.csv', ['Content-Type' => 'text/csv']);
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
                'checked_out' => max(0, (int) $this->csvValue($row, $map, 'checked_out')),
                'min_quantity' => max(0, (int) $this->csvValue($row, $map, 'min_quantity')),
                'notes' => $this->nullableValue($this->csvValue($row, $map, 'notes')),
            ];

            if ($attributes['checked_out'] > $attributes['quantity']) {
                $attributes['checked_out'] = $attributes['quantity'];
            }

            $sku = $attributes['sku'];
            if ($sku !== null) {
                Accessory::query()->updateOrCreate(['sku' => $sku], $attributes);
            } else {
                Accessory::query()->create($attributes);
            }

            $imported++;
        }

        fclose($handle);

        return redirect()->route('accessories.index')->with('success', "Imported {$imported} accessory rows.");
    }

    private function filteredQuery(Request $request)
    {
        $q = trim($request->string('q')->toString());
        $locationId = $request->integer('location_id');
        $stock = trim($request->string('stock')->toString());

        return Accessory::query()
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
            ->when($stock === 'low', fn ($query) => $query->whereRaw('quantity - checked_out <= min_quantity'))
            ->when($stock === 'healthy', fn ($query) => $query->whereRaw('quantity - checked_out > min_quantity'));
    }

    private function validateAccessory(Request $request, ?int $accessoryId): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model_number' => ['nullable', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('accessories', 'sku')->ignore($accessoryId),
            ],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'checked_out' => ['required', 'integer', 'min:0'],
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
