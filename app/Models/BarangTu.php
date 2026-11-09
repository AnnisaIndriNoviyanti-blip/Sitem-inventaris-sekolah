<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangTu extends Model
{
    protected $table = 'barang_tu';

    protected $fillable = [
        'kode_barang',
        'jenis_barang',
        'nama_barang',
        'spesifikasi',
        'kondisi',
        'status',
    ];

    public function peminjamanDetail(): HasMany
    {
        return $this->hasMany(
            PeminjamanDetail::class,
            'barang_tu_id'
        );
    }
    
    public function inventarisKerusakan(): HasMany
{
    return $this->hasMany(
        InventarisKerusakan::class,
        'barang_tu_id'
    );
}
}
