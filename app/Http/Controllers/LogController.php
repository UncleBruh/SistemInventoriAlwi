<?php

namespace App\Http\Controllers;

use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
use App\Models\Makanan;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk fungsi recordLog
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk fungsi recordLog

class LogController extends Controller
{
    public function index(Request $request)
    {
        $id_makanan = $request->input('id_makanan');
        $tgl_mulai = $request->input('tgl_mulai');
        $tgl_akhir = $request->input('tgl_akhir');
        $jenis_aktivitas = $request->input('jenis_aktivitas', 'semua');
        $sort = $request->input('sort', 'terbaru');

        $masuk = MutasiMasuk::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'id_makanan' => $item->id_makanan,
                'tgl_input' => $item->created_at->format('Y-m-d H:i:s'), // Kapan diklik simpan
                'tgl_aktual' => Carbon::parse($item->tgl_mutasi)->format('Y-m-d'), // Kapan fisik barang masuk
                'tgl_aktual_date' => $item->tgl_mutasi,
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Masuk',
                'jumlah' => '+' . $item->jumlah_masuk,
                'jumlah_nilai' => $item->jumlah_masuk,
                'alasan' => '-',
                'petugas' => $item->pengguna ? $item->pengguna->username : '-',
                'sort_date' => $item->created_at // Acuan pengurutan terbaru
            ];
        });
        
        $keluar = MutasiKeluar::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'id_makanan' => $item->id_makanan,
                'tgl_input' => $item->created_at->format('Y-m-d H:i:s'),
                'tgl_aktual' => Carbon::parse($item->tgl_mutasi)->format('Y-m-d'),
                'tgl_aktual_date' => $item->tgl_mutasi,
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Keluar',
                'jumlah' => '-' . $item->jumlah_keluar,
                'jumlah_nilai' => $item->jumlah_keluar,
                'alasan' => $item->alasan,
                'petugas' => $item->pengguna ? $item->pengguna->username : '-',
                'sort_date' => $item->created_at
            ];
        });

        // Gabungkan
        $semua_log_all = $masuk->concat($keluar);

        // Filter berdasarkan jenis aktivitas
        if ($jenis_aktivitas === 'masuk') {
            $semua_log_all = $semua_log_all->filter(function($item) {
                return $item->jenis === 'Barang Masuk';
            });
        } elseif ($jenis_aktivitas === 'keluar') {
            $semua_log_all = $semua_log_all->filter(function($item) {
                return $item->jenis === 'Barang Keluar';
            });
        }

        // Filter berdasarkan produk
        if ($id_makanan) {
            $semua_log_all = $semua_log_all->filter(function($item) use ($id_makanan) {
                return $item->id_makanan == $id_makanan;
            });
        }

        // Filter berdasarkan tanggal mulai
        if ($tgl_mulai) {
            $semua_log_all = $semua_log_all->filter(function($item) use ($tgl_mulai) {
                return Carbon::parse($item->tgl_aktual_date)->format('Y-m-d') >= $tgl_mulai;
            });
        }

        // Filter berdasarkan tanggal akhir
        if ($tgl_akhir) {
            $semua_log_all = $semua_log_all->filter(function($item) use ($tgl_akhir) {
                return Carbon::parse($item->tgl_aktual_date)->format('Y-m-d') <= $tgl_akhir;
            });
        }

        // Sorting
        if ($sort == 'terbaru') {
            $semua_log_all = $semua_log_all->sortByDesc('sort_date')->values();
        } elseif ($sort == 'terlama') {
            $semua_log_all = $semua_log_all->sortBy('sort_date')->values();
        } elseif ($sort == 'jumlah_desc') {
            $semua_log_all = $semua_log_all->sortByDesc('jumlah_nilai')->values();
        } elseif ($sort == 'jumlah_asc') {
            $semua_log_all = $semua_log_all->sortBy('jumlah_nilai')->values();
        } else {
            $semua_log_all = $semua_log_all->sortByDesc('sort_date')->values();
        }

        // Manual pagination (50 item per halaman)
        $perPage = 50;
        $currentPage = $request->get('page', 1);
        $total = $semua_log_all->count();
        $offset = ($currentPage - 1) * $perPage;

        $semua_log = new LengthAwarePaginator(
            $semua_log_all->slice($offset, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Get semua makanan untuk dropdown filter
        $makanan = Makanan::orderBy('nama_makanan')->get();

        return view('log.index', compact('semua_log', 'makanan', 'id_makanan', 'tgl_mulai', 'tgl_akhir', 'jenis_aktivitas', 'sort'));
    }

    /**
     * Fungsi statis untuk mencatat aktivitas ke dalam tabel log
     */
    public static function recordLog($action, $description)
    {
        // Pastikan nama tabel log di database-mu adalah 'log' atau 'logs'
        // Jika tabel log belum ada, abaikan sementara error ini dengan try-catch
        try {
            DB::table('logs')->insert([
                'id_pengguna' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Abaikan error jika tabel logs belum dibuat, agar proses mutasi tidak terhenti
        }
    }
}