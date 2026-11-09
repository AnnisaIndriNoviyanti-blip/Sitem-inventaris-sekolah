<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
            'nama_kelas' => 'X',
            'jurusan' => '-',
            'angkatan' => 10,
            'aktif' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nama_kelas' => 'XI',
            'jurusan' => '-',
            'angkatan' => 11,
            'aktif' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nama_kelas' => 'XII',
            'jurusan' => '-',
            'angkatan' => 12,
            'aktif' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];

    foreach ($data as $kelas) {
        DB::table('kelas')->updateOrInsert(
            ['nama_kelas' => $kelas['nama_kelas']],
            $kelas
        );
    }
    
    }
}
