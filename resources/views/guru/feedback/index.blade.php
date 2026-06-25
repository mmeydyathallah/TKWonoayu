@extends('layouts.teacher')

@php
    $title = 'Feedback - TK Wonoayu';
@endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
</style>
@endsection

@section('content')

<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">feedback</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Feedback ke Wali Murid</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kirim catatan perkembangan ke orang tua</p>
        </div>
    </div>
    <button onclick="openModal('addFeedbackModal')" class="gradient-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 hover:scale-105 transition-transform flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add_circle</span> Kirim Feedback
    </button>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-500 text-[20px]">check_circle</span>
            <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">chat</span> Riwayat Feedback
            </h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($feedbacks as $fb)
            <div class="p-6 hover:bg-slate-50 transition-colors group">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white
                        @if($fb->type === 'praise') bg-emerald-500
                        @elseif($fb->type === 'concern') bg-rose-500
                        @elseif($fb->type === 'reminder') bg-amber-500
                        @else bg-blue-500 @endif">
                        <span class="material-symbols-outlined text-[20px]">
                            @if($fb->type === 'praise') thumb_up
                            @elseif($fb->type === 'concern') warning
                            @elseif($fb->type === 'reminder') notifications
                            @else chat @endif
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-sm font-bold text-slate-800">{{ $fb->title }}</h5>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                @if($fb->type === 'praise') bg-emerald-50 text-emerald-600
                                @elseif($fb->type === 'concern') bg-rose-50 text-rose-600
                                @elseif($fb->type === 'reminder') bg-amber-50 text-amber-600
                                @else bg-blue-50 text-blue-600 @endif">
                                @if($fb->type === 'praise') Apresiasi
                                @elseif($fb->type === 'concern') Perhatian
                                @elseif($fb->type === 'reminder') Pengingat
                                @else Feedback @endif
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $fb->message }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <p class="text-[10px] font-bold text-slate-400">
                                Untuk: {{ $fb->student->full_name }} (Kel. {{ $fb->student->class_group }})
                            </p>
                            <p class="text-[10px] font-bold text-slate-400">
                                {{ $fb->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('guru.feedback.destroy', $fb->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus feedback ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-center opacity-60">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">chat_bubble_outline</span>
                <p class="text-xs font-bold text-slate-400">Belum ada feedback terkirim</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Add Feedback Modal --}}
<div id="addFeedbackModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('addFeedbackModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8">
        <h3 class="text-xl font-black text-slate-800 mb-6">Kirim Feedback ke Wali Murid</h3>
        <form action="{{ route('guru.feedback.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Siswa</label>
                <select name="student_id" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->full_name }} - Kel. {{ $s->class_group }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Judul</label>
                <input type="text" name="title" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all" placeholder="Misal: Perkembangan minggu ini">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Jenis</label>
                <select name="type" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                    <option value="feedback">Feedback Umum</option>
                    <option value="praise">Apresiasi</option>
                    <option value="concern">Perhatian</option>
                    <option value="reminder">Pengingat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Pesan</label>
                <textarea name="message" required rows="4" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all" placeholder="Tulis pesan untuk wali murid..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('addFeedbackModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Kirim Feedback</button>
            </div>
        </form>
    </div>
</div>

@endsection
