<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['categories' => Category::withCount('assets')->orderBy('name')->get()]);
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
        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['category' => 'Kategori yang masih digunakan oleh aset tidak dapat dihapus.']);
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
