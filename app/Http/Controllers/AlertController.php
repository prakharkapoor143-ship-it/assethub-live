<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\LicenseRecord;
use Illuminate\View\View;

final class AlertController extends Controller
{
    public function index(): View
    {
        return view('alerts.index', [
            'lowAccessories' => Accessory::query()->whereRaw('quantity - checked_out <= min_quantity')->get(),
            'lowComponents' => Component::query()->whereRaw('quantity - allocated <= min_quantity')->get(),
            'lowConsumables' => Consumable::query()->whereRaw('quantity - consumed <= min_quantity')->get(),
            'expiringLicenses' => LicenseRecord::query()->whereNotNull('expires_at')->whereDate('expires_at', '<=', now()->addDays(30))->orderBy('expires_at')->get(),
        ]);
    }
}
