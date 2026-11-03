# Dokumentasi REST API - Sistem Informasi Perpustakaan

Dokumentasi resmi RESTful API untuk Sistem Informasi Perpustakaan (`app-perpustakaan`) yang dibangun menggunakan Laravel 12. Seluruh endpoint menggunakan prefix `/api` dan mengembalikan data dalam format JSON standar.

---

## Ketentuan Umum & Request Header

Setiap request ke endpoint API disarankan menyertakan header berikut:

```http
Accept: application/json
Content-Type: application/json
```

---

## Ringkasan Endpoint

Berikut adalah ringkasan seluruh 11 endpoint REST API:

| No | Method | URL | Deskripsi | Status Code |
|---|---|---|---|---|
| 1 | `GET` | `/api/books` | Mengambil daftar buku (dengan filter judul & kategori, paginasi) | `200 OK` |
| 2 | `GET` | `/api/books/{id}` | Mengambil detail buku berdasarkan ID | `200 OK`, `404 Not Found` |
| 3 | `POST` | `/api/books` | Menambahkan data buku baru | `201 Created`, `422 Unprocessable Content` |
| 4 | `PUT` | `/api/books/{id}` | Memperbarui data buku | `200 OK`, `422 Unprocessable Content`, `404 Not Found` |
| 5 | `DELETE` | `/api/books/{id}` | Menghapus data buku | `200 OK`, `404 Not Found` |
| 6 | `GET` | `/api/members` | Mengambil daftar anggota perpustakaan (paginasi) | `200 OK` |
| 7 | `GET` | `/api/members/{id}` | Mengambil detail anggota berdasarkan ID | `200 OK`, `404 Not Found` |
| 8 | `GET` | `/api/loans` | Mengambil riwayat transaksi peminjaman (eager load relasi) | `200 OK` |
| 9 | `POST` | `/api/loans` | Mencatat transaksi peminjaman baru (multi-buku) | `201 Created`, `422 Unprocessable Content` |
| 10 | `PUT` | `/api/loans/{id}/kembalikan` | Memproses pengembalian buku peminjaman | `200 OK`, `404 Not Found` |
| 11 | `GET` | `/api/stats` | Statistik total buku, anggota, dan peminjaman aktif | `200 OK` |

---

## Detail Endpoint & Contoh Response Nyata

### 1. `GET /api/books`
Mengambil daftar buku yang tersedia di perpustakaan dalam bentuk pagination (10 buku per halaman).

- **Query Parameters (Opsional):**
  - `judul`: Filter pencarian buku berdasarkan substring judul.
  - `category_id`: Filter buku berdasarkan ID kategori.
  - `page`: Nomor halaman paginasi.

- **Status Code:** `200 OK`
- **Contoh Response JSON:**
```json
{
  "data": [
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": "2005",
      "isbn": "9789793062792",
      "stok": 5,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "Fiksi"
      },
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    },
    {
      "id": 3,
      "judul": "Bumi Manusia",
      "penulis": "Pramoedya Ananta Toer",
      "penerbit": "Hasta Mitra",
      "tahun_terbit": "1980",
      "isbn": "9789794330746",
      "stok": 4,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "Fiksi"
      },
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T12:15:43.000000Z"
    },
    {
      "id": 4,
      "judul": "Clean Code",
      "penulis": "Robert C. Martin",
      "penerbit": "Prentice Hall",
      "tahun_terbit": "2008",
      "isbn": "9780132350884",
      "stok": 7,
      "sampul": null,
      "category": {
        "id": 3,
        "nama_kategori": "Teknologi"
      },
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    },
    {
      "id": 5,
      "judul": "Sapiens: Riwayat Singkat Umat Manusia",
      "penulis": "Yuval Noah Harari",
      "penerbit": "KPG",
      "tahun_terbit": "2014",
      "isbn": "9786024244163",
      "stok": 4,
      "sampul": null,
      "category": {
        "id": 4,
        "nama_kategori": "Sejarah"
      },
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/books?page=1",
    "last": "http://localhost/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/books?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/books",
    "per_page": 10,
    "to": 4,
    "total": 4
  }
}
```

---

### 2. `GET /api/books/{id}`
Mengambil data detail sebuah buku berdasarkan ID primary key-nya.

- **Status Code:** `200 OK`
- **Contoh Response JSON (ID Ditemukan - `GET /api/books/2`):**
```json
{
  "data": {
    "id": 2,
    "judul": "Laskar Pelangi",
    "penulis": "Andrea Hirata",
    "penerbit": "Bentang Pustaka",
    "tahun_terbit": "2005",
    "isbn": "9789793062792",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-09-23T06:48:20.000000Z",
    "updated_at": "2026-09-23T06:48:20.000000Z"
  }
}
```

- **Status Code:** `404 Not Found`
- **Contoh Response JSON (ID Tidak Ada - `GET /api/books/999`):**
```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```

---

### 3. `POST /api/books`
Menambahkan data buku baru ke database.

- **Request Body JSON:**
```json
{
  "judul": "Clean Code: A Handbook of Agile Software Craftsmanship",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2008,
  "isbn": "978-0132350884",
  "stok": 5,
  "category_id": 2
}
```

- **Status Code:** `201 Created`
- **Contoh Response JSON (Berhasil):**
```json
{
  "data": {
    "id": 7,
    "judul": "Clean Code: A Handbook of Agile Software Craftsmanship",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-11-03T06:44:07.000000Z",
    "updated_at": "2026-11-03T06:44:07.000000Z"
  }
}
```

- **Status Code:** `422 Unprocessable Content`
- **Contoh Response JSON (Data Kosong / Tidak Valid):**
```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": [
      "Judul buku wajib diisi."
    ],
    "penulis": [
      "Nama penulis wajib diisi."
    ],
    "penerbit": [
      "Nama penerbit wajib diisi."
    ],
    "tahun_terbit": [
      "Tahun terbit wajib diisi."
    ],
    "stok": [
      "Stok wajib diisi."
    ],
    "category_id": [
      "Kategori wajib dipilih."
    ]
  }
}
```

---

### 4. `PUT /api/books/{id}`
Memperbarui informasi data buku berdasarkan ID.

- **Request Body JSON:**
```json
{
  "judul": "Clean Code (Updated)",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2008,
  "isbn": "978-0132350884",
  "stok": 8,
  "category_id": 2
}
```

- **Status Code:** `200 OK`
- **Contoh Response JSON:**
```json
{
  "data": {
    "id": 7,
    "judul": "Clean Code (Updated)",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 8,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "Fiksi"
    },
    "created_at": "2026-11-03T06:44:07.000000Z",
    "updated_at": "2026-11-03T06:44:07.000000Z"
  }
}
```

---

### 5. `DELETE /api/books/{id}`
Menghapus data buku dari sistem perpustakaan.

- **Status Code:** `200 OK`
- **Contoh Response JSON (Berhasil):**
```json
{
  "message": "Buku berhasil dihapus."
}
```

- **Status Code:** `404 Not Found` (jika ID buku tidak ditemukan)

---

### 6. `GET /api/members`
Mengambil daftar anggota perpustakaan yang terdaftar secara paginasi (10 anggota per halaman).

- **Status Code:** `200 OK`
- **Contoh Response JSON:**
```json
{
  "data": [
    {
      "id": 2,
      "nama": "Ahmad Fauzi",
      "nim": "2103191001",
      "email": "ahmad.fauzi@pens.ac.id",
      "nomor_telepon": "081234567890",
      "alamat": "Jl. Raya ITS No. 1, Surabaya",
      "status": "aktif",
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    },
    {
      "id": 3,
      "nama": "Siti Nurhaliza",
      "nim": "2103191002",
      "email": "siti.nurhaliza@pens.ac.id",
      "nomor_telepon": "081234567891",
      "alamat": "Jl. Gebang Wetan No. 15, Surabaya",
      "status": "aktif",
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    },
    {
      "id": 4,
      "nama": "Budi Santoso",
      "nim": "2103191003",
      "email": "budi.santoso@pens.ac.id",
      "nomor_telepon": "081234567892",
      "alamat": "Jl. Keputih Perintis No. 8, Surabaya",
      "status": "nonaktif",
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    },
    {
      "id": 5,
      "nama": "Dewi Lestari",
      "nim": "2103191004",
      "email": "dewi.lestari@pens.ac.id",
      "nomor_telepon": "081234567893",
      "alamat": "Jl. Mulyosari Timur No. 20, Surabaya",
      "status": "aktif",
      "created_at": "2026-09-23T06:48:20.000000Z",
      "updated_at": "2026-09-23T06:48:20.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/members?page=1",
    "last": "http://localhost/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/members?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/members",
    "per_page": 10,
    "to": 4,
    "total": 4
  }
}
```

---

### 7. `GET /api/members/{id}`
Mengambil detail satu anggota perpustakaan berdasarkan ID.

- **Status Code:** `200 OK`
- **Contoh Response JSON (ID Ditemukan - `GET /api/members/2`):**
```json
{
  "data": {
    "id": 2,
    "nama": "Ahmad Fauzi",
    "nim": "2103191001",
    "email": "ahmad.fauzi@pens.ac.id",
    "nomor_telepon": "081234567890",
    "alamat": "Jl. Raya ITS No. 1, Surabaya",
    "status": "aktif",
    "created_at": "2026-09-23T06:48:20.000000Z",
    "updated_at": "2026-09-23T06:48:20.000000Z"
  }
}
```

- **Status Code:** `404 Not Found`
- **Contoh Response JSON (ID Tidak Ada - `GET /api/members/999`):**
```json
{
  "message": "No query results for model [App\\Models\\Member] 999"
}
```

---

### 8. `GET /api/loans`
Mengambil riwayat seluruh transaksi peminjaman buku beserta relasi `member`, `petugas` (`user`), dan daftar `books` yang di-eager load untuk mencegah *N+1 Query Problem*.

- **Status Code:** `200 OK`
- **Contoh Response JSON:**
```json
{
  "data": [
    {
      "id": 3,
      "member": {
        "id": 2,
        "nama": "Ahmad Fauzi"
      },
      "petugas": {
        "id": 1,
        "name": "Petugas Perpustakaan"
      },
      "tanggal_pinjam": "2026-11-03",
      "tanggal_kembali": "2026-11-10",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        {
          "id": 2,
          "judul": "Laskar Pelangi"
        }
      ]
    }
  ],
  "links": {
    "first": "http://localhost/api/loans?page=1",
    "last": "http://localhost/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/loans?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/loans",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 9. `POST /api/loans`
Mencatat transaksi peminjaman buku baru. Karena endpoint API bersifat *stateless* (tanpa session `auth()`), ID petugas pencatat dikirim secara eksplisit melalui atribut `user_id`. Mendukung multi-buku dalam satu peminjaman melalui array `book_ids`.

- **Request Body JSON:**
```json
{
  "member_id": 2,
  "user_id": 1,
  "tanggal_pinjam": "2026-11-03",
  "tanggal_kembali": "2026-11-10",
  "book_ids": [2]
}
```

- **Status Code:** `201 Created`
- **Contoh Response JSON (Berhasil):**
```json
{
  "data": {
    "id": 3,
    "member": {
      "id": 2,
      "nama": "Ahmad Fauzi"
    },
    "petugas": {
      "id": 1,
      "name": "Petugas Perpustakaan"
    },
    "tanggal_pinjam": "2026-11-03",
    "tanggal_kembali": "2026-11-10",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 2,
        "judul": "Laskar Pelangi"
      }
    ]
  }
}
```

- **Status Code:** `422 Unprocessable Content`
- **Contoh Response JSON (Validasi Gagal):**
```json
{
  "message": "Anggota wajib dipilih. (and 4 more errors)",
  "errors": {
    "member_id": [
      "Anggota wajib dipilih."
    ],
    "user_id": [
      "Petugas (user_id) wajib diisi."
    ],
    "tanggal_pinjam": [
      "Tanggal pinjam wajib diisi."
    ],
    "tanggal_kembali": [
      "Tanggal kembali wajib diisi."
    ],
    "book_ids": [
      "Pilih minimal satu buku."
    ]
  }
}
```

---

### 10. `PUT /api/loans/{id}/kembalikan`
Memproses pengembalian buku peminjaman. Mengubah kolom `status` menjadi `"dikembalikan"` dan mengisi kolom `tanggal_dikembalikan` dengan tanggal hari ini.

- **Status Code:** `200 OK`
- **Contoh Response JSON (Berhasil):**
```json
{
  "data": {
    "id": 3,
    "member": {
      "id": 2,
      "nama": "Ahmad Fauzi"
    },
    "petugas": {
      "id": 1,
      "name": "Petugas Perpustakaan"
    },
    "tanggal_pinjam": "2026-11-03",
    "tanggal_kembali": "2026-11-10",
    "tanggal_dikembalikan": "2026-11-03",
    "status": "dikembalikan",
    "books": [
      {
        "id": 2,
        "judul": "Laskar Pelangi"
      }
    ]
  }
}
```

- **Status Code:** `404 Not Found` (jika transaksi peminjaman tidak ditemukan)

---

### 11. `GET /api/stats`
Mengambil data ringkasan statistik perpustakaan untuk kebutuhan dashboard: total buku, total anggota, dan jumlah peminjaman yang sedang aktif (`status = 'dipinjam'`).

- **Status Code:** `200 OK`
- **Contoh Response JSON:**
```json
{
  "total_buku": 4,
  "total_anggota": 4,
  "peminjaman_aktif": 0
}
```
