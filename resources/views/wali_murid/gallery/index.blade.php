@extends('layouts.parent')

@php $title = 'Galeri Aktivitas - Portal Wali Murid TK Wonoayu'; @endphp

@section('content')
{{-- Header --}}
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        @if($student)
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary-container text-on-secondary-container text-sm font-bold mb-4">
            <span class="material-symbols-outlined text-[18px]">star</span>
            Portofolio {{ $student->nickname ?? $student->full_name }}
        </div>
        @endif
        <h1 class="font-headline text-4xl md:text-5xl font-extrabold text-on-surface tracking-tight mb-2">Galeri & Catatan</h1>
        <p class="text-on-surface-variant text-lg max-w-2xl">Kumpulan karya kreatif dan momen berharga perkembangan ananda di sekolah.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    {{-- Left: Artwork Gallery --}}
    <div class="xl:col-span-2 space-y-8">
        <h2 class="font-headline text-2xl font-bold flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-primary-container text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">brush</span>
            </span>
            Hasil Karya Anak
        </h2>
        @if($artworks->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($artworks as $artwork)
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_30px_rgba(44,52,55,0.04)] overflow-hidden flex flex-col">
                <div class="h-48 relative overflow-hidden bg-surface-variant">
                    @if($artwork->image_url)
                    <img alt="{{ $artwork->title }}" class="w-full h-full object-cover" src="{{ $artwork->image_url }}"/>
                    @else
                    <div class="w-full h-full flex items-center justify-center text-on-surface-variant/40">
                        <span class="material-symbols-outlined text-5xl">image_not_supported</span>
                    </div>
                    @endif
                    @if($artwork->created_on)
                    <div class="absolute top-4 right-4 bg-surface-container-lowest/80 backdrop-blur-md px-3 py-1.5 rounded-full text-xs font-bold text-primary flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                        {{ $artwork->created_on->format('d M Y') }}
                    </div>
                    @endif
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="font-headline font-bold text-xl mb-2 text-on-surface">{{ $artwork->title }}</h3>
                    @if($artwork->description)
                    <p class="text-sm text-on-surface-variant mb-4 flex-1">{{ $artwork->description }}</p>
                    @endif
                    @if($artwork->score_label)
                    <div class="bg-surface-container-low p-3 rounded-xl border border-outline-variant/15 mt-auto">
                        <span class="text-xs font-bold text-on-surface-variant">Penilaian:</span>
                        <span class="ml-2 px-3 py-1 bg-surface-container-lowest rounded-full text-xs font-bold text-primary shadow-sm">{{ $artwork->score_label }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-surface-container-low rounded-xl p-12 flex flex-col items-center justify-center text-center border border-dashed border-outline-variant/30">
            <span class="material-symbols-outlined text-5xl text-outline-variant/40 mb-3">photo_library</span>
            <p class="font-bold text-on-surface">Belum Ada Karya</p>
            <p class="text-sm text-on-surface-variant mt-1">Hasil karya ananda akan muncul di sini.</p>
        </div>
        @endif
    </div>

    {{-- Right: Anecdotal Notes --}}
    <div class="xl:col-span-1 space-y-8">
        <h2 class="font-headline text-2xl font-bold flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-tertiary-container text-on-tertiary-container flex items-center justify-center">
                <span class="material-symbols-outlined">menu_book</span>
            </span>
            Catatan Anekdot
        </h2>
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-[0_8px_30px_rgba(44,52,55,0.04)]">
            @if($notes->isNotEmpty())
            <div class="relative border-l-2 border-surface-container-high ml-4 space-y-8 pb-4">
                @foreach($notes as $note)
                <div class="relative pl-6">
                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-secondary ring-4 ring-surface-container-lowest"></div>
                    <div class="flex flex-col gap-1 mb-2">
                        <span class="text-xs font-bold text-secondary uppercase tracking-wider">{{ $note->recorded_at->format('d F Y') }}</span>
                        <h4 class="font-headline font-bold text-lg text-on-surface">{{ $note->tone ?? 'Catatan' }}</h4>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/15">
                        @if($note->location)
                        <div class="flex items-center gap-2 text-xs text-on-surface-variant font-bold mb-2">
                            <span class="material-symbols-outlined text-[16px]">location_on</span> {{ $note->location }}
                        </div>
                        @endif
                        <p class="text-sm text-on-surface">{{ $note->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl text-outline-variant/40 mb-2 block">history_edu</span>
                <p class="text-sm italic">Belum ada catatan anekdot tersedia.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection