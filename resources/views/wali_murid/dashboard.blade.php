@extends('layouts.parent')

@php $title = 'Beranda - Portal Wali Murid TK Wonoayu'; @endphp

@section('content')
{{-- Hero Section --}}
<header class="mb-10 relative">
    <div class="absolute right-0 top-0 w-64 h-64 bg-tertiary-container rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute right-20 top-20 w-48 h-48 bg-primary-container rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    <h1 class="text-4xl md:text-5xl font-headline font-black text-on-surface mb-2 relative z-10">
        Halo, {{ $guardian?->guardian_name ?? 'Wali Murid' }}!
    </h1>
    <p class="text-on-surface-variant font-body text-lg relative z-10">
        Berikut adalah ringkasan hari ini untuk
        <span class="font-bold text-primary">{{ $student?->nickname ?? $student?->full_name ?? 'Ananda' }}</span>.
    </p>
</header>

{{-- Bento Grid --}}
<div class="grid grid-cols-1 md:grid-cols-12 gap-6">

    {{-- Kehadiran --}}
    <section class="md:col-span-8 bg-surface-container-lowest rounded-xl p-6 relative overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="font-headline text-xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                    Kehadiran Minggu Ini
                </h2>
                <p class="text-on-surface-variant text-sm mt-1">Minggu aktif belajar</p>
            </div>
            <span class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded-full text-sm font-bold shadow-sm">100% Hadir</span>
        </div>
        <div class="flex justify-between mt-8 relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-surface-container-high -z-10 -translate-y-1/2 rounded-full"></div>
            @foreach(['Sen','Sel','Rab','Kam'] as $day)
            <div class="flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check</span>
                </div>
                <span class="font-label text-sm font-bold">{{ $day }}</span>
            </div>
            @endforeach
            <div class="flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center border-2 border-dashed border-outline-variant shadow-sm">
                    <span class="material-symbols-outlined text-sm">schedule</span>
                </div>
                <span class="font-label text-sm font-medium text-on-surface-variant">Jum</span>
            </div>
        </div>
    </section>

    {{-- Laporan Terbaru --}}
    <section class="md:col-span-4 bg-gradient-to-br from-primary to-primary-container rounded-xl p-6 text-white relative shadow-[0_8px_30px_rgba(0,96,173,0.2)] overflow-hidden">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
        <h2 class="font-headline text-xl font-bold mb-4">Laporan Terbaru</h2>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 mb-4 min-h-[80px]">
            @if($latestReport)
            <p class="font-body text-sm leading-relaxed text-white/90">{{ Str::limit($latestReport->summary ?? 'Lihat detail laporan perkembangan ananda.', 120) }}</p>
            @else
            <p class="font-body text-sm leading-relaxed text-white/70 italic">Belum ada laporan tersedia.</p>
            @endif
        </div>
        <a class="inline-flex items-center justify-center w-full bg-white text-primary font-bold py-3 px-6 rounded-full hover:scale-105 transition-transform duration-300 shadow-md" href="{{ route('wali.report') }}">
            Lihat Detail Laporan
            <span class="material-symbols-outlined ml-2 text-sm">arrow_forward</span>
        </a>
    </section>

    {{-- Agenda & Pengumuman --}}
    <section class="md:col-span-6 space-y-6">
        <div class="bg-tertiary-container rounded-xl p-6 shadow-sm">
            <h2 class="font-headline text-xl font-bold text-on-tertiary-container flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">campaign</span>
                Pengumuman Sekolah
            </h2>
            <div class="flex flex-col gap-4">
                @if($announcement)
                <div class="bg-white/60 rounded-xl p-4 flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-full bg-tertiary text-white flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">campaign</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-on-tertiary-container">{{ $announcement->title }}</h3>
                        <p class="text-sm text-on-tertiary-container/80 mt-1">{{ Str::limit($announcement->content ?? '', 100) }}</p>
                    </div>
                </div>
                @else
                <div class="bg-white/40 rounded-xl p-4 text-center text-on-tertiary-container/70 italic text-sm">
                    Belum ada pengumuman terbaru.
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-headline text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
                    Agenda Sekolah
                </h2>
                <a href="{{ route('wali.agenda') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            
            {{-- Mini Calendar Grid --}}
            @php
                $daysInMonth = now()->daysInMonth;
                $firstDay = now()->startOfMonth()->dayOfWeek;
                
                // Fetch agendas that overlap with current month
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                
                $agendas = \App\Models\SchoolAgenda::where('is_public', true)
                    ->where(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereBetween('event_date', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                          ->orWhere(function($sq) use ($startOfMonth, $endOfMonth) {
                              $sq->where('event_date', '<=', $startOfMonth)
                                ->where('end_date', '>=', $endOfMonth);
                          });
                    })->get();

                // Group agendas by day (handling multi-day)
                $agendasThisMonth = collect();
                foreach($agendas as $agenda) {
                    $start = $agenda->event_date;
                    $end = $agenda->end_date ?: $start;
                    $current = $start->copy();
                    while($current <= $end) {
                        if($current->month == now()->month && $current->year == now()->year) {
                            $day = $current->day;
                            if(!$agendasThisMonth->has($day)) $agendasThisMonth->put($day, collect());
                            $agendasThisMonth->get($day)->push($agenda);
                        }
                        $current->addDay();
                    }
                }

                $typeStyles = [
                    'libur'      => ['bg' => 'bg-rose-500',    'text' => 'text-white',       'label' => 'LIBUR'],
                    'kegiatan'   => ['bg' => 'bg-blue-500',    'text' => 'text-white',       'label' => 'KEGIATAN'],
                    'ujian'      => ['bg' => 'bg-amber-500',   'text' => 'text-white',       'label' => 'UJIAN'],
                    'pengumuman' => ['bg' => 'bg-emerald-500', 'text' => 'text-white',       'label' => 'INFO'],
                    'lainnya'    => ['bg' => 'bg-slate-400',   'text' => 'text-white',       'label' => 'LAIN'],
                ];
            @endphp
            <div class="mb-6">
                <div class="grid grid-cols-7 gap-0.5 text-center mb-1">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">{{ $day }}</span>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-0.5">
                    @for($i = 0; $i < $firstDay; $i++)
                        <div class="h-9"></div>
                    @endfor
                    @for($i = 1; $i <= $daysInMonth; $i++)
                        @php 
                            $isToday = ($i == now()->day); 
                            $dayAgendas = $agendasThisMonth->get($i, collect());
                            $priorityOrder = ['libur','ujian','kegiatan','pengumuman','lainnya'];
                            $topAgenda = null;
                            foreach($priorityOrder as $p) {
                                $found = $dayAgendas->where('type', $p)->first();
                                if ($found) { $topAgenda = $found; break; }
                            }
                        @endphp
                        @if($isToday && $topAgenda)
                            <div class="h-9 flex flex-col items-center justify-center rounded-lg bg-primary text-white shadow-md cursor-pointer relative"
                                 title="{{ $dayAgendas->pluck('title')->implode(', ') }}">
                                <span class="text-[10px] font-black leading-none">{{ $i }}</span>
                                <span class="text-[5px] font-black mt-0.5 opacity-90 uppercase tracking-tighter">{{ $typeStyles[$topAgenda->type]['label'] }}</span>
                            </div>
                        @elseif($isToday)
                            <div class="h-9 flex flex-col items-center justify-center rounded-lg bg-primary text-white shadow-md cursor-pointer">
                                <span class="text-[10px] font-black leading-none">{{ $i }}</span>
                            </div>
                        @elseif($topAgenda)
                            <div class="h-9 flex flex-col items-center justify-center rounded-lg {{ $typeStyles[$topAgenda->type]['bg'] }} {{ $typeStyles[$topAgenda->type]['text'] }} shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                                 title="{{ $dayAgendas->pluck('title')->implode(', ') }}">
                                <span class="text-[10px] font-black leading-none">{{ $i }}</span>
                                <span class="text-[5px] font-black mt-0.5 opacity-90 uppercase tracking-tighter">{{ $typeStyles[$topAgenda->type]['label'] }}</span>
                            </div>
                        @else
                            <div class="h-9 flex items-center justify-center rounded-lg text-[10px] font-bold text-slate-500 hover:bg-slate-50 transition-colors cursor-pointer">
                                {{ $i }}
                            </div>
                        @endif
                    @endfor
                </div>

                {{-- Legend --}}
                <div class="mt-4 pt-3 border-t border-slate-50 grid grid-cols-3 gap-1">
                    @foreach($typeStyles as $key => $s)
                    <div class="flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full {{ $s['bg'] }} flex-shrink-0"></div>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tight">{{ $s['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Mendatang</p>
                @forelse($upcomingAgendas as $agenda)
                <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[9px] font-black text-primary uppercase">{{ $agenda->event_date->format('M') }}</span>
                        <span class="text-sm font-black text-slate-700 leading-none">{{ $agenda->event_date->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $agenda->title }}</h4>
                        <p class="text-[10px] text-slate-400 font-medium">
                            {{ $agenda->event_date->isoFormat('dddd, D MMMM YYYY') }}
                            @if($agenda->end_date && $agenda->end_date->ne($agenda->event_date))
                                - {{ $agenda->end_date->isoFormat('dddd, D MMMM YYYY') }}
                            @endif
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 italic text-center py-4">Tidak ada agenda mendatang.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Menu Cepat --}}
    <section class="md:col-span-6 bg-surface-container-low rounded-xl p-6">
        <h2 class="font-headline text-xl font-bold text-on-surface mb-6">Menu Cepat</h2>
        <div class="grid grid-cols-2 gap-4">
            <a class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-outline-variant/15 flex flex-col items-center text-center hover:scale-[1.02] transition-transform" href="{{ route('wali.report') }}">
                <div class="w-12 h-12 rounded-full bg-primary-fixed/30 text-primary flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assessment</span>
                </div>
                <span class="font-bold text-sm text-on-surface">Laporan Raport</span>
            </a>
            <a class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-outline-variant/15 flex flex-col items-center text-center hover:scale-[1.02] transition-transform" href="{{ route('wali.agenda') }}">
                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
                </div>
                <span class="font-bold text-sm text-on-surface">Agenda Sekolah</span>
            </a>
            <a class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-outline-variant/15 flex flex-col items-center text-center hover:scale-[1.02] transition-transform" href="{{ route('wali.gallery') }}">
                <div class="w-12 h-12 rounded-full bg-secondary-fixed/30 text-secondary flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">photo_library</span>
                </div>
                <span class="font-bold text-sm text-on-surface">Galeri Kegiatan</span>
            </a>
            <a class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-outline-variant/15 flex flex-col items-center text-center hover:scale-[1.02] transition-transform" href="{{ route('wali.profile') }}">
                <div class="w-12 h-12 rounded-full bg-tertiary-container/60 text-tertiary flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">child_care</span>
                </div>
                <span class="font-bold text-sm text-on-surface">Profil Anak</span>
            </a>
        </div>
    </section>

</div>
@endsection