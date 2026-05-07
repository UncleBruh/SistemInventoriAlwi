<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{
    // =========================================================================
    // UPDATE: Menampilkan riwayat transaksi dengan Filter Tanggal
    // =========================================================================
    public function index(Request $request)
    {
        $query = Penjualan::with(['pengguna', 'detail.makanan']);

        // Menangkap input rentang tanggal dari form filter
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        // Jika user memilih tanggal, lakukan filter
        if ($tgl_awal && $tgl_akhir) {
            $query->whereBetween('tanggal_penjualan', [$tgl_awal . ' 00:00:00', $tgl_akhir . ' 23:59:59']);
        }

        // Eksekusi query: urutkan dari yang paling baru
        $penjualan = $query->latest('tanggal_penjualan')->get();
        
        // Hitung total pendapatan dari hasil filter
        $total_pendapatan = $penjualan->sum('total_harga');

        return view('penjualan.index', compact('penjualan', 'tgl_awal', 'tgl_akhir', 'total_pendapatan'));
    }

    // =========================================================================
    // BARU: Fungsi untuk mencetak Laporan PDF berdasarkan rentang tanggal
    // =========================================================================
    public function cetakPdf(Request $request)
    {
        $query = Penjualan::with(['pengguna', 'detail.makanan']);

        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        if ($tgl_awal && $tgl_akhir) {
            $query->whereBetween('tanggal_penjualan', [$tgl_awal . ' 00:00:00', $tgl_akhir . ' 23:59:59']);
        }

        // Urutkan dari yang terlama ke terbaru agar rapi saat dicetak
        $penjualan = $query->orderBy('tanggal_penjualan', 'asc')->get();
        $total_pendapatan = $penjualan->sum('total_harga');

        return view('laporan.penjualan_pdf', compact('penjualan', 'tgl_awal', 'tgl_akhir', 'total_pendapatan'));
    }

    // Menampilkan halaman Aplikasi Kasir (POS)
    public function create()
    {
        // Hanya tampilkan makanan yang stok etalasenya > 0
        $makanan = Makanan::where('stok_etalase', '>', 0)->orderBy('nama_makanan')->get();

        // Ambil data keranjang dari session sementara
        $keranjang = session()->get('keranjang', []);
        $total_harga = array_sum(array_column($keranjang, 'subtotal'));

        return view('penjualan.create', compact('makanan', 'keranjang', 'total_harga'));
    }

    // =========================================================================
    // Fungsi menambahkan barang ke keranjang session
    // Mendukung input Barcode Scanner ATAU Pencarian Manual dari Select Option
    // =========================================================================
    public function tambahKeranjang(Request $request)
    {
        // Validasi bisa menerima barcode ATAU id_makanan
        $request->validate([
            'id_makanan' => 'nullable|exists:makanan,id_makanan',
            'barcode' => 'nullable|string',
            'jumlah' => 'required|integer|min:1'
        ]);

        // 1. Cek apakah input dari Barcode atau Manual
        if ($request->filled('barcode')) {
            // Mencari barang berdasarkan kolom barcode
            $makanan = Makanan::where('barcode', $request->barcode)->first();
            if (!$makanan) {
                return redirect()->back()->with('error', 'Barcode tidak ditemukan di database!');
            }
        } elseif ($request->filled('id_makanan')) {
            $makanan = Makanan::findOrFail($request->id_makanan);
        } else {
            return redirect()->back()->with('error', 'Pilih barang atau scan barcode terlebih dahulu!');
        }

        // 2. Validasi stok etalase
        if ($request->jumlah > $makanan->stok_etalase) {
            return redirect()->back()->with('error', 'Stok etalase tidak cukup! Sisa stok: ' . $makanan->stok_etalase);
        }

        $keranjang = session()->get('keranjang', []);

        if(isset($keranjang[$makanan->id_makanan])) {
            $jumlah_baru = $keranjang[$makanan->id_makanan]['jumlah'] + $request->jumlah;

            if ($jumlah_baru > $makanan->stok_etalase) {
                return redirect()->back()->with('error', 'Stok etalase tidak cukup untuk penambahan ini!');
            }

            $keranjang[$makanan->id_makanan]['jumlah'] = $jumlah_baru;
            $keranjang[$makanan->id_makanan]['subtotal'] = $jumlah_baru * $makanan->harga;
        } else {
            $keranjang[$makanan->id_makanan] = [
                'nama_makanan' => $makanan->nama_makanan,
                'harga_satuan' => $makanan->harga,
                'jumlah' => $request->jumlah,
                'subtotal' => $makanan->harga * $request->jumlah
            ];
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', 'Barang dimasukkan ke keranjang!');
    }

    // Fungsi menghapus 1 jenis barang dari keranjang
    public function hapusKeranjang($id)
    {
        $keranjang = session()->get('keranjang');
        if(isset($keranjang[$id])) {
            unset($keranjang[$id]);
            session()->put('keranjang', $keranjang);
        }
        return redirect()->back()->with('success', 'Barang dihapus dari keranjang.');
    }

    // Fungsi proses Pembayaran (Checkout)
    public function store(Request $request)
    {
        $keranjang = session()->get('keranjang');

        if(!$keranjang || count($keranjang) == 0){
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong!');
        }

        $total_harga = array_sum(array_column($keranjang, 'subtotal'));
        $bayar = $total_harga;  // Tanpa input uang diterima, bayar sama dengan total
        $kembalian = 0;  // Kembalian = 0 karena tidak ada input uang
        // Membuat nomor nota unik (Contoh: INV-X7B9A-167812)
        $no_nota = 'INV-' . strtoupper(Str::random(5)) . '-' . time();

        // Membuat kode transaksi unik untuk laporan penjualan (Contoh: TRX-20260430-001)
        $kode_transaksi = 'TRX-' . now()->format('Ymd') . '-' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // Ambil salah satu ID Makanan dari keranjang sebagai data "dummy" untuk kolom lama yang pensiun
            $id_makanan_dummy = array_key_first($keranjang);

            // 1. Simpan ke tabel induk (penjualans)
// app/Http/Controllers/PenjualanController.php (Di dalam method store)

            // 1. Simpan ke tabel induk (penjualans)
            $penjualan = Penjualan::create([
                'id_pengguna' => Auth::id(),
                'total_harga' => $total_harga,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'no_nota' => $no_nota,
                'tanggal_penjualan' => now(), 
                'kode_transaksi' => $kode_transaksi, // <--- TAMBAHKAN BARIS INI
                
                // Kolom pensiun kita isi data dummy 0 agar database senang
                'id_makanan' => $id_makanan_dummy, 
                'jumlah_terjual' => 0,        
                'harga_per_unit' => 0         
            ]);

            $item_terjual = []; // Array untuk dikirim ke Log Aktivitas

            // 2. Simpan ke tabel anak (detail_penjualans) & Potong Stok
            foreach($keranjang as $id_makanan => $item) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan, // (atau $penjualan->id tergantung primary key-nya)
                    'id_makanan' => $id_makanan,
                    'harga_satuan' => $item['harga_satuan'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update stok makanan (potong stok etalase & stok utama)
                $makanan = Makanan::find($id_makanan);
                $makanan->stok_etalase -= $item['jumlah'];
                $makanan->stok -= $item['jumlah'];
                $makanan->save();

                $item_terjual[] = "{$item['jumlah']}x {$item['nama_makanan']}";
            }

            // 3. Catat ke Log Aktivitas (Terintegrasi)
            $detail_log = implode(', ', $item_terjual);
            LogController::recordLog('Penjualan Kasir', "Kode: {$kode_transaksi} | Nota {$no_nota}: {$detail_log} (Total: Rp " . number_format($total_harga, 0, ',', '.') . ")");

            DB::commit();

            // Bersihkan meja kasir (keranjang)
            session()->forget('keranjang');

            return redirect()->route('penjualan.index')->with('success', "Pembayaran Berhasil! Kembalian: Rp " . number_format($kembalian, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat checkout: ' . $e->getMessage());
        }
    }
}