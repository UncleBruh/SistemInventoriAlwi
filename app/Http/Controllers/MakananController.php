<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use Illuminate\Http\Request;

class MakananController extends Controller
{
    // Menampilkan halaman daftar barang
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');
        $sort = $request->query('sort', 'terbaru'); // Default: terbaru

        $query = Makanan::query();

        // Filter berdasarkan kategori jika ada (dengan AND logic)
        if ($kategori) {
            $query->where('jenis_makanan', $kategori);
        }

        // Filter berdasarkan search query jika ada (HANYA di nama dan barcode, bukan di kategori)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_makanan', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        // Sorting berdasarkan stok
        switch($sort) {
            case 'stok_asc':
                $query->orderBy('stok', 'asc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            default: // 'terbaru'
                $query->latest();
        }

        $makanan = $query->get();

        // Ambil daftar kategori unik untuk dropdown filter
        $categories = Makanan::whereNotNull('jenis_makanan')
                             ->where('jenis_makanan', '!=', '')
                             ->distinct()
                             ->pluck('jenis_makanan')
                             ->sort();

        return view('makanan.index', compact('makanan', 'search', 'kategori', 'categories', 'sort'));
    }

    // Menampilkan halaman form pendaftaran barang baru
    public function create(Request $request)
    {
        // Ambil daftar kategori unik yang sudah pernah diinput ke database
        // Hanya ambil yang tidak kosong
        $categories = Makanan::whereNotNull('jenis_makanan')
                             ->where('jenis_makanan', '!=', '')
                             ->distinct()
                             ->pluck('jenis_makanan');

        // Menangkap "jenis" dari tombol yang diklik di halaman depan (Makanan/Minuman)
        $default_type = $request->query('type');

        return view('makanan.create', compact('categories', 'default_type'));
    }

    // Memproses data dari form dan menyimpannya ke database
    public function store(Request $request)
    {
        // Logika: Jika user mengisi inputan 'Kategori Baru', gunakan itu.
        // Jika tidak, gunakan 'Kategori' yang dipilih dari dropdown.
        $jenis_makanan = $request->jenis_makanan_baru ?: $request->jenis_makanan;

        $request->validate([
            'barcode' => 'nullable|string|unique:makanan,barcode',
            'nama_makanan' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            // Validasi khusus: kategori dari dropdown boleh kosong ASALKAN kategori baru diisi
            'jenis_makanan' => $request->jenis_makanan_baru ? 'nullable' : 'required|string',
            'jenis_makanan_baru' => 'nullable|string|max:50',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada barang lain!',
            'jenis_makanan.required' => 'Kategori wajib dipilih dari daftar atau buat baru!',
        ]);

        Makanan::create([
            'barcode' => $request->barcode,
            'nama_makanan' => $request->nama_makanan,
            'jenis_makanan' => $jenis_makanan, // Menyimpan hasil logika
            'harga' => $request->harga,
            'stok' => $request->stok,
        ]);

        return redirect()->route('makanan.index')->with('success', 'Barang baru berhasil didaftarkan!');
    }

    // API untuk mencari produk berdasarkan barcode (untuk fitur scan barcode)
    public function findByBarcode($barcode)
    {
        $makanan = Makanan::where('barcode', $barcode)->first();

        if (!$makanan) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'id_makanan' => $makanan->id_makanan,
            'nama_makanan' => $makanan->nama_makanan,
            'jenis_makanan' => $makanan->jenis_makanan,
            'harga' => $makanan->harga,
            'stok' => $makanan->stok,
            'barcode' => $makanan->barcode
        ]);
    }

    // Biarkan fungsi bawaan resource lainnya kosong
    public function show($id) {}
    
    public function edit($id)
    {
        $makanan = Makanan::findOrFail($id);
        $categories = Makanan::whereNotNull('jenis_makanan')
                             ->where('jenis_makanan', '!=', '')
                             ->distinct()
                             ->pluck('jenis_makanan')
                             ->sort();
        
        return view('makanan.edit', compact('makanan', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $makanan = Makanan::findOrFail($id);
        
        // Logika: Jika user mengisi inputan 'Kategori Baru', gunakan itu.
        // Jika tidak, gunakan 'Kategori' yang dipilih dari dropdown.
        $jenis_makanan = $request->jenis_makanan_baru ?: $request->jenis_makanan;

        $request->validate([
            'barcode' => 'nullable|string|unique:makanan,barcode,' . $id . ',id_makanan',
            'nama_makanan' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
            // Validasi khusus: kategori dari dropdown boleh kosong ASALKAN kategori baru diisi
            'jenis_makanan' => $request->jenis_makanan_baru ? 'nullable' : 'required|string',
            'jenis_makanan_baru' => 'nullable|string|max:50',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada barang lain!',
            'jenis_makanan.required' => 'Kategori wajib dipilih dari daftar atau buat baru!',
        ]);

        // Update TANPA stok (stok hanya bisa diubah dari mutasi stok)
        $makanan->update([
            'barcode' => $request->barcode,
            'nama_makanan' => $request->nama_makanan,
            'jenis_makanan' => $jenis_makanan,
            'harga' => $request->harga,
        ]);

        return redirect()->route('makanan.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $makanan = Makanan::findOrFail($id);
        $makanan->delete();

        return redirect()->route('makanan.index')->with('success', 'Barang berhasil dihapus!');
    }
}
