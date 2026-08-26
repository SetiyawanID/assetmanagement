@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="eyebrow">Administration</div>
        <h1 class="page-title h2 mb-1">Kelola akun</h1>
        <p class="text-secondary mb-0">Kelola seluruh akun dan hak akses dalam satu direktori.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Buat akun baru</a>
</div>

<div class="panel mb-3"><form class="p-3 row g-2" method="GET"><div class="col-lg-5"><div class="input-group"><span class="input-group-text bg-white"><i class="bi bi-search"></i></span><input class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."></div></div>@if(auth()->user()->isSuperAdmin())<div class="col-sm-6 col-lg-3"><select class="form-select" name="role"><option value="">Semua role</option><option value="super_admin" @selected(request('role') === 'super_admin')>Super Admin</option><option value="admin" @selected(request('role') === 'admin')>Admin</option><option value="user" @selected(request('role') === 'user')>User</option></select></div>@endif<div class="col-sm-6 col-lg-3"><select class="form-select" name="division_id"><option value="">Semua divisi</option>@foreach($divisions as $division)<option value="{{ $division->id }}" @selected(request('division_id') == $division->id)>{{ $division->name }}</option>@endforeach</select></div><div class="col-lg-1 d-grid"><button class="btn btn-dark" data-bs-toggle="tooltip" data-bs-title="Filter" aria-label="Filter"><i class="bi bi-funnel"></i></button></div></form></div>

<div class="panel">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Anggota</th><th>Role</th><th>Divisi</th><th class="text-end">CRUD</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="asset-icon"><i class="bi bi-person"></i></span><div class="fw-semibold">{{ $user->name }}<div class="small text-secondary">{{ $user->email }}</div></div></div></td>
                    <td><span class="badge rounded-pill {{ $user->isSuperAdmin() ? 'text-bg-dark' : ($user->isAdmin() ? 'text-bg-primary' : 'badge-soft') }}">{{ $user->isSuperAdmin() ? 'Super Admin' : ($user->isAdmin() ? 'Admin' : 'User') }}</span></td>
                    <td>{{ $user->division?->name ?? 'Belum diatur' }}</td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown" title="Aksi akun"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="{{ route('users.edit', $user) }}"><i class="bi bi-pencil me-2"></i>Edit Akun</a></li>
                                @if($user->id !== auth()->id())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus akun ini?')">@csrf @method('DELETE')<button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus akun</button></form></li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-5">Belum ada akun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="p-3 border-top">{{ $users->links() }}</div>@endif
</div>
@endsection
