<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View { return view('users.create', ['divisions' => Division::orderBy('name')->get(), 'canCreateAdmin' => request()->user()->isSuperAdmin()]); }

    public function index(Request $request): View
    {
        $query = User::with('division')->orderBy('name');
        if (! request()->user()->isSuperAdmin()) {
            $query->where('role', 'user');
        }
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%')))
            ->when($request->filled('role') && request()->user()->isSuperAdmin(), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('division_id'), fn ($q) => $q->where('division_id', $request->division_id));
        return view('users.index', ['users' => $query->simplePaginate(10)->withQueryString(), 'divisions' => Division::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,user'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);
        abort_unless($data['role'] === 'user' || $request->user()->isSuperAdmin(), 403, 'Admin hanya dapat membuat akun User.');

        if ($data['role'] === 'admin') {
            $password = $request->validate([
                'password' => ['required', 'confirmed', 'min:8'],
            ])['password'];
        } else {
            // User accounts cannot access the workspace, so their password is never shown or entered.
            $password = Str::random(64);
        }

        if ($request->user()->isSuperAdmin()) {
            $data['password'] = $password;
            User::create($data);
            return redirect()->route('users.index')->with('success', 'Akun '.($data['role'] === 'admin' ? 'Admin' : 'User').' berhasil dibuat.');
        }

        ApprovalRequest::create([
            'type' => 'user',
            'payload' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'user',
                'division_id' => $data['division_id'] ?? null,
                'password_hash' => Hash::make($password),
            ],
            'requested_by' => $request->user()->id,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengajuan akun User berhasil dibuat dan menunggu approval Super Admin.');
    }

    public function edit(User $user): View
    {
        $this->authorizeAccountManagement(request(), $user);
        return view('users.edit', ['user' => $user, 'divisions' => Division::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccountManagement($request, $user);
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id], 'division_id' => ['nullable', 'exists:divisions,id']]);
        if (! $request->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('user', $user->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk akun ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'user', 'action' => 'update', 'target_id' => $user->id, 'payload' => $data, 'requested_by' => $request->user()->id]);
            return redirect()->route('users.index')->with('success', 'Perubahan akun diajukan dan menunggu approval Super Admin.');
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Data akun '.$user->name.' berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccountManagement($request, $user);
        abort_if($user->id === $request->user()->id, 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        $name = $user->name;
        if (! $request->user()->isSuperAdmin()) {
            if ($pending = ApprovalRequest::pendingForTarget('user', $user->id)) {
                return back()->with('warning', 'Pengajuan perubahan untuk akun ini masih menunggu approval (diajukan oleh: '.$pending->requester->name.').');
            }
            ApprovalRequest::create(['type' => 'user', 'action' => 'delete', 'target_id' => $user->id, 'payload' => ['name' => $user->name, 'email' => $user->email], 'requested_by' => $request->user()->id]);
            return redirect()->route('users.index')->with('success', 'Penghapusan akun diajukan dan menunggu approval Super Admin.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun '.$name.' berhasil dihapus.');
    }

    public function changePassword(): View { return view('users.change-password'); }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return redirect()->route('dashboard')->with('success', 'Password Anda berhasil diubah.');
    }

    public function forceReset(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isAdmin() && $user->id !== $request->user()->id, 404);
        $data = $request->validate(['password' => ['required', 'confirmed', 'min:8']]);
        $user->update(['password' => $data['password']]);
        return redirect()->route('users.index')->with('success', 'Password '.$user->name.' berhasil di-reset.');
    }

    private function authorizeAccountManagement(Request $request, User $user): void
    {
        abort_unless($request->user()->isSuperAdmin() || $user->role === 'user', 403, 'Admin hanya dapat mengelola akun User.');
    }
}
