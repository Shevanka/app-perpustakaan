<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->latest('id')->paginate(10);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('books.create', compact('categories'));
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('sampul')) {
            $validated['sampul'] = $request->file('sampul')->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', "Buku \"{$validated['judul']}\" berhasil ditambahkan.");
    }

    public function show(string $id)
    {
        $book = Book::with('category')->findOrFail($id);

        return view('books.show', compact('book'));
    }

    public function edit(string $id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'penulis' => 'required|string|max:100',
            'penerbit' => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:'.date('Y'),
            'isbn' => 'nullable|string|max:20',
            'stok' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'sampul' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'sampul.image' => 'Berkas sampul harus berupa gambar.',
            'sampul.mimes' => 'Format sampul harus jpeg, png, jpg, atau webp.',
            'sampul.max' => 'Ukuran berkas sampul maksimal 2MB.',
        ]);

        if ($request->hasFile('sampul')) {
            if ($book->sampul && Storage::disk('public')->exists($book->sampul)) {
                Storage::disk('public')->delete($book->sampul);
            }
            $validated['sampul'] = $request->file('sampul')->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', "Buku \"{$validated['judul']}\" berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);

        if ($book->sampul && Storage::disk('public')->exists($book->sampul)) {
            Storage::disk('public')->delete($book->sampul);
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
