<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use App\Models\MutasiKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total Pendapatan Bulan Ini
            $bulanIni = MutasiKeluar::whereMonth('tgl_mutasi', Carbon::now()->month)
                ->whereYear('tgl_mutasi', Carbon::now()->year)
                ->where('alasan', 'Penjualan')
                ->with('makanan')
                ->get()
                ->sum(function ($item) {
                    return $item->jumlah_keluar * ($item->makanan->harga ?? 0);
                });

            // Total Unit Terjual Bulan Ini
            $unitBulanIni = MutasiKeluar::whereMonth('tgl_mutasi', Carbon::now()->month)
                ->whereYear('tgl_mutasi', Carbon::now()->year)
                ->where('alasan', 'Penjualan')
                ->sum('jumlah_keluar');

            // Total Stok Barang (Inventory Count)
            $totalStok = Makanan::sum('stok') ?? 0;

            // Jumlah Item di Inventory
            $jumlahItem = Makanan::count() ?? 0;

            // Top 5 Makanan Paling Laris (7 hari terakhir)
            $topMakananData = MutasiKeluar::whereDate('tgl_mutasi', '>=', Carbon::now()->subDays(7))
                ->where('alasan', 'Penjualan')
                ->with('makanan')
                ->get();

            $topMakanan = [];
            if ($topMakananData->count() > 0) {
                $topMakanan = $topMakananData
                    ->groupBy('id_makanan')
                    ->map(function ($group) {
                        return [
                            'nama' => $group->first()->makanan->nama_makanan ?? 'N/A',
                            'total_qty' => $group->sum('jumlah_keluar'),
                        ];
                    })
                    ->sortByDesc('total_qty')
                    ->take(5);
            }

            // Tren Penjualan Harian (7 hari terakhir)
            $trendPenjualan = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $tanggal = Carbon::parse($date)->format('d M');

                $total = MutasiKeluar::whereDate('tgl_mutasi', $date)
                    ->where('alasan', 'Penjualan')
                    ->with('makanan')
                    ->get()
                    ->sum(function ($item) {
                        return $item->jumlah_keluar * ($item->makanan->harga ?? 0);
                    });

                $trendPenjualan[] = [
                    'tanggal' => $tanggal,
                    'total' => $total,
                ];
            }

            // Stok Per Kategori
            $makananData = Makanan::with('kategori')->get();
            $stokPerKategori = [];

            if ($makananData->count() > 0) {
                $stokPerKategori = $makananData
                    ->groupBy(function ($item) {
                        return $item->kategori->nama_kategori ?? 'Tanpa Kategori';
                    })
                    ->map(function ($group) {
                        return $group->sum('stok');
                    });
            }

            return view('dashboard', [
                'bulanIni' => $bulanIni ?? 0,
                'unitBulanIni' => $unitBulanIni ?? 0,
                'totalStok' => $totalStok ?? 0,
                'jumlahItem' => $jumlahItem ?? 0,
                'topMakanan' => $topMakanan,
                'trendPenjualan' => $trendPenjualan,
                'stokPerKategori' => $stokPerKategori,
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return view('dashboard', [
                'bulanIni' => 0,
                'unitBulanIni' => 0,
                'totalStok' => 0,
                'jumlahItem' => 0,
                'topMakanan' => [],
                'trendPenjualan' => [],
                'stokPerKategori' => [],
            ]);
        }
    }
}
