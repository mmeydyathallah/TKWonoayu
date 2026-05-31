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
        'belum' => ['label' => 'Belum', 'icon' => 'pending_actions', 'dot' => 'bg-base-300'],
    ];
@endphp

@section('styles')
<style>
    .attendance-page {
        --attendance-panel: rgba(15, 23, 42, 0.88);
        --attendance-panel-soft: rgba(30, 41, 59, 0.78);
        --attendance-panel-muted: rgba(14, 165, 233, 0.11);
        --attendance-border: rgba(56, 189, 248, 0.28);
        --attendance-border-soft: rgba(148, 163, 184, 0.18);
        --attendance-text: #e5eefb;
        --attendance-muted: #a9b8cc;
    }

    .attendance-page .attendance-panel {
        background:
            linear-gradient(135deg, rgba(14, 165, 233, 0.10), transparent 42%),
            var(--attendance-panel) !important;
        border-color: var(--attendance-border) !important;
        box-shadow: 0 18px 42px rgba(2, 6, 23, 0.28) !important;
    }

    .attendance-page .attendance-panel-soft {
        background: var(--attendance-panel-soft) !important;
        border-color: var(--attendance-border-soft) !important;
    }

    .attendance-page .attendance-stat {
        background:
            linear-gradient(180deg, rgba(56, 189, 248, 0.14), rgba(15, 23, 42, 0.92)) !important;
        border-color: var(--attendance-border) !important;
        color: var(--attendance-text) !important;
    }

    .attendance-page .attendance-table-head {
        background: rgba(14, 165, 233, 0.18) !important;
    }

    .attendance-page .attendance-chip {
        background: rgba(56, 189, 248, 0.12) !important;
        border: 1px solid rgba(56, 189, 248, 0.20) !important;
        color: var(--attendance-muted) !important;
    }

    .attendance-page .attendance-radio {
        background: rgba(15, 23, 42, 0.72) !important;
        border-color: rgba(148, 163, 184, 0.22) !important;
    }
</style>
@endsection

@section('content')
<div class="attendance-page max-w-7xl mx-auto space-y-6">
    <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-black uppercase tracking-widest text-primary mb-3">
                <span class="material-symbols-outlined text-[16px]">event_available</span>
                Absensi Harian
            </div>
            <h1 class="font-headline text-3xl md:text-4xl font-black text-on-surface">Absensi Siswa</h1>
            <p class="text-sm text-on-surface-variant mt-2">
                Input kehadiran {{ $selectedDateLabel }} akan langsung muncul di portal wali murid.
            </p>
        </div>

        <div class="flex flex-col items-stretch lg:items-end gap-3">
            <div class="inline-flex self-start lg:self-end items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1.5 text-xs font-black text-primary shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                <span id="current-clock">{{ now()->format('H:i:s') }}</span>
            </div>
            <div class="attendance-panel card bg-base-200 border border-primary/20 shadow-sm rounded-2xl p-3">
                <form id="attendance-date-filter" method="GET" action="{{ route('guru.attendance.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <label class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-on-surface-variant">calendar_today</span>
                        <input id="attendance-date-input" type="date" name="date" value="{{ $dateValue }}" class="input input-bordered w-full sm:w-44 rounded-xl py-3 pl-10 pr-3 text-sm font-bold">
                    </label>
                    <label class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-on-surface-variant">groups</span>
                        <select name="group" class="select select-bordered w-full sm:w-56 rounded-xl py-3 pl-10 pr-9 text-sm font-bold">
                            <option value="">Semua kelompok</option>
                            @foreach($classGroups as $classGroup)
                                <option value="{{ $classGroup }}" @selected($group === $classGroup)>{{ $classGroup }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="btn btn-primary rounded-xl text-sm font-black">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Terapkan
                    </button>
                    @if($isCustomDate)
                        <a href="{{ route('guru.attendance.index', array_filter(['group' => $group])) }}" class="btn btn-outline rounded-xl text-sm font-black">
                            <span class="material-symbols-outlined text-[18px]">today</span>
                            Hari ini
                        </a>
                    @endif
                </form>
                <p class="mt-2 flex items-center gap-1.5 px-1 text-[11px] font-bold text-on-surface-variant">
                    <span class="material-symbols-outlined text-[15px]">{{ $isCustomDate ? 'edit_calendar' : 'today' }}</span>
                    {{ $isCustomDate ? 'Tanggal manual' : 'Default hari ini' }}: {{ $isCustomDate ? $selectedDateLabel : $todayDateLabel }}
                </p>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success rounded-2xl text-sm font-bold">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <section class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="attendance-stat stat rounded-2xl bg-base-200 border border-primary/20 p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-on-surface-variant">Tercatat</p>
            <p class="mt-2 text-3xl font-black text-on-surface">{{ $recordedCount }}/{{ $totalStudents }}</p>
            <p class="mt-1 text-xs font-bold text-on-surface-variant">Data pada tanggal ini</p>
        </div>
        @foreach($displayStatusMeta as $key => $meta)
            <div class="attendance-stat stat rounded-2xl bg-base-200 border border-primary/20 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-black uppercase tracking-widest text-on-surface-variant">{{ $meta['label'] }}</p>
                    <span class="h-3 w-3 rounded-full {{ $meta['dot'] }}"></span>
                </div>
                <p class="mt-2 text-3xl font-black text-on-surface">{{ $statusCounts[$key] ?? 0 }}</p>
                <p class="mt-1 text-xs font-bold text-on-surface-variant">Siswa</p>
            </div>
        @endforeach
    </section>

    <form method="POST" action="{{ route('guru.attendance.store') }}" class="attendance-panel card bg-base-100 rounded-2xl border border-primary/20 shadow-sm overflow-hidden">
        @csrf
        <input id="attendance-submit-date" type="hidden" name="date" value="{{ $dateValue }}">
        <input type="hidden" name="group" value="{{ $group }}">

        <div class="attendance-panel-soft flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-primary/15 bg-base-200/70 px-5 py-4">
            <div>
                <h2 class="font-headline text-lg font-black text-on-surface">Daftar Kehadiran</h2>
                <p class="text-xs font-bold text-on-surface-variant">{{ $selectedDateLabel }} - siswa belum dipilih akan disimpan sebagai Alpa.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($statusMeta as $key => $meta)
                        <button type="button" data-set-status="{{ $key }}" class="btn btn-outline btn-sm rounded-xl text-xs font-black">
                            Semua {{ $meta['label'] }}
                        </button>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Absensi
                </button>
            </div>
        </div>

        @if($students->count())
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full min-w-[860px] text-sm">
                    <thead class="attendance-table-head bg-primary/10 text-left">
                        <tr class="text-[11px] font-black uppercase tracking-widest text-on-surface-variant">
                            <th class="px-5 py-4 w-12">No</th>
                            <th class="px-5 py-4">Siswa</th>
                            <th class="px-5 py-4 w-52">Kelompok</th>
                            <th class="px-5 py-4 w-40">Masuk</th>
                            <th class="px-5 py-4 w-40">Pulang</th>
                            <th class="px-5 py-4 w-72">Status</th>
                            <th class="px-5 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/10">
                        @foreach($students as $student)
                            @php
                                $att = $attendances->get($student->id);
                                $rowStatus = $att?->status ?? 'belum';
                                $rowMeta = $displayStatusMeta[$rowStatus] ?? $displayStatusMeta['belum'];
                            @endphp
                            <tr class="hover:bg-primary/5 transition-colors">
                                <td class="px-5 py-4 font-black text-on-surface-variant">
                                    <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}" class="h-11 w-11 rounded-xl object-cover ring-1 ring-base-300" alt="{{ $student->full_name }}">
                                        <div>
                                            <p class="font-black text-on-surface">{{ $student->full_name }}</p>
                                            <p class="text-xs font-bold text-on-surface-variant">{{ $student->nickname ?: $student->student_no }}</p>
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-base-200 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-on-surface-variant">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $rowMeta['dot'] }}"></span>
                                                {{ $rowMeta['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-bold text-on-surface-variant">{{ $student->class_group }}</td>
                                <td class="px-5 py-4">
                                    <span class="attendance-chip inline-flex items-center gap-1 rounded-lg bg-base-200 px-3 py-1.5 text-xs font-black text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[15px]">login</span>
                                        {{ $att?->check_in_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="attendance-chip inline-flex items-center gap-1 rounded-lg bg-base-200 px-3 py-1.5 text-xs font-black text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[15px]">logout</span>
                                        {{ $att?->check_out_at?->format('H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($statusMeta as $key => $meta)
                                            <label class="cursor-pointer">
                                                <input class="peer sr-only attendance-status" type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $key }}" @checked($att?->status === $key)>
                                                <span class="attendance-radio flex items-center justify-center rounded-xl px-3 py-2 text-xs font-black ring-1 ring-base-300 text-on-surface-variant peer-checked:ring-2 {{ $meta['checked'] }}">
                                                    {{ $meta['label'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <input type="text" name="attendance[{{ $student->id }}][note]" value="{{ $att?->note }}" placeholder="Opsional" class="input input-bordered w-full rounded-xl text-sm font-bold">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-5xl text-base-content/30">groups</span>
                <p class="mt-3 font-black text-on-surface">Tidak ada siswa pada filter ini.</p>
                <p class="text-sm text-on-surface-variant">Ubah kelompok atau tambahkan data siswa terlebih dahulu.</p>
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
