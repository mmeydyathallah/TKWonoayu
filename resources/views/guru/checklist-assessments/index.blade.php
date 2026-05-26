@extends('layouts.teacher')

@php
    $title = 'Penilaian Ceklis - TK Wonoayu';
    $domains = [
        'NAM'  => ['label' => 'Nilai Agama & Moral',  'icon' => 'mosque',              'color' => 'purple'],
        'FM'   => ['label' => 'Fisik Motorik',         'icon' => 'sports_gymnastics',   'color' => 'emerald'],
        'KOG'  => ['label' => 'Kognitif',              'icon' => 'psychology',          'color' => 'amber'],
        'BHS'  => ['label' => 'Bahasa',                'icon' => 'chat_bubble',         'color' => 'blue'],
        'SOSEM' => ['label' => 'Sosial Emosional',      'icon' => 'favorite',            'color' => 'rose'],
        'SENI' => ['label' => 'Seni',                  'icon' => 'palette',             'color' => 'orange'],
    ];
@endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    
    .score-radio { display: none !important; }
    .score-pill {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 44px; height: 32px; padding: 0 10px;
        border-radius: 8px; font-size: 10px; font-weight: 900;
        cursor: pointer; transition: all .18s ease;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8; background: #f8fafc;
        user-select: none; letter-spacing: .03em;
    }
    .score-radio:checked + .score-pill.pill-BB  { background:#fee2e2; color:#dc2626; border-color:#fca5a5; transform:scale(1.07); box-shadow:0 2px 8px rgba(239,68,68,.15); }
    .score-radio:checked + .score-pill.pill-MB  { background:#fef3c7; color:#d97706; border-color:#fcd34d; transform:scale(1.07); box-shadow:0 2px 8px rgba(245,158,11,.15); }
    .score-radio:checked + .score-pill.pill-BSH { background:#dcfce7; color:#16a34a; border-color:#86efac; transform:scale(1.07); box-shadow:0 2px 8px rgba(34,197,94,.15);  }
    .score-radio:checked + .score-pill.pill-BSB { background:#dbeafe; color:#2563eb; border-color:#93c5fd; transform:scale(1.07); box-shadow:0 2px 8px rgba(59,130,246,.15); }
    .score-pill:hover { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }

    .icon-purple  { color:#7c3aed; background:#f3e8ff; }
    .icon-emerald { color:#059669; background:#d1fae5; }
    .icon-amber   { color:#d97706; background:#fef3c7; }
    .icon-blue    { color:#2563eb; background:#dbeafe; }
    .icon-rose    { color:#e11d48; background:#ffe4e6; }
    .icon-orange  { color:#ea580c; background:#ffedd5; }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-8 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">fact_check</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Penilaian Ceklis</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Program Pengembangan & Capaian</p>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto w-full pb-32">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- FILTER BAR --}}
    <form action="{{ route('guru.checklist') }}" method="GET" id="filter-form" class="bg-white rounded-2xl border border-slate-100 ambient-shadow p-4 mb-8 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px] space-y-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Tanggal</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[18px]">calendar_month</span>
                <input name="date" type="date" value="{{ $date->format('Y-m-d') }}" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>
        <div class="flex-1 min-w-[200px] space-y-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Kelompok</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">groups</span>
                <select name="group" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                    <option value="">Semua Kelompok</option>
                    @foreach(['A1','A2','B1','B2'] as $g)
                    <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>Kelompok {{ $g }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>
        </div>
    </form>

    {{-- ASSESSMENT FORM --}}
    <form action="{{ route('guru.checklist.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

        <div class="space-y-6">
            @forelse($students as $student)
            @php
                $studentAssessments = $assessmentsByStudent->get($student->id) ?? collect();
            @endphp
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                {{-- Header Siswa --}}
                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-50 flex items-center gap-4">
                    <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 leading-tight">{{ $student->full_name }}</h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">Kelompok {{ $student->class_group }} · NISN: {{ $student->student_no }}</p>
                    </div>
                </div>

                {{-- Grid Penilaian --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($domains as $code => $domain)
                    @php $currentScore = $studentAssessments->firstWhere('domain_code', $code)?->score_label; @endphp
                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100/60 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg icon-{{ $domain['color'] }} flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">{{ $domain['icon'] }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $domain['label'] }}</span>
                        </div>
                        <div class="flex gap-1.5 justify-between">
                            @foreach(['BB','MB','BSH','BSB'] as $lbl)
                            <label class="flex-1">
                                <input class="score-radio" type="radio" name="assessments[{{ $student->id }}][{{ $code }}]" value="{{ $lbl }}" {{ $currentScore == $lbl ? 'checked' : '' }}>
                                <span class="score-pill pill-{{ $lbl }} w-full">{{ $lbl }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                <p class="text-sm font-bold text-slate-400 mt-3">Tidak ada siswa ditemukan di kelompok ini</p>
            </div>
            @endforelse
        </div>

        @if($students->isNotEmpty())
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
            <button type="submit" class="gradient-primary text-white pl-8 pr-10 py-4 rounded-2xl font-extrabold text-sm flex items-center gap-3 shadow-2xl shadow-blue-500/40 hover:scale-105 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
                Simpan Semua Penilaian Ceklis
            </button>
        </div>
        @endif
    </form>
</div>
@endsection