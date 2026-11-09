<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangTu;
use Illuminate\Validation\Rule;
use App\Models\Peminjaman;

class BarangTuController extends Controller
{
    
public function index(Request $request)
{
    $query = BarangTu::query();

    if ($request->filled('search')) {
        $keyword = $request->search;

        $query->where(function ($q) use ($keyword) {
            $q->where('kode_barang', 'like', "%{$keyword}%")
              ->orWhere('nama_barang', 'like', "%{$keyword}%")
              ->orWhere('spesifikasi', 'like', "%{$keyword}%");
        });
    }

    $barang = $query->latest()->paginate(10)->withQueryString();

    $totalBarang = BarangTu::count();

    $barangTersedia = BarangTu::where(
        'status', 'tersedia'
    )->count();

    $barangDipinjam = BarangTu::where(
        'status', 'dipinjam'
    )->count();

    $barangTidakTersedia = BarangTu::where(
        'status', 'tidak_tersedia'
    )->count();
    
    $menungguVerifikasi = Peminjaman::where(
        'status',
        'menunggu'
    )->count();

    return view('barang-tu.index', compact(
        'barang',
        'totalBarang',
        'barangTersedia',
        'barangDipinjam',
        'barangTidakTersedia',
        'menungguVerifikasi'
    ));
}

    public function create()
    {
        return view('barang-tu.create');
    }
    


    
public function store(Request $request)
{
    $data = $request->validate([
        'kode_barang' => [
            'required',
            'string',
            'max:255',
        ],
        'jenis_barang' => [
            'required',
            Rule::in(['paket_proyektor', 'laptop', 'speaker']),
        ],
        'nama_barang' => [
            'required',
            'string',
            'max:255',
        ],
        'spesifikasi' => [
            'nullable',
            'string',
        ],
        'kondisi' => [
            'required',
            Rule::in(['baik', 'rusak_ringan', 'rusak_berat']),
        ],
        'status' => [
            'required',
            Rule::in(['tersedia', 'dipinjam', 'tidak_tersedia']),
        ],
    ]);

    $duplikat = BarangTu::where('kode_barang', $data['kode_barang'])
        ->where('nama_barang', $data['nama_barang'])
        ->where('jenis_barang', $data['jenis_barang'])
        ->exists();

    if ($duplikat) {
        return back()
            ->withInput()
            ->withErrors([
                'kode_barang' => 'Barang dengan kode, nama, dan jenis yang sama sudah terdaftar.',
            ]);
    }

    BarangTu::create($data);

    return redirect()
        ->route('barang-tu.index')
        ->with('success', 'Barang berhasil ditambahkan.');
}

    
public function update(Request $request, BarangTu $barangTu)
{
    $data = $request->validate([
        'kode_barang' => [
            'required',
            'string',
            'max:255',
        ],
        'jenis_barang' => [
            'required',
            Rule::in(['paket_proyektor', 'laptop', 'speaker']),
        ],
        'nama_barang' => [
            'required',
            'string',
            'max:255',
        ],
        'spesifikasi' => [
            'nullable',
            'string',
        ],
        'kondisi' => [
            'required',
            Rule::in(['baik', 'rusak_ringan', 'rusak_berat']),
        ],
        'status' => [
            'required',
            Rule::in(['tersedia', 'dipinjam', 'tidak_tersedia']),
        ],
    ]);

    $duplikat = BarangTu::where('kode_barang', $data['kode_barang'])
        ->where('nama_barang', $data['nama_barang'])
        ->where('jenis_barang', $data['jenis_barang'])
        ->where('id', '!=', $barangTu->id)
        ->exists();

    if ($duplikat) {
        return back()
            ->withInput()
            ->withErrors([
                'kode_barang' => 'Barang dengan kode, nama, dan jenis yang sama sudah terdaftar.',
            ]);
    }
    $kerusakanAktif = $barangTu->inventarisKerusakan()
    ->whereIn('status_penanganan', [
        'belum_diperbaiki',
        'sedang_diperbaiki',
    ])
    ->exists();

if ($kerusakanAktif) {
    $data['kondisi'] = $barangTu->kondisi;
    $data['status'] = 'tidak_tersedia';
}

    $barangTu->update($data);

    return redirect()
        ->route('barang-tu.index')
        ->with('success', 'Barang berhasil diperbarui.');
}

    public function destroy(BarangTu $barangTu)
    {
        if ($barangTu->peminjamanDetail()->exists()){
            return back()->with(
                'error',
                'Barang memiliki riwayat peminjaman dan tidak dapat dihapus.'
            );
        }

        $barangTu->delete();
        return redirect()
        ->route('barang-tu.index')
        ->with('success', 'Barang berhasil dihapus.');
    }

}
