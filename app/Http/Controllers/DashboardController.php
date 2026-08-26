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
        $assetCounts = Asset::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('dashboard', [
            'totalAssets' => $assetCounts->sum(), 'availableAssets' => $assetCounts->get('available', 0),
            'assignedAssets' => $assetCounts->get('assigned', 0), 'maintenanceAssets' => $assetCounts->get('maintenance', 0),
            'categories' => Category::withCount('assets')->orderByDesc('assets_count')->get(),
            'recentAssets' => Asset::with(['category', 'assignee', 'statusDefinition'])->latest()->take(6)->get(),
            'locationsCount' => Location::count(),
        ]);
    }
}
