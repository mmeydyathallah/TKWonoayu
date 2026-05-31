@extends('layouts.parent')

@php
    $title = 'Absensi - Portal Wali Murid TK Wonoayu';
    $statusMeta = [
        'hadir' => ['label' => 'Hadir', 'icon' => 'check_circle', 'class' => 'bg-emerald-50 text-emerald-700', 'iconText' => 'text-emerald-600'],
        'izin' => ['label' => 'Izin', 'icon' => 'assignment_late', 'class' => 'bg-blue-50 text-blue-700', 'iconText' => 'text-blue-600'],
        'sakit' => ['label' => 'Sakit', 'icon' => 'sick', 'class' => 'bg-amber-50 text-amber-700', 'iconText' => 'text-amber-600'],
        'alpa' => ['label' => 'Alpa', 'icon' => 'cancel', 'class' => 'bg-rose-50 text-rose-700', 'iconText' => 'text-rose-600'],
    ];
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
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
        <div class="card lg:col-span-5 rounded-xl bg-gradient-to-br from-primary to-primary-container p-6 text-white shadow-[0_12px_35px_rgba(0,96,173,0.18)]">
            <p class="text-sm font-bold text-white/75">Persentase minggu ini</p>
            <div class="mt-4 flex items-end gap-3">
                <span class="text-6xl font-black leading-none">{{ $attendancePercent ?? 0 }}</span>
                <span class="pb-2 text-lg font-black">%</span>
            </div>
            <p class="mt-4 text-sm font-bold text-white/80">Dihitung dari Senin sampai Jumat pada minggu aktif belajar.</p>
        </div>

        <div class="card lg:col-span-7 rounded-xl bg-base-100 border border-base-300 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-headline text-lg font-black text-on-surface">Minggu Ini</h2>
                    <p class="text-xs font-bold text-on-surface-variant">Status harian terbaru</p>
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
                    <div class="rounded-2xl border border-outline-variant/15 bg-surface-container-low p-3 text-center">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full {{ $att ? $meta['class'] : 'bg-white text-on-surface-variant' }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $att ? $meta['icon'] : 'schedule' }}</span>
                        </div>
                        <p class="text-xs font-black text-on-surface">{{ $days[$i] }}</p>
                        <p class="text-[10px] font-bold text-on-surface-variant mt-1">{{ $att ? $meta['label'] : 'Belum' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statusMeta as $key => $meta)
            <div class="stat rounded-xl bg-base-100 border border-base-300 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-widest text-on-surface-variant">{{ $meta['label'] }}</span>
                    <span class="material-symbols-outlined text-[20px] {{ $meta['iconText'] }}">{{ $meta['icon'] }}</span>
                </div>
                <p class="mt-3 text-3xl font-black text-on-surface">{{ $statusCounts[$key] ?? 0 }}</p>
                <p class="text-xs font-bold text-on-surface-variant">Dalam halaman riwayat ini</p>
            </div>
        @endforeach
    </section>

    <section class="card rounded-xl bg-base-100 border border-base-300 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/15">
            <div>
                <h2 class="font-headline text-lg font-black text-on-surface">Riwayat Absensi</h2>
                <p class="text-xs font-bold text-on-surface-variant">Data terbaru ditampilkan paling atas</p>
            </div>
        </div>

        @if($attendances->count())
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full min-w-[640px] text-sm">
                    <thead class="bg-surface-container-low text-left">
                        <tr class="text-[11px] font-black uppercase tracking-widest text-on-surface-variant">
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Masuk</th>
                            <th class="px-5 py-4">Pulang</th>
                            <th class="px-5 py-4">Catatan Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/15">
                        @foreach($attendances as $attendance)
                            @php $meta = $statusMeta[$attendance->status] ?? $statusMeta['alpa']; @endphp
                            <tr>
                                <td class="px-5 py-4 font-black text-on-surface">{{ $attendance->date->isoFormat('dddd, D MMMM YYYY') }}</td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-lg px-3 py-1.5 text-xs font-black {{ $meta['class'] }}">
                                        <span class="material-symbols-outlined text-[16px]">{{ $meta['icon'] }}</span>
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-black text-on-surface-variant">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-5 py-4 font-black text-on-surface-variant">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-5 py-4 font-bold text-on-surface-variant">{{ $attendance->note ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-outline-variant/15">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-outline-variant">event_busy</span>
                <p class="mt-3 font-black text-on-surface">Belum ada data absensi.</p>
                <p class="text-sm text-on-surface-variant">Data akan muncul setelah guru menyimpan absensi.</p>
            </div>
        @endif
    </section>
</div>
@endsection
