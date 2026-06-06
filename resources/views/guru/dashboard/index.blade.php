@extends('layouts.teacher')

@php
    $title = 'Dashboard Guru - TK Wonoayu';
@endphp

@section('styles')
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-8 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Dashboard Guru</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Ringkasan Aktivitas & Statistik</p>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto w-full pb-20">
    
    {{-- WELCOME CARD --}}
    <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-100 ambient-shadow mb-8 p-8 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-slate-900 font-headline tracking-tight mb-2">Selamat Pagi, {{ $teacherName ?? 'Bu Guru' }}!</h2>
            <p class="text-slate-500 font-medium">Siap untuk membimbing tunas bangsa hari ini? Berikut ringkasan kelas Anda.</p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ route('guru.daily') }}" class="gradient-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 hover:scale-105 transition-transform flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> Input Penilaian
                </a>
                <a href="{{ route('guru.students.index') }}" class="bg-slate-50 text-slate-700 border border-slate-200 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-100 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">group</span> Database Siswa
                </a>
            </div>
        </div>
        <div class="bg-primary/5 rounded-3xl p-8 flex items-center gap-6 border border-primary/10">
            <div class="w-16 h-16 rounded-2xl gradient-primary flex items-center justify-center text-white shadow-xl shadow-blue-500/30">
                <span class="material-symbols-outlined text-3xl">groups</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">Total Siswa</p>
                <p class="text-4xl font-extrabold text-slate-900 leading-none">{{ $totalStudents ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- ATTENDANCE TODAY --}}
    @php
        $attendanceMeta = [
            'hadir' => ['label' => 'Hadir', 'icon' => 'check_circle', 'class' => 'bg-emerald-50 text-emerald-600'],
            'izin' => ['label' => 'Izin', 'icon' => 'assignment_late', 'class' => 'bg-blue-50 text-blue-600'],
            'sakit' => ['label' => 'Sakit', 'icon' => 'sick', 'class' => 'bg-amber-50 text-amber-600'],
            'alpa' => ['label' => 'Alpa', 'icon' => 'cancel', 'class' => 'bg-rose-50 text-rose-600'],
        ];
    @endphp
    <section class="bg-white rounded-3xl border border-slate-100 ambient-shadow mb-8 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="font-headline text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">event_available</span>
                    Absensi Hari Ini
                </h3>
                <p class="text-xs font-bold text-slate-400 mt-1">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="rounded-2xl bg-primary/5 border border-primary/10 px-5 py-3">
                    <p class="text-[10px] font-black text-primary uppercase tracking-widest">Kehadiran</p>
                    <p class="text-2xl font-black text-slate-900 leading-tight">{{ $attendancePercent ?? 0 }}%</p>
                </div>
                <a href="{{ route('guru.attendance.index', ['date' => now()->toDateString()]) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3 text-sm font-black text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[18px]">edit_calendar</span>
                    Input Absensi
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-0 divide-x divide-y lg:divide-y-0 divide-slate-100">
            <div class="p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tercatat</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $recordedAttendanceCount ?? 0 }}/{{ $totalStudents ?? 0 }}</p>
                <p class="mt-1 text-xs font-bold text-slate-400">Siswa hari ini</p>
            </div>
            @foreach($attendanceMeta as $key => $meta)
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $meta['label'] }}</p>
                        <span class="material-symbols-outlined text-[20px] rounded-xl p-2 {{ $meta['class'] }}">{{ $meta['icon'] }}</span>
                    </div>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $attendanceCounts[$key] ?? 0 }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-400">Siswa</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- BENTO GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- QUICK ACCESS --}}
        <div class="lg:col-span-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('guru.artworks') }}" class="group bg-white p-6 rounded-3xl border border-slate-100 ambient-shadow hover:border-primary/30 transition-all flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">palette</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Hasil Karya</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Dokumentasi karya siswa</p>
                    </div>
                </a>
                <a href="{{ route('guru.anecdotal') }}" class="group bg-white p-6 rounded-3xl border border-slate-100 ambient-shadow hover:border-primary/30 transition-all flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">description</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Catatan Anekdot</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Peristiwa penting harian</p>
                    </div>
                </a>
                <a href="{{ route('guru.attendance.index') }}" class="group bg-white p-6 rounded-3xl border border-slate-100 ambient-shadow hover:border-primary/30 transition-all flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">event_available</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Absensi</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Catat kehadiran siswa</p>
                    </div>
                </a>
            </div>

            {{-- UPCOMING AGENDAS WITH EDIT/DELETE --}}
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">event</span> Agenda Sekolah Mendatang
                    </h3>
                    <div class="flex items-center gap-3">
                        <button onclick="openModal('addAgendaModal')" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">add</span> Tambah
                        </button>
                        <a href="{{ route('guru.agenda') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:underline">Semua</a>
                    </div>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($upcomingAgendas as $agenda)
                    <div class="p-5 flex items-center gap-5 hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h5 class="text-sm font-bold text-slate-800">{{ $agenda->title }}</h5>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                    @if($agenda->type == 'libur') bg-rose-50 text-rose-600 @elseif($agenda->type == 'kegiatan') bg-blue-50 text-blue-600 @elseif($agenda->type == 'ujian') bg-amber-50 text-amber-600 @elseif($agenda->type == 'pengumuman') bg-emerald-50 text-emerald-600 @else bg-slate-50 text-slate-600 @endif">
                                    {{ $agenda->type }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $agenda->description ?? 'Tidak ada deskripsi' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">
                                {{ $agenda->event_date->isoFormat('dddd, D MMMM YYYY') }}
                                @if($agenda->end_date && $agenda->end_date->ne($agenda->event_date))
                                    - {{ $agenda->end_date->isoFormat('dddd, D MMMM YYYY') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="editAgendaFromDash({{ $agenda }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <form action="{{ route('guru.agenda.destroy', $agenda->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 flex flex-col items-center justify-center text-center opacity-60">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">calendar_today</span>
                        <p class="text-xs font-bold text-slate-400">Belum ada agenda sekolah terdaftar</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CALENDAR WIDGET --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow p-6">
                <div class="flex justify-between items-center mb-6 px-2">
                    <h4 class="font-extrabold text-slate-900 text-lg">{{ now()->translatedFormat('F Y') }}</h4>
                    <div class="flex gap-1">
                        <button class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 text-slate-400 transition-colors">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </button>
                        <button class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 text-slate-400 transition-colors">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </button>
                    </div>
                </div>
                @php
                    $daysInMonth = now()->daysInMonth;
                    $firstDay = now()->startOfMonth()->dayOfWeek;
                    
                    // Fetch agendas that overlap with current month
                    $startOfMonth = now()->startOfMonth();
                    $endOfMonth = now()->endOfMonth();
                    
                    $agendas = \App\Models\SchoolAgenda::where(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereBetween('event_date', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                          ->orWhere(function($sq) use ($startOfMonth, $endOfMonth) {
                              $sq->where('event_date', '<=', $startOfMonth)
                                ->where('end_date', '>=', $endOfMonth);
                          });
                    })->get();

                    // Group agendas by day (handling multi-day)
                    $agendasThisMonth = collect();
                    foreach($agendas as $agenda) {
                        $start = $agenda->event_date;
                        $end = $agenda->end_date ?: $start;
                        $current = $start->copy();
                        while($current <= $end) {
                            if($current->month == now()->month && $current->year == now()->year) {
                                $day = $current->day;
                                if(!$agendasThisMonth->has($day)) $agendasThisMonth->put($day, collect());
                                $agendasThisMonth->get($day)->push($agenda);
                            }
                            $current->addDay();
                        }
                    }

                    $typeStyles = [
                        'libur'      => ['bg' => 'bg-rose-500',    'text' => 'text-white',       'label' => 'LIBUR'],
                        'kegiatan'   => ['bg' => 'bg-blue-500',    'text' => 'text-white',       'label' => 'KEGIATAN'],
                        'ujian'      => ['bg' => 'bg-amber-500',   'text' => 'text-white',       'label' => 'UJIAN'],
                        'pengumuman' => ['bg' => 'bg-emerald-500', 'text' => 'text-white',       'label' => 'INFO'],
                        'lainnya'    => ['bg' => 'bg-slate-400',   'text' => 'text-white',       'label' => 'LAIN'],
                    ];
                @endphp
                <div class="grid grid-cols-7 gap-1 text-center mb-2">
                    @foreach(['M','S','S','R','K','J','S'] as $day)
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $day }}</span>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-1">
                    @for($i = 0; $i < $firstDay; $i++)
                        <div class="aspect-square"></div>
                    @endfor
                    @for($i = 1; $i <= $daysInMonth; $i++)
                        @php 
                            $isToday = ($i == now()->day); 
                            $dayAgendas = $agendasThisMonth->get($i, collect());
                            $priorityOrder = ['libur','ujian','kegiatan','pengumuman','lainnya'];
                            $topAgenda = null;
                            foreach($priorityOrder as $p) {
                                $found = $dayAgendas->where('type', $p)->first();
                                if ($found) { $topAgenda = $found; break; }
                            }
                        @endphp
                        @if($isToday && $topAgenda)
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl gradient-primary text-white shadow-lg shadow-blue-500/30 cursor-pointer relative"
                                 title="{{ $dayAgendas->pluck('title')->implode(', ') }}">
                                <span class="text-sm font-black">{{ $i }}</span>
                                <span class="text-[6px] font-black opacity-90 uppercase tracking-tighter mt-0.5 leading-none">{{ $typeStyles[$topAgenda->type]['label'] }}</span>
                            </div>
                        @elseif($isToday)
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl gradient-primary text-white shadow-lg shadow-blue-500/30 cursor-pointer">
                                <span class="text-sm font-black">{{ $i }}</span>
                            </div>
                        @elseif($topAgenda)
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl {{ $typeStyles[$topAgenda->type]['bg'] }} {{ $typeStyles[$topAgenda->type]['text'] }} shadow-md cursor-pointer hover:opacity-90 transition-opacity"
                                 title="{{ $dayAgendas->pluck('title')->implode(', ') }}">
                                <span class="text-sm font-black">{{ $i }}</span>
                                <span class="text-[6px] font-black opacity-90 uppercase tracking-tighter mt-0.5 leading-none">{{ $typeStyles[$topAgenda->type]['label'] }}</span>
                            </div>
                        @else
                            <div class="aspect-square flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                                {{ $i }}
                            </div>
                        @endif
                    @endfor
                </div>

                {{-- Legend --}}
                <div class="mt-6 pt-4 border-t border-slate-50 flex flex-wrap gap-x-4 gap-y-2">
                    @foreach($typeStyles as $key => $s)
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $s['bg'] }}"></div>
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tight">{{ $s['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- MINI TIPS CARD --}}
            <div class="bg-indigo-600 rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-indigo-500/20">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <span class="material-symbols-outlined text-3xl mb-3 opacity-80">tips_and_updates</span>
                <h5 class="text-lg font-bold leading-tight mb-2">Tips Hari Ini</h5>
                <p class="text-xs text-indigo-100 leading-relaxed font-medium">Jangan lupa berikan pujian spesifik atas usaha anak, bukan hanya pada hasilnya.</p>
            </div>
        </div>

    </div>
</div>

{{-- Add Agenda Modal --}}
<div id="addAgendaModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('addAgendaModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8">
        <h3 class="text-xl font-black text-slate-800 mb-6">Tambah Agenda Baru</h3>
        <form action="{{ route('guru.agenda.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Judul Agenda</label>
                <input type="text" name="title" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all" placeholder="Misal: Libur Semester">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Mulai</label>
                    <input type="date" name="event_date" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Selesai</label>
                    <input type="date" name="end_date" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Tipe & Kategori</label>
                <select name="type" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                    <option value="libur">Libur</option>
                    <option value="kegiatan">Kegiatan</option>
                    <option value="ujian">Ujian</option>
                    <option value="pengumuman">Pengumuman</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl">
                <input type="checkbox" name="is_public" checked id="add_is_public" class="w-5 h-5 rounded-lg border-slate-200 text-primary focus:ring-primary">
                <label for="add_is_public" class="text-xs font-bold text-slate-600 cursor-pointer">Tampilkan ke Wali Murid</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('addAgendaModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Agenda Modal --}}
<div id="editAgendaDashModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('editAgendaDashModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8">
        <h3 class="text-xl font-black text-slate-800 mb-6">Edit Agenda</h3>
        <form id="editDashForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Judul Agenda</label>
                <input type="text" name="title" id="dash_edit_title" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Mulai</label>
                    <input type="date" name="event_date" id="dash_edit_date" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Selesai</label>
                    <input type="date" name="end_date" id="dash_edit_end_date" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Tipe & Kategori</label>
                <select name="type" id="dash_edit_type" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                    <option value="libur">Libur</option>
                    <option value="kegiatan">Kegiatan</option>
                    <option value="ujian">Ujian</option>
                    <option value="pengumuman">Pengumuman</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl">
                <input type="checkbox" name="is_public" id="dash_edit_is_public" class="w-5 h-5 rounded-lg border-slate-200 text-primary focus:ring-primary">
                <label for="dash_edit_is_public" class="text-xs font-bold text-slate-600 cursor-pointer">Tampilkan ke Wali Murid</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('editAgendaDashModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function editAgendaFromDash(agenda) {
    document.getElementById('dash_edit_title').value = agenda.title;
    document.getElementById('dash_edit_date').value = agenda.event_date ? agenda.event_date.split('T')[0] : '';
    document.getElementById('dash_edit_end_date').value = agenda.end_date ? agenda.end_date.split('T')[0] : '';
    document.getElementById('dash_edit_type').value = agenda.type;
    document.getElementById('dash_edit_is_public').checked = agenda.is_public;
    
    document.getElementById('editDashForm').action = `/guru/agenda/${agenda.id}`;
    
    openModal('editAgendaDashModal');
}
</script>
@endsection
