<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use App\Models\Kategori; // Memanggil model kategori
use App\Models\AlokasiGudangEtalase;
use Illuminate\Http\Request;

class MakananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori_id = $request->input('kategori'); // Sekarang mencari berdasarkan ID
        $lokasi = $request->input('lokasi'); // Filter lokasi: gudang_only, etalase_only
        $sort = $request->input('sort', 'terbaru');
        $filter_lokasi = $request->input('filter_lokasi'); // Menangkap input filter lokasi

        // Gunakan eager loading (with) agar tidak berat saat meload relasi nama kategori
        $query = Makanan::with('kategori');

        // Fitur Pencarian
        if ($search) {
            $query->where('nama_makanan', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
        }

        // Fitur Filter Kategori
        if ($kategori_id) {
            $query->where('id_kategori', $kategori_id);
        }

        // Fitur Filter Lokasi
        if ($lokasi === 'gudang_only') {
            $query->where('stok_gudang', '>', 0)
                  ->where('stok_etalase', '=', 0);
        } elseif ($lokasi === 'etalase_only') {
            $query->where('stok_etalase', '>', 0)
                  ->where('stok_gudang', '=', 0);
        }

        // Fitur Filter Lokasi (Etalase / Gudang)
        if ($filter_lokasi === 'etalase') {
            $query->where('stok_etalase', '>', 0);
        } elseif ($filter_lokasi === 'gudang') {
            $query->where('stok_gudang', '>', 0);
        }

        // Fitur Sorting
        switch ($sort) {
            case 'terlama':
                $query->oldest();
                break;
            case 'stok_terbanyak':
                $query->orderBy('stok', 'desc');
                break;
            case 'stok_sedikit':
                $query->orderBy('stok', 'asc');
                break;
            case 'gudang_asc':
                $query->orderBy('stok_gudang', 'asc');
                break;
            case 'gudang_desc':
                $query->orderBy('stok_gudang', 'desc');
                break;
            case 'etalase_asc':
                $query->orderBy('stok_etalase', 'asc');
                break;
            case 'etalase_desc':
                $query->orderBy('stok_etalase', 'desc');
                break;
            case 'terbaru':
            default:
                $query->latest();
                break;
        }

        $makanan = $query->paginate(50)->appends($request->all());

        // Ambil semua data kategori untuk dimunculkan di Dropdown Filter
        $categories = Kategori::orderBy('nama_kategori', 'asc')->get();
        $kategori = $kategori_id;

        return view('makanan.index', compact('makanan', 'search', 'kategori', 'categories', 'sort'));
    }

    public function create()
    {
        // Ambil kategori untuk pilihan saat mendaftar jajanan baru
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('makanan.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_makanan' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'barcode' => 'nullable|string|max:255|unique:makanan,barcode',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        // Saat membuat barang baru, stok awal masuk ke gudang
        $data = $request->all();
        $data['stok_gudang'] = $request->stok;
        $data['stok_etalase'] = 0;

        Makanan::create($data);

        return redirect()->route('makanan.index')->with('success', 'Data jajanan berhasil ditambahkan. Stok masuk ke gudang.');
    }

    public function edit($id)
    {
        $makanan = Makanan::findOrFail($id);
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('makanan.edit', compact('makanan', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $makanan = Makanan::findOrFail($id);

        $request->validate([
            'nama_makanan' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'barcode' => 'nullable|string|max:255|unique:makanan,barcode,' . $makanan->id_makanan . ',id_makanan',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $makanan->update($request->all());

        return redirect()->route('makanan.index')->with('success', 'Data jajanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $makanan = Makanan::findOrFail($id);

        // Hapus semua alokasi gudang etalase yang terkait dengan produk ini
        AlokasiGudangEtalase::where('id_makanan', $id)->delete();

        // Hapus produk
        $makanan->delete();

        return redirect()->route('makanan.index')->with('success', 'Data jajanan berhasil dihapus. Semua alokasi terkait juga telah dihapus.');
    }

    public function findByBarcode($barcode)
    {
        $makanan = Makanan::where('barcode', $barcode)->first();
        if ($makanan) {
            return response()->json(['success' => true, 'data' => $makanan]);
        }
        return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan']);
    }
}
