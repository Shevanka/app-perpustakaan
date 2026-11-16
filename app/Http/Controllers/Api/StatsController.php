<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;

class StatsController extends Controller
{
    public function index()
    {
        $kategoriStats = Category::withCount('books')
            ->orderByDesc('books_count')
            ->get()
            ->map(fn ($cat) => [
                'nama' => $cat->nama_kategori,
                'total' => $cat->books_count,
            ]);

        $statusStats = [
            'dipinjam' => Loan::where('status', 'dipinjam')
                ->whereDate('tanggal_kembali', '>=', now()->toDateString())
                ->count(),
            'dikembalikan' => Loan::where('status', 'dikembalikan')->count(),
            'terlambat' => Loan::where('status', 'terlambat')
                ->orWhere(function ($q) {
                    $q->where('status', 'dipinjam')
                        ->whereDate('tanggal_kembali', '<', now()->toDateString());
                })->count(),
        ];

        return response()->json([
            'total_buku' => Book::count(),
            'total_anggota' => Member::count(),
            'peminjaman_aktif' => Loan::where('status', 'dipinjam')->count(),
            'total_denda' => (int) Loan::sum('denda'),
            'kategori_stats' => $kategoriStats,
            'status_stats' => $statusStats,
        ]);
    }
}
