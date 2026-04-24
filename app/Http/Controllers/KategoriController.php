<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        // Memanggil file index.blade.php di dalam folder kategori
        return view('kategori.index', compact('kategori')); 
    }

    public function store(Request $request)
    {
        // FITUR ANTI DUPLIKAT ADA DI SINI ("unique:kategori,nama_kategori")
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori'
        ], [
            'nama_kategori.unique' => 'Kategori ini sudah ada di database! Silakan gunakan nama lain.',
            'nama_kategori.required' => 'Nama kategori tidak boleh kosong.'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}