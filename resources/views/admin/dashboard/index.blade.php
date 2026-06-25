@extends('layouts.admin')

@php
    $title = 'Dashboard Admin - TK Wonoayu';
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
            <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Dashboard Admin</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Ringkasan Sistem & Statistik</p>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">

    {{-- WELCOME CARD --}}
    <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-100 ambient-shadow mb-8 p-8 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-slate-900 font-headline tracking-tight mb-2">Selamat Datang, {{ $adminName ?? 'Admin' }}!</h2>
            <p class="text-slate-500 font-medium">Berikut ringkasan status sistem TK Wonoayu hari ini.</p>
        </div>
        <div class="bg-amber-50 rounded-3xl p-8 flex items-center gap-6 border border-amber-100">
            <div class="w-16 h-16 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-xl shadow-amber-500/30">
                <span class="material-symbols-outlined text-3xl">groups</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Total Siswa</p>
                <p class="text-4xl font-extrabold text-slate-900 leading-none">{{ $totalStudents ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Guru</span>
                <span class="material-symbols-outlined text-[20px] rounded-xl p-2 bg-blue-50 text-blue-600">person_4</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">{{ $totalGuru ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">Akun aktif</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Wali Murid</span>
                <span class="material-symbols-outlined text-[20px] rounded-xl p-2 bg-violet-50 text-violet-600">family_restroom</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">{{ $totalWali ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">Akun terdaftar</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Telegram</span>
                <span class="material-symbols-outlined text-[20px] rounded-xl p-2 bg-emerald-50 text-emerald-600">chat</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">{{ $telegramChats ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">Chat terhubung</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Admin</span>
                <span class="material-symbols-outlined text-[20px] rounded-xl p-2 bg-amber-50 text-amber-600">admin_panel_settings</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900">{{ $totalAdmin ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">Akun aktif</p>
        </div>
    </div>

    {{-- ATTENDANCE TODAY --}}
    @php
        $attendanceMeta = [
            'hadir' => ['label' => 'Hadir', 'icon' => 'check_circle', 'class' => 'bg-emerald-50 text-emerald-600'],
            'izin' => ['label' => 'Izin', 'icon' => 'assignment_late', 'class' => 'bg-blue-50 text-blue-600'],
            'sakit' => ['label' => 'Sakit', 'icon' => 'sick', 'class' => 'bg-amber-50 text-amber-600'],
            'alpa' => ['label' => 'Alpa', 'icon' => 'cancel', 'class' => 'bg-rose-50 text-rose-600'],
        ];
    @endphp
    <section class="bg-white rounded-3xl border border-slate-100 ambient-shadow mb-8 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="font-headline text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">event_available</span>
                    Absensi Hari Ini
                </h3>
                <p class="text-xs font-bold text-slate-400 mt-1">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="rounded-2xl bg-amber-50 border border-amber-100 px-5 py-3">
                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Kehadiran</p>
                    <p class="text-2xl font-black text-slate-900 leading-tight">{{ $attendancePercent ?? 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-0 divide-x divide-y lg:divide-y-0 divide-slate-100">
            <div class="p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tercatat</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $recordedAttendanceCount ?? 0 }}/{{ $totalStudents ?? 0 }}</p>
                <p class="mt-1 text-xs font-bold text-slate-400">Siswa hari ini</p>
            </div>
            @foreach($attendanceMeta as $key => $meta)
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $meta['label'] }}</p>
                        <span class="material-symbols-outlined text-[20px] rounded-xl p-2 {{ $meta['class'] }}">{{ $meta['icon'] }}</span>
                    </div>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $attendanceCounts[$key] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-400">Siswa</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- RECENT ACTIVITY --}}
    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-[18px]">history</span> Aktivitas Terbaru
            </h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentLogs as $log)
            <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-[16px]">
                        @if($log->action === 'login') login
                        @elseif($log->action === 'logout') logout
                        @elseif($log->action === 'create') add_circle
                        @elseif($log->action === 'update') edit
                        @elseif($log->action === 'delete') delete
                        @elseif($log->action === 'reset_password') key
                        @elseif($log->action === 'toggle_active') toggle_on
                        @else info @endif
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-700 truncate">{{ $log->description ?? $log->action }}</p>
                    <p class="text-[11px] text-slate-400">{{ $log->user_name ?? 'System' }} &middot; {{ $log->module }}</p>
                </div>
                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">{{ $log->created_at?->diffForHumans() ?? '-' }}</span>
            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-center opacity-60">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">history</span>
                <p class="text-xs font-bold text-slate-400">Belum ada aktivitas tercatat</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
