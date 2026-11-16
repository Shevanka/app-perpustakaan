@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    <h1>Tambah Buku</h1>
    <p><a href="{{ route('books.index') }}">&larr; Kembali ke daftar buku</a></p>

    <div class="form-container">
        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="judul">Judul Buku</label>
            <input type="text" name="judul" id="judul" value="{{ old('judul') }}">
            @error('judul')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="penulis">Penulis</label>
            <input type="text" name="penulis" id="penulis" value="{{ old('penulis') }}">
            @error('penulis')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="penerbit">Penerbit</label>
            <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit') }}">
            @error('penerbit')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="tahun_terbit">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit', date('Y')) }}">
            @error('tahun_terbit')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="isbn">ISBN (opsional)</label>
            <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}">
            @error('isbn')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="stok">Jumlah Stok Awal</label>
            <input type="number" name="stok" id="stok" value="{{ old('stok', 5) }}">
            @error('stok')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}" @selected(old('category_id') == $category['id'])>
                        {{ $category['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="sampul">Foto Sampul Buku (opsional, maks. 2MB)</label>
            <input type="file" name="sampul" id="sampul" accept="image/jpeg,image/png,image/jpg,image/webp"
                onchange="previewCover(event)">
            @error('sampul')
                <div class="error">{{ $message }}</div>
            @enderror

            <div id="preview-container" style="display: none; margin-top: 12px; text-align: center;">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 6px;">Pratinjau Sampul Terpilih:</p>
                <img id="cover-preview" src="#" alt="Pratinjau Sampul"
                    style="max-width: 140px; max-height: 190px; border-radius: 12px; box-shadow: 4px 4px 12px rgba(163,177,198,0.4);">
            </div>

            <br>
            <button type="submit" class="btn">Simpan Buku</button>
        </form>
    </div>

    <script>
        function previewCover(event) {
            const input = event.target;
            const container = document.getElementById('preview-container');
            const preview = document.getElementById('cover-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                container.style.display = 'none';
            }
        }
    </script>
@endsection
