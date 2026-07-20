# CV BERKAH - ERP & Katalog Pemesanan Besi

Aplikasi ERP Mini & POS (Point of Sales) lengkap dengan sistem Katalog Publik dan Pemesanan via WhatsApp, dirancang secara khusus untuk **CV Berkah**, perusahaan penyedia fabrikasi besi dan logam industri.

## Fitur Utama

- **Katalog Publik Dinamis:** Etalase produk B2B dengan filter kategori bertingkat dan pencarian.
- **Pemesanan via WhatsApp:** Alur checkout *friction-less* untuk pembeli, langsung mengarah ke WhatsApp Admin beserta rincian pesanan.
- **Manajemen Inventaris (Gudang):** Validasi ketat (Atomic Database Transactions) untuk mutasi stok masuk dan keluar, serta fitur Stock Opname (Penyesuaian Fisik).
- **Point of Sales (Kasir):** Transaksi *walk-in* yang terintegrasi penuh menggunakan Alpine.js untuk pencarian produk cepat tanpa reload.
- **Laporan Excel:** Ekspor data penjualan, laporan laba rugi, dan riwayat mutasi stok ke format Excel secara presisi.
- **Role-Based Access:** Mendukung hak akses Admin dan Staff/Kasir.

## Persyaratan Sistem

- PHP 8.2 atau 8.3
- MySQL 8.0 / MariaDB 10+
- Composer 2.x
- Node.js & npm (untuk build aset Vite TailwindCSS)
- Ekstensi PHP: `pdo_mysql`, `gd`, `zip` (untuk Excel)

## Panduan Setup untuk Tim (Pengguna Laragon)

Jika Anda baru saja meng-clone project ini dari repositori dan menggunakan **Laragon**, ikuti langkah-langkah wajib berikut agar project bisa berjalan normal di komputer Anda:

1. **Pastikan Folder Berada di Root Laragon**
   Letakkan hasil clone project ini di dalam folder `C:\laragon\www\`. Pastikan nama foldernya adalah `CV-Berkah-Fabrikasi-Besi` (atau sesuaikan, tapi ingat nama folder ini akan menjadi URL lokal Anda, misal: `http://cv-berkah-fabrikasi-besi.test`).

2. **Nyalakan Laragon**
   Buka aplikasi Laragon, lalu klik **Start All**. Pastikan service **Apache** dan **MySQL** sudah berjalan (berwarna biru/hijau).

3. **Install Dependensi PHP (Vendor)**
   Buka terminal (bisa klik tombol **Terminal** di Laragon) dan pastikan berada di dalam folder project ini. Jalankan:
   ```bash
   composer install
   ```

4. **Konfigurasi File Environment (.env)**
   Project Laravel butuh file konfigurasi lokal. Buat duplikat file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` tersebut, lalu pastikan koneksi database-nya seperti ini (kredensial default Laragon):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cv_berkah
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Buat Database di MySQL**
   Klik tombol **Database** di Laragon (biasanya membuka HeidiSQL atau phpMyAdmin). Buat database baru dengan nama persis: `cv_berkah`.

6. **Generate Key, Migrate, dan Seed (Wajib)**
   Kembali ke terminal, jalankan urutan perintah berikut untuk mengunci aplikasi dan memasukkan struktur tabel beserta akun Admin bawaan:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

7. **Koneksikan Folder Gambar (Sangat Penting!)**
   Agar gambar produk yang di-upload admin bisa muncul dan tidak error "Not Found", Anda **wajib** menjalankan:
   ```bash
   php artisan storage:link
   ```

8. **Build Aset Tampilan (CSS/JS)**
   Agar tampilan TailwindCSS dan Alpine.js berfungsi sempurna, install module Node dan build asetnya:
   ```bash
   npm install
   npm run build
   ```

9. **Selesai! Buka di Browser**
   Buka browser Anda dan akses aplikasi ini melalui URL otomatis Laragon (sesuai nama folder):
   `http://cv-berkah-fabrikasi-besi.test/`
   *(Jika URL .test belum berfungsi, Anda bisa menjalankannya manual dengan `php artisan serve` lalu buka `http://127.0.0.1:8000/`)*

## Akun Login (Hasil Seeding)

**Akses Admin Panel:** `http://cv-berkah-fabrikasi-besi.test/login`
- **Email:** `admin@cvberkah.id`
- **Password:** `password`

## Asumsi & Keputusan Teknis

1. **Transaksi Atomik (Database Locks):** Modul layanan (`InventoryService`, `SalesService`) secara eksplisit menggunakan blok `DB::transaction()` dan pesimistik lock (`lockForUpdate()`) agar jika sistem digunakan oleh banyak kasir sekaligus, tidak terjadi masalah *race condition* (stok minus tanpa sengaja).
2. **Tidak Membutuhkan Redis/Pusher:** Aplikasi dioptimalkan untuk berjalan di **Shared Hosting standard** yang hemat biaya. Sesi, Cache, dan Queue diatur menggunakan driver file/database.
3. **Pengelolaan Gambar (Intervention Image):** Semua gambar produk yang diunggah lewat admin diproses ulang (compress, ubah ke WebP) menggunakan Laravel Intervention v3 agar web tetap cepat.
4. **Keranjang Belanja:** Keranjang publik berjalan menggunakan `Session`, agar pembeli tidak wajib registrasi/login (meningkatkan konversi pesanan).
5. **Theme Industrial:** Desain publik & admin dirancang kustom dengan utility TailwindCSS (Warna Slate, Emerald, dan aksen logam Amber) sehingga terkesan sangat profesional & B2B tanpa menggunakan template pasaran.
6. **Proteksi Data (Audit Trail):** Mulai Juli 2026, ditambahkan Log Aktivitas sentral. Semua perubahan stok dan status void transaksi direkam secara otomatis dan tidak bisa dimanipulasi secara hard-delete melalui aplikasi, lengkap dengan re-autentikasi password untuk transaksi rawan.

## Panduan Backup Berkala (Cron Job di cPanel)

Untuk mengamankan database dari kehilangan, disarankan menambahkan perintah otomatis di fitur **Cron Jobs** cPanel, yang akan menyimpan dump database setiap malam:
```bash
0 0 * * * mysqldump -u username_cpanel_anda -ppassword_db cv_berkah_db > /home/username_cpanel/backups/cv_berkah_$(date +\%Y-\%m-\%d).sql
```
*(Sesuaikan `username_cpanel_anda`, `password_db`, dan lokasi direktori backup sesuai konfigurasi di cPanel server)*

## Hak Cipta & Lisensi
Proyek ini dibangun secara eksklusif untuk **CV Berkah**. Seluruh kode adalah milik pemesan (proprietary).
