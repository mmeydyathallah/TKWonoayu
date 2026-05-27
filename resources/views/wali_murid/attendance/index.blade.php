@extends('layouts.parent')

@php $title = 'Absensi - Portal Wali Murid TK Wonoayu'; @endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-headline font-black">Absensi Anak</h1>
            <p class="text-sm text-on-surface-variant">Ringkasan dan riwayat absensi untuk {{ $student->nickname ?? $student->full_name }}</p>
        </div>
        <a href="{{ route('wali.dashboard') }}" class="text-sm font-bold text-primary">Kembali</a>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
        <h2 class="font-bold mb-2">Ringkasan Terbaru</h2>
        <div class="flex items-center gap-6">
            <div>
                <div class="text-xs text-slate-500">Persentase Kehadiran (Minggu Ini)</div>
                <div class="text-3xl font-black text-primary">{{ isset($attendancePercent) ? $attendancePercent . '%' : '-' }}</div>
            </div>
            <div class="flex-1">
                <div class="text-xs text-slate-500">Entri Terakhir</div>
                @if($attendances->count())
                    <div class="text-sm">{{ $attendances->first()->date->isoFormat('dddd, D MMMM YYYY') }} — <span class="font-bold">{{ ucfirst($attendances->first()->status) }}</span></div>
                @else
                    <div class="text-sm italic text-slate-400">Belum ada data absensi.</div>
                @endif
            </div>
            <div>
                <a href="{{ route('wali.report') }}" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-full font-bold">Lihat Laporan</a>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-low rounded-xl p-4">
        <h3 class="font-bold mb-4">Riwayat Absensi</h3>
        @if($attendances->count())
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 text-xs uppercase">
                    <th class="py-2">Tanggal</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $a)
                <tr class="border-t">
                    <td class="py-3">{{ $a->date->isoFormat('dddd, D MMM YYYY') }}</td>
                    <td class="py-3 font-bold">{{ ucfirst($a->status) }}</td>
                    <td class="py-3">{{ $a->note ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $attendances->links() }}</div>
        @else
        <div class="py-6 text-center text-slate-400 italic">Belum ada data absensi untuk saat ini.</div>
        @endif
    </div>
</div>
@endsection
