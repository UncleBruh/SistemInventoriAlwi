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
        // Pastikan hanya Pemilik yang bisa mengakses
        if (Auth::user()->role !== 'Pemilik') {
            abort(403, 'Hanya pemilik yang dapat mengakses fitur ini.');
        }

        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_perubahan' => 'required|integer|min:1',
            'tipe_keluar' => 'required|in:rusak,hilang,expired,keperluan_prive',
            'tgl_mutasi' => 'required|date',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $userId = Auth::id();
        $jumlah = $request->jumlah_perubahan;

        // PERBAIKAN BUG 2: Cegah double submission dengan mengecek duplikasi
        // Cek apakah ada mutasi yang sama dalam 10 detik terakhir
        $recentDuplicate = MutasiKeluar::where('id_makanan', $makanan->id_makanan)
            ->where('id_pengguna', $userId)
            ->where('jumlah_keluar', $jumlah)
            ->where('tipe_keluar', $request->tipe_keluar)
            ->where('tgl_mutasi', $request->tgl_mutasi)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($recentDuplicate) {
            return redirect()->back()->with('error',
                'Submission duplikat terdeteksi! Data yang sama baru saja diinput dalam beberapa detik terakhir. ' .
                'Silakan tunggu sebentar dan coba lagi jika diperlukan.');
        }

        // PENTING: Barang keluar HANYA dari stok ETALASE
        $stok_etalase_sebelum = $makanan->stok_etalase;
        $stok_gudang_sebelum = $makanan->stok_gudang;

        if ($stok_etalase_sebelum < $request->jumlah_perubahan) {
            return redirect()->back()->with('error', 'Stok etalase tidak mencukupi untuk pengeluaran ini. Stok etalase: ' . $stok_etalase_sebelum);
        }

        $stok_etalase_sesudah = $stok_etalase_sebelum - $request->jumlah_perubahan;
        $stok_gudang_sesudah = $stok_gudang_sebelum; // Gudang TIDAK BERUBAH

        // Total stok juga berkurang (karena barang keluar dari etalase)
        $stok_sebelum = $makanan->stok;
        $stok_sesudah = $stok_sebelum - $request->jumlah_perubahan;

        DB::beginTransaction();
        try {
            MutasiKeluar::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => $userId,
                'jumlah_keluar' => $request->jumlah_perubahan,
                'stok_sebelum' => $stok_sebelum,
                'stok_sesudah' => $stok_sesudah,
                'alasan' => $request->tipe_keluar,
                'tgl_mutasi' => $request->tgl_mutasi,
                'tipe_keluar' => $request->tipe_keluar,
                'stok_etalase_sebelum' => $stok_etalase_sebelum,
                'stok_etalase_sesudah' => $stok_etalase_sesudah,
            ]);

            // Update stok di tabel makanan - HANYA etalase yang berkurang
            $makanan->update([
                'stok' => $stok_sesudah,  // Total berkurang
                'stok_etalase' => $stok_etalase_sesudah,  // Etalase berkurang
                'stok_gudang' => $stok_gudang_sesudah,  // Gudang TIDAK BERUBAH
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pengelolaan stok etalase berhasil dicatat! Stok etalase berkurang dari ' . $stok_etalase_sebelum . ' menjadi ' . $stok_etalase_sesudah . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
