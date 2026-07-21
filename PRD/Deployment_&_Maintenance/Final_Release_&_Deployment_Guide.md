# CV Berkah - Final Release & Deployment Guide

Dokumen ini merangkum status final dari pengembangan aplikasi (v1.0), rekomendasi fitur lanjutan & optimasi SEO/Keamanan (SSL), serta panduan komprehensif untuk melakukan *hosting* dan *deployment* aplikasi Laravel ini ke server produksi.

---

## 1. Status Rilis Saat Ini (Versi Final 1.0)
Aplikasi telah menyelesaikan seluruh fase pengembangan berdasarkan dokumen PRD awal hingga PRD fitur tambahan. Kapabilitas yang sudah siap beroperasi di produksi meliputi:
1. **E-commerce B2B:** Katalog produk dinamis dengan sistem pencarian dan keranjang tanpa login.
2. **Checkout via WhatsApp:** Transformasi otomatis pesanan menjadi format pesan WA.
3. **Point of Sales (POS):** Sistem kasir cerdas dengan *shortcut* kuantitas otomatis sesuai satuan barang (Pcs, Kg, Meter).
4. **Manajemen Gudang Tingkat Lanjut:** Mutasi stok masuk, keluar, dan **Stock Opname** yang dilindungi oleh *Atomic Database Transactions* (Anti-Bocor Stok).
5. **Asisten Chatbot AI:** Widget interaktif pintar dengan respons *Quick Reply* dan fitur *fallback* otomatis ke CS WhatsApp.
6. **Mobile-Optimized UI:** Antarmuka responsif penuh dengan navigasi jempol (*bottom bar navigation*), layout *drawer*, dan desain TailwindCSS khusus industri.
7. **Laporan & Ekspor:** Rekapitulasi laporan penjualan, laba rugi, dan riwayat stok ke format Excel.

---

## 2. Rencana Pengembangan Lanjutan (Roadmap & Fixes)

Untuk fase selanjutnya, pengembangan harus difokuskan pada visibilitas publik, keamanan lalu lintas data, dan skalabilitas.

### A. Keamanan & SEO (Prioritas Utama untuk Google)
Agar website CV Berkah mudah ditemukan di halaman pertama Google, langkah berikut wajib dilakukan saat website sudah di-hosting:
*   **Instalasi SSL (HTTPS):** Website e-commerce wajib menggunakan SSL. Jika tidak, Google Chrome akan memberikan label peringatan **"Not Secure"** yang membuat calon pembeli takut. HTTPS juga menjadi salah satu faktor penentu utama ranking di Google (SEO).
*   **Paksa HTTPS di Laravel:** Setelah SSL aktif di server, pastikan Laravel memaksa semua *traffic* ke HTTPS dengan menambahkan kode ini di `app/Providers/AppServiceProvider.php` bagian `boot()`: 
    `\Illuminate\Support\Facades\URL::forceScheme('https');`
*   **Meta Tags & Open Graph:** Optimasi judul web dan deskripsi yang kaya kata kunci (seperti: "Pabrikasi Besi", "Jual Pipa Stainless Murah").
*   **Sitemap & Google Search Console:** Buat `sitemap.xml` dan daftarkan domain website ke Google Search Console agar Google segera "merayapi" (crawl) katalog produk Anda.

### B. Optimasi Performa
*   **Implementasi Redis:** Jika pengunjung website mulai membeludak, pindahkan sistem Session dan Cache Laravel dari `file` ke memori `Redis`.
*   **CDN Gambar:** Menggunakan layanan seperti Cloudflare agar aset gambar besi/produk dimuat dengan cepat di seluruh Indonesia tanpa membebani server lokal.

---

## 3. Panduan Hosting dan Deployment

Aplikasi ini bisa di-deploy menggunakan layanan **Shared Hosting (cPanel)** yang terjangkau, atau menggunakan **VPS** untuk performa maksimal. Berikut panduannya.

### Opsi 1: Deployment ke Shared Hosting (cPanel) - Disarankan untuk Awal

**Syarat Hosting:** Harus mendukung PHP 8.2/8.3, MySQL 8+, dan akses Terminal/SSH.

1. **Persiapan Data di Komputer Lokal:**
   * Hapus folder `node_modules` dan `vendor` untuk menghemat ukuran (ini akan diinstall di server).
   * Blok seluruh file/folder proyek `CV-Berkah-Fabrikasi-Besi` lalu compress menjadi format `.zip`.
   * Export database `cv_berkah` dari phpMyAdmin komputer Anda menjadi file `.sql`.

2. **Upload ke cPanel:**
   * Login ke cPanel, buka **File Manager**.
   * Jangan upload langsung ke `public_html`. Buat folder baru sejajar dengan `public_html` (misal: `/home/username_anda/cv_berkah_app`).
   * Upload file `.zip` ke folder tersebut dan **Extract**.

3. **Konfigurasi Database Server:**
   * Masuk menu **MySQL® Databases** di cPanel.
   * Buat Database baru, buat User baru, dan hubungkan User ke Database dengan akses *ALL PRIVILEGES*.
   * Masuk ke **phpMyAdmin** cPanel, pilih database baru, lalu *Import* file `.sql` Anda.

4. **Konfigurasi `.env`:**
   * Edit file `.env` di folder aplikasi Anda.
   * Ubah bagian database menyesuaikan nama DB, User DB, dan Password DB yang dibuat di langkah 3.
   * Ubah `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL=https://domain-cvberkah.com`.

5. **Instalasi Dependensi via Terminal cPanel:**
   * Buka fitur **Terminal** di cPanel.
   * Masuk ke folder aplikasi: `cd cv_berkah_app`
   * Jalankan: `composer install --optimize-autoloader --no-dev`

6. **Pengaturan Symlink (Menghubungkan Laravel ke Domain):**
   * Di Shared Hosting, domain utama selalu membaca isi folder `public_html`.
   * Hapus folder `public_html` yang asli (jika kosong/tidak terpakai).
   * Buka Terminal, buat *symlink* agar `public_html` merujuk ke folder `public` milik Laravel:
     ```bash
     ln -s /home/username_anda/cv_berkah_app/public /home/username_anda/public_html
     ```
   * Jika tidak punya akses SSH untuk Symlink, Anda bisa memindahkan isi folder `public` Laravel ke dalam `public_html`, lalu sesuaikan *path* di file `index.php`.

7. **Koneksi Gambar & Optimasi Cache:**
   * Di Terminal (di dalam folder `cv_berkah_app`), jalankan:
     ```bash
     php artisan storage:link
     php artisan optimize
     php artisan view:cache
     ```

8. **Aktivasi SSL (Gembok Hijau):**
   * Di cPanel, cari menu **Let's Encrypt SSL** atau **AutoSSL**.
   * Pilih domain Anda dan klik "Issue". 
   * Proses ini akan memasang sertifikat HTTPS gratis yang akan diperbarui otomatis setiap 3 bulan.


### Opsi 2: Deployment ke VPS (Ubuntu + Nginx)

Bagi perusahaan B2B dengan budget infrastruktur memadai, menggunakan VPS (seperti DigitalOcean, Niagahoster VPS, atau AWS) sangat disarankan untuk kestabilan server. 

Langkah ringkas (menggunakan alat seperti Laravel Forge atau RunCloud):
1. Sambungkan VPS Anda ke panel manajemen seperti Laravel Forge.
2. Tambahkan domain Anda. Panel akan otomatis menginstall Nginx, PHP 8.3, dan MySQL.
3. Hubungkan ke repositori Git proyek ini.
4. Forge akan otomatis menarik (pull) data dari Git, menjalankan `composer install`, `npm run build`, dan mengatur SSL Let's Encrypt hanya dengan 1 kali klik.
5. Setup *Daemon* di Forge untuk menjalankan `php artisan queue:work` jika nantinya Anda butuh memproses email notifikasi di latar belakang.
