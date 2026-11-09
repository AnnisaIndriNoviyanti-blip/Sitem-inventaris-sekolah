<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeminjamanDetail extends Model
{
    protected $table = 'peminjaman_detail';

    protected $fillable = [
        'peminjaman_id',
        'barang_tu_id',
        'jumlah',
        'ttd',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(
            BarangTu::class,
            'barang_tu_id'
        );
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(
            LaporanKerusakan::class,
            'peminjaman_detail_id'
        );
    }
}
