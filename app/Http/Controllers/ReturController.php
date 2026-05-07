<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Makanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturController extends Controller
{
    // 1. Menampilkan Halaman Riwayat Retur
    public function index()
    {
        $retur = Retur::with(['penjualan', 'makanan', 'pengguna'])->latest()->get();
        return view('retur.index', compact('retur'));
    }

    // 2. Menampilkan Halaman Form Input Retur
    public function create(Request $request)
    {
        // Tangkap ID jika user mengakses dari halaman laporan penjualan
        $selected_id = $request->query('id_penjualan');

        // Ambil data transaksi penjualan 30 hari terakhir agar tidak terlalu berat
        $penjualan = Penjualan::with('detail.makanan')
            ->where('tanggal_penjualan', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('retur.create', compact('penjualan', 'selected_id'));
    }

    // 3. Memproses Data Retur
    public function store(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|exists:penjualans,id_penjualan',
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_retur' => 'required|integer|min:1',
            'alasan' => 'required|string',
            'tgl_retur' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::findOrFail($request->id_penjualan);
            $makanan = Makanan::findOrFail($request->id_makanan);

            // Cari harga satuan saat barang itu dijual dulu
            $detailPenjualan = DetailPenjualan::where('id_penjualan', $request->id_penjualan)
                                              ->where('id_makanan', $request->id_makanan)
                                              ->first();

            if (!$detailPenjualan) {
                return back()->with('error', 'Barang ini tidak ditemukan dalam kode transaksi tersebut!');
            }

            if ($request->jumlah_retur > $detailPenjualan->jumlah) {
                return back()->with('error', 'Jumlah retur melebihi jumlah barang yang dibeli!');
            }

            // Hitung nominal uang yang harus dipotong dari laporan penjualan
            $nominal_pengembalian = $request->jumlah_retur * $detailPenjualan->harga_satuan;

            // A. Simpan data Retur ke tabel
            Retur::create([
                'id_penjualan' => $request->id_penjualan,
                'id_makanan' => $request->id_makanan,
                'id_pengguna' => Auth::id() ?? 1, // id kasir
                'jumlah_retur' => $request->jumlah_retur,
                'nominal_pengembalian' => $nominal_pengembalian,
                'alasan' => $request->alasan,
                'tgl_retur' => $request->tgl_retur,
            ]);

            // B. Kembalikan stok fisik ke ETALASE
            $makanan->stok_etalase += $request->jumlah_retur;
            $makanan->save();

            // C. Potong Total Bayar (Pendapatan) di tabel Penjualan
            $penjualan->total_harga -= $nominal_pengembalian;
            $penjualan->save();

            // D. Kurangi jumlah di detail penjualan
            $detailPenjualan->jumlah -= $request->jumlah_retur;
            $detailPenjualan->save();

            DB::commit();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil! Stok etalase bertambah dan total penjualan telah dipotong otomatis.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
