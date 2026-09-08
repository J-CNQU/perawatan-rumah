# Perawatan Rumah

Aplikasi manajemen perawatan properti dan aset rumah berbasis Laravel, dirancang untuk membantu pengguna mengelola lokasi properti, aset, log perawatan, anggaran bulanan, serta pemantauan kondisi kesehatan properti secara terstruktur.

## Tentang Proyek

Perawatan Rumah adalah sistem untuk memantau aset rumah dan properti secara digital. Dengan aplikasi ini, pengguna dapat:

- menambahkan properti dan lokasi aset,
- mencatat aset beserta kondisi dan riwayat pembelian,
- mencatat log perawatan dan biaya servis,
- mengatur anggaran perawatan bulanan,
- memantau kesehatan properti berdasarkan kondisi aset,
- menerima pengingat servis yang sudah terjadwal,
- mengekspor laporan properti dalam format PDF.

Aplikasi ini dibangun dengan Laravel 13 dan menggunakan pendekatan modular untuk mempermudah pengembangan fitur lanjutan.

## Fitur Utama

### 1. Manajemen Properti & Aset

- Menambah lokasi properti seperti Rumah, Kantor, Toko, dan lainnya.
- Menambahkan aset ke tiap properti.
- Mengelola kategori aset.
- Mencatat kondisi aset: Normal, Perlu Servis, Rusak.
- Menyimpan tanggal pembelian, harga beli, dan deskripsi aset.

### 2. Log Perawatan

- Mencatat log servis atau perawatan aset.
- Menyimpan tanggal pelayanan, biaya, dan catatan teknis.
- Mengedit atau menghapus log perawatan sesuai kebutuhan.

### 3. Financial & Budget System

- Mengatur gaji bulanan pengguna.
- Menentukan persentase alokasi anggaran untuk perawatan.
- Menghitung biaya perawatan bulan berjalan.
- Menilai status kesehatan finansial:
    - Aman
    - Waspada
    - Over Budget

### 4. Health Score Properti

- Menghitung Health Score berdasarkan rasio aset dalam kondisi Normal.
- Menampilkan progress indicator pada dashboard dan halaman detail properti.

### 5. Smart Service Reminder

- Menghitung jadwal servis berikutnya berdasarkan:
    - tanggal perawatan terakhir, atau
    - tanggal pembelian jika belum pernah dirawat.
- Menampilkan pengingat servis yang mendekati deadline atau sudah terlambat.

### 6. Garansi Aset

- Mengecek tanggal `warranty_expiration`.
- Memberi status:
    - Garansi Aktif
    - Garansi Habis

### 7. Export PDF Laporan Properti

- Menyediakan tombol ekspor laporan properti ke format PDF.
- Menampilkan ringkasan properti, total biaya perawatan, asset list, serta riwayat servis.

## Stack Teknologi

- PHP 8.3+
- Laravel 13
- MySQL / MariaDB
- Blade Templates
- Tailwind CSS
- Alpine.js
- Vite
- PHPUnit

## Struktur Proyek

```bash
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── PropertyController.php
│   │   └── ...
│   └── Requests/
├── Models/
│   ├── Asset.php
│   ├── Category.php
│   ├── MaintenanceLog.php
│   ├── Property.php
│   └── User.php
config/
database/
├── factories/
├── migrations/
├── seeders/
public/
resources/
├── css/
├── js/
└── views/
routes/
└── web.php
tests/
```

## Prasyarat

Pastikan perangkat Anda sudah memiliki:

- PHP 8.3 atau lebih tinggi
- Composer
- Node.js 18+ dan npm
- Database MySQL/MariaDB
- Web server lokal seperti Laravel Artisan serve atau server web lain

## Setup Instalasi

### 1. Clone repository

```bash
git clone https://github.com/J-CNQU/perawatan-rumah.git
cd perawatan-rumah
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Siapkan file environment

```bash
cp .env.example .env
```

Lalu sesuaikan konfigurasi database pada file `.env`, misalnya:

```env
APP_NAME="Perawatan Rumah"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=home_maintenance
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Jalankan migrasi database

```bash
php artisan migrate
```

Jika ingin langsung membuat data awal:

```bash
php artisan db:seed
```

### 7. Compile asset frontend

Untuk mode development:

```bash
npm run dev
```

Untuk build produksi:

```bash
npm run build
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Akses aplikasi di:

```text
http://localhost:8000
```

## Menjalankan Aplikasi Berbarengan

Untuk menjalankan Laravel + frontend dalam satu perintah:

```bash
composer run dev
```

## Cara Penggunaan

### Autentikasi

- Daftar atau masuk ke aplikasi menggunakan sistem auth bawaan Laravel Breeze.
- Setelah login, pengguna akan diarahkan ke dashboard.

### Dashboard

- Melihat total properti.
- Melihat gaji bulanan dan alokasi anggaran maintenance.
- Melihat progress penggunaan anggaran bulan ini.
- Melihat Health Score tiap properti.

### Properti

- Tambahkan properti baru.
- Kelola data aset di setiap properti.
- Lihat status kesehatan properti secara real-time.

### Perawatan

- Tambahkan log servis baru.
- Catat biaya servis dan notes.
- Mengetahui aset yang sudah saatnya dirawat.

### Export PDF

- Buka halaman detail properti.
- Klik tombol `Cetak Laporan / PDF`.
- Laporan akan ditampilkan dalam format print-friendly.

## Fitur Database Khusus

Beberapa kolom penting yang digunakan oleh sistem:

### Tabel `users`

- `monthly_salary`
- `maintenance_budget_percentage`

### Tabel `assets`

- `warranty_expiration`
- `maintenance_interval_months`

## Testing

Untuk menjalankan test yang relevan:

```bash
php artisan test
```

Atau untuk menguji fitur terkait properti dan dashboard:

```bash
php artisan test --filter=PropertyDetailTest
```

## Kontak & Tim Pengembang

**Developer / Coding:**

- **Juan Felix Katoro**
- **Ethan Francis**

**UI/UX Design:**

- **Ericxander**
- **Enrico**

**Detail Pembelajaran:**

- **Kelas / Jurusan:** XII TKJ 1
- **Mata Pelajaran:** Pemrograman Lanjutan (PL)
- **Project Link:** https://github.com/J-CNQU/perawatan-rumah

## Catatan Akhir

Project ini dikembangkan sebagai tugas akhir pembelajaran untuk mengimplementasikan konsep pengelolaan aset rumah dan keuangan secara digital. Tujuan utamanya adalah mempermudah pencatatan servis, monitoring kondisi properti, serta pengelolaan anggaran perawatan rumah secara lebih terorganisir dan efisien.

---

Dibuat untuk kebutuhan pembelajaran dan pengembangan aplikasi manajemen perawatan rumah berbasis web.

```

## Kontribusi

Kontribusi sangat terbuka untuk pengembangan lebih lanjut. Silakan fork repository, buat branch baru, lalu ajukan pull request.

## Lisensi

Proyek ini dilisensikan di bawah MIT License.

## Catatan Pengembangan

Proyek ini masih bisa dikembangkan lebih lanjut untuk kebutuhan produksi, seperti:

- otomatisasi reminder via email atau notifikasi,
- integrasi chart untuk monitoring biaya bulanan,
- multi-user role management,
- export Excel selain PDF,
- API REST untuk integrasi mobile atau dashboard eksternal.

## Developer Notes

Aplikasi ini cocok digunakan sebagai base project untuk sistem home maintenance digital, terutama untuk keperluan:

- pemilik rumah,
- pengelola properti,
- konsultan maintenance,
- atau tim fasilitas rumah dan aset.

---

Dibuat untuk kebutuhan manajemen properti dan perawatan rumah berbasis web.

```
