# PDF Reports & Export Functionality - Sistema Inventori Alwi

## Overview
The application uses **DomPDF (via Barryvdh\DomPDF)** for PDF generation. All reports are generated from Blade templates and include filtering capabilities (date range, product name).

---

## 📋 PDF Library Configuration

**Package**: `barryvdh/laravel-dompdf: ^3.1`
- Location: [composer.json](composer.json)
- Facade: `Barryvdh\DomPDF\Facade\Pdf`
- Main Controller: [app/Http/Controllers/LaporanController.php](app/Http/Controllers/LaporanController.php)

---

## 📊 PDF Reports Available

### 1. **LAPORAN PENJUALAN** (Sales Report)
**Purpose**: Track all sales transactions with details

**Controller**: [LaporanController.php](app/Http/Controllers/LaporanController.php)
- **View Method**: `laporanPenjualan()` - Lines 120-151
- **PDF Method**: `cetakLaporanPenjualan()` - Lines 153-209

**Routes**:
- View: `/laporan/penjualan` → `laporan.penjualan`
- PDF: `/laporan/penjualan/pdf` → `laporan.penjualan.pdf`

**Blade Templates**:
- View: [resources/views/laporan/penjualan.blade.php](resources/views/laporan/penjualan.blade.php)
- PDF: [resources/views/laporan/penjualan_pdf.blade.php](resources/views/laporan/penjualan_pdf.blade.php)

**Filters Supported**:
- Date range: `start_date` to `end_date`
- Product name: `nama_produk`
- Sort: `sort` (terbaru/terlama - newest/oldest)

**Data Displayed**:
- Transaction number (No. Nota)
- Date & time
- Cashier name (Kasir)
- Product details with quantities
- Total amount per transaction
- **Grand Total Revenue** (total_pendapatan)

**Output File**: `Laporan_Penjualan.pdf`

---

### 2. **LAPORAN BARANG MASUK** (Incoming Goods Report)
**Purpose**: Track inventory receipts/incoming stock

**Controller**: [LaporanController.php](app/Http/Controllers/LaporanController.php)
- **View Method**: `mutasiMasuk()` - Lines 14-42
- **PDF Method**: `cetakMutasiMasuk()` - Lines 44-64

**Routes**:
- View: `/laporan/masuk` → `laporan.masuk`
- PDF: `/laporan/masuk/pdf` → `laporan.masuk.pdf`

**Blade Templates**:
- View: [resources/views/laporan/masuk.blade.php](resources/views/laporan/masuk.blade.php)
- PDF: [resources/views/laporan/masuk_pdf.blade.php](resources/views/laporan/masuk_pdf.blade.php)

**Filters Supported**:
- Date range: `start_date` to `end_date`
- Product name: `nama_produk`
- Sort: `sort` (terbaru/terlama - newest/oldest)

**Data Displayed**:
- Entry number
- Date (Tanggal)
- Product name (Nama Jajanan)
- Input person/user (Penginput)
- Quantity received (Jumlah Masuk) - shown as "+X Pcs"

**Model**: [MutasiMasuk](app/Models/MutasiMasuk.php)

**Output File**: `Laporan_Barang_Masuk.pdf`

---

### 3. **LAPORAN BARANG KELUAR** (Outgoing Goods Report)
**Purpose**: Track inventory deductions/outgoing stock

**Controller**: [LaporanController.php](app/Http/Controllers/LaporanController.php)
- **View Method**: `mutasiKeluar()` - Lines 66-94
- **PDF Method**: `cetakMutasiKeluar()` - Lines 96-118

**Routes**:
- View: `/laporan/keluar` → `laporan.keluar`
- PDF: `/laporan/keluar/pdf` → `laporan.keluar.pdf`

**Blade Templates**:
- View: [resources/views/laporan/keluar.blade.php](resources/views/laporan/keluar.blade.php)
- PDF: [resources/views/laporan/keluar_pdf.blade.php](resources/views/laporan/keluar_pdf.blade.php)

**Filters Supported**:
- Date range: `start_date` to `end_date`
- Product name: `nama_produk`
- Sort: `sort` (terbaru/terlama - newest/oldest)

**Data Displayed**:
- Entry number
- Date (Tanggal)
- Product name (Nama Jajanan)
- Deducted by/user (Pengeksekusi)
- Quantity removed (Jumlah Keluar) - shown as "-X Pcs"

**Model**: [MutasiKeluar](app/Models/MutasiKeluar.php)

**Output File**: `Laporan_Barang_Keluar.pdf`

---

### 4. **LAPORAN PENGELUARAN GUDANG** (Warehouse Expenditure Report)
**Purpose**: Track warehouse expenses/expenditures

**Controller**: [LaporanController.php](app/Http/Controllers/LaporanController.php)
- **View Method**: `pengeluaranGudang()` - Lines 188-208
- **PDF Method**: `cetakPengeluaranGudang()` - Lines 210-250

**Routes**:
- View: `/laporan/pengeluaran-gudang` → `laporan.pengeluaran_gudang`
- PDF: `/laporan/pengeluaran-gudang/pdf` → `laporan.pengeluaran_gudang.pdf`

**Blade Templates**:
- View: [resources/views/laporan/pengeluaran_gudang.blade.php](resources/views/laporan/pengeluaran_gudang.blade.php)
- PDF: [resources/views/laporan/pengeluaran_gudang_pdf.blade.php](resources/views/laporan/pengeluaran_gudang_pdf.blade.php)

**Filters Supported**:
- Date range: `start_date` to `end_date`
- Product name: `nama_produk`
- Sort: `sort` (terbaru/terlama - newest/oldest)

**Data Displayed**:
- Entry number
- Date (Tgl Pengeluaran)
- Product name (Nama Jajanan)
- User/handler (Pengguna)
- Quantity expenditure (Jumlah Keluar)

**Model**: [PengeluaranGudang](app/Models/PengeluaranGudang.php)

**Output File**: `Laporan_Pengeluaran_Gudang.pdf`

---

## 🔗 Alternative Sales Report

**Legacy/Alternative Sales Report** (also uses PenjualanController):

**Controller**: [PenjualanController.php](app/Http/Controllers/PenjualanController.php)
- **PDF Method**: `cetakPdf()` - Lines 44-58

**Route**:
- PDF Only: `/penjualan/cetak-pdf` → `penjualan.cetak.pdf`

**Template**: 
- PDF: [resources/views/laporan/penjualan_pdf.blade.php](resources/views/laporan/penjualan_pdf.blade.php)

**Filters**:
- Start date: `tgl_awal`
- End date: `tgl_akhir`

**Notes**: This is an older/alternative implementation that uses different parameter names.

---

## 📁 File Structure Summary

### Controllers
```
app/Http/Controllers/
├── LaporanController.php           # Main report controller (4 PDF methods)
└── PenjualanController.php         # Alternative sales PDF (1 method)
```

### Blade Templates (PDF)
```
resources/views/laporan/
├── penjualan_pdf.blade.php         # Sales report PDF template
├── masuk_pdf.blade.php             # Incoming goods report template
├── keluar_pdf.blade.php            # Outgoing goods report template
└── pengeluaran_gudang_pdf.blade.php # Warehouse expenditure report template
```

### Blade Templates (Views)
```
resources/views/laporan/
├── penjualan.blade.php             # Sales report view page
├── masuk.blade.php                 # Incoming goods view page
├── keluar.blade.php                # Outgoing goods view page
└── pengeluaran_gudang.blade.php   # Warehouse expenditure view page
```

### Routes
- [routes/web.php](routes/web.php) - Lines 73-89 (all report routes)

---

## 🔐 Access Control
All reports are restricted to **Pemilik (Owner/Admin)** role via middleware:
- Route group: `Route::middleware(['auth', 'admin.pemilik'])->group(...)`
- Protected: All 4 main reports + legacy sales PDF

---

## 🎯 Common Features Across All Reports

1. **Date Range Filtering**: All reports support `start_date` and `end_date` parameters
2. **Product Name Filtering**: `nama_produk` parameter to search by product name
3. **Sorting**: `sort` parameter (terbaru/terlama)
4. **PDF Download**: Uses DomPDF `stream()` method to directly download PDF
5. **Print Styling**: PDFs include `@media print` CSS for optimal print output
6. **Auto-Print**: Most PDFs include `onload="window.print()"` for immediate printing

---

## 📊 Data Models Involved

| Report | Primary Model | Related Models |
|--------|---------------|----------------|
| Penjualan | `Penjualan` | `DetailPenjualan`, `Makanan`, `User` |
| Barang Masuk | `MutasiMasuk` | `Makanan`, `User` |
| Barang Keluar | `MutasiKeluar` | `Makanan`, `User` |
| Pengeluaran Gudang | `PengeluaranGudang` | `Makanan`, `User` |

---

## 🚀 How to Generate PDF Reports

### Via Web UI
1. Navigate to menu: **LAPORAN** (Reports)
2. Select report type (Penjualan, Barang Masuk, Barang Keluar, Pengeluaran Gudang)
3. Apply filters (date range, product name, sort)
4. Click "Cetak PDF" (Print PDF) button

### Via Direct URL
```
/laporan/penjualan/pdf?start_date=2025-01-01&end_date=2025-12-31
/laporan/masuk/pdf?start_date=2025-01-01&end_date=2025-12-31
/laporan/keluar/pdf?start_date=2025-01-01&end_date=2025-12-31
/laporan/pengeluaran-gudang/pdf?start_date=2025-01-01&end_date=2025-12-31
```

---

## 📝 Implementation Documentation
See [PANDUAN_IMPLEMENTASI.md](PANDUAN_IMPLEMENTASI.md) for additional details on:
- Section 4: Laporan Penjualan (Baru)
- Section 5: Laporan Barang Masuk & Keluar (Diperbaharui)
- Implementation guide and usage instructions
