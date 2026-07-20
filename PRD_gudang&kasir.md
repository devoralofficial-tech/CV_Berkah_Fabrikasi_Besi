# Spesifikasi Modul ERP: Gudang & Kasir
**Proyek:** CV BERKAH
**Versi:** 1.1
**Dokumen ini melengkapi:** PRD Umum v4.1 (`PRD_CV_BERKAH_general_v4.1.md`)
**Fokus:** Detail fungsional, alur bisnis, dan skema database untuk modul Gudang (Inventory) & Kasir (POS)

**Changelog v1.0 → v1.1:** Tabel `categories` diperbarui menjadi self-referencing (`parent_id`) untuk mendukung struktur **2 tingkat (induk & anak)** — lihat Bagian 6. Tidak ada perubahan pada alur gudang/kasir itu sendiri; produk tetap satu baris per varian (misal tiap ukuran pipa stainless adalah produk terpisah), hanya cara pengelompokannya via kategori yang kini berjenjang.

---

## 1. Ringkasan Modul

Di PRD utama, admin berperan ganda sebagai **kasir** dan **pengelola gudang**. Dokumen ini merinci dua sisi tersebut menjadi satu sistem yang terintegrasi:

- **Modul Gudang** — mengelola stok: barang masuk (restock dari supplier), barang keluar, penyesuaian stok (opname), dan riwayat pergerakan barang (kartu stok).
- **Modul Kasir** — mencatat transaksi penjualan, baik yang berasal dari **order online** (via website → WA) maupun **transaksi walk-in** (pembeli datang langsung ke gudang/toko dan dilayani admin secara manual).

Kedua modul ini **terhubung otomatis**: setiap transaksi penjualan (dari kasir manapun) akan mengurangi stok dan tercatat sebagai log gudang, sehingga data stok selalu akurat dan laporan selalu konsisten.

---

## 2. Aktor & Skenario Penggunaan

| Skenario | Aktor | Alur Singkat |
|---|---|---|
| Restock barang dari supplier | Admin (Gudang) | Input Barang Masuk → stok bertambah |
| Pembeli order via website | User (publik) | Checkout → order tersimpan (status *pending*) → admin konfirmasi via WA |
| Konfirmasi order online jadi penjualan | Admin (Kasir) | Admin ubah status order → *Selesai* → sistem otomatis buat transaksi penjualan & kurangi stok |
| Pembeli datang langsung (walk-in) | Admin (Kasir) | Admin buka menu "Transaksi Baru" → pilih produk manual → input qty → bayar → cetak nota |
| Barang rusak/hilang | Admin (Gudang) | Input Barang Keluar dengan alasan "Rusak/Retur/Susut" → stok berkurang tanpa transaksi penjualan |
| Cek fisik stok gudang | Admin (Gudang) | Stock Opname → sistem bandingkan stok sistem vs stok fisik → otomatis buat penyesuaian jika beda |
| Lihat laba/rugi | Admin (Owner) | Buka Laporan → filter tanggal → lihat omzet, HPP, margin |
| Barang jenis baru datang pertama kali *(baru v1.1)* | Admin (Gudang/Produk) | Buat produk baru → pilih kategori anak yang sesuai (buat kategori anak baru dulu jika belum ada) → isi stok awal → tercatat otomatis sebagai `inventory_log` pertama |

---

## 3. Modul Gudang (Warehouse Management)

### 3.1 Master Data
Sudah didefinisikan di PRD utama (`categories`, `products`) — field kunci untuk gudang:
- `unit` (pcs / kg / m)
- `stock` (stok berjalan, auto-update, tidak diedit manual langsung)
- `cost_price` (harga beli/HPP terakhir — dipakai untuk hitung margin)
- `low_stock_threshold` (batas stok minimum, per produk)
- `category_id` — **mengacu ke kategori anak** (bukan induk), sesuai struktur kategori 2 tingkat di PRD Umum Bagian 6 *(baru v1.1)*

> Catatan: kolom `stock` di tabel `products` sebaiknya **read-only bagi admin** dan hanya berubah melalui transaksi (IN/OUT/opname) — ini mencegah admin sengaja/tidak sengaja mengubah stok tanpa jejak audit.

> **Catatan varian produk** *(baru v1.1)*: item dengan variasi ukuran/jenis (misal "Pipa Stainless 3x2", "2x1/2", "2x4") **bukan** satu produk dengan banyak varian dalam satu baris — melainkan **produk-produk terpisah**, masing-masing dengan `stock`, `cost_price`, `sell_price`, dan `low_stock_threshold` sendiri-sendiri, dikelompokkan di bawah kategori anak masing-masing (yang biasanya bernama sama dengan produknya, di bawah kategori induk seperti "Pipa Stainless"). Ini penting agar Kartu Stok (3.5) dan seluruh logika stok di bawah tetap presisi per ukuran.

### 3.2 Barang Masuk (Stock In)
Fungsi: mencatat penambahan stok, misal restock dari supplier.

**Field input:**
- Produk (pilih dari master)
- Jumlah masuk (qty, mendukung desimal untuk kg/m)
- Harga beli per satuan (opsional, untuk update `cost_price`)
- Sumber/Supplier (teks bebas, opsional — lihat 3.7 untuk versi lanjutan)
- Catatan (contoh: "Restock dari Bengkel Jaya")
- Tanggal transaksi

**Efek sistem:**
- `products.stock += qty`
- Insert ke `inventory_logs` dengan `type = in`
- Jika harga beli diisi, update `products.cost_price`

**Catatan untuk barang jenis baru (belum pernah ada di sistem)** *(baru v1.1)*: jika barang yang datang adalah **jenis/ukuran yang belum pernah dijual sebelumnya**, admin tidak memakai form Barang Masuk ini — melainkan **membuat produk baru** dulu di menu Produk (lengkap dengan kategori anak yang sesuai, buat kategori anak baru jika perlu), lalu mengisi field "Stok Awal" saat create produk. Stok awal ini otomatis tercatat sebagai `inventory_log` pertama (`type = in`, `source = initial`), sehingga tetap ada jejak audit sejak hari pertama produk itu ada. Form Barang Masuk (3.2) khusus dipakai untuk **restock produk yang sudah terdaftar**.

### 3.3 Barang Keluar (Stock Out)
Ada 2 sumber barang keluar:
1. **Otomatis dari transaksi penjualan** (kasir/order online) — dijelaskan di Bagian 5.
2. **Manual oleh admin gudang** — untuk kasus non-penjualan: barang rusak, susut, hilang, sample, atau koreksi.

**Field input (manual):**
- Produk
- Jumlah keluar
- Alasan (dropdown: Rusak / Susut / Hilang / Sample / Lainnya)
- Catatan
- Tanggal

**Efek sistem:**
- `products.stock -= qty` (validasi: tidak boleh membuat stok jadi negatif — sistem tolak jika qty > stok tersedia)
- Insert ke `inventory_logs` dengan `type = out`, `source = manual`

### 3.4 Stock Opname (Penyesuaian Stok Fisik)
Fitur untuk mencocokkan stok sistem dengan stok fisik di gudang (rutin, misal bulanan).

**Alur:**
1. Admin buka menu Stock Opname → sistem menampilkan daftar semua produk dengan stok sistem saat ini (bisa difilter per kategori induk/anak untuk mempermudah opname bertahap per area gudang)
2. Admin input **stok fisik hasil hitung manual** per produk
3. Sistem menghitung selisih (`stok fisik - stok sistem`)
4. Jika ada selisih, sistem otomatis membuat `inventory_log` penyesuaian (`type = in` jika lebih, `type = out` jika kurang, dengan `source = opname`)
5. Stok produk diupdate ke angka fisik yang benar

**Field tabel opname:**
- Sesi opname (tanggal, dilakukan oleh siapa)
- Detail per produk: stok sistem, stok fisik, selisih, catatan

### 3.5 Kartu Stok (Stock Card)
Halaman detail per produk yang menampilkan **riwayat lengkap pergerakan barang** — gabungan dari IN, OUT (penjualan & manual), dan opname, diurutkan kronologis dengan running balance (saldo stok berjalan). Ini adalah fitur audit trail penting untuk mini ERP.

> Karena setiap ukuran/varian adalah produk terpisah (lihat 3.1), Kartu Stok selalu spesifik per produk individual — misal Kartu Stok "Pipa Stainless 3x2" terpisah sepenuhnya dari Kartu Stok "Pipa Stainless 2x1/2", meski keduanya satu kategori induk yang sama.

### 3.6 Alert Stok Menipis
- Ditampilkan di dashboard admin (badge/notifikasi) dan juga **realtime di halaman publik** (status "Stok Menipis"/"Habis" pada produk)
- Logika: `stock <= low_stock_threshold` → status "Menipis"; `stock <= 0` → status "Habis" (tombol beli otomatis nonaktif di halaman publik)

### 3.7 (Opsional/Future) Manajemen Supplier
Untuk versi awal, cukup field teks bebas "Sumber" di Barang Masuk. Jika ke depan supplier sering berulang, bisa ditambahkan tabel `suppliers` (id, name, phone, address) — dicatat sebagai enhancement, tidak wajib di fase 1 agar tidak menambah kompleksitas.

---

## 4. Modul Kasir (Point of Sale)

### 4.1 Transaksi dari Order Online
Saat user checkout di website, data masuk ke `orders` (status `pending`). Admin membuka **Manajemen Pesanan**, menghubungi pembeli via WA untuk konfirmasi, lalu:

- Jika pembeli jadi membeli → admin ubah status order jadi **`completed`** → sistem otomatis:
  - Membuat record `sales` (transaksi) berdasarkan data `order` & `order_items`
  - Mengurangi stok tiap produk sesuai qty
  - Mencatat `inventory_logs` (`type = out`, `source = sale`, terhubung ke `sale_id`)
- Jika batal → admin ubah status jadi **`cancelled`** → tidak ada perubahan stok sama sekali (karena stok belum pernah dikurangi saat checkout, hanya saat *completed*)

### 4.2 Transaksi Walk-in (Kasir Manual)
Untuk pembeli yang datang langsung tanpa order online. Admin bertindak sebagai kasir di dashboard:

**Alur input:**
1. Admin buka menu **"Transaksi Baru"**
2. Pilih produk satu per satu (dengan pencarian cepat — search bisa mengetik nama produk spesifik seperti "3x2" langsung, karena tiap ukuran adalah produk sendiri), input qty per item (mendukung desimal)
3. Sistem otomatis hitung subtotal per item & total keseluruhan (harga diambil dari `sell_price` produk saat itu, disimpan sebagai snapshot)
4. Admin input nama pembeli (opsional, untuk transaksi walk-in bisa dikosongkan/"Umum")
5. Input metode pembayaran (Cash/Transfer) dan jumlah dibayar → sistem hitung kembalian otomatis (jika cash)
6. Simpan transaksi → sistem cek validasi stok cukup untuk semua item
7. Sistem otomatis:
   - Insert ke `sales` & `sale_items`
   - Kurangi `products.stock`
   - Insert `inventory_logs` (`type = out`, `source = sale`)
8. Tampilkan nota transaksi (bisa dicetak/didownload sebagai PDF sederhana atau cukup ditampilkan di layar)

### 4.3 Pembayaran & Kembalian
- Field: `total`, `amount_paid`, `change` (dihitung otomatis: `amount_paid - total`)
- Untuk pembayaran transfer, `change` tidak relevan (set 0), cukup catat metode

### 4.4 Void / Pembatalan Transaksi
- Transaksi yang sudah tersimpan dapat di-**void** oleh admin (misal salah input)
- Efek: status transaksi jadi `voided`, dan sistem otomatis **mengembalikan stok** (insert `inventory_logs` type `in`, `source = void`) agar data tetap konsisten dan tidak perlu edit manual
- Riwayat void tetap tersimpan (tidak dihapus) untuk audit

### 4.5 Cetak Nota (Struk Sederhana)
- Tidak perlu printer thermal khusus di versi awal — cukup **tampilan nota berbasis web (print-friendly CSS)** yang bisa di-print ke printer biasa atau disimpan sebagai PDF via `Ctrl+P`
- Menghindari kebutuhan library PDF tambahan yang membebani server (`window.print()` dari browser sudah cukup dan gratis)

---

## 5. Integrasi Gudang ↔ Kasir (Alur Otomatis)

```
Order Online (pending) ──[admin konfirmasi]──► Sales (completed) ──► Stock -qty ──► Inventory Log (OUT, source: sale)
Transaksi Walk-in ─────────────────────────────► Sales (completed) ──► Stock -qty ──► Inventory Log (OUT, source: sale)
Barang Masuk (manual, produk existing) ─────────────────────────────► Stock +qty ──► Inventory Log (IN, source: purchase)
Produk Baru Dibuat (stok awal) ──────────────────────────────────────► Stock +qty ──► Inventory Log (IN, source: initial)
Barang Keluar (manual) ─────────────────────────────────────────────► Stock -qty ──► Inventory Log (OUT, source: manual)
Stock Opname (selisih) ─────────────────────────────────────────────► Stock ± ──► Inventory Log (IN/OUT, source: opname)
Void Transaksi ──────────────────────────────────────────────────────► Stock +qty ──► Inventory Log (IN, source: void)
```

**Prinsip kunci:** `products.stock` **tidak pernah diedit langsung** — selalu melalui salah satu jalur di atas. Ini menjamin `inventory_logs` selalu jadi *single source of truth* untuk audit dan laporan.

---

## 6. Skema Database Lengkap (Gudang & Kasir)

| Tabel | Fungsi | Field Utama |
|---|---|---|
| `categories` | Kategori produk, **self-referencing 2 tingkat** *(diperbarui v1.1)* | id, name, slug, **parent_id (nullable, FK ke categories.id — null = kategori induk, terisi = kategori anak)** |
| `products` | Master barang | id, category_id (mengacu ke kategori **anak**), name, unit, cost_price, sell_price, stock, low_stock_threshold, image, deleted_at |
| `orders` | Order dari website | id, order_number, customer_name, customer_phone, status (pending/contacted/completed/cancelled), total_estimate |
| `order_items` | Detail item order | id, order_id, product_id, qty, unit_price_snapshot, subtotal |
| **`sales`** *(baru)* | Transaksi penjualan (kasir & order) | id, sale_number, order_id (nullable), source (online/walk-in), customer_name, total, payment_method, amount_paid, change, status (completed/voided), created_by, created_at |
| **`sale_items`** *(baru)* | Detail item penjualan | id, sale_id, product_id, qty, unit_price_snapshot, subtotal |
| `inventory_logs` | Kartu stok / audit trail | id, product_id, type (in/out), qty, source (purchase/initial/manual/sale/opname/void), reference_id (id sales/opname terkait), note, created_by, created_at |
| **`stock_opnames`** *(baru)* | Sesi stock opname | id, opname_date, note, created_by |
| **`stock_opname_items`** *(baru)* | Detail per produk saat opname | id, opname_id, product_id, system_stock, physical_stock, difference |

**Relasi penting:**
- `sales.order_id` → `orders.id` (nullable; diisi jika transaksi berasal dari order online, kosong jika walk-in)
- `products.category_id` → `categories.id`, **wajib mengarah ke kategori anak** (leaf), bukan induk *(baru v1.1)*
- `categories.parent_id` → `categories.id` (self-referencing; null = induk, terisi = anak). Kategori dengan `parent_id` terisi **tidak boleh** menjadi induk bagi kategori lain (aturan dijaga di level aplikasi) *(baru v1.1)*
- `inventory_logs.reference_id` bersifat polymorphic sederhana (disimpan bersama `source` untuk menentukan tabel asal) — alternatif lebih rapi: gunakan `morphs()` Laravel (`reference_type`, `reference_id`) jika ingin lebih standar

---

## 7. Business Rules & Validasi

1. **Stok tidak boleh negatif** — semua transaksi OUT divalidasi di server terhadap stok tersedia sebelum disimpan.
2. **Qty desimal** — kolom qty menggunakan tipe `decimal(10,2)` agar mendukung kg/m (misal 10.5 kg), sementara untuk satuan `pcs` sebaiknya divalidasi di level form (JS + server) agar tidak menerima desimal.
3. **Snapshot harga** — `unit_price_snapshot` di `order_items`/`sale_items` **wajib** diisi dari harga produk saat transaksi terjadi, bukan mengambil harga produk secara live saat laporan dibuka — agar laporan historis tetap akurat walau harga berubah.
4. **Transaksi = database transaction** — setiap proses yang menyentuh lebih dari satu tabel (misal: buat sale + update stock + insert log) **wajib dibungkus** dalam `DB::transaction()` di Laravel, agar tidak terjadi data setengah jalan jika terjadi error di tengah proses.
5. **Void bukan delete** — transaksi yang dibatalkan tidak dihapus dari database, hanya diubah statusnya, demi jejak audit.
6. **Satu sumber kebenaran stok** — dilarang ada fitur "edit stok langsung" di form produk; perubahan stok hanya lewat transaksi (lihat Bagian 5).
7. **Kategori maksimal 2 tingkat** *(baru v1.1)* — kategori anak tidak boleh punya anak lagi; produk hanya boleh terhubung ke kategori anak, tidak langsung ke kategori induk, agar pengelompokan stok & laporan tetap presisi per jenis/ukuran.

---

## 8. Struktur Menu Dashboard Admin (Disarankan)

```
Dashboard
├── Ringkasan (total produk, stok menipis, order pending, omzet hari ini)
├── Produk
│   ├── Kategori (induk & anak)
│   └── Daftar Produk (CRUD)
├── Gudang
│   ├── Barang Masuk
│   ├── Barang Keluar
│   ├── Stock Opname
│   └── Kartu Stok (per produk)
├── Kasir
│   ├── Transaksi Baru (walk-in)
│   └── Riwayat Transaksi
├── Pesanan Online
│   └── Daftar Order (pending/contacted/completed/cancelled)
├── Laporan
│   ├── Laporan Penjualan (harian/bulanan/tahunan)
│   ├── Laporan Stok (masuk/keluar)
│   └── Laporan Laba Rugi Sederhana
└── Pengaturan
    └── Info Perusahaan (WA, alamat, dsb)
```

---

## 9. Laporan Spesifik Gudang & Kasir

| Laporan | Isi | Filter |
|---|---|---|
| Laporan Penjualan | Total transaksi, omzet, item terlaris, sumber (online vs walk-in) | Harian/Bulanan/Tahunan/Custom, bisa juga per kategori induk/anak |
| Laporan Stok Masuk-Keluar | Rekap semua `inventory_logs` per produk | Per produk, per tanggal, per kategori |
| Kartu Stok | Riwayat lengkap 1 produk dengan saldo berjalan | Per produk |
| Laba Rugi Sederhana | Omzet − Total HPP (dari `cost_price` × qty terjual) = margin kotor | Harian/Bulanan/Tahunan |
| Stok Menipis | Daftar produk di bawah threshold, untuk keputusan restock | Realtime |

Semua laporan tetap **dapat diexport ke Excel** sesuai requirement awal (Laravel Excel/Maatwebsite) — konsisten dengan PRD utama, tidak menambah dependensi baru.

---

## 10. Konsistensi dengan Strategi Biaya (PRD Umum Bagian 8)

Modul ini **tidak menambah kebutuhan infrastruktur baru**, termasuk setelah perubahan struktur kategori v1.1 (self-referencing category tetap 1 tabel, tidak butuh tabel/service tambahan):
- Tidak butuh printer/hardware POS khusus (nota via print browser)
- Tidak butuh Redis/queue (semua proses sale/stock berjalan synchronous, cukup ringan untuk skala 500–1.000 pengunjung/bulan)
- Tidak butuh storage tambahan besar (tidak ada upload nota/dokumen fisik)
- Tetap berjalan penuh di **shared hosting** yang sudah direkomendasikan sebelumnya

---

## 11. Urutan Pengembangan Modul Ini (Disarankan)

1. Migration & Model: `sales`, `sale_items`, `stock_opnames`, `stock_opname_items` (tambahan dari skema PRD utama), termasuk migration `categories.parent_id`
2. Barang Masuk & Barang Keluar (manual) — fondasi paling dasar
3. Kartu Stok (agar bisa langsung diverifikasi setiap transaksi berikutnya)
4. Transaksi Kasir Walk-in (termasuk validasi stok, snapshot harga, kembalian)
5. Integrasi Order Online → Sales otomatis saat status `completed`
6. Void transaksi + reversal stok
7. Stock Opname
8. Laporan (penjualan, stok, laba rugi) + export Excel
9. Dashboard ringkasan & alert stok menipis