@extends('layouts.parent')

@php
    $title = 'Absensi - Portal Wali Murid TK Wonoayu';
    $statusMeta = [
        'hadir' => ['label' => 'Hadir', 'icon' => 'check_circle', 'class' => 'bg-emerald-50 text-emerald-700', 'iconText' => 'text-emerald-500', 'dot' => 'bg-emerald-500'],
        'izin' => ['label' => 'Izin', 'icon' => 'assignment_late', 'class' => 'bg-blue-50 text-blue-700', 'iconText' => 'text-blue-500', 'dot' => 'bg-blue-500'],
        'sakit' => ['label' => 'Sakit', 'icon' => 'sick', 'class' => 'bg-amber-50 text-amber-700', 'iconText' => 'text-amber-400', 'dot' => 'bg-amber-500'],
        'alpa' => ['label' => 'Alpa', 'icon' => 'cancel', 'class' => 'bg-rose-50 text-rose-700', 'iconText' => 'text-rose-500', 'dot' => 'bg-rose-500'],
    ];
@endphp

@section('styles')
<style>
    .attendance-page {
        --attendance-panel: rgba(15, 23, 42, 0.94);
        --attendance-panel-soft: rgba(15, 23, 42, 0.82);
        --attendance-border: rgba(148, 163, 184, 0.22);
        --attendance-border-soft: rgba(148, 163, 184, 0.18);
        --attendance-text: #e5eefb;
        --attendance-muted: #a9b8cc;
    }

    .attendance-page .attendance-panel {
        background: var(--attendance-panel) !important;
        border-color: var(--attendance-border) !important;
        box-shadow: 0 18px 42px rgba(2, 6, 23, 0.28) !important;
        color: var(--attendance-text) !important;
    }

    .attendance-page .attendance-panel-soft {
        background: var(--attendance-panel-soft) !important;
        border-color: var(--attendance-border-soft) !important;
        color: var(--attendance-text) !important;
    }

    .attendance-page .attendance-table-head {
        background: rgba(15, 23, 42, 0.92) !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.22) !important;
    }

    .attendance-page .attendance-table-row {
        background: rgba(15, 23, 42, 0.94) !important;
    }

    .attendance-page .attendance-table-row:hover {
        background: rgba(30, 41, 59, 0.88) !important;
    }

    .attendance-page .attendance-chip {
        background: rgba(56, 189, 248, 0.12) !important;
        border: 1px solid rgba(56, 189, 248, 0.20) !important;
        color: var(--attendance-muted) !important;
    }
</style>
@endsection

@section('content')
<div class="attendance-page max-w-5xl mx-auto space-y-6">
    <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-black uppercase tracking-widest text-primary mb-3">
                <span class="material-symbols-outlined text-[16px]">event_available</span>
                Absensi Anak
            </div>
            <h1 class="text-3xl md:text-4xl font-headline font-black text-on-surface">Kehadiran {{ $student->nickname ?? $student->full_name }}</h1>
            <p class="text-sm text-on-surface-variant mt-2">Riwayat absensi yang dicatat guru akan tampil di halaman ini.</p>
        </div>
        <a href="{{ route('wali.dashboard') }}" class="btn btn-outline rounded-full text-sm font-black">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Beranda
        </a>
    </header>

    <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div class="attendance-panel card lg:col-span-5 rounded-2xl border p-6">
            <p class="text-sm font-bold text-slate-300">Persentase minggu ini</p>
            <div class="mt-4 flex items-end gap-3">
                <span class="text-6xl font-black leading-none text-white">{{ $attendancePercent ?? 0 }}</span>
                <span class="pb-2 text-lg font-black text-sky-200">%</span>
            </div>
            <p class="mt-4 text-sm font-bold text-slate-400">Dihitung dari Senin sampai Jumat pada minggu aktif belajar.</p>
        </div>

        <div class="attendance-panel card lg:col-span-7 rounded-2xl border p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-headline text-lg font-black text-white">Minggu Ini</h2>
                    <p class="text-xs font-bold text-slate-400">Status harian terbaru</p>
                </div>
            </div>
            <div class="grid grid-cols-5 gap-2">
                @php
                    $weekStart = now()->startOfWeek();
                    $days = ['Sen','Sel','Rab','Kam','Jum'];
                @endphp
                @foreach(range(0,4) as $i)
                    @php
                        $d = $weekStart->copy()->addDays($i);
                        $att = $weekAttendances->first(fn($item) => $item->date->toDateString() === $d->toDateString());
                        $meta = $statusMeta[$att?->status ?? 'alpa'];
                    @endphp
                    <div class="attendance-panel-soft rounded-2xl border p-3 text-center">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full {{ $att ? $meta['class'] : 'bg-white text-on-surface-variant' }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $att ? $meta['icon'] : 'schedule' }}</span>
                        </div>
                        <p class="text-xs font-black text-white">{{ $days[$i] }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $att ? $meta['label'] : 'Belum' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statusMeta as $key => $meta)
            <div class="attendance-panel stat rounded-2xl border p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">{{ $meta['label'] }}</span>
                    <span class="material-symbols-outlined text-[20px] {{ $meta['iconText'] }}">{{ $meta['icon'] }}</span>
                </div>
                <p class="mt-3 text-3xl font-black text-white">{{ $statusCounts[$key] ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400">Dalam halaman riwayat ini</p>
            </div>
        @endforeach
    </section>

    <section class="attendance-panel card rounded-2xl border overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700/70">
            <div>
                <h2 class="font-headline text-lg font-black text-white">Riwayat Absensi</h2>
                <p class="text-xs font-bold text-slate-400">Data terbaru ditampilkan paling atas</p>
            </div>
        </div>

        @if($attendances->count())
            <div class="overflow-x-auto">
                <table class="table w-full min-w-[640px] text-sm">
                    <thead class="attendance-table-head text-left">
                        <tr class="text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Masuk</th>
                            <th class="px-5 py-4">Pulang</th>
                            <th class="px-5 py-4">Catatan Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/70">
                        @foreach($attendances as $attendance)
                            @php $meta = $statusMeta[$attendance->status] ?? $statusMeta['alpa']; @endphp
                            <tr class="attendance-table-row transition-colors">
                                <td class="px-5 py-4 font-black text-white">{{ $attendance->date->isoFormat('dddd, D MMMM YYYY') }}</td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-lg px-3 py-1.5 text-xs font-black {{ $meta['class'] }}">
                                        <span class="material-symbols-outlined text-[16px]">{{ $meta['icon'] }}</span>
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="attendance-chip inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-black">
                                        <span class="material-symbols-outlined text-[15px]">login</span>
                                        {{ $attendance->check_in_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="attendance-chip inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-black">
                                        <span class="material-symbols-outlined text-[15px]">logout</span>
                                        {{ $attendance->check_out_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-400">{{ $attendance->note ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-700/70">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-500">event_busy</span>
                <p class="mt-3 font-black text-white">Belum ada data absensi.</p>
                <p class="text-sm text-slate-400">Data akan muncul setelah guru menyimpan absensi.</p>
            </div>
        @endif
    </section>
</div>
@endsection
