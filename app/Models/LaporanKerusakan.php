<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKerusakan extends Model
{
    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'peminjaman_id',
        'peminjaman_detail_id',
        'jenis_laporan',
        'tanggal_laporan',
        'nomor_whatsapp',
        'keterangan',
        'bukti_laporan',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(
            Peminjaman::class,
            'peminjaman_id'
        );
    }

    public function peminjamanDetail(): BelongsTo
    {
        return $this->belongsTo(
            PeminjamanDetail::class,
            'peminjaman_detail_id'
        );
    }
}
