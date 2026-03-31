<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\LicenseRecord;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $assetsByCategory = DB::table('assets')
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->when($q !== '', fn ($query) => $query->where('categories.name', 'like', "%{$q}%"))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->paginate(15)
            ->withQueryString();

        return view('reports.index', [
            'kpis' => [
                'Assets' => Asset::count(),
                'Accessories' => Accessory::count(),
                'Consumables' => Consumable::count(),
                'Components' => Component::count(),
                'Licenses' => LicenseRecord::count(),
                'Categories' => Category::count(),
                'Suppliers' => Supplier::count(),
                'Companies' => Company::count(),
                'Locations' => Location::count(),
                'Users' => User::count(),
            ],
            'assetsByCategory' => $assetsByCategory,
            'filters' => ['q' => $q],
        ]);
    }
}
