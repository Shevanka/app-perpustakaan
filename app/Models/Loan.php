<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'status',
        'denda',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'denda' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Hitung jumlah hari keterlambatan pengembalian buku.
     */
    public function getHariTerlambatAttribute(): int
    {
        if (! $this->tanggal_kembali) {
            return 0;
        }

        $tenggat = Carbon::parse($this->tanggal_kembali)->startOfDay();
        $selesai = $this->tanggal_dikembalikan
            ? Carbon::parse($this->tanggal_dikembalikan)->startOfDay()
            : now()->startOfDay();

        return $selesai->greaterThan($tenggat) ? (int) $tenggat->diffInDays($selesai) : 0;
    }

    /**
     * Hitung nominal denda (Rp 1.000 / hari terlambat).
     * Jika sudah tersimpan di database dan sudah dikembalikan, gunakan nilai tersimpan.
     * Jika belum dikembalikan, hitung denda berjalan secara real-time.
     */
    public function getDendaAttribute(): int
    {
        if (array_key_exists('denda', $this->attributes) && $this->attributes['denda'] > 0) {
            return (int) $this->attributes['denda'];
        }

        if ($this->status === 'dikembalikan') {
            return (int) ($this->attributes['denda'] ?? 0);
        }

        return $this->hari_terlambat * 1000;
    }

    /**
     * Format rupiah nominal denda.
     */
    public function getFormattedDendaAttribute(): string
    {
        return 'Rp '.number_format($this->denda, 0, ',', '.');
    }
}
