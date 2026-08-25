<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(): View { return view('divisions.index', ['divisions' => Division::withCount('users')->orderBy('name')->get()]); }
    public function create(): View { return view('divisions.create'); }
    public function store(Request $request): RedirectResponse { Division::create($this->validated($request)); return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.'); }
    public function edit(Division $division): View { return view('divisions.edit', compact('division')); }
    public function update(Request $request, Division $division): RedirectResponse { $division->update($this->validated($request, $division)); return redirect()->route('divisions.index')->with('success', 'Divisi berhasil diperbarui.'); }
    public function destroy(Division $division): RedirectResponse { $division->delete(); return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.'); }

    private function validated(Request $request, ?Division $division = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:100', 'unique:divisions,name,'.$division?->id], 'description' => ['nullable', 'string', 'max:500']]);
    }
}
