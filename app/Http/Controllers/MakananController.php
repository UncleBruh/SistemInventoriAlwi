<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use App\Models\Kategori; // Memanggil model kategori
use Illuminate\Http\Request;

class MakananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori_id = $request->input('kategori'); // Sekarang mencari berdasarkan ID
        $sort = $request->input('sort', 'terbaru');

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

        // Fitur Sorting
        if ($sort == 'terbaru') {
            $query->latest();
        } elseif ($sort == 'terlama') {
            $query->oldest();
        } elseif ($sort == 'stok_terbanyak') {
            $query->orderBy('stok', 'desc');
        } elseif ($sort == 'stok_sedikit') {
            $query->orderBy('stok', 'asc');
        }

        $makanan = $query->paginate(10)->appends($request->all());

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

        Makanan::create($request->all());

        return redirect()->route('makanan.index')->with('success', 'Data jajanan berhasil ditambahkan.');
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
        $makanan->delete();

        return redirect()->route('makanan.index')->with('success', 'Data jajanan berhasil dihapus.');
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