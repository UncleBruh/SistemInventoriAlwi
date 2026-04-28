<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Makanan;
use App\Models\AlokasiGudangEtalase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlokasiGudangEtalaseController extends Controller
{
    /**
     * Tampilkan daftar semua alokasi gudang ke etalase
     */
    public function index(Request $request)
    {
        $id_makanan = $request->input('id_makanan');
        $tgl_mulai = $request->input('tgl_mulai');
        $tgl_akhir = $request->input('tgl_akhir');
        $sort = $request->input('sort', 'terbaru');

        $query = AlokasiGudangEtalase::with(['makanan', 'pengguna']);

        // Filter berdasarkan produk/makanan
        if ($id_makanan) {
            $query->where('id_makanan', $id_makanan);
        }

        // Filter berdasarkan tanggal mulai
        if ($tgl_mulai) {
            $query->whereDate('tgl_alokasi', '>=', $tgl_mulai);
        }

        // Filter berdasarkan tanggal akhir
        if ($tgl_akhir) {
            $query->whereDate('tgl_alokasi', '<=', $tgl_akhir);
        }

        // Fitur Sorting
        if ($sort == 'terbaru') {
            $query->latest('tgl_alokasi');
        } elseif ($sort == 'terlama') {
            $query->oldest('tgl_alokasi');
        } elseif ($sort == 'jumlah_desc') {
            $query->orderBy('jumlah_dialokasi', 'desc');
        } elseif ($sort == 'jumlah_asc') {
            $query->orderBy('jumlah_dialokasi', 'asc');
        }

        $data = $query->paginate(50);

        // Get semua makanan untuk dropdown filter
        $makanan = Makanan::orderBy('nama_makanan')->get();

        return view('alokasi_gudang_etalase.index', compact('data', 'makanan', 'id_makanan', 'tgl_mulai', 'tgl_akhir', 'sort'));
    }

    /**
     * Tampilkan form untuk membuat alokasi baru
     */
    public function create()
    {
        $makanan = Makanan::where('stok_gudang', '>', 0)->get(); // Hanya tampilkan yang punya stok gudang
        return view('alokasi_gudang_etalase.create', compact('makanan'));
    }

    /**
     * Simpan alokasi gudang ke etalase
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_makanan' => 'required|exists:makanan,id_makanan',
            'jumlah_dialokasi' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $makanan = Makanan::findOrFail($request->id_makanan);
        $jumlah = $request->jumlah_dialokasi;

        // Validasi stok gudang
        if ($makanan->stok_gudang < $jumlah) {
            return redirect()->back()->with('error',
                'Stok gudang tidak mencukupi. Stok gudang saat ini: ' . $makanan->stok_gudang);
        }

        // Hitung stok sebelum dan sesudah
        $stok_gudang_sebelum = $makanan->stok_gudang;
        $stok_gudang_sesudah = $stok_gudang_sebelum - $jumlah;

        $stok_etalase_sebelum = $makanan->stok_etalase;
        $stok_etalase_sesudah = $stok_etalase_sebelum + $jumlah;

        DB::beginTransaction();
        try {
            // Catat alokasi
            AlokasiGudangEtalase::create([
                'id_makanan' => $makanan->id_makanan,
                'jumlah_dialokasi' => $jumlah,
                'stok_gudang_sebelum' => $stok_gudang_sebelum,
                'stok_gudang_sesudah' => $stok_gudang_sesudah,
                'stok_etalase_sebelum' => $stok_etalase_sebelum,
                'stok_etalase_sesudah' => $stok_etalase_sesudah,
                'id_pengguna' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            // Update stok makanan
            $makanan->update([
                'stok_gudang' => $stok_gudang_sesudah,
                'stok_etalase' => $stok_etalase_sesudah,
            ]);

            DB::commit();

            return redirect()->route('alokasi-gudang-etalase.index')
                ->with('success', 'Alokasi barang berhasil disimpan. ' . $jumlah . ' barang telah dipindahkan dari gudang ke etalase.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan alokasi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail alokasi
     */
    public function show($id)
    {
        $alokasi = AlokasiGudangEtalase::with(['makanan', 'pengguna'])->findOrFail($id);
        return view('alokasi_gudang_etalase.show', compact('alokasi'));
    }

    /**
     * Hapus alokasi (hanya untuk Pemilik)
     */
    public function destroy($id)
    {
        // Double-check: pastikan hanya Pemilik yang bisa hapus
        if (Auth::user()->role !== 'Pemilik') {
            abort(403, 'Anda tidak memiliki izin untuk menghapus alokasi. Hanya Pemilik yang dapat menghapus.');
        }

        $alokasi = AlokasiGudangEtalase::findOrFail($id);
        $makanan = Makanan::findOrFail($alokasi->id_makanan);

        DB::beginTransaction();
        try {
            // Kembalikan stok gudang dan kurangi etalase
            $makanan->update([
                'stok_gudang' => $makanan->stok_gudang + $alokasi->jumlah_dialokasi,
                'stok_etalase' => $makanan->stok_etalase - $alokasi->jumlah_dialokasi,
            ]);

            // Hapus record alokasi
            $alokasi->delete();

            DB::commit();

            return redirect()->route('alokasi-gudang-etalase.index')
                ->with('success', 'Alokasi berhasil dihapus. Stok telah dikembalikan ke gudang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus alokasi: ' . $e->getMessage());
        }
    }
}
