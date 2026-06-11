@extends('layouts.teacher')

@php
    $title = 'Penilaian Harian - TK Wonoayu';
    $scoreBadge = [
        'BB' => 'bg-red-100 text-red-700 border-red-200',
        'MB' => 'bg-amber-100 text-amber-700 border-amber-200',
        'BSH' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'BSB' => 'bg-sky-100 text-sky-700 border-sky-200',
    ];
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .daily-shell { color: #e5eefb; }
    .daily-panel {
        background: rgba(15, 23, 42, .92);
        border: 1px solid rgba(148, 163, 184, .18);
        box-shadow: 0 18px 44px rgba(2, 6, 23, .28);
    }
    .daily-soft {
        background: rgba(30, 41, 59, .72);
        border: 1px solid rgba(148, 163, 184, .16);
    }
    .daily-input {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, .22);
        background: rgba(15, 23, 42, .72);
        color: #f8fafc;
        outline: none;
    }
    .daily-input:focus { border-color: rgba(56, 189, 248, .65); box-shadow: 0 0 0 3px rgba(56, 189, 248, .14); }
    .daily-input::placeholder { color: #64748b; }
    .score-radio { display: none; }
    .score-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 46px;
        height: 32px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, .26);
        background: rgba(15, 23, 42, .74);
        color: #cbd5e1;
        font-size: 10px;
        font-weight: 900;
        cursor: pointer;
        transition: all .16s ease;
    }
    .score-radio:checked + .score-pill { transform: translateY(-1px); border-color: rgba(125, 211, 252, .65); box-shadow: 0 8px 18px rgba(14, 165, 233, .16); }
    .score-radio:checked + .pill-BB { background: #fee2e2; color: #b91c1c; }
    .score-radio:checked + .pill-MB { background: #fef3c7; color: #b45309; }
    .score-radio:checked + .pill-BSH { background: #dcfce7; color: #15803d; }
    .score-radio:checked + .pill-BSB { background: #dbeafe; color: #1d4ed8; }
    .image-preview { object-fit: contain; background: rgba(2, 6, 23, .55); }
    .student-focus { box-shadow: 0 0 0 3px rgba(125, 211, 252, .24), 0 24px 54px rgba(2, 6, 23, .36) !important; }
</style>
@endsection

@section('content')
<div class="daily-shell max-w-7xl mx-auto pb-32">
    <header class="daily-panel rounded-3xl p-5 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/16 text-sky-200 flex items-center justify-center border border-sky-300/20">
                <span class="material-symbols-outlined">assignment</span>
            </div>
            <div>
                <h1 class="font-headline text-xl font-black text-white">Penilaian Harian</h1>
                <p class="text-[11px] font-bold text-slate-400 mt-1">{{ $date->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
        <div class="daily-soft rounded-2xl px-4 py-3 text-xs font-bold text-slate-300">
            Intrakurikuler, Kokurikuler, dan Ekstrakurikuler per siswa
        </div>
    </header>

    @if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-300/20 bg-emerald-500/12 px-5 py-4 text-sm font-bold text-emerald-100 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-300">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 rounded-2xl border border-rose-300/20 bg-rose-500/12 px-5 py-4 text-sm font-bold text-rose-100 flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-300">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-300/20 bg-rose-500/12 px-5 py-4 text-sm font-bold text-rose-100">
        {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('guru.daily') }}" method="GET" id="filter-form" class="daily-panel rounded-3xl p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-4 space-y-1.5">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Siswa</label>
            <input name="search" type="text" value="{{ request('search') }}" placeholder="Cari nama siswa..."
                   class="daily-input px-4 py-3 text-xs font-bold">
        </div>
        <div class="md:col-span-3 space-y-1.5">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</label>
            <input name="date" type="date" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()"
                   class="daily-input px-4 py-3 text-xs font-bold">
        </div>
        <div class="md:col-span-3 space-y-1.5">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelompok</label>
            <select name="group" onchange="this.form.submit()" class="daily-input px-4 py-3 text-xs font-bold">
                <option class="text-slate-900" value="">Semua Kelompok</option>
                @foreach(['A', 'B'] as $g)
                <option class="text-slate-900" value="{{ $g }}" {{ $group === $g ? 'selected' : '' }}>Kelompok {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <button type="submit" class="w-full rounded-2xl bg-sky-500 px-5 py-3 text-xs font-black text-white hover:bg-sky-400 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                Filter
            </button>
        </div>
    </form>

    <form action="{{ route('guru.daily.store') }}" method="POST" enctype="multipart/form-data" id="daily-learning-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

        <div class="grid grid-cols-1 gap-6">
            @forelse($students as $student)
            @php
                $report = $reportsByStudent->get($student->id);
                $photosByDomain = $report?->photos?->groupBy('domain_code') ?? collect();
                $hasReport = filled($report);
            @endphp

            <article id="student-card-{{ $student->id }}" class="daily-panel rounded-3xl overflow-hidden">
                <div class="daily-soft border-x-0 border-t-0 rounded-none px-5 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=0f172a&color=e0f2fe&bold=true&size=96' }}"
                             alt="Foto {{ $student->full_name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-500/30">
                        <div class="min-w-0">
                            <h2 class="text-sm font-black text-white truncate">{{ $student->full_name }}</h2>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">Kelompok {{ $student->class_group }}{{ $student->nickname ? ' - '.$student->nickname : '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-[10px] font-black {{ $hasReport ? 'bg-emerald-400/15 text-emerald-200 border border-emerald-300/20' : 'bg-slate-700/70 text-slate-300 border border-slate-500/20' }}">
                            {{ $hasReport ? 'Sudah Ada Data' : 'Belum Diisi' }}
                        </span>
                        <button type="button" onclick="focusStudent({{ $student->id }})" class="w-9 h-9 rounded-xl bg-slate-800 text-slate-200 hover:bg-sky-500 hover:text-white transition-colors" title="Fokus siswa">
                            <span class="material-symbols-outlined text-[17px]">center_focus_strong</span>
                        </button>
                        @if($hasReport)
                        <button type="submit" form="delete-daily-{{ $report->id }}" onclick="return confirm('Hapus laporan harian {{ $student->full_name }} pada {{ $date->format('d-m-Y') }}?')" class="w-9 h-9 rounded-xl bg-rose-500/12 text-rose-200 hover:bg-rose-500 hover:text-white transition-colors" title="Hapus laporan">
                            <span class="material-symbols-outlined text-[17px]">delete</span>
                        </button>
                        @endif
                    </div>
                </div>

                <div class="p-5 space-y-5">
                    <section>
                        <div class="mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sky-300 text-[18px]">school</span>
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-300">Intrakurikuler</h3>
                        </div>
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                            @foreach($intrakurikulerDomains as $domainCode => $domain)
                            @php
                                $scoreColumn = $domain['score_column'];
                                $currentScore = old('reports.'.$student->id.'.intrakurikuler.'.$domainCode.'.score_label', $report?->{$scoreColumn});
                            @endphp
                            <div class="daily-soft rounded-2xl p-4 space-y-4">
                                <div class="flex items-start gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-sky-400/10 text-sky-200 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">{{ $domain['icon'] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-white leading-snug">{{ $domain['label'] }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">Nilai aspek dan 2 bukti foto.</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-1.5">
                                    <label title="Kosongkan nilai">
                                        <input class="score-radio" type="radio" name="reports[{{ $student->id }}][intrakurikuler][{{ $domainCode }}][score_label]" value="" {{ blank($currentScore) ? 'checked' : '' }}>
                                        <span class="score-pill">Kosong</span>
                                    </label>
                                    @foreach($scoreOptions as $score => $scoreLabel)
                                    <label title="{{ $scoreLabel }}">
                                        <input class="score-radio" type="radio" name="reports[{{ $student->id }}][intrakurikuler][{{ $domainCode }}][score_label]" value="{{ $score }}" {{ $currentScore === $score ? 'checked' : '' }}>
                                        <span class="score-pill pill-{{ $score }}">{{ $score }}</span>
                                    </label>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-3">
                                    @foreach([1, 2] as $slot)
                                    @php
                                        $photo = ($photosByDomain->get($domainCode) ?? collect())->firstWhere('slot', $slot);
                                        $existingUrl = $photo?->image_path ? asset('storage/'.$photo->image_path) : null;
                                        $titleName = 'reports['.$student->id.'][intrakurikuler]['.$domainCode.'][photos]['.$slot.'][title]';
                                        $oldTitleKey = 'reports.'.$student->id.'.intrakurikuler.'.$domainCode.'.photos.'.$slot.'.title';
                                    @endphp
                                    <div class="rounded-2xl border border-slate-500/20 bg-slate-950/28 p-3">
                                        <div class="aspect-[4/3] overflow-hidden rounded-xl border border-slate-600/30 bg-slate-950/60 mb-3">
                                            @if($existingUrl)
                                            <img data-preview-target="preview-{{ $student->id }}-{{ $domainCode }}-{{ $slot }}" src="{{ $existingUrl }}" alt="{{ $photo->title }}" class="image-preview w-full h-full">
                                            @else
                                            <div data-preview-target="preview-{{ $student->id }}-{{ $domainCode }}-{{ $slot }}" class="w-full h-full flex flex-col items-center justify-center text-slate-500">
                                                <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                                <span class="text-[10px] font-black mt-1">Foto {{ $slot }}</span>
                                            </div>
                                            @endif
                                        </div>
                                        <input type="text" name="{{ $titleName }}" value="{{ old($oldTitleKey, $photo?->title) }}" placeholder="Judul foto {{ $slot }}"
                                               class="daily-input px-3 py-2 text-[11px] font-bold mb-2">
                                        <input type="file" name="photos[{{ $student->id }}][{{ $domainCode }}][{{ $slot }}]" accept="image/*"
                                               data-preview="preview-{{ $student->id }}-{{ $domainCode }}-{{ $slot }}"
                                               class="block w-full text-[10px] font-bold text-slate-400 file:mr-2 file:rounded-lg file:border-0 file:bg-sky-500 file:px-2 file:py-1.5 file:text-[10px] file:font-black file:text-white">
                                        @if($photo)
                                        <label class="mt-2 flex items-center gap-2 text-[10px] font-bold text-slate-400">
                                            <input type="checkbox" name="reports[{{ $student->id }}][intrakurikuler][{{ $domainCode }}][photos][{{ $slot }}][delete]" value="1" class="rounded border-slate-500 bg-slate-900">
                                            Hapus foto slot ini
                                        </label>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="daily-soft rounded-2xl p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-300 text-[18px]">hub</span>
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-300">Kokurikuler</h3>
                            </div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi Pengembangan Proyek</label>
                            <textarea name="reports[{{ $student->id }}][kokurikuler_description]" rows="5" placeholder="Tuliskan perkembangan proyek ananda hari ini..."
                                      class="daily-input px-4 py-3 text-xs font-bold resize-y">{{ old('reports.'.$student->id.'.kokurikuler_description', $report?->kokurikuler_description) }}</textarea>
                        </div>

                        <div class="daily-soft rounded-2xl p-4 space-y-3">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-300 text-[18px]">sports_handball</span>
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-300">Ekstrakurikuler</h3>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kegiatan Pelaksanaan</label>
                                <input name="reports[{{ $student->id }}][extracurricular_implementation]" type="text" value="{{ old('reports.'.$student->id.'.extracurricular_implementation', $report?->extracurricular_implementation) }}" placeholder="Contoh: Latihan rutin, unjuk kerja, praktik bersama..."
                                       class="daily-input px-4 py-3 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kegiatan Ekstrakurikuler</label>
                                <input name="reports[{{ $student->id }}][extracurricular_activity]" type="text" value="{{ old('reports.'.$student->id.'.extracurricular_activity', $report?->extracurricular_activity) }}" placeholder="Contoh: Menari, menggambar, drumband..."
                                       class="daily-input px-4 py-3 text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Keterangan Nilai</label>
                                <div class="flex flex-wrap gap-1.5">
                                    @php $currentExtraScore = old('reports.'.$student->id.'.extracurricular_score_label', $report?->extracurricular_score_label); @endphp
                                    <label title="Kosongkan nilai">
                                        <input class="score-radio" type="radio" name="reports[{{ $student->id }}][extracurricular_score_label]" value="" {{ blank($currentExtraScore) ? 'checked' : '' }}>
                                        <span class="score-pill">Kosong</span>
                                    </label>
                                    @foreach($scoreOptions as $score => $scoreLabel)
                                    <label title="{{ $scoreLabel }}">
                                        <input class="score-radio" type="radio" name="reports[{{ $student->id }}][extracurricular_score_label]" value="{{ $score }}" {{ $currentExtraScore === $score ? 'checked' : '' }}>
                                        <span class="score-pill pill-{{ $score }}">{{ $score }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </article>
            @empty
            <div class="daily-panel rounded-3xl py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-500">group_off</span>
                <p class="mt-3 text-sm font-bold text-slate-400">Tidak ada siswa ditemukan.</p>
            </div>
            @endforelse
        </div>

        @if($students->isNotEmpty())
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
            <button type="submit" class="rounded-2xl bg-sky-500 px-7 py-4 text-sm font-black text-white shadow-2xl shadow-sky-950/50 hover:bg-sky-400 active:scale-95 transition-all flex items-center gap-3">
                <span class="material-symbols-outlined">cloud_done</span>
                Simpan Penilaian Harian
            </button>
        </div>
        @endif
    </form>

    @foreach($reportsByStudent as $report)
    <form id="delete-daily-{{ $report->id }}" action="{{ route('guru.daily.destroy', $report) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach

    <section class="daily-panel rounded-3xl mt-8 overflow-hidden">
        <div class="daily-soft rounded-none border-x-0 border-t-0 px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="font-headline text-base font-black text-white">Rekap Penilaian Mingguan</h3>
                <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    {{ $startOfWeek->translatedFormat('d F Y') }} - {{ $endOfWeek->translatedFormat('d F Y') }}
                </p>
            </div>
            <form action="{{ route('guru.daily') }}" method="GET" class="flex items-end gap-2">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="group" value="{{ $group }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pilih Minggu</label>
                    <input name="week" type="date" value="{{ $recapDate->format('Y-m-d') }}" onchange="this.form.submit()" class="daily-input px-4 py-2.5 text-xs font-bold">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left">
                <thead class="bg-slate-950/30 text-[9px] font-black uppercase tracking-widest text-slate-400">
                    <tr>
                        <th class="px-5 py-4">Siswa</th>
                        @foreach($intrakurikulerDomains as $domain)
                        <th class="px-4 py-4 text-center">{{ $domain['short'] }}</th>
                        @endforeach
                        <th class="px-4 py-4">Kokurikuler</th>
                        <th class="px-4 py-4">Ekstrakurikuler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    @forelse($students as $student)
                    @php
                        $studentReports = $weeklyReports->get($student->id) ?? collect();
                        $latestReport = $studentReports->sortByDesc('assessed_on')->first();
                    @endphp
                    <tr class="hover:bg-slate-800/35 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=0f172a&color=e0f2fe&bold=true&size=64' }}" class="w-10 h-10 rounded-xl object-cover border border-slate-500/30" alt="">
                                <div>
                                    <p class="text-xs font-black text-white">{{ $student->full_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">Kelompok {{ $student->class_group }}</p>
                                </div>
                            </div>
                        </td>
                        @foreach($intrakurikulerDomains as $domain)
                        @php
                            $column = $domain['score_column'];
                            $score = $latestReport?->{$column};
                        @endphp
                        <td class="px-4 py-4 text-center">
                            @if($score)
                            <span class="inline-flex min-w-10 items-center justify-center rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreBadge[$score] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">{{ $score }}</span>
                            @else
                            <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-4 py-4">
                            <p class="max-w-[260px] truncate text-xs font-bold text-slate-300">{{ $latestReport?->kokurikuler_description ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            @if($latestReport?->extracurricular_score_label)
                            <div class="flex items-center gap-2">
                                <span class="rounded-lg border px-2 py-1 text-[10px] font-black {{ $scoreBadge[$latestReport->extracurricular_score_label] ?? '' }}">{{ $latestReport->extracurricular_score_label }}</span>
                                <span class="text-xs font-bold text-slate-300 truncate max-w-[180px]">{{ $latestReport->extracurricular_activity ?: '-' }}</span>
                            </div>
                            @else
                            <span class="text-slate-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm font-bold text-slate-500">Tidak ada data rekap.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    function focusStudent(studentId) {
        const card = document.getElementById(`student-card-${studentId}`);
        if (!card) return;
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.classList.add('student-focus');
        setTimeout(() => card.classList.remove('student-focus'), 1400);
    }

    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const target = document.querySelector(`[data-preview-target="${this.dataset.preview}"]`);
            if (!target) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                if (target.tagName === 'IMG') {
                    target.src = event.target.result;
                    return;
                }

                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'image-preview w-full h-full';
                img.setAttribute('data-preview-target', this.dataset.preview);
                target.replaceWith(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection
