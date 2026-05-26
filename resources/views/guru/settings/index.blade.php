@extends('layouts.teacher')

@php $title = 'Pengaturan - TK Wonoayu'; @endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .tab-btn { transition: all .2s ease; }
    .tab-btn.active { background: white; color: #0060ad; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center gap-4 px-6 py-4 w-full -mx-8 mb-8 docked full-width">
    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
        <span class="material-symbols-outlined text-[20px]">settings</span>
    </div>
    <div>
        <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Pengaturan</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kelola Akun & Preferensi</p>
    </div>
</header>

<div class="max-w-5xl mx-auto w-full pb-20">

    {{-- TAB NAV --}}
    <div class="bg-slate-100 rounded-2xl p-1.5 flex gap-1 mb-8 w-fit">
        <button onclick="switchTab('profil')" id="tab-profil" class="tab-btn active px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">person</span> Profil
        </button>
        <button onclick="switchTab('password')" id="tab-password" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">lock</span> Keamanan
        </button>
        <button onclick="switchTab('info')" id="tab-info" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">info</span> Informasi Sistem
        </button>
    </div>

    {{-- ==================== TAB: PROFIL ==================== --}}
    <div id="panel-profil">
        @if(session('success_profile'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span> {{ session('success_profile') }}
        </div>
        @endif
        @if($errors->has('name') || $errors->has('email'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-sm font-bold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Avatar Card --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8 flex flex-col items-center gap-4 text-center">
                    <div class="w-24 h-24 rounded-full gradient-primary flex items-center justify-center text-white text-4xl font-extrabold shadow-xl shadow-blue-500/30">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">{{ $user->name }}</h3>
                        <span class="inline-block mt-1 bg-primary/10 text-primary text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">{{ $user->email }}</p>
                    <div class="w-full border-t border-slate-100 pt-4 space-y-2 text-left">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                            Bergabung: {{ $user->created_at->format('d M Y') }}
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="material-symbols-outlined text-[16px] text-emerald-500">verified_user</span>
                            Akun Aktif & Terverifikasi
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Profil --}}
            <div class="md:col-span-2">
                <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="font-extrabold text-slate-800 text-base">Informasi Akun</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Perbarui nama dan email yang terdaftar.</p>
                    </div>
                    <form action="{{ route('guru.settings.profile') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Nama Lengkap</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">person</span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all focus:bg-white">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Alamat Email</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">email</span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all focus:bg-white">
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="gradient-primary text-white px-8 py-3.5 rounded-2xl text-sm font-extrabold shadow-lg shadow-blue-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">save</span> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: PASSWORD ==================== --}}
    <div id="panel-password" class="hidden">
        @if(session('success_password'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span> {{ session('success_password') }}
        </div>
        @endif
        @if($errors->has('current_password') || $errors->has('password'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-sm font-bold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="max-w-2xl">
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="font-extrabold text-slate-800 text-base">Ubah Password</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pastikan password baru minimal 8 karakter.</p>
                </div>
                <form action="{{ route('guru.settings.password') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Password Saat Ini</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                            <input type="password" name="current_password" required placeholder="••••••••"
                                class="w-full bg-slate-50 border @error('current_password') border-rose-300 bg-rose-50 @else border-slate-200 @enderror rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all focus:bg-white">
                        </div>
                        @error('current_password')
                        <p class="text-xs text-rose-600 font-bold pl-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Password Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock_open</span>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all focus:bg-white">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">check_circle</span>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password baru"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all focus:bg-white">
                        </div>
                    </div>

                    {{-- Password strength indicator --}}
                    <div id="strength-bar-wrap" class="hidden space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kekuatan Password</span>
                            <span id="strength-label" class="text-[10px] font-black uppercase tracking-wider"></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div id="strength-bar" class="h-2 rounded-full transition-all duration-300" style="width:0%"></div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
                        <span class="material-symbols-outlined text-amber-500 text-[20px] flex-shrink-0 mt-0.5">warning</span>
                        <p class="text-xs text-amber-700 font-medium leading-relaxed">Setelah password diubah, Anda akan tetap masuk menggunakan password baru. Jangan bagikan password Anda kepada siapapun.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="gradient-primary text-white px-8 py-3.5 rounded-2xl text-sm font-extrabold shadow-lg shadow-blue-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">shield</span> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== TAB: INFO SISTEM ==================== --}}
    <div id="panel-info" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8">
                <h3 class="font-extrabold text-slate-800 text-base mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">school</span> Informasi Sekolah
                </h3>
                <div class="space-y-4">
                    @php
                    $infos = [
                        ['icon' => 'apartment',       'label' => 'Nama Sekolah',    'value' => 'TK Wonoayu Madiun'],
                        ['icon' => 'location_on',     'label' => 'Alamat',          'value' => 'Wonoayu, Madiun, Jawa Timur'],
                        ['icon' => 'call',            'label' => 'Telepon',         'value' => '-'],
                        ['icon' => 'calendar_month',  'label' => 'Tahun Ajaran',    'value' => '2025 / 2026'],
                    ];
                    @endphp
                    @foreach($infos as $info)
                    <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary text-[18px]">{{ $info['icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $info['label'] }}</p>
                            <p class="text-sm font-bold text-slate-700 mt-0.5">{{ $info['value'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8">
                <h3 class="font-extrabold text-slate-800 text-base mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">developer_mode</span> Informasi Sistem
                </h3>
                <div class="space-y-4">
                    @php
                    $sys = [
                        ['icon' => 'code',           'label' => 'Framework',   'value' => 'Laravel ' . app()->version()],
                        ['icon' => 'terminal',       'label' => 'PHP',         'value' => phpversion()],
                        ['icon' => 'storage',        'label' => 'Database',    'value' => 'MySQL'],
                        ['icon' => 'update',         'label' => 'Server Time', 'value' => now()->format('d M Y, H:i') . ' WIB'],
                    ];
                    @endphp
                    @foreach($sys as $s)
                    <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-indigo-500 text-[18px]">{{ $s['icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $s['label'] }}</p>
                            <p class="text-sm font-bold text-slate-700 mt-0.5">{{ $s['value'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="md:col-span-2 bg-rose-50 border border-rose-200 rounded-3xl p-8">
                <h3 class="font-extrabold text-rose-700 text-base mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">warning</span> Zona Berbahaya
                </h3>
                <p class="text-xs text-rose-600 font-medium mb-6">Tindakan berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-white border border-rose-300 text-rose-700 px-6 py-3 rounded-xl text-sm font-bold hover:bg-rose-700 hover:text-white transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">logout</span> Keluar dari Akun
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function switchTab(tab) {
    ['profil','password','info'].forEach(t => {
        document.getElementById('panel-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).classList.remove('active');
    });
    document.getElementById('panel-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active');
}

// Open correct tab on error
@if($errors->has('current_password') || $errors->has('password') || session('success_password'))
    document.addEventListener('DOMContentLoaded', () => switchTab('password'));
@endif

// Password strength meter
const pwdInput = document.querySelector('input[name="password"]');
if (pwdInput) {
    pwdInput.addEventListener('input', function() {
        const val = this.value;
        const wrap = document.getElementById('strength-bar-wrap');
        const bar  = document.getElementById('strength-bar');
        const lbl  = document.getElementById('strength-label');
        if (!val) { wrap.classList.add('hidden'); return; }
        wrap.classList.remove('hidden');

        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { pct: '25%',  cls: 'bg-rose-500',   text: 'Lemah',   textCls: 'text-rose-500' },
            { pct: '50%',  cls: 'bg-amber-500',   text: 'Cukup',  textCls: 'text-amber-500' },
            { pct: '75%',  cls: 'bg-blue-500',    text: 'Baik',   textCls: 'text-blue-500' },
            { pct: '100%', cls: 'bg-emerald-500', text: 'Kuat',   textCls: 'text-emerald-500' },
        ];
        const lvl = levels[Math.max(0, score - 1)];
        bar.style.width = lvl.pct;
        bar.className = 'h-2 rounded-full transition-all duration-300 ' + lvl.cls;
        lbl.textContent = lvl.text;
        lbl.className = 'text-[10px] font-black uppercase tracking-wider ' + lvl.textCls;
    });
}
</script>
@endsection
