# Product Requirements Document (PRD) — Fitur Tambahan
**Proyek:** CV BERKAH — Website Company Profile & Mini ERP Gudang-Kasir
**Versi:** 1.0
**Tanggal:** Juli 2026
**Dokumen ini melengkapi:** PRD Umum v4, PRD User v1.1, PRD Admin v1.0, PRD Gudang & Kasir
**Cakupan:** 5 fitur baru/revisi — Chatbot FAQ, Produk Unggulan di Home, Revisi UI/UX Mobile (User & Admin), Detail Produk ala Shopee, Penyesuaian Home (hapus konsep "Produk Baru")

---

## 1. Ringkasan Perubahan

| # | Fitur | Sisi | Sifat |
|---|---|---|---|
| 1 | Chatbot Template Q&A + eskalasi ke WA CS | User | Baru |
| 2 | Produk Unggulan interaktif di Home | User | Revisi dari "Highlight Kategori" |
| 3 | Revisi besar UI/UX Mobile | User & Admin | Revisi menyeluruh |
| 4 | Detail Produk ala Shopee (klik → deskripsi lengkap + tambah keranjang) | User | Revisi dari alur Detail Produk sebelumnya |
| 5 | Home tanpa fitur "Produk Baru" | User | Penegasan scope — konsisten dengan #2 |

> **Catatan konsistensi biaya:** seluruh fitur di dokumen ini dirancang **tanpa API pihak ketiga berbayar** (chatbot rule-based, bukan LLM API), sesuai prinsip hemat biaya di PRD Umum Bagian 8.

---

## 2. Fitur 1 — Chatbot Template Q&A

### 2.1 Tujuan
Menjawab pertanyaan umum calon pembeli secara instan (jam buka, cara pesan, lokasi, dll) tanpa admin harus standby terus di WA, tapi tetap mengarahkan ke WA CS untuk pertanyaan yang lebih spesifik/di luar template.

### 2.2 Prinsip Desain
- **Rule-based, bukan AI/LLM** — chatbot menjawab dari daftar Tanya-Jawab yang dikelola admin, dicocokkan lewat pilihan tombol (quick reply) dan/atau pencocokan kata kunci sederhana dari teks bebas. **Tidak menggunakan API AI berbayar** — sejalan dengan strategi hemat biaya.
- Chatbot **tidak menggantikan** CS manusia — perannya sebagai penyaring pertanyaan umum, bukan customer service penuh.

### 2.3 Perilaku & Alur

**Tampilan:**
- Widget bubble melayang (floating button) di pojok kanan bawah, muncul di semua halaman publik
- Di mobile, posisi widget **wajib disesuaikan** agar tidak bertabrakan dengan sticky bar "Tambah ke Keranjang" di halaman Detail Produk (lihat Fitur 3 & 4) — misal digeser ke atas sticky bar, atau disembunyikan sementara saat sticky bar tampil dan muncul lagi saat discroll ke atas

**Alur percakapan:**
1. User klik ikon chatbot → muncul panel chat dengan pesan sambutan + **daftar pertanyaan cepat** (quick reply buttons), contoh:
   - "Jam operasional toko?"
   - "Bagaimana cara memesan?"
   - "Apakah bisa COD/diantar?"
   - "Cara pembayaran?"
   - "Lokasi toko di mana?"
2. User klik salah satu tombol → chatbot langsung menampilkan jawaban template yang sudah diset admin
3. User juga bisa mengetik pertanyaan bebas di kolom teks:
   - Sistem mencocokkan teks dengan **kata kunci (keywords)** yang terhubung ke tiap FAQ (misal keyword "jam", "buka", "tutup" → cocok ke FAQ "Jam Operasional")
   - Jika ditemukan kecocokan → tampilkan jawaban template terkait
   - Jika **tidak ditemukan kecocokan** → tampilkan pesan: *"Maaf, pertanyaan Anda belum bisa saya jawab. Silakan hubungi CS kami langsung"* + tombol **"Chat CS via WhatsApp"** (redirect `wa.me` dengan pesan pre-filled, misal berisi pertanyaan yang tadi diketik agar admin langsung paham konteksnya)
4. Setelah jawaban tampil, chatbot menawarkan lagi: "Ada yang bisa saya bantu lagi?" dengan quick reply + opsi "Hubungi CS" tetap selalu tersedia sebagai fallback kapan pun

### 2.4 Manajemen FAQ oleh Admin
Menu baru di panel admin: **Chatbot** (`/admin/chatbot`)

**Daftar FAQ:**
- Tabel: Pertanyaan (label tombol), Kata Kunci, Urutan Tampil, Status (Aktif/Nonaktif), Aksi
- Tombol "+ Tambah FAQ"

**Form Tambah/Edit FAQ:**
| Field | Tipe | Keterangan |
|---|---|---|
| Judul Pertanyaan | text | Ditampilkan sebagai tombol quick reply |
| Kata Kunci (Keywords) | text (comma-separated) | Untuk pencocokan teks bebas, misal `jam, buka, tutup, operasional` |
| Jawaban | textarea | Bisa multi-baris, mendukung teks sederhana (tanpa perlu rich editor berat) |
| Urutan | number | Menentukan urutan tombol quick reply |
| Status | toggle | Aktif/Nonaktif tanpa perlu hapus data |

**Log Pertanyaan Tidak Terjawab** *(fitur pendukung penting)*
- Halaman `/admin/chatbot/unanswered` — daftar pertanyaan bebas dari user yang tidak cocok dengan FAQ manapun
- Tujuan: admin bisa lihat pola pertanyaan yang sering muncul tapi belum ada template-nya, lalu buat FAQ baru — chatbot jadi makin lengkap dari waktu ke waktu tanpa biaya tambahan

### 2.5 Skema Database

| Tabel | Field Utama |
|---|---|
| `chatbot_faqs` | id, question_title, keywords, answer, sort_order, is_active |
| `chatbot_unanswered_logs` | id, question_text, created_at |

### 2.6 Kebutuhan Non-Fungsional
- Widget chatbot **client-side ringan** (Alpine.js/vanilla JS), tidak reload halaman
- Pencocokan keyword dilakukan di server via endpoint ringan (`POST /chatbot/ask`), query sederhana `LIKE`/`whereJsonContains` terhadap kata kunci — tidak butuh search engine terpisah (Elasticsearch dsb) yang mahal & berat untuk skala ini
- Tidak menyimpan riwayat percakapan penuh per user (tidak perlu login) — cukup log pertanyaan tak terjawab demi privasi & kesederhanaan sistem

---

## 3. Fitur 2 — Produk Unggulan Interaktif di Home

### 3.1 Tujuan
Mengganti/menyempurnakan bagian "Highlight Kategori" di Home menjadi satu area **Produk Unggulan** yang lebih actionable — user bisa langsung tertarik ke produk spesifik, bukan cuma kategori umum.

### 3.2 Sumber Data
- Tambahan field `is_featured` (boolean) di tabel `products` — admin menandai produk mana yang ingin ditampilkan di Home
- **Bukan otomatis dari "produk terbaru"** — murni kurasi manual admin (lihat Fitur 5), agar admin bisa strategis menonjolkan produk yang stoknya banyak/ingin didorong penjualannya
- Admin mengatur ini dari halaman **Manajemen Produk** (PRD Admin Bagian 6) — tambahan toggle "Tampilkan di Produk Unggulan" pada form Tambah/Edit Produk, plus urutan tampil (`featured_order`, opsional)

### 3.3 Perilaku Tampilan & Interaksi
Section "Produk Unggulan" di Home menampilkan grid kartu produk (bukan kartu kategori generik). Tiap kartu berperilaku seperti kartu produk di halaman Product (lihat Fitur 4):

| Aksi User | Hasil |
|---|---|
| Klik/tekan **kartu produk** (gambar/nama) | Diarahkan ke **halaman Detail Produk** (`/product/{slug}`) — sama seperti klik dari katalog biasa |
| Klik tombol cepat **"+ Keranjang"** pada kartu (jika stok tersedia) | Produk **langsung ditambahkan ke keranjang** tanpa pindah halaman (dengan qty default = 1 atau minimum satuan), notifikasi toast muncul |
| Klik **label kategori** pada kartu (misal chip kecil "Besi Siku" di bawah nama produk) | Diarahkan ke **halaman Product, terfilter otomatis** ke kategori anak tsb (dan otomatis mewarisi filter kategori induknya juga, sesuai struktur 2 tingkat di PRD User Bagian 6.1) |

> Dengan begini, satu kartu produk unggulan punya 3 jalan pintas: langsung lihat detail, langsung beli cepat, atau eksplor kategori terkait — sesuai kebutuhan "bisa langsung order atau diarahkan ke halaman produk dengan kategori/induk sesuai yang ditekan".

### 3.4 Kondisi Kosong (Empty State)
- Jika admin belum menandai produk unggulan apapun, section ini **tidak ditampilkan** di Home (bukan tampil kosong/placeholder aneh) — Home tetap rapi

### 3.5 Batasan Jumlah
- Tampilkan maksimal **8 produk unggulan** di Home (grid 2 kolom mobile / 4 kolom desktop) — jika admin menandai lebih dari itu, urutkan berdasarkan `featured_order` dan ambil yang teratas, dengan tombol "Lihat Semua Produk" di akhir section mengarah ke halaman Product

---

## 4. Fitur 3 — Revisi Besar UI/UX Mobile (User & Admin)

### 4.1 Latar Belakang
PRD sebelumnya sudah menyebut "mobile-first"/"responsif" sebagai kebutuhan non-fungsional umum, namun belum cukup rinci. Karena mayoritas user (pembeli) dan bahkan admin (saat cek stok di gudang) mengakses dari HP, bagian ini menjadi **revisi menyeluruh**, bukan sekadar tambahan.

### 4.2 Breakpoint Standar
| Breakpoint | Rentang | Target Device |
|---|---|---|
| Mobile | < 640px | HP (prioritas utama desain) |
| Tablet | 640px – 1024px | Tablet / HP landscape |
| Desktop | > 1024px | Laptop/PC |

Desain dibangun **mobile-first**: style dasar untuk mobile, lalu di-*enhance* progresif ke breakpoint lebih besar (bukan sebaliknya).

### 4.3 Revisi UI/UX — Sisi User (Publik)

| Area | Perubahan |
|---|---|
| Navigasi utama | Navbar atas disederhanakan (logo + ikon keranjang + ikon menu hamburger); tambahkan **bottom navigation bar** khas mobile-commerce (Home, Product, Chat/WA, Keranjang) agar navigasi 1 jempol tanpa perlu scroll ke atas |
| Grid produk | 2 kolom di mobile (bukan 1 kolom penuh yang boros scroll, bukan juga dipaksa 3–4 kolom yang bikin teks kepencet) |
| Filter kategori | Di mobile, filter kategori (induk & anak) memakai **bottom-sheet/drawer** yang muncul dari bawah saat tombol "Filter" ditekan — bukan sidebar yang memakan ruang layar sempit |
| Detail Produk | Sticky bottom bar berisi tombol "Chat" (WA cepat) dan "Tambah ke Keranjang" selalu terlihat walau discroll (lihat Fitur 4) |
| Keranjang | List item full-width dengan tombol qty (+/–) berukuran cukup besar untuk jempol (minimal 44×44px area tap) |
| Form Checkout | Input field besar, keyboard number otomatis muncul untuk No. HP, label jelas di atas field (bukan placeholder-only yang hilang saat diketik) |
| Chatbot widget | Ukuran & posisi disesuaikan agar tidak menutupi sticky bar (lihat Fitur 1 & 4) |

### 4.4 Revisi UI/UX — Sisi Admin

| Area | Perubahan |
|---|---|
| Sidebar | Berubah jadi **drawer/off-canvas** yang tersembunyi default di mobile, dibuka lewat ikon hamburger; opsi tambahan: bottom nav dengan ikon shortcut untuk menu paling sering dipakai (Dashboard, Kasir, Pesanan, Gudang) |
| Tabel data | Di mobile, tabel **tidak dipaksa horizontal-scroll sebagai solusi utama** (revisi dari versi sebelumnya) — beralih ke **tampilan kartu (card list)** per baris data untuk tabel-tabel utama (Daftar Produk, Riwayat Transaksi, Daftar Order), menampilkan info paling penting saja, dengan tombol "Detail" untuk info lengkap |
| Halaman Kasir (POS) | Redesain khusus mobile: karena layout 2 panel (produk \| keranjang) tidak muat berdampingan di HP, jadi **1 kolom dengan tab/toggle** — "Pilih Produk" dan "Keranjang" sebagai 2 tab, keranjang menampilkan badge jumlah item di tab-nya |
| Form Tambah/Edit Produk | Input dikelompokkan per section dengan spacing cukup, upload gambar dengan preview besar & tombol ganti yang jelas |
| Stock Opname | Tabel input stok fisik massal — di mobile, tetap tabel tapi dengan kolom diminimalkan (Nama Produk, Stok Sistem kecil di bawah nama, 1 input Stok Fisik) agar tidak perlu scroll horizontal |
| Notifikasi & badge | Ukuran badge/notifikasi disesuaikan agar tetap terbaca jelas di layar kecil, tidak menumpuk dengan ikon lain |
| Touch target | Semua tombol aksi (Edit/Hapus/Void dsb) minimal area tap 44×44px, dengan jarak antar tombol cukup untuk menghindari salah tekan |

### 4.5 Prinsip Umum Revisi
- **Konten prioritas dulu, dekorasi belakangan** — di layar sempit, informasi paling penting (harga, stok, status) harus terlihat tanpa scroll berlebihan
- **Hindari modal bertumpuk** — di mobile, gunakan halaman/bottom-sheet penuh alih-alih modal kecil di tengah layar yang sulit dioperasikan dengan jempol
- **Konsisten tanpa menambah beban library** — revisi ini murni di level CSS/komponen Tailwind + Alpine.js, tidak menambah dependensi JS besar baru (tetap selaras strategi hemat biaya & performa PRD Umum)

---

## 5. Fitur 4 — Detail Produk ala Shopee

### 5.1 Tujuan
Mengganti pengalaman "detail produk sederhana" menjadi pola yang sudah familiar bagi user Indonesia (ala Shopee/marketplace) — begitu produk ditekan, langsung tampil halaman detail lengkap dengan aksi cepat tambah ke keranjang.

### 5.2 Perilaku Navigasi
- Menekan/klik **kartu produk** di manapun (Home Produk Unggulan, halaman Product, hasil search) → **selalu membuka halaman Detail Produk penuh** (`/product/{slug}`), **bukan** modal kecil/popup — konsisten dan mudah di-share linknya, juga lebih baik untuk SEO

### 5.3 Struktur Halaman Detail Produk (Revisi)

```
┌─────────────────────────────┐
│  ← Kembali      [ikon share]│  ← header mobile, sticky saat scroll
├─────────────────────────────┤
│                             │
│      Galeri Gambar Produk   │  ← gambar utama, swipe jika ada >1 gambar (opsional multi-gambar, lihat 5.5)
│                             │
├─────────────────────────────┤
│ Nama Produk                 │
│ Rp 8.500 / kg                │  ← harga + satuan jelas
│ 🟢 Tersedia (Stok: cukup)    │  ← badge status stok
│ Breadcrumb: Product > Besi > │
│             Besi Siku        │
├─────────────────────────────┤
│ Deskripsi Produk             │
│ [teks deskripsi lengkap,     │
│  bisa "Lihat Selengkapnya"   │
│  jika panjang]                │
├─────────────────────────────┤
│ Produk Lain dari Kategori Ini│  ← rekomendasi ringan (opsional, lihat 5.6)
└─────────────────────────────┘
┌─────────────────────────────┐
│ [Stepper Qty]  [+ Keranjang] │  ← STICKY BOTTOM BAR, selalu terlihat
│  atau: [Chat CS] [+ Keranjang]│
└─────────────────────────────┘
```

### 5.4 Komponen Detail

| Komponen | Perilaku |
|---|---|
| Galeri Gambar | Minimal 1 gambar wajib (dari `products.image`); dukungan multi-gambar bersifat opsional (lihat 5.5) |
| Nama & Harga | Format Rupiah jelas, satuan selalu ditampilkan mengikuti harga (misal "/ kg", "/ pcs", "/ m") |
| Badge Status Stok | Tersedia (hijau) / Menipis (kuning) / Habis (merah) — sama seperti kartu produk, dihitung realtime dari server |
| Breadcrumb Kategori | `Product > [Induk] > [Anak]`, tiap bagian bisa diklik untuk lompat ke listing terfilter (konsisten dengan PRD User Bagian 6.2) |
| Deskripsi | Teks dari `products.description`; jika lebih dari ~150 karakter, dipotong dengan tombol "Lihat Selengkapnya" (expand in-place, tanpa reload) |
| Stepper Kuantitas | Tombol −/+ dan input angka langsung; desimal diaktifkan otomatis jika satuan produk kg/m, dinonaktifkan (integer-only) jika satuan pcs — validasi tidak boleh melebihi stok tersedia |
| Sticky Bottom Bar | Selalu terlihat di layar (mobile maupun desktop) berisi: stepper qty ringkas + tombol utama **"Tambah ke Keranjang"**; jika stok habis, tombol berubah jadi disabled dengan label "Stok Habis" dan opsi "Chat CS" untuk tanya ketersediaan berikutnya |
| Tombol Chat CS | Shortcut ke WA admin dengan pesan pre-filled berisi nama produk yang sedang dilihat (memudahkan tanya stok/nego tanpa lewat chatbot dulu) |

### 5.5 Multi-Gambar Produk *(opsional, catatan skop)*
Skema saat ini (`products.image`, 1 kolom) hanya mendukung 1 gambar per produk. Jika ingin galeri multi-gambar ala Shopee sepenuhnya, dibutuhkan tabel tambahan `product_images` (id, product_id, image_path, sort_order). **Ini dicatat sebagai opsi peningkatan**, bukan wajib di fase 1 — 1 gambar per produk sudah cukup untuk kebutuhan katalog besi tua yang relatif standar per jenis barang, dan meminimalkan storage/biaya hosting. Keputusan akhir: mulai dengan 1 gambar, upgrade ke galeri jika kebutuhan bisnis berkembang.

### 5.6 Rekomendasi "Produk Lain dari Kategori Ini" *(opsional)*
Menampilkan 4 produk lain dari kategori anak yang sama (query sederhana `WHERE category_id = ... AND id != current LIMIT 4`) — meningkatkan eksplorasi produk tanpa kompleksitas algoritma rekomendasi yang berat/mahal.

### 5.7 Alur Tambah ke Keranjang dari Detail Produk
1. User atur qty via stepper (default 1, atau minimum sesuai satuan)
2. Klik "Tambah ke Keranjang" pada sticky bar
3. Validasi client-side (qty ≤ stok tersedia, format sesuai satuan) → jika lolos, item masuk session cart
4. Toast konfirmasi muncul ("Berhasil ditambahkan ke keranjang"), badge ikon keranjang di navbar/bottom-nav bertambah
5. User tetap di halaman yang sama (tidak dipaksa pindah ke halaman keranjang) — konsisten pola marketplace, user bisa lanjut browsing produk lain

---

## 6. Fitur 5 — Penegasan: Home Tanpa "Produk Baru"

### 6.1 Keputusan
Home **tidak akan** memiliki section terpisah "Produk Baru/Terbaru" (misal berbasis `created_at` terbaru). Section **Produk Unggulan (Fitur 2)** sudah memenuhi peran menonjolkan produk di Home — dan sifatnya **kuratif oleh admin**, bukan otomatis kronologis.

### 6.2 Alasan
- Menghindari duplikasi tujuan (dua section yang fungsinya tumpang tindih membingungkan user & menambah beban query/halaman)
- Admin lebih diuntungkan dengan kontrol manual — produk yang stoknya menumpuk/ingin didorong penjualannya bisa ditonjolkan, bukan sekadar "produk yang paling baru diinput ke sistem" yang belum tentu relevan secara bisnis

### 6.3 Dampak ke Struktur Home (Revisi Final)
```
Home
├── Hero Section
├── Produk Unggulan (Fitur 2)         ← menggantikan "Highlight Kategori" versi lama
├── Kenapa Pilih Kami (opsional, teks dari settings)
└── CTA Penutup
```
> Section "Highlight Kategori" versi lama di PRD User Bagian 5 **resmi digantikan** oleh Produk Unggulan yang lebih actionable (Fitur 2) — bukan ditambahkan sebagai section terpisah, untuk menjaga Home tetap ringkas dan tidak membingungkan.

---

## 7. Dampak ke Skema Database (Ringkasan Perubahan)

| Tabel | Perubahan |
|---|---|
| `products` | + `is_featured` (boolean, default false), + `featured_order` (integer, nullable) |
| `chatbot_faqs` | **Tabel baru** — id, question_title, keywords, answer, sort_order, is_active |
| `chatbot_unanswered_logs` | **Tabel baru** — id, question_text, created_at |
| `product_images` | *(opsional, tidak wajib fase 1)* — lihat Bagian 5.5 |

---

## 8. Dampak ke Panel Admin (Ringkasan Perubahan)

| Menu Admin | Perubahan |
|---|---|
| Manajemen Produk | + Toggle "Tampilkan di Produk Unggulan" + field urutan tampil pada form Tambah/Edit |
| Chatbot *(menu baru)* | CRUD FAQ + halaman Log Pertanyaan Tidak Terjawab |
| Seluruh halaman admin | Revisi tampilan mobile sesuai Bagian 4.4 (drawer sidebar, tabel jadi card list, redesain halaman Kasir) |

---

## 9. Kriteria Selesai (Acceptance Criteria)

**Chatbot:**
- [ ] Widget chatbot tampil di semua halaman publik, tidak menutupi sticky bar Detail Produk
- [ ] Quick reply menampilkan FAQ aktif sesuai urutan yang diatur admin
- [ ] Pertanyaan bebas dicocokkan lewat keyword; jika tidak cocok, tampil fallback + tombol WA CS
- [ ] Pertanyaan tak terjawab tercatat di log dan bisa dilihat admin
- [ ] Admin bisa CRUD FAQ tanpa menyentuh kode

**Produk Unggulan di Home:**
- [ ] Admin bisa menandai produk sebagai unggulan + atur urutan tampil
- [ ] Section tidak muncul jika belum ada produk ditandai
- [ ] Klik kartu produk → ke Detail Produk; klik "+Keranjang" → langsung tambah ke keranjang; klik label kategori → ke listing terfilter (induk/anak sesuai kategori produk tsb)
- [ ] Maksimal 8 produk tampil, sisanya lewat "Lihat Semua Produk"

**Revisi Mobile:**
- [ ] Seluruh halaman publik & admin diuji di breakpoint mobile (< 640px) tanpa elemen terpotong/overflow horizontal tak disengaja
- [ ] Navigasi utama publik memakai bottom nav di mobile
- [ ] Sidebar admin jadi drawer di mobile, tabel utama jadi card list
- [ ] Halaman Kasir (POS) punya layout khusus mobile (tab Produk/Keranjang)
- [ ] Semua tombol aksi memenuhi area tap minimum 44×44px

**Detail Produk ala Shopee:**
- [ ] Klik produk dari manapun selalu membuka halaman detail penuh (bukan modal)
- [ ] Sticky bottom bar dengan stepper qty + tombol Tambah ke Keranjang selalu terlihat saat scroll
- [ ] Breadcrumb kategori berfungsi & bisa diklik
- [ ] Deskripsi panjang bisa di-expand tanpa reload
- [ ] Validasi qty sesuai satuan (desimal untuk kg/m, integer untuk pcs) tetap berlaku

**Home tanpa Produk Baru:**
- [ ] Tidak ada section "Produk Baru/Terbaru" di Home
- [ ] Section "Highlight Kategori" lama sudah digantikan sepenuhnya oleh Produk Unggulan

---

## 10. Roadmap / Tidak Termasuk Fase Ini

- Multi-gambar produk (galeri) — lihat Bagian 5.5, jadi enhancement terpisah
- Chatbot berbasis AI/LLM (saat ini rule-based) — bisa dipertimbangkan di masa depan jika volume pertanyaan kompleks meningkat signifikan, dengan evaluasi biaya API terlebih dahulu
- Rekomendasi produk berbasis algoritma (saat ini hanya query sederhana per kategori)
- Push notification mobile (saat ini semua notifikasi hanya di dalam dashboard/website)

---

## 11. Prioritas Pengembangan Fitur Ini

1. Revisi UI/UX Mobile — fondasi (banyak fitur lain bergantung pada layout baru ini, terutama sticky bar & card list)
2. Detail Produk ala Shopee (termasuk sticky bottom bar)
3. Produk Unggulan di Home (bergantung pada pola kartu produk yang sudah direvisi di poin 2)
4. Penyesuaian struktur Home (hapus "Produk Baru", pastikan section final sesuai Bagian 6.3)
5. Chatbot FAQ (fitur paling independen, bisa dikerjakan paralel dengan tim/waktu terpisah)