<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function index(): View
    {
        return view('statuses.index', ['statuses' => Status::withCount('assets')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('statuses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:statuses,name'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        validator($data, ['slug' => ['required', 'max:50', 'unique:statuses,slug']])->validate();

        if ($request->user()->isSuperAdmin()) {
            Status::create($data);
            return redirect()->route('statuses.index')->with('success', 'Status berhasil ditambahkan.');
        }

        ApprovalRequest::create(['type' => 'status', 'payload' => $data, 'requested_by' => $request->user()->id]);
        return redirect()->route('statuses.index')->with('success', 'Status diajukan dan menunggu approval Super Admin.');
    }

    public function edit(Status $status): View
    {
        return view('statuses.edit', compact('status'));
    }

    public function update(Request $request, Status $status): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:statuses,name,'.$status->id],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        validator($data, ['slug' => ['required', 'max:50', 'unique:statuses,slug,'.$status->id]])->validate();
        if ($status->assets()->exists() && $data['slug'] !== $status->slug) {
            return back()->withErrors(['name' => 'Slug status yang sedang digunakan oleh aset tidak dapat diubah.'])->withInput();
        }
        $status->update($data);
        return redirect()->route('statuses.index')->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy(Status $status): RedirectResponse
    {
        if ($status->assets()->exists()) {
            return back()->withErrors(['status' => 'Status yang masih digunakan oleh aset tidak dapat dihapus.']);
        }
        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'Status berhasil dihapus.');
    }
}
