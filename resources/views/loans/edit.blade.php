@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
    <h1>Edit Peminjaman</h1>
    <p><a href="{{ route('loans.index') }}">&larr; Kembali ke daftar peminjaman</a></p>

    <div class="form-container">
        <label>Anggota</label>
        <div class="readonly">{{ $loan->member->nama ?? '-' }} ({{ $loan->member->nim ?? '-' }})</div>

        <label>Buku yang Dipinjam</label>
        <div class="readonly">
            @foreach ($loan->loanItems as $item)
                📖 {{ $item->book->judul ?? 'Buku dihapus' }} (stok saat ini: {{ $item->book->stok ?? 0 }})@if (!$loop->last), @endif
            @endforeach
        </div>

        @if ($loan->denda > 0)
            <label>Denda Tercatat</label>
            <div class="readonly" style="color: #b91c1c; font-weight: 700;">
                {{ $loan->formatted_denda }} (Keterlambatan: {{ $loan->hari_terlambat }} hari)
            </div>
        @endif

        <form action="{{ route('loans.update', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="tanggal_pinjam" value="{{ $loan->tanggal_pinjam }}">

            <label for="tanggal_kembali">Batas Tanggal Pengembalian (Tenggat)</label>
            <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="{{ old('tanggal_kembali', $loan->tanggal_kembali) }}">
            @error('tanggal_kembali')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="dipinjam" @selected(old('status', $loan->status) == 'dipinjam')>Dipinjam</option>
                <option value="dikembalikan" @selected(old('status', $loan->status) == 'dikembalikan')>Dikembalikan</option>
                <option value="terlambat" @selected(old('status', $loan->status) == 'terlambat')>Terlambat</option>
            </select>
            @error('status')
                <div class="error">{{ $message }}</div>
            @enderror

            <br>
            <button type="submit" class="btn">Perbarui Peminjaman</button>
        </form>
    </div>
@endsection
