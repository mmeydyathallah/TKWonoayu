@extends('layouts.admin')

@php
    $title = 'Manajemen Pengguna - Admin Panel';
@endphp

@section('styles')
<style>
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
</style>
@endsection

@section('content')

<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Manajemen Pengguna</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kelola Akun Guru, Wali Murid & Admin</p>
        </div>
    </div>
    <a href="{{ route('admin.pengguna.create') }}" class="bg-amber-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 hover:scale-105 transition-transform flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add_circle</span> Tambah
    </a>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-500 text-[20px]">check_circle</span>
            <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-rose-500 text-[20px]">error</span>
            <p class="text-sm font-semibold text-rose-700">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                <span class="material-symbols-outlined text-[18px]">search</span>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
        </div>
        <select name="role" class="bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
            <option value="">Semua Role</option>
            <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
            <option value="wali_murid" {{ request('role') === 'wali_murid' ? 'selected' : '' }}>Wali Murid</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-slate-100 text-slate-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">Filter</button>
    </form>

    {{-- Users Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pengguna</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Login Terakhir</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm
                                    {{ $user->role === 'admin' ? 'bg-amber-500' : ($user->role === 'guru' ? 'bg-blue-500' : 'bg-violet-500') }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-600' : ($user->role === 'guru' ? 'bg-blue-50 text-blue-600' : 'bg-violet-50 text-violet-600') }}">
                                {{ $user->role === 'wali_murid' ? 'Wali Murid' : ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active ?? true)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.pengguna.edit', $user->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>
                                <form action="{{ route('admin.pengguna.toggle-active', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-lg {{ ($user->is_active ?? true) ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} transition-colors flex items-center justify-center" title="{{ ($user->is_active ?? true) ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <span class="material-symbols-outlined text-[16px]">{{ ($user->is_active ?? true) ? 'toggle_off' : 'toggle_on' }}</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-200 mb-2">person_off</span>
                            <p class="text-sm font-bold text-slate-400">Tidak ada pengguna ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Reset Password Modal --}}
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('resetPasswordModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8">
        <h3 class="text-xl font-black text-slate-800 mb-2">Reset Password</h3>
        <p class="text-sm text-slate-500 mb-6">Password baru untuk: <strong id="resetUserName"></strong></p>
        <form id="resetPasswordForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Password Baru</label>
                <input type="password" name="new_password" required minlength="8" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-amber-400 transition-all" placeholder="Minimal 8 karakter">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('resetPasswordModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-amber-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:scale-105 transition-all">Reset</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openResetPasswordModal(userId, userName) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetPasswordForm').action = `/admin/pengguna/${userId}/reset-password`;
    openModal('resetPasswordModal');
}
</script>
@endsection
