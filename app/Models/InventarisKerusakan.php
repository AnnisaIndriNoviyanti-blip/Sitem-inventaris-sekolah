<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarisKerusakan extends Model
{
    protected $table = 'inventaris_kerusakan';

    protected $fillable = [
    'barang_tu_id',
    'created_by',
    'tanggal_kerusakan',
    'tingkat_kerusakan',
    'catatan',
    'foto_bukti',
    'foto_perbaikan',
    'tanggal_perbaikan',
    'status_penanganan',
    'tindakan_perbaikan',
    'hasil_perbaikan',
];
    protected function casts(): array
{
    return [
        'tanggal_kerusakan' => 'date',
        'tanggal_perbaikan' => 'date',
    ];
}

    public function barang()
{
    return $this->belongsTo(
        \App\Models\BarangTu::class,
        'barang_tu_id'
    );
}

public function admin()
{
    return $this->belongsTo(
        \App\Models\User::class,
        'created_by'
    );
}
    
}