<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $counts = [
            'all' => Loan::count(),
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

        $query = Loan::with(['member', 'user', 'loanItems.book'])->latest('id');

        if ($status === 'dipinjam') {
            $query->where('status', 'dipinjam')
                ->whereDate('tanggal_kembali', '>=', now()->toDateString());
        } elseif ($status === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($status === 'terlambat') {
            $query->where(function ($q) {
                $q->where('status', 'terlambat')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'dipinjam')
                            ->whereDate('tanggal_kembali', '<', now()->toDateString());
                    });
            });
        }

        $loans = $query->paginate(10)->withQueryString();

        return view('loans.index', compact('loans', 'status', 'counts'));
    }

    public function report(Request $request)
    {
        $body = ['data' => [], 'meta' => null];

        try {
            $response = Http::get(config('services.internal_api.base_url').'/api/loans', [
                'page' => $request->query('page', 1),
            ]);

            if ($response->successful()) {
                $body = $response->json();
            }
        } catch (ConnectionException $e) {
            // Server internal API (port 8011) tidak bisa dihubungi - tabel laporan tampil kosong.
        }

        return view('loans.report', [
            'loans' => $body['data'] ?? [],
            'meta' => $body['meta'] ?? null,
        ]);
    }

    public function create()
    {
        $members = Member::orderBy('nama')->get();
        $books = Book::orderBy('judul')->get();

        return view('loans.create', compact('members', 'books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'integer|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'book_ids.required' => 'Pilih minimal satu buku yang dipinjam.',
        ]);

        // Cek stok buku yang dipilih
        $books = Book::whereIn('id', $validated['book_ids'])->get();
        foreach ($books as $book) {
            if ($book->stok <= 0) {
                return back()->withInput()->withErrors([
                    'book_ids' => "Buku \"{$book->judul}\" sedang habis (stok: 0) dan tidak dapat dipinjam.",
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $loan = Loan::create([
                'member_id' => $validated['member_id'],
                'user_id' => auth()->id(),
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status' => 'dipinjam',
                'denda' => 0,
            ]);

            foreach ($validated['book_ids'] as $bookId) {
                $loan->loanItems()->create(['book_id' => $bookId]);
                Book::where('id', $bookId)->decrement('stok');
            }
        });

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat dan stok buku otomatis dikurangi.');
    }

    public function show(string $id)
    {
        $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);

        return view('loans.show', compact('loan'));
    }

    public function edit(string $id)
    {
        $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);

        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, string $id)
    {
        $loan = Loan::with('loanItems.book')->findOrFail($id);

        $validated = $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|in:dipinjam,dikembalikan,terlambat',
        ], [
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $oldStatus = $loan->status;
            $newStatus = $validated['status'];

            if ($newStatus === 'dikembalikan' && $oldStatus !== 'dikembalikan') {
                $today = now()->startOfDay();
                $tenggat = Carbon::parse($validated['tanggal_kembali'])->startOfDay();
                $hariTerlambat = $today->greaterThan($tenggat) ? (int) $tenggat->diffInDays($today) : 0;
                $validated['denda'] = $hariTerlambat * 1000;
                $validated['tanggal_dikembalikan'] = $today->toDateString();

                foreach ($loan->loanItems as $item) {
                    $item->book?->increment('stok');
                }
            } elseif ($oldStatus === 'dikembalikan' && $newStatus !== 'dikembalikan') {
                $validated['tanggal_dikembalikan'] = null;
                $validated['denda'] = 0;
                foreach ($loan->loanItems as $item) {
                    $item->book?->decrement('stok');
                }
            }

            $loan->update($validated);
        });

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $loan = Loan::with('loanItems.book')->findOrFail($id);

        DB::transaction(function () use ($loan) {
            // Jika status belum dikembalikan, kembalikan stok buku sebelum data transaksi dihapus
            if ($loan->status !== 'dikembalikan') {
                foreach ($loan->loanItems as $item) {
                    $item->book?->increment('stok');
                }
            }

            $loan->loanItems()->delete();
            $loan->delete();
        });

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }

    public function kembalikan(string $id)
    {
        $loan = Loan::with('loanItems.book')->findOrFail($id);

        if ($loan->status === 'dikembalikan') {
            return redirect()->route('loans.index')
                ->with('info', 'Buku pada transaksi ini sudah pernah dikembalikan.');
        }

        $hariTerlambat = 0;
        $denda = 0;

        DB::transaction(function () use ($loan, &$hariTerlambat, &$denda) {
            $tenggat = Carbon::parse($loan->tanggal_kembali)->startOfDay();
            $hariIni = now()->startOfDay();
            $hariTerlambat = $hariIni->greaterThan($tenggat) ? (int) $tenggat->diffInDays($hariIni) : 0;
            $denda = $hariTerlambat * 1000;

            $loan->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
                'denda' => $denda,
            ]);

            foreach ($loan->loanItems as $item) {
                $item->book?->increment('stok');
            }
        });

        $pesan = 'Buku berhasil dikembalikan dan stok buku telah dikembalikan.';
        if ($denda > 0) {
            $pesan .= " Terlambat {$hariTerlambat} hari (Denda: Rp ".number_format($denda, 0, ',', '.').').';
        } else {
            $pesan .= ' Pengembalian tepat waktu tanpa denda.';
        }

        return redirect()->route('loans.index')
            ->with('success', $pesan);
    }
}
