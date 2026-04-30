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
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_keluar' => 'required|integer|min:1',
            'alasan' => 'required|in:expired,tikus,rusak,lainnya',
            'keterangan' => 'nullable|string|max:500',
            'tgl_pengeluaran' => 'required|date',
            'barcode' => 'nullable|string',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);

        // Validasi stok gudang
        $stok_gudang_sebelum = $makanan->stok_gudang;

        if ($stok_gudang_sebelum < $request->jumlah_keluar) {
            return redirect()->back()->with('error', 'Stok gudang tidak mencukupi untuk pengeluaran ini. Sisa stok gudang: ' . $stok_gudang_sebelum);
        }

        $stok_gudang_sesudah = $stok_gudang_sebelum - $request->jumlah_keluar;

        // Total stok juga berkurang (karena barang keluar dari gudang)
        $stok_sebelum = $makanan->stok;
        $stok_sesudah = $stok_sebelum - $request->jumlah_keluar;

        DB::beginTransaction();
        try {
            PengeluaranGudang::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => Auth::id(),
                'jumlah_keluar' => $request->jumlah_keluar,
                'stok_gudang_sebelum' => $stok_gudang_sebelum,
                'stok_gudang_sesudah' => $stok_gudang_sesudah,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                'tgl_pengeluaran' => $request->tgl_pengeluaran,
                'barcode' => $request->barcode,
            ]);

            // Update stok di tabel makanan - HANYA gudang yang berkurang
            $makanan->update([
                'stok' => $stok_sesudah,           // Total berkurang
                'stok_gudang' => $stok_gudang_sesudah,  // Gudang berkurang
                'stok_etalase' => $makanan->stok_etalase,  // Etalase tetap
            ]);

            DB::commit();

            return redirect()->route('pengeluaran_gudang.index')->with('success', 'Pengeluaran gudang berhasil dicatat! Stok gudang berkurang dari ' . $stok_gudang_sebelum . ' menjadi ' . $stok_gudang_sesudah . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
