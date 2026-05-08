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
                'stok' => $stok_gudang_sesudah + $stok_etalase_sesudah,  // Sinkronisasi stok total
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
            // PERBAIKAN BUG: Cek apakah stok etalase sudah berkurang karena penjualan
            // Saat alokasi dibuat: stok_etalase = $alokasi->stok_etalase_sesudah
            // Sekarang: stok_etalase = $makanan->stok_etalase (bisa lebih kecil karena penjualan)

            $stok_etalase_saat_ini = $makanan->stok_etalase;
            $stok_etalase_saat_alokasi = $alokasi->stok_etalase_sesudah;

            // Jika ada penjualan: stok_etalase_saat_ini < stok_etalase_saat_alokasi
            $selisih_penjualan = $stok_etalase_saat_alokasi - $stok_etalase_saat_ini;

            // Jumlah yang bisa dikembalikan ke gudang = yang belum terjual
            $jumlah_untuk_dikembalikan = $alokasi->jumlah_dialokasi - $selisih_penjualan;

            // Validasi: tidak boleh hasil stok menjadi negatif
            if ($jumlah_untuk_dikembalikan < 0) {
                DB::rollBack();
                return redirect()->back()->with('error',
                    'Tidak dapat menghapus alokasi. Stok etalase sudah terjual lebih banyak dari yang dialokasi. ' .
                    'Alokasi: ' . $alokasi->jumlah_dialokasi . ' pcs, Terjual: ' . $selisih_penjualan . ' pcs.');
            }

            if ($jumlah_untuk_dikembalikan > 0) {
                // Kembalikan hanya stok yang belum terjual ke gudang
                $stok_gudang_baru = $makanan->stok_gudang + $jumlah_untuk_dikembalikan;
                $stok_etalase_baru = $makanan->stok_etalase - $jumlah_untuk_dikembalikan;

                $makanan->update([
                    'stok_gudang' => $stok_gudang_baru,
                    'stok_etalase' => $stok_etalase_baru,
                    'stok' => $stok_gudang_baru + $stok_etalase_baru,  // Sinkronisasi stok total
                ]);

                $pesan = 'Alokasi berhasil dihapus. ' . $jumlah_untuk_dikembalikan . ' pcs dikembalikan ke gudang. ' .
                         $selisih_penjualan . ' pcs dianggap terjual dan dihapuskan dari pencatatan alokasi.';
            } else {
                // Semua sudah terjual, tidak ada yang dikembalikan
                $pesan = 'Alokasi berhasil dihapus. Seluruh alokasi (' . $alokasi->jumlah_dialokasi . ' pcs) sudah terjual sebelumnya.';
            }

            // Hapus record alokasi
            $alokasi->delete();

            DB::commit();

            return redirect()->route('alokasi-gudang-etalase.index')
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus alokasi: ' . $e->getMessage());
        }
    }
}
