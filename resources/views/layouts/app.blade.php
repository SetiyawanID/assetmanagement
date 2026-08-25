<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} | AssetHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ request()->routeIs('users.edit') ? 'account-edit-page' : '' }}">
<div class="app-shell d-lg-flex">
    <aside class="sidebar flex-shrink-0 p-3">
        <a href="{{ route('dashboard') }}" class="brand d-flex align-items-center gap-2 text-white text-decoration-none fs-4 fw-bold mb-4"><i class="bi bi-boxes"></i> AssetHub</a>
        <div class="text-uppercase small text-white-50 fw-semibold mb-2">Workspace</div>
        <nav class="nav flex-column gap-1 mb-4">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill me-2"></i> Ringkasan</a>
            <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}"><i class="bi bi-laptop me-2"></i> Semua Aset</a>
            @if(auth()->user()->isAdministrator())
            <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}"><i class="bi bi-person-plus me-2"></i> Buat akun baru</a>
            <a class="nav-link {{ request()->routeIs('users.index', 'users.edit', 'users.update', 'users.destroy', 'users.password.update') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i> Kelola akun</a>
            <a class="nav-link {{ request()->routeIs('divisions.*') ? 'active' : '' }}" href="{{ route('divisions.index') }}"><i class="bi bi-diagram-3 me-2"></i> List Divisi</a>
            @endif
        </nav>
        <div class="mt-auto pt-4 border-top border-secondary-subtle small text-white-50">IT Operations<br><span class="text-white">Inventory & lifecycle</span></div>
    </aside>
    <main class="main-content flex-grow-1">
        <header class="topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
            <div class="text-secondary small d-none d-sm-block">{{ now()->translatedFormat('l, d F Y') }} <span class="badge text-bg-light ms-2">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</span></div>
            <div class="dropdown ms-auto"><button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown"><span class="rounded-circle bg-success-subtle text-success px-2 py-1 fw-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="d-none d-sm-inline">{{ auth()->user()->name }}</span><i class="bi bi-chevron-down small"></i></button><ul class="dropdown-menu dropdown-menu-end shadow-sm"><li><span class="dropdown-item-text small text-secondary">{{ auth()->user()->email }}</span></li><li><a class="dropdown-item" href="{{ route('password.edit') }}"><i class="bi bi-key me-2"></i>Ganti password</a></li><li><hr class="dropdown-divider"></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></form></li></ul></div>
        </header>
        <div class="p-3 p-lg-4">
            @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any())<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-circle me-2"></i>Periksa kembali isian Anda.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
