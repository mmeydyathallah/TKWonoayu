@extends('layouts.parent')

@php $title = 'Galeri Aktivitas - Portal Wali Murid TK Wonoayu'; @endphp

@section('styles')
<style>
    .gallery-modal {
        background: rgba(2, 6, 23, 0.86);
        backdrop-filter: blur(14px);
    }

    .gallery-image {
        object-fit: contain;
        background: rgba(15, 23, 42, .92);
    }
</style>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        @if($student)
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary-container text-on-secondary-container text-sm font-bold mb-4">
            <span class="material-symbols-outlined text-[18px]">photo_library</span>
            Galeri {{ $student->nickname ?? $student->full_name }}
        </div>
        @endif
        <h1 class="font-headline text-4xl md:text-5xl font-extrabold text-on-surface tracking-tight mb-2">Galeri & Catatan</h1>
        <p class="text-on-surface-variant text-lg max-w-2xl">Foto kegiatan dari penilaian harian beserta penjelasan perkembangan ananda.</p>
    </div>
</div>

@if($galleryItems->isNotEmpty())
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($galleryItems as $item)
    <article class="bg-surface-container-lowest rounded-2xl shadow-[0_8px_30px_rgba(44,52,55,0.05)] overflow-hidden border border-outline-variant/15 flex flex-col">
        <div class="aspect-[4/3] relative overflow-hidden bg-slate-950">
            <button type="button"
                    class="group block w-full h-full"
                    onclick="openGalleryImage(@js($item->image_url), @js($item->title))"
                    aria-label="Lihat gambar penuh {{ $item->title }}">
                <img alt="{{ $item->title }}" class="gallery-image w-full h-full transition-transform duration-300 group-hover:scale-[1.02]" src="{{ $item->image_url }}">
                <span class="absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-slate-950/75 px-3 py-1.5 text-xs font-black text-white shadow-sm backdrop-blur">
                    <span class="material-symbols-outlined text-[15px]">open_in_full</span>
                    Lihat Full
                </span>
            </button>
            <div class="absolute top-4 right-4 bg-surface-container-lowest/85 backdrop-blur-md px-3 py-1.5 rounded-full text-xs font-bold text-primary flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                {{ $item->assessed_on?->translatedFormat('d M Y') }}
            </div>
        </div>
        <div class="p-5 flex-1 flex flex-col">
            <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-black text-primary uppercase tracking-widest">{{ $item->domain_short }}</p>
                    <h2 class="font-headline font-black text-lg text-on-surface leading-snug mt-1">{{ $item->title }}</h2>
                </div>
            </div>
            <div class="rounded-2xl bg-surface-container-low p-4 border border-outline-variant/15 flex-1">
                <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Aspek</p>
                <p class="text-sm font-bold text-on-surface mb-3">{{ $item->domain_label }}</p>
                <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Penjelasan</p>
                <p class="text-sm text-on-surface-variant leading-relaxed">{{ $item->explanation ?: 'Belum ada penjelasan tambahan dari guru.' }}</p>
            </div>
        </div>
    </article>
    @endforeach
</div>
@else
<div class="bg-surface-container-low rounded-2xl p-12 flex flex-col items-center justify-center text-center border border-dashed border-outline-variant/30">
    <span class="material-symbols-outlined text-5xl text-outline-variant/40 mb-3">photo_library</span>
    <p class="font-black text-on-surface">Belum Ada Foto Kegiatan</p>
    <p class="text-sm text-on-surface-variant mt-1 max-w-md">Foto akan muncul setelah guru mengisi Penilaian Harian dan mengunggah foto untuk anak ini.</p>
</div>
@endif

<div id="gallery-full-modal" class="gallery-modal fixed inset-0 z-50 hidden items-center justify-center p-4">
    <button type="button" class="absolute inset-0 cursor-default" onclick="closeGalleryImage()" aria-label="Tutup gambar"></button>
    <div class="relative z-10 flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-950 shadow-2xl">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
            <p id="gallery-full-title" class="truncate text-sm font-black text-white">Foto Kegiatan</p>
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
    image.alt = title || 'Foto Kegiatan';
    titleEl.textContent = title || 'Foto Kegiatan';
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
