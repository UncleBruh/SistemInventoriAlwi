<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use Illuminate\Http\Request;

class MakananController extends Controller
{
    // Menampilkan halaman daftar barang
    public function index()
    {
        $makanan = Makanan::latest()->get(); 
        return view('makanan.index', compact('makanan'));
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

    // Biarkan fungsi bawaan resource lainnya kosong
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}