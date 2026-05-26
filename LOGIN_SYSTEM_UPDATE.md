## Perubahan Login System - TK Wonoayu

### Ringkasan Perubahan
Sistem login telah diperbarui untuk menghapus fitur akses cepat dan mengharuskan login dengan validasi database.

### Perubahan yang Dilakukan:

#### 1. **Halaman Login** (`resources/views/auth/login.blade.php`)
- ❌ Dihapus: Tombol toggle role (Guru/Wali Murid)
- ❌ Dihapus: Link akses cepat di bawah form
- ✅ Ditambah: Pesan error handling untuk login gagal
- ✅ Diubah: Form method dari GET ke POST dengan CSRF protection

#### 2. **User Model** (`app/Models/User.php`)
- ✅ Ditambah: Kolom `role` dalam fillable array

#### 3. **Database Migration**
- ✅ Dibuat: `2026_04_24_120000_add_role_to_users_table.php`
- Menambahkan kolom `role` (enum: 'guru', 'wali_murid') dengan default 'guru'

#### 4. **Controller** (`app/Http/Controllers/PortalController.php`)
- ✅ Ditambah: Method `handleLogin()` - Validasi login dengan database
- ✅ Ditambah: Method `logout()` - Proses logout
- ✅ Ditambah: Import untuk `User`, `Auth`, dan `Hash`
- Fitur validasi:
  - Cari user berdasarkan email atau name
  - Validasi password dengan Hash checking
  - Redirect sesuai role (guru → guru.dashboard, wali_murid → wali.dashboard)

#### 5. **Routes** (`routes/web.php`)
- ✅ Ditambah: Route POST `/login` untuk `handleLogin`
- ✅ Ditambah: Route POST `/logout` untuk `logout`
- ✅ Ditambah: Middleware `auth` pada semua route guru dan wali murid
- ✅ Diubah: User yang tidak login akan diredirect ke halaman login

### Langkah Setup:

1. **Jalankan Migration**
```bash
php artisan migrate
```

2. **Buat User (Guru)**
Buka tinker:
```bash
php artisan tinker
```

Buat user guru:
```php
User::create([
    'name' => 'Bu Siti',
    'email' => 'guru@tkwonoayu.local',
    'password' => Hash::make('password123'),
    'role' => 'guru'
]);
```

Buat user wali murid:
```php
User::create([
    'name' => 'Ibu Sari',
    'email' => 'wali@tkwonoayu.local',
    'password' => Hash::make('password123'),
    'role' => 'wali_murid'
]);
```

### Cara Login:
1. Buka halaman `/login`
2. Masukkan username/email (name atau email dari database)
3. Masukkan password yang sudah di-hash
4. Sistem akan otomatis mengarahkan ke dashboard sesuai role

### Keamanan:
- ✅ Password di-hash dengan `Hash::make()`
- ✅ CSRF protection aktif di form
- ✅ Middleware auth melindungi dashboard
- ✅ Tidak ada akses langsung tanpa login
