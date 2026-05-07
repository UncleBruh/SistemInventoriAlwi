<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Makanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    /**
     * Display a listing of returns.
     */
    public function index(Request $request)
    {
        $query = Retur::with(['penjualan', 'makanan', 'pengguna'])->orderBy('tgl_retur', 'desc');

        // Filter berdasarkan range tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_retur', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan nama produk
        if ($request->filled('nama_produk')) {
            $query->whereHas('makanan', function($q) use ($request) {
                $q->where('nama_makanan', 'like', '%' . $request->nama_produk . '%');
            });
        }

        // Filter berdasarkan kode transaksi penjualan
        if ($request->filled('kode_transaksi')) {
            $query->whereHas('penjualan', function($q) use ($request) {
                $q->where('kode_transaksi', 'like', '%' . $request->kode_transaksi . '%');
            });
        }

        // Sorting
        if ($request->filled('sort')) {
            if ($request->sort === 'terlama') {
                $query->orderBy('tgl_retur', 'asc');
            } elseif ($request->sort === 'terbanyak') {
                $query->orderBy('total_retur', 'desc');
            } elseif ($request->sort === 'tersedikit') {
                $query->orderBy('total_retur', 'asc');
            } else {
                $query->orderBy('tgl_retur', 'desc');
            }
        }

        $retur = $query->paginate(50)->appends($request->all());

        return view('retur.index', compact('retur'));
    }

    /**
     * Show the form for creating a new return.
     */
    public function create()
    {
        // Ambil semua transaksi penjualan yang sudah terjadi
        $penjualanList = Penjualan::with(['detail.makanan', 'pengguna'])
                                  ->orderBy('tanggal_penjualan', 'desc')
                                  ->get();

        return view('retur.create', compact('penjualanList'));
    }

    /**
     * Store a newly created return in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|exists:penjualans,id_penjualan',
            'retur_items' => 'required|array|min:1',
            'retur_items.*.id_makanan' => 'required|exists:makanan,id_makanan',
            'retur_items.*.jumlah_retur' => 'required|integer|min:1',
            'retur_items.*.harga_satuan' => 'required|numeric|min:0',
            'retur_items.*.alasan_retur' => 'required|string|max:255',
            'retur_items.*.keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::findOrFail($request->id_penjualan);

            // Loop through each retur item
            foreach ($request->retur_items as $item) {
                if (!$item['jumlah_retur'] || $item['jumlah_retur'] <= 0) {
                    continue;
                }

                $totalRetur = $item['jumlah_retur'] * $item['harga_satuan'];

                // Create retur record
                Retur::create([
                    'id_penjualan' => $request->id_penjualan,
                    'id_makanan' => $item['id_makanan'],
                    'jumlah_retur' => $item['jumlah_retur'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_retur' => $totalRetur,
                    'alasan_retur' => $item['alasan_retur'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'id_pengguna' => Auth::id(),
                    'tgl_retur' => now(),
                ]);

                // Update penjualan total_harga (kurangi dengan jumlah retur)
                $penjualan->total_harga -= $totalRetur;

                // Update makanan stok_etalase (kembalikan ke etalase karena produk tidak terjual)
                $makanan = Makanan::findOrFail($item['id_makanan']);
                $makanan->stok_etalase += $item['jumlah_retur'];
                $makanan->save();
            }

            // Simpan perubahan total_harga penjualan
            $penjualan->save();

            DB::commit();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil dicatat dan pendapatan telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified return.
     */
    public function show($id)
    {
        $retur = Retur::with(['penjualan', 'makanan', 'pengguna'])->findOrFail($id);

        return view('retur.show', compact('retur'));
    }

    /**
     * Show the form for editing the specified return.
     */
    public function edit($id)
    {
        $retur = Retur::with(['penjualan', 'makanan', 'pengguna'])->findOrFail($id);

        // Hanya Pemilik yang bisa edit
        if (Auth::user()->role !== 'Pemilik') {
            return redirect()->route('retur.show', $retur->id_retur)->with('error', 'Anda tidak memiliki akses untuk mengubah data retur.');
        }

        return view('retur.edit', compact('retur'));
    }

    /**
     * Update the specified return in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'alasan_retur' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $retur = Retur::findOrFail($id);

        // Hanya Pemilik yang bisa edit
        if (Auth::user()->role !== 'Pemilik') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data retur.');
        }

        $retur->update($request->only(['alasan_retur', 'keterangan']));

        return redirect()->route('retur.show', $retur->id_retur)->with('success', 'Data retur berhasil diperbarui.');
    }

    /**
     * Remove the specified return from storage.
     */
    public function destroy($id)
    {
        $retur = Retur::findOrFail($id);

        // Hanya Pemilik yang bisa hapus
        if (Auth::user()->role !== 'Pemilik') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus data retur.');
        }

        try {
            DB::beginTransaction();

            // Reverse the changes:
            // 1. Tambah kembali total_harga ke penjualan
            $penjualan = $retur->penjualan;
            $penjualan->total_harga += $retur->total_retur;
            $penjualan->save();

            // 2. Kurangi stok_etalase makanan (karena produk tidak jadi diretur)
            $makanan = $retur->makanan;
            if ($makanan) {
                $makanan->stok_etalase -= $retur->jumlah_retur;
                $makanan->save();
            }

            // 3. Hapus retur record
            $retur->delete();

            DB::commit();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus dan data telah dipulihkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Get detail penjualan by penjualan ID (API endpoint for AJAX)
     */
    public function getDetailPenjualan($id_penjualan)
    {
        $penjualan = Penjualan::with('detail.makanan')->findOrFail($id_penjualan);

        return response()->json([
            'success' => true,
            'penjualan' => $penjualan,
            'detail' => $penjualan->detail,
        ]);
    }
}
