<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $kategoriStats = Category::withCount('books')
            ->orderByDesc('books_count')
            ->get()
            ->map(fn ($cat) => [
                'nama' => $cat->nama_kategori,
                'total' => $cat->books_count,
            ])->toArray();

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

        $stats = [
            'total_buku' => Book::count(),
            'total_anggota' => Member::count(),
            'peminjaman_aktif' => Loan::where('status', 'dipinjam')->count(),
            'total_denda' => (int) Loan::sum('denda'),
            'kategori_stats' => $kategoriStats,
            'status_stats' => $statusStats,
        ];

        try {
            $response = Http::get(config('services.internal_api.base_url').'/api/stats');

            if ($response->successful()) {
                $stats = array_merge($stats, $response->json());
            }
        } catch (ConnectionException $e) {
            // Server internal API (port 8011) tidak bisa dihubungi - menggunakan data lokal langsung.
        }

        return view('dashboard', compact('stats'));
    }
}
