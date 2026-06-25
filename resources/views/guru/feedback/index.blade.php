@extends('layouts.teacher')

@php
    $title = 'Feedback dari Wali Murid - TK Wonoayu';
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
            <span class="material-symbols-outlined text-[20px]">chat</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Feedback dari Wali Murid</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Pesan & Balasan Orang Tua</p>
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

    <div class="space-y-4">
        @forelse($feedbacks as $fb)
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
            {{-- Parent Message --}}
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-violet-500 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr($fb->parent->name ?? 'W', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-sm font-bold text-slate-800">{{ $fb->parent->name ?? 'Wali Murid' }}</h5>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                @if($fb->type === 'praise') bg-emerald-50 text-emerald-600
                                @elseif($fb->type === 'concern') bg-rose-50 text-rose-600
                                @elseif($fb->type === 'reminder') bg-amber-50 text-amber-600
                                @else bg-blue-50 text-blue-600 @endif">
                                @if($fb->type === 'praise') Apresiasi
                                @elseif($fb->type === 'concern') Perhatian
                                @elseif($fb->type === 'reminder') Pengingat
                                @else Pesan @endif
                            </span>
                            <span class="text-[10px] font-bold text-slate-400">• {{ $fb->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-500 mb-1">Ananda: {{ $fb->student->full_name }} (Kel. {{ $fb->student->class_group }})</p>
                        <p class="text-sm font-bold text-slate-800 mb-2">{{ $fb->title }}</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $fb->message }}</p>
                    </div>
                    <form action="{{ route('guru.feedback.destroy', $fb->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Hapus pesan ini beserta balasannya?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Replies --}}
            @if($fb->replies->count())
            <div class="border-t border-slate-100 bg-slate-50/50">
                @foreach($fb->replies->sortBy('created_at') as $reply)
                <div class="px-6 py-4 flex items-start gap-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs shrink-0">
                        {{ strtoupper(substr($reply->teacher->name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-xs font-bold text-slate-800">{{ $reply->teacher->name ?? 'Guru' }}</h5>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-blue-50 text-blue-600">Balasan</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $reply->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Reply Form --}}
            <div class="border-t border-slate-100 p-4">
                <form action="{{ route('guru.feedback.store') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="reply_to" value="{{ $fb->id }}">
                    <input type="text" name="message" required placeholder="Tulis balasan..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <button type="submit" class="gradient-primary text-white px-5 py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 hover:scale-105 transition-transform shrink-0">
                        Balas
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-8 flex flex-col items-center justify-center text-center opacity-60">
            <span class="material-symbols-outlined text-5xl text-slate-300 mb-2">chat_bubble_outline</span>
            <p class="text-sm font-bold text-slate-400">Belum ada pesan dari wali murid</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
