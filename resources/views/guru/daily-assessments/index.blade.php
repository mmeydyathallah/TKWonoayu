@extends('layouts.teacher')

@php
    $title = 'Penilaian Harian - TK Wonoayu';
    $aspects = [
        'NAM'  => ['label' => 'Nilai Agama & Moral',  'icon' => 'mosque',              'color' => 'purple'],
        'FM'   => ['label' => 'Fisik Motorik',         'icon' => 'sports_gymnastics',   'color' => 'emerald'],
        'KOG'  => ['label' => 'Kognitif',              'icon' => 'psychology',          'color' => 'amber'],
        'BHS'  => ['label' => 'Bahasa',                'icon' => 'chat_bubble',         'color' => 'blue'],
        'SEM'  => ['label' => 'Sosial Emosional',      'icon' => 'favorite',            'color' => 'rose'],
        'SENI' => ['label' => 'Seni',                  'icon' => 'palette',             'color' => 'orange'],
    ];
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow   { box-shadow: 0 4px 24px rgba(0,0,0,0.04); }

    .score-radio { display: none; }
    .score-pill {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 50px; height: 34px; padding: 0 12px;
        border-radius: 10px; font-size: 10px; font-weight: 900;
        cursor: pointer; transition: all .18s ease;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8; background: #f8fafc;
        user-select: none; letter-spacing: .03em;
    }
    .score-radio:checked + .score-pill.pill-BB  { background:#fee2e2; color:#dc2626; border-color:#fca5a5; transform:scale(1.05); box-shadow:0 2px 8px rgba(239,68,68,.12); }
    .score-radio:checked + .score-pill.pill-MB  { background:#fef3c7; color:#d97706; border-color:#fcd34d; transform:scale(1.05); box-shadow:0 2px 8px rgba(245,158,11,.12); }
    .score-radio:checked + .score-pill.pill-BSH { background:#dcfce7; color:#16a34a; border-color:#86efac; transform:scale(1.05); box-shadow:0 2px 8px rgba(34,197,94,.12);  }
    .score-radio:checked + .score-pill.pill-BSB { background:#dbeafe; color:#2563eb; border-color:#93c5fd; transform:scale(1.05); box-shadow:0 2px 8px rgba(59,130,246,.12); }
    
    /* Clear button pill style */
    .score-radio:checked + .score-pill.pill-clear { background:#f1f5f9; color:#64748b; border-color:#cbd5e1; }
    
    .score-pill:hover { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }
    .assessment-card-focus { box-shadow: 0 0 0 3px rgba(0, 96, 173, .16), 0 18px 36px rgba(15, 23, 42, .08) !important; }

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
<header class="assessment-header bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-8 py-4 w-full -mx-8 mb-8 docked full-width">
    <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl gradient-primary flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
            <span class="material-symbols-outlined text-[22px]">edit_note</span>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 font-headline tracking-tight leading-none">Penilaian Harian</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $date->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>
</header>

<div class="assessment-module max-w-7xl mx-auto w-full pb-32">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold shadow-sm">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- LAYOUT --}}
    <div class="flex flex-col gap-8">

        {{-- FILTER BAR --}}
        <div class="w-full">
            <form action="{{ route('guru.daily') }}" method="GET" id="filter-form"
                  class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-5 mb-6 flex flex-wrap gap-4 items-end">

                {{-- Search --}}
                <div class="flex-1 min-w-[180px] space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">search</span>
                        <input name="search" type="text" value="{{ request('search') }}" placeholder="Cari nama..."
                               class="w-full bg-slate-50 rounded-2xl py-3 pl-9 pr-3 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                    </div>
                </div>

                {{-- Date --}}
                <div class="flex-1 min-w-[160px] space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[16px]">calendar_month</span>
                        <input name="date" type="date" value="{{ $date->format('Y-m-d') }}"
                               onchange="document.getElementById('filter-form').submit()"
                               class="w-full bg-slate-50 rounded-2xl py-3 pl-9 pr-3 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                    </div>
                </div>

                {{-- Aspect Dropdown --}}
                <div class="flex-1 min-w-[210px] space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Aspek Perkembangan</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[16px]">collections_bookmark</span>
                        <select name="aspect" onchange="document.getElementById('filter-form').submit()"
                                class="w-full bg-slate-50 rounded-2xl py-3 pl-9 pr-8 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none appearance-none transition-all">
                            @foreach($aspects as $code => $asp)
                            <option value="{{ $asp['label'] }}" {{ $selectedAspect === $asp['label'] ? 'selected' : '' }}>{{ $asp['label'] }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                {{-- Group --}}
                <div class="flex-1 min-w-[140px] space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelompok</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">groups</span>
                        <select name="group" onchange="document.getElementById('filter-form').submit()"
                                class="w-full bg-slate-50 rounded-2xl py-3 pl-9 pr-8 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none appearance-none transition-all">
                            <option value="">Semua</option>
                            @foreach(['A','B'] as $g)
                            <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>Kel. {{ $g }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <button type="submit" class="gradient-primary text-white px-5 py-3 rounded-2xl font-extrabold text-xs flex items-center gap-1.5 shadow-md shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all h-[46px]">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span> Filter
                </button>
            </form>
            
            {{-- ASSESSMENT FORM --}}
            <form action="{{ route('guru.daily.store') }}" method="POST" id="assessment-form">
                @csrf
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="aspect" value="{{ $selectedAspect }}">

                {{-- GLOBAL ACTIVITY INPUT --}}
                <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-5 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                        <div class="md:col-span-5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Aspek Perkembangan</label>
                            <div class="flex items-center gap-3">
                                @php $activeAspectDetails = collect($aspects)->firstWhere('label', $selectedAspect); @endphp
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center icon-{{ $activeAspectDetails['color'] ?? 'blue' }} shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">{{ $activeAspectDetails['icon'] ?? 'star' }}</span>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-800 text-sm leading-none">{{ $selectedAspect }}</h4>
                                    <p class="text-[9px] text-slate-400 font-bold mt-1">Pilih aspek perkembangan dari daftar di filter atas.</p>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-7 space-y-1">
                            <label for="activity-input" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Tema / Subtema</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">topic</span>
                                <input id="activity-input" name="activity" type="text" value="{{ $activity }}" list="activity-options" autocomplete="off" placeholder="Contoh: Tanaman / Buah Mangga, Diriku / Anggota Tubuh..."
                                       class="w-full bg-slate-50 rounded-2xl py-3 pl-11 pr-3 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                                <datalist id="activity-options">
                                    @foreach($activityOptions as $option)
                                    <option value="{{ $option }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STUDENT CARD GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @forelse($students as $student)
                    @php
                        $hasAssessment = $assessmentsByStudent->has($student->id);
                        $currentScore = $assessmentsByStudent->get($student->id)?->score_label;
                        $currentObservation = $assessmentsByStudent->get($student->id)?->observation;
                    @endphp

                    <div id="daily-card-{{ $student->id }}" class="bg-white rounded-3xl border {{ $hasAssessment ? 'border-emerald-100 bg-emerald-50/5' : 'border-slate-100' }} ambient-shadow overflow-hidden transition-all hover:border-slate-200">
                        {{-- Student Header --}}
                        <div class="flex items-center gap-3 px-5 py-4 border-b {{ $hasAssessment ? 'border-emerald-100/50 bg-emerald-50/20' : 'border-slate-50 bg-slate-50/40' }}">
                            <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=e0efff&color=0060ad&bold=true&size=80' }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-extrabold text-slate-900 text-sm leading-none truncate">{{ $student->full_name }}</p>
                                <p class="text-[10px] font-bold text-slate-400 mt-1">Kelompok {{ $student->class_group }}{{ $student->nickname ? ' · "'.$student->nickname.'"' : '' }}</p>
                            </div>
                            <div>
                                @if($hasAssessment)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-emerald-600 bg-emerald-100 text-[9px] font-black px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Terisi
                                    </span>
                                    <button type="button" onclick="focusDailyAssessment({{ $student->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit penilaian">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                    <button type="submit" form="delete-daily-{{ $assessmentsByStudent->get($student->id)->id }}" onclick="return confirm('Hapus penilaian harian {{ $student->full_name }}?')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Hapus penilaian">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </div>
                                @else
                                <span class="text-slate-400 text-[9px] font-black uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-full">Belum Dinilai</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Assessment Body --}}
                        <div class="p-5 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Capaian Perkembangan</span>
                                <div class="flex gap-1.5 flex-wrap">
                                    {{-- Clear / Kosongkan --}}
                                    <label>
                                        <input class="score-radio" type="radio"
                                               name="scores[{{ $student->id }}]"
                                               value="" {{ !$hasAssessment ? 'checked' : '' }}>
                                        <span class="score-pill pill-clear">KOSONG</span>
                                    </label>
                                    
                                    {{-- Scores --}}
                                    @foreach(['BB','MB','BSH','BSB'] as $lbl)
                                    <label>
                                        <input class="score-radio" type="radio"
                                               name="scores[{{ $student->id }}]"
                                               value="{{ $lbl }}" {{ $currentScore === $lbl ? 'checked' : '' }}>
                                        <span class="score-pill pill-{{ $lbl }}">{{ $lbl }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Observation --}}
                            <div class="space-y-1">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Catatan Observasi Khusus Ananda (Opsional)</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[16px]">history_edu</span>
                                    <textarea name="observations[{{ $student->id }}]" rows="2" placeholder="Tulis catatan unik, kebiasaan, atau keberhasilan spesifik anak hari ini..."
                                              class="w-full bg-slate-50/50 rounded-2xl py-2 pl-9 pr-3 text-xs font-bold text-slate-600 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none resize-none leading-relaxed transition-all">{{ $currentObservation }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                        <p class="text-sm font-bold text-slate-400 mt-3">Tidak ada siswa ditemukan</p>
                    </div>
                    @endforelse
                </div>

                @if($students->isNotEmpty())
                <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
                    <button type="submit" class="gradient-primary text-white pl-6 pr-8 py-4 rounded-2xl font-extrabold text-sm flex items-center gap-3 shadow-2xl shadow-blue-500/40 hover:scale-105 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[22px]">cloud_done</span>
                        Simpan Penilaian Harian
                    </button>
                </div>
                @endif
            </form>

            @foreach($assessmentsByStudent as $assessment)
            <form id="delete-daily-{{ $assessment->id }}" action="{{ route('guru.daily.destroy', $assessment) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            @endforeach
        </div>

        {{-- BOTTOM SECTION: WEEKLY RECAP TABLE --}}
        <div class="w-full mt-8">
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-slate-50/50 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Rekap Penilaian Mingguan</h3>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-primary">calendar_view_week</span>
                            {{ $startOfWeek->translatedFormat('d F Y') }} — {{ $endOfWeek->translatedFormat('d F Y') }}
                        </p>
                    </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <form action="{{ route('guru.daily') }}" method="GET" class="flex items-end gap-2 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                <input type="hidden" name="aspect" value="{{ $selectedAspect }}">
                                <input type="hidden" name="group" value="{{ $group }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Pilih Minggu</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[15px]">calendar_view_week</span>
                                        <input name="week" type="date" value="{{ $recapDate->format('Y-m-d') }}"
                                               onchange="this.form.submit()"
                                               class="w-[170px] bg-white rounded-xl py-2.5 pl-9 pr-3 text-xs font-bold text-slate-700 border border-slate-100 focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                                    </div>
                                </div>
                            </form>
                            <div class="flex items-center gap-3 bg-primary/5 px-4 py-2.5 rounded-xl border border-primary/10">
                                <span class="text-[10px] font-black text-primary uppercase tracking-wider">Aspek Perkembangan</span>
                                <span class="text-xs font-black text-white bg-primary px-3 py-1 rounded-lg shadow-sm">Tema/Subtema Mandiri</span>
                            </div>
                        </div>
                    </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[850px]">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 uppercase text-[9px] font-black tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4 w-[250px]">Identitas Siswa</th>
                                @foreach($aspects as $code => $asp)
                                <th class="px-4 py-4 text-center" title="{{ $asp['label'] }}">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center icon-{{ $asp['color'] }} shrink-0">
                                            <span class="material-symbols-outlined text-[13px]">{{ $asp['icon'] }}</span>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-slate-500 mt-1">{{ $code }}</span>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($students as $student)
                            @php
                                $studentAssessments = $weeklyAssessments->get($student->id) ?? collect();
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}"
                                             class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100 flex-shrink-0">
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 leading-tight">{{ $student->full_name }}</p>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-[9px] font-bold text-slate-500">No. {{ $student->student_no ?? '-' }}</span>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-[9px] font-bold text-primary bg-primary/10 px-1.5 rounded">Kel. {{ $student->class_group }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                @foreach($aspects as $code => $asp)
                                @php
                                    $assessment = $studentAssessments->firstWhere('aspect_name', $asp['label']);
                                    $score = $assessment?->score_label;
                                    $badgeClass = match($score) {
                                        'BB' => 'bg-red-50 text-red-600 border-red-200',
                                        'MB' => 'bg-amber-50 text-amber-600 border-amber-200',
                                        'BSH' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                        'BSB' => 'bg-blue-50 text-blue-600 border-blue-200',
                                        default => 'bg-slate-50 text-slate-300 border-slate-100',
                                    };
                                @endphp
                                <td class="px-4 py-4 text-center">
                                    @if($score)
                                    <div class="flex flex-col items-center">
                                        <span class="inline-flex items-center justify-center w-10 h-6 rounded border text-[10px] font-black {{ $badgeClass }}" 
                                              title="{{ $assessment->activity }}">
                                            {{ $score }}
                                        </span>
                                        @if($assessment->observation)
                                            <span class="material-symbols-outlined text-[12px] text-slate-400 mt-1 cursor-help" 
                                                  title="Catatan: {{ $assessment->observation }}">info</span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400 italic text-sm">Tidak ada rekap mingguan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end layout --}}
</div>

<script>
    function focusDailyAssessment(studentId) {
        const card = document.getElementById(`daily-card-${studentId}`);
        if (!card) return;
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.classList.add('assessment-card-focus');
        setTimeout(() => card.classList.remove('assessment-card-focus'), 1400);
    }
</script>
@endsection
