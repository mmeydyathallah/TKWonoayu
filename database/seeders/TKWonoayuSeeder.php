<?php

namespace Database\Seeders;

use App\Models\AnecdotalNote;
use App\Models\Artwork;
use App\Models\ChecklistAssessment;
use App\Models\DailyAssessment;
use App\Models\DevelopmentReport;
use App\Models\ParentProfile;
use App\Models\SchoolActivity;
use App\Models\SchoolAnnouncement;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class TKWonoayuSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $siti = Student::create([
            'student_no' => '2023045',
            'nisn' => '3124567890',
            'class_group' => 'Kelompok B - Mawar',
            'school_year' => '2025/2026',
            'full_name' => 'Siti Aisyah Putri Permata',
            'nickname' => 'Siti Aisyah',
            'nik' => '3578012345678901',
            'birth_place' => 'Madiun',
            'birth_date' => '2018-08-15',
            'gender' => 'Perempuan',
            'religion' => 'Islam',
            'sibling_order' => 2,
            'siblings_total' => 3,
            'address' => 'Jl. Pahlawan No. 45, Kel. Pangongangan, Kec. Manguharjo, Kota Madiun',
            'distance_to_school_km' => 2.5,
            'avatar_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=320&q=80',
        ]);

        $bima = Student::create([
            'student_no' => '2023046',
            'nisn' => '3124567891',
            'class_group' => 'Kelompok B - Matahari',
            'school_year' => '2025/2026',
            'full_name' => 'Bima Arya Surya',
            'nickname' => 'Bima Arya',
            'nik' => '3578012345678902',
            'birth_place' => 'Madiun',
            'birth_date' => '2018-06-01',
            'gender' => 'Laki-laki',
            'religion' => 'Islam',
            'sibling_order' => 1,
            'siblings_total' => 2,
            'address' => 'Jl. Mayjen Sungkono No. 11, Kota Madiun',
            'distance_to_school_km' => 3.1,
            'avatar_url' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=320&q=80',
        ]);

        $raka = Student::create([
            'student_no' => '2023047',
            'nisn' => '3124567892',
            'class_group' => 'Kelompok A - Bintang',
            'school_year' => '2025/2026',
            'full_name' => 'Raka Putra Pratama',
            'nickname' => 'Raka Putra',
            'birth_place' => 'Madiun',
            'birth_date' => '2019-01-17',
            'gender' => 'Laki-laki',
            'religion' => 'Islam',
            'sibling_order' => 1,
            'siblings_total' => 1,
            'address' => 'Jl. Soekarno Hatta No. 10, Kota Madiun',
            'distance_to_school_km' => 1.7,
            'avatar_url' => 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?auto=format&fit=crop&w=320&q=80',
        ]);

        ParentProfile::create([
            'student_id' => $siti->id,
            'guardian_name' => 'Ibu Siti',
            'guardian_email' => 'ibu.siti@example.com',
            'guardian_phone' => '081345678901',
            'father_name' => 'Budi Santoso',
            'father_nik' => '3578011111111111',
            'father_job' => 'Wiraswasta',
            'father_phone' => '081234567890',
            'mother_name' => 'Sri Wahyuni',
            'mother_nik' => '3578012222222222',
            'mother_job' => 'Ibu Rumah Tangga',
            'mother_phone' => '081356789012',
        ]);

        ParentProfile::create([
            'student_id' => $bima->id,
            'guardian_name' => 'Ibu Rani',
            'guardian_email' => 'ibu.rani@example.com',
            'guardian_phone' => '081239876543',
            'father_name' => 'Andri Surya',
            'father_job' => 'Karyawan Swasta',
            'mother_name' => 'Rani Sulastri',
            'mother_job' => 'Wiraswasta',
        ]);

        ParentProfile::create([
            'student_id' => $raka->id,
            'guardian_name' => 'Ibu Diah',
            'guardian_phone' => '081298765432',
            'father_name' => 'Hadi Pratama',
            'father_job' => 'PNS',
            'mother_name' => 'Diah Lestari',
            'mother_job' => 'Guru',
        ]);

        $today = CarbonImmutable::today();

        SchoolActivity::insert([
            [
                'title' => 'Pembiasaan',
                'description' => 'Salam pagi, berdoa, dan menyanyikan lagu nasional',
                'starts_at' => $today->setTime(7, 30),
                'color' => 'secondary',
                'icon' => 'self_improvement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Agama',
                'description' => 'Menghafal surat pendek dan doa harian',
                'starts_at' => $today->setTime(8, 0),
                'color' => 'tertiary',
                'icon' => 'menu_book',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fisik Motorik',
                'description' => 'Permainan keseimbangan dan koordinasi gerak',
                'starts_at' => $today->setTime(9, 0),
                'color' => 'primary',
                'icon' => 'sports_gymnastics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        SchoolAnnouncement::create([
            'title' => 'Pekan Budaya Nusantara',
            'content' => 'Mohon siapkan pakaian adat untuk kegiatan hari Jumat.',
            'category' => 'Kegiatan Sekolah',
            'published_on' => $today,
        ]);

        DailyAssessment::insert([
            [
                'student_id' => $bima->id,
                'assessed_on' => $today,
                'class_group' => $bima->class_group,
                'activity' => 'Kegiatan Pagi',
                'aspect_code' => 'SOSEM',
                'aspect_name' => 'Sosial Emosional',
                'score_label' => 'BSH',
                'score_value' => 3,
                'observation' => 'Menunggu giliran berbicara dan membantu temannya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $siti->id,
                'assessed_on' => $today,
                'class_group' => $siti->class_group,
                'activity' => 'Melukis dengan Jari',
                'aspect_code' => 'SENI',
                'aspect_name' => 'Seni',
                'score_label' => 'BSB',
                'score_value' => 4,
                'observation' => 'Sangat antusias mencoba kombinasi warna.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $raka->id,
                'assessed_on' => $today,
                'class_group' => $raka->class_group,
                'activity' => 'Membangun Balok',
                'aspect_code' => 'KOG',
                'aspect_name' => 'Kognitif',
                'score_label' => 'MB',
                'score_value' => 2,
                'observation' => 'Mulai memahami konsep tinggi-rendah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        ChecklistAssessment::insert([
            [
                'student_id' => $bima->id,
                'assessed_on' => $today,
                'domain_code' => 'NAM',
                'domain_name' => 'Nilai Agama dan Moral',
                'indicator' => 'Mengenal kegiatan ibadah',
                'score_label' => 'BSH',
                'score_value' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $bima->id,
                'assessed_on' => $today,
                'domain_code' => 'KOG',
                'domain_name' => 'Kognitif',
                'indicator' => 'Menyusun pola sederhana',
                'score_label' => 'BSB',
                'score_value' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $siti->id,
                'assessed_on' => $today,
                'domain_code' => 'FM',
                'domain_name' => 'Fisik Motorik',
                'indicator' => 'Menggunakan alat mewarnai',
                'score_label' => 'BSH',
                'score_value' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $raka->id,
                'assessed_on' => $today,
                'domain_code' => 'BHS',
                'domain_name' => 'Bahasa',
                'indicator' => 'Menceritakan pengalaman',
                'score_label' => 'MB',
                'score_value' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Artwork::insert([
            [
                'student_id' => $bima->id,
                'title' => 'Melukis Bebas "Keluargaku"',
                'description' => 'Bima menggambarkan keluarga dan rumah dengan warna cerah.',
                'aspects' => 'Kreativitas, Motorik Halus',
                'score_label' => 'BSB',
                'score_value' => 4,
                'status' => 'dinilai',
                'image_url' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=900&q=80',
                'created_on' => $today->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $siti->id,
                'title' => 'Kolase Kupu-kupu',
                'description' => 'Siti menempel potongan kertas warna dengan rapi.',
                'aspects' => 'Kemandirian, Fokus',
                'score_label' => 'BSH',
                'score_value' => 3,
                'status' => 'dinilai',
                'image_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                'created_on' => $today->subDays(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $raka->id,
                'title' => 'Menara Balok',
                'description' => 'Raka menyusun menara dengan keseimbangan yang baik.',
                'aspects' => 'Kognitif, Fisik Motorik',
                'score_label' => null,
                'score_value' => null,
                'status' => 'belum dinilai',
                'image_url' => 'https://images.unsplash.com/photo-1607457561901-e6ec3a6d16cf?auto=format&fit=crop&w=900&q=80',
                'created_on' => $today->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        AnecdotalNote::insert([
            [
                'student_id' => $siti->id,
                'recorded_at' => $today->setTime(9, 30),
                'location' => 'Area Balok',
                'tone' => 'positif',
                'description' => 'Siti berbagi balok merah miliknya kepada teman yang menangis.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $siti->id,
                'recorded_at' => $today->subDays(2)->setTime(8, 15),
                'location' => 'Karpet Lingkaran',
                'tone' => 'observasi',
                'description' => 'Siti berani bercerita tentang pengalaman ke kebun binatang.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $bima->id,
                'recorded_at' => $today->setTime(10, 0),
                'location' => 'Sentra Seni',
                'tone' => 'positif',
                'description' => 'Bima fokus melukis hingga selesai dan merapikan alatnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DevelopmentReport::create([
            'student_id' => $bima->id,
            'semester' => 'Semester Ganjil',
            'school_year' => '2025/2026',
            'summary' => 'Ananda Bima menunjukkan perkembangan sangat membanggakan, terutama pada kemandirian dan kreativitas.',
            'teacher_note' => 'Terus latih motorik halus di rumah melalui aktivitas menulis dan menggunting.',
        ]);

        DevelopmentReport::create([
            'student_id' => $siti->id,
            'semester' => 'Semester Ganjil',
            'school_year' => '2025/2026',
            'summary' => 'Siti aktif berinteraksi, semakin percaya diri, dan konsisten dalam kegiatan seni.',
            'teacher_note' => 'Pertahankan semangat belajar dan kebiasaan berbagi dengan teman.',
        ]);
    }
}

