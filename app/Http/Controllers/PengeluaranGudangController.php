<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\PengeluaranGudang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranGudangController extends Controller
{
    public function index()
    {
        $data = PengeluaranGudang::with(['makanan', 'pengguna'])->latest('tgl_pengeluaran')->get();
        return view('pengeluaran_gudang.index', compact('data'));
    }

    public function create()
    {
        // Hanya tampilkan makanan yang memiliki stok gudang > 0
        $makanan = Makanan::where('stok_gudang', '>', 0)->orderBy('nama_makanan')->get();
        return view('pengeluaran_gudang.create', compact('makanan'));
    }

    public function store(Request $request)
    {
        // 1. Cari data barang terlebih dahulu
        $makanan = Makanan::findOrFail($request->id_makanan);

        // 2. Hitung dan validasi stok gudang[cite: 2]
        $stok_gudang_sebelum = $makanan->stok_gudang;

        if ($stok_gudang_sebelum < $request->jumlah_keluar) {
            return redirect()->back()->with('error', 'Stok gudang tidak mencukupi untuk pengeluaran ini. Sisa stok gudang: ' . $stok_gudang_sebelum);
        }

        $stok_gudang_sesudah = $stok_gudang_sebelum - $request->jumlah_keluar;

        // Total stok juga berkurang (karena barang keluar dari gudang)
        $stok_sebelum = $makanan->stok;
        $stok_sesudah = $stok_sebelum - $request->jumlah_keluar;

        // 3. Simpan data dan potong stok dalam satu Transaksi Database yang aman
        DB::beginTransaction();
        try {
            // Mencatat riwayat pengeluaran gudang
            PengeluaranGudang::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => Auth::id(),
                'jumlah_keluar' => $request->jumlah_keluar,
                'stok_gudang_sebelum' => $stok_gudang_sebelum,
                'stok_gudang_sesudah' => $stok_gudang_sesudah,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                'tgl_pengeluaran' => $request->tgl_pengeluaran ?? date('Y-m-d'), // Pastikan tanggal terisi[cite: 2]
                'barcode' => $request->barcode,
            ]);

            // Update stok di tabel makanan - HANYA gudang dan total yang berkurang
            $makanan->update([
                'stok' => $stok_sesudah,                 // Total berkurang
                'stok_gudang' => $stok_gudang_sesudah,   // Gudang berkurang
                // stok_etalase tidak perlu di-update karena tidak berubah
            ]);

            DB::commit();

            return redirect()->route('pengeluaran_gudang.index')->with('success', 'Pengeluaran gudang berhasil dicatat! Stok gudang berkurang dari ' . $stok_gudang_sebelum . ' menjadi ' . $stok_gudang_sesudah . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}