# PRD — Fitur Admin Panel TK Wonoayu Management System

| Field | Value |
|-------|-------|
| **Dokumen** | Product Requirements Document (PRD) |
| **Proyek** | TK Wonoayu Management System |
| **Fitur** | Admin Panel & Manajemen Sistem |
| **Versi** | 1.0 |
| **Tanggal** | 25 Juni 2026 |
| **Status** | Draft untuk Review |
| **Environment** | VPS 103.215.229.171 — `/var/www/TKWonoayu` |
| **Stack** | Laravel 13, PHP 8.3, MySQL, TailwindCSS 4 + DaisyUI 5 |

---

## 1. Latar Belakang

Saat ini sistem TK Wonoayu memiliki dua peran pengguna:

1. **Guru** — mengelola biodata siswa, penilaian harian, catatan anekdot, narasi perkembangan, absensi manual, agenda, dan pengaturan absensi.
2. **Wali Murid** — melihat dashboard anak, profil, laporan perkembangan, galeri aktivitas, riwayat absensi, agenda, dan status koneksi Telegram.

Belum ada peran khusus untuk **administrator sistem** yang bertugas mengelola keseluruhan operasional sekolah secara administratif — seperti manajemen akun pengguna, pengaturan master data, konfigurasi sistem, laporan lintas kelas, dan pengawasan aktivitas.

Kebutuhan ini muncul agar kepala sekolah / operator TK dapat:
- Mengawasi seluruh data sekolah dari satu dasbor terpadu.
- Mengelola akun guru dan wali murid (termasuk reset password).
- Mengonfigurasi pengaturan sekolah, token perangkat RFID, dan bot Telegram.
- Melihat laporan dan statistik lintas kelas.
- Membuat pengumuman dan siaran notifikasi ke seluruh wali murid.

---

## 2. Tujuan & Sasaran

### Tujuan
Menambahkan peran **Admin** ke dalam sistem TK Wonoayu dengan panel administrasi terpisah yang mencakup manajemen pengguna, master data, pengaturan sistem, pelaporan, dan pengawasan — tanpa mengganggu alur kerja guru dan wali murid yang sudah berjalan.

### Sasaran (Goals)
1. Admin dapat login menggunakan username `admin` dan password `admintkwonoayu`.
2. Admin memiliki dasbor khusus dengan ringkasan status seluruh sekolah.
3. Admin dapat mengelola seluruh akun pengguna (guru, wali murid, admin lain).
4. Admin dapat mengelola master data siswa, wali murid, dan kelompok kelas secara menyeluruh.
5. Admin dapat mengonfigurasi pengaturan sekolah, perangkat RFID, dan bot Telegram.
6. Admin dapat melihat laporan dan analitik lintas kelas serta periode.
7. Admin dapat membuat pengumuman dan siaran notifikasi massal.
8. Admin tetap dapat mengakses seluruh fitur guru (sebagai superuser).

### Non-Goals (Tidak Termasuk Scope Ini)
- Mengubah alur kerja penilaian guru yang sudah berjalan.
- Mengubah struktur data absensi RFID yang sudah berjalan.
- Membangun aplikasi mobile native terpisah.
- Sistem pembayaran SPP / keuangan sekolah (dapat menjadi fase mendatang).

---

## 3. Stakeholders & Pengguna

| Peran | Deskripsi | Login |
|-------|-----------|-------|
| **Admin** | Kepala sekolah / operator TK. Superuser yang dapat mengelola seluruh sistem dan juga melakukan semua yang bisa dilakukan guru. | Username: `admin`, Password: `admintkwonoayu`, Role: `admin` |
| **Guru** | Guru kelas yang melakukan penilaian, absensi, dan pengelolaan siswa. | Tetap seperti sekarang |
| **Wali Murid** | Orang tua/wali siswa yang memantau perkembangan anak. | Tetap seperti sekarang |

---

## 4. Arsitektur Peran & Hak Akses

### 4.1 Penambahan Role `admin`

Kolom `role` pada tabel `users` saat ini bertipe `VARCHAR` (sudah di-migrate dari `enum` ke `string` pada migration `2026_04_24_065144`), sehingga penambahan nilai `admin` **tidak memerlukan perubahan skema kolom** — cukup masukkan nilai `'admin'` ke kolom `role`.

### 4.2 Matriks Hak Akses

| Modul / Fitur | Admin | Guru | Wali Murid |
|---------------|:-----:|:----:|:----------:|
| Admin Dashboard | RW | — | — |
| Manajemen Pengguna (semua role) | RW | — | — |
| Manajemen Guru | RW | — | — |
| Manajemen Siswa (CRUD + bulk) | RW | RW* | R (anak sendiri) |
| Manajemen Wali Murid | RW | — | — |
| Manajemen Kelas & Tahun Ajaran | RW | — | — |
| Pengaturan Sekolah | RW | R** | — |
| Pengaturan Absensi (jam masuk/pulang) | RW | R** | — |
| Manajemen Token RFID | RW | — | — |
| Manajemen Bot Telegram | RW | R (statistik) | — |
| Laporan & Analitik Lintas Kelas | RW | R (kelas sendiri) | R (anak sendiri) |
| Pengumuman & Siaran Massal | RW | R | R |
| Agenda Sekolah | RW | RW | R |
| Audit Log | R | — | — |
| Penilaian Harian | RW | RW | R (anak sendiri) |
| Catatan Anekdot | RW | RW | R (anak sendiri) |
| Narasi Perkembangan | RW | RW | R (anak sendiri) |
| Absensi Manual | RW | RW | R (anak sendiri) |
| Profil & Password sendiri | RW | RW | RW |

> *Guru tetap bisa CRUD siswa seperti sekarang. Admin memiliki kemampuan tambahan: bulk import/export, naik kelas, lulus, arsip.
> **Guru dapat melihat pengaturan tetapi tidak mengubah (di-fase ini bisa tetap dibiarkan seperti sekarang, lihat catatan §10).

### 4.3 Akses Admin ke Fitur Guru

Admin adalah superuser — dapat mengakses **seluruh route `/guru/*`** selain route `/admin/*`. Implementasi: middleware `auth` pada grup route guru diperluas untuk menerima role `guru` **atau** `admin`. Dengan demikian admin dapat langsung melakukan penilaian, absensi, dll. tanpa switch role.

### 4.4 Alur Login

```
Halaman Login (/login)
   ├─ Pilih Peran: [Guru] [Wali Murid] [Admin]   ← tambahan opsi "Admin"
   ├─ Username: admin
   └─ Password: admintkwonoayu
         │
         ▼
   PortalController::handleLogin()
   ├─ Cari user by email OR name = "admin"
   ├─ Verifikasi password (Hash::check)
   ├─ Verifikasi role == 'admin' (sesuai pilihan form)
   ├─ Auth::login($user)
   └─ Redirect → /admin/dashboard
```

**Perubahan login:**
- Tambahkan opsi `admin` pada validasi `role` di `handleLogin()`: `'in:guru,wali_murid,admin'`.
- Tambahkan tombol/opsi "Admin" pada view `auth/login.blade.php`.
- Tambahkan redirect: `if ($user->role === 'admin') return redirect()->route('admin.dashboard')`.

---

## 5. Fitur Admin — Spesifikasi Detail

### 5.1 Admin Dashboard (`/admin/dashboard`)

Dasbor utama yang menampilkan ringkasan status seluruh sekolah.

**Tampilan:**
- **Kartu Statistik:**
  - Total Siswa Aktif (dengan breakdown per kelompok A/B)
  - Total Guru
  - Total Wali Murid (terdaftar + terhubung Telegram)
  - Kehadiran Hari Ini (jumlah hadir/izin/sakit/alpa/belum, persentase)
- **Grafik:**
  - Tren kehadiran 7 hari terakhir (line/bar chart)
  - Distribusi siswa per kelompok kelas (donut chart)
- **Status Sistem:**
  - Status bot Telegram (polling aktif/nonaktif, jumlah chat terhubung)
  - Status perangkat RFID (last activity timestamp, jumlah tap hari ini)
  - Status database (ukuran, jumlah record utama)
- **Aktivitas Terbaru:**
  - 10 aktivitas terakhir (siswa baru ditambah, penilaian dibuat, absensi RFID, login admin, dll.) — dari audit log
- **Akses Cepat:**
  - Shortcut ke modul-modul utama

### 5.2 Manajemen Pengguna (`/admin/pengguna`)

Mengelola seluruh akun pengguna sistem (semua role).

**Fitur:**
- **Daftar Pengguna** — tabel dengan kolom: Nama, Email/Username, Role, Status (aktif/nonaktif), Terakhir Login, Siswa Terhubung (untuk wali murid), Aksi.
  - Filter: berdasarkan role, status, kata kunci pencarian.
  - Pagination.
- **Tambah Pengguna** — form: nama, email, password (atau generate otomatis), role (guru/wali_murid/admin), kaitkan ke siswa (jika wali murid).
- **Edit Pengguna** — ubah nama, email, role, kaitan siswa.
- **Reset Password** — set password baru atau generate random + tampilkan sekali.
- **Aktifkan/Nonaktifkan Akun** — soft disable (tambah kolom `is_active` atau `disabled_at`).
- **Hapus Pengguna** — dengan konfirmasi; tidak boleh menghapus diri sendiri atau admin terakhir.
- **Aktivitas Login** — riwayat login per pengguna (dari audit log).

**Aturan:**
- Tidak boleh mengubah role akun admin sendiri menjadi non-admin (mencegah lockout).
- Minimal harus ada 1 admin aktif di sistem.
- Reset password wali murid juga dapat memperbarui kaitan ke siswa.

### 5.3 Manajemen Guru (`/admin/guru`)

Sub-modul khusus untuk mengelola akun guru.

**Fitur:**
- Daftar guru dengan statistik aktivitas (jumlah siswa dikelola, penilaian dibuat minggu ini, absensi ditandai minggu ini).
- Tambah/edit/hapus akun guru.
- Penugasan kelompok kelas (opsional: guru mengajar kelas A/B/semua).
- Lihat aktivitas guru (penilaian, absensi, catatan anekdot yang dibuat).
- Reset password guru.

### 5.4 Manajemen Siswa (`/admin/siswa`)

Versi diperluas dari manajemen siswa yang sudah dimiliki guru, dengan kemampuan administratif tambahan.

**Fitur (selain CRUD yang sudah ada):**
- **Bulk Import** — upload CSV/Excel untuk menambah banyak siswa sekaligus dengan validasi dan preview.
- **Bulk Export** — ekspor data siswa ke CSV/Excel (semua atau terfilter).
- **Naik Kelas (Promosi)** — pindahkan kelompok siswa dari kelas A → B atau ke tahun ajaran berikutnya secara massal.
- **Kelulusan/Arsip** — tandai siswa sebagai lulus/arsip (tidak dihapus, tetapi tidak tampil di daftar aktif).
- **Filter lanjutan** — berdasarkan kelompok, tahun ajaran, status (aktif/lulus/arsip), usia, jenis kelamin.
- **Lihat Profil Lengkap** — profil siswa + seluruh riwayat penilaian, absensi, catatan, laporan dalam satu halaman.
- **Manajemen RFID** — lihat/ubah kode RFID siswa, identifikasi duplikat lintas siswa.

### 5.5 Manajemen Wali Murid (`/admin/wali-murid`)

Mengelola profil wali murid dan kaitan akun.

**Fitur:**
- Daftar profil wali murid (dari `parent_profiles`) dengan info siswa terkait.
- Edit data wali (nama wali, nomor HP, data ayah/ibu).
- Kaitkan/lepaskan akun User wali murid ke siswa.
- Lihat status koneksi Telegram per wali.
- Putuskan koneksi Telegram (hapus `GuardianTelegramChat`) jika diperlukan.
- Cari wali berdasarkan nomor HP (untuk troubleshooting Telegram).

### 5.6 Manajemen Kelas & Tahun Ajaran (`/admin/akademik`)

Mengelola struktur akademik sekolah.

**Fitur:**
- **Kelompok Kelas** — saat ini hardcode A/B. Buat dapat dikonfigurasi: tambah/edit/hapus kelompok kelas (mis. A, B, C). Disimpan di tabel `class_groups` (baru) atau `school_settings`.
  - Catatan: saat ini `class_group` divalidasi `in:A,B` di beberapa controller — perlu disesuaikan agar membaca dari konfigurasi dinamis.
- **Tahun Ajaran** — kelola daftar tahun ajaran (mis. 2025/2026, 2026/2027). Tandai tahun ajaran aktif.
- **Semester** — kelola semester (Ganjil/Genap) per tahun ajaran.
- **Promosi Massal** — wizard untuk menaikkan seluruh siswa kelas A → B dan kelas B → lulus pada akhir tahun ajaran.
- **Riwayat Akademik** — arsip data per tahun ajaran.

### 5.7 Pengaturan Sekolah (`/admin/pengaturan`)

Konfigurasi sistem tingkat sekolah.

**Sub-modul:**

#### 5.7.1 Profil Sekolah
- Nama sekolah, NPSN, alamat, telepon, email, logo.
- Disimpan di tabel `school_settings` (key-value) atau tabel `school_profiles` (baru).
- Logo diupload dan disimpan di storage.

#### 5.7.2 Pengaturan Absensi
- Jam masuk default (saat ini 07:00) dan jam pulang (saatini 11:00).
- Window toleransi (saat ini 60 menit).
- Menggunakan `AttendanceSchedule::save()` yang sudah ada.
- Catatan: saat ini guru juga bisa mengubah ini di `/guru/pengaturan`. Pertimbangkan untuk memindahkan eksklusif ke admin (lihat §10).

#### 5.7.3 Token RFID
- Lihat token API saat ini (dari `config('services.rfid_attendance.token')` / `.env`).
- Regenerasi token baru → update `.env` atau `school_settings` + cache clear.
- Tampilkan instruksi update token di firmware ESP32.
- Riwayat tap RFID terakhir.

#### 5.7.4 Bot Telegram
- Konfigurasi bot token (dari `.env`).
- Konfigurasi webhook secret.
- Status polling (cek service `tkwonoayu-telegram-poll.service`).
- Restart polling service.
- Jumlah chat terhubung, jumlah yang sudah pilih siswa.
- Test kirim pesan ke chat tertentu.

### 5.8 Laporan & Analitik (`/admin/laporan`)

Pelaporan lintas kelas dan periode.

**Fitur:**
- **Laporan Kehadiran:**
  - Per kelas, per tanggal, per bulan, rentang custom.
  - Rekap: hadir/izin/sakit/alpa per siswa, persentase kehadiran.
  - Grafik tren harian/mingguan/bulanan.
  - Export PDF/Excel.
- **Laporan Penilaian:**
  - Tingkat penyelesaian penilaian harian per guru/kelas.
  - Distribusi skor (BB/MB/BSH/BSB) per domain intrakurikuler.
  - Progress siswa per periode.
- **Laporan Pendaftaran:**
  - Jumlah siswa aktif per tahun ajaran.
  - Demografi (jenis kelamin, agama, usia).
  - Tren pendaftaran year-over-year.
- **Laporan Aktivitas Sistem:**
  - Statistik penggunaan (login, penilaian dibuat, absensi dicatat).
  - Aktivitas RFID (jumlah tap per hari/bulan).
  - Notifikasi Telegram terkirim.

### 5.9 Pengumuman & Siaran Massal (`/admin/pengumuman`)

**Fitur:**
- **Pengumuman Sekolah** — CRUD `SchoolAnnouncement` (sudah ada modelnya):
  - Judul, konten, kategori, tanggal publish, status (draft/published).
  - Pengumuman tampil di dashboard wali murid.
- **Siaran Telegram Massal** — kirim pesan ke seluruh wali murid yang terhubung Telegram sekaligus:
  - Compose pesan (mendukung HTML formatting).
  - Pilih target: semua / per kelas / per siswa tertentu.
  - Preview jumlah penerima.
  - Kirim via `TelegramNotifier::sendMessage()` ke semua `GuardianTelegramChat`.
  - Log pengiriman (berhasil/gagal per penerima).
- **Riwayat Siaran** — daftar siaran yang pernah dikirim dengan status delivery.

### 5.10 Audit Log (`/admin/audit-log`)

Melacak aktivitas penting dalam sistem untuk akuntabilitas.

**Fitur:**
- Tabel log dengan kolom: Timestamp, Pengguna, Aksi (create/update/delete/login), Modul, Deskripsi, IP Address.
- Filter: berdasarkan pengguna, modul, rentang tanggal, jenis aksi.
- Pencarian teks.
- Export.
- Retention policy (simpan X hari, hapus otomatis via scheduled command).

**Event yang dicatat:**
- Login/logout (semua role).
- CRUD siswa, wali murid, pengguna.
- Perubahan pengaturan sekolah / token RFID / bot Telegram.
- Pengiriman siaran massal.
- Reset password.
- Promosi/kelulusan siswa.

**Implementasi:**
- Tabel baru `audit_logs` (id, user_id, user_name, action, module, description, ip_address, created_at).
- Laravel middleware atau event listener untuk auto-capture.
- Package opsional: `spatie/laravel-activitylog` atau custom implementation.

### 5.11 Manajemen Perangkat RFID (`/admin/perangkat`)

**Fitur:**
- Status perangkat (last activity, jumlah tap hari ini, total tap).
- Token API (lihat §5.7.3).
- Log tap RFID terakhir (dari `Attendance` dengan note "Absen otomatis RFID").
- Daftar siswa yang belum punya RFID terdaftar.
- Deteksi duplikat RFID (satu kode dipakai >1 siswa).
- Test endpoint API (kirim request test).

---

## 6. User Flow Utama

### Flow 1: Login Admin
```
/login → pilih "Admin" → masukkan "admin" / "admintkwonoayu"
  → handleLogin validasi → Auth::login → redirect /admin/dashboard
```

### Flow 2: Reset Password Wali Murid
```
/admin/pengguna → cari wali murid → klik "Reset Password"
  → input password baru (atau generate) → simpan
  → notifikasi sukses → password wali terupdate
```

### Flow 3: Promosi Massal Naik Kelas
```
/admin/akademik → "Promosi Massal" → pilih tahun ajaran baru
  → preview: Kelas A → B, Kelas B → Lulus
  → konfirmasi → eksekusi → ringkasan hasil
```

### Flow 4: Siaran Telegram Massal
```
/admin/pengumuman → "Siaran Baru" → compose pesan
  → pilih target (semua/kelas A/kelas B) → preview penerima (15 wali)
  → kirim → progress → hasil: 14 berhasil, 1 gagal
```

### Flow 5: Bulk Import Siswa
```
/admin/siswa → "Import" → upload CSV → preview data + validasi
  → perbaiki error → konfirmasi import → 25 siswa ditambahkan
```

---

## 7. Perubahan Teknis

### 7.1 Database — Migrasi Baru

| Migrasi | Tabel/Kolom | Tujuan |
|---------|-------------|--------|
| `2026_06_25_000001_add_is_active_to_users_table` | `users.is_active` (boolean, default true) | Aktifkan/nonaktifkan akun |
| `2026_06_25_000002_create_class_groups_table` | `class_groups` (id, name, description, sort_order, is_active) | Kelola kelompok kelas dinamis |
| `2026_06_25_000003_create_academic_years_table` | `academic_years` (id, year_label, is_active, start_date, end_date) | Manajemen tahun ajaran |
| `2026_06_25_000004_add_status_to_students_table` | `students.status` (enum: aktif, lulus, arsip; default aktif) | Status siswa |
| `2026_06_25_000005_create_audit_logs_table` | `audit_logs` (id, user_id, user_name, action, module, description, ip_address, created_at) | Audit trail |
| `2026_06_25_000006_add_login_fields_to_users_table` | `users.last_login_at`, `users.last_login_ip` | Tracking login |
| `2026_06_25_000007_create_broadcast_logs_table` | `broadcast_logs` (id, sender_id, message, target_type, total_recipients, success_count, fail_count, created_at) | Log siaran Telegram |
| `2026_06_25_000008_add_school_profile_to_settings` | Seed `school_settings` (nama_sekolah, npsn, alamat, telepon, email, logo) | Profil sekolah |

### 7.2 Seeder — Admin Account

Update `UserSeeder.php` (atau buat `AdminSeeder.php`):

```php
User::updateOrCreate(
    ['name' => 'admin'],
    [
        'email' => 'admin@tkwonoayu.sch.id',
        'password' => Hash::make('admintkwonoayu'),
        'role' => 'admin',
        'is_active' => true,
    ]
);
```

> Hapus atau ubah entri "Admin TK Wonoayu" lama yang masih role `guru` dengan password `admin123`.

### 7.3 Route Baru

```php
// routes/web.php

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen Pengguna
    Route::resource('pengguna', UserController::class);
    Route::post('/pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->name('pengguna.reset-password');
    Route::post('/pengguna/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('pengguna.toggle-active');

    // Manajemen Guru
    Route::resource('guru', GuruController::class);

    // Manajemen Siswa (admin-level)
    Route::get('/siswa', [AdminStudentController::class, 'index'])->name('siswa.index');
    Route::post('/siswa/import', [AdminStudentController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/export', [AdminStudentController::class, 'export'])->name('siswa.export');
    Route::post('/siswa/promote', [AdminStudentController::class, 'promote'])->name('siswa.promote');
    Route::post('/siswa/{student}/archive', [AdminStudentController::class, 'archive'])->name('siswa.archive');

    // Manajemen Wali Murid
    Route::resource('wali-murid', GuardianController::class);
    Route::delete('/wali-murid/{chat}/telegram', [GuardianController::class, 'disconnectTelegram'])->name('wali-murid.telegram.disconnect');

    // Akademik
    Route::resource('akademik/kelas', ClassGroupController::class);
    Route::resource('akademik/tahun-ajaran', AcademicYearController::class);

    // Pengaturan
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil-sekolah', [SettingController::class, 'updateSchoolProfile'])->name('settings.school');
    Route::post('/pengaturan/absensi', [SettingController::class, 'updateAttendance'])->name('settings.attendance');
    Route::post('/pengaturan/rfid-token', [SettingController::class, 'regenerateRfidToken'])->name('settings.rfid');
    Route::post('/pengaturan/telegram', [SettingController::class, 'updateTelegram'])->name('settings.telegram');

    // Laporan
    Route::get('/laporan/kehadiran', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/laporan/penilaian', [ReportController::class, 'assessment'])->name('reports.assessment');
    Route::get('/laporan/pendaftaran', [ReportController::class, 'enrollment'])->name('reports.enrollment');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    // Pengumuman & Siaran
    Route::resource('pengumuman', AnnouncementController::class);
    Route::get('/siaran/create', [BroadcastController::class, 'create'])->name('broadcast.create');
    Route::post('/siaran', [BroadcastController::class, 'store'])->name('broadcast.store');
    Route::get('/siaran', [BroadcastController::class, 'index'])->name('broadcast.index');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Perangkat RFID
    Route::get('/perangkat', [DeviceController::class, 'index'])->name('devices.index');

    // Profil & password admin sendiri
    Route::post('/pengaturan/profil', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/pengaturan/password', [SettingController::class, 'updatePassword'])->name('settings.password');
});
```

### 7.4 Modifikasi Route Existing

```php
// Grup guru — izinkan admin juga mengakses
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru,admin'])->group(function () {
    // ... route guru yang sudah ada ...
});
```

### 7.5 Middleware Baru

```php
// app/Http/Middleware/CheckRole.php
public function handle(Request $request, Closure $next, string ...$roles): mixed
{
    if (! in_array($request->user()?->role, $roles, true)) {
        abort(403, 'Akses ditolak.');
    }
    return $next($request);
}
```

Daftarkan di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    });
})
```

### 7.6 Modifikasi Login

**`PortalController::handleLogin()`** — perubahan:
```php
$validated = $request->validate([
    'username' => ['required', 'string'],
    'password' => ['required', 'string'],
    'role' => ['required', 'in:guru,wali_murid,admin'],  // ← tambah 'admin'
]);

// ... validasi user/password/role ...

// Redirect berdasarkan role
return match ($user->role) {
    'admin' => redirect()->route('admin.dashboard'),
    'guru' => redirect()->route('guru.dashboard'),
    default => redirect()->route('wali.dashboard'),
};
```

**`auth/login.blade.php`** — tambah tombol opsi "Admin".

### 7.7 Controller & View Baru

| Controller | Lokasi |
|------------|--------|
| `AdminController` | `app/Http/Controllers/Admin/AdminController.php` |
| `UserController` | `app/Http/Controllers/Admin/UserController.php` |
| `GuruController` | `app/Http/Controllers/Admin/GuruController.php` |
| `AdminStudentController` | `app/Http/Controllers/Admin/AdminStudentController.php` |
| `GuardianController` | `app/Http/Controllers/Admin/GuardianController.php` |
| `ClassGroupController` | `app/Http/Controllers/Admin/ClassGroupController.php` |
| `AcademicYearController` | `app/Http/Controllers/Admin/AcademicYearController.php` |
| `SettingController` | `app/Http/Controllers/Admin/SettingController.php` |
| `ReportController` | `app/Http/Controllers/Admin/ReportController.php` |
| `AnnouncementController` | `app/Http/Controllers/Admin/AnnouncementController.php` |
| `BroadcastController` | `app/Http/Controllers/Admin/BroadcastController.php` |
| `AuditLogController` | `app/Http/Controllers/Admin/AuditLogController.php` |
| `DeviceController` | `app/Http/Controllers/Admin/DeviceController.php` |

| View Directory | Isi |
|----------------|-----|
| `resources/views/admin/dashboard/` | Dasbor admin |
| `resources/views/admin/users/` | Manajemen pengguna |
| `resources/views/admin/guru/` | Manajemen guru |
| `resources/views/admin/students/` | Manajemen siswa (admin-level) |
| `resources/views/admin/guardians/` | Manajemen wali murid |
| `resources/views/admin/academic/` | Kelas & tahun ajaran |
| `resources/views/admin/settings/` | Pengaturan sekolah |
| `resources/views/admin/reports/` | Laporan & analitik |
| `resources/views/admin/announcements/` | Pengumuman |
| `resources/views/admin/broadcasts/` | Siaran Telegram |
| `resources/views/admin/audit-log/` | Audit log |
| `resources/views/admin/devices/` | Perangkat RFID |
| `resources/views/layouts/admin.blade.php` | Layout admin (sidebar + navbar) |

### 7.8 Model Baru

| Model | Tabel |
|-------|-------|
| `ClassGroup` | `class_groups` |
| `AcademicYear` | `academic_years` |
| `AuditLog` | `audit_logs` |
| `BroadcastLog` | `broadcast_logs` |

### 7.9 Service Baru

| Service | Tanggung Jawab |
|---------|----------------|
| `AuditLogger` | Mencatat aktivitas ke `audit_logs` (dipanggil dari middleware/event listener) |
| `BroadcastService` | Mengirim siaran Telegram massal (menggunakan `TelegramNotifier`) |
| `StudentImportService` | Parse & validasi CSV import siswa |
| `ReportExportService` | Generate export PDF/Excel laporan |

---

## 8. UI/UX

### 8.1 Layout Admin
- **Sidebar** kiri dengan menu terkelompok: Dashboard, Manajemen (Pengguna, Guru, Siswa, Wali Murid), Akademik (Kelas, Tahun Ajaran), Laporan, Pengumuman, Pengaturan, Audit Log, Perangkat.
- **Navbar** atas: nama sekolah/logo, notifikasi, profil admin (dropdown: profil, ganti password, logout).
- **Tema:** konsisten dengan tema dark existing (TailwindCSS + DaisyUI), namun dengan accent color berbeda (mis. amber/orange) untuk membedakan dari panel guru.
- **Responsive:** sidebar collapse menjadi hamburger menu di mobile.

### 8.2 Komponen Reusable
- Tabel data dengan sorting, filtering, pagination (DaisyUI table).
- Modal konfirmasi untuk aksi destruktif.
- Toast notification untuk feedback sukses/error.
- Empty state illustration untuk data kosong.
- Loading spinner untuk proses async (import, broadcast).

### 8.3 Breadcrumb
Setiap halaman menampilkan breadcrumb: `Dashboard > [Modul] > [Sub-modul]`.

---

## 9. Security Considerations

1. **Middleware role check** — semua route `/admin/*` dilindungi middleware `role:admin`. Guru/wali murid yang mencoba akses → 403.
2. **Self-protection** — admin tidak bisa menonaktifkan/menghapus akun sendiri atau mengubah role sendiri keluar dari admin.
3. **Minimum admin** — sistem harus selalu memiliki minimal 1 admin aktif. Operasi yang akan menghasilkan 0 admin ditolak.
4. **Audit trail** — semua aksi admin dicatat di `audit_logs` (termasuk reset password, perubahan pengaturan, hapus data).
5. **Password policy** — password minimum 8 karakter. Reset password menghasilkan password random yang ditampilkan sekali saja.
6. **CSRF** — semua form POST menggunakan `@csrf` (standar Laravel).
7. **Rate limiting** — endpoint import dan broadcast dibatasi untuk mencegah abuse.
8. **Token RFID** — regenerasi token menggantikan token lama; perangkat harus di-update. Token disimpan di `school_settings` (bukan hardcoded di `.env`) agar bisa dirotasi tanpa edit file.
9. **Telegram bot token** — hanya tampilkan masked (sebagian) di UI; tidak pernah tampilkan full token.
10. **Initial password** — password `admintkwonoayu` wajib diganti setelah login pertama (opsional: enforce change on first login).

---

## 10. Catatan & Keputusan Desain

1. **Pengaturan absensi (jam masuk/pulang):** Saat ini guru dapat mengubah di `/guru/pengaturan`. Opsi:
   - **(A)** Pindahkan eksklusif ke admin — guru hanya bisa lihat.
   - **(B)** Biarkan guru tetap bisa ubah, admin juga bisa.
   - **Rekomendasi: (A)** — agar pengaturan terpusat di admin. Guru melihat jam masuk/pulang sebagai info read-only.

2. **Kelompok kelas A/B:** Saat ini hardcode di validasi (`in:A,B`). Dengan tabel `class_groups`, validasi harus membaca dari DB. Memerlukan update di `PortalController::storeStudent()` dan `updateStudent()`.

3. **Token RFID di .env vs DB:** Saat ini `RFID_ATTENDANCE_TOKEN` ada di `.env`. Untuk rotasi via UI, pindahkan ke `school_settings` dan update `config/services.php` untuk membaca dari DB dengan fallback ke `.env`.

4. **Audit log implementation:** Bisa menggunakan package `spatie/laravel-activitylog` (cepat, teruji) atau custom event listener (lebih ringan, tanpa dependency). Rekomendasi: custom untuk kontrol penuh dan minim dependency.

5. **Broadcast Telegram:** Mengirim ratusan pesan sekaligus dapat memakan waktu. Implementasi via queued job (Laravel Queue) agar tidak timeout. Pastikan `queue:work` berjalan (saat ini belum ada worker — perlu setup).

6. **Login form — eksposur opsi Admin:** Menampilkan opsi "Admin" di halaman login publik adalah trade-off UX vs security. Alternatif: admin login via URL terpisah (mis. `/admin/login`) tanpa opsi di form publik. Rekomendasi: tetap di form utama untuk kesederhanaan (sistem TK, risiko rendah), namun tambahkan rate limiting pada endpoint login.

7. **Backup database:** Tidak ada mekanisme backup otomatis saat ini. Sebaiknya tambahkan scheduled artisan command untuk mysqldump berkala (di luar scope PRD ini, tapi direkomendasikan sebagai pekerjaan paralel).

---

## 11. Non-Functional Requirements

| Aspek | Requirement |
|-------|-------------|
| **Performance** | Halaman dashboard < 2 detik load. Laporan dengan data 1 tahun < 5 detik. Import 100 siswa < 10 detik. |
| **Scalability** | Mendukung hingga 500 siswa, 50 guru, 500 wali murid tanpa degradasi signifikan. |
| **Availability** | Mengikuti VPS uptime existing. Tidak ada requirement HA khusus. |
| **Browser** | Chrome, Firefox, Safari, Edge (latest 2 versions). Mobile responsive. |
| **Localization** | Bahasa Indonesia (sesuai existing). |
| **Accessibility** | Kontras warna cukup, keyboard navigable, label form jelas. |
| **Logging** | Error dicatat ke Laravel log. Audit log persisten. |
| **Backup** | (Direkomendasikan) mysqldup harian via cron/scheduler. |

---

## 12. Roadmap & Fase Pengembangan

### Fase 1 — Fondasi Admin (MVP)
**Prioritas: HIGHEST**
- [ ] Migrasi: `is_active`, `audit_logs`, `last_login_at/ip`, seeder admin account
- [ ] Middleware `role`, modifikasi login (tambah opsi admin + redirect)
- [ ] Layout admin (sidebar, navbar)
- [ ] Admin Dashboard (statistik + status sistem)
- [ ] Manajemen Pengguna (CRUD + reset password + aktifkan/nonaktifkan)
- [ ] Audit Log (logging dasar)
- [ ] Akses admin ke route guru

### Fase 2 — Manajemen Data
**Prioritas: HIGH**
- [ ] Manajemen Siswa (admin-level: filter lanjutan, profil lengkap, manajemen RFID)
- [ ] Manajemen Wali Murid (edit profil, kaitan akun, disconnect Telegram)
- [ ] Manajemen Guru (CRUD + statistik aktivitas)
- [ ] Pengaturan Sekolah (profil sekolah, absensi, pindahkan dari guru)

### Fase 3 — Akademik & Laporan
**Prioritas: MEDIUM**
- [ ] Manajemen Kelas dinamis (class_groups table)
- [ ] Manajemen Tahun Ajaran
- [ ] Promosi massal & kelulusan
- [ ] Laporan Kehadiran (dengan export)
- [ ] Laporan Penilaian
- [ ] Laporan Pendaftaran

### Fase 4 — Komunikasi & Integrasi
**Prioritas: MEDIUM**
- [ ] Pengumuman sekolah (CRUD)
- [ ] Siaran Telegram massal (dengan queue worker)
- [ ] Manajemen Bot Telegram (konfigurasi, status, test)
- [ ] Manajemen Token RFID (regenerasi)

### Fase 5 — Fitur Lanjutan
**Prioritas: LOW**
- [ ] Bulk import/export siswa (CSV)
- [ ] Audit log lengkap (semua event, retention policy)
- [ ] Manajemen Perangkat RFID (monitoring detail)
- [ ] Enforce change password on first login
- [ ] Database backup otomatis (scheduled command)

---

## 13. Acceptance Criteria

### Umum
- [ ] Admin dapat login dengan username `admin` / password `admintkwonoayu` dan diarahkan ke `/admin/dashboard`.
- [ ] Guru/wali murid yang mencoba mengakses `/admin/*` mendapat 403.
- [ ] Admin dapat mengakses seluruh fitur di `/guru/*` tanpa error.
- [ ] Semua aksi admin tercatat di audit log.

### Manajemen Pengguna
- [ ] Admin dapat membuat, mengedit, menghapus akun pengguna.
- [ ] Admin dapat reset password pengguna lain.
- [ ] Admin dapat menonaktifkan akun; akun nonaktif tidak dapat login.
- [ ] Admin tidak dapat menonaktifkan/menghapus akun sendiri.
- [ ] Sistem mencegah penghapusan admin terakhir.

### Dashboard
- [ ] Dashboard menampilkan total siswa, guru, wali murid, dan statistik kehadiran hari ini.
- [ ] Status Telegram dan RFID ditampilkan real-time.
- [ ] Grafik tren kehadiran 7 hari terakhir tampil dengan benar.

### Laporan
- [ ] Laporan kehadiran dapat difilter per kelas dan rentang tanggal.
- [ ] Laporan dapat diekspor ke PDF/Excel.
- [ ] Data laporan akurat sesuai database.

### Siaran Telegram
- [ ] Admin dapat compose pesan dan memilih target (semua/per kelas).
- [ ] Sistem menampilkan jumlah penerima sebelum kirim.
- [ ] Pesan terkirim ke seluruh chat Telegram yang terhubung.
- [ ] Log pengiriman mencatat berhasil/gagal per penerima.

---

## 14. Open Questions

1. Apakah pengaturan jam absensi (masuk/pulang) dipindahkan eksklusif ke admin, atau guru tetap bisa mengubah? *(Rekomendasi: pindah ke admin)*
2. Apakah perlu login URL terpisah untuk admin (`/admin/login`) demi keamanan, atau cukup opsi di form login utama? *(Rekomendasi: form utama)*
3. Apakah password `admintkwonoayu` perlu diubah setelah login pertama (enforce), atau dibiarkan? *(Rekomendasi: enforce)*
4. Berapa lama audit log disimpan sebelum dihapus otomatis? *(Rekomendasi: 90 hari)*
5. Apakah bulk import siswa perlu mendukung format Excel (.xlsx) selain CSV? *(Rekomendasi: CSV dulu, Excel di Fase 5)*
6. Apakah perlu sistem backup database otomatis sebagai bagian dari fitur admin, atau ditangani terpisah di level server? *(Rekomendasi: terpisah, tapi direkomendasikan segera)*

---

*Dokumen ini akan diperbarui setelah review dan sebelum implementasi dimulai.*
