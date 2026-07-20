# Product Requirements Document (PRD)
**Proyek:** Website Company Profile & Sistem Administrasi "CV BERKAH"
**Versi:** 4.1 (Refined — Cost-Effective Deployment Edition + Kategori Berjenjang)
**Tanggal:** Juli 2026
**Platform:** Web Application (Laravel 11.x)
**Status Proyek:** Struktur project Laravel sudah dibuat (kosong), siap masuk fase development

**Changelog v4.0 → v4.1:** Kategori produk kini mendukung **2 tingkat (induk & anak)** — lihat Bagian 3 poin 8 dan Bagian 6. Keputusan ini diambil karena kebutuhan nyata di lapangan: item seperti "Pipa Stainless 3x2", "2x1/2", "2x4" adalah **produk terpisah** (stok & harga masing-masing), dikelompokkan di bawah kategori induk "Pipa Stainless"; begitu juga "Besi Siku", "Besi Beton" dikelompokkan di bawah induk "Besi".

---

## 1. Ringkasan Proyek

CV BERKAH adalah perusahaan yang bergerak di bidang penjualan besi tua. Proyek ini membangun platform web hybrid yang menggabungkan tiga fungsi dalam satu sistem:

1. **Company Profile** — Home, Product Catalog, About Us
2. **Katalog & "Keranjang" Pemesanan** — tanpa checkout online, order diteruskan ke WhatsApp admin
3. **Mini ERP Gudang & Kasir** — CRUD produk, stok multi-satuan, log barang masuk/keluar, laporan

**Prinsip desain utama v4:** sistem harus *production-ready* namun tetap bisa **di-deploy dan dioperasikan dengan biaya hosting rendah**, sesuai skala trafik 500–1.000 pengunjung/bulan. Setiap keputusan arsitektur di dokumen ini disaring melalui pertanyaan: *"apakah ini menambah biaya server/layanan pihak ketiga yang tidak perlu di skala ini?"*

---

## 2. Tujuan & Sasaran

| Tujuan | Detail |
|---|---|
| Ekspansi Digital | Memperkenalkan CV BERKAH ke audiens lebih luas via web |
| Kemudahan Pemesanan | User bisa memesan ke WA admin tanpa login/registrasi |
| Efisiensi Gudang | Catat pergerakan barang (IN/OUT) dengan multi-satuan (pcs/kg/m) |
| Stok Realtime | Alert otomatis saat stok di bawah batas aman (threshold **per produk**, bukan angka tetap) |
| Pelaporan Eksekutif | Laporan harian/bulanan/tahunan, export Excel (.xlsx) |
| Kemandirian Sistem | Admin bisa ubah data statis (No WA, alamat, dsb) tanpa sentuh kode |
| **Efisiensi Biaya** | Sistem berjalan lancar di **shared hosting/VPS murah**, tanpa dependensi layanan berbayar pihak ketiga |
| **Pengelompokan Produk yang Jelas** *(baru v4.1)* | Kategori berjenjang (induk & anak) agar produk dengan banyak variasi ukuran/jenis tetap mudah ditelusuri user maupun dikelola admin |

---

## 3. Perubahan Penting dari PRD Sebelumnya (v3 → v4 → v4.1)

Ini bagian paling penting — beberapa celah di PRD versi Gemini yang saya perbaiki, ditambah penyempurnaan terbaru:

1. **Tidak ada tabel `orders`.** Di versi lama, keranjang hanya session, begitu user klik "kirim ke WhatsApp", data pesanan **hilang** dari sistem — admin tidak bisa lihat riwayat order, tidak bisa follow-up, tidak bisa hitung konversi. Saya tambahkan tabel `orders` & `order_items` agar setiap checkout tetap tercatat di database sebelum redirect ke WA.
2. **Threshold stok hardcoded (< 50).** Tidak masuk akal untuk semua produk — besi tua dijual per pcs, kg, dan meter dengan skala berbeda jauh. Saya ubah jadi `low_stock_threshold` per produk, diatur admin sendiri.
3. **Tidak ada pencatatan harga beli.** Tanpa `cost_price`, sistem tidak bisa menghitung margin/keuntungan — padahal ini fungsi dasar "mini ERP kasir gudang". Saya tambahkan.
4. **Tidak ada strategi biaya hosting.** PRD lama hanya menyebut tech stack tanpa mempertimbangkan biaya operasional bulanan. Saya tambahkan Bagian 8 secara khusus.
5. **Redis/queue tidak dibahas** — saya pastikan stack yang direkomendasikan **tidak butuh Redis**, cukup driver database, supaya tidak perlu upgrade paket hosting.
6. **Tidak ada rencana backup.** Data stok & laporan adalah data bisnis kritis — saya tambahkan strategi backup gratis/murah.
7. **Nomor pesanan & status follow-up admin** belum ada — ditambahkan agar admin bisa menandai pesanan "Sudah Dihubungi / Selesai / Batal".
8. **Kategori masih flat 1 tingkat** *(baru v4.1)* — tidak cukup untuk produk dengan banyak jenis/ukuran seperti besi dan pipa stainless. Saya ubah `categories` menjadi **self-referencing (`parent_id`), maksimal 2 tingkat**: kategori induk (misal "Besi", "Pipa Stainless") dan kategori anak (misal "Besi Siku", "Pipa Stainless 3x2"). Setiap produk tetap terhubung ke **satu kategori anak** — bukan berarti satu produk punya banyak varian ukuran dalam satu baris data, melainkan setiap ukuran/jenis adalah **produk sendiri** dengan stok & harga masing-masing, sesuai kebutuhan akurasi gudang.

---

## 4. Pengguna Sistem (User Roles)

1. **User / Pengunjung (Public)** — akses tanpa login, browsing produk, isi keranjang, checkout via WA.
2. **Admin** — 1 role tunggal (kasir + gudang + pengelola web) untuk menyederhanakan sistem dan menghindari kompleksitas permission yang tidak perlu di skala ini. *(Jika ke depan tim bertambah, role "Staff Gudang" vs "Owner" bisa ditambahkan sebagai enhancement — lihat Bagian 10.)*

---

## 5. Kebutuhan Fungsional

### A. Fitur Publik (User) — 3 Halaman Utama

**1. Home**
- Hero section singkat tentang CV BERKAH
- Highlight kategori produk unggulan (kategori **induk**, misal "Besi", "Pipa Stainless")
- CTA ke halaman Product

**2. Product (Katalog)**
- Daftar produk dikelompokkan per kategori, dengan **filter kategori 2 tingkat**: pilih kategori induk (menampilkan gabungan seluruh produk dari kategori-anak di bawahnya) atau langsung pilih kategori anak (lebih spesifik, misal langsung "Pipa Stainless 3x2")
- Search produk by nama
- Setiap kartu produk menampilkan: nama, gambar, satuan (pcs/kg/m), harga, **status stok** (Tersedia / Stok Menipis / Habis — dihitung realtime dari field `stock` vs `low_stock_threshold`)
- Detail produk: breadcrumb kategori (`Induk > Anak`), deskripsi, harga per satuan, input kuantitas (mendukung desimal, misal 10.5 kg)
- Tombol "Tambah ke Keranjang"

**3. About Us**
- Profil perusahaan, sejarah singkat, alamat, kontak (semua ditarik dari tabel `settings`, bukan hardcode)

**4. Keranjang & Checkout (bagian dari flow Product)**
- Keranjang berbasis **session** (tanpa login), mendukung banyak item dengan kuantitas desimal per item
- Validasi: kuantitas tidak boleh melebihi stok tersedia saat checkout (cek ulang di server, bukan hanya di frontend)
- Form checkout: Nama, No HP, Alamat (opsional), Metode Pembayaran (Cash/Transfer — informatif saja)
- **Saat submit:** sistem menyimpan data ke tabel `orders` + `order_items` (status: `pending`) dengan nomor order otomatis, **baru kemudian** redirect ke WhatsApp dengan template pesan berisi nomor order, daftar barang, dan total estimasi
- User tetap menekan tombol "Kirim" di WhatsApp secara manual (sesuai requirement awal — tidak ada auto-send)

### B. Fitur Dashboard (Admin)

**1. Pengaturan Website (Settings)**
- Ubah No. WhatsApp Admin, Alamat, Email, Deskripsi Perusahaan, Jam Operasional
- Semua field ini yang dipakai di halaman publik (About Us, footer, template WA)

**2. Manajemen Kategori** *(diperbarui v4.1)*
- CRUD kategori dengan **struktur 2 tingkat**: kategori induk (`parent_id = null`) dan kategori anak (`parent_id` menunjuk ke induknya)
- Kategori anak **tidak boleh** dijadikan induk bagi kategori lain (mencegah struktur lebih dari 2 tingkat)
- Kategori (induk maupun anak) yang masih punya produk/anak terkait **tidak boleh dihapus**

**3. Manajemen Produk (CRUD)**
- Field: nama, **kategori (pilih dari kategori anak)**, satuan (pcs/kg/m), harga jual, **harga beli (cost price)**, stok, **threshold stok minimum per produk**, gambar, deskripsi
- Setiap varian ukuran/jenis (misal "Pipa Stainless 3x2" vs "2x1/2") diinput sebagai **produk terpisah**, masing-masing dengan `category_id` ke kategori anak yang sesuai
- Validasi gambar: maks 2MB, otomatis di-compress (mengurangi ukuran file agar hemat storage & bandwidth hosting)
- **Soft Delete** — produk terhapus tidak hilang dari database, demi integritas riwayat laporan gudang

**4. Manajemen Gudang (Inventory Log)**
- Input Barang Masuk (IN) — misal restock dari supplier
- Input Barang Keluar (OUT) — otomatis tercatat juga saat order dari user dikonfirmasi admin, atau manual (misal barang rusak/retur)
- Update stok otomatis setiap ada transaksi IN/OUT
- Riwayat log per produk (siapa/kapan/berapa)

**5. Manajemen Pesanan (Order Management)** *(fitur baru di v4)*
- Daftar order masuk dari web (status: Pending → Dihubungi → Selesai/Dibatalkan)
- Admin bisa update status setelah follow-up via WA
- Saat status "Selesai", sistem otomatis membuat `inventory_log` tipe OUT dan mengurangi stok — jadi stok tidak berkurang otomatis begitu user checkout (mencegah stok "hilang" dari pesanan yang batal/tidak jadi)

**6. Sistem Laporan (Reporting & Export)**
- Filter laporan: harian / bulanan / tahunan / custom range
- Laporan mencakup: barang masuk, barang keluar, penjualan, dan **estimasi margin** (harga jual − harga beli)
- Export ke Excel (.xlsx) satu klik menggunakan Laravel Excel (Maatwebsite)
- Dashboard ringkas: total produk, produk stok menipis, order pending, omzet bulan berjalan

---

## 6. Arsitektur Database (Disempurnakan)

| Tabel | Deskripsi | Atribut Utama |
|---|---|---|
| `users` | Akun Admin | id, name, email, password |
| `settings` | Pengaturan Web | id, wa_number, address, email, company_description, operating_hours |
| `categories` | Kategori Produk, **self-referencing 2 tingkat** *(diperbarui v4.1)* | id, name, slug, **parent_id (nullable, FK ke categories.id — null berarti kategori induk)** |
| `products` | Master Barang | id, category_id (mengacu ke kategori **anak**), name, slug, image, unit (pcs/kg/m), cost_price, sell_price, stock, low_stock_threshold, description, deleted_at (soft delete) |
| `inventory_logs` | Catatan Gudang | id, product_id, type (in/out), qty, note, order_id (nullable, jika OUT berasal dari order), created_by, created_at |
| `orders` | **[BARU]** Pesanan dari Web | id, order_number, customer_name, customer_phone, customer_address, payment_method, status (pending/contacted/completed/cancelled), total_estimate, created_at |
| `order_items` | **[BARU]** Detail Item per Order | id, order_id, product_id, qty, unit_price_snapshot, subtotal |

> Catatan: `unit_price_snapshot` disimpan agar laporan historis tetap akurat walau harga produk berubah di kemudian hari.

> **Catatan struktur kategori (v4.1):** `categories.parent_id` bersifat self-referencing ke `categories.id`. Bisnis rule: kategori dengan `parent_id` terisi (kategori anak) **tidak boleh** dijadikan `parent_id` bagi kategori lain — ini dijaga di level aplikasi (validasi form), bukan constraint database, agar tetap fleksibel jika struktur bisnis berkembang. Setiap `products.category_id` **wajib** mengarah ke kategori anak (leaf), bukan ke kategori induk, agar pengelompokan tetap presisi (misal produk "Pipa Stainless 3x2" masuk kategori anak "Pipa Stainless 3x2", bukan langsung ke induk "Pipa Stainless").

---

## 7. Kebutuhan Non-Fungsional & UI/UX

- **Skalabilitas:** dioptimalkan untuk 500–1.000 pengunjung/bulan; query katalog dioptimasi (hindari N+1 dengan eager loading `with()`, termasuk saat memuat relasi kategori induk-anak)
- **Desain:** minimalis, clean, mobile-first, tanpa animasi berat
- **Responsif:** desktop, tablet, smartphone — termasuk tabel data di dashboard admin (gunakan horizontal scroll pada tabel di layar kecil, bukan memaksa layout)
- **Performa:**
  - Gambar produk dikompresi otomatis sebelum disimpan (library `Intervention/Image`)
  - Laravel config/route/view caching aktif di production (`php artisan optimize`)
  - Cache driver: **file atau database** (bukan Redis) — cukup untuk skala ini, tidak perlu service tambahan
- **Keamanan:**
  - CSRF protection di semua form
  - Validasi input ketat (Form Request) — cegah XSS & SQL Injection (Eloquent ORM sudah aman dari SQL Injection secara default)
  - Rate limiting/throttle pada halaman login admin (cegah brute force)
  - `.env` tidak pernah masuk repository; APP_DEBUG=false di production

---

## 8. Strategi Deployment & Estimasi Biaya (Fokus Utama)

Karena skala trafik masih kecil (500–1.000 pengunjung/bulan) dan sistem tidak butuh proses berat (tidak ada AI, tidak ada video processing, tidak ada real-time chat), **shared hosting berbasis cPanel sudah cukup** — tidak perlu VPS di awal.

### Opsi A — Shared Hosting (Direkomendasikan untuk mulai)
- Provider lokal yang mendukung Laravel (PHP 8.2+, Composer via SSH/Terminal, akses `.env`): contoh kelas **Niagahoster, DomaiNesia, IDCloudHost, Rumahweb** — paket "Bisnis/Pro" biasanya sudah cukup
- Estimasi biaya: **± Rp30.000–60.000/bulan** (dibayar tahunan biasanya lebih murah), belum termasuk domain
- Domain `.com`: **± Rp150.000/tahun**
- SSL: **gratis** (Let's Encrypt, biasanya otomatis tersedia di cPanel modern)
- Database: MySQL yang sudah include di paket hosting, tidak perlu server DB terpisah
- Kekurangan: kontrol server terbatas, cron job biasanya terbatas (tapi cukup untuk 1 scheduled task Laravel)

### Opsi B — VPS Murah (jika butuh kontrol lebih / trafik naik)
- Contoh: DigitalOcean, Vultr, Contabo, Biznet Gio — mulai **$4–6/bulan (± Rp65.000–100.000/bulan)**
- Perlu setup manual (Nginx/Apache, PHP-FPM, MySQL, SSL via Certbot) — butuh sedikit skill DevOps atau bantuan sekali setup
- Lebih fleksibel untuk scaling nanti, tapi **tidak wajib di fase awal**

### Langkah Hemat Biaya Tambahan
| Area | Strategi Hemat |
|---|---|
| Storage gambar | Simpan lokal di server (bukan S3/cloud storage berbayar) — cukup di skala ini, asal gambar sudah dikompresi |
| CDN & keamanan | Gunakan **Cloudflare Free Plan** di depan domain — gratis, sekaligus proteksi dasar DDoS & caching aset statis |
| Cache/Queue | Gunakan driver **file/database**, hindari Redis/Memcached agar tidak perlu upgrade paket hosting |
| Cron Job | 1 cron entry ke `php artisan schedule:run` setiap menit — cukup untuk cek stok & tugas terjadwal, didukung hampir semua shared hosting |
| Monitoring | **UptimeRobot** (gratis) untuk cek uptime, tidak perlu tool berbayar |
| Backup | Backup database mingguan otomatis (mysqldump via cron) disimpan ke email/Google Drive gratis, atau fitur backup bawaan cPanel |
| Export Excel | Jalankan langsung di request (bukan queue/job terpisah) — aman untuk skala data yang belum besar |

### Estimasi Total Biaya Tahun Pertama
| Item | Estimasi |
|---|---|
| Domain .com | Rp150.000/tahun |
| Shared Hosting (Paket Bisnis) | Rp360.000–720.000/tahun |
| SSL | Gratis |
| CDN/Security (Cloudflare) | Gratis |
| Monitoring | Gratis |
| **Total** | **± Rp500.000–900.000/tahun** (~Rp42.000–75.000/bulan) |

---

## 9. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11.x, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS, Alpine.js (interaktivitas ringan, tanpa build tool berat seperti React/Vue) |
| Database | MySQL/MariaDB (sudah tersedia di shared hosting) |
| Excel Export | Laravel Excel (Maatwebsite) |
| Image Processing | Intervention Image (compress otomatis) |
| Cache/Session/Queue | Driver `file`/`database` — tanpa Redis |

---

## 10. Rencana Pengembangan Lanjutan (Future Enhancements)

*Tidak dikerjakan di fase 1 — hanya dicatat sebagai roadmap, agar tidak menambah kompleksitas/biaya di awal:*

- Integrasi Payment Gateway (Midtrans/Xendit) untuk pembayaran otomatis
- Sistem registrasi member untuk pelanggan tetap (B2B)
- Cetak invoice PDF otomatis dari aplikasi
- Role tambahan (Owner vs Staff Gudang) jika tim bertambah
- Migrasi ke VPS + Redis/queue worker jika trafik jauh melampaui 1.000 pengunjung/bulan
- Kategori lebih dari 2 tingkat, jika kompleksitas produk berkembang jauh lebih jauh (saat ini 2 tingkat dianggap cukup)

---

## 11. Ringkasan Prioritas Pengembangan (Disarankan)

1. Setup struktur project, migration, model (`products`, `categories` dengan `parent_id`, `settings`, `orders`, `order_items`, `inventory_logs`)
2. Autentikasi Admin (Laravel Breeze — ringan, cocok untuk skala ini)
3. CRUD Kategori (induk & anak) & Produk + upload gambar terkompresi
4. Halaman publik: Home, Product (katalog + filter berjenjang + detail + breadcrumb), About Us
5. Keranjang session + checkout → simpan ke `orders` → redirect WA
6. Dashboard admin: manajemen pesanan, inventory log, alert stok
7. Modul laporan + export Excel
8. Optimasi performa (caching, eager loading) + hardening keamanan
9. Deployment ke shared hosting + setup domain, SSL, Cloudflare, backup cron