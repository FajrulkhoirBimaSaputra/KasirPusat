Berikut adalah _template_ `README.md` yang ringkas, rapi, dan _to-the-point_ sesuai dengan alur kerja (pull -> refresh database -> seed admin) yang kamu minta.

Kamu tinggal _copy_ kode di bawah ini dan _paste_ ke dalam file `README.md` di _root_ direktori proyek Laravel kamu:

````markdown
# 🛒 Sistem Manajemen Kasir & POS

Aplikasi Point of Sales (POS) berbasis Laravel untuk mengelola transaksi kasir, manajemen kas (shift), laporan keuangan analitik, dan pelacakan stok bahan baku secara _real-time_.

---

## 🚀 Panduan Pembaruan & Setup Database

Jika Anda baru saja melakukan _clone_ atau ingin mengambil pembaruan kode terbaru dari GitHub dan mereset sistem ke kondisi awal, ikuti langkah-langkah berikut:

### 1. Ambil Kode Terbaru dari GitHub (Pull)

Buka terminal di dalam folder proyek, pastikan Anda berada di _branch_ yang benar, lalu tarik pembaruan terbaru:

```bash
git pull origin main
```
````

_(Catatan: Ubah `main` menjadi `master` jika repositori Anda menggunakan master sebagai branch utama)._

### 2. Update Dependensi (Opsional namun Disarankan)

Jika ada penambahan _package_ baru pada pembaruan tersebut, jalankan perintah ini:

```bash
composer install
npm install
npm run build

```

### 3. Refresh Database (Migrate:Fresh)

**⚠️ PERINGATAN:** Perintah ini akan **MENGHAPUS (DROP)** seluruh tabel dan data lama di database, lalu membuat ulang struktur tabelnya dari awal. Pastikan Anda tidak melakukan ini di database _Production_ yang datanya masih terpakai!

```bash
php artisan migrate:fresh

```

### 4. Jalankan Seeder Akun Admin

Setelah database kosong dan struktur tabel baru terbentuk, jalankan _seeder_ untuk membuat akun Admin standar agar Anda bisa _login_ ke dalam sistem.

Jalankan perintah berikut (jika Anda memisahkan seeder admin):

```bash
php artisan db:seed --class=AdminSeeder

```

_(Atau, jika seeder admin sudah dimasukkan ke dalam pemanggilan `DatabaseSeeder.php` utama, Anda cukup menggunakan jalan pintas ini untuk langkah 3 dan 4 sekaligus):_

```bash
php artisan migrate:fresh --seed

```

---

## 🔐 Kredensial Login Default

Setelah _seeder_ berhasil dijalankan, Anda dapat masuk ke dasbor menggunakan akun berikut:

- **Role:** Admin Pusat
- **Email:** `admin@admin.com` _(Sesuaikan dengan konfigurasi file seeder Anda)_
- **Password:** `password`

---

**💡 Catatan Tambahan:**
Pastikan file `.env` Anda sudah terkonfigurasi dengan benar (terutama bagian `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`) sebelum menjalankan perintah _migrate_. Jika file `.env` belum ada, _copy_ dari `.env.example` lalu jalankan `php artisan key:generate`.

```

*Template* ini sudah sangat standar dan profesional untuk dibaca oleh *developer* lain (atau untuk pengingat dirimu sendiri di masa depan) saat ingin menjalankan aplikasi ini di komputer/server baru!

```
