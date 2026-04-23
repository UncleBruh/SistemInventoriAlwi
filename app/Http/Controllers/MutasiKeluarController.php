<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiKeluarController extends Controller
{
    // Menampilkan riwayat barang keluar
    public function index()
    {
        $data = MutasiKeluar::with(['makanan', 'pengguna'])->latest()->get();
        return view('mutasi_keluar.index', compact('data'));
    }

    public function create()
    {
        $makanan = Makanan::all();
        // Mengarah ke folder mutasi_keluar
        return view('mutasi_keluar.create', compact('makanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_perubahan' => 'required|integer|min:1',
            'alasan' => 'required|string|max:255',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $stok_sebelum = $makanan->stok;

        if ($stok_sebelum < $request->jumlah_perubahan) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        $stok_sesudah = $stok_sebelum - $request->jumlah_perubahan;

        DB::beginTransaction();
        try {
            MutasiKeluar::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => Auth::id(),
                'jumlah_keluar' => $request->jumlah_perubahan,
                'stok_sebelum' => $stok_sebelum,
                'stok_sesudah' => $stok_sesudah,
                'alasan' => $request->alasan,
                'tgl_mutasi' => now(),
            ]);

            $makanan->update(['stok' => $stok_sesudah]);

            DB::commit();
            return redirect()->route('mutasi_keluar.index')->with('success', 'Barang Keluar berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data.');
        }
    }
}