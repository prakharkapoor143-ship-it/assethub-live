<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            'assetCount' => Asset::count(),
            'categoryCount' => Category::count(),
            'locationCount' => Location::count(),
            'recentAssets' => Asset::query()->with(['category', 'location'])->latest()->limit(8)->get(),
        ]);
    }
}
