@extends('layouts.parent')

@section('styles')
<style>
    .telegram-page {
        --telegram-panel: rgba(15, 23, 42, 0.94);
        --telegram-panel-soft: rgba(15, 23, 42, 0.82);
        --telegram-border: rgba(148, 163, 184, 0.22);
        --telegram-border-soft: rgba(148, 163, 184, 0.18);
        --telegram-text: #e5eefb;
        --telegram-muted: #a9b8cc;
    }

    .telegram-page .telegram-panel {
        background: var(--telegram-panel) !important;
        border-color: var(--telegram-border) !important;
        box-shadow: 0 18px 42px rgba(2, 6, 23, 0.28) !important;
        color: var(--telegram-text) !important;
    }

    .telegram-page .telegram-panel-soft {
        background: var(--telegram-panel-soft) !important;
        border-color: var(--telegram-border-soft) !important;
        color: var(--telegram-text) !important;
    }

    .telegram-page .telegram-kv {
        background: rgba(30, 41, 59, 0.72) !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
    }

    .telegram-page .telegram-step {
        background: rgba(30, 41, 59, 0.58) !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        color: var(--telegram-text) !important;
    }

    .telegram-page .telegram-step-item {
        display: grid;
        grid-template-columns: 2rem 1fr;
        gap: 0.75rem;
        align-items: start;
        padding: 0.85rem 0.9rem;
        border-radius: 0.9rem;
        background: rgba(30, 41, 59, 0.58);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .telegram-page .telegram-step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 900;
        letter-spacing: 0.04em;
        color: #082f49;
        background: #7dd3fc;
        box-shadow: inset 0 0 0 1px rgba(14, 116, 144, 0.25);
    }

    .telegram-page .telegram-note {
        border-radius: 0.9rem;
        padding: 0.9rem 1rem;
        background: rgba(14, 116, 144, 0.14);
        border: 1px solid rgba(56, 189, 248, 0.28);
        color: #bae6fd;
    }

    .telegram-page .telegram-table-head {
        background: rgba(15, 23, 42, 0.92) !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.22) !important;
    }

    .telegram-page .telegram-table-row {
        background: rgba(15, 23, 42, 0.94) !important;
    }

    .telegram-page .telegram-table-row:hover {
        background: rgba(30, 41, 59, 0.88) !important;
    }
</style>
@endsection

@section('content')
<section class="telegram-page space-y-6">
    <header class="flex flex-col gap-2">
        <div class="inline-flex items-center gap-2 self-start rounded-full bg-primary/10 px-3 py-1 text-xs font-black uppercase tracking-widest text-primary">
            <span class="material-symbols-outlined text-[16px]">send</span>
            Telegram Wali
        </div>
        <h1 class="text-2xl md:text-3xl font-headline font-black text-on-surface">Status Telegram Wali</h1>
        <p class="text-sm text-on-surface-variant">Pantau apakah bot Telegram sudah terhubung dengan nomor wali dan siswa yang benar.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article class="telegram-panel card border shadow-sm rounded-2xl">
            <div class="card-body space-y-4">
                <h2 class="text-lg font-headline font-bold text-white">Status Koneksi</h2>

                <div class="telegram-kv flex items-center justify-between rounded-2xl p-4">
                    <span class="text-sm font-semibold text-slate-300">Koneksi Bot</span>
                    <span class="badge badge-sm font-bold {{ $isConnected ? 'badge-success' : 'badge-error' }}">
                        {{ $isConnected ? 'Terhubung' : 'Belum Terhubung' }}
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-slate-300">Siswa:</span> <span class="text-white font-bold">{{ $student->full_name }}</span></p>
                    <p><span class="font-semibold text-slate-300">Nomor Wali:</span> <span class="text-white font-mono">{{ $guardianPhoneNormalized ?? '-' }}</span></p>
                    <p><span class="font-semibold text-slate-300">Chat ID:</span> <span class="text-white font-mono">{{ $telegramChat?->chat_id ?? '-' }}</span></p>
                </div>

                @if($isConnected)
                    <div class="alert {{ $isSelectedForThisStudent ? 'alert-success' : 'alert-warning' }} rounded-2xl border-0">
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

        <article class="telegram-panel card border shadow-sm rounded-2xl">
            <div class="card-body space-y-4">
                <h2 class="text-lg font-headline font-bold text-white">Instruksi Penggunaan</h2>
                <div class="space-y-3 text-sm text-slate-300">
                    <div class="telegram-step-item">
                        <span class="telegram-step-number">01</span>
                        <p>Kirim perintah <span class="font-black text-white">/hubungkan</span> ke bot Telegram.</p>
                    </div>
                    <div class="telegram-step-item">
                        <span class="telegram-step-number">02</span>
                        <p>Tekan tombol kirim kontak yang muncul agar nomor HP tersinkron.</p>
                    </div>
                    <div class="telegram-step-item">
                        <span class="telegram-step-number">03</span>
                        <p>Kirim perintah <span class="font-black text-white">/siswa</span> untuk memilih anak yang dipantau.</p>
                    </div>
                    <div class="telegram-step-item">
                        <span class="telegram-step-number">04</span>
                        <p>Kirim perintah <span class="font-black text-white">/plan</span> untuk melihat panduan fitur bot.</p>
                    </div>
                </div>

                <div class="telegram-note text-xs font-semibold leading-relaxed">
                    Jika status masih belum terhubung, pastikan nomor HP pada biodata wali sama dengan nomor Telegram yang dibagikan ke bot.
                </div>
            </div>
        </article>
    </div>
</section>
@endsection
