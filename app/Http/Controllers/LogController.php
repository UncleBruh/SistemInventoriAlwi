<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Makanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk Database Transaction

class LogController extends Controller
{
    // 1. Menampilkan Halaman Riwayat Log (Khusus Pemilik)
    public function index(Request $request)
    {
        // Mengambil data log beserta relasinya (makanan dan pengguna)
        $query = LogAktivitas::with(['makanan', 'pengguna'])->latest('tgl_aktivitas');

        // Fitur Filter: Jenis Aktivitas
        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', $request->jenis_aktivitas);
        }

        // Fitur Filter: Rentang Tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tgl_aktivitas', [
                $request->tanggal_awal . ' 00:00:00', 
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        // Fitur Search: Cari berdasarkan nama makanan atau barcode
        if ($request->filled('search')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', $request->search);
            });
        }

        // Gunakan pagination agar halaman tidak berat jika data sudah ribuan
        $logs = $query->paginate(20)->withQueryString();

        return view('log.index', compact('logs'));
    }

    // 2. Menampilkan Form Transaksi Masuk/Keluar (Admin & Pemilik)
    public function create()
    {
        // Ambil data makanan untuk ditampilkan di dropdown/pilihan
        $makanan = Makanan::orderBy('nama_makanan', 'asc')->get();
        return view('log.create', compact('makanan'));
    }

    // 3. Memproses Transaksi (Inti Logika)
    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jenis_aktivitas' => 'required|in:Barang Masuk,Barang Keluar',
            'jumlah_perubahan' => 'required|integer|min:1',
        ]);

        try {
            // Gunakan DB::transaction untuk keamanan data
            DB::transaction(function () use ($request) {
                // 1. Kunci baris makanan ini sementara (pessimistic locking) agar tidak ada tabrakan data jika ada 2 admin input bersamaan
                $makanan = Makanan::where('id_makanan', $request->id_makanan)->lockForUpdate()->first();
                
                $stok_sebelum = $makanan->stok;
                
                // 2. Hitung stok sesudah
                if ($request->jenis_aktivitas == 'Barang Masuk') {
                    $stok_sesudah = $stok_sebelum + $request->jumlah_perubahan;
                } else {
                    $stok_sesudah = $stok_sebelum - $request->jumlah_perubahan;
                }

                // 3. Validasi: Stok tidak boleh minus jika barang keluar
                if ($stok_sesudah < 0) {
                    throw new \Exception('Gagal! Stok ' . $makanan->nama_makanan . ' saat ini hanya ' . $stok_sebelum . ', tidak cukup untuk dikeluarkan.');
                }

                // 4. Update tabel makanan
                $makanan->update(['stok' => $stok_sesudah]);

                // 5. Catat ke tabel log_aktivitas
                LogAktivitas::create([
                    'id_makanan' => $makanan->id_makanan,
                    'id_pengguna' => Auth::id(), // ID akun yang sedang login
                    'jenis_aktivitas' => $request->jenis_aktivitas,
                    'jumlah_perubahan' => $request->jumlah_perubahan,
                    'stok_sebelum' => $stok_sebelum,
                    'stok_sesudah' => $stok_sesudah,
                    'tgl_aktivitas' => now(), // Tanggal dan waktu saat ini
                ]);
            });

            return redirect()->back()->with('success', 'Transaksi ' . $request->jenis_aktivitas . ' berhasil dicatat!');

        } catch (\Exception $e) {
            // Jika ada error (misal stok minus), kembalikan pesan error
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}