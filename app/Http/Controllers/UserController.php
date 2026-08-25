<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View { return view('users.create', ['divisions' => Division::orderBy('name')->get(), 'canCreateAdmin' => request()->user()->isSuperAdmin()]); }

    public function index(): View
    {
        $query = User::with('division')->orderBy('name');
        if (! request()->user()->isSuperAdmin()) {
            $query->where('role', 'user');
        }
        return view('users.index', ['users' => $query->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,user'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'password' => ['nullable', 'required_if:role,admin', 'confirmed', 'min:8'],
        ]);
        abort_unless($data['role'] === 'user' || $request->user()->isSuperAdmin(), 403, 'Admin hanya dapat membuat akun User.');
        $data['password'] ??= Str::random(64);
        User::create($data);
        return redirect()->route('dashboard')->with('success', 'Akun '.($data['role'] === 'admin' ? 'Admin' : 'User').' berhasil dibuat.');
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
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Data akun '.$user->name.' berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccountManagement($request, $user);
        abort_if($user->id === $request->user()->id, 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        $name = $user->name; $user->delete();
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
