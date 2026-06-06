@extends('layouts.parent')

@php $title = 'Galeri Aktivitas - Portal Wali Murid TK Wonoayu'; @endphp

@section('styles')
<style>
    .gallery-modal {
        background: rgba(2, 6, 23, 0.86);
        backdrop-filter: blur(14px);
    }
</style>
@endsection

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
                    <button type="button"
                            class="group block w-full h-full"
                            onclick="openGalleryImage(@js($artwork->image_url), @js($artwork->title ?? 'Hasil Karya Anak'))"
                            aria-label="Lihat gambar penuh {{ $artwork->title }}">
                        <img alt="{{ $artwork->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" src="{{ $artwork->image_url }}"/>
                        <span class="absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-slate-950/70 px-3 py-1.5 text-xs font-black text-white shadow-sm backdrop-blur">
                            <span class="material-symbols-outlined text-[15px]">open_in_full</span>
                            Lihat Full
                        </span>
                    </button>
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

<div id="gallery-full-modal" class="gallery-modal fixed inset-0 z-50 hidden items-center justify-center p-4">
    <button type="button" class="absolute inset-0 cursor-default" onclick="closeGalleryImage()" aria-label="Tutup gambar"></button>
    <div class="relative z-10 flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-950 shadow-2xl">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
            <p id="gallery-full-title" class="truncate text-sm font-black text-white">Hasil Karya Anak</p>
            <button type="button" onclick="closeGalleryImage()" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" aria-label="Tutup">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="flex min-h-[60vh] items-center justify-center bg-black">
            <img id="gallery-full-image" src="" alt="" class="max-h-[82vh] w-full object-contain">
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openGalleryImage(src, title) {
    const modal = document.getElementById('gallery-full-modal');
    const image = document.getElementById('gallery-full-image');
    const titleEl = document.getElementById('gallery-full-title');
    if (!modal || !image || !titleEl) return;

    image.src = src;
    image.alt = title || 'Hasil Karya Anak';
    titleEl.textContent = title || 'Hasil Karya Anak';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeGalleryImage() {
    const modal = document.getElementById('gallery-full-modal');
    const image = document.getElementById('gallery-full-image');
    if (!modal || !image) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    image.src = '';
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeGalleryImage();
});
</script>
@endsection
