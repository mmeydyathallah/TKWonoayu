@extends('layouts.teacher')

@php
    $title = 'Database Siswa - TK Wonoayu';
@endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 10px 32px rgba(44, 52, 55, 0.06); }
    .ghost-border { border: 1px solid rgba(172, 179, 183, 0.15); }
    .avatar-ring { ring: 4px solid white; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .table-row-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,0.04); }
    .glass-pill { backdrop-filter: blur(8px); background: rgba(255,255,255,0.7); }
</style>
@endsection

@section('content')
<!-- TopAppBar -->
<header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl docked full-width top-0 sticky z-30 border-b border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center px-8 py-4 w-full -mx-8 mb-8">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <span class="material-symbols-outlined">group</span>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white font-headline">Database Siswa</h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">TK Wonoayu Management</p>
        </div>
    </div>
    
    <div class="flex items-center gap-4">
        <a href="{{ route('guru.students.form') }}" class="gradient-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-blue-500/20">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            Tambah Siswa
        </a>
    </div>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">
    <!-- Feedback Messages -->
    @if(session('success'))
    <div class="mb-8 flex items-center gap-4 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl animate-in fade-in slide-in-from-top-4 duration-500">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('duplicate_identity_notices') && count(session('duplicate_identity_notices')))
    <div class="mb-8 rounded-2xl border border-amber-100 bg-amber-50 px-6 py-4 text-amber-800">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined">info</span>
            <div>
                <p class="text-sm font-black">Data tersimpan, tetapi ada identitas anak yang sama.</p>
                <p class="text-xs font-bold text-amber-700">Periksa kembali agar tidak terjadi duplikasi tidak sengaja.</p>
            </div>
        </div>
        <ul class="mt-3 list-disc space-y-1 pl-10 text-sm font-bold">
            @foreach(session('duplicate_identity_notices') as $notice)
                <li>{{ $notice }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Search & Filter Area -->
    <div class="mb-10 flex flex-col lg:flex-row gap-6 items-center justify-between">
        <form action="{{ route('guru.students.index') }}" method="GET" class="relative w-full lg:w-[400px] group">
            <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full bg-white dark:bg-slate-800 text-sm rounded-2xl pl-12 pr-5 py-4 outline-none focus:ring-4 focus:ring-primary/10 border border-slate-100 focus:border-primary/30 transition-all font-body text-slate-700 ambient-shadow" placeholder="Cari nama, NISN, NIK, atau RFID..." type="text" onchange="this.form.submit()"/>
            @if(request('group'))
                <input type="hidden" name="group" value="{{ request('group') }}">
            @endif
        </form>
        
        <div class="flex items-center gap-3 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2 whitespace-nowrap">Filter Kelompok:</span>
            <a href="{{ route('guru.students.index', array_merge(request()->query(), ['group' => ''])) }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ !request('group') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white text-slate-600 border border-slate-100 hover:bg-slate-50' }}">
               Semua
            </a>
            @foreach(['A', 'B'] as $g)
            <a href="{{ route('guru.students.index', array_merge(request()->query(), ['group' => $g])) }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request('group') == $g ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white text-slate-600 border border-slate-100 hover:bg-slate-50' }}">
               {{ $g }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Student Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100 dark:border-slate-800">
                        <th class="px-10 py-6">Profil Siswa</th>
                        <th class="px-6 py-6">Identitas & Alamat</th>
                        <th class="px-6 py-6">Kelompok</th>
                        <th class="px-6 py-6">Wali Murid</th>
                        <th class="px-10 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($students as $student)
                    <tr class="transition-all duration-300 table-row-hover group bg-white dark:bg-slate-900">
                        <!-- Profil Column -->
                        <td class="px-10 py-7">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden avatar-ring bg-slate-50 flex-shrink-0">
                                    <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=f0f7ff&color=0060ad&bold=true&size=128' }}" 
                                         alt="{{ $student->full_name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-lg leading-tight group-hover:text-primary transition-colors">{{ $student->full_name }}</h4>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md {{ $student->gender == 'Laki-laki' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} text-[9px] font-black uppercase tracking-tighter">{{ $student->gender ?? 'N/A' }}</span>
                                        <p class="text-xs font-bold text-slate-400">"{{ $student->nickname ?? '-' }}"</p>
                                        @if($student->user_id)
                                        <span class="flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter" title="Akun Wali Aktif">
                                            <span class="material-symbols-outlined text-[10px]">vpn_key</span> Aktif
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Identity Column -->
                        <td class="px-6 py-7">
                            <div class="space-y-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                        <span class="material-symbols-outlined text-[16px] text-primary">badge</span>
                                        <span>Induk: {{ $student->student_no }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] font-bold tracking-tight">
                                        @if($student->rfid_code)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-600" title="RFID: {{ $student->rfid_code }}">
                                                <span class="material-symbols-outlined text-[14px]">nfc</span> RFID
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-400" title="RFID belum diisi">
                                                <span class="material-symbols-outlined text-[14px]">nfc</span> -
                                            </span>
                                        @endif
                                        @if($student->fingerprint_id)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600" title="Fingerprint ID: {{ $student->fingerprint_id }}">
                                                <span class="material-symbols-outlined text-[14px]">fingerprint</span> FP
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-400" title="Fingerprint belum terdaftar">
                                                <span class="material-symbols-outlined text-[14px]">fingerprint</span> -
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold tracking-tight">
                                        <span class="material-symbols-outlined text-[16px]">badge</span>
                                        <span>NIK: {{ $student->nik ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-slate-50 space-y-1">
                                    <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold">
                                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                                        <span class="line-clamp-1 italic">{{ $student->address ?? '-' }}</span>
                                    </div>
                                    @if($student->phone_number)
                                    <div class="flex items-center gap-2 text-[10px] text-primary font-bold">
                                        <span class="material-symbols-outlined text-[16px]">call</span>
                                        <span>{{ $student->phone_number }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Group Column (Editable) -->
                        <td class="px-6 py-7">
                            <form action="{{ route('guru.students.quickUpdateGroup', $student) }}" method="POST" class="relative group/select">
                                @csrf
                                @method('PUT')
                                <select name="class_group" onchange="this.form.submit()" class="appearance-none bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2 pr-10 text-xs font-black text-primary cursor-pointer hover:bg-primary/5 transition-all focus:ring-2 focus:ring-primary/20">
                                    @foreach(['A', 'B'] as $grp)
                                        <option value="{{ $grp }}" {{ $student->class_group == $grp ? 'selected' : '' }}>KELOMPOK {{ $grp }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-primary text-[16px] pointer-events-none transition-transform group-hover/select:translate-y-[-40%]">unfold_more</span>
                            </form>
                        </td>

                        <!-- Parent Column -->
                        <td class="px-6 py-7">
                            @if($student->parentProfile)
                            <div class="space-y-3 p-3 rounded-2xl bg-slate-50/50 border border-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center text-white text-[14px] shadow-md">
                                        <span class="material-symbols-outlined">supervisor_account</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Wali</p>
                                        <p class="text-xs font-bold text-slate-700">{{ $student->parentProfile->guardian_name }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-4 pl-1">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Ayah</p>
                                        <p class="text-[10px] font-bold text-slate-500">{{ $student->parentProfile->father_name ?? '-' }}</p>
                                    </div>
                                    <div class="border-l border-slate-200 pl-4">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Ibu</p>
                                        <p class="text-[10px] font-bold text-slate-500">{{ $student->parentProfile->mother_name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center gap-2 text-slate-300 italic text-xs">
                                <span class="material-symbols-outlined text-[18px]">info</span>
                                <span>Belum diatur</span>
                            </div>
                            @endif
                        </td>

                        <!-- Action Column -->
                        <td class="px-10 py-7 text-right">
                            <div class="flex items-center justify-end gap-3 transition-opacity duration-300">
                                <a href="{{ route('guru.students.edit', $student) }}" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white shadow-lg border border-slate-100 text-slate-600 hover:text-primary hover:border-primary/30 transition-all">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('guru.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Hapus data {{ $student->full_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white shadow-lg border border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-200 transition-all">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-32 text-center bg-slate-50/20">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 rounded-[2rem] bg-white shadow-xl flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-5xl text-slate-200">person_off</span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Database Masih Kosong</h3>
                                <p class="text-slate-400 text-sm mt-2 max-w-xs mx-auto">Silakan tambahkan data siswa pertama Anda untuk mulai mengelola administrasi.</p>
                                <a href="{{ route('guru.students.form') }}" class="mt-8 gradient-primary text-white px-8 py-3 rounded-xl font-bold text-sm shadow-xl shadow-blue-500/20">
                                    Tambah Siswa Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($students->isNotEmpty())
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Data: {{ $students->count() }} Siswa Terdaftar</p>
            <div class="flex gap-2">
                {{-- Pagination Links would go here --}}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
