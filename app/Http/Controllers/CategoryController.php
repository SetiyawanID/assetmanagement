<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('assets')->orderBy('name');
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%')))
            ->when($request->usage === 'used', fn ($q) => $q->has('assets'))
            ->when($request->usage === 'empty', fn ($q) => $q->doesntHave('assets'));
        return view('categories.index', ['categories' => $query->simplePaginate(10)->withQueryString()]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon' => ['required', 'string', 'max:50', 'regex:/^bi-[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->user()->isSuperAdmin()) {
            Category::create($data);
            return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
        }

        ApprovalRequest::create(['type' => 'category', 'payload' => $data, 'requested_by' => $request->user()->id]);
        return redirect()->route('categories.index')->with('success', 'Kategori diajukan dan menunggu approval Super Admin.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
            'icon' => ['required', 'string', 'max:50', 'regex:/^bi-[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        if (! $request->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('category', $category->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk kategori ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'category', 'action' => 'update', 'target_id' => $category->id, 'payload' => $data, 'requested_by' => $request->user()->id]);
            return redirect()->route('categories.index')->with('success', 'Perubahan kategori diajukan dan menunggu approval Super Admin.');
        }
        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['category' => 'Kategori yang masih digunakan oleh aset tidak dapat dihapus.']);
        }
        if (! request()->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('category', $category->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk kategori ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'category', 'action' => 'delete', 'target_id' => $category->id, 'payload' => ['name' => $category->name], 'requested_by' => request()->user()->id]);
            return redirect()->route('categories.index')->with('success', 'Penghapusan kategori diajukan dan menunggu approval Super Admin.');
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
