@extends('layouts.teacher')

@php
    $title = 'Edit Biodata Siswa - TK Wonoayu';
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
        <h1 class="text-xl font-bold tracking-tight text-blue-800 dark:text-blue-300 font-headline hidden sm:block">Edit Data Siswa</h1>
    </div>
    <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400">
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
        <h2 class="text-4xl md:text-5xl font-extrabold font-headline text-on-surface tracking-tight mb-3">Edit Biodata</h2>
        <p class="text-on-surface-variant font-body text-lg max-w-2xl">Perbarui informasi untuk <strong>{{ $student->full_name }}</strong>. Pastikan semua data tetap akurat.</p>
    </div>

    <form action="{{ route('guru.students.update', $student) }}" class="space-y-8 md:space-y-12" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">No Induk</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="student_no" value="{{ $student->student_no }}" required="" type="text"/>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NISN</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nisn" value="{{ $student->nisn }}" type="text"/>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Kelompok (Group)</label>
                    <div class="relative">
                        <select class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 appearance-none outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm cursor-pointer" name="class_group" required="">
                            @foreach(['A1', 'A2', 'B1', 'B2'] as $grp)
                                <option value="{{ $grp }}" {{ $student->class_group == $grp ? 'selected' : '' }}>Kelompok {{ $grp }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tahun Pelajaran</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="school_year" value="{{ $student->school_year }}" required="" type="text"/>
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
                    @if($student->avatar_url)
                        <img id="avatarPreview" src="{{ $student->avatar_url }}" class="w-full h-full object-cover" />
                    @else
                        <span class="material-symbols-outlined text-4xl text-outline-variant" id="avatarIcon">account_circle</span>
                        <img id="avatarPreview" class="w-full h-full object-cover hidden" />
                    @endif
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">photo_camera</span>
                    </div>
                </div>
                <div class="space-y-2 flex-1">
                    <label class="block text-sm font-bold text-on-surface ml-1">Foto Profil Siswa</label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary-container/80 transition-all" onchange="previewImage(this)"/>
                    <p class="text-xs text-on-surface-variant/70 ml-1">Kosongkan jika tidak ingin mengganti foto.</p>
                </div>
            </div>

            <script>
                function previewImage(input) {
                    const icon = document.getElementById('avatarIcon');
                    const preview = document.getElementById('avatarPreview');
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            if(icon) icon.classList.add('hidden');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="space-y-2 md:col-span-8">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Lengkap</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="full_name" value="{{ $student->full_name }}" required="" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nama Panggilan</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nickname" value="{{ $student->nickname }}" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-6">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK (Nomor Induk Kependudukan)</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="nik" value="{{ $student->nik }}" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-3">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tempat Lahir</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="birth_place" value="{{ $student->birth_place }}" type="text"/>
                </div>
                <div class="space-y-2 md:col-span-3">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Tanggal Lahir</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="birth_date" value="{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '' }}" type="date"/>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Jenis Kelamin</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input class="w-5 h-5 text-primary border-outline-variant focus:ring-primary/20 rounded-full cursor-pointer" name="gender" type="radio" value="Laki-laki" {{ $student->gender == 'Laki-laki' ? 'checked' : '' }}/>
                            <span class="text-on-surface font-medium group-hover:text-primary transition-colors">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input class="w-5 h-5 text-primary border-outline-variant focus:ring-primary/20 rounded-full cursor-pointer" name="gender" type="radio" value="Perempuan" {{ $student->gender == 'Perempuan' ? 'checked' : '' }}/>
                            <span class="text-on-surface font-medium group-hover:text-primary transition-colors">Perempuan</span>
                        </label>
                    </div>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Agama</label>
                    <div class="relative">
                        <select class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 appearance-none outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm cursor-pointer" name="religion">
                            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                                <option value="{{ $rel }}" {{ $student->religion == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="space-y-2 md:col-span-4">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Info Saudara (Anak Ke)</label>
                    <div class="flex items-center gap-2">
                        <input class="w-20 bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-3 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm text-center" name="sibling_order" value="{{ $student->sibling_order }}" placeholder="Ke" type="number"/>
                        <span class="text-on-surface-variant font-medium">dari</span>
                        <input class="w-20 bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-3 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm text-center" name="siblings_total" value="{{ $student->siblings_total }}" placeholder="Total" type="number"/>
                        <span class="text-on-surface-variant font-medium">bersaudara</span>
                    </div>
                </div>

                <div class="md:col-span-12 space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Alamat Lengkap</label>
                    <textarea class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-xl px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="address" rows="3" placeholder="Alamat rumah lengkap">{{ $student->address }}</textarea>
                </div>
                <div class="md:col-span-12 space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nomor Telepon (WhatsApp)</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="phone_number" value="{{ $student->phone_number }}" placeholder="Contoh: 08123456789" type="text"/>
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
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="guardian_name" value="{{ $student->parentProfile?->guardian_name }}" required="" type="text"/>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Nomor HP Wali</label>
                    <input class="w-full bg-surface-container-high focus:bg-surface-container-lowest text-on-surface rounded-DEFAULT px-4 py-3 outline-none transition-all ghost-border focus:border-primary border-transparent shadow-sm" name="guardian_phone" value="{{ $student->parentProfile?->guardian_phone }}" type="text"/>
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
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_name" value="{{ $student->parentProfile?->father_name }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_nik" value="{{ $student->parentProfile?->father_nik }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Pekerjaan</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="father_job" value="{{ $student->parentProfile?->father_job }}" type="text"/>
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
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_name" value="{{ $student->parentProfile?->mother_name }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">NIK</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_nik" value="{{ $student->parentProfile?->mother_nik }}" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Pekerjaan</label>
                            <input class="w-full bg-surface-container focus:bg-white text-on-surface rounded-DEFAULT px-4 py-2.5 outline-none transition-all ghost-border focus:border-primary border-transparent" name="mother_job" value="{{ $student->parentProfile?->mother_job }}" type="text"/>
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

            @if(session('success_account'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span> {{ session('success_account') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Username / Email Login</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                            <input class="w-full bg-white text-on-surface rounded-DEFAULT px-12 py-3 outline-none transition-all ghost-border focus:border-emerald-500 border-transparent shadow-sm" 
                                   name="parent_email" 
                                   value="{{ $student->user?->email ?? 'wali.'.$student->student_no.'@tkwonoayu.com' }}" 
                                   placeholder="Contoh: wali.12345" 
                                   type="text"/>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium ml-1 italic">* Digunakan untuk login ke portal wali murid.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-on-surface-variant ml-1 font-body">Password Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input class="w-full bg-white text-on-surface rounded-DEFAULT px-12 py-3 outline-none transition-all ghost-border focus:border-emerald-500 border-transparent shadow-sm" 
                                   name="parent_password" 
                                   placeholder="{{ $student->user_id ? 'Isi untuk ganti password' : 'Set password awal' }}" 
                                   type="password"/>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100/50">
                    <h4 class="text-emerald-800 font-bold text-sm mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">info</span> Petunjuk Akun
                    </h4>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-xs text-emerald-700 font-medium">
                            <span class="text-emerald-500">•</span>
                            <span>Satu siswa hanya memiliki satu akun akses untuk wali murid.</span>
                        </li>
                        <li class="flex items-start gap-2 text-xs text-emerald-700 font-medium">
                            <span class="text-emerald-500">•</span>
                            <span>Wali murid dapat melihat semua perkembangan anak (Harian, Ceklis, Hasil Karya, dll) melalui portal khusus.</span>
                        </li>
                        <li class="flex items-start gap-2 text-xs text-emerald-700 font-medium">
                            <span class="text-emerald-500">•</span>
                            <span>Jika password dikosongkan saat edit, password lama tetap berlaku.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Update Actions -->
        <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/20 mt-12">
            <a href="{{ route('guru.students.index') }}" class="px-8 py-3 rounded-full font-bold text-primary ghost-border hover:bg-surface-container-high hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center justify-center">
                Batal
            </a>
            <button class="px-10 py-3 rounded-full font-bold text-white gradient-primary shadow-lg shadow-primary/30 hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center gap-2" type="submit">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection
