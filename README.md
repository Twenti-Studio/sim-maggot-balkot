# SIM Rumah Maggot

Mobile-first web application dan Progressive Web App untuk operasional Rumah Maggot. Dibangun menggunakan Laravel 13, Blade, Tailwind CSS 4, Heroicons, Vite, PostgreSQL, dan Web Push.

## Modul yang tersedia

- Dashboard operasional dan tren tujuh hari.
- RBAC empat role: Super Admin, Admin/Pengelola, Petugas Operasional, dan Bendahara/Keuangan.
- Manajemen pengguna dan lokasi oleh Super Admin.
- Laporan harian terpadu untuk sampah, siklus maggot, dan hasil produksi.
- Workflow Draft → Diajukan → Divalidasi/Ditolak serta permintaan revisi.
- Manajemen petugas, absensi check-in/check-out, gaji, dan bonus.
- Manajemen keuangan untuk kas masuk, transaksi kasir, kas keluar, dan ringkasan laba rugi.
- Inventaris aset, pelaporan kondisi, jadwal, dan riwayat perawatan.
- Audit log untuk tindakan penting.
- Pusat notifikasi, subscription Web Push per perangkat, dan deep link.
- PWA installable dengan manifest, service worker, mode standalone, dan halaman offline.
- Export PDF dan Excel untuk produksi/sampah, absensi, inventaris aset, dan perawatan aset.

## Menjalankan dengan Docker

Prasyarat: Docker Engine dan Docker Compose.

```bash
cp .env.example .env
docker compose build
docker compose up -d db app queue web
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Buka `http://127.0.0.1:8091`. Ganti `DB_PASSWORD`, `APP_URL`, konfigurasi email, dan VAPID key sebelum deployment produksi.

## Deployment Produksi HTTPS

Contoh environment production tersedia di `.env.production.example`.

```bash
cp .env.production.example .env
nano .env
scripts/deploy-production.sh
scripts/deploy-production.sh --ssl
```

Script akan membuat `APP_KEY` jika masih kosong. Ganti `DB_PASSWORD` dan `INITIAL_ADMIN_PASSWORD` sebelum menjalankan deployment. Pastikan DNS `simaggotbalkot.com` dan `www.simaggotbalkot.com` mengarah ke IP server sebelum menjalankan mode `--ssl`. Container Nginx production memakai sertifikat sementara self-signed sampai sertifikat Let’s Encrypt berhasil diterbitkan.

## Development tanpa Docker

Prasyarat: PHP 8.3+, Composer, Node.js 20+, npm, serta PostgreSQL atau ekstensi SQLite PHP. Untuk memakai SQLite lokal, ubah `DB_CONNECTION=sqlite`, kosongkan konfigurasi host PostgreSQL, lalu buat file database.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk development frontend, jalankan `npm run dev` pada terminal terpisah.

## Akun awal

Seeder membuat empat akun awal. Kata sandinya wajib dibaca dari `INITIAL_ADMIN_PASSWORD`; production akan menolak proses seed jika nilai tersebut kosong.

| Role | Email |
|---|---|
| Super Admin | `superadmin@rumahmaggot.id` |
| Admin/Pengelola | `admin@rumahmaggot.id` |
| Petugas Operasional | `petugas@rumahmaggot.id` |
| Bendahara/Keuangan | `bendahara@rumahmaggot.id` |

Deployment produksi tersedia di `https://maggot.twenti.studio`. Ubah kata sandi awal setelah serah terima dan jangan menyimpan `.env` ke repository.

## Web Push

Web Push membutuhkan VAPID key dan HTTPS pada deployment produksi. Buat pasangan key:

```bash
php -r "require 'vendor/autoload.php'; echo json_encode(Minishlink\\WebPush\\VAPID::createVapidKeys(), JSON_PRETTY_PRINT), PHP_EOL;"
```

Masukkan hasilnya ke `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, dan atur `VAPID_SUBJECT` di `.env`. Push hanya dapat diaktifkan setelah aplikasi dipasang dan pengguna memberi izin.

## Validasi

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Dokumen kebutuhan produk tersedia di [PRD.md](PRD.md).
