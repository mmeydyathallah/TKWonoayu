@extends('layouts.parent')

@php
    $title = 'Feedback ke Guru - TK Wonoayu';
@endphp

@section('styles')
<style>
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
</style>
@endsection

@section('content')

<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center px-6 py-4 w-full -mx-8 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">chat</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Feedback ke Guru</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kirim Pesan & Lihat Balasan untuk {{ $student->full_name }}</p>
        </div>
    </div>
</header>

<div class="max-w-4xl mx-auto w-full pb-20">

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-500 text-[20px]">check_circle</span>
            <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Form Kirim Feedback --}}
    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6 mb-6">
        <h3 class="font-headline text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[22px]">edit</span> Kirim Pesan ke Guru
        </h3>
        <form action="{{ route('wali.feedback.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Judul</label>
                <input type="text" name="title" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" placeholder="Misal: Perkembangan anak minggu ini">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Jenis</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all">
                    <option value="feedback">Pesan Umum</option>
                    <option value="praise">Apresiasi</option>
                    <option value="concern">Keluhan</option>
                    <option value="reminder">Pengingat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Pesan</label>
                <textarea name="message" required rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all" placeholder="Tulis pesan untuk guru..."></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-2xl font-black text-sm shadow-lg shadow-blue-500/20 hover:scale-[1.01] transition-all">
                Kirim Pesan
            </button>
        </form>
    </div>

    {{-- Riwayat Feedback --}}
    <div class="space-y-4">
        @forelse($topLevel as $fb)
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
            {{-- My Message --}}
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-violet-500 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</h5>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                @if($fb->type === 'praise') bg-emerald-50 text-emerald-600
                                @elseif($fb->type === 'concern') bg-rose-50 text-rose-600
                                @elseif($fb->type === 'reminder') bg-amber-50 text-amber-600
                                @else bg-blue-50 text-blue-600 @endif">
                                @if($fb->type === 'praise') Apresiasi
                                @elseif($fb->type === 'concern') Keluhan
                                @elseif($fb->type === 'reminder') Pengingat
                                @else Pesan @endif
                            </span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $fb->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm font-bold text-slate-800 mb-2">{{ $fb->title }}</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $fb->message }}</p>
                    </div>
                </div>
            </div>

            {{-- Teacher Replies --}}
            @if(isset($replies[$fb->id]) && $replies[$fb->id]->count())
            <div class="border-t border-slate-100 bg-blue-50/30">
                @foreach($replies[$fb->id]->sortBy('created_at') as $reply)
                <div class="px-6 py-4 flex items-start gap-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs shrink-0">
                        {{ strtoupper(substr($reply->teacher->name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-xs font-bold text-slate-800">{{ $reply->teacher->name ?? 'Guru' }}</h5>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-blue-100 text-blue-700">Balasan Guru</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $reply->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="border-t border-slate-100 px-6 py-3 bg-slate-50/50">
                <p class="text-xs font-bold text-slate-400 italic">Menunggu balasan guru...</p>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8 flex flex-col items-center justify-center text-center opacity-60">
            <span class="material-symbols-outlined text-5xl text-slate-300 mb-2">chat_bubble_outline</span>
            <p class="text-sm font-bold text-slate-400">Belum ada pesan. Kirim pesan pertama ke guru!</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
