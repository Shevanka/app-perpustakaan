@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
    <!-- CSS Khusus Cetak / Print PDF -->
    <style>
        .print-only {
            display: none;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 1.2cm;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }

            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Sembunyikan navigasi, tombol, dan pagination saat print */
            nav,
            .no-print,
            .pagination,
            .btn,
            footer {
                display: none !important;
            }

            /* Tampilkan KOP surat dan lembar tanda tangan */
            .print-only {
                display: block !important;
            }

            /* Reset style tabel untuk cetak */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 14px !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            th, td {
                border: 1px solid #333333 !important;
                padding: 6px 8px !important;
                font-size: 10pt !important;
                color: #000000 !important;
                background: transparent !important;
            }

            th {
                background-color: #f1f5f9 !important;
                font-weight: bold !important;
                text-align: center !important;
            }

            .badge {
                border: 1px solid #666 !important;
                box-shadow: none !important;
                background: transparent !important;
                color: #000 !important;
                padding: 1px 4px !important;
            }
        }
    </style>

    <!-- KOP Surat Resmi (Muncul Saat Cetak) -->
    <div class="print-only" style="margin-bottom: 18px; text-align: center;">
        <div style="font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
            PERPUSTAKAAN DIGITAL KAMPUS TERPADU
        </div>
        <div style="font-size: 13px; font-weight: 600; color: #334155;">
            UPT PERPUSTAKAAN & PUSAT SUMBER BELAJAR
        </div>
        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
            Jl. Raya Kampus Terpadu No. 01 • Telp: (021) 7654321 • Email: perpustakaan@kampus.ac.id
        </div>
        <div style="border-bottom: 3px double #0f172a; margin-top: 10px; margin-bottom: 14px;"></div>

        <div style="font-size: 15px; font-weight: 700; text-transform: uppercase;">
            LAPORAN REKAPITULASI TRANSAKSI PEMINJAMAN BUKU
        </div>
        <div style="font-size: 11px; color: #475569; margin-top: 4px;">
            Tanggal Cetak: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong> &bull; Dicetak oleh: <strong>{{ auth()->user()->name }}</strong>
        </div>
    </div>

    <!-- Header Tampilan Web (No Print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="margin-bottom: 4px;">📑 Laporan Peminjaman</h1>
            <p style="color: #64748b; font-size: 14px; margin: 0;">
                Data disinkronkan secara live melalui endpoint RESTful API internal (<code>GET /api/loans</code>).
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn" style="background: linear-gradient(135deg, #86d3b4, #5cb996); color: white; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                <span>🖨️</span> Cetak / Simpan PDF
            </button>
            <a href="{{ route('loans.index') }}" class="btn">
                Kembali ke Peminjaman
            </a>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">ID</th>
                <th>Anggota</th>
                <th>Petugas</th>
                <th>Buku yang Dipinjam</th>
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
                    <td style="text-align: center;"><strong>#{{ $loan['id'] }}</strong></td>
                    <td>{{ $loan['member']['nama'] ?? '-' }}</td>
                    <td>{{ $loan['petugas']['name'] ?? '-' }}</td>
                    <td>
                        @foreach ($loan['books'] as $book)
                            <span style="display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 12px; margin: 1px 0;">
                                📖 {{ $book['judul'] ?? '-' }}
                            </span>@if (!$loop->last)<br>@endif
                        @endforeach
                    </td>
                    <td style="text-align: center;">{{ $loan['tanggal_pinjam'] }}</td>
                    <td style="text-align: center;">{{ $loan['tanggal_kembali'] }}</td>
                    <td style="text-align: center;">
                        @if (!empty($loan['tanggal_dikembalikan']))
                            <strong style="color: #047857;">{{ $loan['tanggal_dikembalikan'] }}</strong>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if (!empty($loan['denda']) && $loan['denda'] > 0)
                            <span style="font-weight: 700; color: #b91c1c;">
                                {{ $loan['denda_formatted'] ?? ('Rp ' . number_format($loan['denda'], 0, ',', '.')) }}
                            </span>
                            @if (!empty($loan['hari_terlambat']))
                                <div style="font-size: 11px; color: #ef4444;">({{ $loan['hari_terlambat'] }} hr)</div>
                            @endif
                        @else
                            <span style="color: #64748b; font-size: 13px;">Rp 0</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
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

    <!-- Lembar Pengesahan / Tanda Tangan (Hanya Muncul Saat Cetak) -->
    <div class="print-only" style="margin-top: 40px; page-break-inside: avoid;">
        <table style="width: 100%; border: none !important; box-shadow: none !important;">
            <tr style="border: none !important;">
                <td style="width: 50%; border: none !important; text-align: center; padding: 0;">
                    <div>Mengetahui,</div>
                    <div style="font-weight: 700; margin-top: 4px;">Kepala UPT Perpustakaan</div>
                    <div style="height: 65px;"></div>
                    <div style="font-weight: 700; text-decoration: underline;">Dr. Ir. H. Perpustakaan, M.Kom.</div>
                    <div style="font-size: 10pt; color: #475569;">NIP. 19800101 200501 1 002</div>
                </td>
                <td style="width: 50%; border: none !important; text-align: center; padding: 0;">
                    <div>Kota Kampus, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="font-weight: 700; margin-top: 4px;">Petugas Pelayanan</div>
                    <div style="height: 65px;"></div>
                    <div style="font-weight: 700; text-decoration: underline;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 10pt; color: #475569;">NIP/ID: {{ auth()->user()->id }}00{{ auth()->user()->id }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Pagination Web (No Print) -->
    @if ($meta && $meta['last_page'] > 1)
        <div class="pagination no-print">
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
