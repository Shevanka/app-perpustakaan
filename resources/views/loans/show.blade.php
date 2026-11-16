@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <h1>Detail Peminjaman</h1>
    <p><a href="{{ route('loans.index') }}">&larr; Kembali ke daftar peminjaman</a></p>

    <div class="form-container">
        <table>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Anggota</th>
                <td>{{ $loan->member->nama ?? '-' }} ({{ $loan->member->nim ?? '-' }})</td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Petugas Pencatat</th>
                <td>{{ $loan->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Tanggal Pinjam</th>
                <td>{{ $loan->tanggal_pinjam }}</td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Batas Pengembalian (Tenggat)</th>
                <td>{{ $loan->tanggal_kembali }}</td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Tanggal Aktual Dikembalikan</th>
                <td>
                    @if ($loan->tanggal_dikembalikan)
                        <strong style="color: #047857;">{{ $loan->tanggal_dikembalikan }}</strong>
                    @else
                        <span style="color: #94a3b8;">Belum dikembalikan</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Keterlambatan</th>
                <td>
                    @if ($loan->hari_terlambat > 0)
                        <span style="color: #b91c1c; font-weight: 700;">{{ $loan->hari_terlambat }} Hari</span>
                    @else
                        <span style="color: #047857;">Tepat Waktu (0 hari)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Denda Keterlambatan</th>
                <td>
                    @if ($loan->denda > 0)
                        <span style="color: #b91c1c; font-weight: 700; font-size: 16px;">{{ $loan->formatted_denda }}</span>
                        <span style="font-size: 12px; color: #64748b;">(Tarif: Rp 1.000 / hari)</span>
                    @else
                        <span style="color: #047857; font-weight: 600;">Rp 0 (Bebas Denda)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width: 200px; background: #f3f4f6;">Status Peminjaman</th>
                <td>
                    @if ($loan->status === 'dikembalikan')
                        <span class="badge badge-dikembalikan">Dikembalikan</span>
                    @elseif ($loan->hari_terlambat > 0)
                        <span class="badge badge-terlambat">Terlambat ({{ $loan->hari_terlambat }} hari)</span>
                    @else
                        <span class="badge badge-dipinjam">Dipinjam</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <h2 style="margin-top: 28px;">Buku yang Dipinjam</h2>
    <table>
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Stok Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loan->loanItems as $item)
                <tr>
                    <td><strong>{{ $item->book->judul ?? 'Buku telah dihapus' }}</strong></td>
                    <td>{{ $item->book->penulis ?? '-' }}</td>
                    <td>{{ $item->book->penerbit ?? '-' }}</td>
                    <td>
                        @if ($item->book)
                            <span class="badge {{ $item->book->stok > 0 ? 'badge-dikembalikan' : 'badge-terlambat' }}">
                                {{ $item->book->stok }} unit
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
