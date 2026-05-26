@extends('layouts.parent')

@php
    $daysInMonth = $date->daysInMonth;
    $firstDayOfMonth = $date->copy()->startOfMonth()->dayOfWeek;
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
<div class="mb-8">
    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Agenda Sekolah</h1>
    <p class="text-sm text-slate-500 font-medium">Jadwal kegiatan dan hari libur TK Wonoayu.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    {{-- Left: Event List --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 text-sm uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">event_list</span>
                Event Bulan Ini
            </h3>
            
            <div class="space-y-4">
                @forelse($agendas->sortBy('event_date') as $agenda)
                <div class="p-4 {{ $types[$agenda->type]['bg'] }} rounded-2xl border border-transparent hover:border-slate-100 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-black {{ $types[$agenda->type]['text'] }} uppercase tracking-widest">{{ $types[$agenda->type]['label'] }}</span>
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ $agenda->event_date->format('d M Y') }}
                            @if($agenda->end_date && $agenda->end_date->ne($agenda->event_date))
                                - {{ $agenda->end_date->format('d M Y') }}
                            @endif
                        </span>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 mb-1">{{ $agenda->title }}</h4>
                    @if($agenda->description)
                    <p class="text-[11px] text-slate-500 leading-relaxed italic line-clamp-2">"{{ $agenda->description }}"</p>
                    @endif
                </div>
                @empty
                <div class="py-12 text-center">
                    <p class="text-xs text-slate-400 italic font-bold">Tidak ada agenda bulan ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-4 text-sm uppercase tracking-widest">Keterangan</h3>
            <div class="grid grid-cols-2 gap-2">
                @foreach($types as $key => $style)
                <div class="flex items-center gap-2 p-2 rounded-xl">
                    <div class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></div>
                    <span class="text-[10px] font-bold text-slate-500">{{ $style['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Calendar Grid --}}
    <div class="lg:col-span-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Calendar Header --}}
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-black text-slate-800">{{ $monthName }} <span class="text-slate-300">{{ $year }}</span></h2>
                    <div class="flex bg-slate-50 rounded-xl p-1">
                        <a href="{{ route('wali.agenda', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="p-1 hover:bg-white hover:shadow-sm rounded-lg transition-all">
                            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                        </a>
                        <a href="{{ route('wali.agenda', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="p-1 hover:bg-white hover:shadow-sm rounded-lg transition-all">
                            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                        </a>
                    </div>
                </div>
                <a href="{{ route('wali.agenda') }}" class="text-xs font-black text-primary uppercase tracking-widest hover:underline">Bulan Ini</a>
            </div>

            {{-- Weekdays Header --}}
            <div class="grid grid-cols-7 bg-slate-50/50 border-b border-slate-50">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                <div class="py-4 text-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $day }}</span>
                </div>
                @endforeach
            </div>

            {{-- Calendar Days Grid --}}
            <div class="grid grid-cols-7 auto-rows-[100px]">
                @for($i = 0; $i < $firstDayOfMonth; $i++)
                <div class="border-b border-r border-slate-50 bg-slate-50/10"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $isToday = now()->isSameDay(\Carbon\Carbon::create($year, $month, $day));
                        $dayAgendas = $agendasByDay->get($day, collect());
                        $holiday = $dayAgendas->where('type', 'libur')->first();
                    @endphp
                <div class="border-b border-r border-slate-50 p-2 relative {{ $holiday ? 'bg-rose-50/10' : '' }}">
                    <span class="w-6 h-6 flex items-center justify-center rounded-lg text-xs font-black 
                        @if($isToday) bg-primary text-white shadow-lg shadow-primary/30 @elseif($holiday) bg-rose-500 text-white shadow-sm @else text-slate-400 @endif">
                        {{ $day }}
                    </span>
                    
                    <div class="mt-1 space-y-0.5">
                        @foreach($dayAgendas as $agenda)
                        <div class="w-full h-1.5 rounded-full {{ $types[$agenda->type]['dot'] }} opacity-60" title="{{ $agenda->title }}"></div>
                        @endforeach
                    </div>
                </div>
                @endfor

                @php
                    $remainingDays = (7 - (($firstDayOfMonth + $daysInMonth) % 7)) % 7;
                @endphp
                @for($i = 0; $i < $remainingDays; $i++)
                <div class="border-b border-r border-slate-50 bg-slate-50/10"></div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection
