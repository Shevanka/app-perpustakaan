@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>📊 Dashboard Statistik</h1>
    <p>Ringkasan statistik operasional perpustakaan secara real-time, dikonsumsi langsung dari endpoint internal <code>GET /api/stats</code>.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div style="font-size: 24px; margin-bottom: 8px;">📚</div>
            <div class="value">{{ $stats['total_buku'] }}</div>
            <div class="label">Total Buku</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 24px; margin-bottom: 8px;">👥</div>
            <div class="value">{{ $stats['total_anggota'] }}</div>
            <div class="label">Total Anggota</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 24px; margin-bottom: 8px;">⏳</div>
            <div class="value">{{ $stats['peminjaman_aktif'] }}</div>
            <div class="label">Peminjaman Aktif</div>
        </div>
    </div>

    <div style="margin-top: 32px; display: flex; gap: 14px; flex-wrap: wrap;">
        <a href="{{ route('loans.report') }}" class="btn">📄 Lihat Laporan Peminjaman</a>
        <a href="{{ route('loans.create') }}" class="btn" style="background: linear-gradient(135deg, #86d3b4, #5cb996); box-shadow: 5px 5px 12px rgba(134,211,180,0.4), -5px -5px 12px #ffffff;">➕ Catat Peminjaman Baru</a>
    </div>
@endsection
