@php
    $isActive = fn(string $route) => request()->routeIs($route) ? 'bg-amber-500 text-white rounded-2xl shadow-lg shadow-amber-500/30 scale-[1.02]' : 'text-slate-400 hover:bg-slate-800/85 hover:text-white';
    $isActiveFill = fn(string $route) => request()->routeIs($route) ? 'font-variation-settings: \'FILL\' 1;' : '';
@endphp

<aside class="hidden md:flex fixed inset-y-0 left-0 z-30 w-64 flex-col bg-slate-900 text-white shadow-2xl">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center p-1.5">
            <x-school-logo class="h-8 w-8" />
        </div>
        <div>
            <p class="font-headline text-sm font-bold text-white leading-tight">Admin Panel</p>
            <p class="text-[10px] font-bold text-slate-400 tracking-wide">TK Wonoayu Madiun</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 flex flex-col gap-2 p-4 overflow-y-auto">
        <p class="px-4 text-[10px] font-black text-amber-400 uppercase tracking-[0.2em] mb-1">Utama</p>
        <a class="flex items-center gap-3 px-4 py-3 {{ $isActive('admin.dashboard') }} transition-all duration-300" href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ $isActiveFill('admin.dashboard') }}">dashboard</span>
            <span class="font-bold text-xs">Dashboard</span>
        </a>

        <div class="my-3 border-t border-white/10"></div>
        <p class="px-4 text-[10px] font-black text-amber-400 uppercase tracking-[0.2em] mb-1">Manajemen</p>
        <a class="flex items-center gap-3 px-4 py-3 {{ $isActive('admin.pengguna.*') }} transition-all duration-300" href="{{ route('admin.pengguna.index') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ $isActiveFill('admin.pengguna.*') }}">manage_accounts</span>
            <span class="font-bold text-xs">Pengguna</span>
        </a>

        <div class="my-3 border-t border-white/10"></div>
        <p class="px-4 text-[10px] font-black text-sky-300 uppercase tracking-[0.2em] mb-1">Portal Guru</p>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800/85 hover:text-white rounded-2xl transition-all duration-300" href="{{ route('guru.dashboard') }}">
            <span class="material-symbols-outlined text-[20px]">school</span>
            <span class="font-bold text-xs">Masuk sebagai Guru</span>
        </a>
    </nav>

    {{-- Logout --}}
    <div class="p-4 border-t border-white/10">
        <form action="{{ route('auth.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-rose-400 transition-colors text-xs font-bold rounded-2xl hover:bg-white/5">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Keluar
            </button>
        </form>
    </div>
</aside>
