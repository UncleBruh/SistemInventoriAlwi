<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiKeluarController extends Controller
{
    public function index()
    {
        $data = MutasiKeluar::with(['makanan', 'pengguna'])->latest()->get();
        return view('mutasi_keluar.index', compact('data'));
    }

    public function create()
    {
        $makanan = Makanan::all();
        return view('mutasi_keluar.create', compact('makanan'));
    }

    public function store(Request $request)
    {
        // -------------------------------------------------------------
        // PENENTUAN ALASAN BERDASARKAN ROLE (PENJUALAN ATAU KETIK SENDIRI)
        // -------------------------------------------------------------
        if (Auth::user()->role === 'Admin') {
            // Admin dipaksa ke opsi Penjualan
            $request->merge(['alasan' => 'Penjualan']);
        } else {
            // Pemilik: Cek apakah memilih "Lainnya"
            if ($request->alasan_pilihan === 'Lainnya') {
                // Ambil teks dari kolom ketikan
                $request->merge(['alasan' => $request->alasan_lain]);
            } else {
                // Ambil teks dari dropdown bawaan
                $request->merge(['alasan' => $request->alasan_pilihan]);
            }
        }

        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_perubahan' => 'required|integer|min:1',
            'alasan' => 'required|string|max:255',
            'tgl_mutasi' => 'required|date', 
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
                'tgl_mutasi' => $request->tgl_mutasi,
            ]);

            $makanan->update(['stok' => $stok_sesudah]);
            DB::commit();

            return redirect()->back()->with('success', 'Barang Keluar berhasil dicatat. Silakan input barang selanjutnya jika ada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}