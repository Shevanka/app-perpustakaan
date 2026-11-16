@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
    <div style="margin-bottom: 20px;">
        <h1 style="margin-bottom: 4px;">Laporan Peminjaman</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Data diambil secara live melalui endpoint RESTful API (<code>GET /api/loans</code>) via HTTP Client.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Anggota</th>
                <th>Petugas</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tenggat</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                <tr>
                    <td><strong>#{{ $loan['id'] }}</strong></td>
                    <td>{{ $loan['member']['nama'] ?? '-' }}</td>
                    <td>{{ $loan['petugas']['name'] ?? '-' }}</td>
                    <td>
                        @foreach ($loan['books'] as $book)
                            <span style="display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 12px; margin: 1px 0;">
                                📖 {{ $book['judul'] ?? '-' }}
                            </span>@if (!$loop->last)<br>@endif
                        @endforeach
                    </td>
                    <td>{{ $loan['tanggal_pinjam'] }}</td>
                    <td>{{ $loan['tanggal_kembali'] }}</td>
                    <td>
                        @if (!empty($loan['tanggal_dikembalikan']))
                            <strong style="color: #047857;">{{ $loan['tanggal_dikembalikan'] }}</strong>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td>
                        @if (!empty($loan['denda']) && $loan['denda'] > 0)
                            <span style="font-weight: 700; color: #b91c1c;">{{ $loan['denda_formatted'] ?? ('Rp ' . number_format($loan['denda'], 0, ',', '.')) }}</span>
                            @if (!empty($loan['hari_terlambat']))
                                <div style="font-size: 11px; color: #ef4444;">({{ $loan['hari_terlambat'] }} hr)</div>
                            @endif
                        @else
                            <span style="color: #64748b; font-size: 13px;">Rp 0</span>
                        @endif
                    </td>
                    <td>
                        @if ($loan['status'] === 'dikembalikan')
                            <span class="badge badge-dikembalikan">Dikembalikan</span>
                        @elseif (!empty($loan['hari_terlambat']) && $loan['hari_terlambat'] > 0)
                            <span class="badge badge-terlambat">Terlambat</span>
                        @else
                            <span class="badge badge-dipinjam">Dipinjam</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 32px; color: #94a3b8;">Belum ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($meta && $meta['last_page'] > 1)
        <div class="pagination">
            @if ($meta['current_page'] > 1)
                <a href="{{ route('loans.report', ['page' => $meta['current_page'] - 1]) }}">&laquo; Sebelumnya</a>
            @else
                <span class="pagination-disabled">&laquo; Sebelumnya</span>
            @endif

            <span class="pagination-current">Halaman {{ $meta['current_page'] }} dari {{ $meta['last_page'] }}</span>

            @if ($meta['current_page'] < $meta['last_page'])
                <a href="{{ route('loans.report', ['page' => $meta['current_page'] + 1]) }}">Berikutnya &raquo;</a>
            @else
                <span class="pagination-disabled">Berikutnya &raquo;</span>
            @endif
        </div>
    @endif
@endsection
