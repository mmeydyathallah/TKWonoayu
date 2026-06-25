@extends('layouts.admin')

@php
    $title = 'Edit Pengguna - Admin Panel';
@endphp

@section('styles')
<style>
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
</style>
@endsection

@section('content')

<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center px-6 py-4 w-full -mx-8 mb-8">
    <a href="{{ route('admin.pengguna.index') }}" class="mr-4 w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
    </a>
    <div>
        <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Edit Pengguna</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $user->name }}</p>
    </div>
</header>

<div class="max-w-2xl mx-auto w-full pb-20">

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-rose-500 text-[20px]">error</span>
            <div class="text-sm font-semibold text-rose-700">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Edit User Form --}}
    <form method="POST" action="{{ route('admin.pengguna.update', $user->id) }}" class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8 space-y-6 mb-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Role</label>
            <select name="role" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                <option value="guru" {{ old('role', $user->role) === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="wali_murid" {{ old('role', $user->role) === 'wali_murid' ? 'selected' : '' }}>Wali Murid</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('admin.pengguna.index') }}" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all text-center">Batal</a>
            <button type="submit" class="flex-1 bg-amber-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:scale-105 transition-all">Perbarui</button>
        </div>
    </form>

    {{-- Reset Password --}}
    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8">
        <h3 class="font-headline text-lg font-extrabold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-[22px]">key</span> Reset Password
        </h3>
        <form method="POST" action="{{ route('admin.pengguna.reset-password', $user->id) }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            @method('PUT')
            <input type="password" name="new_password" required minlength="8" class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all" placeholder="Password baru (minimal 8 karakter)">
            <button type="submit" class="bg-amber-500 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all whitespace-nowrap" onclick="return confirm('Reset password {{ $user->name }}?')">
                Reset Password
            </button>
        </form>
    </div>

</div>
@endsection
