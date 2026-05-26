@extends('layouts.parent')

@php $title = 'Profil Anak - Portal Wali Murid TK Wonoayu'; @endphp

@section('content')
{{-- Header --}}
<div class="mb-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
        <h1 class="font-headline text-4xl md:text-5xl font-extrabold text-primary mb-2">Profil Biodata</h1>
        <p class="text-on-surface-variant text-lg">Data lengkap peserta didik TK Wonoayu</p>
    </div>
</div>

@if($student)
{{-- Hero Card --}}
<section class="bg-gradient-to-br from-primary to-primary-container rounded-xl p-8 mb-8 shadow-[0_20px_50px_rgba(0,96,173,0.1)] relative overflow-hidden text-on-primary">
    <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
        <img alt="Foto Profil {{ $student->full_name }}"
             class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white/20 object-cover shadow-lg"
             src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=68abff&color=ffffff&size=160' }}"/>
        <div class="text-center md:text-left">
            <div class="inline-block bg-white/20 px-4 py-1 rounded-full text-sm font-bold mb-3">
                Kelompok {{ $student->class_group }} — {{ $student->school_year }}
            </div>
            <h2 class="font-headline text-3xl md:text-4xl font-extrabold mb-2">{{ $student->full_name }}</h2>
            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-white/80 text-sm font-medium">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">badge</span> No. Induk: {{ $student->student_no }}</span>
                @if($student->nisn)
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">fingerprint</span> NISN: {{ $student->nisn }}</span>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Detail Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    {{-- Identitas Anak --}}
    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_8px_30px_rgba(44,52,55,0.04)] relative">
        <div class="absolute top-0 left-0 w-2 h-full bg-secondary rounded-l-xl opacity-80"></div>
        <h3 class="font-headline text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">face</span>
            Identitas Anak
        </h3>
        <div class="space-y-4">
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Nama Lengkap</p>
                <p class="text-base font-medium">{{ $student->full_name }}</p>
            </div>
            @if($student->nickname)
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Nama Panggilan</p>
                <p class="text-base font-medium">{{ $student->nickname }}</p>
            </div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                @if($student->nik)
                <div>
                    <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">NIK</p>
                    <p class="text-base font-medium">{{ $student->nik }}</p>
                </div>
                @endif
                @if($student->gender)
                <div>
                    <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Jenis Kelamin</p>
                    <p class="text-base font-medium">{{ $student->gender }}</p>
                </div>
                @endif
            </div>
            @if($student->birth_place || $student->birth_date)
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</p>
                <p class="text-base font-medium">
                    {{ $student->birth_place }}{{ $student->birth_place && $student->birth_date ? ', ' : '' }}{{ optional($student->birth_date)->format('d F Y') }}
                </p>
            </div>
            @endif
            @if($student->religion)
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Agama</p>
                <p class="text-base font-medium">{{ $student->religion }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Identitas Orang Tua --}}
    <div class="bg-surface-container-low rounded-xl p-8 relative">
        <h3 class="font-headline text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">family_restroom</span>
            Identitas Orang Tua / Wali
        </h3>
        @if($parentProfile)
        <div class="space-y-4">
            <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/15">
                <p class="text-xs text-primary font-bold uppercase tracking-wider mb-2">Wali Murid</p>
                <div class="grid gap-1">
                    <p><span class="text-on-surface-variant text-sm">Nama:</span> <span class="font-medium">{{ $parentProfile->guardian_name }}</span></p>
                    @if($parentProfile->guardian_phone)
                    <p><span class="text-on-surface-variant text-sm">No. HP:</span> <span class="font-medium">{{ $parentProfile->guardian_phone }}</span></p>
                    @endif
                </div>
            </div>
            @if($parentProfile->father_name)
            <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/15">
                <p class="text-xs text-primary font-bold uppercase tracking-wider mb-2">Data Ayah</p>
                <div class="grid gap-1">
                    <p><span class="text-on-surface-variant text-sm">Nama:</span> <span class="font-medium">{{ $parentProfile->father_name }}</span></p>
                    @if($parentProfile->father_job)<p><span class="text-on-surface-variant text-sm">Pekerjaan:</span> <span class="font-medium">{{ $parentProfile->father_job }}</span></p>@endif
                </div>
            </div>
            @endif
            @if($parentProfile->mother_name)
            <div class="bg-surface-container-lowest p-4 rounded-lg shadow-sm border border-outline-variant/15">
                <p class="text-xs text-tertiary font-bold uppercase tracking-wider mb-2">Data Ibu</p>
                <div class="grid gap-1">
                    <p><span class="text-on-surface-variant text-sm">Nama:</span> <span class="font-medium">{{ $parentProfile->mother_name }}</span></p>
                    @if($parentProfile->mother_job)<p><span class="text-on-surface-variant text-sm">Pekerjaan:</span> <span class="font-medium">{{ $parentProfile->mother_job }}</span></p>@endif
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="text-center py-8 text-on-surface-variant italic">Belum ada data orang tua tersedia.</div>
        @endif
    </div>

    {{-- Alamat --}}
    @if($student->address)
    <div class="md:col-span-2 bg-surface-container-lowest rounded-xl p-8 shadow-[0_8px_30px_rgba(44,52,55,0.04)] relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-tertiary-container/20 rounded-full blur-3xl pointer-events-none"></div>
        <h3 class="font-headline text-xl font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">location_on</span>
            Alamat Tempat Tinggal
        </h3>
        <p class="text-lg font-medium leading-relaxed">{{ $student->address }}</p>
        @if($student->distance_to_school_km)
        <div class="flex items-center gap-2 text-sm text-on-surface-variant bg-surface-container-low px-3 py-2 rounded-lg inline-flex mt-4">
            <span class="material-symbols-outlined text-base">directions_bus</span> Jarak ke sekolah: ~{{ $student->distance_to_school_km }} km
        </div>
        @endif
    </div>
    @endif
</div>

@else
{{-- Empty State --}}
<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="w-24 h-24 rounded-full bg-surface-container-low flex items-center justify-center mb-4">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/40">child_care</span>
    </div>
    <h2 class="font-headline text-2xl font-bold text-on-surface mb-2">Data Siswa Belum Tersedia</h2>
    <p class="text-on-surface-variant max-w-sm">Data profil anak belum terdaftar di sistem. Silakan hubungi pihak sekolah.</p>
</div>
@endif
@endsection