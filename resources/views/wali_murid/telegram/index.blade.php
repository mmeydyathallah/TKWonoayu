@extends('layouts.parent')

@section('content')
<section class="space-y-6">
    <header class="flex flex-col gap-2">
        <h1 class="text-2xl md:text-3xl font-headline font-black text-on-surface">Status Telegram Wali</h1>
        <p class="text-sm text-on-surface-variant">Pantau apakah bot Telegram sudah terhubung dengan nomor wali dan siswa yang benar.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article class="card bg-base-200 border border-base-300 shadow-sm">
            <div class="card-body space-y-4">
            <h2 class="text-lg font-headline font-bold text-on-surface">Status Koneksi</h2>

            <div class="flex items-center justify-between rounded-xl bg-base-300 p-4">
                <span class="text-sm font-semibold text-on-surface-variant">Koneksi Bot</span>
                <span class="badge badge-sm font-bold {{ $isConnected ? 'badge-success' : 'badge-error' }}">
                    {{ $isConnected ? 'Terhubung' : 'Belum Terhubung' }}
                </span>
            </div>

            <div class="space-y-2 text-sm">
                <p><span class="font-semibold text-on-surface">Siswa:</span> {{ $student->full_name }}</p>
                <p><span class="font-semibold text-on-surface">Nomor Wali:</span> {{ $guardianPhoneNormalized ?? '-' }}</p>
                <p><span class="font-semibold text-on-surface">Chat ID:</span> {{ $telegramChat?->chat_id ?? '-' }}</p>
            </div>

            @if($isConnected)
                <div class="alert {{ $isSelectedForThisStudent ? 'alert-success' : 'alert-warning' }}">
                    <span>
                    @if($isSelectedForThisStudent)
                        <p class="text-sm font-semibold">Siswa aktif sudah sesuai.</p>
                        <p class="text-xs mt-1">Notifikasi masuk/pulang akan dikirim untuk {{ $student->full_name }}.</p>
                    @else
                        <p class="text-sm font-semibold">Siswa aktif belum sesuai.</p>
                        <p class="text-xs mt-1">
                            Bot saat ini terhubung ke siswa lain: {{ $selectedStudent?->full_name ?? 'tidak diketahui' }}.
                            Jalankan perintah <span class="font-bold">/siswa</span> lalu pilih {{ $student->full_name }}.
                        </p>
                    @endif
                    </span>
                </div>
            @endif
            </div>
        </article>

        <article class="card bg-base-200 border border-base-300 shadow-sm">
            <div class="card-body space-y-4">
            <h2 class="text-lg font-headline font-bold text-on-surface">Instruksi Penggunaan</h2>
            <ul class="steps steps-vertical text-sm text-on-surface-variant">
                <li class="step step-primary">Kirim perintah <span class="font-semibold text-on-surface">/hubungkan</span> ke bot Telegram.</li>
                <li class="step step-primary">Tekan tombol kirim kontak yang muncul agar nomor HP tersinkron.</li>
                <li class="step step-primary">Kirim perintah <span class="font-semibold text-on-surface">/siswa</span> untuk memilih anak yang dipantau.</li>
                <li class="step step-primary">Kirim perintah <span class="font-semibold text-on-surface">/plan</span> untuk melihat panduan fitur bot.</li>
            </ul>

            <div class="alert alert-info text-xs">
                <span>
                Jika status masih belum terhubung, cek lagi apakah nomor HP pada biodata wali sama dengan nomor Telegram yang dibagikan ke bot.
                </span>
            </div>
            </div>
        </article>
    </div>
</section>
@endsection
