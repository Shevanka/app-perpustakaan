<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardAndBookFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'nama_kategori' => 'Sains',
            'deskripsi' => 'Buku sains dan penelitian',
        ]);
    }

    public function test_dashboard_renders_with_stats_and_charts(): void
    {
        Book::create([
            'judul' => 'Kosmos',
            'penulis' => 'Carl Sagan',
            'penerbit' => 'Gramedia',
            'tahun_terbit' => 2016,
            'isbn' => '9786020332115',
            'stok' => 4,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('categoryChart');
        $response->assertSee('statusChart');
        $response->assertSee('Denda Terkumpul');
    }

    public function test_loan_index_filters_correctly(): void
    {
        $member = Member::factory()->create();

        // Pinjaman aktif
        $loanDipinjam = Loan::create([
            'member_id' => $member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Pinjaman sudah dikembalikan
        $loanDikembalikan = Loan::create([
            'member_id' => $member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->subDays(10)->toDateString(),
            'tanggal_kembali' => now()->subDays(3)->toDateString(),
            'tanggal_dikembalikan' => now()->subDays(2)->toDateString(),
            'status' => 'dikembalikan',
            'denda' => 1000,
        ]);

        // Filter: dipinjam
        $responseDipinjam = $this->actingAs($this->user)->get(route('loans.index', ['status' => 'dipinjam']));
        $responseDipinjam->assertStatus(200);
        $responseDipinjam->assertSee("<strong>#{$loanDipinjam->id}</strong>", false);
        $responseDipinjam->assertDontSee("<strong>#{$loanDikembalikan->id}</strong>", false);

        // Filter: dikembalikan
        $responseDikembalikan = $this->actingAs($this->user)->get(route('loans.index', ['status' => 'dikembalikan']));
        $responseDikembalikan->assertStatus(200);
        $responseDikembalikan->assertSee("<strong>#{$loanDikembalikan->id}</strong>", false);
        $responseDikembalikan->assertDontSee("<strong>#{$loanDipinjam->id}</strong>", false);
    }

    public function test_book_cover_can_be_uploaded_and_stored(): void
    {
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('cover-buku.jpg', 400, 600);

        $response = $this->actingAs($this->user)->post(route('books.store'), [
            'judul' => 'Fisika Kuantum',
            'penulis' => 'Richard Feynman',
            'penerbit' => 'Penerbit ITB',
            'tahun_terbit' => 2020,
            'isbn' => '9786021234567',
            'stok' => 7,
            'category_id' => $this->category->id,
            'sampul' => $cover,
        ]);

        $response->assertRedirect(route('books.index'));

        $book = Book::where('judul', 'Fisika Kuantum')->first();
        $this->assertNotNull($book);
        $this->assertNotNull($book->sampul);
        Storage::disk('public')->assertExists($book->sampul);
    }

    public function test_book_cover_can_be_updated_and_replaces_old_file(): void
    {
        Storage::fake('public');

        $initialCover = UploadedFile::fake()->image('old-cover.jpg');
        $oldPath = $initialCover->store('covers', 'public');

        $book = Book::create([
            'judul' => 'Biologi Modern',
            'penulis' => 'Campbell',
            'penerbit' => 'Erlangga',
            'tahun_terbit' => 2021,
            'isbn' => '9786029999999',
            'stok' => 3,
            'category_id' => $this->category->id,
            'sampul' => $oldPath,
        ]);

        $newCover = UploadedFile::fake()->image('new-cover.jpg');

        $response = $this->actingAs($this->user)->put(route('books.update', $book->id), [
            'judul' => 'Biologi Modern Edisi 2',
            'penulis' => 'Campbell',
            'penerbit' => 'Erlangga',
            'tahun_terbit' => 2022,
            'isbn' => '9786029999999',
            'stok' => 3,
            'category_id' => $this->category->id,
            'sampul' => $newCover,
        ]);

        $response->assertRedirect(route('books.index'));

        $freshBook = $book->fresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($freshBook->sampul);
    }

    public function test_deleting_book_removes_cover_file(): void
    {
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('cover-to-delete.jpg');
        $path = $cover->store('covers', 'public');

        $book = Book::create([
            'judul' => 'Kimia Terapan',
            'penulis' => 'Raymond Chang',
            'penerbit' => 'Erlangga',
            'tahun_terbit' => 2019,
            'isbn' => '9786028888888',
            'stok' => 2,
            'category_id' => $this->category->id,
            'sampul' => $path,
        ]);

        $response = $this->actingAs($this->user)->delete(route('books.destroy', $book->id));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
