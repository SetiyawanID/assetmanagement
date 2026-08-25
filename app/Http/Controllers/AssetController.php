<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    private function formData(): array { return ['categories' => Category::orderBy('name')->get(), 'locations' => Location::orderBy('name')->get(), 'users' => User::orderBy('name')->get()]; }

    public function index(Request $request): View
    {
        $assets = Asset::with(['category', 'location', 'assignee'])->when($request->filled('search'), fn ($q) => $q->where(fn ($x) => $x->where('asset_tag', 'like', '%'.$request->search.'%')->orWhere('name', 'like', '%'.$request->search.'%')->orWhere('serial_number', 'like', '%'.$request->search.'%')))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))->latest()->paginate(10)->withQueryString();
        return view('assets.index', array_merge(['assets' => $assets, 'categories' => Category::orderBy('name')->get()], $this->formData()));
    }

    public function create(): View { return view('assets.create', $this->formData()); }
    public function store(Request $request): RedirectResponse { Asset::create($this->validated($request)); return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.'); }
    public function show(Asset $asset): View { return view('assets.show', ['asset' => $asset->load(['category', 'location', 'assignee'])]); }
    public function edit(Asset $asset): View { return view('assets.edit', array_merge(['asset' => $asset], $this->formData())); }
    public function update(Request $request, Asset $asset): RedirectResponse { $asset->update($this->validated($request, $asset)); return redirect()->route('assets.show', $asset)->with('success', 'Data aset diperbarui.'); }
    public function destroy(Asset $asset): RedirectResponse { $asset->delete(); return redirect()->route('assets.index')->with('success', 'Aset dihapus.'); }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        $id = $asset?->id;
        return $request->validate([
            'asset_tag' => ['required', 'string', 'max:50', 'unique:assets,asset_tag,'.$id], 'name' => ['required', 'string', 'max:150'], 'category_id' => ['required', 'exists:categories,id'], 'location_id' => ['nullable', 'exists:locations,id'], 'assigned_to' => ['nullable', 'exists:users,id'], 'brand' => ['nullable', 'max:100'], 'model' => ['nullable', 'max:100'], 'serial_number' => ['nullable', 'max:100', 'unique:assets,serial_number,'.$id], 'purchase_date' => ['nullable', 'date'], 'purchase_price' => ['nullable', 'numeric', 'min:0'], 'status' => ['required', 'in:available,assigned,maintenance,retired'], 'condition' => ['required', 'in:excellent,good,fair,poor'], 'warranty_until' => ['nullable', 'date'], 'notes' => ['nullable', 'string'],
        ]);
    }
}
