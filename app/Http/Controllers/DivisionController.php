<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Division::withCount('users')->orderBy('name');
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%')))
            ->when($request->usage === 'used', fn ($q) => $q->has('users'))
            ->when($request->usage === 'empty', fn ($q) => $q->doesntHave('users'));
        return view('divisions.index', ['divisions' => $query->simplePaginate(10)->withQueryString()]);
    }
    public function create(): View { return view('divisions.create'); }
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if (! $request->user()->isSuperAdmin()) {
            ApprovalRequest::create([
                'type' => 'division',
                'action' => 'create',
                'payload' => $data,
                'requested_by' => $request->user()->id,
            ]);
            return redirect()->route('divisions.index')->with('success', 'Divisi diajukan dan menunggu approval Super Admin.');
        }

        Division::create($data);
        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }
    public function edit(Division $division): View { return view('divisions.edit', compact('division')); }
    public function update(Request $request, Division $division): RedirectResponse
    {
        $data = $this->validated($request, $division);
        if (! $request->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('division', $division->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk divisi ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'division', 'action' => 'update', 'target_id' => $division->id, 'payload' => $data, 'requested_by' => $request->user()->id]);
            return redirect()->route('divisions.index')->with('success', 'Perubahan divisi diajukan dan menunggu approval Super Admin.');
        }
        $division->update($data);
        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Request $request, Division $division): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('division', $division->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk divisi ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'division', 'action' => 'delete', 'target_id' => $division->id, 'payload' => ['name' => $division->name], 'requested_by' => $request->user()->id]);
            return redirect()->route('divisions.index')->with('success', 'Penghapusan divisi diajukan dan menunggu approval Super Admin.');
        }
        $division->delete();
        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }

    private function validated(Request $request, ?Division $division = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:100', 'unique:divisions,name,'.$division?->id], 'description' => ['nullable', 'string', 'max:500']]);
    }
}
