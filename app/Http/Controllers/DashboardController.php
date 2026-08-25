<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalAssets' => Asset::count(), 'availableAssets' => Asset::where('status', 'available')->count(),
            'assignedAssets' => Asset::where('status', 'assigned')->count(), 'maintenanceAssets' => Asset::where('status', 'maintenance')->count(),
            'categories' => Category::withCount('assets')->orderByDesc('assets_count')->get(),
            'recentAssets' => Asset::with(['category', 'assignee'])->latest()->take(6)->get(),
            'locationsCount' => Location::count(),
        ]);
    }
}
