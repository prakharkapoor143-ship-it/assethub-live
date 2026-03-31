<?php

namespace App\Http\Controllers;

use App\Models\AccessoryTransaction;
use App\Models\ComponentTransaction;
use App\Models\ConsumableTransaction;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

final class TimelineController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $items = collect();

        $items = $items->concat(AccessoryTransaction::query()->with('accessory')->get()->map(fn ($x) => [
            'when' => $x->transacted_at,
            'module' => 'Accessory',
            'action' => ucfirst($x->type),
            'item' => $x->accessory?->name,
            'quantity' => $x->quantity,
            'counterparty' => $x->counterparty,
        ]));

        $items = $items->concat(ComponentTransaction::query()->with('component')->get()->map(fn ($x) => [
            'when' => $x->transacted_at,
            'module' => 'Component',
            'action' => ucfirst($x->type),
            'item' => $x->component?->name,
            'quantity' => $x->quantity,
            'counterparty' => $x->counterparty,
        ]));

        $items = $items->concat(ConsumableTransaction::query()->with('consumable')->get()->map(fn ($x) => [
            'when' => $x->transacted_at,
            'module' => 'Consumable',
            'action' => ucfirst($x->type),
            'item' => $x->consumable?->name,
            'quantity' => $x->quantity,
            'counterparty' => $x->counterparty,
        ]));

        if ($q !== '') {
            $items = $items->filter(function (array $item) use ($q): bool {
                $haystack = implode(' ', [
                    (string) ($item['module'] ?? ''),
                    (string) ($item['action'] ?? ''),
                    (string) ($item['item'] ?? ''),
                    (string) ($item['counterparty'] ?? ''),
                ]);

                return str_contains(strtolower($haystack), strtolower($q));
            });
        }

        $sorted = $items->sortByDesc('when')->values();
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $sorted->forPage($currentPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $pageItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => route('timeline.index'), 'query' => $request->query()]
        );

        return view('timeline.index', [
            'items' => $paginated,
            'filters' => ['q' => $q],
        ]);
    }
}
