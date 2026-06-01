@extends('layouts.teacher')

@php
    $title = 'Penilaian Percakapan - TK Wonoayu';
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    
    /* Score radio buttons */
    .score-radio { display: none !important; }
    .score-pill {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 48px; height: 36px; padding: 0 12px;
        border-radius: 10px; font-size: 11px; font-weight: 900;
        cursor: pointer; transition: all 0.18s cubic-bezier(.4,0,.2,1);
        border: 1.5px solid #e2e8f0; letter-spacing: 0.03em;
        color: #94a3b8; background: #f8fafc;
        user-select: none;
    }
    .score-radio:checked + .score-pill.pill-BB { background:#fee2e2; color:#dc2626; border-color:#fca5a5; box-shadow: 0 2px 8px rgba(239,68,68,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-MB { background:#fef3c7; color:#d97706; border-color:#fcd34d; box-shadow: 0 2px 8px rgba(245,158,11,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-BSH { background:#dcfce7; color:#16a34a; border-color:#86efac; box-shadow: 0 2px 8px rgba(34,197,94,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-BSB { background:#dbeafe; color:#2563eb; border-color:#93c5fd; box-shadow: 0 2px 8px rgba(59,130,246,0.18); transform: scale(1.06); }
    .score-pill:hover { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="assessment-header bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-6 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">forum</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Penilaian Percakapan</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Metode Tanya Jawab Terpandu</p>
        </div>
    </div>
</header>

<div class="assessment-module max-w-7xl mx-auto w-full pb-20">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 flex flex-col gap-1 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl text-sm font-bold">
        @foreach($errors->all() as $error)
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
            {{ $error }}
        </div>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- LEFT COLUMN: FORM INPUT --}}
        <div class="lg:col-span-1 bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden sticky top-24">
            <div class="assessment-form-header assessment-form-header-panel px-6 py-5 border-b border-slate-50 bg-gradient-to-b from-slate-50/50 to-white">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_circle</span> Tambah Penilaian
                </h3>
            </div>
            
            <form action="{{ route('guru.panel.store') }}" method="POST" class="p-6 space-y-5" id="assessment-form">
                @csrf
                <input type="hidden" name="assessment_id" id="assessment_id">
                
                {{-- Student --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                        <select name="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Pilih Siswa...</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">Kel. {{ $student->class_group }} - {{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                {{-- Date --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Tanggal</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[18px]">calendar_month</span>
                        <input name="assessed_on" type="date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                {{-- Activity --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Kegiatan Pembelajaran</label>
                    <input name="activity" type="text" placeholder="Contoh: Mengenal Binatang" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium">
                </div>

                {{-- Aspect --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Aspek yang Diamati</label>
                    <textarea name="aspect" rows="2" placeholder="Contoh: Kemampuan menjawab pertanyaan secara lisan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium resize-none"></textarea>
                </div>

                {{-- Score --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Capaian Perkembangan</label>
                    <div class="flex items-center gap-2 w-full">
                        @foreach(['BB','MB','BSH','BSB'] as $lbl)
                        <label class="flex-1">
                            <input class="score-radio" type="radio" name="score_label" value="{{ $lbl }}" required>
                            <span class="score-pill pill-{{ $lbl }} w-full">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full gradient-primary text-white py-3.5 rounded-xl text-sm font-extrabold shadow-lg shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: LIST OF RECORDS --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Filter Bar --}}
            <form action="{{ route('guru.panel') }}" method="GET" id="filter-form" class="bg-white rounded-2xl border border-slate-100 ambient-shadow p-4 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px] space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Tanggal</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[16px]">calendar_today</span>
                        <input name="date" type="date" value="{{ $date->format('Y-m-d') }}" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>
                <div class="flex-1 min-w-[150px] space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelompok</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">groups</span>
                        <select name="group" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-8 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Semua Kelompok</option>
                            @foreach(['A','B'] as $g)
                            <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>Kelompok {{ $g }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                    </div>
                </div>
            </form>

            {{-- Records List --}}
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">history</span> Riwayat Penilaian
                    </h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-200/50 px-2.5 py-1 rounded-md">{{ $assessments->count() }} Data</span>
                </div>
                
                <div class="divide-y divide-slate-50">
                    @forelse($assessments as $assessment)
                    <div class="p-6 hover:bg-slate-50/50 transition-colors flex flex-col sm:flex-row gap-5 items-start">
                        <div class="flex items-center gap-3 w-full sm:w-1/3 flex-shrink-0">
                            <img src="{{ $assessment->student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($assessment->student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}"
                                 class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100">
                            <div class="truncate">
                                <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $assessment->student->full_name }}</p>
                                <p class="text-[10px] font-bold text-slate-500 mt-0.5">Kel. {{ $assessment->student->class_group }}</p>
                            </div>
                        </div>
                        
                        <div class="flex-1 space-y-2 w-full">
                            <div>
                                <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-0.5">Kegiatan</p>
                                <p class="text-sm font-bold text-slate-700">{{ $assessment->activity }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Aspek Diamati</p>
                                <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ $assessment->aspect }}</p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 self-start sm:self-center">
                            @php
                                $score = $assessment->score_label;
                                $badgeClass = match($score) {
                                    'BB' => 'bg-red-100 text-red-700 border border-red-200',
                                    'MB' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                    'BSH' => 'bg-green-100 text-green-700 border border-green-200',
                                    'BSB' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    default => 'bg-slate-100 text-slate-400 border border-slate-200',
                                };
                            @endphp
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Capaian</span>
                                <span class="inline-flex items-center justify-center min-w-[48px] px-2 py-1.5 rounded-lg text-xs font-black shadow-sm {{ $badgeClass }}">
                                    {{ $score }}
                                </span>
                            </div>
                        </div>

                        <div class="flex sm:flex-col items-center gap-2 border-l border-slate-100 pl-4 ml-2 flex-shrink-0">
                            <button type="button" onclick="editAssessment({{ $assessment->toJson() }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <form action="{{ route('guru.panel.destroy', $assessment->id) }}" method="POST" onsubmit="return confirm('Hapus penilaian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="py-16 text-center bg-slate-50/50">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                            <span class="material-symbols-outlined text-3xl text-slate-300">chat_bubble_outline</span>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Belum ada penilaian percakapan</p>
                        <p class="text-[11px] font-medium text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk menambah penilaian.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function editAssessment(data) {
        document.getElementById('assessment_id').value = data.id;
        document.getElementsByName('student_id')[0].value = data.student_id;
        
        // Handle date
        if (data.assessed_on) {
            document.getElementsByName('assessed_on')[0].value = data.assessed_on.split('T')[0];
        }
        
        document.getElementsByName('activity')[0].value = data.activity;
        document.getElementsByName('aspect')[0].value = data.aspect;
        
        // Score radio
        const radio = document.querySelector(`input[name="score_label"][value="${data.score_label}"]`);
        if (radio) radio.checked = true;
        
        // Scroll to form
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Visual indicator
        const formTitle = document.querySelector('h3.font-extrabold');
        formTitle.innerHTML = '<span class="material-symbols-outlined text-amber-500 text-[20px]">edit</span> Edit Penilaian';
        formTitle.parentElement.classList.add('bg-amber-50');
    }
</script>
@endsection
