<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
    'user_id',
    'nama_peminjam',
    'tanggal_pinjam',
    'tanggal_rencana_kembali',
    'tanggal_kembali',
    'keperluan',
    'catatan',
    'status',
    'ttd_peminjam',
    'ttd_peminjam_data',
    'ttd_peminjaman_at',
    'ttd_pengembalian_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_rencana_kembali' => 'date',
            'tanggal_kembali' => 'date',
            'ttd_peminjaman_at' => 'datetime',
            'ttd_peminjam_data' => 'array',
            'ttd_pengembalian_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasMany 
    {
        return $this->hasMany(
            PeminjamanDetail::class,
            'peminjaman_id'
        );
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(
            LaporanKerusakan::class,

            'peminjaman_id'
        );
    }
    
}
