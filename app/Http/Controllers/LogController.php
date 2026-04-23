<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * Menampilkan riwayat mutasi (Hanya untuk Pemilik).
     */
    public function index()
    {
        $masuk = MutasiMasuk::with(['makanan', 'pengguna'])->latest()->get();
        $keluar = MutasiKeluar::with(['makanan', 'pengguna'])->latest()->get();

        return view('log.index', compact('masuk', 'keluar'));
    }

    /**
     * Menampilkan form input barang masuk atau keluar.
     */
    public function create()
    {
        $makanan = Makanan::all();
        return view('log.create', compact('makanan'));
    }

    /**
     * Memproses penyimpanan mutasi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jenis_aktivitas' => 'required|in:Barang Masuk,Barang Keluar',
            'jumlah_perubahan' => 'required|integer|min:1',
            'alasan' => 'required_if:jenis_aktivitas,Barang Keluar', // Alasan wajib jika barang keluar
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $stok_sebelum = $makanan->stok;

        // Gunakan Database Transaction agar data aman jika terjadi error di tengah jalan
        DB::beginTransaction();

        try {
            if ($request->jenis_aktivitas === 'Barang Masuk') {
                // Logika Barang Masuk (Bisa dilakukan Admin/Pemilik)
                $stok_sesudah = $stok_sebelum + $request->jumlah_perubahan;
                
                MutasiMasuk::create([
                    'id_makanan' => $makanan->id_makanan,
                    'id_pengguna' => Auth::id(),
                    'jumlah_masuk' => $request->jumlah_perubahan,
                    'stok_sebelum' => $stok_sebelum,
                    'stok_sesudah' => $stok_sesudah,
                    'tgl_mutasi' => now(),
                ]);
            } else {
                // Logika Barang Keluar (HANYA Pemilik)
                if (Auth::user()->role !== 'Pemilik') {
                    return redirect()->back()->with('error', 'Akses ditolak. Hanya Pemilik yang bisa mengeluarkan barang.');
                }

                if ($stok_sebelum < $request->jumlah_perubahan) {
                    return redirect()->back()->with('error', 'Stok tidak mencukupi untuk pengeluaran ini.');
                }

                $stok_sesudah = $stok_sebelum - $request->jumlah_perubahan;

                MutasiKeluar::create([
                    'id_makanan' => $makanan->id_makanan,
                    'id_pengguna' => Auth::id(),
                    'jumlah_keluar' => $request->jumlah_perubahan,
                    'stok_sebelum' => $stok_sebelum,
                    'stok_sesudah' => $stok_sesudah,
                    'alasan' => $request->alasan,
                    'tgl_mutasi' => now(),
                ]);
            }

            // Update stok di tabel makanan
            $makanan->update(['stok' => $stok_sesudah]);

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Data mutasi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}