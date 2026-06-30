@php
    $fingerprintId = $student->fingerprint_id ?? null;
    $fingerprintEnrolled = !empty($fingerprintId);
@endphp

<div class="rounded-2xl border border-slate-500/20 bg-slate-950/28 p-5">
    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">fingerprint</span>
        Fingerprint
    </label>

    {{-- Status Display --}}
    <div id="fingerprintStatus" class="mb-4">
        @if($fingerprintEnrolled)
            <div class="flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4">
                <span class="material-symbols-outlined text-emerald-400 text-[24px]">check_circle</span>
                <div class="flex-1">
                    <p class="text-sm font-black text-emerald-300">Fingerprint Terdaftar</p>
                    <p class="text-[10px] font-bold text-slate-400">ID: {{ $fingerprintId }} @if($student->fingerprint_enrolled_at) • {{ $student->fingerprint_enrolled_at->diffForHumans() }} @endif</p>
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 rounded-xl bg-slate-500/10 border border-slate-500/20 p-4">
                <span class="material-symbols-outlined text-slate-400 text-[24px]">fingerprint</span>
                <div class="flex-1">
                    <p class="text-sm font-black text-slate-400">Belum Terdaftar</p>
                    <p class="text-[10px] font-bold text-slate-500">Klik tombol di bawah untuk mendaftarkan</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Pending Status (hidden by default, shown during polling) --}}
    <div id="fingerprintPending" class="hidden mb-4">
        <div class="flex items-center gap-3 rounded-xl bg-amber-500/10 border border-amber-500/20 p-4">
            <span class="material-symbols-outlined text-amber-400 text-[24px] animate-pulse">fingerprint</span>
            <div class="flex-1">
                <p class="text-sm font-black text-amber-300" id="fingerprintPendingText">Menunggu scan di perangkat...</p>
                <p class="text-[10px] font-bold text-slate-500" id="fingerprintPendingHint">Pastikan perangkat ESP32 menyala dan terhubung</p>
            </div>
            <button type="button" onclick="cancelFingerprintEnroll()" class="text-[10px] font-black text-rose-400 hover:text-rose-300 uppercase tracking-widest">Batal</button>
        </div>
    </div>

    {{-- Error Status (hidden by default) --}}
    <div id="fingerprintError" class="hidden mb-4">
        <div class="flex items-center gap-3 rounded-xl bg-rose-500/10 border border-rose-500/20 p-4">
            <span class="material-symbols-outlined text-rose-400 text-[24px]">error</span>
            <div class="flex-1">
                <p class="text-sm font-black text-rose-300" id="fingerprintErrorText">Enrollment gagal</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-2">
        @if($fingerprintEnrolled)
            <button type="button" onclick="requestFingerprintEnroll({{ $student->id ?? 'null' }})" class="flex-1 rounded-xl bg-sky-500/15 border border-sky-500/30 px-4 py-3 text-xs font-black text-sky-200 hover:bg-sky-500/25 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">autorenew</span> Re-enroll
            </button>
            <button type="button" onclick="requestFingerprintDelete({{ $student->id ?? 'null' }})" class="flex-1 rounded-xl bg-rose-500/15 border border-rose-500/30 px-4 py-3 text-xs font-black text-rose-200 hover:bg-rose-500/25 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
            </button>
        @else
            <button type="button" onclick="requestFingerprintEnroll({{ $student->id ?? 'null' }})" class="w-full rounded-xl bg-sky-500/15 border border-sky-500/30 px-4 py-3 text-xs font-black text-sky-200 hover:bg-sky-500/25 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">fingerprint</span> Daftarkan Fingerprint
            </button>
        @endif
    </div>
</div>

<script>
let fingerprintPollTimer = null;
let fingerprintEnrollmentId = null;

async function requestFingerprintEnroll(studentId) {
    if (!studentId) {
        alert('Simpan data siswa terlebih dahulu sebelum mendaftarkan fingerprint.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('student_id', studentId);
        const resp = await fetch('{{ route("api.fingerprint.enrollment.request") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await resp.json();

        if (!data.success) {
            alert(data.message || 'Gagal membuat permintaan enrollment.');
            return;
        }

        fingerprintEnrollmentId = data.data.enrollment_id;
        document.getElementById('fingerprintStatus').classList.add('hidden');
        document.getElementById('fingerprintError').classList.add('hidden');
        document.getElementById('fingerprintPending').classList.remove('hidden');
        document.getElementById('fingerprintPendingText').textContent = 'Menunggu scan di perangkat...';

        startFingerprintPoll();
    } catch (e) {
        alert('Error: ' + e.message);
    }
}

async function requestFingerprintDelete(studentId) {
    if (!studentId) return;
    if (!confirm('Hapus fingerprint siswa ini? Template akan dihapus dari perangkat.')) return;

    try {
        const formData = new FormData();
        formData.append('student_id', studentId);
        const resp = await fetch('{{ route("api.fingerprint.deletion.request") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await resp.json();

        if (data.success) {
            alert('Permintaan hapus dikirim. Menunggu eksekusi di perangkat.');
            setTimeout(() => location.reload(), 2000);
        } else {
            alert(data.message || 'Gagal.');
        }
    } catch (e) {
        alert('Error: ' + e.message);
    }
}

function startFingerprintPoll() {
    if (fingerprintPollTimer) clearInterval(fingerprintPollTimer);
    fingerprintPollTimer = setInterval(pollFingerprintStatus, 2000);
}

async function pollFingerprintStatus() {
    if (!fingerprintEnrollmentId) return;

    try {
        const resp = await fetch('/api/fingerprint/enrollment/status/' + fingerprintEnrollmentId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await resp.json();
        if (!data.success) return;

        const status = data.data.status;

        if (status === 'enrolled') {
            clearInterval(fingerprintPollTimer);
            fingerprintPollTimer = null;
            document.getElementById('fingerprintPending').classList.add('hidden');
            document.getElementById('fingerprintStatus').classList.remove('hidden');
            alert('Fingerprint berhasil terdaftar! ID: ' + data.data.fingerprint_id);
            location.reload();
        } else if (status === 'failed') {
            clearInterval(fingerprintPollTimer);
            fingerprintPollTimer = null;
            document.getElementById('fingerprintPending').classList.add('hidden');
            document.getElementById('fingerprintError').classList.remove('hidden');
            document.getElementById('fingerprintErrorText').textContent = data.data.error_message || 'Enrollment gagal';
        } else if (status === 'cancelled') {
            clearInterval(fingerprintPollTimer);
            fingerprintPollTimer = null;
            document.getElementById('fingerprintPending').classList.add('hidden');
            document.getElementById('fingerprintStatus').classList.remove('hidden');
        } else if (status === 'pending') {
            document.getElementById('fingerprintPendingText').textContent = 'Menunggu scan di perangkat...';
        }
    } catch (e) {
        // keep polling
    }
}

function cancelFingerprintEnroll() {
    if (fingerprintPollTimer) {
        clearInterval(fingerprintPollTimer);
        fingerprintPollTimer = null;
    }
    fingerprintEnrollmentId = null;
    document.getElementById('fingerprintPending').classList.add('hidden');
    document.getElementById('fingerprintStatus').classList.remove('hidden');
}
</script>
