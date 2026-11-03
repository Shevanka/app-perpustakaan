# Sistem Informasi Perpustakaan Digital Kampus

Aplikasi web sistem informasi perpustakaan digital kampus yang dibangun menggunakan **Laravel 12** dan **PHP 8.5**. Aplikasi ini menyediakan fitur manajemen koleksi buku, kategori, keanggotaan mahasiswa, transaksi peminjaman multi-buku, serta integrasi RESTful API internal dan eksternal.

---

## 🚀 Fitur Utama

- **Autentikasi & Otorisasi Berbasis Peran (RBAC):**
  - Session-based authentication (Login, Logout).
  - Manajemen peran: **Admin** (akses penuh termasuk manajemen kategori) dan **Petugas** (akses operasional).
  - Halaman profil petugas dan fitur ganti password yang aman.
- **Manajemen Data (CRUD):**
  - **Buku:** Judul, penulis, penerbit, tahun terbit, ISBN, stok, cover, dan relasi kategori.
  - **Kategori:** Kategori buku terstruktur khusus dikelola oleh Admin.
  - **Anggota:** Data mahasiswa (NIM, nama, email kampus, nomor telepon, alamat, status) dengan fitur pencarian.
- **Transaksi Peminjaman & Pengembalian:**
  - Peminjaman multi-buku dalam satu transaksi.
  - Eager loading relasi untuk mencegah *N+1 Query Problem*.
  - Status peminjaman dengan badge visual (`dipinjam`, `dikembalikan`, `terlambat`).
  - Fitur pengembalian buku langsung dari antarmuka web.
- **Konsumsi API Internal (BFF Pattern):**
  - **Dashboard:** Kartu statistik (`total_buku`, `total_anggota`, `peminjaman_aktif`) dikonsumsi dari `GET /api/stats` via Laravel HTTP Client.
  - **Laporan Peminjaman:** Tabel riwayat transaksi dikonsumsi dari `GET /api/loans` via Laravel HTTP Client.
  - Dilengkapi mekanisme penanganan error koneksi (*fallback handling*) dengan `ConnectionException`.
- **RESTful API Lengkap:**
  - 11 Endpoint RESTful API standar untuk `books`, `members`, `loans`, dan `stats`.
  - Transformasi data menggunakan Laravel API Resources.
- **Seeder & Factory Data Realistis:**
  - Menggunakan Faker dengan locale Indonesia (`id_ID`).
  - Kurasi judul buku populer dan penerbit nasional asli Indonesia.
  - Email anggota terstruktur dengan domain kampus `@pens.ac.id`.
- **Tampilan Bersih & Paginasi Kustom:**
  - Master layout Blade yang konsisten dan rapi.
  - Custom pagination view tanpa ketergantungan SVG raksasa.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 12
- **Bahasa Pemrograman:** PHP 8.5
- **Database:** MySQL 8.0+
- **Template Engine:** Blade Templating
- **HTTP Client:** Guzzle / Laravel HTTP Client (`Illuminate\Support\Facades\Http`)
- **Code Formatter:** Laravel Pint
- **Testing:** PHPUnit

---

## 📦 Panduan Instalasi & Menjalankan Project

### 1. Clone Repository
```bash
git clone https://github.com/Shevanka/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependency
```bash
composer install
```

### 3. Konfigurasi File Environment (.env)
Salin template environment:
```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan konfigurasi database dan API internal:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

# Base URL instance kedua khusus panggilan API internal (wajib port berbeda dari server browser)
INTERNAL_API_URL=http://127.0.0.1:8011

# Locale faker untuk data dummy Indonesia
APP_FAKER_LOCALE=id_ID
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migration & Seeder
Buat database MySQL bernama `db_perpustakaan`, lalu jalankan:
```bash
php artisan migrate:fresh --seed
```
Perintah ini akan membuat seluruh tabel database dan mengisinya dengan data awal yang realistis (3 akun pengguna, 5 kategori, 20 buku, 15 anggota, dan 10 transaksi peminjaman).

---

## ⚡ Menjalankan Aplikasi (Kebutuhan Dua Server)

> ⚠️ **PENTING: Menjalankan Dua Server**
> Server bawaan PHP (`php artisan serve`) hanya dapat memproses satu request dalam satu waktu (*single-threaded*). Agar halaman **Dashboard** dan **Laporan Peminjaman** dapat memanggil endpoint API internal tanpa menyebabkan *deadlock*, Anda **wajib menjalankan dua terminal secara bersamaan**:

### Terminal 1: Server Utama (Trafik Browser)
```bash
php artisan serve --port=8000
```
Akses web melalui browser di: **`http://127.0.0.1:8000`**

### Terminal 2: Server API Internal (Panggilan Http::get())
```bash
php artisan serve --port=8011
```
Server ini khusus menerima request internal dari `DashboardController` dan `LoanController@report`.

---

## 🔑 Akun Demo untuk Login

| Role | Nama | Email | Password |
|---|---|---|---|
| **Admin** | Bambang Sutrisno | `admin@pens.ac.id` | `password` |
| **Petugas 1** | Siti Rahmawati | `petugas1@pens.ac.id` | `password` |
| **Petugas 2** | Ahmad Fauzi | `petugas2@pens.ac.id` | `password` |

---

## 📚 Dokumentasi Terkait

- **[API.md](./API.md):** Dokumentasi lengkap seluruh 11 endpoint REST API (Method, URL, Body, response JSON sungguhan, dan status code).
- **[DEMO.md](./DEMO.md):** Panduan skenario demonstrasi aplikasi live langkah-demi-langkah beserta ringkasan tanya jawab konseptual untuk UAS.
