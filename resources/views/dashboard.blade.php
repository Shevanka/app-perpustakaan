@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 6px;">📊 Dashboard Perpustakaan</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">
            Ringkasan statistik operasional, visualisasi peredaran buku, dan monitoring denda secara real-time.
        </p>
    </div>

    <!-- 4 Neumorphic Stat Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="stat-card">
            <div style="font-size: 26px; margin-bottom: 6px;">📚</div>
            <div class="value">{{ $stats['total_buku'] }}</div>
            <div class="label">Total Buku</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 26px; margin-bottom: 6px;">👥</div>
            <div class="value">{{ $stats['total_anggota'] }}</div>
            <div class="label">Total Anggota</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 26px; margin-bottom: 6px;">⏳</div>
            <div class="value">{{ $stats['peminjaman_aktif'] }}</div>
            <div class="label">Peminjaman Aktif</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 26px; margin-bottom: 6px;">💰</div>
            <div class="value" style="font-size: 22px; color: #b91c1c;">
                Rp {{ number_format($stats['total_denda'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="label">Denda Terkumpul</div>
        </div>
    </div>

    <!-- 2 Interactive Pastel Charts -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Chart 1: Koleksi per Kategori (Doughnut) -->
        <div style="background: #eef2f6; border-radius: 20px; padding: 22px; box-shadow: 6px 6px 14px #d1d9e6, -6px -6px 14px #ffffff;">
            <h3 style="margin-top: 0; margin-bottom: 14px; font-size: 16px; color: #334155; display: flex; align-items: center; gap: 8px;">
                <span>📖</span> Koleksi Buku per Kategori
            </h3>
            <div style="position: relative; height: 260px; display: flex; justify-content: center; align-items: center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Transaksi Peminjaman (Bar Chart) -->
        <div style="background: #eef2f6; border-radius: 20px; padding: 22px; box-shadow: 6px 6px 14px #d1d9e6, -6px -6px 14px #ffffff;">
            <h3 style="margin-top: 0; margin-bottom: 14px; font-size: 16px; color: #334155; display: flex; align-items: center; gap: 8px;">
                <span>🔄</span> Status Transaksi Peminjaman
            </h3>
            <div style="position: relative; height: 260px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Action Navigation -->
    <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('loans.index') }}" class="btn">
            📋 Kelola Peminjaman
        </a>
        <a href="{{ route('loans.report') }}" class="btn" style="background: linear-gradient(135deg, #a7f3d0, #6ee7b7); color: #065f46; border: 1px solid #a7f3d0;">
            🖨️ Cetak Laporan Peminjaman
        </a>
        <a href="{{ route('loans.create') }}" class="btn" style="background: linear-gradient(135deg, #86d3b4, #5cb996); color: #ffffff;">
            ➕ Catat Peminjaman Baru
        </a>
        <a href="{{ route('books.create') }}" class="btn">
            📚 Tambah Buku Baru
        </a>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Kategori
            const kategoriData = @json($stats['kategori_stats'] ?? []);
            const catLabels = kategoriData.length > 0 ? kategoriData.map(c => c.nama) : ['Belum Ada Kategori'];
            const catValues = kategoriData.length > 0 ? kategoriData.map(c => c.total) : [1];

            // Palette Pastel Elegan
            const pastelColors = [
                '#93c5fd', // Pastel Blue
                '#a7f3d0', // Pastel Mint
                '#fbcfe8', // Pastel Pink
                '#c4b5fd', // Pastel Purple
                '#fde68a', // Pastel Yellow
                '#fed7aa', // Pastel Orange
                '#bae6fd', // Pastel Sky
                '#ddd6fe', // Pastel Lavender
            ];

            // Render Doughnut Chart
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catValues,
                        backgroundColor: pastelColors.slice(0, catLabels.length),
                        borderColor: '#eef2f6',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 12,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Data Status Peminjaman
            const statusStats = @json($stats['status_stats'] ?? ['dipinjam' => 0, 'dikembalikan' => 0, 'terlambat' => 0]);
            const statusLabels = ['Sedang Dipinjam', 'Sudah Dikembalikan', 'Terlambat / Denda'];
            const statusValues = [
                statusStats.dipinjam || 0,
                statusStats.dikembalikan || 0,
                statusStats.terlambat || 0
            ];

            // Render Bar Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: statusValues,
                        backgroundColor: [
                            '#93c5fd', // Biru Pastel (Dipinjam)
                            '#86efac', // Hijau Pastel (Dikembalikan)
                            '#fca5a5'  // Merah Pastel (Terlambat)
                        ],
                        borderRadius: 10,
                        borderWidth: 0,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { family: "'Inter', sans-serif", size: 11 }
                            },
                            grid: {
                                color: '#e2e8f0',
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 11 }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
