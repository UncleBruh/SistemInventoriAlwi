<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiKeluar;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data MutasiKeluar yang hanya untuk Penjualan
        $query = MutasiKeluar::with(['makanan', 'pengguna'])
            ->where('alasan', 'Penjualan')
            ->latest('tgl_mutasi');

        // Filter berdasarkan tanggal jika ada
        if ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->whereDate('tgl_mutasi', '>=', $tanggalMulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereDate('tgl_mutasi', '<=', $tanggalAkhir);
        }

        $data = $query->get();

        // Hitung ringkasan data
        $totalPendapatan = $data->sum(function($item) {
            return $item->jumlah_keluar * $item->makanan->harga;
        });

        $jumlahTransaksi = $data->count();
        $totalUnitTerjual = $data->sum('jumlah_keluar');

        // Kelompokkan data berdasarkan tanggal untuk laporan per hari
        $laporanPerHari = $data->groupBy(function($item) {
            return Carbon::parse($item->tgl_mutasi)->format('Y-m-d');
        })->map(function($items) {
            $totalHariIni = $items->sum(function($item) {
                return $item->jumlah_keluar * $item->makanan->harga;
            });

            return [
                'tanggal' => $items->first()->tgl_mutasi,
                'items' => $items,
                'total' => $totalHariIni,
                'jumlah_unit' => $items->sum('jumlah_keluar'),
            ];
        })->sortByDesc('tanggal')->values();

        return view('penjualan.index', compact('data', 'laporanPerHari', 'totalPendapatan', 'jumlahTransaksi', 'totalUnitTerjual'));
    }
}

