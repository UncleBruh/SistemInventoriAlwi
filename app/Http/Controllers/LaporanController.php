<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
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

        $mutasiMasuk = $query->get();

        return view('laporan.masuk', compact('mutasiMasuk'));
    }

    public function cetakMutasiMasuk(Request $request)
    {
        $query = MutasiMasuk::with('makanan')->orderBy('tgl_mutasi', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mutasi', [$request->start_date, $request->end_date]);
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

        $mutasiKeluar = $query->get();
        
        // Render ke PDF
        $pdf = Pdf::loadView('laporan.keluar_pdf', compact('mutasiKeluar'));
        return $pdf->stream('Laporan_Barang_Keluar.pdf');
    }
}