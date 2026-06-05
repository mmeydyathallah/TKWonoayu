@extends('layouts.parent')

@php 
    $title = 'Laporan Perkembangan - Portal Wali Murid TK Wonoayu'; 
    $domains = [
        'NAM'   => ['icon' => 'mosque',            'color' => 'bg-indigo-50 text-indigo-600',  'label' => 'Nilai Agama & Moral'],
        'FM'    => ['icon' => 'directions_run',    'color' => 'bg-orange-50 text-orange-600',  'label' => 'Fisik Motorik'],
        'KOG'   => ['icon' => 'extension',         'color' => 'bg-blue-50 text-blue-600',      'label' => 'Kognitif'],
        'BHS'   => ['icon' => 'record_voice_over', 'color' => 'bg-teal-50 text-teal-600',      'label' => 'Bahasa'],
        'SOSEM' => ['icon' => 'people',             'color' => 'bg-pink-50 text-pink-600',      'label' => 'Sosial Emosional'],
        'SENI'  => ['icon' => 'palette',            'color' => 'bg-yellow-50 text-yellow-600',  'label' => 'Seni'],
    ];
    $scoreColors = [
        'BB'  => 'bg-red-100 text-red-600',
        'MB'  => 'bg-amber-100 text-amber-600',
        'BSH' => 'bg-green-100 text-green-600',
        'BSB' => 'bg-blue-100 text-blue-600',
    ];
@endphp

@section('styles')
<style>
    .tab-btn { border-bottom: 2px solid transparent; cursor: pointer; }
    .tab-btn.active { border-color: #0060ad; color: #0060ad; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    .score-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-BB { background: #ef4444; }
    .dot-MB { background: #f59e0b; }
    .dot-BSH { background: #10b981; }
    .dot-BSB { background: #3b82f6; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="mb-8 flex items-center gap-5 bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <img alt="Foto {{ $student->full_name }}"
         class="w-16 h-16 rounded-xl object-cover border-2 border-slate-100"
         src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=68abff&color=ffffff&size=64' }}"/>
    <div class="flex-1">
        <h1 class="text-xl font-black text-slate-800">{{ $student->full_name }}</h1>
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest mt-0.5">Kelompok {{ $student->class_group }} · TA {{ $student->school_year }}</p>
    </div>
    <div class="hidden sm:block text-right">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Semester</p>
        <p class="text-sm font-extrabold text-primary">{{ $report?->semester ?? '—' }}</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-1 border-b border-slate-200 mb-8 overflow-x-auto">
    @foreach([
        'raport'     => 'Ringkasan',
        'harian'     => 'Harian',
        'percakapan' => 'Percakapan',
        'anekdot'    => 'Anekdot',
        'karya'      => 'Hasil Karya',
    ] as $key => $label)
    <button type="button" onclick="switchTab('{{ $key }}')" id="btn-{{ $key }}"
        class="tab-btn {{ $key === 'raport' ? 'active' : '' }} px-4 py-3 text-sm font-semibold text-slate-400 whitespace-nowrap transition-colors hover:text-slate-600">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ===== TAB: RINGKASAN ===== --}}
<div id="tab-raport">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <div class="md:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-blue-50 rounded-full -z-10"></div>
                <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">auto_awesome</span>
                    Narasi Perkembangan Ananda
                </h3>
                @if($report?->summary)
                <p class="text-slate-600 font-medium leading-relaxed whitespace-pre-line">{{ $report->summary }}</p>
                @else
                <p class="text-slate-400 italic text-center py-8">Belum ada ringkasan narasi dari wali kelas.</p>
                @endif
            </div>

            @if($report?->teacher_note)
            <div class="bg-indigo-900 rounded-3xl p-8 text-white shadow-xl shadow-indigo-900/20">
                <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">chat</span>
                    Pesan dari Wali Kelas
                </h3>
                <p class="text-indigo-100 italic leading-relaxed">"{{ $report->teacher_note }}"</p>
            </div>
            @endif
        </div>

        <div class="md:col-span-4">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 mb-4 text-sm uppercase tracking-widest">Keterangan Nilai</h3>
                <div class="space-y-3">
                    @foreach(['BB' => 'Belum Berkembang', 'MB' => 'Mulai Berkembang', 'BSH' => 'Berkembang Sesuai Harapan', 'BSB' => 'Berkembang Sangat Baik'] as $code => $label)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="score-dot dot-{{ $code }}"></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $code }}</p>
                            <p class="text-xs font-bold text-slate-700">{{ $label }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TAB: HARIAN (Grouped by Week) ===== --}}
<div id="tab-harian" class="hidden max-h-screen overflow-y-auto">

    @php
        $allAspects = [
            'NAM'  => ['label'=>'Nilai Agama & Moral', 'icon'=>'mosque',            'color'=>'bg-purple-50 text-purple-600 border-purple-100','hex'=>'#7c3aed'],
            'FM'   => ['label'=>'Fisik Motorik',        'icon'=>'sports_gymnastics', 'color'=>'bg-emerald-50 text-emerald-600 border-emerald-100','hex'=>'#059669'],
            'KOG'  => ['label'=>'Kognitif',             'icon'=>'psychology',        'color'=>'bg-amber-50 text-amber-600 border-amber-100','hex'=>'#d97706'],
            'BHS'  => ['label'=>'Bahasa',               'icon'=>'chat_bubble',       'color'=>'bg-blue-50 text-blue-600 border-blue-100','hex'=>'#2563eb'],
            'SEM'  => ['label'=>'Sosial Emosional',     'icon'=>'favorite',          'color'=>'bg-rose-50 text-rose-600 border-rose-100','hex'=>'#e11d48'],
            'SENI' => ['label'=>'Seni',                 'icon'=>'palette',           'color'=>'bg-orange-50 text-orange-600 border-orange-100','hex'=>'#ea580c'],
        ];
        $aspCodeMap = [
            'Nilai Agama & Moral'=>'NAM','Fisik Motorik'=>'FM','Kognitif'=>'KOG',
            'Bahasa'=>'BHS','Sosial Emosional'=>'SEM','Seni'=>'SENI',
        ];
        $scoreToNum = ['BB'=>1,'MB'=>2,'BSH'=>3,'BSB'=>4];
        $allChartData  = [];
        $trendLabels   = [];
        $trendDatasets = array_fill_keys(array_keys($allAspects), []);
    @endphp

    {{-- Multi-week Trend Chart --}}
    @if($dailyAssessments->count() > 0)
    <div class="mb-8 bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-[18px]">trending_up</span>
            </div>
            <div>
                <p class="font-extrabold text-slate-800 text-sm leading-none">Tren Perkembangan Mingguan</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Skor per aspek tiap minggu (1=BB, 2=MB, 3=BSH, 4=BSB)</p>
            </div>
        </div>
        <div style="position:relative;height:240px;max-height:240px;">
            <canvas id="trend-chart"></canvas>
        </div>
    </div>
    <div id="charts-container" class="mb-8"></div>
    @endif

    @php
        $weekdayLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $weekdayColors = ['#2563eb', '#14b8a6', '#f59e0b', '#ec4899', '#10b981'];
        $latestWeekdayDatasets = array_fill_keys(array_keys($allAspects), array_fill(0, 5, 0));
    @endphp

    @forelse($dailyAssessments as $weekKey => $items)
    @php
        [$startStr, $endStr] = explode('|', $weekKey);
        $weekStart = \Carbon\Carbon::parse($startStr);
        $weekEnd   = \Carbon\Carbon::parse($endStr);
        $weekLabel = $weekStart->format('d M') . ' — ' . $weekEnd->format('d M Y');

        $weekScores = [];
        foreach($items as $item) {
            $code = $aspCodeMap[$item->aspect_name] ?? $item->aspect_code;
            if ($code && !isset($weekScores[$code])) $weekScores[$code] = $item;
        }

        $sum = 0; $count = 0;
        foreach(array_keys($allAspects) as $code) {
            if(isset($weekScores[$code])) {
                $sum += $scoreToNum[$weekScores[$code]->score_label] ?? 0;
                $count++;
            }
        }
        $avg = $count ? $sum / $count : 0;
        $statusInfo = $avg >= 3.5
            ? ['BSB','bg-blue-100 text-blue-700','#2563eb','Berkembang Sangat Baik']
            : ($avg >= 2.5
                ? ['BSH','bg-green-100 text-green-700','#16a34a','Berkembang Sesuai Harapan']
                : ($avg >= 1.5
                    ? ['MB','bg-amber-100 text-amber-700','#d97706','Mulai Berkembang']
                    : ['BB','bg-red-100 text-red-700','#dc2626','Belum Berkembang']));

        $trendLabels[] = $weekStart->format('d M');
        foreach(array_keys($allAspects) as $code) {
            $trendDatasets[$code][] = isset($weekScores[$code]) ? ($scoreToNum[$weekScores[$code]->score_label] ?? null) : null;
        }

        if ($loop->first) {
            $dayGroups = $items->groupBy(fn($item) => $item->assessed_on->dayOfWeekIso);
            foreach (range(1, 5) as $dayIso) {
                $dayItems = $dayGroups[$dayIso] ?? collect();
                foreach (array_keys($allAspects) as $code) {
                    $scoreItem = $dayItems->first(fn($item) => (($aspCodeMap[$item->aspect_name] ?? $item->aspect_code) === $code));
                    $latestWeekdayDatasets[$code][$dayIso - 1] = $scoreItem ? ($scoreToNum[$scoreItem->score_label] ?? 0) : 0;
                }
            }
        }
    @endphp

    <div class="mb-6 rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        {{-- Week Header --}}
        <div class="bg-gradient-to-br from-slate-50 to-white border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-primary text-[20px]">calendar_view_week</span>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Minggu Pembelajaran</p>
                        <p class="font-extrabold text-slate-800 text-sm mt-0.5">{{ $weekLabel }}</p>
                    </div>
                </div>
                <div class="flex flex-col md:items-end shrink-0">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Rata-rata Minggu Ini</p>
                    <span class="text-xs font-black {{ $statusInfo[1] }} px-3 py-1 rounded-full">{{ $statusInfo[0] }} — {{ $statusInfo[3] }}</span>
                    <p class="text-[9px] font-bold text-slate-400 mt-1 md:text-right">Skor {{ number_format($avg,1) }} / 4.0</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-slate-100">
                @foreach($allAspects as $code => $asp)
                @php $wItem = $weekScores[$code] ?? null; @endphp
                <div class="flex items-center gap-1.5 bg-white rounded-xl px-2.5 py-2 border border-slate-100 shadow-sm">
                    <div class="w-5 h-5 rounded-md flex items-center justify-center {{ $asp['color'] }} border">
                        <span class="material-symbols-outlined text-[11px]">{{ $asp['icon'] }}</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-600">{{ $code }}</span>
                    <span class="text-[10px] font-black rounded px-1 {{ $wItem ? ($scoreColors[$wItem->score_label] ?? '') : 'text-slate-300' }}">
                        {{ $wItem ? $wItem->score_label : '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        {{-- Day Cards --}}
        <div class="bg-white p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($items->sortBy('assessed_on') as $item)
                @php
                    $aspCode    = $aspCodeMap[$item->aspect_name] ?? ($item->aspect_code ?: strtoupper(substr($item->aspect_name,0,3)));
                    $aspDetails = $allAspects[$aspCode] ?? ['icon'=>'star','color'=>'bg-slate-50 text-slate-500 border-slate-200','label'=>$item->aspect_name];
                    $scoreFull  = match($item->score_label){
                        'BB'=>'Belum Berkembang','MB'=>'Mulai Berkembang',
                        'BSH'=>'Berkembang Sesuai Harapan','BSB'=>'Berkembang Sangat Baik',default=>$item->score_label};
                @endphp
                <div class="bg-slate-50/60 rounded-2xl border border-slate-100 hover:shadow-md transition-all p-4 flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $aspDetails['color'] }} border">
                                <span class="material-symbols-outlined text-[15px]">{{ $aspDetails['icon'] }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-800 truncate leading-none">{{ $aspDetails['label'] }}</p>
                                <p class="text-[9px] text-slate-400 mt-0.5">{{ $item->assessed_on->format('l, d M Y') }}</p>
                            </div>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded text-[11px] font-black {{ $scoreColors[$item->score_label] ?? 'bg-slate-100 text-slate-500' }}" title="{{ $scoreFull }}">{{ $item->score_label }}</span>
                    </div>
                    @if($item->activity)
                    <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-0.5">Tema / Kegiatan</p>
                        <p class="text-xs font-bold text-slate-700 leading-snug">{{ $item->activity }}</p>
                    </div>
                    @endif
                    @if($item->observation)
                    <div class="flex items-start gap-2 bg-primary/5 rounded-xl p-3 border border-primary/10">
                        <span class="material-symbols-outlined text-[13px] text-primary shrink-0 mt-0.5">comment</span>
                        <p class="text-[11px] text-slate-600 italic leading-relaxed">&ldquo;{{ $item->observation }}&rdquo;</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @empty
    <div class="py-16 text-center bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
        <span class="material-symbols-outlined text-4xl text-slate-300">event_busy</span>
        <p class="text-sm font-bold text-slate-400 mt-3">Belum ada catatan penilaian harian.</p>
    </div>
    @endforelse

    @if($dailyAssessments->isNotEmpty())
    <div id="chart-data-store"
         data-aspect-labels='@json(array_keys($allAspects))'
         data-aspect-colors='@json(array_column($allAspects,"hex"))'
         data-trend-labels='@json($trendLabels)'
         data-trend-datasets='@json($trendDatasets)'
         data-weekday-labels='@json($weekdayLabels)'
         data-weekday-datasets='@json($latestWeekdayDatasets)'
         data-weekday-colors='@json($weekdayColors)'
         style="display:none"></div>
    <div class="border-t border-slate-100 pt-5 mt-2 flex flex-wrap gap-4">
        @foreach(['BB'=>['Belum Berkembang','bg-red-100 text-red-600'],'MB'=>['Mulai Berkembang','bg-amber-100 text-amber-600'],'BSH'=>['Berkembang Sesuai Harapan','bg-green-100 text-green-600'],'BSB'=>['Berkembang Sangat Baik','bg-blue-100 text-blue-600']] as $c=>$i)
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $i[1] }}">{{ $c }}</span>
            <span class="text-xs font-bold text-slate-500">{{ $i[0] }}</span>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- ===== TAB: PERCAKAPAN ===== --}}
<div id="tab-percakapan" class="hidden">
    @php
        $toneColors = ['Positif'=>'bg-emerald-50 text-emerald-600 border-emerald-200','Netral'=>'bg-slate-50 text-slate-500 border-slate-200','Negatif'=>'bg-red-50 text-red-600 border-red-200'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        @forelse($conversationAssessments as $conv)
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-start justify-between gap-3 mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $conv->assessed_on->isoFormat('D MMM Y') }}</span>
                <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-black {{ $scoreColors[$conv->score_label] ?? 'bg-slate-100 text-slate-500' }}">
                    {{ $conv->score_label }}
                    <span class="font-medium"> · {{ ['BB'=>'Belum Berkembang','MB'=>'Mulai Berkembang','BSH'=>'Berkembang Sesuai Harapan','BSB'=>'Berkembang Sangat Baik'][$conv->score_label] ?? '' }}</span>
                </span>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-purple-500 text-[16px]">chat_bubble</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-800 mb-1">{{ $conv->activity }}</p>
                    <p class="text-xs text-slate-500 italic leading-relaxed">&ldquo;{{ $conv->aspect }}&rdquo;</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-slate-300 text-2xl">record_voice_over</span>
            </div>
            <p class="text-sm text-slate-400 italic">Belum ada catatan percakapan.</p>
        </div>
        @endforelse
    </div>
    @if($conversationAssessments->isNotEmpty())
    <div class="border-t border-slate-100 pt-5">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Keterangan Nilai</p>
        <div class="flex flex-wrap gap-3">
            @foreach(['BB'=>['Belum Berkembang','bg-red-100 text-red-600'],'MB'=>['Mulai Berkembang','bg-amber-100 text-amber-600'],'BSH'=>['Berkembang Sesuai Harapan','bg-green-100 text-green-600'],'BSB'=>['Berkembang Sangat Baik','bg-blue-100 text-blue-600']] as $code=>$info)
            <div class="flex items-center gap-1.5"><span class="px-2 py-0.5 rounded text-[10px] font-black {{ $info[1] }}">{{ $code }}</span><span class="text-xs text-slate-500">{{ $info[0] }}</span></div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ===== TAB: ANEKDOT ===== --}}
<div id="tab-anekdot" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        @forelse($anecdotalNotes as $note)
        @php
            $toneStyle = match($note->tone) {
                'Positif' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                'Negatif' => 'bg-red-50 text-red-700 border border-red-100',
                default   => 'bg-slate-50 text-slate-600 border border-slate-100',
            };
        @endphp
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $note->recorded_at->isoFormat('D MMM Y') }}</span>
                <div class="flex items-center gap-2">
                    @if($note->location)
                    <span class="flex items-center gap-1 text-[10px] text-slate-400 font-semibold">
                        <span class="material-symbols-outlined text-[12px]">location_on</span>{{ $note->location }}
                    </span>
                    @endif
                    @if($note->tone)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $toneStyle }}">{{ $note->tone }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-amber-500 text-[16px]">history_edu</span>
                </div>
                <p class="text-sm text-slate-700 leading-relaxed flex-1">{{ $note->description }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-slate-300 text-2xl">history_edu</span>
            </div>
            <p class="text-sm text-slate-400 italic">Belum ada catatan anekdot.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ===== TAB: HASIL KARYA ===== --}}
<div id="tab-karya" class="hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($artworks as $art)
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 card-hover transition-all flex flex-col">
            <div class="aspect-video bg-slate-100">
                <img src="{{ $art->image_url }}" class="w-full h-full object-cover" alt="{{ $art->title }}">
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-sm font-black text-slate-800 leading-tight">{{ $art->title ?? 'Karya Seni' }}</p>
                    <span class="shrink-0 px-2 py-0.5 rounded text-xs font-bold {{ $scoreColors[$art->score_label] ?? 'bg-slate-100 text-slate-500' }}">
                        {{ $art->score_label }}
                    </span>
                </div>
                <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase">{{ $art->created_on?->isoFormat('D MMM Y') }}</p>
                @if($art->description)
                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 italic">"{{ $art->description }}"</p>
                @endif
                @if($art->aspects)
                <div class="mt-auto pt-3 border-t border-slate-50 mt-3">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Aspek</p>
                    <p class="text-xs font-semibold text-slate-600">{{ $art->aspects }}</p>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-sm text-slate-400 italic">Belum ada karya terdokumentasi.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const allTabs = ['raport', 'harian', 'percakapan', 'anekdot', 'karya'];
let chartsReady = false;

function switchTab(tab) {
    allTabs.forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
        document.getElementById('btn-' + t).classList.remove('active');
    });
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('btn-' + tab).classList.add('active');
    if (tab === 'harian') initCharts();
}

function initCharts() {
    if (chartsReady) return;
    chartsReady = true;
    const store = document.getElementById('chart-data-store');
    if (!store) return;

    const aspectLabels   = JSON.parse(store.dataset.aspectLabels  || '[]');
    const aspectColors   = JSON.parse(store.dataset.aspectColors  || '[]');
    const trendLabels    = JSON.parse(store.dataset.trendLabels   || '[]');
    const trendDatasets  = JSON.parse(store.dataset.trendDatasets || '{}');
    const weekdayLabels  = JSON.parse(store.dataset.weekdayLabels || '[]');
    const weekdayDatasets = JSON.parse(store.dataset.weekdayDatasets || '{}');
    const weekdayColors  = JSON.parse(store.dataset.weekdayColors || '[]');

    // Normalize datasets: convert null/undefined to 0 and ensure numeric
    Object.keys(trendDatasets).forEach(k => {
        trendDatasets[k] = (trendDatasets[k] || []).map(v => {
            if (v === null || typeof v === 'undefined') return 0;
            const n = Number(v);
            return Number.isNaN(n) ? 0 : n;
        });
    });

    // If there's no meaningful data (all zeros), show placeholder and stop
    const hasData = Object.values(trendDatasets).some(arr => arr.some(v => v > 0));
    if (!hasData) {
        const container = document.getElementById('charts-container');
        if (container) {
            container.innerHTML = '<div class="py-12 text-center text-slate-400"><p class="font-bold">Belum ada data penilaian untuk ditampilkan.</p></div>';
        }
        return;
    }

    const scoreLabel = ['', 'BB', 'MB', 'BSH', 'BSB'];

    // ── Multi-week trend chart: one dataset per ASPEK (bar chart) ─────────
    const trendEl = document.getElementById('trend-chart');
    if (!trendEl) return;

    const aspKeys = Object.keys(trendDatasets);
    const lineDatasets = aspKeys.map((code, i) => ({
        label: code,
        data: (trendDatasets[code] || []).map(v => Number(v || 0)),
        backgroundColor: aspectColors[i] || '#888',
        borderRadius: 4,
        borderSkipped: false,
    }));

    new Chart(trendEl, {
        type: 'bar',
        data: { labels: trendLabels, datasets: lineDatasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800 },
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        font: { size: 11, weight: '600' }, 
                        usePointStyle: true, 
                        padding: 12,
                        boxHeight: 6
                    },
                    margin: { top: 16 }
                },
                tooltip: { 
                    callbacks: { 
                        label: ctx => ' ' + ctx.dataset.label + ': ' + (scoreLabel[ctx.raw] || '—') 
                    },
                    padding: 8,
                    font: { size: 11 }
                }
            },
            scales: {
                y: { 
                    min: 0, 
                    max: 4, 
                    ticks: { 
                        stepSize: 1, 
                        callback: v => scoreLabel[v] || '',
                        font: { size: 10, weight: '600' }
                    }, 
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false } 
                },
                x: { 
                    ticks: { 
                        font: { size: 10, weight: '600' }, 
                        color: '#64748b' 
                    }, 
                    grid: { display: false },
                    stacked: false
                }
            },
            datasets: {
                bar: { 
                    barPercentage: 0.7, 
                    categoryPercentage: 0.8 
                }
            }
        }
    });

    // ── Per-aspect mini charts: show weekly trend for each aspect (2 columns max) ──
    const chartContainer = document.getElementById('charts-container');
    if (!chartContainer) return;

    const perAspectContainer = document.createElement('div');
    perAspectContainer.className = 'grid gap-4 grid-cols-1 sm:grid-cols-2 auto-rows-max';

    aspKeys.forEach((code, idx) => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-2xl p-3 shadow-sm border border-slate-100';

        const title = document.createElement('div');
        title.className = 'flex items-center justify-between mb-2';
        title.innerHTML = `<strong class="text-xs font-black text-slate-800">${code}</strong><span class="text-[9px] text-slate-400">Tren Per Minggu</span>`;

        const c = document.createElement('canvas');
        c.id = 'chart-' + code;
        c.style.height = '100px';
        c.style.maxHeight = '100px';

        card.appendChild(title);
        card.appendChild(c);
        perAspectContainer.appendChild(card);

        const weekData = (trendDatasets[code] || []).map(v => Number(v || 0));
        const hasValues = weekData.some(v => v > 0);
        const multiplePoints = (weekData.length > 1);

        if (!hasValues) {
            const no = document.createElement('div');
            no.className = 'py-6 text-center text-slate-400 text-sm';
            no.innerText = 'Belum ada data';
            // remove canvas and show placeholder
            c.remove();
            card.appendChild(no);
            return;
        }

        new Chart(c, {
            type: multiplePoints ? 'line' : 'bar',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: code,
                    data: weekData,
                    borderColor: aspectColors[idx] || '#888',
                    backgroundColor: (aspectColors[idx] || '#888') + (multiplePoints ? '15' : '33'),
                    borderWidth: 2,
                    fill: multiplePoints,
                    tension: 0.3,
                    pointRadius: multiplePoints ? 3 : 5,
                    pointBackgroundColor: aspectColors[idx] || '#888',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => ' ' + (scoreLabel[ctx.raw] || '—') },
                        font: { size: 11 }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 4,
                        ticks: {
                            stepSize: 1,
                            callback: v => scoreLabel[v] || '',
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        display: multiplePoints,
                        ticks: { font: { size: 9, weight: '600' }, color: '#64748b' },
                        grid: { display: false }
                    }
                },
                elements: {
                    point: { radius: multiplePoints ? 3 : 6 },
                    line: { borderWidth: 2 }
                },
                layout: { padding: { top: 4, bottom: 4, left: 6, right: 6 } }
            }
        });
    });

    chartContainer.appendChild(perAspectContainer);
}


// Auto-init charts on page load if chart data exists
function bindTabs() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabKey = btn.id.replace('btn-', '');
            switchTab(tabKey);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('chart-data-store')) {
        initCharts();
    }
    bindTabs();
    window.switchTab = switchTab;
});
</script>
@endsection
