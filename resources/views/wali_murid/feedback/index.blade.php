@extends('layouts.parent')

@php
    $title = 'Feedback dari Guru - TK Wonoayu';
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
            <span class="material-symbols-outlined text-[20px]">feedback</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Feedback dari Guru</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Catatan & Pesan untuk {{ $student->full_name }}</p>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">

    <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500 text-[18px]">chat</span> Pesan dari Guru
            </h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($feedbacks as $fb)
            <div class="p-6 hover:bg-slate-50 transition-colors">
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
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $fb->message }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <p class="text-[10px] font-bold text-slate-400">
                                Dari: {{ $fb->teacher->name }}
                            </p>
                            <p class="text-[10px] font-bold text-slate-400">
                                {{ $fb->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 flex flex-col items-center justify-center text-center opacity-60">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">chat_bubble_outline</span>
                <p class="text-xs font-bold text-slate-400">Belum ada feedback dari guru</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
