<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiMasuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// Tambahkan ini agar fungsi recordLog bisa dipanggil
use App\Http\Controllers\LogController;

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
            'lokasi_tujuan' => 'required|in:gudang,etalase', // Validasi lokasi tujuan
            'tgl_mutasi' => 'required|date',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $lokasi = $request->lokasi_tujuan;
        $jumlah = $request->jumlah_perubahan;
        $userId = Auth::id();

        // PERBAIKAN BUG 2: Cegah double submission dengan mengecek duplikasi
        // Cek apakah ada mutasi yang sama dalam 10 detik terakhir
        $recentDuplicate = MutasiMasuk::where('id_makanan', $makanan->id_makanan)
            ->where('id_pengguna', $userId)
            ->where('jumlah_masuk', $jumlah)
            ->where('lokasi_tujuan', $lokasi)
            ->where('tgl_mutasi', $request->tgl_mutasi)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($recentDuplicate) {
            return redirect()->back()->with('error',
                'Submission duplikat terdeteksi! Data yang sama baru saja diinput dalam beberapa detik terakhir. ' .
                'Silakan tunggu sebentar dan coba lagi jika diperlukan.');
        }

        // Simpan stok sebelum
        $stok_gudang_sebelum = $makanan->stok_gudang;
        $stok_etalase_sebelum = $makanan->stok_etalase;
        $stok_sebelum = $makanan->stok;

        // Tentukan stok sesudah berdasarkan lokasi tujuan
        if ($lokasi === 'gudang') {
            $stok_gudang_sesudah = $stok_gudang_sebelum + $jumlah;
            $stok_etalase_sesudah = $stok_etalase_sebelum;
        } else { // etalase
            $stok_gudang_sesudah = $stok_gudang_sebelum;
            $stok_etalase_sesudah = $stok_etalase_sebelum + $jumlah;
        }

        $stok_sesudah = $stok_sebelum + $jumlah;

        DB::beginTransaction();
        try {
            MutasiMasuk::create([
                'id_makanan' => $makanan->id_makanan,
                'id_pengguna' => $userId,
                'jumlah_masuk' => $jumlah,
                'stok_sebelum' => $stok_sebelum,
                'stok_sesudah' => $stok_sesudah,
                'tgl_mutasi' => $request->tgl_mutasi,
                'lokasi_tujuan' => $lokasi,
                'stok_gudang_sebelum' => $stok_gudang_sebelum,
                'stok_gudang_sesudah' => $stok_gudang_sesudah,
                'stok_etalase_sebelum' => $stok_etalase_sebelum,
                'stok_etalase_sesudah' => $stok_etalase_sesudah,
            ]);

            // Update stok di tabel makanan
            $makanan->update([
                'stok' => $stok_sesudah,
                'stok_gudang' => $stok_gudang_sesudah,
                'stok_etalase' => $stok_etalase_sesudah,
            ]);

            // --- TAMBAHAN BARU: LOG AKTIVITAS ---
            // Mencatat tindakan ini ke dalam sistem Log
            LogController::recordLog(
                'Mutasi Masuk',
                "Menambahkan {$jumlah} pcs {$makanan->nama_makanan} ke {$lokasi}"
            );
            // ------------------------------------

            DB::commit();

            return redirect()->back()->with('success', 'Barang Masuk berhasil dicatat ke ' . ucfirst($lokasi) . '. Silakan input barang selanjutnya jika ada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
