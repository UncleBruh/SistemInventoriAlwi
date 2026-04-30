<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
use App\Models\PengeluaranGudang;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // --- LAPORAN BARANG MASUK ---
    public function mutasiMasuk(Request $request)
    {
        $query = MutasiMasuk::with(['makanan', 'pengguna'])->orderBy('tgl_mutasi', 'desc');

        // Jika ada filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mutasi', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        // Filter sortir (terbaru/terlama)
        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_mutasi', 'asc');
            } else {
                $query->orderBy('tgl_mutasi', 'desc');
            }
        }

        $mutasiMasuk = $query->get();

        return view('laporan.masuk', compact('mutasiMasuk'));
    }

    public function cetakMutasiMasuk(Request $request)
    {
        $query = MutasiMasuk::with('makanan')->orderBy('tgl_mutasi', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mutasi', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        $mutasiMasuk = $query->get();

        // Render ke PDF
        $pdf = Pdf::loadView('laporan.masuk_pdf', compact('mutasiMasuk'));
        return $pdf->stream('Laporan_Barang_Masuk.pdf');
    }

    // --- LAPORAN BARANG KELUAR ---
    public function mutasiKeluar(Request $request)
    {
        $query = MutasiKeluar::with(['makanan', 'pengguna'])->orderBy('tgl_mutasi', 'desc');

        // Jika user memilih rentang waktu, filter datanya
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mutasi', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        // Filter sortir (terbaru/terlama)
        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_mutasi', 'asc');
            } else {
                $query->orderBy('tgl_mutasi', 'desc');
            }
        }

        $mutasiKeluar = $query->get();
        return view('laporan.keluar', compact('mutasiKeluar'));
    }

    public function cetakMutasiKeluar(Request $request) // (Sesuaikan dengan nama fungsi cetak aslimu)
    {
        $query = MutasiKeluar::with(['makanan', 'pengguna'])->orderBy('tgl_mutasi', 'desc');

        // Filter data yang dicetak sesuai rentang waktu yang dipilih
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mutasi', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        $mutasiKeluar = $query->get();

        // Render ke PDF
        $pdf = Pdf::loadView('laporan.keluar_pdf', compact('mutasiKeluar'));
        return $pdf->stream('Laporan_Barang_Keluar.pdf');
    }

    // --- LAPORAN PENJUALAN ---
    public function laporanPenjualan(Request $request)
    {
        $query = \App\Models\Penjualan::with(['pengguna', 'detail.makanan'])->orderBy('tgl_penjualan', 'desc');

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_penjualan', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('detail.makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        // Filter sortir (terbaru/terlama)
        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_penjualan', 'asc');
            } else {
                $query->orderBy('tgl_penjualan', 'desc');
            }
        }

        $penjualan = $query->get();

        // Hitung total penghasilan per tanggal
        $totalPerTanggal = $penjualan->groupBy('tgl_penjualan')->map(function($items) {
            return $items->sum('total_harga');
        });

        return view('laporan.penjualan', compact('penjualan', 'totalPerTanggal'));
    }

    public function cetakLaporanPenjualan(Request $request)
    {
        $query = \App\Models\Penjualan::with(['pengguna', 'detail.makanan'])->orderBy('tgl_penjualan', 'desc');

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_penjualan', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('detail.makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        $penjualan = $query->get();

        // Hitung total penghasilan per tanggal
        $totalPerTanggal = $penjualan->groupBy('tgl_penjualan')->map(function($items) {
            return $items->sum('total_harga');
        });

        // Render ke PDF
        $pdf = Pdf::loadView('laporan.penjualan_pdf', compact('penjualan', 'totalPerTanggal'));
        return $pdf->stream('Laporan_Penjualan.pdf');
    }

    // --- LAPORAN PENGELUARAN GUDANG ---
    public function pengeluaranGudang(Request $request)
    {
        $query = \App\Models\PengeluaranGudang::with(['makanan', 'pengguna'])->orderBy('tgl_pengeluaran', 'desc');

        // Jika ada filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pengeluaran', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        // Filter sortir (terbaru/terlama)
        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_pengeluaran', 'asc');
            } else {
                $query->orderBy('tgl_pengeluaran', 'desc');
            }
        }

        $pengeluaranGudang = $query->get();

        return view('laporan.pengeluaran_gudang', compact('pengeluaranGudang'));
    }

    public function cetakPengeluaranGudang(Request $request)
    {
        $query = \App\Models\PengeluaranGudang::with(['makanan', 'pengguna'])->orderBy('tgl_pengeluaran', 'desc');

        // Filter data yang dicetak sesuai rentang waktu yang dipilih
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pengeluaran', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        $pengeluaranGudang = $query->get();

        // Render ke PDF
        $pdf = Pdf::loadView('laporan.pengeluaran_gudang_pdf', compact('pengeluaranGudang'));
        return $pdf->stream('Laporan_Pengeluaran_Gudang.pdf');
    }
}
