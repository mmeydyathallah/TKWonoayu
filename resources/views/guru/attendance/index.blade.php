@extends('layouts.teacher')

@php
    $title = 'Absensi Siswa - Portal Guru TK Wonoayu';
    $dateValue = \Carbon\Carbon::parse($date)->format('Y-m-d');
    $selectedDateLabel = \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY');
    $todayDateLabel = \Carbon\Carbon::parse($todayDate)->isoFormat('dddd, D MMMM YYYY');
    $statusMeta = [
        'hadir' => ['label' => 'Hadir', 'icon' => 'check_circle', 'checked' => 'peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'izin' => ['label' => 'Izin', 'icon' => 'assignment_late', 'checked' => 'peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:ring-blue-200', 'dot' => 'bg-blue-500'],
        'sakit' => ['label' => 'Sakit', 'icon' => 'sick', 'checked' => 'peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:ring-amber-200', 'dot' => 'bg-amber-500'],
        'alpa' => ['label' => 'Alpa', 'icon' => 'cancel', 'checked' => 'peer-checked:bg-rose-50 peer-checked:text-rose-700 peer-checked:ring-rose-200', 'dot' => 'bg-rose-500'],
    ];
    $displayStatusMeta = $statusMeta + [
        'belum' => ['label' => 'Belum', 'icon' => 'pending_actions', 'dot' => 'bg-slate-300'],
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-black uppercase tracking-widest text-primary mb-3">
                <span class="material-symbols-outlined text-[16px]">event_available</span>
                Absensi Harian
            </div>
            <h1 class="font-headline text-3xl md:text-4xl font-black text-slate-900">Absensi Siswa</h1>
            <p class="text-sm text-slate-500 mt-2">
                Input kehadiran {{ $selectedDateLabel }} akan langsung muncul di portal wali murid.
            </p>
        </div>

        <div class="flex flex-col items-stretch lg:items-end gap-3">
            <div class="inline-flex self-start lg:self-end items-center gap-2 rounded-full border border-slate-100 bg-white px-3 py-1.5 text-xs font-black text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                <span id="current-clock">{{ now()->format('H:i:s') }}</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-3">
                <form id="attendance-date-filter" method="GET" action="{{ route('guru.attendance.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <label class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400">calendar_today</span>
                        <input id="attendance-date-input" type="date" name="date" value="{{ $dateValue }}" class="w-full sm:w-44 rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm font-bold text-slate-700 focus:border-primary focus:ring-primary/20">
                    </label>
                    <label class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400">groups</span>
                        <select name="group" class="w-full sm:w-56 rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:border-primary focus:ring-primary/20">
                            <option value="">Semua kelompok</option>
                            @foreach($classGroups as $classGroup)
                                <option value="{{ $classGroup }}" @selected($group === $classGroup)>{{ $classGroup }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-black text-white shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Terapkan
                    </button>
                    @if($isCustomDate)
                        <a href="{{ route('guru.attendance.index', array_filter(['group' => $group])) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-600 hover:border-primary/30 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[18px]">today</span>
                            Hari ini
                        </a>
                    @endif
                </form>
                <p class="mt-2 flex items-center gap-1.5 px-1 text-[11px] font-bold text-slate-400">
                    <span class="material-symbols-outlined text-[15px]">{{ $isCustomDate ? 'edit_calendar' : 'today' }}</span>
                    {{ $isCustomDate ? 'Tanggal manual' : 'Default hari ini' }}: {{ $isCustomDate ? $selectedDateLabel : $todayDateLabel }}
                </p>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <section class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tercatat</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $recordedCount }}/{{ $totalStudents }}</p>
            <p class="mt-1 text-xs font-bold text-slate-400">Data pada tanggal ini</p>
        </div>
        @foreach($displayStatusMeta as $key => $meta)
            <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">{{ $meta['label'] }}</p>
                    <span class="h-3 w-3 rounded-full {{ $meta['dot'] }}"></span>
                </div>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $statusCounts[$key] ?? 0 }}</p>
                <p class="mt-1 text-xs font-bold text-slate-400">Siswa</p>
            </div>
        @endforeach
    </section>

    <form method="POST" action="{{ route('guru.attendance.store') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @csrf
        <input id="attendance-submit-date" type="hidden" name="date" value="{{ $dateValue }}">
        <input type="hidden" name="group" value="{{ $group }}">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-headline text-lg font-black text-slate-900">Daftar Kehadiran</h2>
                <p class="text-xs font-bold text-slate-400">{{ $selectedDateLabel }} - siswa belum dipilih akan disimpan sebagai Alpa.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($statusMeta as $key => $meta)
                        <button type="button" data-set-status="{{ $key }}" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-black text-slate-600 hover:border-primary/30 hover:text-primary transition-colors">
                            Semua {{ $meta['label'] }}
                        </button>
                    @endforeach
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-black text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Absensi
                </button>
            </div>
        </div>

        @if($students->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr class="text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-5 py-4 w-12">No</th>
                            <th class="px-5 py-4">Siswa</th>
                            <th class="px-5 py-4 w-52">Kelompok</th>
                            <th class="px-5 py-4 w-40">Masuk</th>
                            <th class="px-5 py-4 w-40">Pulang</th>
                            <th class="px-5 py-4 w-72">Status</th>
                            <th class="px-5 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            @php
                                $att = $attendances->get($student->id);
                                $rowStatus = $att?->status ?? 'belum';
                                $rowMeta = $displayStatusMeta[$rowStatus] ?? $displayStatusMeta['belum'];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 font-black text-slate-400">
                                    <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}" class="h-11 w-11 rounded-xl object-cover ring-1 ring-slate-100" alt="{{ $student->full_name }}">
                                        <div>
                                            <p class="font-black text-slate-800">{{ $student->full_name }}</p>
                                            <p class="text-xs font-bold text-slate-400">{{ $student->nickname ?: $student->student_no }}</p>
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-slate-500">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $rowMeta['dot'] }}"></span>
                                                {{ $rowMeta['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-500">{{ $student->class_group }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-black text-slate-600">
                                        <span class="material-symbols-outlined text-[15px]">login</span>
                                        {{ $att?->check_in_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-black text-slate-600">
                                        <span class="material-symbols-outlined text-[15px]">logout</span>
                                        {{ $att?->check_out_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($statusMeta as $key => $meta)
                                            <label class="cursor-pointer">
                                                <input class="peer sr-only attendance-status" type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $key }}" @checked($att?->status === $key)>
                                                <span class="flex items-center justify-center rounded-xl px-3 py-2 text-xs font-black ring-1 ring-slate-200 text-slate-500 peer-checked:ring-2 {{ $meta['checked'] }}">
                                                    {{ $meta['label'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <input type="text" name="attendance[{{ $student->id }}][note]" value="{{ $att?->note }}" placeholder="Opsional" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:border-primary focus:ring-primary/20">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300">groups</span>
                <p class="mt-3 font-black text-slate-700">Tidak ada siswa pada filter ini.</p>
                <p class="text-sm text-slate-400">Ubah kelompok atau tambahkan data siswa terlebih dahulu.</p>
            </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-set-status]').forEach((button) => {
        button.addEventListener('click', () => {
            const status = button.dataset.setStatus;
            document.querySelectorAll(`.attendance-status[value="${status}"]`).forEach((input) => {
                input.checked = true;
            });
        });
    });

    const dateFilterForm = document.getElementById('attendance-date-filter');
    const dateInput = document.getElementById('attendance-date-input');
    const submitDateInput = document.getElementById('attendance-submit-date');

    dateInput?.addEventListener('change', () => {
        if (submitDateInput) {
            submitDateInput.value = dateInput.value;
        }

        if (dateFilterForm && dateInput.value) {
            dateFilterForm.requestSubmit();
        }
    });

    const clock = document.getElementById('current-clock');
    const updateClock = () => {
        if (!clock) return;
        clock.textContent = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(new Date()).replace(/\./g, ':');
    };

    updateClock();
    setInterval(updateClock, 1000);
</script>
@endsection
