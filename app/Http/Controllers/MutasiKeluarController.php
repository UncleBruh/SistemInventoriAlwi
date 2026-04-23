<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiKeluarController extends Controller
{
    public function create()
    {
        $makanan = Makanan::all();
        // Langsung kirimkan type 'keluar' ke view
        return view('log.create', compact('makanan'))->with('type', 'keluar');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_perubahan' => 'required|integer|min:1',
            'alasan' => 'required|string|max:255', // Alasan wajib diisi
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $stok_sebelum = $makanan->stok;

        if ($stok_sebelum < $request->jumlah_perubahan) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk pengeluaran ini.');
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
            return redirect()->route('dashboard')->with('success', 'Barang Keluar berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}