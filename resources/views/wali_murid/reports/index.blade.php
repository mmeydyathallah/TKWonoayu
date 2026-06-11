@extends('layouts.parent')

@php
    $title = 'Laporan Perkembangan - Portal Wali Murid TK Wonoayu';
    $scoreColors = [
        'BB' => 'bg-red-100 text-red-700 border-red-200',
        'MB' => 'bg-amber-100 text-amber-700 border-amber-200',
        'BSH' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'BSB' => 'bg-sky-100 text-sky-700 border-sky-200',
    ];
@endphp

@section('styles')
<style>
    .tab-btn { border-bottom: 2px solid transparent; cursor: pointer; }
    .tab-btn.active { border-color: #38bdf8; color: #e0f2fe; }
    .report-panel {
        background: rgba(15, 23, 42, .92);
        border: 1px solid rgba(148, 163, 184, .18);
        box-shadow: 0 18px 42px rgba(2, 6, 23, .24);
        color: #e5eefb;
    }
    .report-soft {
        background: rgba(30, 41, 59, .72);
        border: 1px solid rgba(148, 163, 184, .16);
    }
    .report-muted { color: #a9b8cc; }
    .gallery-image { object-fit: contain; background: rgba(2, 6, 23, .56); }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <div class="report-panel rounded-3xl p-5 flex items-center gap-5">
        <img alt="Foto {{ $student->full_name }}"
             class="w-16 h-16 rounded-2xl object-cover border border-slate-500/30"
             src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=0f172a&color=e0f2fe&size=96' }}">
        <div class="min-w-0 flex-1">
            <h1 class="font-headline text-xl font-black text-white truncate">{{ $student->full_name }}</h1>
            <p class="mt-1 text-xs font-bold report-muted">Kelompok {{ $student->class_group }} - TA {{ $student->school_year }}</p>
        </div>
        <div class="hidden sm:block text-right">
            <p class="text-[10px] font-black uppercase tracking-widest report-muted">Semester</p>
            <p class="text-sm font-black text-sky-200">{{ $report?->semester ?? '-' }}</p>
        </div>
    </div>

    <div class="flex gap-1 border-b border-slate-700/60 overflow-x-auto">
        @foreach([
            'raport' => 'Ringkasan',
            'harian' => 'Harian',
            'anekdot' => 'Anekdot',
            'karya' => 'Hasil Karya',
        ] as $key => $label)
        <button type="button" onclick="switchTab('{{ $key }}')" id="btn-{{ $key }}"
                class="tab-btn {{ $key === 'raport' ? 'active' : '' }} px-4 py-3 text-sm font-black text-slate-400 whitespace-nowrap hover:text-sky-100 transition-colors">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <div id="tab-raport">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="report-panel rounded-3xl p-6 lg:col-span-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-2xl bg-sky-400/12 text-sky-200 flex items-center justify-center">
                        <span class="material-symbols-outlined">auto_awesome</span>
                    </div>
                    <h2 class="font-headline text-lg font-black text-white">Narasi Perkembangan Ananda</h2>
                </div>
                @if($report?->summary)
                <p class="text-sm leading-relaxed report-muted whitespace-pre-line">{{ $report->summary }}</p>
                @else
                <p class="text-sm font-bold report-muted py-8 text-center">Belum ada ringkasan narasi dari wali kelas.</p>
                @endif
            </section>

            <aside class="report-panel rounded-3xl p-6 lg:col-span-4">
                <h3 class="text-xs font-black uppercase tracking-widest text-white mb-4">Keterangan Nilai</h3>
                <div class="space-y-3">
                    @foreach($scoreOptions as $code => $label)
                    <div class="report-soft rounded-2xl p-3 flex items-center gap-3">
                        <span class="inline-flex w-11 justify-center rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreColors[$code] ?? '' }}">{{ $code }}</span>
                        <div>
                            <p class="text-xs font-black text-white">{{ $label }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </aside>
        </div>

        @if($report?->teacher_note)
        <section class="report-panel rounded-3xl p-6 mt-6">
            <h3 class="font-headline text-base font-black text-white mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-sky-200">chat</span>
                Pesan dari Wali Kelas
            </h3>
            <p class="text-sm italic leading-relaxed report-muted">"{{ $report->teacher_note }}"</p>
        </section>
        @endif
    </div>

    <div id="tab-harian" class="hidden">
        @forelse($dailyLearningReports as $weekKey => $items)
        @php
            [$startStr, $endStr] = explode('|', $weekKey);
            $weekStart = \Carbon\Carbon::parse($startStr);
            $weekEnd = \Carbon\Carbon::parse($endStr);
        @endphp
        <section class="report-panel rounded-3xl overflow-hidden mb-6">
            <div class="report-soft rounded-none border-x-0 border-t-0 px-6 py-5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-400/12 text-sky-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">calendar_view_week</span>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest report-muted">Minggu Pembelajaran</p>
                    <h2 class="text-sm font-black text-white">{{ $weekStart->translatedFormat('d M') }} - {{ $weekEnd->translatedFormat('d M Y') }}</h2>
                </div>
            </div>

            <div class="p-5 space-y-5">
                @foreach($items->sortBy('assessed_on') as $dailyReport)
                @php
                    $photosByDomain = $dailyReport->photos->groupBy('domain_code');
                @endphp
                <article class="report-soft rounded-3xl p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest report-muted">Laporan Harian</p>
                            <h3 class="font-headline text-lg font-black text-white">{{ $dailyReport->assessed_on->translatedFormat('l, d F Y') }}</h3>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <section>
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sky-200 text-[18px]">school</span>
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-300">Intrakurikuler</h4>
                            </div>
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                                @foreach($intrakurikulerDomains as $domainCode => $domain)
                                @php
                                    $score = $dailyReport->{$domain['score_column']};
                                    $domainPhotos = $photosByDomain->get($domainCode) ?? collect();
                                @endphp
                                <div class="rounded-2xl border border-slate-600/30 bg-slate-950/24 p-4">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-start gap-2">
                                            <div class="w-9 h-9 rounded-xl bg-sky-400/10 text-sky-200 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">{{ $domain['icon'] }}</span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-white leading-snug">{{ $domain['label'] }}</p>
                                            </div>
                                        </div>
                                        @if($score)
                                        <span class="shrink-0 rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreColors[$score] ?? '' }}">{{ $score }}</span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-3">
                                        @foreach([1, 2] as $slot)
                                        @php $photo = $domainPhotos->firstWhere('slot', $slot); @endphp
                                        <div class="rounded-2xl border border-slate-600/25 bg-slate-950/30 overflow-hidden">
                                            <div class="aspect-[4/3]">
                                                @if($photo?->image_path)
                                                <img src="{{ asset('storage/'.$photo->image_path) }}" alt="{{ $photo->title }}" class="gallery-image h-full w-full">
                                                @else
                                                <div class="h-full w-full flex flex-col items-center justify-center text-slate-500">
                                                    <span class="material-symbols-outlined text-3xl">image_not_supported</span>
                                                    <span class="text-[10px] font-black mt-1">Foto belum tersedia</span>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="px-3 py-2 border-t border-slate-700/50">
                                                <p class="text-xs font-bold text-slate-300">{{ $photo?->title ?: 'Judul foto belum diisi' }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </section>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <section class="rounded-2xl border border-slate-600/30 bg-slate-950/24 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-emerald-200 text-[18px]">hub</span>
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-300">Kokurikuler</h4>
                                </div>
                                @if($dailyReport->kokurikuler_description)
                                <p class="text-sm leading-relaxed report-muted whitespace-pre-line">{{ $dailyReport->kokurikuler_description }}</p>
                                @else
                                <p class="text-sm font-bold report-muted">Belum ada deskripsi pengembangan proyek.</p>
                                @endif
                            </section>

                            <section class="rounded-2xl border border-slate-600/30 bg-slate-950/24 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-amber-200 text-[18px]">sports_handball</span>
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-300">Ekstrakurikuler</h4>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest report-muted">Kegiatan Pelaksanaan</p>
                                        <p class="text-sm font-bold text-white">{{ $dailyReport->extracurricular_implementation ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest report-muted">Kegiatan Ekstrakurikuler</p>
                                        <p class="text-sm font-bold text-white">{{ $dailyReport->extracurricular_activity ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest report-muted">Keterangan Nilai</p>
                                        @if($dailyReport->extracurricular_score_label)
                                        <span class="mt-1 inline-flex rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreColors[$dailyReport->extracurricular_score_label] ?? '' }}">{{ $dailyReport->extracurricular_score_label }}</span>
                                        @else
                                        <p class="text-sm font-bold report-muted">-</p>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @empty
        <div class="report-panel rounded-3xl py-16 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-500">event_busy</span>
            <p class="mt-3 text-sm font-bold report-muted">Belum ada laporan harian format baru.</p>
        </div>
        @endforelse
    </div>

    <div id="tab-anekdot" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($anecdotalNotes as $note)
            <article class="report-panel rounded-3xl p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="text-[10px] font-black uppercase tracking-widest report-muted">{{ $note->recorded_at->translatedFormat('d F Y') }}</p>
                    @if($note->tone)
                    <span class="rounded-full bg-slate-800 px-3 py-1 text-[10px] font-black text-slate-200">{{ $note->tone }}</span>
                    @endif
                </div>
                <p class="text-sm leading-relaxed report-muted">{{ $note->description }}</p>
            </article>
            @empty
            <div class="report-panel rounded-3xl py-16 text-center md:col-span-2">
                <p class="text-sm font-bold report-muted">Belum ada catatan anekdot.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div id="tab-karya" class="hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($artworks as $art)
            <article class="report-panel rounded-3xl overflow-hidden">
                <div class="aspect-video bg-slate-950/50">
                    @if($art->image_url)
                    <img src="{{ $art->image_url }}" class="gallery-image h-full w-full" alt="{{ $art->title }}">
                    @else
                    <div class="h-full w-full flex flex-col items-center justify-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl">image_not_supported</span>
                        <span class="text-[10px] font-black uppercase tracking-widest mt-2">Foto belum tersedia</span>
                    </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-sm font-black text-white leading-snug">{{ $art->title ?? 'Hasil Karya' }}</h3>
                        @if($art->score_label)
                        <span class="shrink-0 rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreColors[$art->score_label] ?? '' }}">{{ $art->score_label }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest report-muted">{{ $art->created_on?->translatedFormat('d F Y') }}</p>
                    @if($art->description)
                    <p class="mt-3 text-xs leading-relaxed report-muted">{{ $art->description }}</p>
                    @endif
                </div>
            </article>
            @empty
            <div class="report-panel rounded-3xl py-16 text-center sm:col-span-2 lg:col-span-3">
                <p class="text-sm font-bold report-muted">Belum ada karya terdokumentasi.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const allTabs = ['raport', 'harian', 'anekdot', 'karya'];

    function switchTab(tab) {
        allTabs.forEach((key) => {
            document.getElementById('tab-' + key)?.classList.add('hidden');
            document.getElementById('btn-' + key)?.classList.remove('active');
        });
        document.getElementById('tab-' + tab)?.classList.remove('hidden');
        document.getElementById('btn-' + tab)?.classList.add('active');
    }
</script>
@endsection
