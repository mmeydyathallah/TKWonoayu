@extends('layouts.teacher')

@php
    $title = 'Catatan Anekdot - TK Wonoayu';
@endphp

@section('styles')
<x-assessment-module-theme />
<style>
    .gradient-primary { background: linear-gradient(135deg, #0060ad, #68abff); }
    .ambient-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
</style>
@endsection

@section('content')

{{-- TOP BAR --}}
<header class="assessment-header bg-white/90 backdrop-blur-2xl sticky top-0 z-30 border-b border-slate-100 shadow-sm flex items-center justify-between px-6 py-4 w-full -mx-8 mb-6 docked full-width">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white shadow-md">
            <span class="material-symbols-outlined text-[20px]">description</span>
        </div>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 font-headline leading-tight">Catatan Anekdot</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Dokumentasi Peristiwa Khusus Siswa</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- LEFT COLUMN: FORM INPUT --}}
        <div class="lg:col-span-1 bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden sticky top-24">
            <div class="assessment-form-header assessment-form-header-anecdotal px-6 py-5 border-b border-slate-50 bg-gradient-to-b from-slate-50/50 to-white">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]" id="anecdotal-form-icon">add_circle</span>
                    <span id="anecdotal-form-title">Tambah Catatan</span>
                </h3>
            </div>
            
            <form action="{{ route('guru.anecdotal.store') }}" method="POST" class="p-6 space-y-5" id="anecdotal-form">
                @csrf
                <input type="hidden" name="note_id" id="note_id">
                
                {{-- Student --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Siswa</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                        <select name="student_id" id="note_student_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-9 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                            <option value="">Pilih Siswa...</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">Kel. {{ $student->class_group }} - {{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                {{-- Date & Time --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Waktu Kejadian</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary text-[18px]">schedule</span>
                        <input name="recorded_at" id="note_recorded_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                {{-- Location --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Tempat/Lokasi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">location_on</span>
                        <input name="location" id="note_location" type="text" placeholder="Contoh: Taman Bermain" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium">
                    </div>
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">Deskripsi Peristiwa</label>
                    <textarea name="description" id="note_description" rows="5" placeholder="Tuliskan kejadian secara objektif..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none placeholder:text-slate-400 placeholder:font-medium resize-none"></textarea>
                </div>

                <div class="pt-4 space-y-2">
                    <button type="submit" class="w-full gradient-primary text-white py-3.5 rounded-xl text-sm font-extrabold shadow-lg shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span id="anecdotal-submit-label">Simpan Catatan</span>
                    </button>
                    <button type="button" id="anecdotal-cancel-edit" onclick="resetAnecdotalForm()" class="hidden w-full bg-slate-100 text-slate-600 py-3 rounded-xl text-sm font-extrabold hover:bg-slate-200 transition-all">
                        Batal Edit
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: LIST OF RECORDS --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Records List --}}
            <div class="bg-white rounded-3xl border border-slate-100 ambient-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">history</span> Riwayat Catatan
                    </h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('guru.anecdotal') }}" method="GET" id="date-filter-form" class="flex items-center gap-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Tanggal</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">calendar_today</span>
                                <input type="date" name="date" value="{{ $selectedDate }}" onchange="document.getElementById('date-filter-form').submit()" class="bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            @if($selectedDate)
                            <a href="{{ route('guru.anecdotal') }}" class="h-9 px-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-black hover:bg-slate-200 transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">restart_alt</span>
                                Reset
                            </a>
                            @endif
                        </form>
                        <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-200/50 px-2.5 py-1 rounded-md">{{ $notes->count() }} Data</span>
                    </div>
                </div>
                
                <div class="divide-y divide-slate-50">
                    @forelse($notes as $note)
                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $note->student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($note->student->full_name).'&background=e0efff&color=0060ad&bold=true&size=64' }}"
                                     class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100">
                                <div>
                                    <p class="text-sm font-bold text-slate-800 leading-tight">{{ $note->student->full_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-500 mt-0.5">
                                        Kel. {{ $note->student->class_group }} • {{ $note->recorded_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($note->location)
                                <span class="flex items-center gap-1 text-[10px] font-black text-slate-400 uppercase bg-slate-100 px-2 py-1 rounded-lg">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span> {{ $note->location }}
                                </span>
                                @endif
                                @php
                                    $notePayload = [
                                        'id' => $note->id,
                                        'student_id' => $note->student_id,
                                        'recorded_at' => optional($note->recorded_at)->format('Y-m-d\TH:i'),
                                        'location' => $note->location,
                                        'description' => $note->description,
                                    ];
                                @endphp
                                <script type="application/json" id="note-data-{{ $note->id }}">{!! json_encode($notePayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                                <button type="button" onclick="editAnecdotalFromScript({{ $note->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit catatan">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <form action="{{ route('guru.anecdotal.destroy', $note) }}" method="POST" onsubmit="return confirm('Hapus catatan anekdot ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Hapus catatan">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                            <p class="text-sm text-slate-700 font-medium leading-relaxed italic">"{{ $note->description }}"</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-16 text-center bg-slate-50/50">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                            <span class="material-symbols-outlined text-3xl text-slate-300">sticky_note_2</span>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Belum ada catatan anekdot</p>
                        <p class="text-[11px] font-medium text-slate-400 mt-1">Dokumentasikan peristiwa penting melalui formulir di kiri.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const defaultRecordedAt = @js(now()->format('Y-m-d\TH:i'));

    function editAnecdotalFromScript(noteId) {
        const script = document.getElementById(`note-data-${noteId}`);
        if (!script) return;
        editAnecdotal(JSON.parse(script.textContent));
    }

    function editAnecdotal(note) {
        document.getElementById('note_id').value = note.id || '';
        document.getElementById('note_student_id').value = note.student_id || '';
        document.getElementById('note_recorded_at').value = note.recorded_at || defaultRecordedAt;
        document.getElementById('note_location').value = note.location || '';
        document.getElementById('note_description').value = note.description || '';
        document.getElementById('anecdotal-form-title').textContent = 'Edit Catatan';
        document.getElementById('anecdotal-form-icon').textContent = 'edit_note';
        document.getElementById('anecdotal-submit-label').textContent = 'Update Catatan';
        document.getElementById('anecdotal-cancel-edit').classList.remove('hidden');
        document.getElementById('anecdotal-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetAnecdotalForm() {
        document.getElementById('note_id').value = '';
        document.getElementById('note_student_id').value = '';
        document.getElementById('note_recorded_at').value = defaultRecordedAt;
        document.getElementById('note_location').value = '';
        document.getElementById('note_description').value = '';
        document.getElementById('anecdotal-form-title').textContent = 'Tambah Catatan';
        document.getElementById('anecdotal-form-icon').textContent = 'add_circle';
        document.getElementById('anecdotal-submit-label').textContent = 'Simpan Catatan';
        document.getElementById('anecdotal-cancel-edit').classList.add('hidden');
    }
</script>
@endsection
