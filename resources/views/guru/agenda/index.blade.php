@extends('layouts.teacher')

@section('title', 'Agenda Sekolah - Portal Guru')

@php
    $daysInMonth = $date->daysInMonth;
    $firstDayOfMonth = $date->copy()->startOfMonth()->dayOfWeek; // 0 (Sun) to 6 (Sat)
    $monthName = $date->translatedFormat('F');
    $prevMonth = $date->copy()->subMonth();
    $nextMonth = $date->copy()->addMonth();
    
    // Group agendas by day (handling multi-day)
    $agendasByDay = collect();
    foreach($agendas as $agenda) {
        $start = $agenda->event_date;
        $end = $agenda->end_date ?: $start;
        
        $current = $start->copy();
        while($current <= $end) {
            if($current->month == $date->month && $current->year == $date->year) {
                $day = $current->day;
                if(!$agendasByDay->has($day)) $agendasByDay->put($day, collect());
                $agendasByDay->get($day)->push($agenda);
            }
            $current->addDay();
        }
    }
    
    $types = [
        'libur' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'dot' => 'bg-rose-500', 'label' => 'Libur'],
        'kegiatan' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'dot' => 'bg-blue-500', 'label' => 'Kegiatan'],
        'ujian' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500', 'label' => 'Ujian'],
        'pengumuman' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'label' => 'Pengumuman'],
        'lainnya' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'dot' => 'bg-slate-500', 'label' => 'Lainnya'],
    ];
@endphp

@section('content')
<div class="p-8">
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl animate-in fade-in slide-in-from-top-4">
        <div class="flex items-center gap-3 mb-2">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-bold">Terjadi kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-xs font-medium ml-8 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Agenda Sekolah</h1>
            <p class="text-sm text-slate-500 font-medium">Kelola kegiatan dan hari libur sekolah.</p>
        </div>
        <button onclick="openModal('addAgendaModal')" class="bg-primary text-white px-6 py-3 rounded-2xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-105 transition-all">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Agenda
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Calendar Sidebar/Stats --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 mb-4 text-sm uppercase tracking-widest">Kategori</h3>
                <div class="space-y-2">
                    @foreach($types as $key => $style)
                    <div class="flex items-center gap-3 p-3 {{ $style['bg'] }} rounded-2xl border border-transparent hover:border-slate-100 transition-all">
                        <div class="w-2 h-2 rounded-full {{ $style['dot'] }}"></div>
                        <span class="text-xs font-bold {{ $style['text'] }}">{{ $style['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-primary rounded-3xl p-6 text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <h3 class="font-bold text-sm mb-1 opacity-80 uppercase tracking-widest">Event Bulan Ini</h3>
                <p class="text-4xl font-black">{{ $agendas->count() }}</p>
                <p class="text-[10px] font-bold opacity-60 mt-2 uppercase">Sinkron ke Wali Murid</p>
            </div>
        </div>

        {{-- Main Calendar Grid --}}
        <div class="lg:col-span-9">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                {{-- Calendar Header --}}
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-black text-slate-800">{{ $monthName }} <span class="text-slate-300">{{ $year }}</span></h2>
                        <div class="flex bg-slate-50 rounded-xl p-1">
                            <a href="{{ route('guru.agenda', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="p-1 hover:bg-white hover:shadow-sm rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </a>
                            <a href="{{ route('guru.agenda', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="p-1 hover:bg-white hover:shadow-sm rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('guru.agenda') }}" class="text-xs font-black text-primary uppercase tracking-widest hover:underline">Bulan Ini</a>
                </div>

                {{-- Weekdays Header --}}
                <div class="grid grid-cols-7 bg-slate-50/50 border-b border-slate-50">
                    @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                    <div class="py-4 text-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $day }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Calendar Days Grid --}}
                <div class="grid grid-cols-7 auto-rows-[120px]">
                    {{-- Empty days at start --}}
                    @for($i = 0; $i < $firstDayOfMonth; $i++)
                    <div class="border-b border-r border-slate-50 bg-slate-50/20"></div>
                    @endfor

                    {{-- Actual days --}}
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $isToday = now()->isSameDay(\Carbon\Carbon::create($year, $month, $day));
                            $dayAgendas = $agendasByDay->get($day, collect());
                            $holiday = $dayAgendas->where('type', 'libur')->first();
                        @endphp
                    <div class="border-b border-r border-slate-50 p-2 hover:bg-slate-50/50 transition-colors group relative {{ $holiday ? 'bg-rose-50/20' : '' }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-sm font-black 
                                @if($isToday) bg-primary text-white shadow-lg shadow-primary/30 @elseif($holiday) bg-rose-500 text-white shadow-md @else text-slate-400 @endif">
                                {{ $day }}
                            </span>
                        </div>
                        
                        <div class="space-y-1 overflow-y-auto max-h-[80px] no-scrollbar">
                            @foreach($dayAgendas as $agenda)
                            <div onclick="editAgenda({{ $agenda }})" class="px-2 py-1 {{ $types[$agenda->type]['bg'] }} rounded-lg border border-slate-100 cursor-pointer hover:shadow-sm transition-all overflow-hidden whitespace-nowrap">
                                <p class="text-[9px] font-black {{ $types[$agenda->type]['text'] }} truncate">{{ $agenda->title }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endfor

                    {{-- Empty days at end --}}
                    @php
                        $remainingDays = (7 - (($firstDayOfMonth + $daysInMonth) % 7)) % 7;
                    @endphp
                    @for($i = 0; $i < $remainingDays; $i++)
                    <div class="border-b border-r border-slate-50 bg-slate-50/20"></div>
                    @endfor
                </div>
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
                    @foreach($types as $key => $style)
                    <option value="{{ $key }}">{{ $style['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl">
                <input type="checkbox" name="is_public" checked id="is_public" class="w-5 h-5 rounded-lg border-slate-200 text-primary focus:ring-primary">
                <label for="is_public" class="text-xs font-bold text-slate-600 cursor-pointer">Tampilkan ke Wali Murid</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('addAgendaModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Agenda Modal --}}
<div id="editAgendaModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('editAgendaModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8">
        <div class="flex justify-between items-start mb-6">
            <h3 class="text-xl font-black text-slate-800">Edit Agenda</h3>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-all" onclick="return confirm('Hapus agenda ini?')">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </form>
        </div>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Judul Agenda</label>
                <input type="text" name="title" id="edit_title" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Mulai</label>
                    <input type="date" name="event_date" id="edit_date" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Selesai</label>
                    <input type="date" name="end_date" id="edit_end_date" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Tipe & Kategori</label>
                <select name="type" id="edit_type" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary transition-all">
                    @foreach($types as $key => $style)
                    <option value="{{ $key }}">{{ $style['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl">
                <input type="checkbox" name="is_public" id="edit_is_public" class="w-5 h-5 rounded-lg border-slate-200 text-primary focus:ring-primary">
                <label for="edit_is_public" class="text-xs font-bold text-slate-600 cursor-pointer">Tampilkan ke Wali Murid</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('editAgendaModal')" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
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

function editAgenda(agenda) {
    document.getElementById('edit_title').value = agenda.title;
    document.getElementById('edit_date').value = agenda.event_date.split('T')[0];
    document.getElementById('edit_end_date').value = agenda.end_date ? agenda.end_date.split('T')[0] : '';
    document.getElementById('edit_type').value = agenda.type;
    document.getElementById('edit_is_public').checked = agenda.is_public;
    
    document.getElementById('editForm').action = `/guru/agenda/${agenda.id}`;
    document.getElementById('deleteForm').action = `/guru/agenda/${agenda.id}`;
    
    openModal('editAgendaModal');
}
</script>
@endsection
