@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="margin-bottom: 4px;">Daftar Buku</h1>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Katalog koleksi buku, ketersediaan stok, dan sampul buku perpustakaan.</p>
        </div>
        <a href="{{ route('books.create') }}" class="btn">+ Tambah Buku Baru</a>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Sampul</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $book)
                <tr>
                    <td style="text-align: center;">
                        @if ($book->sampul_url)
                            <img src="{{ $book->sampul_url }}" alt="{{ $book->judul }}"
                                style="width: 44px; height: 58px; object-fit: cover; border-radius: 8px; box-shadow: 2px 2px 6px rgba(163,177,198,0.35);">
                        @else
                            <div style="width: 44px; height: 58px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto; box-shadow: inset 1px 1px 3px rgba(163,177,198,0.25);">
                                📖
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $book->judul }}</strong>
                        @if ($book->isbn)
                            <div style="font-size: 11px; color: #64748b;">ISBN: {{ $book->isbn }}</div>
                        @endif
                    </td>
                    <td>{{ $book->penulis }}</td>
                    <td>{{ $book->penerbit }}</td>
                    <td>{{ $book->tahun_terbit }}</td>
                    <td>
                        <span class="badge {{ $book->stok > 0 ? 'badge-dikembalikan' : 'badge-terlambat' }}">
                            {{ $book->stok }} unit
                        </span>
                    </td>
                    <td>
                        <span style="display: inline-block; background: #ede9fe; color: #6d28d9; padding: 3px 10px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                            {{ $book->category->nama_kategori ?? 'Umum' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('books.show', $book->id) }}">Detail</a>
                        <a href="{{ route('books.edit', $book->id) }}">Edit</a>
                        <form class="inline" action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 32px; color: #94a3b8;">Belum ada data buku.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $books->links() }}
@endsection
