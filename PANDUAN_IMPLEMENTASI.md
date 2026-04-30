# PANDUAN IMPLEMENTASI REVISI SISTEM INVENTORY

## 📋 RINGKASAN PERUBAHAN

Sistem inventory Anda telah direvisi dengan fitur-fitur baru:

### 1. ✅ Fitur Pengelolaan Gudang (Pengeluaran Gudang)
- **Akses**: Pemilik ONLY
- **Lokasi Menu**: MUTASI → Pengeluaran Gudang
- **Fitur**: 
  - Scan barcode untuk mencari produk
  - Input jumlah pengeluaran
  - Alasan: Expired, Tikus, Rusak, Lainnya
  - Keterangan detail (opsional)
  - Tracking stok gudang sebelum-sesudah
- **Route**: `/pengeluaran-gudang/`

### 2. ✅ Revisi Pengelolaan Stok Etalase (Barang Keluar)
- **Akses**: Pemilik ONLY (sebelumnya: Pemilik+Admin)
- **Lokasi Menu**: MUTASI → Pengelolaan Stok Etalase
- **Perubahan**:
  - Hapus opsi "PENJUALAN" dari dropdown alasan
  - Opsi alasan baru: Rusak, Hilang, Expired, Keperluan Prive
  - Dengan scan barcode dan keterangan
- **Route**: `/pengelolaan-stok-etalase/`

### 3. ✅ Menu Penjualan (Mengganti Transaksi/Kasir)
- **Akses**: Pemilik + Admin
- **Lokasi Menu**: TRANSAKSI → Mesin Kasir (tetap di atas, sebelum MUTASI)
- **Fitur**:
  - Scan multiple barcode sekaligus
  - Cart system dengan multiple items
  - Generate KODE_TRANSAKSI unik (TRX-YYYYMMDD-XXX)
  - Total bayar otomatis
- **Route**: `/penjualan/tambah` (create) dan `/penjualan/simpan` (store)
- **Database Field**: `kode_transaksi` di tabel `penjualans`

### 4. ✅ Laporan Penjualan (Baru)
- **Akses**: Pemilik ONLY
- **Lokasi Menu**: LAPORAN → Laporan Penjualan
- **Fitur**:
  - Tampilkan Kode Transaksi, Tanggal, Petugas, Detail Belanjaan, Total Bayar
  - Total Penghasilan per Tanggal
  - Filter: Nama Produk, Rentang Tanggal, Sortir (Terbaru/Terlama)
  - Cetak PDF
- **Route**: `/laporan/penjualan` (view) dan `/laporan/penjualan/pdf` (PDF)

### 5. ✅ Laporan Barang Masuk & Keluar (Diperbaharui)
- **Akses**: Pemilik ONLY
- **Filter Baru**:
  - Cari Nama Produk
  - Rentang Tanggal
  - Sortir (Terbaru/Terlama)
  - Cetak PDF
- **Routes**: 
  - `/laporan/masuk` dan `/laporan/masuk/pdf`
  - `/laporan/keluar` dan `/laporan/keluar/pdf`

### 6. ✅ Log Aktivitas Mutasi (Renamed)
- **Akses**: Pemilik ONLY
- **Nama Baru**: "Lihat Aktivitas Mutasi" (sebelumnya: "Log Aktivitas")
- **Lokasi Menu**: MUTASI → Lihat Aktivitas Mutasi
- **Route**: `/lihat-aktivitas-mutasi`

## 🚀 LANGKAH-LANGKAH IMPLEMENTASI

### Step 1: Update Database (PENTING!)
```bash
php artisan migrate
```

Ini akan membuat tabel `pengeluaran_gudang` dan menambahkan kolom `kode_transaksi` ke tabel `penjualans`.

### Step 2: Clear Cache
```bash
php artisan cache:clear
php artisan config:cache
php artisan view:clear
php artisan route:clear
```

### Step 3: Test Sistem
1. Login sebagai **Pemilik**
2. Buka menu **MUTASI** → **Pengeluaran Gudang** → Coba buat pengeluaran gudang
3. Buka menu **MUTASI** → **Pengelolaan Stok Etalase** → Coba update stok etalase
4. Buka menu **TRANSAKSI** → **Mesin Kasir** → Lakukan transaksi penjualan
5. Buka menu **LAPORAN** → **Laporan Penjualan** → Lihat laporan dengan filter
6. Login sebagai **Admin** → Verifikasi bahwa Admin tidak bisa akses Pengeluaran Gudang & Pengelolaan Stok Etalase

## 📊 STRUKTUR MENU BARU

```
Dashboard

TRANSAKSI
  🛒 Mesin Kasir (Pemilik+Admin)

DATABASE
  🏷️ Kategori Barang
  📦 Data Jajanan

MUTASI
  ➕ Barang Masuk (All)
  🏭 Alokasi Gudang→Etalase (Pemilik+Admin)
  📦 Pengelolaan Stok Etalase (Pemilik only)
  🏭 Pengeluaran Gudang (Pemilik only)
  📋 Lihat Aktivitas Mutasi (Pemilik only)

LAPORAN (Pemilik only)
  💰 Laporan Penjualan
  📄 Laporan Barang Masuk
  📄 Laporan Barang Keluar

ADMIN (Pemilik only)
  👤 Tambah Pengguna
```

## 🔐 AUTHORIZATION MATRIX

| Fitur | Pemilik | Admin | Karyawan |
|-------|---------|-------|----------|
| Barang Masuk | ✅ | ✅ | ❌ |
| Alokasi Gudang→Etalase | ✅ | ✅ | ❌ |
| Pengelolaan Stok Etalase | ✅ | ❌ | ❌ |
| Pengeluaran Gudang | ✅ | ❌ | ❌ |
| Mesin Kasir (Penjualan) | ✅ | ✅ | ❌ |
| Laporan Penjualan | ✅ | ❌ | ❌ |
| Laporan Barang Masuk | ✅ | ❌ | ❌ |
| Laporan Barang Keluar | ✅ | ❌ | ❌ |
| Lihat Aktivitas Mutasi | ✅ | ❌ | ❌ |
| Tambah Pengguna | ✅ | ❌ | ❌ |

## 📱 UI COMPATIBILITY

Semua fitur baru telah didesain responsive:
- ✅ Desktop (Laptop/Tablet landscape) - Table view
- ✅ Mobile (Smartphone) - Card view
- ✅ Barcode scanner support
- ✅ PDF export untuk semua laporan

## 🐛 TROUBLESHOOTING

### Error: Table not found - pengeluaran_gudang
**Solusi**: Jalankan `php artisan migrate`

### Error: Column not found - kode_transaksi
**Solusi**: Jalankan `php artisan migrate`

### Menu tidak tampil sesuai harapan
**Solusi**: 
1. Clear cache: `php artisan cache:clear`
2. Clear view: `php artisan view:clear`
3. Refresh browser (Ctrl+F5 atau Cmd+Shift+R)

### Fitur masih menampilkan menu lama
**Solusi**: Jalankan `php artisan route:clear` dan `php artisan view:clear`

### Admin masih bisa akses fitur Pemilik
**Solusi**: Pastikan sudah run migrations dan clear cache. Role check dilakukan di routes dengan middleware `role:Pemilik`.

## 📝 DATABASE CHANGES DETAIL

### Tabel Baru: `pengeluaran_gudang`
```sql
- id_pengeluaran_gudang (Primary Key)
- id_makanan (FK ke makanan)
- id_pengguna (FK ke pengguna)
- jumlah_keluar (Integer)
- stok_gudang_sebelum (Integer)
- stok_gudang_sesudah (Integer)
- alasan (ENUM: expired, tikus, rusak, lainnya)
- keterangan (Text, nullable)
- tgl_pengeluaran (Date)
- barcode (String, nullable)
- timestamps
```

### Kolom Baru: `penjualans.kode_transaksi`
```sql
- kode_transaksi (String, unique, nullable)
```

Format: `TRX-YYYYMMDD-NNN` (contoh: TRX-20260430-001)

## ✨ NEXT IMPROVEMENTS (OPTIONAL)

Untuk rilis berikutnya, pertimbangkan:
1. Notifikasi untuk stok rendah
2. Export data ke Excel
3. Dashboard analytics yang lebih detail
4. QR code generation untuk produk
5. Integration dengan sistem pembayaran online
6. Mobile app native

## 📞 SUPPORT

Jika ada pertanyaan atau masalah, silakan hubungi developer!

---
Last Updated: April 30, 2026
Version: 1.0
