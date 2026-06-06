@extends('layouts.teacher')

@php
    $title = 'Penilaian Hasil Karya - TK Wonoayu';
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    
    /* Score radio buttons */
    .score-radio { display: none !important; }
    .score-pill {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 48px; height: 36px; padding: 0 12px;
        border-radius: 10px; font-size: 11px; font-weight: 900;
        cursor: pointer; transition: all 0.18s cubic-bezier(.4,0,.2,1);
        border: 1.5px solid #e2e8f0; letter-spacing: 0.03em;
        color: #94a3b8; background: #f8fafc;
        user-select: none;
    }
    .score-radio:checked + .score-pill.pill-BB { background:#fee2e2; color:#dc2626; border-color:#fca5a5; box-shadow: 0 2px 8px rgba(239,68,68,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-MB { background:#fef3c7; color:#d97706; border-color:#fcd34d; box-shadow: 0 2px 8px rgba(245,158,11,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-BSH { background:#dcfce7; color:#16a34a; border-color:#86efac; box-shadow: 0 2px 8px rgba(34,197,94,0.18); transform: scale(1.06); }
    .score-radio:checked + .score-pill.pill-BSB { background:#dbeafe; color:#2563eb; border-color:#93c5fd; box-shadow: 0 2px 8px rgba(59,130,246,0.18); transform: scale(1.06); }
    .score-pill:hover { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    
    /* File input styling */
    .file-input-wrapper { position: relative; overflow: hidden; display: inline-block; width: 100%; }
    .file-input-wrapper input[type=file] {
        font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; height: 100%;
    }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="assessment-header bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-6 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">palette</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Penilaian Hasil Karya</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Dokumentasi & Observasi Karya</p>
        </div>
    </div>
</header>

<div class="assessment-module max-w-7xl mx-auto w-full pb-20">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 flex flex-col gap-1 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl text-sm font-bold">
        @foreach($errors->all() as $error)
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
            {{ $error }}
        </div>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- LEFT COLUMN: FORM INPUT --}}
        <div class="lg:col-span-1 bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden sticky top-24">
            <div class="assessment-form-header assessment-form-header-artwork px-6 py-5 border-b border-slate-50 bg-gradient-to-b from-slate-50/50 to-white">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_photo_alternate</span> Tambah Hasil Karya
                </h3>
            </div>
            
            <form action="{{ route('guru.artworks.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" id="artwork-form">
                @csrf
                <input type="hidden" name="artwork_id" id="artwork_id">
                
                {{-- Student --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                        <select name="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Pilih Siswa...</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">Kel. {{ $student->class_group }} - {{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                {{-- Date --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Tanggal</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[18px]">calendar_month</span>
                        <input name="created_on" type="date" value="{{ $date->format('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                {{-- Activity --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Kegiatan Pembelajaran</label>
                    <input name="activity" type="text" placeholder="Contoh: Menggambar Pemandangan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium">
                </div>

                {{-- Aspect --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Aspek yang Diamati</label>
                    <textarea name="aspect" rows="2" placeholder="Contoh: Kerapian warna dan kreativitas bentuk" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium resize-none"></textarea>
                </div>

                {{-- Image Upload --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Foto Karya</label>
                    <div class="file-input-wrapper bg-slate-50 border border-slate-200 border-dashed rounded-xl p-4 text-center hover:bg-slate-100 transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined text-slate-400 text-3xl mb-1 group-hover:text-primary transition-colors">cloud_upload</span>
                        <p class="text-xs font-bold text-slate-600">Klik untuk unggah foto</p>
                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">Opsional, bisa ditambahkan saat edit · JPG/PNG (Maks 5MB)</p>
                        <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <div id="image-preview-container" class="hidden mt-3 relative rounded-xl overflow-hidden border border-slate-200">
                        <img id="image-preview" class="w-full h-32 object-contain bg-slate-100" src="#" alt="Preview">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-lg backdrop-blur-md">Ganti Foto</span>
                        </div>
                    </div>
                </div>

                {{-- Score --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Capaian Perkembangan</label>
                    <div class="flex items-center gap-2 w-full">
                        @foreach(['BB','MB','BSH','BSB'] as $lbl)
                        <label class="flex-1">
                            <input class="score-radio" type="radio" name="score_label" value="{{ $lbl }}" required>
                            <span class="score-pill pill-{{ $lbl }} w-full">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full gradient-primary text-white py-3.5 rounded-xl text-sm font-extrabold shadow-lg shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Hasil Karya
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: LIST OF RECORDS --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Filter Bar --}}
            <form action="{{ route('guru.artworks') }}" method="GET" id="filter-form" class="bg-white rounded-2xl border border-slate-100 ambient-shadow p-4 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px] space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Tanggal</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[16px]">calendar_today</span>
                        <input name="date" type="date" value="{{ $date->format('Y-m-d') }}" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>
                <div class="flex-1 min-w-[150px] space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelompok</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">groups</span>
                        <select name="group" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-8 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Semua Kelompok</option>
                            @foreach(['A','B'] as $g)
                            <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>Kelompok {{ $g }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                    </div>
                </div>
            </form>

            {{-- Records List (Grid Layout for Artworks) --}}
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">collections</span> Galeri Hasil Karya
                    </h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-200/50 px-2.5 py-1 rounded-md">{{ $artworks->count() }} Karya</span>
                </div>
                
                <div class="p-6">
                    @if($artworks->isEmpty())
                    <div class="py-16 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                            <span class="material-symbols-outlined text-3xl text-slate-300">image_not_supported</span>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Belum ada hasil karya</p>
                        <p class="text-[11px] font-medium text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk menambah dokumentasi.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($artworks as $artwork)
                        <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden hover:shadow-md transition-shadow group flex flex-col">
                            {{-- Image Container --}}
                            <div class="relative h-48 bg-slate-100 overflow-hidden">
                                @if($artwork->image_url)
                                <img src="{{ $artwork->image_url }}" class="w-full h-full object-contain transition-transform duration-500">
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2">image_not_supported</span>
                                    <span class="text-xs font-black uppercase tracking-widest">Belum ada foto</span>
                                    <span class="text-[10px] font-semibold mt-1">Edit untuk menambahkan gambar</span>
                                </div>
                                @endif
                                @php
                                    $score = $artwork->score_label;
                                    $badgeClass = match($score) {
                                        'BB' => 'bg-red-500 text-white',
                                        'MB' => 'bg-amber-500 text-white',
                                        'BSH' => 'bg-green-500 text-white',
                                        'BSB' => 'bg-blue-500 text-white',
                                        default => 'bg-slate-500 text-white',
                                    };
                                @endphp
                                <span class="absolute top-3 right-3 px-2 py-1 rounded-lg text-[10px] font-black shadow-lg {{ $badgeClass }}">
                                    {{ $score }}
                                </span>
                            </div>
                            
                            {{-- Details Container --}}
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-center gap-2.5 mb-3 border-b border-slate-200/60 pb-3">
                                    <img src="{{ $artwork->student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($artwork->student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}"
                                         class="w-8 h-8 rounded-full object-cover ring-2 ring-white">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $artwork->student->full_name }}</p>
                                        <p class="text-[9px] font-bold text-slate-500 mt-0.5">Kel. {{ $artwork->student->class_group }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 flex-1">
                                    <div>
                                        <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-0.5">Kegiatan</p>
                                        <p class="text-[11px] font-bold text-slate-700 leading-snug">{{ $artwork->title }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Aspek Diamati</p>
                                        <p class="text-[11px] text-slate-600 font-medium leading-relaxed">{{ $artwork->description }}</p>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 pt-3 border-t border-slate-200/60 flex justify-end gap-2">
                                    <button type="button" onclick="editArtwork({{ $artwork->toJson() }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <form action="{{ route('guru.artworks.destroy', $artwork->id) }}" method="POST" onsubmit="return confirm('Hapus hasil karya ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function editArtwork(data) {
        document.getElementById('artwork_id').value = data.id;
        document.getElementsByName('student_id')[0].value = data.student_id;
        
        if (data.created_on) {
            document.getElementsByName('created_on')[0].value = data.created_on.split('T')[0];
        }
        
        document.getElementsByName('activity')[0].value = data.title;
        document.getElementsByName('aspect')[0].value = data.description;
        
        const radio = document.querySelector(`input[name="score_label"][value="${data.score_label}"]`);
        if (radio) radio.checked = true;

        const fileInput = document.getElementsByName('image')[0];
        fileInput.value = '';
        fileInput.required = false;

        // Show existing image as preview
        if (data.image_url) {
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            previewImage.src = data.image_url;
            previewContainer.classList.remove('hidden');
        } else {
            document.getElementById('image-preview-container').classList.add('hidden');
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        const formTitle = document.querySelector('h3.font-extrabold');
        formTitle.innerHTML = '<span class="material-symbols-outlined text-amber-500 text-[20px]">edit</span> Edit Hasil Karya';
        formTitle.parentElement.classList.add('bg-amber-50');
    }
</script>
@endsection
