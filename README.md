# Sistem Informasi Reservasi & Pengelolaan Fasilitas Kampus

> **Project PPK (Pengembangan Platfrom Khusus)**  

Aplikasi web berbasis **Laravel** yang dirancang untuk mengelola peminjaman/reservasi fasilitas kampus dan pelaporan kerusakan sarana-prasarana secara terpusat, transparan, dan terstruktur. Sistem ini menyediakan mekanisme validasi bentrok jadwal otomatis, penyimpanan foto bukti kerusakan privat, audit log perubahan status, dan rekapitulasi okupansi yang dapat diekspor ke berbagai format (CSV, Excel, dan PDF).

---

## 📋 Daftar Isi
1. [Deskripsi Project](#-deskripsi-project)
2. [SRS (Software Requirements Specification)](#-srs-software-requirements-specification)
3. [FR (Functional Requirements)](#-fr-functional-requirements)
4. [Struktur Folder Project](#-struktur-folder-project)
5. [Pembagian Tugas & Workflow Tim](#-pembagian-tugas--workflow-tim)
6. [Akun Testing & Skenario Pengujian](#-akun-testing--skenario-pengujian)

---

## 📌 Deskripsi Project

Sistem Reservasi & Pengelolaan Fasilitas Kampus memfasilitasi interaksi antara sivitas akademika (mahasiswa/dosen/staf) dengan pengelola fasilitas kampus. Aplikasi ini menggantikan proses reservasi manual yang rawan tumpang-tindih (*double-booking*) dan pelaporan kerusakan fasilitas yang seringkali tidak terdokumentasi dengan baik.

### Tech Stack:
- **Backend Framework**: Laravel 13 (PHP 8.4+; dibutuhkan oleh Symfony 8.1 di composer.lock)
- **Database**: MySQL
- **Frontend / UI**: Laravel Blade Templates, Tailwind CSS, Alpine.js
- **Export Engine**:
  - `maatwebsite/excel` (PhpSpreadsheet) untuk ekspor format XLSX/Excel
  - `barryvdh/laravel-dompdf` untuk pembuatan dokumen rekapitulasi PDF
- **Asset Bundler**: Vite

---

## 📑 SRS (Software Requirements Specification)

### 1. Karakteristik Pengguna (User Roles)
Sistem memiliki 3 (tiga) peran aktor dengan batas kewenangan (*role-based access control*) yang ketat:
1. **Pengguna (Sivitas Akademika / Mahasiswa / Dosen)**:
   - Melihat katalog fasilitas dan ketersediaan jadwal per slot waktu 30 menit secara publik.
   - Mengajukan reservasi fasilitas kampus (jam operasional 07:00 – 20:00).
   - Melihat riwayat permohonan reservasi pribadi dan membatalkan reservasi miliknya yang belum berjalan.
   - Melaporkan kerusakan sarana/prasarana dengan melampirkan deskripsi dan foto bukti.
   - Melacak status tindak lanjut laporan kerusakan.
2. **Petugas Fasilitas**:
   - Memantau antrian reservasi yang berstatus `pending`.
   - Menyetujui (*approve*) atau menolak (*reject*) reservasi dengan mencantumkan alasan.
   - Membatalkan (*cancel*) reservasi berstatus `approved` jika terjadi kondisi darurat/mendesak.
   - Mengelola laporan kerusakan: memproses laporan (`diproses`), menandai perbaikan selesai (`selesai`), atau menolak laporan (`ditolak`).
   - Saat memproses laporan, petugas dapat mencentang opsi **Tutup fasilitas untuk perbaikan**; status fasilitas lalu berubah menjadi `dalam perbaikan` dan kembali `aktif` otomatis saat tidak ada lagi laporan yang menutup fasilitas tersebut.
3. **Admin**:
   - Memantau ringkasan statistik global fasilitas, reservasi, laporan, dan pengguna.
   - Melakukan manajemen data fasilitas kampus (Tambah, Edit, Nonaktifkan, dan Aktivasi).
   - Melakukan manajemen akun: mendaftarkan petugas/pengguna langsung (*auto-verified*), memverifikasi/menolak pendaftaran mandiri, serta membekukan (*suspend*) dan mengaktifkan kembali (*reactivate*) akun pengguna.
   - Mengakses rekapitulasi data okupansi & kerusakan fasilitas serta mengekspor data ke CSV, Excel, dan PDF.

### 2. Aturan Bisnis & Batasan Sistem (Business Rules)
- **Jam Operasional**: Reservasi hanya diizinkan pada rentang pukul `07:00:00` hingga `20:00:00`.
- **Interval Waktu Slot**: Durasi reservasi wajib kelipatan slot 30 menit (misal: 08:00 - 09:30).
- **Pencegahan Bentrok (*Anti-Conflict*)**: Dua reservasi tidak boleh disetujui pada fasilitas, tanggal, dan rentang jam yang saling tumpang-tindih. Proteksi berlapis: di tingkat aplikasi menggunakan transaksi database dengan mekanisme *pessimistic locking* (`lockForUpdate()`), dan di tingkat database menggunakan *trigger* anti-bentrok pada tabel `reservations` sebagai pengaman terakhir.
- **Batas Waktu Pembatalan oleh Pengguna**: Reservasi `approved` hanya dapat dibatalkan oleh pemiliknya maksimal **H-1** sebelum tanggal jadwal kegiatan dan paling lambat pada pukul **23:59 WIB**. Pada hari-H kegiatan, pengguna tidak dapat lagi membatalkan reservasi `approved` secara mandiri (pembatalan darurat pada hari-H hanya dapat diproses oleh Petugas Fasilitas). Reservasi `pending` (belum diproses petugas) boleh dibatalkan kapan saja selama kegiatannya belum lewat.
- **Batasan Pengajuan Reservasi**: reservasi tidak dilayani pada hari Minggu (libur), pengajuan minimal 1 jam sebelum jam mulai (buffer), maksimal 60 hari ke depan, durasi reservasi bebas hingga seharian penuh (selama jam operasional 07:00–20:00, maksimal 13 jam), maksimal 3 reservasi `pending` dan 5 reservasi aktif (`pending` + `approved`) per pengguna, serta dibatasi 10 pengajuan per menit per pengguna (anti-spam).
- **Single Source of Truth Status Fasilitas**:
  - `aktif`: Fasilitas siap digunakan dan dapat direservasi.
  - `dalam perbaikan`: Fasilitas sedang ditangani petugas akibat kerusakan (tidak dapat direservasi).
  - `nonaktif`: Fasilitas dinonaktifkan oleh Admin (disembunyikan dari katalog publik).
- **Reservasi Pending Kedaluwarsa**: reservasi `pending` yang waktu mulainya sudah lewat ditolak otomatis (alasan "Kedaluwarsa", tercatat di log status) sehingga tidak mengunci slot maupun kuota pemesan (maks. 3 reservasi pending yang masih berlaku per pengguna).
- **Penonaktifan Fasilitas**: admin tidak dapat menonaktifkan fasilitas yang masih punya reservasi `approved` yang akan berlangsung (batalkan lewat petugas dulu). Reservasi `pending` yang akan berlangsung ditolak otomatis. Fasilitas yang hanya punya riwayat reservasi lama tetap bisa dinonaktifkan tanpa error. Saat petugas memproses laporan dengan opsi **Tutup fasilitas untuk perbaikan**, reservasi `approved` yang akan berlangsung dibatalkan dan reservasi `pending` ditolak otomatis (alasan tercatat dan terlihat oleh pemesan); detail laporan juga menampilkan daftar reservasi approved yang terdampak.
- **Catatan Resolusi Laporan**: wajib diisi saat laporan ditutup (`selesai` atau `ditolak`).
- **Rekap Okupansi**: okupansi = jam reservasi `approved` dibanding jam operasional (07:00-20:00) pada periode terpilih (default: awal bulan sampai hari ini); tersedia agregasi per lokasi. Hanya reservasi yang sudah selesai yang dihitung, dan penyebut hanya hari kerja (Senin-Jumat) yang sudah berjalan; fasilitas nonaktif tidak dihitung, kecuali pada hari ia terbukti dipakai. CSV memakai BOM UTF-8. Filter periode ikut ke ekspor CSV/Excel/PDF.
- **Privasi Bukti Foto Kerusakan**: Foto laporan disimpan pada storage lokal privat (`storage/app/private`), bukan direktori public. Akses file diproteksi melalui controller resmi (`/laporan/foto/{foto}`).
- **Pencatatan Audit Trail**: Setiap perubahan status reservasi dicatat ke tabel `log_status_reservasi`, dan perubahan status laporan dicatat ke tabel `log_status_laporan`.
- **Verifikasi Akun Baru**: Pengguna yang mendaftar mandiri melalui `/register` mendapat status `pending` dan **tidak dapat login** sebelum diverifikasi oleh Admin.
- **Keamanan Login & Sesi**: kredensial dan status akun diperiksa sebelum sesi dibuat. Percobaan login dibatasi (5x per kombinasi email+IP, 20x per IP, 30x per email). Status akun dicek ulang di setiap request, sehingga akun yang dibekukan/ditolak admin langsung terputus dari sesi lamanya.

---

## Functional Requirements

### 1.Pengguna (Sivitas Akademika & Publik)

| Kode FR | Modul | Deskripsi Kebutuhan Fungsional |
| :--- | :--- | :--- |
| **FR-USR-01** | Autentikasi | Pengguna dapat melakukan registrasi akun mandiri dengan status awal `pending` (menunggu verifikasi Admin). |
| **FR-USR-02** | Autentikasi | Pengguna dapat melakukan login, logout, dan memperbarui informasi profil akun. |
| **FR-USR-03** | Fasilitas | Pengguna publik dapat melihat katalog fasilitas kampus serta melakukan pencarian berdasarkan tipe, lokasi, dan kapasitas. |
| **FR-USR-04** | Fasilitas | Pengguna publik dapat melihat ketersediaan timeline slot waktu 30 menit (07:00–20:00) pada fasilitas untuk tanggal tertentu. |
| **FR-USR-05** | Reservasi | Pengguna terdaftar dapat mengajukan reservasi fasilitas dengan validasi batas jam operasional, kelipatan slot 30 menit, dan anti-bentrok. |
| **FR-USR-06** | Reservasi | Pengguna dapat melihat riwayat dan memantau status permohonan reservasi miliknya (`pending`, `approved`, `rejected`, `cancelled`). |
| **FR-USR-07** | Reservasi | Pengguna dapat membatalkan reservasi miliknya sendiri: reservasi `approved` maksimal H-1 sebelum tanggal jadwal kegiatan (maksimal pukul 23:59 WIB), reservasi `pending` kapan saja selama kegiatan belum lewat. |
| **FR-USR-08** | Laporan | Pengguna dapat membuat laporan kerusakan sarana/prasarana dengan memilih fasilitas, kategori, deskripsi, dan upload beberapa foto bukti. |
| **FR-USR-09** | Laporan | Pengguna dapat melihat daftar laporan kerusakan yang pernah diajukan, melihat pratinjau foto, dan membaca catatan resolusi dari petugas. |

### 2.Petugas Fasilitas

| Kode FR | Modul | Deskripsi Kebutuhan Fungsional |
| :--- | :--- | :--- |
| **FR-STF-01** | Dashboard | Petugas dapat melihat ringkasan statistik: total reservasi pending, laporan baru, laporan diproses, dan fasilitas dalam perbaikan. |
| **FR-STF-02** | Reservasi | Petugas dapat melihat antrian permohonan reservasi berstatus `pending` yang diurutkan dari yang paling lama menunggu pada panel dashboard, serta riwayat seluruh reservasi (dapat difilter per status, terbaru lebih dulu) pada menu Antrian Reservasi. |
| **FR-STF-03** | Reservasi | Petugas dapat menyetujui (*approve*) reservasi dengan validasi pencegahan bentrok jadwal otomatis (*pessimistic locking*). |
| **FR-STF-04** | Reservasi | Petugas dapat menolak (*reject*) reservasi pending dengan kewajiban mencantumkan alasan penolakan untuk pemohon. |
| **FR-STF-05** | Reservasi | Petugas dapat membatalkan (*cancel*) reservasi yang sudah disetujui sebelumnya dalam kondisi mendesak dengan wajib mengisi alasan pembatalan. |
| **FR-STF-06** | Laporan | Petugas dapat melihat laporan `baru` (terlama dulu) pada panel dashboard, serta daftar seluruh laporan kerusakan (dapat difilter per status `baru`, `diproses`, `selesai`, `ditolak`) pada menu Laporan Kerusakan. |
| **FR-STF-07** | Laporan | Petugas dapat membuka detail laporan kerusakan dan melihat foto bukti kerusakan yang tersimpan secara privat. |
| **FR-STF-08** | Laporan | Petugas dapat memperbarui status laporan (`diproses`, `selesai`, `ditolak`) serta memberikan catatan resolusi penanganan. |
| **FR-STF-09** | Fasilitas | Petugas dapat menandai fasilitas `dalam perbaikan` dengan mencentang **Tutup fasilitas untuk perbaikan** saat memproses laporan. Sistem otomatis mengembalikan status fasilitas menjadi `aktif` saat laporan tersebut `selesai`/`ditolak` dan tidak ada laporan lain yang masih menutup fasilitas. |
| **FR-STF-10** | Audit Log | Sistem otomatis mencatat log riwayat setiap perubahan status reservasi dan laporan ke tabel database audit. |

### 3.Administrator

| Kode FR | Modul | Deskripsi Kebutuhan Fungsional |
| :--- | :--- | :--- |
| **FR-ADM-01** | Dashboard | Admin dapat memantau ringkasan statistik global: total fasilitas, fasilitas aktif, reservasi pending, laporan baru, dan akun pending. |
| **FR-ADM-02** | Fasilitas | Admin dapat menambah fasilitas baru ke dalam sistem dengan status awal `aktif`. |
| **FR-ADM-03** | Fasilitas | Admin dapat mengedit data fasilitas (nama, tipe, lokasi, kapasitas, deskripsi). |
| **FR-ADM-04** | Fasilitas | Admin dapat menonaktifkan fasilitas (*soft delete* / ubah status `nonaktif`) tanpa merusak relasi data reservasi/laporan yang sudah ada. |
| **FR-ADM-05** | Fasilitas | Admin dapat mengaktifkan kembali fasilitas yang dinonaktifkan (dengan sinkronisasi status perbaikan jika masih ada laporan aktif). |
| **FR-ADM-06** | User | Admin dapat mendaftarkan akun petugas atau pengguna baru secara langsung dengan status langsung `verified`. |
| **FR-ADM-07** | User | Admin dapat melihat antrian pendaftaran mandiri, menyetujui (*verify*), atau menolak (*reject*) permohonan akun baru. |
| **FR-ADM-08** | User | Admin dapat membekukan akun pengguna (*suspend*) agar tidak dapat login, dan mengaktifkannya kembali (*reactivate*). |
| **FR-ADM-09** | Rekapitulasi | Admin dapat melihat tabel rekapitulasi data okupansi reservasi dan frekuensi laporan kerusakan per fasilitas. |
| **FR-ADM-10** | Ekspor Data | Admin dapat mengekspor data rekapitulasi fasilitas ke dalam format CSV, Excel (XLSX Times New Roman & timestamp WIB), dan PDF (A4 Landscape). |

---

## 📂 Struktur Folder Project

Berikut adalah struktur berkas utama pada project ini:

```text
ProjectPPK-Reservasi-Fasilitas/
├── app/
│   ├── Enums/
│   │   └── UserRole.php              # Enum role pengguna: admin, petugas, pengguna
│   ├── Console/
│   │   └── Commands/
│   │       └── KedaluwarsakanReservasi.php # php artisan reservasi:kedaluwarsakan
│   ├── Exports/
│   │   └── RekapFasilitasExport.php  # Class generator export Excel (Maatwebsite Excel)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                # Controller khusus Administrator (Abhista)
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── FacilityController.php
│   │   │   │   ├── RekapController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Auth/                 # Controller autentikasi Laravel Breeze (Akmal)
│   │   │   ├── Petugas/              # Controller khusus Petugas Fasilitas (Ilham)
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── ReservationController.php
│   │   │   ├── FacilityController.php    # Controller katalog fasilitas publik (Zhafran)
│   │   │   ├── ProfileController.php     # Controller profil pengguna
│   │   │   ├── ReportController.php      # Controller laporan kerusakan pengguna (Akbar)
│   │   │   └── ReservationController.php # Controller reservasi pengguna (Zhafran)
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php                 # Middleware validasi hak akses role
│   │   │   └── PastikanAkunTerverifikasi.php # Cek ulang status akun di setiap request
│   │   └── Requests/                 # Form Request (validasi sisi server)
│   │       ├── Admin/                # BuatAkunRequest, SimpanFasilitasRequest
│   │       ├── Auth/                 # LoginRequest (rate limiting berlapis)
│   │       ├── Petugas/              # UbahStatusLaporanRequest
│   │       ├── ProfileUpdateRequest.php
│   │       ├── RegistrasiPenggunaRequest.php
│   │       └── SimpanReservasiRequest.php
│   ├── Models/
│   │   ├── Facility.php              # Model Fasilitas
│   │   ├── LogStatusLaporan.php      # Model Log Perubahan Status Laporan
│   │   ├── LogStatusReservasi.php    # Model Log Perubahan Status Reservasi
│   │   ├── Report.php                # Model Laporan Kerusakan
│   │   ├── ReportCategory.php        # Model Kategori Kerusakan
│   │   ├── ReportPhoto.php           # Model Foto Kerusakan (Storage Lokal)
│   │   ├── Reservation.php           # Model Reservasi Fasilitas
│   │   └── User.php                  # Model Pengguna
│   └── Services/
│       ├── RekapService.php          # Perhitungan rekap okupansi & kerusakan
│       ├── ReservationAvailability.php # Service validasi bentrok & slot waktu
│       └── ReservationExpiry.php     # Penolakan otomatis reservasi pending kedaluwarsa
├── database/
│   ├── factories/                    # Factory pengujian data
│   ├── migrations/                   # Skema migrasi tabel database & trigger
│   └── seeders/
│       ├── DatabaseSeeder.php        # Seeder akun testing & data contoh fasilitas
│       └── ReportCategorySeeder.php  # Seeder kategori laporan kerusakan
├── lang/
│   └── id/                           # Pesan validasi, auth, & password bahasa Indonesia
├── resources/
│   └── views/
│       ├── admin/                    # Tampilan modul Admin
│       ├── auth/                     # Tampilan login, register, forgot-password
│       ├── facilities/               # Tampilan katalog & detail slot fasilitas
│       ├── layouts/                  # Layout navigasi & blade wrapper
│       ├── petugas/                  # Tampilan dashboard & antrian Petugas
│       ├── reports/                  # Tampilan form & riwayat laporan pengguna
│       └── reservations/             # Tampilan form & riwayat reservasi pengguna
├── routes/
│   ├── admin.php                     # Rute modul Admin (prefix: /admin)
│   ├── auth.php                      # Rute autentikasi
│   ├── console.php                   # Jadwal reservasi:kedaluwarsakan (tiap 5 menit)
│   ├── laporan.php                   # Rute modul Laporan Kerusakan (prefix: /laporan)
│   ├── petugas.php                   # Rute modul Petugas (prefix: /petugas)
│   ├── reservasi.php                 # Rute modul Fasilitas (/fasilitas) & Reservasi (/reservasi)
│   └── web.php                       # Pintu masuk utama & redirector dashboard
└── tests/
    ├── Concerns/                     # Helper pembuat data uji
    └── Feature/                      # Tes auth, reservasi, pembatalan, laporan, fasilitas, user, rekap
```

---

## 👥 Pembagian Tugas & Workflow Tim

Untuk mencegah konflik merge file kode (*merge conflict*), arsitektur rute dan controller dipisah secara modular:

| No | Anggota Tim (NIM) | Modul Tanggung Jawab | File Rute | Controller & View Utama |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Akmal** (NIM: ........) | **Auth & Struktur Dasar** | `routes/auth.php`<br>`routes/web.php` | • `app/Http/Controllers/Auth/*`<br>• Setup skema migrasi awal & layout dasar |
| 2 | **Zhafran** (NIM: ........) | **Reservasi (Pengguna)** | `routes/reservasi.php` | • `FacilityController.php`<br>• `ReservationController.php`<br>• `ReservationAvailability.php`<br>• `resources/views/facilities/*`<br>• `resources/views/reservations/*` |
| 3 | **Akbar** (NIM: ........) | **Laporan Kerusakan (Pengguna)** | `routes/laporan.php` | • `ReportController.php`<br>• Upload foto storage privat & serve handler<br>• `resources/views/reports/*` |
| 4 | **Ilham** (NIM: ........) | **Modul Petugas** | `routes/petugas.php` | • `Petugas\DashboardController.php`<br>• `Petugas\ReservationController.php`<br>• `Petugas\ReportController.php`<br>• Audit Log (`LogStatusReservasi`, `LogStatusLaporan`)<br>• `resources/views/petugas/*` |
| 5 | **Abhista** (NIM: ........) | **Modul Admin** | `routes/admin.php` | • `Admin\DashboardController.php`<br>• `Admin\FacilityController.php`<br>• `Admin\UserController.php`<br>• `Admin\RekapController.php`<br>• `app/Exports/RekapFasilitasExport.php`<br>• `resources/views/admin/*` |

---
## ⚙️ Setup & Informasi Setting Menjalankan Program

1. `composer install` dan `npm install && npm run build`
2. `cp .env.example .env` lalu `php artisan key:generate`
3. Buat database MySQL `PPK_project`, sesuaikan `DB_USERNAME` / `DB_PASSWORD` di `.env`
   (file `.env` asli tidak boleh ikut dikumpulkan/di-commit demi keamanan; `.env.example` sudah menyediakan templatenya).
4. `php artisan migrate --seed` (migrasi membuat trigger & CHECK constraint → wajib **MySQL 8.0.16+** / MariaDB 10.2+)
5. `php artisan serve` → buka `http://localhost:8000`
6. **Pengaturan `php.ini` untuk upload foto laporan** (maks. 5 foto × 2 MB):
   `upload_max_filesize = 3M` dan `post_max_size = 16M`. Jika terlalu kecil, form laporan gagal tanpa pesan jelas.
7. `APP_LOCALE=id` (pesan validasi bahasa Indonesia, berkas ada di `lang/id`). Untuk demo/produksi set `APP_DEBUG=false`.
8. **Penjadwal (opsional, disarankan)**: jalankan `php artisan schedule:work` agar reservasi `pending` yang jadwalnya sudah lewat
   ditolak otomatis tiap 5 menit. Tanpa penjadwal pun, pembersihan yang sama berjalan otomatis (maks. 1x/menit) saat petugas
   membuka dashboard/antrian dan saat pengguna mengajukan reservasi. Bisa juga manual: `php artisan reservasi:kedaluwarsakan`.
9. **Email**: `MAIL_MAILER=log` berarti email (mis. tautan reset password) hanya ditulis ke `storage/logs/laravel.log`, tidak terkirim.
   Untuk pengiriman nyata isi `MAIL_*` dengan SMTP yang valid.
10. **Menjalankan tes**: buat database kosong `PPK_project_test` (`CREATE DATABASE PPK_project_test;`), lalu `php artisan test`.
    Tes memakai database MySQL terpisah (bukan SQLite) karena migrasi memakai trigger & CHECK constraint MySQL.
11. Akun seeder (lihat tabel di bawah) memakai password `password` hanya untuk demo. **Ganti/hapus** sebelum dipakai di luar lingkungan lokal.

---
## Akun Testing & Skenario Pengujian

Database seeder secara otomatis menyediakan 3 akun default siap pakai :

| Peran (Role) | Alamat Email | Password | Halaman Redirect Setelah Login |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` | `/admin/dashboard` |
| **Petugas** | `petugas@example.com` | `password` | `/petugas/dashboard` |
| **Pengguna** | `ferry@example.com` | `password` | `/fasilitas` / `/reservasi` |

---

### Skenario Pengujian Alur Kerja

#### 1. Pengujian Pengguna
1. Akses halaman `/fasilitas` tanpa login. Pastikan katalog fasilitas dan slot waktu per 30 menit dapat dilihat, namun tombol pengajuan meminta login terlebih dahulu.
2. Login menggunakan akun `ferry@example.com`.
3. Buka menu **Daftar Fasilitas**, pilih salah satu fasilitas aktif, dan klik **Ajukan Reservasi di Fasilitas Ini**.
4. Ajukan reservasi pada rentang jam operasional (07:00–20:00) dengan kelipatan 30 menit. Pastikan pengajuan berhasil dan statusnya `pending`.
5. Buka menu **Laporan Kerusakan** -> **Buat Laporan Baru**. Pilih fasilitas, kategori, tuliskan deskripsi, lampirkan foto kerusakan, lalu kirim. Pastikan laporan muncul dengan status `baru`.

#### 2. Pengujian Petugas
1. Login menggunakan akun `petugas@example.com`.
2. Pada **Dashboard Petugas**, periksa ringkasan statistik antrian reservasi dan laporan kerusakan.
3. Buka menu **Antrian Reservasi**:
   - Klik **Setujui** pada reservasi pending dari akun pengguna.
   - Coba setujui reservasi lain yang jam dan tanggalnya bertabrakan pada fasilitas yang sama. Sistem akan menolak karena bentrok.
4. Buka menu **Laporan Kerusakan**:
   - Buka detail laporan, ubah status menjadi `diproses` dengan mencentang **Tutup fasilitas untuk perbaikan** (peringatan beserta daftar reservasi terdampak akan muncul). Periksa bahwa status fasilitas tersebut berubah menjadi `dalam perbaikan` di katalog dan reservasi mendatang pada fasilitas itu dibatalkan/ditolak. Tanpa centang tersebut, laporan tetap `diproses` tetapi fasilitas masih `aktif`.
   - Setelah selesai, ubah status menjadi `selesai` dengan mengisi catatan resolusi (wajib). Fasilitas otomatis kembali berstatus `aktif`.
5. Periksa tabel `log_status_reservasi` dan `log_status_laporan` di database untuk memastikan audit trail tersimpan dengan benar.

#### 3. Pengujian Administrator
1. Login menggunakan akun `admin@example.com`.
2. Buka menu **Fasilitas**:
   - Tambah fasilitas baru melalui form `/admin/fasilitas/create`.
   - Coba nonaktifkan fasilitas yang hanya memiliki riwayat reservasi lama. Sistem akan melakukan *soft-deactivation* (mengubah status menjadi `nonaktif` tanpa error foreign key).
   - Coba nonaktifkan fasilitas yang masih punya reservasi `approved` yang akan berlangsung. Sistem menolak dan meminta petugas membatalkan reservasinya terlebih dahulu.
3. Buka menu **User**:
   - Daftarkan akun petugas atau pengguna baru secara langsung. Akun tersebut langsung berstatus `verified`.
   - Lakukan pengetesan *suspend* pada salah satu akun untuk memastikan akun yang dibekukan tidak dapat melakukan login.
4. Buka menu **Rekap**:
   - Periksa tabel rekap okupansi dan frekuensi kerusakan fasilitas.
   - Uji tombol ekspor laporan:
     - **CSV**: Mengunduh file `.csv` 
     - **Excel**: Mengunduh file `.xlsx` 
     - **PDF**: Mengunduh dokumen `.pdf` 
