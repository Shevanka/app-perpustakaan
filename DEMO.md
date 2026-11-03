# Skenario Demo Aplikasi Perpustakaan Digital Kampus

Dokumen ini memuat panduan langkah-demi-langkah demonstrasi aplikasi **app-perpustakaan** untuk evaluasi Ujian Akhir Semester (UAS) / evaluasi project akhir framework Laravel 12.

---

## A. Persiapan Sebelum Demo

Pastikan hal-hal berikut sudah disiapkan sebelum memulai sesi demo:

1. **Database dalam Kondisi Bersih & Ter-seed:**
   Jalankan perintah berikut agar database memiliki data dummy yang realistis:
   ```bash
   php artisan migrate:fresh --seed
   ```
   Data hasil seeder:
   - 3 Akun Pengguna (1 Admin, 2 Petugas)
   - 5 Kategori Buku
   - 20 Buku dengan judul dan penerbit kurasi Indonesia
   - 15 Anggota mahasiswa dengan email kampus `@pens.ac.id`
   - 10 Riwayat transaksi peminjaman

2. **Menjalankan Dua Instance Server (Wajib):**
   Karena server pengembangan `php artisan serve` bersifat single-threaded, konsumsi API internal dari Dashboard dan Laporan membutuhkan dua instance server agar tidak terjadi *deadlock*:
   - **Terminal 1 (Server Utama - Trafik Browser):**
     ```bash
     php artisan serve --port=8000
     ```
   - **Terminal 2 (Server Internal API - Endpoint API):**
     ```bash
     php artisan serve --port=8011
     ```

3. **Buka Aplikasi & Tool Uji:**
   - Browser: buka `http://127.0.0.1:8000`
   - Postman: siapkan collection endpoint API perpustakaan

---

## B. Kredensial Akun Demo

| Role | Nama | Email | Password | Hak Akses |
|---|---|---|---|---|
| **Admin** | Bambang Sutrisno | `admin@pens.ac.id` | `password` | Akses penuh (Dashboard, Buku, Kategori, Anggota, Peminjaman, Laporan, Profil) |
| **Petugas 1** | Siti Rahmawati | `petugas1@pens.ac.id` | `password` | Akses operasional (Dashboard, Buku, Anggota, Peminjaman, Laporan, Profil) |
| **Petugas 2** | Ahmad Fauzi | `petugas2@pens.ac.id` | `password` | Akses operasional |

---

## C. Urutan Langkah Skenario Demo Live

### Langkah 1: Login sebagai Petugas / Admin
1. Buka browser pada URL `http://127.0.0.1:8000/login`.
2. Masukkan email `admin@pens.ac.id` dan password `password`.
3. Klik tombol **Login**.
4. **Poin yang Ditunjukkan:**
   - Pengguna otomatis diarahkan (*redirect*) ke halaman Dashboard (`/`).
   - Muncul alert sukses: *"Login berhasil, selamat datang Bambang Sutrisno."*
   - Navbar menampilkan nama pengguna dan badge role: `Bambang Sutrisno (Admin)`.
   - Menu navigasi menampilkan: **Dashboard, Buku, Kategori (hanya Admin), Anggota, Peminjaman, Laporan, Profil**.

---

### Langkah 2: Tampilkan Dashboard Statistik (Konsumsi API Internal)
1. Tinjau halaman Dashboard (`/`).
2. Tunjukkan 3 kartu statistik utama:
   - **Total Buku**: 20
   - **Total Anggota**: 15
   - **Peminjaman Aktif**: (jumlah peminjaman berstatus `dipinjam`)
3. **Poin Konseptual yang Dijelaskan:**
   - Angka statistik ini **tidak** diambil dari query database langsung di `DashboardController`, melainkan dikonsumsi dari endpoint `GET /api/stats` via **Laravel HTTP Client** (`Http::get()`).
   - Kode controller dilengkapi `try/catch (ConnectionException $e)` sehingga apabila server API mati/tidak dapat dihubungi, halaman tetap tampil anggun dengan nilai *fallback* nol (`0`) tanpa memicu error 500.

---

### Langkah 3: Tambah Kategori Baru (Fitur Admin)
1. Klik menu **Kategori** pada navbar (`/categories`).
2. Klik tombol **+ Tambah Kategori**.
3. Isi form:
   - Nama Kategori: `Ensiklopedia`
   - Deskripsi: `Buku referensi lengkap seputar ilmu pengetahuan dan umum.`
4. Klik **Simpan**.
5. **Poin yang Ditunjukkan:**
   - Flash message sukses muncul: *"Kategori berhasil ditambahkan."*
   - Kategori baru muncul di tabel daftar kategori.

---

### Langkah 4: Tambah Buku dengan Relasi Kategori
1. Klik menu **Buku** pada navbar (`/books`).
2. Klik tombol **+ Tambah Buku**.
3. Isi form buku baru:
   - Judul: `Ensiklopedia Sains dan Teknologi Nusantara`
   - Penulis: `Tim Peneliti PENS`
   - Penerbit: `PENS Press`
   - Tahun Terbit: `2024`
   - ISBN: `978-602-001-0011`
   - Stok: `5`
   - Kategori: Pilih kategori yang baru dibuat (`Ensiklopedia`).
4. Klik **Simpan**.
5. **Poin yang Ditunjukkan:**
   - Buku tersimpan dan muncul di daftar buku.
   - Kolom kategori menampilkan nama kategori (`Ensiklopedia`), bukan `category_id` mentah.
   - Paginasi di bagian bawah tabel menggunakan custom pagination view yang rapi dan responsif.

---

### Langkah 5: Tambah Anggota Baru & Pengujian Validasi
1. Klik menu **Anggota** pada navbar (`/members`).
2. Klik tombol **+ Tambah Anggota**.
3. Isi form anggota:
   - Nama: `Farhan Maulana`
   - NIM: `2103191099`
   - Email: `farhan.maulana@pens.ac.id`
   - Nomor Telepon: `081299887766`
   - Alamat: `Jl. Menur Pumpungan No. 12, Surabaya`
   - Status: `aktif`
4. Klik **Simpan**.
5. **Poin yang Ditunjukkan:**
   - Data anggota berhasil tersimpan.
   - Uji validasi: Coba tambahkan anggota baru dengan NIM atau Email yang sama persis, sistem akan menampilkan pesan error validasi bahwa NIM/Email sudah terdaftar.

---

### Langkah 6: Buat Transaksi Peminjaman (Multi-Buku)
1. Klik menu **Peminjaman** pada navbar (`/loans`).
2. Klik tombol **+ Catat Peminjaman**.
3. Isi form peminjaman:
   - Anggota: Pilih `Farhan Maulana`.
   - Tanggal Pinjam: Pilih tanggal hari ini.
   - Tanggal Kembali: Pilih 7 hari ke depan.
   - Buku yang Dipinjam: Centang 2 buku sekaligus (fitur multi-buku).
4. Klik **Simpan Transaksi**.
5. **Poin yang Ditunjukkan:**
   - Petugas pencatat (`user_id`) otomatis diisi dari session login `auth()->id()`.
   - Transaksi berhasil tercatat dan relasi item buku tersimpan ke tabel `loan_items`.

---

### Langkah 7: Lihat Daftar Peminjaman Aktif & Relasi Eager Loading
1. Pada halaman `/loans`:
2. **Poin yang Ditunjukkan:**
   - Transaksi yang baru dibuat muncul dengan status **`dipinjam`**.
   - Badge status berwarna kuning untuk `dipinjam`, hijau untuk `dikembalikan`, dan merah untuk `terlambat`.
   - Kolom anggota menampilkan nama lengkap, kolom petugas menampilkan nama petugas pencatat, dan kolom buku menampilkan daftar judul buku yang dipinjam.
   - Controller menggunakan **Eager Loading** (`with(['member', 'user', 'loanItems.book'])`) untuk mencegah **N+1 Query Problem**.

---

### Langkah 8: Proses Pengembalian Buku
1. Pada baris transaksi peminjaman yang baru dibuat di tabel `/loans`:
2. Klik tombol **Kembalikan**.
3. **Poin yang Ditunjukkan:**
   - Status transaksi langsung berubah menjadi **`dikembalikan`** (badge hijau).
   - Kolom tanggal pengembalian terisi dengan tanggal hari ini.
   - Tombol "Kembalikan" otomatis hilang untuk transaksi yang sudah kembali.

---

### Langkah 9: Tampilkan Halaman Laporan Peminjaman (Konsumsi API Internal)
1. Klik menu **Laporan** pada navbar atau tautan di Dashboard (`/loans/report`).
2. **Poin yang Ditunjukkan:**
   - Seluruh transaksi peminjaman (termasuk yang baru saja dikembalikan) tampil pada tabel laporan.
   - Data pada halaman ini diambil murni dari endpoint API internal `GET /api/loans`, bukan query Eloquent langsung dari controller web.
   - Navigasi paginasi laporan (`« Sebelumnya` dan `Selanjutnya »`) bekerja melalui query string `?page=`.

---

### Langkah 10: Demonstrasi REST API di Postman
Buka Postman dan jalankan endpoint API utama:
1. `GET http://127.0.0.1:8000/api/books`
   - Header: `Accept: application/json`
   - Tunjukkan struktur response JSON standar: `data` array buku beserta `category` ter-nesting, plus metadata `links` dan `meta` untuk pagination.
2. `GET http://127.0.0.1:8000/api/stats`
   - Tunjukkan response angka agregasi: `{"total_buku": 21, "total_anggota": 16, "peminjaman_aktif": ...}`.
3. `POST http://127.0.0.1:8000/api/books` (validasi error 422 vs sukses 201)
   - Tunjukkan response `422 Unprocessable Content` jika field kosong.
   - Tunjukkan response `201 Created` jika payload JSON valid.
4. Tunjukkan file dokumentasi lengkap di [`API.md`](./API.md).

---

## D. Ringkasan Tanya Jawab Konseptual

Siap menjawab pertanyaan penguji mengenai:
1. **Arsitektur MVC:** Model (data & business logic), View (tampilan Blade), Controller (pengatur alur request-response).
2. **Eloquent Relationships:** Relasi `belongsTo`, `hasMany`, dan pivot/intermediate table (`loans` -> `loan_items` -> `books`).
3. **N+1 Query Problem:** Masalah eksekusi query berulang di dalam loop; diatasi dengan *eager loading* menggunakan method `with()`.
4. **Autentikasi & Middleware:** Session-based authentication via `Auth::attempt()`, pengamanan route group via middleware `auth` dan role check via middleware `admin`.
5. **REST API & API Resources:** Pemisahan struktur database internal dengan representasi JSON publik menggunakan `JsonResource`.
6. **Konsumsi API Internal & Penanganan Deadlock:** Alasan digunakannya 2 server port pada server PHP built-in single-threaded dan penggunaan `try/catch` untuk mencegah crash pada layer presentasi.
7. **Seeder & Factory:** Pemanfaatan Factory dan Faker untuk menghasilkan data acak massal terstruktur, serta kurasi data judul dan penerbit berbahasa Indonesia.
