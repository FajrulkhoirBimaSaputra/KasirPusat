```markdown
# 🛒 Sistem Manajemen Kasir & POS

Aplikasi Point of Sales (POS) berbasis Laravel untuk mengelola transaksi kasir, manajemen kas (shift), laporan keuangan analitik, dan pelacakan stok bahan baku secara real-time.
```
---

## 🚀 Panduan Pembaruan & Setup Database

Jika Anda baru saja melakukan *clone* atau ingin mengambil pembaruan kode terbaru dari GitHub dan mereset sistem ke kondisi awal, ikuti 3 langkah mudah berikut:

### 1. Ambil Kode Terbaru (Pull) dari GitHub
Buka terminal di dalam folder proyek Anda, lalu jalankan perintah ini untuk menarik kode terbaru:
```bash
git pull origin main

```
---

### 2. Update Dependensi (Jika Diperlukan)

Untuk memastikan semua *package* (PHP & Node.js) sudah yang paling baru sesuai kode yang di-pull:

```bash
composer install
npm install
npm run build

```

### 3. Refresh Database & Buat Akun Admin (Seeder)

**⚠️ PERINGATAN:** Perintah ini akan **MENGHAPUS (DROP)** seluruh tabel beserta data lama di database Anda, lalu membangunnya ulang dari awal.

Jalankan satu baris perintah ini untuk melakukan migrasi ulang sekaligus menjalankan *seeder* otomatis (membuat akun Admin):

```bash
php artisan migrate:fresh --seed

```

*(Alternatif: Jika Anda memisahkan file seeder khusus Admin, jalankan ini secara berurutan):*

```bash
php artisan migrate:fresh
php artisan db:seed --class=AdminSeeder

```

---

## 🔐 Kredensial Login Default

Setelah perintah di langkah ke-3 berhasil, database sudah memiliki akun pusat. Anda dapat langsung masuk ke sistem menggunakan:

* **Role:** Admin Pusat
* **Email:** `admin@admin.com`
* **Password:** `password`

---

**💡 Troubleshooting:**

* Jika terjadi *error* pada database, pastikan file `.env` sudah terkonfigurasi dengan benar (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
* Jika file `.env` belum ada, silakan *copy* dari `.env.example`, lalu jalankan perintah `php artisan key:generate` sebelum melakukan *migrate*.

```

```
