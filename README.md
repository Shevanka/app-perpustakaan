# Sistem Perpustakaan Digital Kampus

**app-perpustakaan** adalah aplikasi berbasis web yang dikembangkan menggunakan Laravel 12 untuk membantu pengelolaan perpustakaan digital kampus. Aplikasi ini ditujukan untuk admin/petugas dalam mengelola data buku, anggota perpustakaan, serta transaksi peminjaman.

## Tujuan

Aplikasi ini dibuat untuk menyediakan sistem pengelolaan perpustakaan yang terstruktur dan memudahkan admin/petugas dalam melakukan manajemen data perpustakaan.

## Tech Stack

- Laravel 12
- PHP
- MySQL
- Blade
- MVC Architecture

## Cara Menjalankan Project Secara Lokal

### 1. Clone Repository

```bash
git clone https://github.com/Shevanka/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependency

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian sesuaikan konfigurasi database pada file `.env`:

```env
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migration

Pastikan database `db_perpustakaan` sudah dibuat, kemudian jalankan:

```bash
php artisan migrate
```

### 6. Jalankan Server Laravel

```bash
php artisan serve
```

Buka aplikasi melalui:

```text
http://127.0.0.1:8000
```

<!-- Model bertanggung jawab penuh untuk mengelola data, struktur tabel, dan berinteraksi langsung dengan database.
View bertugas mengatur antarmuka visual (seperti HTML) yang akan ditampilkan dan dilihat langsung oleh pengguna.
Controller berfungsi sebagai jembatan yang menerima request dari pengguna, memproses data melalui Model, lalu mengirimkan hasilnya ke View untuk ditampilkan -->
