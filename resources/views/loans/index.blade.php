@extends('layouts.app')

@section('title', 'Daftar Peminjaman')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="margin-bottom: 4px;">Daftar Peminjaman</h1>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Kelola transaksi peminjaman, pengembalian, status denda, dan stok buku.</p>
        </div>
        <a href="{{ route('loans.create') }}" class="btn">+ Tambah Peminjaman</a>
    </div>

    <!-- Quick Filter Pills Neumorphic -->
    <div style="display: flex; gap: 10px; margin-bottom: 22px; flex-wrap: wrap;">
        <a href="{{ route('loans.index', ['status' => 'all']) }}"
           style="text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;
           {{ $status === 'all'
               ? 'background: #eef2f6; color: #3b82f6; box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;'
               : 'background: #eef2f6; color: #64748b; box-shadow: 4px 4px 10px #d1d9e6, -4px -4px 10px #ffffff;' }}">
            <span>📑 Semua</span>
            <span style="background: {{ $status === 'all' ? '#bfdbfe' : '#e2e8f0' }}; color: {{ $status === 'all' ? '#1d4ed8' : '#475569' }}; padding: 1px 7px; border-radius: 12px; font-size: 11px;">
                {{ $counts['all'] }}
            </span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'dipinjam']) }}"
           style="text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;
           {{ $status === 'dipinjam'
               ? 'background: #eef2f6; color: #0284c7; box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;'
               : 'background: #eef2f6; color: #64748b; box-shadow: 4px 4px 10px #d1d9e6, -4px -4px 10px #ffffff;' }}">
            <span>⏳ Sedang Dipinjam</span>
            <span style="background: #bae6fd; color: #0369a1; padding: 1px 7px; border-radius: 12px; font-size: 11px;">
                {{ $counts['dipinjam'] }}
            </span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'dikembalikan']) }}"
           style="text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;
           {{ $status === 'dikembalikan'
               ? 'background: #eef2f6; color: #059669; box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;'
               : 'background: #eef2f6; color: #64748b; box-shadow: 4px 4px 10px #d1d9e6, -4px -4px 10px #ffffff;' }}">
            <span>✅ Sudah Dikembalikan</span>
            <span style="background: #a7f3d0; color: #047857; padding: 1px 7px; border-radius: 12px; font-size: 11px;">
                {{ $counts['dikembalikan'] }}
            </span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'terlambat']) }}"
           style="text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;
           {{ $status === 'terlambat'
               ? 'background: #eef2f6; color: #dc2626; box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;'
               : 'background: #eef2f6; color: #64748b; box-shadow: 4px 4px 10px #d1d9e6, -4px -4px 10px #ffffff;' }}">
            <span>⚠️ Terlambat / Denda</span>
            <span style="background: #fecaca; color: #b91c1c; padding: 1px 7px; border-radius: 12px; font-size: 11px;">
                {{ $counts['terlambat'] }}
            </span>
        </a>
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
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                <tr>
                    <td><strong>#{{ $loan->id }}</strong></td>
                    <td>
                        <strong>{{ $loan->member->nama ?? '-' }}</strong>
                        <div style="font-size: 11px; color: #64748b;">NIM: {{ $loan->member->nim ?? '-' }}</div>
                    </td>
                    <td>{{ $loan->user->name ?? '-' }}</td>
                    <td>
                        @foreach ($loan->loanItems as $item)
                            <span style="display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 12px; margin: 1px 0;">
                                📖 {{ $item->book->judul ?? 'Buku dihapus' }}
                            </span>@if (!$loop->last)<br>@endif
                        @endforeach
                    </td>
                    <td>{{ $loan->tanggal_pinjam }}</td>
                    <td>{{ $loan->tanggal_kembali }}</td>
                    <td>
                        @if ($loan->tanggal_dikembalikan)
                            <strong style="color: #047857;">{{ $loan->tanggal_dikembalikan }}</strong>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($loan->denda > 0)
                            <span style="font-weight: 700; color: #b91c1c;">{{ $loan->formatted_denda }}</span>
                            <div style="font-size: 11px; color: #ef4444;">({{ $loan->hari_terlambat }} hari)</div>
                        @else
                            <span style="color: #64748b; font-size: 13px;">Rp 0</span>
                        @endif
                    </td>
                    <td>
                        @if ($loan->status === 'dikembalikan')
                            <span class="badge badge-dikembalikan">Dikembalikan</span>
                        @elseif ($loan->hari_terlambat > 0)
                            <span class="badge badge-terlambat">Terlambat ({{ $loan->hari_terlambat }} hr)</span>
                        @else
                            <span class="badge badge-dipinjam">Dipinjam</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('loans.show', $loan->id) }}">Detail</a>
                        <a href="{{ route('loans.edit', $loan->id) }}">Edit</a>
                        <form class="inline" action="{{ route('loans.destroy', $loan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok buku akan dipulihkan jika belum dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                        @if ($loan->status !== 'dikembalikan')
                            <form class="inline" action="{{ route('loans.kembalikan', $loan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" style="background: #ecfdf5; color: #047857; border-color: #a7f3d0;" title="Kembalikan buku sekarang">
                                    ✓ Kembalikan
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 32px; color: #94a3b8;">
                        Tidak ada data peminjaman untuk filter status ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $loans->links() }}
@endsection
