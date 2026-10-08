# 🧾 Invoice Maker — Aplikasi Manajemen & Pembuatan Invoice Modern

**Invoice Maker** adalah aplikasi web berbasis **Laravel 11+** yang dirancang untuk mempermudah pembuatan, pengelolaan, dan pencetakan invoice secara profesional, cepat, dan presisi. 

Dengan antarmuka **Obsidian Black & White GenZ Minimalist**, aplikasi ini dilengkapi sistem CRUD lengkap, live preview dokumen A4 real-time, manajemen pelanggan (*customers*), serta konfigurasi profil & logo bisnis.

---

## ✨ Fitur Utama

- 📊 **Dashboard Ringkasan Interactive**: Menampilkan statistik total invoice, jumlah tagihan belum dibayar (*unpaid*), status lunas (*paid*), dan jatuh tempo (*overdue*).
- 📝 **Live Preview A4 Synchronized**: Preview tampilan fisik invoice langsung diperbarui secara *real-time* saat Anda mengetik rincian item atau memilih pelanggan.
- 🧮 **Kalkulasi Otomatis Presisi**: Perhitungan otomatis Subtotal, Diskon Invoice, Pajak (Tax), dan Grand Total dengan dukungan format input fleksibel (titik/koma).
- 🏷️ **Logo Brand Bisnis**: Tampilan logo bisnis yang proporsional dan besar pada header invoice serta halaman pengaturan profil usaha.
- 👥 **Manajemen Pelanggan & Soft Delete**: Pencatatan data customer yang terhubung langsung ke invoice. Tetap aman menjaga riwayat invoice lama meskipun pelanggan di-nonaktifkan.
- 🖨️ **Print-Ready Layout**: Layout cetak yang rapi dan dioptimalkan untuk dicetak langsung dari browser (`Ctrl + P` / `Cmd + P`).
- ⚡ **Desain Modern Minimalis**: Bebas dari emoji, 100% menggunakan Icon Vector SVG profesional dengan estetika hitam-putih kontras tinggi.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 11 / 12
- **Language**: PHP 8.2+
- **Database**: MariaDB / MySQL
- **Frontend**: Laravel Blade + Tailwind CSS v4 + Alpine.js
- **Icons**: Clean Vector SVG Icons
- **Bundler**: Vite

---

## 📋 Prasyarat Sistem (Prerequisites)

Sebelum menginstall di laptop/komputer lokal Anda, pastikan perangkat Anda sudah terpasang:

1. **PHP** versi `>= 8.2` (ekstensi wajib: `pdo`, `pdo_mysql` / `pdo_mariadb`, `mbstring`, `openssl`, `curl`)
2. **Composer** versi `>= 2.x`
3. **Database Server**: MariaDB versi `>= 10.4` atau MySQL versi `>= 8.0`
4. **Node.js** versi `>= 18.x` & **NPM**

---

## 🚀 Langkah Instalasi di Laptop (Lokal)

Ikuti langkah-langkah di bawah ini secara urut dari awal hingga aplikasi dapat berjalan dengan normal:

### 1. Clone / Download Repository
Buka terminal dan clone repository ini ke komputer Anda, lalu masuk ke direktori proyek:
```bash
git clone <URL_REPOSITORY_ANDA>
cd invoice
```

### 2. Install Dependensi PHP
Jalankan perintah composer untuk memasang seluruh package Laravel:
```bash
composer install
```

### 3. Setup File Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka file `.env` menggunakan text editor (VS Code / Sublime / Nano) dan sesuaikan konfigurasi database dengan MariaDB/MySQL lokal Anda:
```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ratio32
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan Database**: Pastikan Anda sudah membuat database kosong bernama `ratio32` di MariaDB/MySQL Anda (misalnya via phpMyAdmin, DBeaver, HeidiSQL, atau Terminal MariaDB):
> ```sql
> CREATE DATABASE ratio32 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> ```

### 4. Generate Application Key
Buat kunci enkripsi aplikasi Laravel:
```bash
php artisan key:generate
```

### 5. Buat Symlink Storage (Penting untuk Logo Bisnis)
Hubungkan folder storage ke direktori publik agar gambar logo bisnis yang diunggah dapat tampil:
```bash
php artisan storage:link
```

### 6. Jalankan Migrasi Database
Jalankan perintah berikut untuk membuat seluruh tabel yang dibutuhkan (invoices, invoice_items, customers, settings):
```bash
php artisan migrate
```

### 7. Install Dependensi Frontend & Compile Assets
Jalankan npm untuk menginstall dependensi Tailwind CSS / Vite dan buat file bundle rilis:
```bash
npm install
npm run build
```

### 8. Jalankan Server Lokal (Laravel Development Server)
Jalankan server aplikasi:
```bash
php artisan serve
```

Aplikasi sekarang sudah berjalan! Buka browser Anda dan akses alamat:
👉 **`http://127.0.0.1:8000`** atau **`http://localhost:8000`**

---

## 📁 Struktur Direktori Utama Proyek

```text
invoice/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php   # Logika halaman ringkasan & statistik
│   │   ├── InvoiceController.php     # Logika CRUD Invoice
│   │   ├── CustomerController.php    # Logika CRUD & Quick Modal Customer
│   │   └── SettingController.php     # Pengaturan Profil & Logo Bisnis
│   ├── Models/                       # Eloquent Models (Invoice, Customer, Item, Setting)
│   └── Services/
│       └── InvoiceCalculator.php     # Helper kalkulasi matematika invoice & formatting
├── database/
│   └── migrations/                   # Skema tabel database (invoices, items, customers, settings)
├── resources/
│   ├── css/                          # Stylesheet Tailwind CSS
│   └── views/
│       ├── components/               # Komponen UI Blade
│       ├── customers/                # View Manajemen Pelanggan
│       ├── invoices/                 # View Create, Edit, Show (A4 Live Preview), Index
│       ├── layouts/                  # Layout Induk (Sidebar & Top Navigation)
│       ├── settings/                 # View Pengaturan Profil Usaha
│       └── dashboard.blade.php       # Dashboard Utama
├── routes/
│   └── web.php                       # Web Route Mappings
└── README.md                         # Dokumentasi Proyek
```

---

## ❓ Troubleshoot / Kendala Umum

1. **Logo Bisnis Tidak Muncul Setelah Diunggah**:
   - Pastikan Anda sudah menjalankan perintah `php artisan storage:link`.
   - Cek folder `storage/app/public/logos` dan folder `public/storage/logos`.

2. **Error `SQLSTATE[HY000] [1049] Unknown database 'ratio32'`**:
   - Database `ratio32` belum dibuat di MariaDB/MySQL. Buat database tersebut terlebih dahulu menggunakan MariaDB CLI / phpMyAdmin.

3. **Perubahan Tampilan / CSS Tidak Muncul**:
   - Jalankan `npm run build` untuk meng-compile ulang aset CSS & JS Vite.

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan penggunaan personal / bisnis dan berlisensi bawah [MIT License](LICENSE).
