# Product Requirements Document (PRD)
**Modul:** Panel Admin / Dashboard Administrasi
**Proyek:** CV BERKAH — Website Company Profile & Mini ERP Gudang-Kasir
**Versi:** 1.1
**Tanggal:** Juli 2026
**Dokumen ini merangkum & merinci:** PRD Umum v4 + Spesifikasi Modul Gudang & Kasir — difokuskan khusus pada sisi Admin
**Platform:** Web Application (Laravel 11.x, Blade + Tailwind + Alpine.js)

**Changelog v1.1:** Menambahkan struktur kategori 2 tingkat (induk & anak) — lihat Bagian 7.1. Produk tetap satu-satu (setiap ukuran/varian seperti "Pipa Stainless 3x2" adalah produk terpisah dengan stok & harga sendiri, bukan varian dalam satu produk).

---

## 1. Latar Belakang & Tujuan

Panel Admin adalah **satu-satunya sisi tertutup (butuh login)** dari sistem CV BERKAH. Semua operasional bisnis harian — kelola produk, kelola stok, kasir, follow-up pesanan online, dan pelaporan — dilakukan dari sini. Karena tim CV BERKAH masih kecil, seluruh fungsi ini dipegang oleh **satu role: Admin** (merangkap kasir, pengelola gudang, dan pengelola web).

**Tujuan dokumen ini:**
- Menjabarkan seluruh fitur admin secara lengkap dan siap untuk dieksekusi tim development
- Menyatukan requirement dari PRD Umum (settings, produk, laporan) dan PRD Gudang & Kasir (inventory, POS, opname) ke dalam satu peta modul admin yang koheren
- Menjadi acuan untuk desain UI dashboard, struktur menu, alur kerja, dan skema database sisi admin

---

## 2. Ruang Lingkup

**Termasuk dalam dokumen ini:**
- Autentikasi & keamanan akses admin
- Dashboard ringkasan (overview)
- Manajemen Pengaturan Website (Settings)
- Manajemen Kategori & Produk
- Manajemen Gudang (Barang Masuk, Barang Keluar, Stock Opname, Kartu Stok)
- Manajemen Kasir/POS (Transaksi Walk-in, Riwayat, Void)
- Manajemen Pesanan Online (Order Management)
- Sistem Laporan & Export
- Struktur menu, hak akses, dan alur kerja admin end-to-end

**Tidak termasuk (dibahas di PRD Umum, sisi publik):**
- Halaman Home, Product (katalog publik), About Us
- Alur checkout & keranjang sisi pengunjung

---

## 3. Role & Akses

| Role | Deskripsi | Hak Akses |
|---|---|---|
| **Admin** | Role tunggal, merangkap Owner/Kasir/Gudang | Akses penuh ke seluruh modul dashboard |

> Di fase 1 sengaja **tidak ada level permission bertingkat** (misal Owner vs Staff Gudang) agar sistem tetap sederhana. Ini dicatat sebagai *future enhancement* (lihat Bagian 12) jika tim bertambah — struktur database dan UI harus dirancang agar mudah ditambahkan role baru di kemudian hari tanpa refactor besar (gunakan `users.role` sebagai kolom, bukan hardcode).

### 3.1 Autentikasi
- Menggunakan **Laravel Breeze** (ringan, sesuai prinsip hemat resource di PRD Umum)
- Login dengan email + password
- **Rate limiting/throttle** pada halaman login (mencegah brute force) — misal maks 5 percobaan/menit
- Session timeout otomatis setelah periode tidak aktif tertentu (disarankan 60–120 menit, dapat dikonfigurasi)
- Fitur "Lupa Password" via email (menggunakan mail driver bawaan hosting, tanpa layanan pihak ketiga berbayar)
- Tidak ada fitur registrasi admin mandiri — akun admin dibuat via seeder/manual oleh developer saat setup awal

### 3.2 Keamanan Umum Panel Admin
- Semua route `/admin/*` dilindungi middleware `auth`
- CSRF protection aktif di semua form
- Validasi input ketat via Laravel Form Request di setiap aksi create/update
- `APP_DEBUG=false` di production — error tidak menampilkan stack trace ke admin/publik
- Log aktivitas penting (siapa melakukan apa, kapan) minimal untuk: transaksi stok, transaksi penjualan, perubahan status order, void transaksi (lihat Bagian 11 — Audit Trail)

---

## 4. Struktur Menu Dashboard Admin

```
Dashboard (Login) → Ringkasan (Overview)
├── Ringkasan
│   └── Total produk aktif, produk stok menipis/habis, order pending, omzet hari ini/bulan ini
├── Produk
│   ├── Kategori (CRUD)
│   └── Daftar Produk (CRUD, upload gambar, soft delete)
├── Gudang
│   ├── Barang Masuk (Stock In)
│   ├── Barang Keluar (Stock Out — manual)
│   ├── Stock Opname
│   └── Kartu Stok (per produk, riwayat + saldo berjalan)
├── Kasir
│   ├── Transaksi Baru (Walk-in POS)
│   └── Riwayat Transaksi (termasuk aksi Void)
├── Pesanan Online
│   └── Daftar Order (Pending → Dihubungi → Selesai/Dibatalkan)
├── Laporan
│   ├── Laporan Penjualan (harian/bulanan/tahunan/custom)
│   ├── Laporan Stok Masuk-Keluar
│   ├── Laporan Laba Rugi Sederhana
│   └── Laporan Stok Menipis
└── Pengaturan
    ├── Info Perusahaan (WA, alamat, email, jam operasional, deskripsi)
    └── Profil Admin (ubah nama/email/password akun sendiri)
```

---

## 5. Dashboard — Halaman Ringkasan (Overview)

Halaman pertama yang tampil setelah login. Menyajikan gambaran cepat kondisi bisnis hari itu tanpa perlu masuk ke sub-menu.

**Widget/kartu ringkasan:**
| Widget | Sumber Data | Catatan |
|---|---|---|
| Total Produk Aktif | `products` (tidak soft-deleted) | |
| Produk Stok Menipis | `products` dengan `stock <= low_stock_threshold AND stock > 0` | Link langsung ke daftar produk terkait |
| Produk Stok Habis | `products` dengan `stock <= 0` | Ditandai merah |
| Order Pending | `orders` dengan `status = pending` | Link ke Manajemen Pesanan |
| Omzet Hari Ini | `sales` dengan `status = completed`, `created_at = hari ini` | Gabungan online + walk-in |
| Omzet Bulan Berjalan | `sales` dengan `status = completed`, bulan berjalan | |
| Transaksi Terbaru (5 terakhir) | `sales` terurut terbaru | Quick access ke detail |
| Grafik Penjualan 7/30 hari | `sales` | Chart sederhana (library ringan, hindari yang berat) |

**Prinsip performa:** semua query dashboard menggunakan aggregate query (COUNT/SUM), bukan load seluruh data lalu dihitung di PHP — penting karena dashboard ini halaman paling sering diakses admin.

---

## 6. Modul: Pengaturan Website (Settings)

**Tujuan:** admin bisa mengubah data statis situs tanpa sentuh kode (requirement inti dari PRD Umum).

**Form Settings (single-record, tabel `settings`):**
| Field | Tipe | Keterangan |
|---|---|---|
| `wa_number` | string | Nomor WA admin, dipakai di tombol checkout & footer |
| `address` | text | Ditampilkan di About Us & footer |
| `email` | string | |
| `company_description` | text | Untuk About Us |
| `operating_hours` | string | Contoh: "Senin–Sabtu, 08.00–17.00" |

- Form berupa **satu halaman edit** (bukan CRUD list, karena hanya 1 row data)
- Perubahan langsung berefek ke halaman publik (About Us, footer, template pesan WA checkout) — perlu cache-clear otomatis jika settings di-cache
- Validasi: nomor WA wajib format valid (angka, minimal 10 digit), email wajib format email

**Sub-menu Profil Admin (terpisah dari Settings perusahaan):**
- Ubah nama tampilan
- Ubah email login
- Ubah password (wajib input password lama untuk konfirmasi)

---

## 7. Modul: Manajemen Kategori & Produk

### 7.1 Kategori (Struktur 2 Tingkat: Induk & Anak)

**Perubahan dari versi sebelumnya:** kategori tidak lagi flat 1 tingkat — sekarang mendukung **kategori induk** dan **kategori anak**, maksimal 2 tingkat kedalaman. Contoh:

```
Besi (induk)
├── Besi Siku (anak)
├── Besi Beton (anak)
└── Besi Plat (anak)

Pipa Stainless (induk)
├── Pipa Stainless 3x2 (anak)
├── Pipa Stainless 2x1/2 (anak)
└── Pipa Stainless 2x4 (anak)
```

**Field form kategori:**
| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `name` | string | Ya | Nama kategori |
| `slug` | string | Auto | Auto-generate dari nama |
| `parent_id` | select (nullable) | Tidak | Kosong = kategori ini adalah **induk**. Diisi = kategori ini adalah **anak** dari induk yang dipilih |

**Business rules:**
- **Maksimal 2 tingkat** — kategori yang sudah punya `parent_id` (yaitu kategori anak) **tidak boleh dipilih lagi sebagai induk** untuk kategori lain. Dropdown pilihan "Induk" di form hanya menampilkan kategori yang levelnya induk (`parent_id IS NULL`), agar admin tidak bisa membuat kategori 3 tingkat secara tidak sengaja.
- Kategori induk **boleh tidak punya produk langsung** — produk sebaiknya selalu dikaitkan ke kategori **anak** (leaf), bukan ke induk, supaya pengelompokan tetap rapi. Jika bisnis butuh produk tanpa sub-kategori spesifik (langsung di bawah induk), ini bisa diizinkan sebagai pengecualian — tapi disarankan tetap buat minimal 1 kategori anak per induk (misal "Besi Lainnya") untuk konsistensi struktur.
- **Validasi hapus:** kategori (induk maupun anak) yang masih punya produk terkait **tidak boleh dihapus**. Kategori induk yang masih punya kategori-anak di bawahnya **juga tidak boleh dihapus** sebelum semua anaknya dihapus/dipindah dulu — mencegah data yatim (orphan)
- Validasi: nama kategori unik **dalam level yang sama** (2 kategori anak di induk berbeda boleh punya nama mirip, misal "Ukuran Kecil" di bawah "Pipa Stainless" dan di bawah "Besi Siku")

**Tampilan list kategori di dashboard:**
- Ditampilkan sebagai **struktur pohon/indentasi** (induk di atas, anak di-indent di bawahnya) atau accordion (klik induk untuk expand daftar anaknya) — bukan tabel datar, agar hierarki terlihat jelas
- Setiap baris kategori anak menampilkan jumlah produk di dalamnya

### 7.2 Produk
**Field form produk:**
| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `name` | string | Ya | |
| `category_id` | select (kategori **anak**) | Ya | Dropdown bertingkat: pilih induk dulu (misal "Pipa Stainless"), lalu pilih anak (misal "Pipa Stainless 3x2"). Ditampilkan di list produk sebagai "Pipa Stainless > Pipa Stainless 3x2" atau breadcrumb serupa |
| `unit` | enum (pcs/kg/m) | Ya | Menentukan apakah qty desimal diizinkan di transaksi |
| `sell_price` | decimal | Ya | Harga jual |
| `cost_price` | decimal | Tidak (bisa diisi awal, atau otomatis terupdate dari Barang Masuk) | Dipakai hitung margin |
| `stock` | decimal, read-only | — | **Tidak bisa diedit langsung di form ini** — hanya lewat transaksi (lihat Bagian 8) |
| `low_stock_threshold` | decimal | Ya | Batas per produk, default bisa diisi saat create |
| `image` | file | Tidak | Maks 2MB, auto-compress via Intervention Image |
| `description` | text | Tidak | |

**Perilaku khusus:**
- Field `stock` ditampilkan sebagai **info saja** (badge/angka non-editable) di form edit produk, dengan link "Lihat Kartu Stok" — ini menegakkan aturan bisnis di PRD Gudang: *satu sumber kebenaran stok*
- Saat create produk baru, stok awal diisi via input terpisah "Stok Awal" yang otomatis membuat `inventory_log` pertama (`type = in`, `source = initial`) — bukan langsung set angka ke kolom `stock`
- Soft delete: tombol "Hapus" hanya menonaktifkan produk (`deleted_at` terisi), produk tidak lagi muncul di katalog publik dan pencarian POS, tapi riwayat laporan lama tetap utuh
- Halaman list produk menampilkan indikator status stok (Tersedia/Menipis/Habis) dengan warna, plus filter & search

---

## 8. Modul: Gudang (Inventory Management)

Prinsip kunci di seluruh modul ini: **`products.stock` tidak pernah diedit langsung** — hanya berubah melalui salah satu transaksi di bawah, semuanya tercatat di `inventory_logs`.

### 8.1 Barang Masuk (Stock In)
**Form:**
- Produk (searchable select)
- Jumlah masuk (qty, desimal untuk kg/m)
- Harga beli per satuan (opsional — jika diisi, update `products.cost_price`)
- Sumber/Supplier (teks bebas)
- Catatan
- Tanggal transaksi

**Aksi sistem (dalam `DB::transaction()`):**
1. `products.stock += qty`
2. Insert `inventory_logs` (`type = in`, `source = purchase`)
3. Update `cost_price` jika diisi

### 8.2 Barang Keluar Manual (Stock Out)
**Form:**
- Produk
- Jumlah keluar
- Alasan (dropdown: Rusak / Susut / Hilang / Sample / Lainnya)
- Catatan
- Tanggal

**Validasi:** sistem menolak jika qty > stok tersedia (stok tidak boleh negatif)

**Aksi sistem:**
1. `products.stock -= qty`
2. Insert `inventory_logs` (`type = out`, `source = manual`)

### 8.3 Stock Opname
**Alur admin:**
1. Buka menu Stock Opname → sistem tampilkan seluruh produk aktif dengan stok sistem saat ini
2. Admin input stok fisik hasil hitung manual per produk (bisa sebagian, tidak wajib semua produk sekaligus)
3. Sistem hitung selisih otomatis (`fisik - sistem`)
4. Admin submit sesi opname
5. Sistem otomatis, per produk yang punya selisih:
   - Buat `inventory_log` (`type = in` jika lebih, `type = out` jika kurang, `source = opname`)
   - Update `products.stock` ke angka fisik
6. Sesi opname tersimpan permanen di `stock_opnames` + `stock_opname_items` untuk histori

### 8.4 Kartu Stok
- Halaman detail per produk
- Tabel kronologis seluruh pergerakan: tanggal, tipe (IN/OUT), sumber, qty, catatan, **saldo berjalan (running balance)**
- Bisa difilter per rentang tanggal
- Fungsi audit trail utama untuk verifikasi manual oleh admin/owner

### 8.5 Alert Stok Menipis
- Badge notifikasi jumlah produk menipis/habis, terlihat dari header dashboard (semua halaman admin)
- Klik badge → langsung ke daftar produk terkait, terfilter

---

## 9. Modul: Kasir (POS)

### 9.1 Transaksi Baru (Walk-in)
**Alur input admin:**
1. Buka "Transaksi Baru"
2. Cari & pilih produk (search cepat by nama), input qty per item (desimal jika unit kg/m, integer jika pcs — divalidasi di JS & server)
3. Sistem hitung subtotal per item & total otomatis (harga diambil live dari `sell_price` saat transaksi, lalu **disimpan sebagai snapshot** di `sale_items.unit_price_snapshot`)
4. Input nama pembeli (opsional, default "Umum")
5. Pilih metode pembayaran (Cash/Transfer)
6. Jika Cash: input jumlah dibayar → sistem hitung kembalian otomatis. Jika Transfer: kembalian = 0
7. Submit → sistem validasi stok cukup untuk **semua item sekaligus** sebelum simpan
8. Jika valid, dalam satu `DB::transaction()`:
   - Insert `sales` (`source = walk-in`, `status = completed`) + `sale_items`
   - Kurangi `products.stock` per item
   - Insert `inventory_logs` (`type = out`, `source = sale`, `reference_id = sale.id`)
9. Tampilkan halaman nota (print-friendly, bisa `Ctrl+P` ke PDF/printer biasa — tanpa library PDF tambahan)

### 9.2 Riwayat Transaksi
- List seluruh `sales` (online + walk-in), dengan filter: tanggal, sumber, status, metode pembayaran
- Detail transaksi menampilkan item, harga snapshot, total, status

### 9.3 Void Transaksi
- Tombol "Void" hanya tersedia untuk transaksi berstatus `completed`
- Setelah konfirmasi admin (dialog konfirmasi wajib, mencegah klik tidak sengaja):
  1. `sales.status = voided`
  2. Sistem otomatis kembalikan stok: insert `inventory_logs` (`type = in`, `source = void`, `reference_id = sale.id`)
  3. Data transaksi **tidak dihapus**, tetap tampil di riwayat dengan status Voided (untuk audit)

---

## 10. Modul: Manajemen Pesanan Online (Order Management)

**Alur status order:** `pending` → `contacted` → `completed` / `cancelled`

**Halaman Daftar Order:**
- Tabel order dengan kolom: nomor order, nama customer, no. HP, total estimasi, status, tanggal
- Filter by status, tanggal
- Klik order → detail (daftar item, qty, alamat, metode pembayaran informatif)

**Aksi admin per order:**
| Aksi | Efek Sistem |
|---|---|
| Tandai "Dihubungi" (contacted) | Update `orders.status`, tidak ada efek stok |
| Tandai "Selesai" (completed) | Sistem otomatis: cek stok tiap item cukup → jika ya, buat `sales` (`source = online`, `order_id` terisi) + `sale_items` dari `order_items` → kurangi stok → insert `inventory_logs` (`source = sale`). Jika stok tidak cukup (misal berubah sejak order dibuat), sistem tampilkan peringatan dan **tidak mengizinkan** ubah status sampai admin menyesuaikan (edit qty item order atau restock dulu) |
| Tandai "Dibatalkan" (cancelled) | Update status saja, **tidak ada efek stok** (karena stok belum pernah dikurangi saat checkout) |

**Catatan penting:** admin **tidak bisa** langsung ubah status dari `pending` ke `completed` tanpa melalui `contacted` terlebih dahulu (mencegah kelalaian follow-up) — atau jika ingin lebih fleksibel, ini bisa dijadikan konfigurasi opsional; default: alur linear wajib.

---

## 11. Modul: Laporan (Reporting)

Semua laporan mendukung filter **harian/bulanan/tahunan/custom range**, dan tombol **Export Excel** satu-klik (Laravel Excel/Maatwebsite), dijalankan langsung di request (tanpa queue, sesuai prinsip hemat biaya).

| Laporan | Isi | Sumber Data |
|---|---|---|
| Laporan Penjualan | Total transaksi, omzet, item terlaris, breakdown online vs walk-in | `sales`, `sale_items` |
| Laporan Stok Masuk-Keluar | Rekap `inventory_logs`, bisa difilter per produk | `inventory_logs` |
| Laporan Laba Rugi Sederhana | Omzet − Total HPP (`cost_price` × qty terjual) = margin kotor | `sale_items` join `products` |
| Laporan Stok Menipis | Daftar produk di bawah threshold saat ini | `products` |
| Kartu Stok (per produk) | Riwayat + saldo berjalan | `inventory_logs` |

**Format Export Excel** mengikuti struktur tabel di atas per jenis laporan, dengan header judul laporan + rentang tanggal di baris atas file.

---

## 12. Audit Trail & Jejak Aktivitas

Karena admin adalah role tunggal dengan akses penuh, jejak audit penting untuk akuntabilitas meski hanya 1 pengguna (berguna juga saat tim bertambah nanti):

- Setiap `inventory_logs`, `sales`, `stock_opnames` menyimpan `created_by` (id admin yang melakukan aksi)
- Aksi sensitif (Void transaksi, Barang Keluar manual, Stock Opname) sebaiknya tercatat dengan timestamp jelas dan tidak bisa dihapus dari sistem — hanya bisa ditambah data koreksi baru
- Tidak ada fitur "hapus permanen" untuk data transaksi di panel admin manapun (selaras dengan aturan Void bukan Delete di PRD Gudang & Kasir)

---

## 13. Kebutuhan Non-Fungsional Khusus Admin

- **Responsif:** dashboard harus tetap dapat dioperasikan dari tablet/smartphone (misal saat admin di gudang, jauh dari komputer) — tabel data menggunakan horizontal scroll di layar kecil
- **Kecepatan input Kasir:** halaman Transaksi Baru harus punya search produk yang cepat (debounce, tanpa reload halaman — pakai Alpine.js), karena dipakai saat pembeli menunggu di depan admin
- **Validasi ganda:** semua validasi stok/qty dilakukan di server (bukan hanya JS), karena ini menyentuh data bisnis kritis
- **Konsistensi data:** semua operasi yang menyentuh lebih dari 1 tabel wajib dibungkus `DB::transaction()`
- **Tanpa dependensi berat:** tidak ada fitur admin yang membutuhkan Redis, queue worker, atau layanan pihak ketiga berbayar — selaras dengan strategi hemat biaya PRD Umum Bagian 8

---

## 14. Skema Database Terkait Admin (Ringkasan)

| Tabel | Fungsi |
|---|---|
| `users` | Akun admin (id, name, email, password, role) |
| `settings` | Data statis perusahaan (single row) |
| `categories` | Kategori produk, **self-referencing via `parent_id`** (2 tingkat: induk & anak) |
| `products` | Master produk (stock read-only via UI) |
| `orders`, `order_items` | Pesanan dari web |
| `sales`, `sale_items` | Transaksi penjualan (online & walk-in) |
| `inventory_logs` | Audit trail pergerakan stok (single source of truth) |
| `stock_opnames`, `stock_opname_items` | Sesi & detail stock opname |

*(Detail penuh atribut per tabel mengikuti skema di PRD Umum Bagian 6 dan PRD Gudang & Kasir Bagian 6 — tidak diulang di sini untuk menghindari duplikasi yang bisa jadi tidak sinkron.)*

---

## 15. Prioritas Pengembangan Modul Admin (Disarankan)

1. Autentikasi Admin (Laravel Breeze) + middleware proteksi route
2. Layout dasar dashboard (sidebar menu, header dengan badge alert stok)
3. Modul Settings (Info Perusahaan + Profil Admin)
4. CRUD Kategori & Produk (termasuk upload gambar, stok awal, soft delete)
5. Modul Gudang: Barang Masuk → Barang Keluar → Kartu Stok
6. Modul Kasir: Transaksi Baru (walk-in) + Riwayat + Void
7. Modul Pesanan Online: daftar order + alur status + integrasi otomatis ke `sales`
8. Stock Opname
9. Modul Laporan + Export Excel (Penjualan, Stok, Laba Rugi, Stok Menipis)
10. Dashboard Ringkasan (widget & grafik) — dikerjakan setelah data transaksi tersedia agar bisa diuji dengan data nyata
11. Hardening: rate limiting login, audit trail review, testing validasi stok negatif & concurrency

---

## 16. Kriteria Selesai (Acceptance Criteria) — Ringkas

- [ ] Admin bisa login, dan seluruh route admin tidak bisa diakses tanpa login
- [ ] Admin bisa mengubah data Settings dan perubahannya langsung tampil di halaman publik
- [ ] Admin bisa CRUD kategori (induk & anak, maksimal 2 tingkat) dan produk, termasuk upload gambar terkompresi, tanpa bisa mengedit `stock` secara langsung
- [ ] Kategori anak tidak bisa dijadikan induk bagi kategori lain (mencegah struktur 3 tingkat); kategori/induk yang masih punya produk/anak tidak bisa dihapus
- [ ] Barang Masuk & Keluar tercatat benar di `inventory_logs` dan mengubah `products.stock` secara konsisten
- [ ] Kartu Stok menampilkan riwayat lengkap dengan saldo berjalan yang akurat
- [ ] Kasir walk-in bisa mencatat transaksi multi-item dengan snapshot harga, kembalian otomatis, dan validasi stok
- [ ] Void transaksi mengembalikan stok dengan benar dan tidak menghapus data
- [ ] Order online bisa diubah statusnya, dan status `completed` otomatis membuat `sales` + mengurangi stok, sedangkan `cancelled` tidak mengubah stok sama sekali
- [ ] Stock Opname menghasilkan penyesuaian otomatis yang sesuai selisih fisik vs sistem
- [ ] Semua laporan bisa difilter per rentang tanggal dan diexport ke Excel dengan data yang akurat
- [ ] Semua operasi multi-tabel dibungkus transaksi database (tidak ada data setengah jalan saat error)

---

## 17. Catatan Roadmap (Tidak Dikerjakan di Fase 1)

- Role bertingkat (Owner vs Staff Gudang) dengan permission granular
- Manajemen Supplier terstruktur (tabel `suppliers` terpisah)
- Cetak invoice PDF otomatis (saat ini cukup print-friendly HTML)
- Notifikasi push/email otomatis untuk order baru atau stok kritis
- Log aktivitas admin yang lebih detail (activity log terpisah per aksi, bukan hanya `created_by`)