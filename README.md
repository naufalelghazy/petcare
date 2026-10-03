# 🐾 PetCare POS, Grooming & Hotel Management System

Sistem Informasi Manajemen Terpadu untuk Pet Shop, Salon Grooming, dan Pet Hotel (*Boarding*) berbasis **Native PHP Modern (OOP Model + Page Controller Pattern)** dengan PDO MySQL, transaksi ACID, RBAC (*Role-Based Access Control*), dan Portal Mandiri Pelanggan.

---

## 🌟 4 Pilar Fitur Utama

### 1. 🛒 Kasir POS Hibrida & Konversi Repack Pakan
- **POS Hibrida:** Menangani transaksi ritel produk fisik (dry food, wet food, vitamin) dan layanan salon jasa grooming sekaligus dalam satu keranjang belanja.
- **Modul Konversi Repack (`repack.php`):** Mengonversi karung pakan besar (contoh: 10kg / 15kg) menjadi kemasan eceran siap jual (500g / 1kg) dengan kalkulasi susut bahan (*shrinkage*) dan pencatatan audit log mutasi stok otomatis.
- **Struk Thermal POS (`struk.php`):** Desain struk thermal ukuran 58mm & 80mm dengan tiket nomor antrean penjemputan anabul.

### 2. ✂️ Papan Antrean Grooming & Hak Komisi Staf
- **Papan Kanban Antrean (`antrean_grooming.php`):** Alur kerja teknis salon anabul bertahap:  
  `Antre` ➔ `Mandi` ➔ `Pengeringan` ➔ `Siap Ambil` ➔ `Selesai`.
- **Deteksi Alergi & Karakter Hewan:** Tampilan peringatan alergi sampo/makanan yang menonjol untuk mencegah malpraktik salon.
- **Kalkulasi Komisi Groomer Otomatis:** Perhitungan hak bagi hasil komisi staf (default 20%) setiap kali layanan grooming selesai diproses di kasir.

### 3. 🏨 Reservasi Fasilitas Kamar Pet Hotel (*Boarding*)
- **Meja Front-Desk (`booking_inap.php`):** Manajemen status kamar (Small, Medium, Large, VIP) dan data tamu anabul yang sedang menginap.
- **Kalkulasi Denda Overstay Otomatis:** Sistem mendeteksi keterlambatan penjemputan anabul melewati jam estimasi keluar dan otomatis menghitung denda per jam saat proses pelunasan check-out.
- **Opsi Pakan:** Mendukung pakan bawa mandiri (gratis) atau disediakan toko (+Rp 20.000/hari).

### 4. 📱 Portal Mandiri Pelanggan (*Customer Self-Service*)
- **Pendaftaran Profil Anabul (`portal/my_pets.php`):** Pemilik dapat mendaftarkan riwayat alergi, ras, berat badan, dan kebiasaan hewan.
- **Booking Online Daring (`portal/booking.php`):** Reservasi kamar inap dan jadwal perawatan grooming langsung dari smartphone pelanggan.
- **Live Tracking Progres Salon (`portal/track.php`):** Pemilik memantau tahapan pengerjaan grooming secara *real-time* via *stepper timeline* dengan teknologi auto-polling.
- **Arsip Struk Digital (`portal/riwayat.php`):** Akses riwayat nota transaksi dan tiket penjemputan anabul.

---

## 🏛️ Arsitektur & Struktur Direktori

Mengikuti aturan baku **Universal Skeleton (Pola 1 Modul = 2 File)**:

```text
petcare/
├── assets/
│   ├── css/
│   │   ├── style.css           # Styling antarmuka kasir & admin
│   │   ├── portal.css          # Styling portal publik pelanggan
│   │   ├── print.css           # Layout cetak nota thermal 58mm/80mm
│   │   └── dynamic.php         # Generator CSS variabel dari database
│   └── js/
│       ├── kasir.js            # Interaksi keranjang belanja hibrida
│       └── tracker.js          # Polling status pengerjaan grooming live
├── config/
│   ├── config.php              # Base URL, session helper & konstanta
│   └── database.php            # Handler koneksi PDO & transaksi ACID
├── database/
│   └── petcare_schema.sql      # DDL database relasional & seed data
├── models/
│   ├── User.php                # Entitas staf internal (admin, kasir, groomer)
│   ├── Customer.php            # Entitas pemilik hewan & kredensial portal
│   ├── Hewan.php               # Entitas hewan peliharaan & rekam alergi
│   ├── Barang.php              # Entitas produk ritel, repack, & jasa
│   ├── Kandang.php             # Entitas fisik kamar pet hotel
│   ├── Penjualan.php           # Entitas transaksi kasir & komisi
│   ├── Inap.php                # Entitas reservasi, check-in & overstay
│   ├── Grooming.php            # Entitas antrean & pembagian komisi
│   ├── Repack.php              # Logika konversi karung & susut gram
│   ├── Pembelian.php           # Pengadaan pasokan supplier
│   ├── Vendor.php              # Entitas pemasok barang
│   ├── KategoriBarang.php      # Entitas klasifikasi kategori
│   └── Pengaturan.php          # Entitas pengaturan dinamis & tema UI
├── portal/                     # Sub-direktori Portal Mandiri Pelanggan
│   ├── index.php               # Beranda akun pelanggan
│   ├── my_pets.php             # Form kelola profil hewan peliharaan
│   ├── booking.php             # Form reservasi kamar & grooming daring
│   ├── track.php               # Layar pemantau progres perawatan live
│   └── riwayat.php             # Unduh arsip struk digital
├── antrean_grooming.php        # Papan pantau tugas teknis groomer
├── booking_inap.php            # Meja front-desk reservasi pet hotel
├── repack.php                  # Antarmuka eksekusi pemecahan pakan
├── penjualan.php               # Kasir hibrida (ritel + jasa)
├── customer.php                # Master data pelanggan & hewan walk-in
├── barang.php                  # Master data produk, paket jasa, & kamar
├── kandang.php                 # Master data fasilitas kamar hotel
├── stok.php                    # Monitoring mutasi stok gudang
├── struk.php                   # Generator tiket thermal & faktur
├── dashboard.php               # Analitik omzet, kamar, & bagi hasil
├── login.php                   # Form login multi-aktor staf
├── logout.php                  # Destruksi sesi staf
├── index.php                   # Landing page publik sistem
└── install.php                 # Skrip migrasi & setup otomatis database
```

---

## 🚀 Panduan Instalasi & Menjalankan Sistem

1. Pastikan server lokal Anda aktif (contoh: **Laragon / XAMPP** dengan Apache & MySQL berjalan).
2. Letakkan proyek di folder root web server (contoh: `C:/laragon/www/petcare`).
3. Buka browser dan jalankan installer otomatis:
   ```
   http://localhost/petcare/install.php
   ```
   Skrip ini akan otomatis membuat basis data `petcare_db`, mengimpor seluruh relasi tabel, dan mengisi data awal (*seed data*).
4. Akses aplikasi:
   - **Halaman Utama / Landing Page:** `http://localhost/petcare/`
   - **Login Staf (POS / Admin / Groomer):** `http://localhost/petcare/login.php`
   - **Portal Mandiri Pelanggan:** `http://localhost/petcare/portal/index.php`

---

## 🔑 Kredensial Akun Demo Bawaan

### 1. Staf Internal (Login di `login.php`)
| Role | Username | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password` | Akses penuh ke seluruh fitur, konfigurasi, user, & log repack. |
| **Kasir** | `kasir1` | `password` | Kasir POS, antrean grooming, booking hotel, & master customer. |
| **Groomer**| `groomer1`| `password` | Papan kerja antrean salon, rekam alergi, & pantau bagi hasil komisi. |

### 2. Pelanggan Mandiri (Login di `portal/index.php`)
| Pelanggan | Nomor Telepon / ID | Password | Anabul Terdaftar |
| :--- | :--- | :--- | :--- |
| **Ahmad Fauzi** | `081234567890` | `password` | Milo (Kucing Persia), Bobby (Poodle) |
| **Jessica Tan** | `085678901234` | `password` | Cleo (Kucing British Shorthair) |
