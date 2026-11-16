@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    <h1>Detail Buku</h1>
    <p><a href="{{ route('books.index') }}">&larr; Kembali ke daftar buku</a></p>

    <div style="display: flex; gap: 28px; flex-wrap: wrap; margin-top: 16px;">
        <!-- Card Sampul Buku -->
        <div style="flex: 0 0 240px; text-align: center;">
            <div style="background: #eef2f6; border-radius: 18px; padding: 18px; box-shadow: 6px 6px 14px #d1d9e6, -6px -6px 14px #ffffff;">
                @if ($book->sampul_url)
                    <img src="{{ $book->sampul_url }}" alt="{{ $book->judul }}"
                        style="width: 100%; max-width: 200px; height: 280px; object-fit: cover; border-radius: 12px; box-shadow: 3px 3px 10px rgba(163,177,198,0.45);">
                @else
                    <div style="width: 100%; height: 280px; background: #e2e8f0; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; box-shadow: inset 2px 2px 5px rgba(163,177,198,0.3);">
                        <span style="font-size: 54px;">📖</span>
                        <span style="font-size: 13px; color: #64748b;">Belum ada sampul</span>
                    </div>
                @endif
                <div style="margin-top: 14px;">
                    <span class="badge {{ $book->stok > 0 ? 'badge-dikembalikan' : 'badge-terlambat' }}">
                        {{ $book->stok > 0 ? 'Tersedia: ' . $book->stok . ' unit' : 'Stok Habis' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detail Informasi Buku -->
        <div style="flex: 1; min-width: 320px;">
            <div class="form-container" style="margin-top: 0;">
                <table>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Judul Buku</th>
                        <td><strong>{{ $book->judul }}</strong></td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Penulis</th>
                        <td>{{ $book->penulis }}</td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Penerbit</th>
                        <td>{{ $book->penerbit }}</td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Tahun Terbit</th>
                        <td>{{ $book->tahun_terbit }}</td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">ISBN</th>
                        <td>{{ $book->isbn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Kategori</th>
                        <td>
                            <span style="display: inline-block; background: #ede9fe; color: #6d28d9; padding: 3px 10px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                {{ $book->category->nama_kategori ?? 'Umum' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 160px; background: #f3f4f6;">Aksi</th>
                        <td>
                            <a href="{{ route('books.edit', $book->id) }}" class="btn" style="padding: 6px 14px; font-size: 13px;">✏️ Edit Buku</a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
