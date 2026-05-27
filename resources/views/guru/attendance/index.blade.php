@extends('layouts.teacher')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Absensi Siswa</h1>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('guru.attendance.store') }}">
        @csrf
        <div class="flex gap-3 items-center mb-4">
            <label class="font-bold">Tanggal</label>
            <input type="date" name="date" value="{{ 
                \Carbon\Carbon::parse($date)->format('Y-m-d') }}" class="border px-3 py-2 rounded" />
        </div>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="text-left">
                    <th class="p-2">#</th>
                    <th class="p-2">Nama</th>
                    <th class="p-2">Kelompok</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                @php $att = $attendances->get($student->id); @endphp
                <tr class="border-t">
                    <td class="p-2">{{ $loop->iteration }}</td>
                    <td class="p-2">{{ $student->full_name }}</td>
                    <td class="p-2">{{ $student->class_group }}</td>
                    <td class="p-2">
                        <select name="attendance[{{ $student->id }}][status]" class="border px-2 py-1 rounded">
                            @foreach(['hadir'=>'Hadir','izin'=>'Izin','sakit'=>'Sakit','alpa'=>'Alpa'] as $k=>$v)
                            <option value="{{ $k }}" {{ ($att?->status ?? 'alpa') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2"><input type="text" name="attendance[{{ $student->id }}][note]" value="{{ $att?->note }}" class="border px-2 py-1 rounded w-full" /></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button class="px-4 py-2 bg-primary text-white rounded">Simpan Absensi</button>
        </div>
    </form>
</div>
@endsection
