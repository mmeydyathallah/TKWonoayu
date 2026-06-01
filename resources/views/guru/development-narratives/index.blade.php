@extends('layouts.teacher')

@php
    $title = 'Narasi Perkembangan - TK Wonoayu';
    $currentSchoolYear = now()->month >= 7 ? now()->year . '/' . (now()->year + 1) : (now()->year - 1) . '/' . now()->year;
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .gradient-narrative { background: linear-gradient(135deg, #0f766e, #0284c7); }
    .ambient-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
</style>
@endsection

@section('content')
<header class="assessment-header bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-6 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-narrative flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">description</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Narasi Perkembangan</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Ringkasan yang tampil di portal wali murid</p>
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
        <div class="lg:col-span-1 bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden sticky top-24">
            <div class="assessment-form-header px-6 py-5 border-b border-slate-50 gradient-narrative">
                <h3 id="form-title" class="font-extrabold text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-white text-[20px]">edit_note</span> Input Narasi
                </h3>
            </div>

            <form action="{{ route('guru.development-narrative.store') }}" method="POST" class="p-6 space-y-5" id="narrative-form">
                @csrf
                <input type="hidden" name="report_id" id="report_id" value="{{ old('report_id') }}">

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                        <select name="student_id" id="student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Pilih Siswa...</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" data-school-year="{{ $student->school_year }}" {{ (string) old('student_id', $selectedStudentId) === (string) $student->id ? 'selected' : '' }}>
                                Kel. {{ $student->class_group }} - {{ $student->full_name }}
                            </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Semester</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[18px]">school</span>
                            <select name="semester" id="semester" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                                @foreach(['Semester Ganjil', 'Semester Genap'] as $semester)
                                <option value="{{ $semester }}" {{ old('semester', 'Semester Ganjil') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Tahun Ajaran</label>
                        <input name="school_year" id="school_year" type="text" value="{{ old('school_year', $currentSchoolYear) }}" required placeholder="2025/2026" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Narasi Perkembangan Ananda</label>
                    <textarea name="summary" id="summary" rows="8" required placeholder="Tuliskan rangkuman perkembangan, capaian, kebiasaan positif, dan area yang perlu didampingi..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium resize-y">{{ old('summary') }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Pesan dari Wali Kelas</label>
                    <textarea name="teacher_note" id="teacher_note" rows="4" placeholder="Contoh: Mohon dilanjutkan pembiasaan membaca doa dan merapikan alat belajar di rumah." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium resize-y">{{ old('teacher_note') }}</textarea>
                </div>

                <div class="pt-2 flex flex-col gap-3">
                    <button type="submit" class="w-full gradient-narrative text-white py-3.5 rounded-xl text-sm font-extrabold shadow-lg shadow-cyan-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan & Sinkronkan
                    </button>
                    <button type="button" onclick="resetNarrativeForm()" class="w-full bg-slate-100 text-slate-600 py-3 rounded-xl text-xs font-extrabold hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span> Form Baru
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('guru.development-narrative') }}" method="GET" id="filter-form" class="bg-white rounded-2xl border border-slate-100 ambient-shadow p-4 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[220px] space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[16px]">search</span>
                        <select name="student_id" onchange="document.getElementById('filter-form').submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-9 pr-8 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Semua Siswa</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ (string) $selectedStudentId === (string) $student->id ? 'selected' : '' }}>Kel. {{ $student->class_group }} - {{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <a href="{{ route('guru.development-narrative') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-black hover:bg-slate-200 transition-colors">Reset</a>
            </form>

            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">history</span> Riwayat Narasi
                    </h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-200/50 px-2.5 py-1 rounded-md">{{ $reports->count() }} Data</span>
                </div>

                <div class="divide-y divide-slate-50">
                    @forelse($reports as $report)
                    @php
                        $payload = [
                            'id' => $report->id,
                            'student_id' => $report->student_id,
                            'semester' => $report->semester,
                            'school_year' => $report->school_year,
                            'summary' => $report->summary,
                            'teacher_note' => $report->teacher_note,
                        ];
                    @endphp
                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                        <div class="flex flex-col sm:flex-row gap-5 items-start">
                            <div class="flex items-center gap-3 w-full sm:w-1/3 flex-shrink-0">
                                <img src="{{ $report->student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($report->student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}"
                                     class="w-11 h-11 rounded-full object-cover ring-2 ring-slate-100">
                                <div class="truncate">
                                    <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $report->student->full_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-500 mt-0.5">Kel. {{ $report->student->class_group }} · {{ $report->school_year }}</p>
                                </div>
                            </div>

                            <div class="flex-1 space-y-3 w-full">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-cyan-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-cyan-700 border border-cyan-100">
                                        <span class="material-symbols-outlined text-[14px]">school</span>{{ $report->semester }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">Diperbarui {{ $report->updated_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                <p class="text-xs text-slate-600 font-semibold leading-relaxed whitespace-pre-line">{{ \Illuminate\Support\Str::limit($report->summary, 260) }}</p>
                                @if($report->teacher_note)
                                <p class="text-xs text-slate-500 italic leading-relaxed border-l-2 border-cyan-300 pl-3">{{ \Illuminate\Support\Str::limit($report->teacher_note, 160) }}</p>
                                @endif
                            </div>

                            <div class="flex sm:flex-col items-center gap-2 border-l border-slate-100 pl-4 ml-2 flex-shrink-0">
                                <script type="application/json" id="report-data-{{ $report->id }}">{!! json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                                <button type="button" onclick="editNarrativeFromScript({{ $report->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit narasi">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <form action="{{ route('guru.development-narrative.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus narasi perkembangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Hapus narasi">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-16 text-center bg-slate-50/50">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                            <span class="material-symbols-outlined text-3xl text-slate-300">description</span>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Belum ada narasi perkembangan</p>
                        <p class="text-[11px] font-medium text-slate-400 mt-1">Isi formulir di sebelah kiri agar muncul di portal wali murid.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const studentSelect = document.getElementById('student_id');
    const schoolYearInput = document.getElementById('school_year');

    function syncSchoolYearFromStudent(force = false) {
        if (!studentSelect || !schoolYearInput) return;
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];
        const schoolYear = selectedOption?.dataset?.schoolYear;
        if (schoolYear && (force || !schoolYearInput.value.trim())) {
            schoolYearInput.value = schoolYear;
        }
    }

    function setFormTitle(editing) {
        const title = document.getElementById('form-title');
        if (!title) return;
        title.innerHTML = editing
            ? '<span class="material-symbols-outlined text-white text-[20px]">edit</span> Edit Narasi'
            : '<span class="material-symbols-outlined text-white text-[20px]">edit_note</span> Input Narasi';
    }

    function editNarrative(data) {
        document.getElementById('report_id').value = data.id || '';
        document.getElementById('student_id').value = data.student_id || '';
        document.getElementById('semester').value = data.semester || 'Semester Ganjil';
        document.getElementById('school_year').value = data.school_year || '';
        document.getElementById('summary').value = data.summary || '';
        document.getElementById('teacher_note').value = data.teacher_note || '';
        setFormTitle(true);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function editNarrativeFromScript(id) {
        const script = document.getElementById(`report-data-${id}`);
        if (!script) return;
        editNarrative(JSON.parse(script.textContent));
    }

    function resetNarrativeForm() {
        document.getElementById('narrative-form').reset();
        document.getElementById('report_id').value = '';
        setFormTitle(false);
        syncSchoolYearFromStudent(true);
    }

    studentSelect?.addEventListener('change', () => syncSchoolYearFromStudent(true));
    syncSchoolYearFromStudent(false);
</script>
@endsection
