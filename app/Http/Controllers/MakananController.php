<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use Illuminate\Http\Request;

class MakananController extends Controller
{
    // Menampilkan halaman daftar makanan
    public function index()
    {
        // Mengambil semua data makanan dari database, diurutkan dari yang terbaru
        $makanan = Makanan::latest()->get(); 
        
        // Nanti kita akan buat file view 'makanan.index'
        return view('makanan.index', compact('makanan'));
    }

    // Menampilkan halaman form pendaftaran barang baru
    public function create()
    {
        return view('makanan.create');
    }

    // Memproses data dari form dan menyimpannya ke database
    public function store(Request $request)
    {
        // Validasi inputan dari form
        $request->validate([
            'barcode' => 'nullable|string|unique:makanan,barcode',
            'nama_makanan' => 'required|string|max:50',
            'jenis_makanan' => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada jajanan lain!',
        ]);

        // Simpan ke database
        Makanan::create([
            'barcode' => $request->barcode,
            'nama_makanan' => $request->nama_makanan,
            'jenis_makanan' => $request->jenis_makanan,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ]);

        // Catatan: Logika pencatatan Log Aktivitas "Barang Masuk" perdana 
        // bisa ditambahkan di sini nanti jika diperlukan.

        return redirect()->route('makanan.index')->with('success', 'Jajanan baru berhasil didaftarkan!');
    }

    // (Biarkan fungsi show, edit, update, destroy kosong untuk saat ini)
}