<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use App\Models\MutasiKeluar;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total Pendapatan Bulan Ini - From Penjualan table
            $bulanIni = Penjualan::whereMonth('tanggal_penjualan', Carbon::now()->month)
                ->whereYear('tanggal_penjualan', Carbon::now()->year)
                ->sum('total_harga');

            // Total Unit Terjual Bulan Ini - From DetailPenjualan
            $unitBulanIni = DetailPenjualan::join('penjualans', 'detail_penjualans.id_penjualan', '=', 'penjualans.id_penjualan')
                ->whereMonth('penjualans.tanggal_penjualan', Carbon::now()->month)
                ->whereYear('penjualans.tanggal_penjualan', Carbon::now()->year)
                ->sum('detail_penjualans.jumlah');

            // Total Stok Barang (Inventory Count)
            $totalStok = Makanan::sum('stok') ?? 0;

            // Jumlah Item di Inventory
            $jumlahItem = Makanan::count() ?? 0;

            // Top 5 Makanan Paling Laris (30 hari terakhir) - From DetailPenjualan
            $topMakananData = DetailPenjualan::join('penjualans', 'detail_penjualans.id_penjualan', '=', 'penjualans.id_penjualan')
                ->join('makanan', 'detail_penjualans.id_makanan', '=', 'makanan.id_makanan')
                ->whereDate('penjualans.tanggal_penjualan', '>=', Carbon::now()->subDays(30))
                ->select('detail_penjualans.id_makanan', 'makanan.nama_makanan', DB::raw('SUM(detail_penjualans.jumlah) as total_qty'))
                ->groupBy('detail_penjualans.id_makanan', 'makanan.nama_makanan')
                ->orderByDesc('total_qty')
                ->get();

            $topMakanan = [];
            if ($topMakananData->count() > 0) {
                $topMakanan = $topMakananData
                    ->map(function ($item) {
                        return [
                            'nama' => $item->nama_makanan ?? 'N/A',
                            'total_qty' => $item->total_qty,
                        ];
                    })
                    ->take(5);
            }

            // Tren Penjualan Harian (7 hari terakhir)
            $trendPenjualan = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $tanggal = Carbon::parse($date)->format('d M');

                $total = Penjualan::whereDate('tanggal_penjualan', $date)
                    ->sum('total_harga');

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
