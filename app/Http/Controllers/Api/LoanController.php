<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['member', 'user', 'loanItems.book'])
            ->latest('id')
            ->paginate(10);

        return LoanResource::collection($loans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'user_id' => 'required|integer|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'integer|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'user_id.required' => 'Petugas (user_id) wajib diisi.',
            'user_id.exists' => 'Petugas yang dipilih tidak valid.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'book_ids.required' => 'Pilih minimal satu buku.',
        ]);

        // Cek stok buku
        $books = Book::whereIn('id', $validated['book_ids'])->get();
        foreach ($books as $book) {
            if ($book->stok <= 0) {
                return response()->json([
                    'message' => "Buku \"{$book->judul}\" sedang habis (stok: 0).",
                    'errors' => [
                        'book_ids' => ["Buku \"{$book->judul}\" sedang habis (stok: 0)."],
                    ],
                ], 422);
            }
        }

        $loan = DB::transaction(function () use ($validated) {
            $loan = Loan::create([
                'member_id' => $validated['member_id'],
                'user_id' => $validated['user_id'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status' => 'dipinjam',
                'denda' => 0,
            ]);

            foreach ($validated['book_ids'] as $bookId) {
                $loan->loanItems()->create(['book_id' => $bookId]);
                Book::where('id', $bookId)->decrement('stok');
            }

            return $loan;
        });

        $loan->refresh()->load(['member', 'user', 'loanItems.book']);

        return (new LoanResource($loan))->response()->setStatusCode(201);
    }

    public function kembalikan(string $id)
    {
        $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);

        if ($loan->status === 'dikembalikan') {
            return new LoanResource($loan);
        }

        DB::transaction(function () use ($loan) {
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

        $loan->refresh()->load(['member', 'user', 'loanItems.book']);

        return new LoanResource($loan);
    }
}
