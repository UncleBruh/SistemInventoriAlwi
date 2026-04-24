<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiMasuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiMasukController extends Controller
{
    public function index()
    {
        $data = MutasiMasuk::with(['makanan', 'pengguna'])->latest()->get();
        return view('mutasi_masuk.index', compact('data'));
    }

    public function create()
    {
        $makanan = Makanan::all();
        return view('mutasi_masuk.create', compact('makanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_perubahan' => 'required|integer|min:1',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $stok_sebelum = $makanan->stok;
        $stok_sesudah = $stok_sebelum + $request->jumlah_perubahan;

        DB::beginTransaction();
        try {
            MutasiMasuk::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => Auth::id(),
                'jumlah_masuk' => $request->jumlah_perubahan,
                'stok_sebelum' => $stok_sebelum,
                'stok_sesudah' => $stok_sesudah,
                'tgl_mutasi' => now(),
            ]);

            $makanan->update(['stok' => $stok_sesudah]);
            DB::commit();
            
            return redirect()->back()->with('success', 'Barang Masuk berhasil dicatat. Silakan input barang selanjutnya jika ada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}