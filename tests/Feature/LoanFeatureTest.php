<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Member $member;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->member = Member::factory()->create();
        $this->category = Category::create([
            'nama_kategori' => 'Teknologi',
            'deskripsi' => 'Buku seputar IT dan pemrograman',
        ]);
    }

    public function test_user_cannot_borrow_book_when_stock_is_zero(): void
    {
        $book = Book::create([
            'judul' => 'Clean Code',
            'penulis' => 'Robert C. Martin',
            'penerbit' => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'isbn' => '9780132350884',
            'stok' => 0,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('loans.store'), [
            'member_id' => $this->member->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'book_ids' => [$book->id],
        ]);

        $response->assertSessionHasErrors('book_ids');
        $this->assertDatabaseCount('loans', 0);
        $this->assertSame(0, $book->fresh()->stok);
    }

    public function test_borrowing_book_decrements_book_stock(): void
    {
        $book = Book::create([
            'judul' => 'Refactoring',
            'penulis' => 'Martin Fowler',
            'penerbit' => 'Addison-Wesley',
            'tahun_terbit' => 2018,
            'isbn' => '9780134757599',
            'stok' => 5,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('loans.store'), [
            'member_id' => $this->member->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'book_ids' => [$book->id],
        ]);

        $response->assertRedirect(route('loans.index'));
        $this->assertDatabaseCount('loans', 1);
        $this->assertSame(4, $book->fresh()->stok);
    }

    public function test_returning_book_increments_book_stock_and_calculates_denda(): void
    {
        $book = Book::create([
            'judul' => 'The Pragmatic Programmer',
            'penulis' => 'Andy Hunt',
            'penerbit' => 'Pragmatic Bookshelf',
            'tahun_terbit' => 2019,
            'isbn' => '9780135957059',
            'stok' => 3,
            'category_id' => $this->category->id,
        ]);

        // Tanggal tenggat 3 hari yang lalu
        $tanggalPinjam = now()->subDays(10)->toDateString();
        $tanggalKembali = now()->subDays(3)->toDateString();

        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'dipinjam',
            'denda' => 0,
        ]);
        $loan->loanItems()->create(['book_id' => $book->id]);

        $response = $this->actingAs($this->user)->put(route('loans.kembalikan', $loan->id));

        $response->assertRedirect(route('loans.index'));

        $freshLoan = $loan->fresh();
        $this->assertSame('dikembalikan', $freshLoan->status);
        $this->assertSame(now()->toDateString(), $freshLoan->tanggal_dikembalikan);
        $this->assertSame(3, $freshLoan->hari_terlambat);
        $this->assertSame(3000, $freshLoan->denda);
        $this->assertSame('Rp 3.000', $freshLoan->formatted_denda);

        // Stok buku bertambah 1 kembali
        $this->assertSame(4, $book->fresh()->stok);
    }

    public function test_returning_book_on_time_has_zero_denda(): void
    {
        $book = Book::create([
            'judul' => 'Domain-Driven Design',
            'penulis' => 'Eric Evans',
            'penerbit' => 'Addison-Wesley',
            'tahun_terbit' => 2003,
            'isbn' => '9780321125217',
            'stok' => 2,
            'category_id' => $this->category->id,
        ]);

        $tanggalPinjam = now()->subDays(2)->toDateString();
        $tanggalKembali = now()->addDays(5)->toDateString();

        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'dipinjam',
            'denda' => 0,
        ]);
        $loan->loanItems()->create(['book_id' => $book->id]);

        $response = $this->actingAs($this->user)->put(route('loans.kembalikan', $loan->id));

        $response->assertRedirect(route('loans.index'));

        $freshLoan = $loan->fresh();
        $this->assertSame('dikembalikan', $freshLoan->status);
        $this->assertSame(0, $freshLoan->hari_terlambat);
        $this->assertSame(0, $freshLoan->denda);
        $this->assertSame('Rp 0', $freshLoan->formatted_denda);
        $this->assertSame(3, $book->fresh()->stok);
    }

    public function test_deleting_active_loan_restores_book_stock(): void
    {
        $book = Book::create([
            'judul' => 'Patterns of Enterprise Application Architecture',
            'penulis' => 'Martin Fowler',
            'penerbit' => 'Addison-Wesley',
            'tahun_terbit' => 2002,
            'isbn' => '9780321127426',
            'stok' => 1,
            'category_id' => $this->category->id,
        ]);

        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
        ]);
        $loan->loanItems()->create(['book_id' => $book->id]);

        $response = $this->actingAs($this->user)->delete(route('loans.destroy', $loan->id));

        $response->assertRedirect(route('loans.index'));
        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
        $this->assertSame(2, $book->fresh()->stok);
    }

    public function test_updating_loan_status_manages_denda_and_stock(): void
    {
        $book = Book::create([
            'judul' => 'Working Effectively with Legacy Code',
            'penulis' => 'Michael Feathers',
            'penerbit' => 'Prentice Hall',
            'tahun_terbit' => 2004,
            'isbn' => '9780131177055',
            'stok' => 2,
            'category_id' => $this->category->id,
        ]);

        $tanggalPinjam = now()->subDays(6)->toDateString();
        $tanggalKembali = now()->subDays(2)->toDateString();

        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'dipinjam',
            'denda' => 0,
        ]);
        $loan->loanItems()->create(['book_id' => $book->id]);

        // Update status to 'dikembalikan' via edit form
        $response = $this->actingAs($this->user)->put(route('loans.update', $loan->id), [
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'dikembalikan',
        ]);

        $response->assertRedirect(route('loans.index'));
        $freshLoan = $loan->fresh();
        $this->assertSame('dikembalikan', $freshLoan->status);
        $this->assertSame(2000, $freshLoan->denda);
        $this->assertSame(3, $book->fresh()->stok);
    }

    public function test_loan_model_dynamic_denda_for_active_loan_past_due(): void
    {
        $loan = new Loan([
            'tanggal_pinjam' => now()->subDays(10)->toDateString(),
            'tanggal_kembali' => now()->subDays(4)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $this->assertSame(4, $loan->hari_terlambat);
        $this->assertSame(4000, $loan->denda);
        $this->assertSame('Rp 4.000', $loan->formatted_denda);
    }

    public function test_api_manages_stock_and_calculates_denda(): void
    {
        $book = Book::create([
            'judul' => 'Designing Data-Intensive Applications',
            'penulis' => 'Martin Kleppmann',
            'penerbit' => "O'Reilly Media",
            'tahun_terbit' => 2017,
            'isbn' => '9781449373320',
            'stok' => 5,
            'category_id' => $this->category->id,
        ]);

        // Test API store decrements stock
        $storeResponse = $this->postJson('/api/loans', [
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->subDays(5)->toDateString(),
            'tanggal_kembali' => now()->subDays(2)->toDateString(),
            'book_ids' => [$book->id],
        ]);

        $storeResponse->assertStatus(201);
        $this->assertSame(4, $book->fresh()->stok);

        $loanId = $storeResponse->json('data.id');

        // Test API kembalikan calculates denda & increments stock
        $returnResponse = $this->putJson("/api/loans/{$loanId}/kembalikan");

        $returnResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonPath('data.hari_terlambat', 2)
            ->assertJsonPath('data.denda', 2000)
            ->assertJsonPath('data.denda_formatted', 'Rp 2.000');

        $this->assertSame(5, $book->fresh()->stok);
    }
}
