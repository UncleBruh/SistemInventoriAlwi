<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\AlokasiGudangEtalase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlokasiGudangEtalaseController extends Controller
{
    /**
     * Tampilkan daftar semua alokasi gudang ke etalase
     */
    public function index()
    {
        $data = AlokasiGudangEtalase::with(['makanan', 'pengguna'])
            ->latest('tgl_alokasi')
            ->get();
        return view('alokasi_gudang_etalase.index', compact('data'));
    }

    /**
     * Tampilkan form untuk membuat alokasi baru
     */
    public function create()
    {
        $makanan = Makanan::where('stok_gudang', '>', 0)->get(); // Hanya tampilkan yang punya stok gudang
        return view('alokasi_gudang_etalase.create', compact('makanan'));
    }

    /**
     * Simpan alokasi gudang ke etalase
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_dialokasi' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $jumlah = $request->jumlah_dialokasi;

        // Validasi stok gudang
        if ($makanan->stok_gudang < $jumlah) {
            return redirect()->back()->with('error',
                'Stok gudang tidak mencukupi. Stok gudang saat ini: ' . $makanan->stok_gudang);
        }

        // Hitung stok sebelum dan sesudah
        $stok_gudang_sebelum = $makanan->stok_gudang;
        $stok_gudang_sesudah = $stok_gudang_sebelum - $jumlah;

        $stok_etalase_sebelum = $makanan->stok_etalase;
        $stok_etalase_sesudah = $stok_etalase_sebelum + $jumlah;

        DB::beginTransaction();
        try {
            // Catat alokasi
            AlokasiGudangEtalase::create([
                'id_makanan' => $makanan->id_makanan,
                'jumlah_dialokasi' => $jumlah,
                'stok_gudang_sebelum' => $stok_gudang_sebelum,
                'stok_gudang_sesudah' => $stok_gudang_sesudah,
                'stok_etalase_sebelum' => $stok_etalase_sebelum,
                'stok_etalase_sesudah' => $stok_etalase_sesudah,
                'id_pengguna' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            // Update stok makanan
            $makanan->update([
                'stok_gudang' => $stok_gudang_sesudah,
                'stok_etalase' => $stok_etalase_sesudah,
            ]);

            DB::commit();

            return redirect()->route('alokasi-gudang-etalase.index')
                ->with('success', 'Alokasi barang berhasil disimpan. ' . $jumlah . ' barang telah dipindahkan dari gudang ke etalase.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan alokasi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail alokasi
     */
    public function show($id)
    {
        $alokasi = AlokasiGudangEtalase::with(['makanan', 'pengguna'])->findOrFail($id);
        return view('alokasi_gudang_etalase.show', compact('alokasi'));
    }
}
