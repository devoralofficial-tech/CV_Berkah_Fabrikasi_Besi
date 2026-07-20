# Product Requirements Document (PRD)
**Modul:** Sisi Publik / User (Company Profile, Katalog, Keranjang & Checkout)
**Proyek:** CV BERKAH — Website Company Profile & Mini ERP Gudang-Kasir
**Versi:** 1.1
**Tanggal:** Juli 2026
**Dokumen ini merangkum & merinci:** PRD Umum v4 — difokuskan khusus pada sisi User/Pengunjung (Public)
**Dokumen terkait:** PRD Admin CV BERKAH (panel administrasi, sisi tertutup)
**Platform:** Web Application (Laravel 11.x, Blade + Tailwind + Alpine.js)

**Changelog v1.1:** Filter & navigasi kategori diperbarui untuk mendukung struktur 2 tingkat (kategori induk & anak) — lihat Bagian 6.1 dan breadcrumb di Bagian 6.2.

---

## 1. Latar Belakang & Tujuan

Sisi User adalah **wajah publik** CV BERKAH — bagian yang bisa diakses siapa saja tanpa login/registrasi. Tujuannya dua arah:

1. **Company Profile** — memperkenalkan CV BERKAH ke audiens lebih luas (siapa mereka, apa yang dijual, bagaimana dihubungi)
2. **Katalog & Pemesanan** — memudahkan calon pembeli melihat stok & harga barang, lalu memesan tanpa proses berbelit, dengan follow-up final tetap manual via WhatsApp (bukan pembayaran online)

**Prinsip desain utama:** tidak butuh akun, tidak butuh checkout online, tidak ada friksi. User cukup pilih barang, isi form singkat, dan lanjut ke WhatsApp admin.

---

## 2. Ruang Lingkup

**Termasuk dalam dokumen ini:**
- Halaman Home
- Halaman Product (katalog, filter, search, detail produk)
- Halaman About Us
- Keranjang belanja (session-based)
- Alur Checkout → simpan order → redirect WhatsApp
- Kebutuhan non-fungsional khusus sisi publik (performa, SEO dasar, mobile-first)

**Tidak termasuk (dibahas di PRD Admin):**
- Login, dashboard, CRUD produk/kategori, manajemen stok
- Manajemen pesanan sisi admin, kasir, laporan

---

## 3. Pengguna & Karakteristik

| Aspek | Detail |
|---|---|
| Siapa | Pengunjung umum — calon pembeli besi tua, individu maupun bisnis kecil |
| Akses | Tanpa login/registrasi, sepenuhnya publik |
| Perangkat dominan | Mobile-first (asumsi mayoritas akses dari HP, terutama saat membandingkan harga/stok di lapangan) |
| Perilaku khas | Cepat cek harga & stok → langsung mau chat WA, tidak terbiasa proses checkout panjang ala e-commerce |

---

## 4. Peta Halaman (Sitemap Publik)

```
Navbar: Home | Product | About Us | [Ikon Keranjang]
├── Home (/)
├── Product (/product)
│   ├── Filter Kategori Induk (misal "Besi", "Pipa Stainless")
│   │   └── Filter Kategori Anak (misal "Besi Siku", "Pipa Stainless 3x2")
│   ├── Search Produk
│   └── Detail Produk (/product/{slug})
│       ├── Breadcrumb: Product > [Induk] > [Anak]
│       └── Tambah ke Keranjang
├── Keranjang (/cart)
│   └── Checkout (/checkout) → Simpan Order → Redirect WhatsApp
└── About Us (/about)
Footer: Kontak, Alamat, Jam Operasional, No. WA (ditarik dari `settings`)
```

---

## 5. Halaman: Home

**Tujuan:** kesan pertama yang meyakinkan + jalan cepat ke katalog.

**Komponen:**
| Bagian | Isi |
|---|---|
| Hero Section | Nama perusahaan, tagline singkat, CTA utama "Lihat Produk" |
| Highlight Kategori | 3–6 kategori unggulan (kartu dengan ikon/gambar), klik → langsung ke Product terfilter kategori tsb |
| Kenapa Pilih Kami | Poin singkat (opsional): pengalaman, harga bersaing, dsb — teks statis, bisa dari `settings.company_description` atau konten terpisah |
| CTA Penutup | Ajakan hubungi via WA atau lihat katalog |

**Kebutuhan konten:** semua teks non-produk sebaiknya bisa diedit admin lewat Settings (bukan hardcode di Blade), sesuai prinsip "kemandirian sistem" di PRD Umum.

**Kebutuhan performa:** hero image dikompresi, tanpa animasi/JS berat, first paint cepat karena ini halaman entry point utama dari pencarian Google/iklan.

---

## 6. Halaman: Product (Katalog)

### 6.1 Daftar Produk

**Filter kategori 2 tingkat (induk & anak):**
- Sidebar/chip filter menampilkan **kategori induk** dulu (misal "Besi", "Pipa Stainless") sebagai grup
- Klik kategori induk → expand menampilkan **kategori anak** di bawahnya (misal "Besi Siku", "Besi Beton" di bawah "Besi") — bisa berupa accordion di sidebar (desktop) atau dropdown/bottom-sheet (mobile)
- User bisa filter langsung ke kategori anak (paling umum, karena produk sebenarnya terhubung ke kategori anak) atau klik kategori induk untuk melihat **semua produk dari seluruh anaknya sekaligus** (gabungan)
- URL filter mencerminkan pilihan, misal `/product?kategori=besi-siku` (anak) atau `/product?kategori=besi` (induk, otomatis gabungan semua anak)
- **Search** produk by nama (input dengan debounce, tanpa reload penuh jika memungkinkan — Alpine.js/AJAX ringan)
- Kombinasi filter kategori (induk atau anak) + search bisa dipakai bersamaan
- Pagination atau infinite scroll (pagination lebih ringan & lebih sesuai skala trafik 500–1.000/bulan)

**Kartu produk menampilkan:**
| Elemen | Keterangan |
|---|---|
| Gambar | Thumbnail terkompresi |
| Nama produk | |
| Satuan | pcs / kg / m |
| Harga | Format Rupiah, per satuan (misal "Rp 8.500 / kg") |
| Status stok | Badge realtime: **Tersedia** (hijau) / **Stok Menipis** (kuning) / **Habis** (merah) — dihitung dari `stock` vs `low_stock_threshold` di server, bukan disimpan statis |
| Tombol | "Lihat Detail" (dan/atau tombol cepat "Tambah ke Keranjang" jika stok tersedia) |

**Perilaku saat stok habis:** kartu tetap tampil (bukan disembunyikan, agar user tahu barang eksis) tapi tombol beli/tambah-ke-keranjang **dinonaktifkan**.

### 6.2 Detail Produk
- Gambar produk (lebih besar)
- **Breadcrumb kategori:** `Product > [Kategori Induk] > [Kategori Anak]` (misal `Product > Pipa Stainless > Pipa Stainless 3x2`) — memudahkan user lompat balik ke listing kategori terkait, sekaligus membantu SEO
- Nama, kategori, deskripsi lengkap
- Harga per satuan
- Status stok (sama seperti di kartu, lebih detail: sisa stok bisa ditampilkan atau cukup badge saja — keputusan UX, disarankan tampilkan badge saja untuk hindari kesan "war stok")
- **Input kuantitas** — mendukung **desimal** untuk satuan kg/m (misal 10.5 kg), integer-only untuk pcs (divalidasi di form)
- Validasi input: kuantitas tidak boleh melebihi stok saat ini (dicek juga ulang di server saat checkout, karena stok bisa berubah)
- Tombol "Tambah ke Keranjang"
- Notifikasi kecil (toast) setelah berhasil ditambahkan, tanpa harus pindah halaman

---

## 7. Halaman: About Us

**Tujuan:** membangun kepercayaan (siapa CV BERKAH, sudah berapa lama, di mana lokasinya).

**Konten (semua ditarik dari tabel `settings`, tidak hardcode):**
- Profil perusahaan / sejarah singkat (`company_description`)
- Alamat (`address`)
- Kontak: email (`email`), No. WA (`wa_number`)
- Jam operasional (`operating_hours`)
- (Opsional) peta lokasi sederhana (embed Google Maps gratis berdasarkan alamat — tidak butuh API berbayar untuk sekadar embed iframe)

---

## 8. Keranjang & Checkout

### 8.1 Keranjang (Cart)
- Berbasis **session** (bukan tabel database, bukan butuh login) — sesuai requirement awal "tanpa checkout online"
- Mendukung banyak item sekaligus, masing-masing dengan kuantitas desimal jika relevan
- Halaman Keranjang menampilkan: daftar item (gambar mini, nama, qty yang bisa diubah langsung, subtotal), tombol hapus per item, total estimasi keseluruhan
- Perubahan qty di halaman keranjang langsung update subtotal & total (client-side untuk UX cepat, tapi **divalidasi ulang di server** saat checkout)
- Ikon keranjang di navbar menampilkan jumlah item (badge angka), terlihat di semua halaman publik

### 8.2 Checkout
**Form checkout:**
| Field | Wajib | Keterangan |
|---|---|---|
| Nama | Ya | |
| No. HP | Ya | Untuk dihubungi admin via WA |
| Alamat | Tidak | Opsional, berguna untuk estimasi pengambilan/pengiriman |
| Metode Pembayaran | Ya | Cash / Transfer — **informatif saja**, tidak ada proses pembayaran online |

**Alur saat submit (server-side, wajib berurutan):**
1. **Validasi ulang stok** setiap item terhadap `products.stock` saat ini (bukan hanya cek di frontend saat item ditambahkan — stok bisa berubah sejak saat itu)
2. Jika ada item yang stoknya sudah tidak cukup/habis, tampilkan pesan jelas per item yang bermasalah, **belum simpan apapun**, user diminta sesuaikan keranjang dulu
3. Jika semua valid, simpan ke database dalam satu `DB::transaction()`:
   - Insert `orders` (nomor order otomatis/`order_number`, status = `pending`, data customer, total estimasi)
   - Insert `order_items` (per item: produk, qty, `unit_price_snapshot` dari harga saat itu, subtotal)
4. **Setelah tersimpan**, redirect ke WhatsApp (`wa.me` link) dengan template pesan berisi: nomor order, daftar barang beserta qty & satuan, total estimasi
5. User tetap menekan tombol "Kirim" secara manual di aplikasi WhatsApp — **tidak ada auto-send** (sesuai requirement awal)
6. Setelah redirect, session keranjang dikosongkan (karena sudah tersimpan permanen sebagai order)

**Catatan penting:** stok produk **tidak berkurang saat checkout ini** — stok baru berkurang saat admin menandai order sebagai "Selesai" di panel admin (lihat PRD Admin, Bagian 10). Ini mencegah stok "hilang" akibat order yang ujungnya batal/tidak jadi dihubungi.

### 8.3 Contoh Template Pesan WhatsApp (Ilustratif)
```
Halo, saya ingin memesan (Order #ORD-2026-00123):
1. Besi Siku 5cm — 25 kg
2. Plat Baja 3mm — 10 pcs
Total estimasi: Rp 1.250.000

Nama: [nama customer]
No. HP: [no hp]
Alamat: [alamat, jika diisi]
```
*(Format persis disesuaikan saat implementasi; poin pentingnya nomor order + rincian barang + total selalu ada.)*

---

## 9. Kebutuhan Non-Fungsional Khusus Sisi Publik

- **Mobile-first:** breakpoint dan layout dirancang untuk HP terlebih dahulu, baru diperluas ke tablet/desktop
- **Desain:** minimalis, clean, tanpa animasi berat — konsisten dengan seluruh sistem
- **Performa:**
  - Query katalog memakai eager loading (`with()`) untuk hindari N+1, terutama saat menampilkan kategori + status stok bersamaan
  - Gambar produk terkompresi otomatis (Intervention Image), lazy-load di grid katalog
  - Halaman statis (Home, About) memanfaatkan Laravel view/route caching di production
- **SEO dasar:** setiap halaman punya title & meta description yang relevan (khususnya Home, Product per kategori, dan detail produk) — penting karena ini adalah kanal akuisisi pengunjung baru
- **Aksesibilitas dasar:** kontras warna cukup untuk badge status stok, ukuran tombol cukup besar untuk tap di mobile
- **Keamanan:**
  - CSRF protection di form checkout
  - Validasi input ketat (Form Request) mencegah XSS/SQL Injection
  - Rate limiting pada endpoint checkout (cegah spam order otomatis/bot)
- **Tanpa dependensi berat:** tidak ada fitur publik yang butuh Redis, queue, atau API pihak ketiga berbayar — selaras strategi hemat biaya PRD Umum Bagian 8

---

## 10. Skema Data Terkait Sisi Publik (Ringkasan)

| Sumber Data | Dipakai di |
|---|---|
| `settings` | Home (opsional), About Us, footer, template WA |
| `categories` (`parent_id` untuk struktur induk-anak) | Filter berjenjang & breadcrumb di halaman Product |
| `products` (`stock`, `low_stock_threshold`, `sell_price`, `unit`, dst) | Kartu produk, detail produk, validasi keranjang & checkout |
| Session (`cart`) | Keranjang — tidak tersimpan di database sampai checkout |
| `orders`, `order_items` | Dibuat saat checkout berhasil (status awal selalu `pending`) |

*(Field detail per tabel mengikuti skema lengkap di PRD Umum Bagian 6 — tidak diulang di sini agar tidak duplikat/berisiko tidak sinkron.)*

---

## 11. Alur Pengguna End-to-End (User Flow)

```
Pengunjung buka Home
      │
      ▼
Klik "Lihat Produk" / kategori highlight
      │
      ▼
Halaman Product → filter/cari produk
      │
      ▼
Klik produk → Detail Produk → input qty → "Tambah ke Keranjang"
      │
      ├──► (opsional) lanjut belanja produk lain
      │
      ▼
Buka Keranjang → review item & qty → "Checkout"
      │
      ▼
Isi form (Nama, No HP, Alamat opsional, Metode Bayar)
      │
      ▼
Submit → validasi stok server
      │
   ┌──┴───────────────┐
   ▼                  ▼
Stok tidak cukup   Semua valid
(tampilkan error,  → simpan `orders` + `order_items`
kembali ke cart)   → redirect WhatsApp dengan template
                        │
                        ▼
                 User tekan "Kirim" manual di WA
                        │
                        ▼
        (Selesai di sisi user — follow-up lanjut
         ditangani admin, lihat PRD Admin Bagian 10)
```

---

## 12. Kriteria Selesai (Acceptance Criteria) — Ringkas

- [ ] Halaman Home, Product, dan About Us bisa diakses tanpa login dan tampil benar di mobile & desktop
- [ ] Filter kategori berjenjang (induk & anak) dan search produk berfungsi, bisa dikombinasikan, dan tidak menyebabkan N+1 query
- [ ] Memfilter kategori induk menampilkan gabungan produk dari seluruh kategori anak di bawahnya; breadcrumb di halaman detail produk menampilkan induk > anak dengan benar
- [ ] Status stok (Tersedia/Menipis/Habis) di kartu & detail produk selalu mencerminkan data terbaru dari server
- [ ] Input qty di detail produk mendukung desimal untuk kg/m dan menolak desimal untuk pcs
- [ ] Tombol beli/tambah-ke-keranjang otomatis nonaktif saat stok habis
- [ ] Keranjang berbasis session berfungsi tanpa login, mendukung banyak item dan qty desimal
- [ ] Saat checkout, validasi stok dilakukan ulang di server (bukan hanya percaya data di keranjang/frontend)
- [ ] Order tersimpan ke `orders` + `order_items` dengan status `pending` **sebelum** redirect ke WhatsApp
- [ ] Redirect WhatsApp berisi template lengkap: nomor order, daftar barang, total estimasi — dan tidak auto-send
- [ ] Stok produk **tidak berkurang** pada saat checkout (baru berkurang saat admin proses order jadi "Selesai")
- [ ] Semua konten non-produk (kontak, alamat, jam operasional) bisa diubah lewat Settings admin tanpa sentuh kode
- [ ] Halaman publik tetap ringan/cepat diakses di skala 500–1.000 pengunjung/bulan tanpa Redis/queue

---

## 13. Catatan Roadmap (Tidak Dikerjakan di Fase 1)

- Registrasi/login member untuk pelanggan tetap (B2B) — lihat PRD Umum Bagian 10
- Payment gateway otomatis (Midtrans/Xendit)
- Riwayat pesanan yang bisa dilihat user sendiri (butuh akun member terlebih dulu)
- Live chat di website (saat ini cukup redirect WhatsApp)
- Notifikasi otomatis ke user (email/SMS) saat status order berubah — di fase 1 follow-up sepenuhnya manual via WA oleh admin