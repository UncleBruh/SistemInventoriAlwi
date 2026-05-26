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
    public function index(Request $request)
    {
        $query = Retur::with(['penjualan', 'makanan', 'pengguna']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_retur', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_retur', 'asc');
            } else {
                $query->orderBy('tgl_retur', 'desc');
            }
        } else {
            $query->orderBy('tgl_retur', 'desc');
        }

        $retur = $query->get();

        $total_pengembalian = $retur->sum('nominal_pengembalian');

        return view('retur.index', compact('retur', 'total_pengembalian'));
    }

    public function create($id_penjualan)
    {
        $selected_id = $id_penjualan;

        $penjualan = Penjualan::with('detail.makanan')
            ->where('tanggal_penjualan', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('retur.create', compact('penjualan', 'selected_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|exists:penjualans,id_penjualan',
            'id_makanan' => 'required|array',
            'id_makanan.*' => 'exists:makanan,id_makanan',
            'jumlah_retur' => 'required|array',
            'jumlah_retur.*' => 'integer|min:1',
            'alasan' => 'required|string',
            'tgl_retur' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::findOrFail($request->id_penjualan);

            foreach ($request->id_makanan as $index => $id_makanan) {
                $jumlah_retur = $request->jumlah_retur[$index];
                $makanan = Makanan::findOrFail($id_makanan);

                $detailPenjualan = DetailPenjualan::where('id_penjualan', $request->id_penjualan)
                                                  ->where('id_makanan', $id_makanan)
                                                  ->first();

                if (!$detailPenjualan) {
                    DB::rollback();
                    return back()->with('error', 'Barang ' . $makanan->nama_makanan . ' tidak ditemukan dalam kode transaksi tersebut!');
                }

                if ($jumlah_retur > $detailPenjualan->jumlah) {
                    DB::rollback();
                    return back()->with('error', 'Jumlah retur ' . $makanan->nama_makanan . ' melebihi jumlah barang yang dibeli!');
                }

                $nominal_pengembalian = $jumlah_retur * $detailPenjualan->harga_satuan;

                Retur::create([
                    'id_penjualan' => $request->id_penjualan,
                    'id_makanan' => $id_makanan,
                    'id_pengguna' => Auth::id() ?? 1,
                    'jumlah_retur' => $jumlah_retur,
                    'nominal_pengembalian' => $nominal_pengembalian,
                    'alasan' => $request->alasan,
                    'tgl_retur' => $request->tgl_retur,
                ]);

                $makanan->stok_etalase += $jumlah_retur;
                $makanan->stok = $makanan->stok_gudang + $makanan->stok_etalase;
                $makanan->save();

                $penjualan->total_harga -= $nominal_pengembalian;

                $detailPenjualan->jumlah -= $jumlah_retur;
                $detailPenjualan->save();
            }

            $penjualan->save();

            DB::commit();

            return redirect()->route('retur.index')->with('success', 'Retur barang berhasil! Stok etalase bertambah dan total penjualan telah dipotong otomatis.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
