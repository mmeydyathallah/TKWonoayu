@extends('layouts.teacher')

@php
    $title = 'Formulir Biodata Siswa - TK Wonoayu';
@endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 10px 32px rgba(44, 52, 55, 0.06); }
    .ghost-border { border: 1px solid rgba(172, 179, 183, 0.15); }
</style>
@endsection

@section('content')
<!-- TopAppBar -->
<header class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl docked full-width top-0 sticky z-30 no-border shadow-sm shadow-xl shadow-blue-900/5 flex justify-between items-center px-8 py-3 w-full -mx-8 mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('guru.students.index') }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
            <span class="material-symbols-outlined text-blue-800 dark:text-blue-300">arrow_back</span>
        </a>
        <h1 class="text-xl font-bold tracking-tight text-blue-800 dark:text-blue-300 font-headline hidden sm:block">Tambah Siswa Baru</h1>
    </div>
    <!-- Search Bar -->
    <div class="flex-1 max-w-md ml-auto mr-4 hidden md:block">
        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">search</span>
            <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-sm rounded-full pl-11 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/20 border border-transparent focus:border-primary/30 transition-all font-body text-on-surface placeholder:text-on-surface-variant/70" placeholder="Cari data siswa..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400">
        <button class="p-2 hover:scale-105 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all scale-98 active:scale-95 duration-200 rounded-full relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full border-2 border-white"></span>
        </button>
        <button class="p-2 hover:scale-105 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all scale-98 active:scale-95 duration-200 rounded-full">
            <span class="material-symbols-outlined">account_circle</span>
        </button>
    </div>
</header>

<!-- Form Content Canvas -->
<div class="max-w-7xl mx-auto w-full">
    <!-- Hero Header -->
    <div class="mb-12 relative">
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-primary-container/30 rounded-full blur-3xl -z-10"></div>
        <div class="absolute top-10 right-10 w-40 h-40 bg-tertiary-container/20 rounded-full blur-3xl -z-10"></div>
        <h2 class="text-4xl md:text-5xl font-extrabold font-headline text-on-surface tracking-tight mb-3">Biodata Siswa</h2>
        <p class="text-on-surface-variant font-body text-lg max-w-2xl">Masukkan detail siswa yang komprehensif untuk memelihara catatan administrasi yang akurat.</p>
    </div>
    @if (session('success'))
    <div class="mb-6 rounded-xl bg-secondary-container/30 px-4 py-3 text-sm font-semibold text-on-secondary-container">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="mb-6 rounded-xl bg-error-container/20 px-4 py-3 text-sm font-semibold text-on-error-container">
        {{ session('error') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-100 px-5 py-4 text-sm font-semibold text-rose-700">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span>Data belum bisa disimpan.</span>
        </div>
        <ul class="list-disc pl-6 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form id="studentCreateForm" action="{{ route('guru.students.store') }}" class="space-y-8 md:space-y-12" method="post" enctype="multipart/form-data">
        @csrf
        @if(session('duplicate_confirmation_notices') && count(session('duplicate_confirmation_notices')))
        <div class="rounded-xl bg-amber-50 border border-amber-200 px-5 py-4 text-sm text-amber-900">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <span class="material-symbols-outlined text-[18px]">warning</span>
                <span>Ditemukan data yang sama.</span>
            </div>
            <p class="mb-3 font-medium">Periksa dulu data berikut. Jika memang benar, klik Tetap Simpan.</p>
            <ul class="list-disc pl-6 space-y-1 mb-4">
                @foreach(session('duplicate_confirmation_notices') as $notice)
                    <li>{{ $notice }}</li>
                @endforeach
            </ul>
            <div class="flex flex-wrap gap-3">
                <button class="px-5 py-2 rounded-full bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors" type="submit" name="confirm_duplicate_save" value="1">
                    Tetap Simpan
                </button>
                <a href="#studentCreateForm" class="px-5 py-2 rounded-full border border-amber-300 text-amber-800 font-bold hover:bg-amber-100 transition-colors">
                    Tidak, Periksa Lagi
                </a>
            </div>
            <p class="mt-3 text-xs text-amber-700">Jika mengunggah foto atau mengisi password wali, pilih/isi ulang sebelum klik Tetap Simpan.</p>
        </div>
        @endif
        <!-- Section 1: Basic Info -->
        <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-8 ghost-border ambient-shadow relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-secondary-container/20 rounded-bl-[4rem] -z-10"></div>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <h3 class="text-2xl font-bold font-headline text-on-surface">1. Informasi Dasar</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Input Field Component -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">No Induk</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="student_no" value="{{ old('student_no') }}" placeholder="Contoh: 12345" required="" type="text"/>
                    @error('student_no')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NISN</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nisn" value="{{ old('nisn') }}" placeholder="Contoh: 0123456789" type="text"/>
                    @error('nisn')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Kode RFID</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">nfc</span>
                        <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT pl-12 pr-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm uppercase" name="rfid_code" value="{{ old('rfid_code') }}" placeholder="Contoh: 04A1B2C3D4" type="text"/>
                    </div>
                    <p class="text-[10px] text-on-surface-variant/70 ml-1">Isi UID kartu dari alat PN532. Spasi, titik dua, dan strip akan dinormalisasi otomatis.</p>
                    @error('rfid_code')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Kelompok (Group)</label>
                    <div class="relative">
                        <select class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 appearance-none outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm cursor-pointer" name="class_group" required="">
                            <option disabled="" {{ old('class_group') ? '' : 'selected' }} value="">Pilih Kelompok</option>
                            <option value="A" {{ old('class_group') === 'A' ? 'selected' : '' }}>Kelompok A</option>
                            <option value="B" {{ old('class_group') === 'B' ? 'selected' : '' }}>Kelompok B</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tahun Pelajaran</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="school_year" value="{{ old('school_year') }}" placeholder="Contoh: 2025/2026" required="" type="text"/>
                </div>
            </div>
        </section>
        <!-- Section 2: Child Identity -->
        <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-8 ghost-border ambient-shadow">
            <div class="flex items-center gap-4 mb-8 border-b border-outline-variant/10 pb-6">
                <div class="w-12 h-12 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container">
                    <span class="material-symbols-outlined">child_care</span>
                </div>
                <h3 class="text-2xl font-bold font-headline text-on-surface">2. Identitas Anak</h3>
            </div>
            
            <!-- Profile Photo Upload -->
            <div class="mb-8 flex flex-col md:flex-row items-center gap-6">
                <div class="w-32 h-32 rounded-full bg-surface-container-high ghost-border flex items-center justify-center overflow-hidden relative group">
                    <span class="material-symbols-outlined text-4xl text-outline-variant" id="avatarIcon">account_circle</span>
                    <img id="avatarPreview" class="w-full h-full object-cover hidden" />
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">photo_camera</span>
                    </div>
                </div>
                <div class="space-y-2 flex-1">
                    <label class="block text-sm font-bold text-on-surface ml-1">Foto Profil Siswa</label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" data-preview-target="avatarPreview" data-icon-target="avatarIcon" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary-container/80 transition-all" onchange="openAvatarCropper(this)"/>
                    <p class="text-xs text-on-surface-variant/70 ml-1">Format: JPG, PNG. Maksimal 2MB.</p>
                </div>
            </div>

            @include('guru.students.partials.avatar-cropper')

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="space-y-2 md:col-span-8">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Lengkap</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="full_name" value="{{ old('full_name') }}" placeholder="Nama lengkap resmi" required="" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Panggilan</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nickname" value="{{ old('nickname') }}" placeholder="Nama panggilan" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-6">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK (Nomor Induk Kependudukan)</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK" type="text"/>
                    @error('nik')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2 md:col-span-3">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tempat Lahir</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="birth_place" value="{{ old('birth_place') }}" placeholder="Kota/Kabupaten" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-3">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tanggal Lahir</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="birth_date" value="{{ old('birth_date') }}" type="date"/>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Jenis Kelamin</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input class="w-5 h-5 text-primary border-outline-variant focus:ring-primary/20 rounded-full cursor-pointer" name="gender" type="radio" value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'checked' : '' }}/>
                            <span class="text-on-surface font-medium group-hover:text-primary transition-colors">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input class="w-5 h-5 text-primary border-outline-variant focus:ring-primary/20 rounded-full cursor-pointer" name="gender" type="radio" value="Perempuan" {{ old('gender') === 'Perempuan' ? 'checked' : '' }}/>
                            <span class="text-on-surface font-medium group-hover:text-primary transition-colors">Perempuan</span>
                        </label>
                    </div>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Agama</label>
                    <div class="relative">
                        <select class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 appearance-none outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm cursor-pointer" name="religion">
                            <option disabled="" {{ old('religion') ? '' : 'selected' }} value="">Pilih Agama</option>
                            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $religion)
                                <option value="{{ $religion }}" {{ old('religion') === $religion ? 'selected' : '' }}>{{ $religion }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Info Saudara (Anak Ke)</label>
                    <div class="flex items-center gap-2">
                        <input class="w-20 bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-3 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm text-center" name="sibling_order" value="{{ old('sibling_order') }}" placeholder="Ke" type="number"/>
                        <span class="text-on-surface-variant font-medium">dari</span>
                        <input class="w-20 bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-3 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm text-center" name="siblings_total" value="{{ old('siblings_total') }}" placeholder="Total" type="number"/>
                        <span class="text-on-surface-variant font-medium">bersaudara</span>
                    </div>
                </div>

                <div class="md:col-span-12 space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Alamat Lengkap</label>
                    <textarea class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-xl px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="address" rows="3" placeholder="Alamat rumah lengkap">{{ old('address') }}</textarea>
                </div>
                <div class="md:col-span-12 space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nomor Telepon (WhatsApp)</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="phone_number" value="{{ old('phone_number') }}" placeholder="Contoh: 08123456789" type="text"/>
                </div>
            </div>
        </section>
        <!-- Section 3: Parent Identity -->
        <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-8 ghost-border ambient-shadow">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined">family_restroom</span>
                </div>
                <h3 class="text-2xl font-bold font-headline text-on-surface">3. Identitas Orang Tua</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Wali Murid</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="guardian_name" value="{{ old('guardian_name') }}" required="" type="text"/>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nomor HP Wali</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="guardian_phone" value="{{ old('guardian_phone') }}" type="text"/>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Father Info Card -->
                <div class="bg-surface-container-lowest rounded-xl p-6 ghost-border relative">
                    <div class="absolute top-4 right-4 text-outline-variant/30">
                        <span class="material-symbols-outlined text-4xl">face</span>
                    </div>
                    <h4 class="text-lg font-bold font-headline text-primary mb-6">Informasi Ayah</h4>
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Lengkap</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_name" value="{{ old('father_name') }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_nik" value="{{ old('father_nik') }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Pekerjaan</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_job" value="{{ old('father_job') }}" type="text"/>
                        </div>
                    </div>
                </div>
                <!-- Mother Info Card -->
                <div class="bg-surface-container-lowest rounded-xl p-6 ghost-border relative">
                    <div class="absolute top-4 right-4 text-outline-variant/30">
                        <span class="material-symbols-outlined text-4xl">face_3</span>
                    </div>
                    <h4 class="text-lg font-bold font-headline text-secondary mb-6">Informasi Ibu</h4>
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Lengkap</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_name" value="{{ old('mother_name') }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_nik" value="{{ old('mother_nik') }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Pekerjaan</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_job" value="{{ old('mother_job') }}" type="text"/>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Section 4: Akun Wali Murid -->
        <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-8 ghost-border ambient-shadow relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-bl-[4rem] -z-10"></div>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <span class="material-symbols-outlined">vpn_key</span>
                </div>
                <h3 class="text-2xl font-bold font-headline text-on-surface">4. Akun Login Wali Murid</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Username / Email Login</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                            <input class="w-full bg-white text-on-surface rounded-DEFAULT px-12 py-3 outline-none transition-all ghost-border focus:border-emerald-500 border-transparent shadow-sm" 
                                   name="parent_email" 
                                   value="{{ old('parent_email') }}"
                                   placeholder="Contoh: wali.12345" 
                                   type="text"/>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium ml-1 italic">* Digunakan oleh wali murid untuk login.</p>
                        @error('parent_email')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input class="w-full bg-white text-on-surface rounded-DEFAULT px-12 py-3 outline-none transition-all ghost-border focus:border-emerald-500 border-transparent shadow-sm" 
                                   name="parent_password" 
                                   placeholder="Set password awal" 
                                   type="password"/>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100/50">
                    <h4 class="text-emerald-800 font-bold text-sm mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">info</span> Petunjuk Akun
                    </h4>
                    <p class="text-xs text-emerald-700 font-medium leading-relaxed">
                        Akun ini akan otomatis terbuat saat Anda menyimpan data siswa. Wali murid dapat menggunakan email/username dan password ini untuk masuk ke portal perkembangan anak.
                    </p>
                </div>
            </div>
        </section>
        <!-- Save Actions -->
        <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/20 mt-12">
            <a href="{{ route('guru.students.index') }}" class="px-8 py-3 rounded-full font-bold text-primary ghost-border hover:bg-surface-container-high hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center justify-center">
                Batal
            </a>
            <button class="px-10 py-3 rounded-full font-bold text-white gradient-primary shadow-lg shadow-primary/30 hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center gap-2" type="submit">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
