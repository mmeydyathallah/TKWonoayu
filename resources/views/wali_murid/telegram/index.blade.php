@extends('layouts.parent')

@section('content')
<section class="space-y-6">
    <header class="flex flex-col gap-2">
        <h1 class="text-2xl md:text-3xl font-headline font-black text-on-surface">Status Telegram Wali</h1>
        <p class="text-sm text-on-surface-variant">Pantau apakah bot Telegram sudah terhubung dengan nomor wali dan siswa yang benar.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article class="rounded-2xl border border-outline-variant/40 bg-surface-container p-5 space-y-4">
            <h2 class="text-lg font-headline font-bold text-on-surface">Status Koneksi</h2>

            <div class="flex items-center justify-between rounded-xl bg-surface-container-high p-4">
                <span class="text-sm font-semibold text-on-surface-variant">Koneksi Bot</span>
                <span class="text-xs px-3 py-1 rounded-full font-bold {{ $isConnected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $isConnected ? 'Terhubung' : 'Belum Terhubung' }}
                </span>
            </div>

            <div class="space-y-2 text-sm">
                <p><span class="font-semibold text-on-surface">Siswa:</span> {{ $student->full_name }}</p>
                <p><span class="font-semibold text-on-surface">Nomor Wali:</span> {{ $guardianPhoneNormalized ?? '-' }}</p>
                <p><span class="font-semibold text-on-surface">Chat ID:</span> {{ $telegramChat?->chat_id ?? '-' }}</p>
            </div>

            @if($isConnected)
                <div class="rounded-xl p-4 {{ $isSelectedForThisStudent ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                    @if($isSelectedForThisStudent)
                        <p class="text-sm font-semibold text-emerald-700">Siswa aktif sudah sesuai.</p>
                        <p class="text-xs text-emerald-700/80 mt-1">Notifikasi masuk/pulang akan dikirim untuk {{ $student->full_name }}.</p>
                    @else
                        <p class="text-sm font-semibold text-amber-700">Siswa aktif belum sesuai.</p>
                        <p class="text-xs text-amber-700/80 mt-1">
                            Bot saat ini terhubung ke siswa lain: {{ $selectedStudent?->full_name ?? 'tidak diketahui' }}.
                            Jalankan perintah <span class="font-bold">/siswa</span> lalu pilih {{ $student->full_name }}.
                        </p>
                    @endif
                </div>
            @endif
        </article>

        <article class="rounded-2xl border border-outline-variant/40 bg-surface-container p-5 space-y-4">
            <h2 class="text-lg font-headline font-bold text-on-surface">Instruksi Penggunaan</h2>
            <ol class="list-decimal pl-5 space-y-2 text-sm text-on-surface-variant">
                <li>Kirim perintah <span class="font-semibold text-on-surface">/hubungkan</span> ke bot Telegram.</li>
                <li>Tekan tombol kirim kontak yang muncul agar nomor HP tersinkron.</li>
                <li>Kirim perintah <span class="font-semibold text-on-surface">/siswa</span> untuk memilih anak yang dipantau.</li>
                <li>Kirim perintah <span class="font-semibold text-on-surface">/plan</span> untuk melihat panduan fitur bot.</li>
            </ol>

            <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-xs text-blue-700">
                Jika status masih belum terhubung, cek lagi apakah nomor HP pada biodata wali sama dengan nomor Telegram yang dibagikan ke bot.
            </div>
        </article>
    </div>
</section>
@endsection
