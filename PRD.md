# Product Requirements Document (PRD)

## Sistem Informasi Manajemen Rumah Maggot (SIM Rumah Maggot)

| Informasi | Nilai |
|---|---|
| Versi dokumen | 1.1 |
| Status | Draft awal |
| Tanggal | 23 Juni 2026 |
| Pemilik produk | Pengelola/Ketua KSM Rumah Maggot |
| Platform awal | Mobile-first web application dan Progressive Web App (PWA) |

## 1. Ringkasan Produk

SIM Rumah Maggot adalah mobile-first web application terintegrasi untuk mendigitalisasi pencatatan produksi maggot, pengelolaan petugas, transaksi keuangan, serta inventaris dan perawatan aset. Sistem tersedia sebagai Progressive Web App (PWA) yang dapat dipasang pada perangkat pengguna dan mengirim push notification setelah pengguna memberikan izin. Sistem menggantikan pencatatan manual yang tersebar dengan satu sumber data yang dapat dipantau, ditelusuri, dan dilaporkan berdasarkan Role-Based Access Control (RBAC).

Produk dirancang untuk membantu operasional harian di lapangan sekaligus menyediakan informasi ringkas bagi pengelola untuk mengambil keputusan.

## 2. Latar Belakang dan Masalah

Operasional Rumah Maggot saat ini dilakukan secara manual. Kondisi ini menimbulkan beberapa masalah:

- Data produksi, sampah, absensi, keuangan, dan aset tersebar di media yang berbeda.
- Rekap harian, bulanan, dan tahunan membutuhkan waktu dan rawan salah hitung.
- Riwayat perubahan data dan pihak yang melakukan pencatatan sulit ditelusuri.
- Pengelola tidak memperoleh gambaran kondisi operasional secara cepat.
- Perawatan aset berisiko terlambat karena tidak ada jadwal dan pengingat terpusat.
- Hubungan antara hasil produksi, penjualan, biaya operasional, dan laba belum terdokumentasi secara konsisten.

## 3. Tujuan Produk

1. Menyediakan satu sumber data operasional Rumah Maggot.
2. Mempercepat pencatatan harian dan mengurangi kesalahan manual.
3. Memudahkan monitoring siklus maggot dan hasil produksi.
4. Meningkatkan transparansi absensi, penggajian, dan keuangan.
5. Menjaga kondisi aset melalui jadwal dan riwayat perawatan.
6. Menghasilkan laporan PDF dan Excel yang konsisten serta dapat diaudit.
7. Mendukung pengambilan keputusan melalui dashboard berbasis data terkini.
8. Menyediakan pengalaman penggunaan yang optimal dari ponsel serta dapat dipasang sebagai PWA.
9. Mengirim pengingat dan pemberitahuan operasional berbasis peran melalui push notification.

## 4. Indikator Keberhasilan

- Minimal 90% laporan operasional harian dicatat pada hari yang sama.
- Waktu pembuatan rekap bulanan turun menjadi kurang dari 10 menit.
- Seluruh transaksi kas memiliki kategori, tanggal, pencatat, dan bukti bila diwajibkan.
- Seluruh aset aktif memiliki status kondisi dan lokasi yang terbarui.
- Selisih rekap kas sistem dengan kas aktual tidak melebihi toleransi yang ditetapkan pengelola.
- Minimal 80% pengguna aktif dapat menyelesaikan tugas utamanya tanpa bantuan setelah pelatihan awal.
- Minimal 80% pengguna lapangan menggunakan aplikasi dari ponsel tanpa hambatan tata letak pada alur utama.
- Push notification yang berhasil dikirim tercatat dan hanya ditujukan kepada pengguna dengan izin serta konteks peran yang sesuai.

## 5. Ruang Lingkup

### 5.1 Termasuk dalam MVP

- Autentikasi, profil pengguna, dan RBAC dengan empat role MVP.
- Mobile-first responsive web application.
- PWA yang dapat dipasang, memiliki app manifest dan service worker.
- Push notification berbasis opt-in untuk PWA yang telah dipasang pada perangkat yang kompatibel.
- Pusat notifikasi dalam aplikasi sebagai riwayat dan fallback ketika Web Push tidak tersedia.
- Dashboard utama sesuai hak akses.
- Pencatatan sampah organik dan non-organik.
- Monitoring tahapan siklus maggot.
- Pencatatan hasil produksi.
- Data petugas, absensi, dan penggajian sederhana.
- Kas masuk, kas keluar, transaksi kasir, dan laporan keuangan dasar.
- Daftar aset, kondisi aset, jadwal, dan riwayat perawatan.
- Filter, pencarian, rekap, serta ekspor PDF dan Excel.
- Audit log untuk tindakan penting.

### 5.2 Di Luar MVP

- Aplikasi mobile native; kebutuhan mobile MVP dipenuhi melalui responsive web dan PWA.
- Integrasi perangkat IoT atau timbangan digital.
- Pembayaran daring dan integrasi rekening bank.
- Akuntansi penuh berbasis jurnal berpasangan.
- Integrasi WhatsApp/SMS.
- Prediksi produksi berbasis kecerdasan buatan.
- Portal pelanggan atau pemasok.
- Pengadaan dan manajemen stok bahan secara mendalam.

Fitur di luar MVP dapat dipertimbangkan setelah data operasional dasar stabil.

## 6. RBAC dan Hak Akses

### 6.1 Role MVP

MVP hanya menggunakan empat role berikut:

1. **Super Admin** — pemilik akses tertinggi untuk developer, owner sistem, atau admin pusat. Dapat mengatur pengguna, role, permission, lokasi, seluruh data, dan koreksi data yang diaudit.
2. **Admin/Pengelola** — pengelola operasional Rumah Maggot. Memantau dan memvalidasi laporan harian, mengelola petugas dan aset, serta melihat laporan keuangan tanpa dapat mengubah transaksi keuangan secara bebas.
3. **Petugas Operasional** — berfokus pada input aktivitas lapangan: sampah, siklus maggot, hasil produksi, absensi diri sendiri, serta laporan kondisi dan perawatan aset jika ditugaskan. Tidak dapat melihat detail keuangan.
4. **Bendahara/Keuangan** — mengelola kas masuk, kas keluar, transaksi kasir, gaji, bonus, dan laporan keuangan. Tidak dapat mengubah data produksi; akses rekap operasional hanya diberikan bila diperlukan untuk pekerjaan keuangan.

Ketua KSM/Pimpinan tidak menjadi role terpisah pada MVP. Pengguna tersebut memakai role Admin/Pengelola dengan fokus pada dashboard dan laporan.

### 6.2 Matriks Akses Fitur

| Modul/Fitur | Super Admin | Admin/Pengelola | Petugas Operasional | Bendahara/Keuangan |
|---|---|---|---|---|
| Login | Ya | Ya | Ya | Ya |
| Dashboard utama | Penuh | Penuh | Terbatas pada operasional pribadi/relevan | Ringkasan keuangan dan operasional relevan |
| Kelola user | Ya | Tidak | Tidak | Tidak |
| Kelola role & akses | Ya | Tidak | Tidak | Tidak |
| Kelola data Rumah Maggot/lokasi | Ya | Ya | Tidak | Tidak |
| Input laporan sampah organik | Ya | Ya | Ya | Tidak |
| Input laporan sampah non-organik | Ya | Ya | Ya | Tidak |
| Input data telur maggot | Ya | Ya | Ya | Tidak |
| Input data bayi maggot | Ya | Ya | Ya | Tidak |
| Input data maggot dewasa | Ya | Ya | Ya | Tidak |
| Input data pre-pupa | Ya | Ya | Ya | Tidak |
| Input data lalat BSF | Ya | Ya | Ya | Tidak |
| Input data kasgot | Ya | Ya | Ya | Tidak |
| Input pupuk organik lain | Ya | Ya | Ya | Tidak |
| Input maggot basah | Ya | Ya | Ya | Tidak |
| Input maggot kering | Ya | Ya | Ya | Tidak |
| Edit laporan harian | Ya | Ya | Terbatas: milik sendiri dan belum divalidasi | Tidak |
| Hapus laporan harian | Ya | Ya | Tidak | Tidak |
| Validasi laporan harian | Ya | Ya | Tidak | Tidak |
| Absensi petugas | Ya | Ya | Ya, diri sendiri | Tidak |
| Lihat rekap absensi | Ya | Ya | Diri sendiri | Ya |
| Kelola data petugas | Ya | Ya | Tidak | Tidak |
| Kelola gaji petugas | Ya | Lihat | Tidak | Ya |
| Kelola bonus petugas | Ya | Lihat | Tidak | Ya |
| Input kas masuk | Ya | Lihat | Tidak | Ya |
| Input transaksi kasir | Ya | Lihat | Tidak | Ya |
| Input kas keluar | Ya | Lihat | Tidak | Ya |
| Edit transaksi keuangan | Ya | Approval/validasi saja | Tidak | Ya, sebelum divalidasi |
| Hapus/void transaksi keuangan | Ya | Tidak | Tidak | Tidak |
| Validasi transaksi keuangan | Ya | Ya | Tidak | Tidak |
| Laporan laba rugi | Ya | Ya | Tidak | Ya |
| Export laporan keuangan | Ya | Ya | Tidak | Ya |
| Daftar aset | Ya | Ya | Lihat | Tidak |
| Tambah aset | Ya | Ya | Tidak | Tidak |
| Edit data aset | Ya | Ya | Tidak | Tidak |
| Hapus/arsip aset | Ya | Tidak | Tidak | Tidak |
| Update kondisi aset | Ya | Ya | Ya, melaporkan kondisi | Tidak |
| Input laporan perawatan | Ya | Ya | Ya, jika ditugaskan | Tidak |
| Export laporan operasional | Ya | Ya | Tidak | Hanya laporan keuangan |

### 6.3 Permission MVP

| Kode permission | Fungsi dan scope |
|---|---|
| `user.view` | Melihat pengguna |
| `user.create` | Menambah pengguna |
| `user.update` | Mengubah pengguna |
| `user.delete` | Menonaktifkan/menghapus pengguna sesuai aturan retensi |
| `production.view` | Melihat laporan produksi sesuai scope role/lokasi |
| `production.create` | Input laporan produksi |
| `production.update` | Edit laporan produksi; Petugas dibatasi pada data milik sendiri yang belum divalidasi |
| `production.delete` | Hapus/arsip laporan produksi oleh Super Admin atau Admin/Pengelola |
| `production.validate` | Memvalidasi, menolak, atau membuka revisi laporan produksi |
| `attendance.create` | Melakukan absensi; Petugas hanya untuk diri sendiri |
| `attendance.view` | Melihat absensi sesuai scope role |
| `staff.manage` | Mengelola data petugas |
| `payroll.manage` | Mengelola gaji dan bonus |
| `finance.view` | Melihat data keuangan |
| `finance.create` | Input transaksi keuangan |
| `finance.update` | Edit transaksi keuangan yang belum divalidasi |
| `finance.delete` | Void/soft delete transaksi; hanya Super Admin |
| `finance.validate` | Validasi atau tolak transaksi keuangan |
| `asset.view` | Melihat aset |
| `asset.create` | Menambah aset |
| `asset.update` | Mengubah aset; aksi Petugas dibatasi menjadi laporan kondisi |
| `asset.delete` | Menghapus/mengarsipkan aset; hanya Super Admin |
| `maintenance.create` | Input laporan perawatan sesuai penugasan |
| `report.export` | Ekspor laporan sesuai domain yang diizinkan pada role |

Permission sistem tambahan untuk Super Admin adalah `role.manage`. Permission untuk Super Admin dan Admin/Pengelola dalam mengelola lokasi adalah `location.manage`. Seluruh permission diterapkan pada route/API dan query data, bukan hanya untuk menyembunyikan menu.

### 6.4 Aturan Enforcement RBAC

1. Akses ditentukan oleh kombinasi role, permission, scope kepemilikan data, scope lokasi, dan status data.
2. Petugas hanya dapat mengedit laporan miliknya sendiri selama status masih Draft atau Diajukan dan belum divalidasi.
3. Setelah laporan divalidasi, Petugas tidak dapat mengubahnya. Petugas harus mengajukan revisi beserta alasan; Super Admin atau Admin/Pengelola dapat menolak atau membuka laporan untuk revisi.
4. Bendahara dapat membuat dan mengubah transaksi keuangan hanya sebelum divalidasi.
5. Admin/Pengelola dapat melihat serta memvalidasi/menolak transaksi keuangan, tetapi tidak dapat mengubah isi transaksi.
6. Hanya Super Admin yang memiliki `finance.delete`; implementasinya berupa void/soft delete dengan alasan dan audit log, bukan hard delete.
7. Super Admin secara default memiliki semua permission. Perubahan konfigurasi role hanya dapat dilakukan Super Admin.
8. Role MVP merupakan role sistem dan tidak dapat dihapus. Permission role dapat dikonfigurasi Super Admin dengan audit log, tetapi tidak boleh melampaui batas domain role tanpa keputusan perubahan produk.
9. Endpoint yang tidak memiliki permission eksplisit harus ditolak secara default (*default deny*).

## 7. Kebutuhan Fungsional

### 7.1 Autentikasi dan Administrasi

**FR-AUTH-01** Pengguna dapat masuk menggunakan identitas pengguna dan kata sandi.

**FR-AUTH-02** Sistem menyediakan fitur lupa/reset kata sandi melalui mekanisme yang disetujui organisasi.

**FR-AUTH-03** Super Admin dapat membuat, mengubah, menonaktifkan, dan mengatur ulang akun pengguna.

**FR-AUTH-04** Sistem membatasi menu, API, data, dan tindakan berdasarkan role, permission, scope lokasi, kepemilikan data, dan status data.

**FR-AUTH-05** Sistem mencatat login, logout, kegagalan login, serta perubahan data penting pada audit log.

**FR-AUTH-06** Pengguna dapat melihat dan memperbarui profil dasar serta mengganti kata sandi.

**FR-AUTH-07** Super Admin dapat melihat dan mengatur pemetaan permission untuk empat role MVP.

**FR-AUTH-08** Sistem menerapkan *default deny*: permintaan ditolak jika pengguna tidak memiliki permission dan scope yang secara eksplisit dibutuhkan.

**FR-AUTH-09** Sistem mencatat perubahan role/permission, pemberian akses, penolakan akses sensitif, validasi, pembukaan revisi, dan void pada audit log.

### 7.2 Dashboard Utama

**FR-DASH-01** Dashboard menampilkan kartu ringkasan berikut sesuai periode yang dipilih:

- Total sampah organik masuk.
- Total sampah non-organik masuk.
- Total produksi maggot basah dan kering.
- Total produksi kasgot.
- Jumlah petugas hadir hari ini.
- Saldo kas saat ini.
- Pendapatan bulan berjalan.
- Pengeluaran bulan berjalan.
- Jumlah aset kritis.

**FR-DASH-02** Dashboard menampilkan grafik operasional harian, mingguan, dan bulanan.

**FR-DASH-03** Pengguna dapat memfilter dashboard berdasarkan rentang tanggal dan lokasi bila terdapat lebih dari satu lokasi.

**FR-DASH-04** Data ringkasan dapat ditelusuri menuju daftar transaksi sumber.

**FR-DASH-05** Sistem menampilkan peringatan untuk aset rusak berat, jadwal perawatan jatuh tempo, dan data harian yang belum dilaporkan.

**FR-DASH-06** Isi dashboard mengikuti scope RBAC: Petugas tidak melihat detail keuangan, sedangkan Bendahara hanya melihat ringkasan operasional yang diperlukan untuk konteks keuangan.

### 7.3 Modul Manajemen Siklus Maggot

#### A. Laporan Sampah Organik

**FR-PROD-01** Petugas dapat membuat, melihat, mengubah, dan mengajukan laporan sampah organik dengan data:

- Tanggal dan waktu penerimaan.
- Berat masuk dalam kilogram.
- Sumber sampah.
- Catatan opsional.
- Petugas pencatat.
- Lokasi operasional.

**FR-PROD-02** Sistem memvalidasi berat lebih besar dari nol dan tidak menerima tanggal masa depan tanpa izin khusus.

#### B. Laporan Sampah Non-organik

**FR-PROD-03** Petugas dapat mencatat tanggal, berat, jenis sampah, catatan, pencatat, dan lokasi.

**FR-PROD-04** Jenis sampah menggunakan data master yang dapat dikelola Super Admin atau Admin/Pengelola sesuai permission.

#### C. Monitoring Siklus Maggot

**FR-PROD-05** Petugas dapat mencatat kondisi siklus per tanggal dan batch untuk:

- Telur maggot.
- Bayi maggot/larva muda.
- Maggot dewasa.
- Pre-pupa.
- Lalat BSF.

**FR-PROD-06** Setiap catatan menyimpan satuan pengukuran yang jelas, misalnya gram, kilogram, jumlah koloni, atau estimasi ekor.

**FR-PROD-07** Sistem menampilkan riwayat perkembangan setiap batch.

**FR-PROD-08** Sistem memberi tanda pada batch yang tidak memiliki pembaruan melewati batas hari yang ditentukan.

#### D. Hasil Produksi

**FR-PROD-09** Petugas dapat mencatat hasil produksi per tanggal dan batch untuk kasgot, pupuk organik lainnya, maggot basah, dan maggot kering.

**FR-PROD-10** Setiap hasil produksi mencatat jumlah, satuan, kualitas/grade opsional, dan catatan.

**FR-PROD-11** Sistem dapat menampilkan total dan tren hasil produksi harian, mingguan, dan bulanan.

**FR-PROD-12** Sistem mencegah duplikasi laporan pada kombinasi tanggal, batch, jenis hasil, dan lokasi kecuali disimpan sebagai koreksi yang dapat diaudit.

**FR-PROD-13** Laporan harian memiliki status Draft, Diajukan, Divalidasi, Ditolak, Revisi Diminta, dan Diarsipkan.

**FR-PROD-14** Petugas hanya dapat mengubah laporan yang dibuatnya sendiri selama laporan belum divalidasi.

**FR-PROD-15** Super Admin dan Admin/Pengelola dapat memvalidasi atau menolak laporan yang diajukan. Pengguna tidak boleh memvalidasi laporan melalui endpoint tanpa `production.validate`.

**FR-PROD-16** Petugas dapat mengajukan permintaan revisi untuk laporan tervalidasi dengan alasan wajib. Permintaan tidak langsung mengubah data laporan.

**FR-PROD-17** Super Admin atau Admin/Pengelola dapat membuka revisi, menetapkan batas waktu, dan memvalidasi ulang laporan. Sistem menyimpan versi sebelum dan sesudah perubahan.

### 7.4 Modul Manajemen Petugas

#### A. Data Petugas

**FR-SDM-01** Pengguna berwenang dapat mencatat nama, nomor identitas internal, jabatan, nomor kontak, tanggal mulai, status aktif, dan lokasi kerja.

**FR-SDM-02** Penonaktifan petugas tidak menghapus riwayat absensi dan gaji.

#### B. Absensi

**FR-SDM-03** Petugas dapat check-in dan check-out dengan waktu yang dicatat server.

**FR-SDM-04** Sistem hanya mengizinkan satu sesi absensi aktif per petugas per hari, kecuali pola kerja mengharuskan beberapa sif.

**FR-SDM-05** Sistem menghitung durasi kerja dan status hadir, terlambat, izin, sakit, atau alpa.

**FR-SDM-06** Koreksi absensi wajib menyimpan alasan, nilai sebelum/sesudah, waktu perubahan, dan pengguna yang mengubah.

**FR-SDM-07** Pengguna berwenang dapat melihat rekap absensi per petugas dan periode.

#### C. Penggajian

**FR-SDM-08** Bendahara dapat membuat periode penggajian dan menghitung gaji pokok, bonus, potongan, dan total pembayaran.

**FR-SDM-09** Pengguna berwenang dapat meninjau dan menyetujui penggajian sebelum dibayar.

**FR-SDM-10** Pembayaran gaji yang diselesaikan menghasilkan transaksi kas keluar terkait.

**FR-SDM-11** Sistem menyediakan slip gaji yang dapat dicetak atau diekspor ke PDF.

### 7.5 Modul Manajemen Keuangan

#### A. Kas Masuk dan Kas Keluar

**FR-KEU-01** Bendahara dapat mencatat transaksi kas masuk untuk penjualan maggot, kasgot, pupuk, dan pendapatan lainnya.

**FR-KEU-02** Bendahara dapat mencatat transaksi kas keluar untuk bahan, operasional, utilitas, perawatan aset, gaji, dan pengeluaran lainnya.

**FR-KEU-03** Setiap transaksi menyimpan nomor transaksi unik, tanggal, kategori, nominal, metode pembayaran, pihak terkait opsional, deskripsi, pencatat, dan bukti transaksi opsional/wajib sesuai kebijakan.

**FR-KEU-04** Sistem menghitung saldo berjalan dari saldo awal ditambah kas masuk dikurangi kas keluar yang berstatus final.

**FR-KEU-05** Transaksi final tidak boleh dihapus permanen; pembatalan dilakukan dengan status void, alasan, dan audit log.

**FR-KEU-05A** Bendahara hanya dapat mengubah transaksi berstatus Draft atau Ditolak. Setelah transaksi diajukan atau divalidasi, perubahan memerlukan penolakan/pembukaan koreksi oleh validator.

**FR-KEU-05B** Admin/Pengelola dapat memvalidasi atau menolak transaksi tanpa mengubah detail nominal, kategori, tanggal, atau bukti transaksi.

**FR-KEU-05C** Hanya Super Admin yang dapat melakukan void/soft delete transaksi melalui `finance.delete`; alasan wajib dan saldo dihitung ulang secara terlacak.

#### B. Transaksi Kasir

**FR-KEU-06** Pengguna berwenang dapat memasukkan satu transaksi penjualan dengan satu atau beberapa item.

**FR-KEU-07** Sistem menghitung subtotal, diskon, total, pembayaran, dan kembalian.

**FR-KEU-08** Transaksi kasir yang selesai otomatis menghasilkan kas masuk dan bukti transaksi bernomor unik.

**FR-KEU-09** Pengguna dapat mencari riwayat transaksi berdasarkan nomor, tanggal, kategori, atau pihak terkait.

#### C. Laporan Keuangan

**FR-KEU-10** Sistem menghasilkan buku kas dan arus kas berdasarkan rentang tanggal.

**FR-KEU-11** Sistem menghasilkan laporan laba rugi sederhana menggunakan pendapatan dikurangi beban pada periode terpilih.

**FR-KEU-12** Sistem menyediakan rekap bulanan dan tahunan beserta perbandingan periode sebelumnya.

### 7.6 Modul Manajemen Aset

#### A. Daftar dan Kondisi Aset

**FR-ASET-01** Pengguna berwenang dapat mencatat kode aset unik, nama, kategori, lokasi, tanggal pembelian, nilai perolehan, kondisi, status penggunaan, dan foto/dokumen opsional.

**FR-ASET-02** Kondisi aset terdiri dari Baik, Perlu Perawatan, Rusak Ringan, dan Rusak Berat.

**FR-ASET-03** Perubahan kondisi aset menyimpan tanggal, penilai, catatan, dan bukti opsional.

**FR-ASET-04** Aset tidak boleh dihapus jika memiliki riwayat perawatan; aset dinonaktifkan atau ditandai dilepas.

#### B. Perawatan Aset

**FR-ASET-05** Pengguna dapat membuat jadwal perawatan berisi tanggal rencana, jenis perawatan, petugas, estimasi biaya, dan catatan.

**FR-ASET-06** Status perawatan terdiri dari Terjadwal, Berlangsung, Selesai, Terlambat, dan Dibatalkan.

**FR-ASET-07** Penyelesaian perawatan mencatat tanggal aktual, tindakan, komponen yang diganti, kondisi setelah perawatan, biaya aktual, dan bukti.

**FR-ASET-08** Biaya perawatan yang disetujui dapat menghasilkan transaksi kas keluar terkait.

**FR-ASET-09** Sistem menampilkan riwayat kerusakan, perawatan, dan penggantian komponen per aset.

### 7.7 Pencarian, Filter, dan Ekspor

**FR-REP-01** Semua daftar utama menyediakan pencarian, filter periode/status/kategori, pengurutan, dan pagination.

**FR-REP-02** Sistem menghasilkan laporan berikut:

1. Produksi Maggot.
2. Sampah Organik.
3. Sampah Non-organik.
4. Absensi Petugas.
5. Gaji dan Bonus.
6. Kas Masuk.
7. Kas Keluar.
8. Laba Rugi.
9. Inventaris Aset.
10. Perawatan Aset.

**FR-REP-03** Setiap laporan dapat difilter berdasarkan rentang tanggal dan atribut relevan lainnya.

**FR-REP-04** Setiap laporan dapat diekspor ke PDF dan Excel dengan judul, periode, waktu pembuatan, pembuat, filter aktif, dan nomor halaman bila relevan.

**FR-REP-05** Nilai pada file ekspor harus sama dengan nilai yang ditampilkan pada layar untuk filter yang sama.

### 7.8 Mobile Web, PWA, dan Push Notification

**FR-PWA-01** Seluruh alur utama tersedia sebagai mobile-first responsive web application dengan target lebar layar mulai 320 piksel.

**FR-PWA-02** Aplikasi menyediakan Web App Manifest yang valid, nama aplikasi, ikon pada ukuran yang diperlukan, warna tema, `start_url`, dan mode tampilan `standalone` agar dapat dipasang pada perangkat yang kompatibel.

**FR-PWA-03** Aplikasi mendaftarkan service worker melalui HTTPS untuk menyediakan app shell, aset statis penting, halaman offline, serta pembaruan versi yang terkontrol.

**FR-PWA-04** Pada MVP, mode offline menjamin aplikasi dapat dibuka dan menampilkan halaman offline/app shell. Pengiriman formulir dan transaksi tetap memerlukan koneksi; sistem harus menampilkan status offline dengan jelas dan tidak mengklaim data tersimpan sebelum server mengonfirmasi.

**FR-PWA-05** Sistem menampilkan ajakan instalasi PWA pada browser yang mendukung tanpa menghalangi penggunaan web biasa. Pengguna dapat menunda ajakan tersebut.

**FR-PUSH-01** Setelah PWA dipasang, pengguna dapat mengaktifkan push notification secara eksplisit dari halaman pengaturan. Sistem tidak meminta izin browser tanpa tindakan pengguna.

**FR-PUSH-02** Sistem menyimpan subscription per pengguna dan per perangkat, termasuk endpoint terenkripsi, status aktif, waktu persetujuan, dan waktu penggunaan terakhir.

**FR-PUSH-03** Pengguna dapat menonaktifkan kategori notifikasi atau seluruh push notification untuk setiap perangkat.

**FR-PUSH-04** Sistem mengirim notifikasi berdasarkan role, permission, lokasi, penugasan, dan preferensi pengguna. Pengguna tidak boleh menerima tautan atau informasi yang tidak dapat diakses melalui RBAC.

**FR-PUSH-05** Kategori push notification MVP meliputi:

- Laporan harian menunggu validasi untuk Super Admin dan Admin/Pengelola.
- Laporan ditolak atau dibuka untuk revisi bagi Petugas pembuat laporan.
- Pengingat laporan harian yang belum lengkap bagi Petugas terkait.
- Transaksi keuangan menunggu validasi bagi Super Admin dan Admin/Pengelola.
- Transaksi disetujui atau ditolak bagi Bendahara pembuat transaksi.
- Jadwal perawatan mendekati jatuh tempo atau terlambat bagi Admin/Pengelola dan Petugas yang ditugaskan.
- Aset berubah menjadi Rusak Berat bagi Super Admin dan Admin/Pengelola.
- Informasi penggajian yang relevan bagi Bendahara/Keuangan.

**FR-PUSH-06** Klik notifikasi membuka halaman tujuan yang sesuai di PWA. Sistem memeriksa ulang autentikasi dan authorization sebelum menampilkan data.

**FR-PUSH-07** Sistem mencatat notifikasi dibuat, dikirim, gagal, kedaluwarsa, dan dibuka bila informasi tersebut tersedia dari platform.

**FR-PUSH-08** Subscription yang tidak valid atau kedaluwarsa dinonaktifkan otomatis tanpa menghapus subscription aktif pada perangkat lain.

## 8. Alur Pengguna Utama

### 8.1 Pelaporan Operasional Harian

1. Petugas masuk ke sistem.
2. Petugas melihat tugas atau data harian yang belum lengkap.
3. Petugas mencatat sampah masuk, kondisi siklus, dan hasil produksi sebagai Draft.
4. Petugas memperbaiki data miliknya sendiri lalu mengajukan laporan.
5. Sistem mengirim push notification kepada validator sesuai lokasi.
6. Admin/Pengelola memvalidasi atau menolak laporan.
7. Jika ditolak, Petugas menerima alasan dan dapat memperbaiki laporan. Jika laporan tervalidasi perlu diubah, Petugas mengajukan permintaan revisi.
8. Admin/Pengelola membuka revisi atau menolaknya; seluruh perubahan tercatat pada audit log.
9. Dashboard produksi diperbarui menggunakan data tervalidasi.

### 8.2 Absensi hingga Penggajian

1. Petugas melakukan check-in dan check-out.
2. Sistem menghitung durasi dan status kehadiran.
3. Pada akhir periode, Bendahara membuat draft penggajian.
4. Sistem mengambil komponen gaji, bonus, dan potongan.
5. Pengelola/pejabat berwenang menyetujui penggajian.
6. Bendahara menandai pembayaran selesai.
7. Sistem membuat slip gaji dan transaksi kas keluar.

### 8.3 Penjualan dan Kas Masuk

1. Bendahara memilih item penjualan dan memasukkan kuantitas serta harga.
2. Sistem menghitung total transaksi.
3. Bendahara mencatat pembayaran dan mengajukan transaksi untuk validasi.
4. Admin/Pengelola memvalidasi atau menolak tanpa mengubah isi transaksi.
5. Sistem menerbitkan bukti transaksi dan kas masuk terkait setelah transaksi final.
6. Saldo serta laporan keuangan diperbarui.

### 8.4 Instalasi PWA dan Push Notification

1. Pengguna membuka aplikasi dari browser ponsel yang kompatibel dan login.
2. Sistem menawarkan instalasi PWA tanpa menghalangi penggunaan aplikasi.
3. Setelah terpasang, pengguna membuka Pengaturan Notifikasi dan memilih Aktifkan.
4. Sistem menjelaskan kategori notifikasi dan meminta izin browser melalui tindakan pengguna.
5. Setelah disetujui, sistem menyimpan subscription untuk perangkat tersebut.
6. Event operasional membuat notifikasi sesuai role, permission, lokasi, penugasan, dan preferensi.
7. Ketika notifikasi dibuka, aplikasi memeriksa login serta authorization sebelum menampilkan tujuan.

### 8.5 Perawatan Aset

1. Pengguna mencatat kondisi aset atau membuat jadwal perawatan.
2. Sistem menampilkan pengingat ketika jadwal mendekati jatuh tempo.
3. Petugas menyelesaikan perawatan dan mencatat tindakan serta biaya.
4. Pengelola meninjau hasil perawatan.
5. Sistem memperbarui kondisi aset, riwayat, dan kas keluar bila disetujui.

## 9. Model Data Inti

| Entitas | Data utama |
|---|---|
| User | identitas, kredensial, status, lokasi utama |
| Role | kode, nama empat role sistem, status |
| Permission | kode permission, domain, tindakan, deskripsi |
| User Role | pengguna, role, waktu pemberian, pemberi akses |
| Role Permission | role, permission, scope dan kondisi akses |
| Petugas | identitas internal, jabatan, kontak, status, lokasi |
| Lokasi | kode, nama, alamat, status |
| Sumber Sampah | nama sumber, kontak opsional, status |
| Laporan Sampah | jenis, tanggal, berat, sumber/jenis, lokasi, catatan, pencatat, status validasi |
| Batch Maggot | kode batch, tanggal mulai, asal telur, lokasi, status |
| Monitoring Siklus | batch, tahap, nilai, satuan, tanggal, catatan, status validasi |
| Hasil Produksi | batch, jenis hasil, jumlah, satuan, grade, tanggal, status validasi |
| Permintaan Revisi | jenis dan ID laporan, pemohon, alasan, status, pembuka revisi, batas waktu |
| Absensi | petugas, check-in, check-out, status, durasi, koreksi |
| Periode Gaji | periode, status, pembuat, penyetuju |
| Detail Gaji | petugas, gaji pokok, bonus, potongan, total, status bayar |
| Kategori Keuangan | tipe masuk/keluar, nama, status |
| Transaksi Keuangan | nomor, tipe, kategori, nominal, tanggal, status validasi, validator, bukti, void reason |
| Penjualan | nomor, item, kuantitas, harga, pembayaran, total |
| Aset | kode, nama, kategori, lokasi, nilai, kondisi, status |
| Jadwal Perawatan | aset, rencana, jenis, petugas, biaya, status |
| Riwayat Perawatan | aset, tindakan, komponen, biaya, kondisi akhir, bukti |
| Push Subscription | pengguna, perangkat, endpoint terenkripsi, public key, auth secret, status, persetujuan |
| Preferensi Notifikasi | pengguna, perangkat, kategori, kanal, status aktif |
| Notification Log | event, penerima, perangkat, kategori, status kirim, waktu, target URL |
| Audit Log | pengguna, tindakan, modul, referensi data, sebelum/sesudah, waktu |

Seluruh entitas transaksi perlu memiliki `created_at`, `created_by`, `updated_at`, dan `updated_by`. Data sensitif dan tindakan final perlu memiliki jejak audit yang tidak dapat diubah oleh pengguna biasa.

## 10. Aturan Bisnis

1. Satuan berat baku adalah kilogram; input satuan lain harus dikonversi dan nilai asal tetap disimpan bila diperlukan.
2. Tanggal transaksi final yang sudah masuk periode terkunci tidak dapat diubah tanpa membuka periode oleh pengguna berwenang.
3. Saldo kas hanya menggunakan transaksi berstatus final; draft dan void tidak dihitung.
4. Nominal transaksi, nilai aset, dan komponen gaji tidak boleh negatif. Potongan disimpan sebagai nilai positif dengan tipe potongan.
5. Data historis tidak dihapus permanen jika sudah dirujuk laporan atau transaksi lain.
6. Setiap koreksi data penting harus menyimpan alasan dan audit log.
7. Transaksi otomatis dari gaji atau perawatan menyimpan referensi dua arah untuk mencegah pencatatan ganda.
8. Aset kritis adalah aset berstatus Rusak Berat atau melewati jadwal perawatan sesuai aturan organisasi.
9. Satu batch maggot memiliki kode unik dan tidak dapat ditutup sebelum hasil akhir atau alasan penghentian dicatat.
10. Zona waktu sistem dan batas pergantian hari ditetapkan pada konfigurasi organisasi.
11. Laporan operasional tervalidasi hanya dapat diubah setelah permintaan revisi dibuka oleh Super Admin atau Admin/Pengelola.
12. Petugas tidak dapat mengedit atau menghapus laporan milik petugas lain.
13. Bendahara hanya dapat mengubah transaksi Draft atau Ditolak; transaksi yang telah divalidasi tidak dapat diubah oleh Bendahara.
14. `finance.delete` hanya tersedia untuk Super Admin dan selalu menghasilkan void/soft delete serta audit log, bukan hard delete.
15. Push notification bersifat opt-in per perangkat. Penolakan izin tidak boleh menghalangi fitur inti aplikasi.
16. Payload push notification tidak boleh memuat informasi keuangan sensitif, data personal, token akses, atau data yang melampaui permission penerima.
17. Data yang ditampilkan setelah notifikasi dibuka selalu mengikuti pemeriksaan RBAC terkini, bukan hak akses saat notifikasi dibuat.

## 11. Kebutuhan Nonfungsional

### 11.1 Keamanan

- Kata sandi disimpan menggunakan hashing adaptif yang aman.
- Seluruh komunikasi produksi menggunakan HTTPS.
- Kontrol akses diterapkan di sisi server, bukan hanya antarmuka.
- Sesi berakhir otomatis setelah periode tidak aktif yang dapat dikonfigurasi.
- Login gagal berulang dibatasi dan dicatat.
- Dokumen bukti transaksi dibatasi berdasarkan tipe dan ukuran file serta dipindai sesuai kemampuan infrastruktur.
- Audit log penting hanya dapat dibaca oleh pengguna berwenang dan tidak dapat diedit melalui aplikasi.
- Backup dan proses pemulihan diuji secara berkala.
- Authorization RBAC diterapkan di setiap endpoint dan query; pemeriksaan UI hanya menjadi lapisan tambahan.
- Endpoint push subscription memerlukan autentikasi, perlindungan CSRF yang sesuai, validasi asal, dan pembatasan laju.
- Endpoint, key, dan auth secret push subscription diperlakukan sebagai data sensitif dan dienkripsi saat tersimpan bila infrastruktur mendukung.
- Pesan push menggunakan konten minimum dan tidak memuat informasi sensitif pada lock screen.

### 11.2 Kinerja

- Halaman utama ditargetkan tampil dalam waktu maksimal 3 detik pada koneksi operasional normal untuk 95% permintaan.
- Pencarian/filter daftar umum ditargetkan merespons maksimal 2 detik untuk volume data MVP.
- Ekspor laporan besar dapat diproses sebagai pekerjaan latar belakang dengan indikator status.
- Muatan awal mobile dioptimalkan melalui pemisahan bundle, kompresi, lazy loading, dan cache aset statis.
- Service worker tidak boleh menyajikan data pengguna lama setelah logout atau pergantian akun.

### 11.3 Ketersediaan dan Keandalan

- Target ketersediaan awal minimal 99% per bulan di luar pemeliharaan terjadwal.
- Sistem mencegah pengiriman formulir ganda akibat klik berulang.
- Proses transaksi yang saling terkait menggunakan transaksi basis data agar tidak menghasilkan data parsial.
- Backup harian disimpan sesuai kebijakan retensi organisasi.
- Kegagalan pengiriman push tidak menggagalkan transaksi bisnis utama; pengiriman diproses melalui antrean dengan retry terbatas.
- Versi service worker baru diaktifkan secara aman dan tidak menghapus draft yang sedang dikerjakan.

### 11.4 Kemudahan Penggunaan

- Antarmuka mobile-first dan responsif untuk ponsel, tablet, serta desktop, dimulai dari lebar 320 piksel.
- Formulir lapangan mengutamakan input singkat, label bahasa Indonesia, dan validasi yang jelas.
- Format tanggal, angka, mata uang rupiah, dan satuan konsisten.
- Status tidak hanya dibedakan menggunakan warna; tersedia label atau ikon pendamping.
- Target sentuh utama minimal 44 × 44 CSS pixel dan tidak bergantung pada interaksi hover.
- Navigasi mobile menyediakan akses cepat ke Dashboard, Tambah Laporan, Absensi, Notifikasi, dan Profil sesuai role.
- Status online/offline, proses penyimpanan, keberhasilan, dan kegagalan harus terlihat jelas.

### 11.5 PWA, Kompatibilitas, dan Aksesibilitas

- Mendukung dua versi terbaru Chrome, Edge, Firefox, dan Safari pada saat rilis.
- PWA dapat dipasang pada browser/OS yang mendukung manifest, service worker, dan kriteria instalasi platform.
- Push notification tersedia hanya pada kombinasi browser/OS yang mendukung Web Push. Jika tidak didukung atau izin ditolak, notifikasi dalam aplikasi tetap tersedia.
- Aplikasi memenuhi pemeriksaan installability PWA dan memiliki ikon standar serta *maskable icon*.
- Service worker hanya berjalan melalui HTTPS, kecuali pada lingkungan pengembangan lokal.
- Halaman offline menjelaskan bahwa transaksi memerlukan koneksi dan menyediakan tindakan untuk mencoba kembali.
- Target aksesibilitas WCAG 2.1 level AA untuk alur utama.
- Navigasi keyboard dan label form harus tersedia untuk alur utama.

### 11.6 Privasi dan Retensi

- Sistem hanya menyimpan data pribadi petugas yang diperlukan untuk operasional.
- Hak melihat dan mengekspor data pribadi dibatasi.
- Masa retensi data dan prosedur penghapusan/anonimisasi ditetapkan sebelum produksi.
- Persetujuan notifikasi, preferensi kategori, dan pencabutan subscription disimpan sebagai bukti pengaturan pengguna.
- Logout menawarkan pilihan untuk mempertahankan atau menonaktifkan push pada perangkat; perangkat bersama disarankan menonaktifkannya.

## 12. Rancangan Navigasi

- Dashboard
- Operasional
  - Sampah Organik
  - Sampah Non-organik
  - Batch dan Siklus Maggot
  - Hasil Produksi
- Petugas
  - Data Petugas
  - Absensi
  - Penggajian
- Keuangan
  - Kasir/Penjualan
  - Kas Masuk
  - Kas Keluar
  - Buku Kas
  - Laba Rugi
- Aset
  - Daftar Aset
  - Jadwal Perawatan
  - Riwayat Perawatan
- Laporan
- Notifikasi
- Profil dan Pengaturan
  - Perangkat Terpasang
  - Preferensi Notifikasi
- Administrasi
  - Pengguna dan Hak Akses
  - Data Master
  - Pengaturan
  - Audit Log

## 13. Kriteria Penerimaan MVP

MVP dinyatakan siap uji pengguna jika:

1. Empat role sistem—Super Admin, Admin/Pengelola, Petugas Operasional, dan Bendahara/Keuangan—dapat masuk dan hanya mengakses menu, API, data, lokasi, serta tindakan yang diizinkan.
2. Pengujian negatif membuktikan pengguna tidak dapat melewati RBAC dengan memanggil URL/API secara langsung.
3. Petugas dapat menyelesaikan laporan operasional harian dari perangkat ponsel dengan lebar layar 320 piksel tanpa scroll horizontal pada alur utama.
4. Petugas hanya dapat mengedit laporan miliknya sendiri yang belum divalidasi.
5. Laporan tervalidasi tidak dapat diedit Petugas sebelum permintaan revisi dibuka Admin/Pengelola atau Super Admin.
6. Bendahara dapat mengedit transaksi sebelum validasi, tetapi tidak dapat mengedit atau menghapus transaksi tervalidasi.
7. Admin/Pengelola dapat memvalidasi transaksi keuangan tanpa dapat mengubah isi transaksi.
8. Hanya Super Admin dapat melakukan void/soft delete transaksi dan tindakan tersebut memiliki alasan serta audit log.
9. Aplikasi memenuhi kriteria instalasi PWA pada perangkat uji yang kompatibel dan dapat dibuka dalam mode standalone.
10. Service worker menyediakan app shell/halaman offline, menampilkan status offline, dan tidak menyatakan transaksi berhasil sebelum server mengonfirmasi.
11. Pengguna dapat mengaktifkan push setelah instalasi melalui tindakan eksplisit, memilih kategori, dan menonaktifkannya kembali per perangkat.
12. Push notification dikirim kepada role dan scope yang tepat, membuka target yang benar, dan tetap memeriksa ulang authorization.
13. Pengguna yang tidak mendukung atau menolak push tetap dapat memakai seluruh fitur inti dan melihat notifikasi dalam aplikasi.
14. Subscription tidak valid dinonaktifkan tanpa memengaruhi perangkat aktif lainnya.
15. Dashboard menghitung ringkasan dari data transaksi sumber dengan benar.
16. Batch dapat dipantau dari tahap awal sampai hasil produksi.
17. Check-in, check-out, koreksi, dan rekap absensi berjalan dengan audit trail.
18. Penggajian dapat dihitung, disetujui, dibayar, dan menghasilkan kas keluar tanpa duplikasi.
19. Penjualan dapat menghasilkan bukti transaksi dan kas masuk.
20. Saldo kas, buku kas, arus kas, dan laba rugi sesuai dengan data uji yang disepakati.
21. Kondisi, jadwal, dan riwayat perawatan aset dapat ditelusuri.
22. Sepuluh jenis laporan dapat difilter dan diekspor ke PDF serta Excel.
23. Transaksi final tidak dapat dihapus permanen dan koreksi penting tercatat.
24. Backup uji dapat dipulihkan pada lingkungan pengujian.

## 14. Tahapan Implementasi

### Fase 0 — Discovery dan Validasi

- Konfirmasi proses kerja aktual dan istilah operasional.
- Tetapkan satuan pengukuran tiap tahap siklus.
- Petakan kategori transaksi dan rumus laba rugi.
- Tetapkan aturan absensi, gaji, persetujuan, dan periode terkunci.
- Inventarisasi format laporan yang sudah digunakan.
- Validasi matriks empat role, permission, scope lokasi, dan alur revisi/validasi.
- Tentukan kategori, jadwal, penerima, dan kebijakan konten push notification.

### Fase 1 — Fondasi dan Operasional

- Autentikasi, empat role MVP, permission, enforcement RBAC pada API, dan audit log.
- Fondasi mobile-first, Web App Manifest, ikon PWA, service worker, app shell, halaman offline, dan registrasi push subscription.
- Sampah organik/non-organik, batch, monitoring siklus, hasil produksi.
- Alur draft, validasi, penolakan, dan permintaan revisi laporan operasional.
- Dashboard operasional dasar dan push notification operasional.
- Laporan produksi dan sampah.

### Fase 2 — SDM dan Keuangan

- Data petugas, absensi, penggajian.
- Kasir, kas masuk, kas keluar, buku kas, dan laba rugi sederhana.
- Laporan SDM dan keuangan.
- Alur validasi transaksi dan push notification keuangan/penggajian.

### Fase 3 — Aset dan Penyempurnaan

- Inventaris, kondisi, jadwal, dan riwayat perawatan.
- Notifikasi dalam aplikasi, push perawatan/aset, audit log lengkap, dan optimasi ekspor.
- Uji penerimaan pengguna, migrasi data awal, pelatihan, dan go-live.

## 15. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Definisi satuan siklus tidak konsisten | Grafik dan perbandingan menyesatkan | Tetapkan data master satuan dan prosedur ukur sebelum implementasi |
| Pengguna terlambat memasukkan data | Dashboard tidak mencerminkan kondisi aktual | Pengingat, indikator laporan belum lengkap, dan SOP harian |
| Transaksi tercatat ganda | Saldo dan laba rugi salah | Nomor unik, idempotensi, referensi transaksi otomatis, audit log |
| Koneksi lapangan tidak stabil | Input gagal atau berulang | Form ringkas, retry aman, indikator penyimpanan; mode offline dievaluasi pasca-MVP |
| Rumus gaji/laba rugi belum disepakati | Hasil laporan diperdebatkan | Validasi aturan bersama Bendahara dan Pengelola pada fase discovery |
| Data awal tidak rapi | Migrasi lambat | Template impor, pembersihan, dan uji migrasi sebelum go-live |
| Bukti transaksi mengandung data sensitif | Kebocoran informasi | Pembatasan akses, validasi file, enkripsi penyimpanan bila tersedia |
| RBAC hanya diterapkan di tampilan | Akses data melalui API tetap terbuka | Enforcement pada middleware/policy server, query scope, default deny, dan pengujian negatif |
| Permission terlalu longgar akibat konfigurasi | Pengguna melihat atau mengubah data di luar tugas | Empat role sistem, perubahan hanya oleh Super Admin, audit log, dan review matriks berkala |
| Push tidak didukung atau ditolak pengguna | Pengingat tidak diterima | Sediakan notifikasi dalam aplikasi dan jangan menjadikan push sebagai satu-satunya kanal |
| Payload push tampil di lock screen | Informasi sensitif terlihat pihak lain | Gunakan pesan generik dan muat detail hanya setelah login serta pemeriksaan RBAC |
| Cache PWA menyajikan data akun lama | Kebocoran pada perangkat bersama | Jangan cache respons data sensitif, bersihkan cache saat logout, dan uji pergantian akun |

## 16. Asumsi

- Rumah Maggot memiliki koneksi internet yang memadai untuk aplikasi web.
- Mayoritas pengguna memiliki ponsel dan browser modern yang mendukung responsive web; kemampuan instalasi PWA dan Web Push mengikuti dukungan perangkat masing-masing.
- Satu organisasi dapat memiliki satu atau beberapa lokasi operasional.
- Bahasa utama aplikasi adalah bahasa Indonesia dan mata uang utama rupiah.
- Laporan laba rugi MVP bersifat sederhana, bukan pengganti sistem akuntansi formal.
- Persetujuan transaksi/penggajian dapat diaktifkan sesuai kebijakan organisasi.
- Data lama akan dimigrasikan menggunakan template Excel terstandar bila diperlukan.
- Empat role MVP bersifat tetap; Ketua KSM/Pimpinan menggunakan Admin/Pengelola.
- Transaksi dan pengiriman formulir memerlukan koneksi pada MVP; sinkronisasi formulir offline berada di luar ruang lingkup.
- Push notification hanya aktif setelah instalasi, tindakan opt-in pengguna, serta izin browser.

## 17. Keputusan yang Perlu Dikonfirmasi

Pertanyaan berikut perlu diputuskan pada fase discovery dan tidak menghalangi draft awal:

1. Apakah jumlah telur, larva, pre-pupa, dan lalat dicatat sebagai berat, estimasi jumlah, atau keduanya?
2. Apakah satu lokasi menggunakan beberapa kandang/biopond yang perlu dilacak terpisah?
3. Apakah stok produk jadi dan pengurangan stok saat penjualan harus masuk MVP?
4. Apakah absensi memerlukan lokasi GPS, foto, sif, atau persetujuan atasan?
5. Bagaimana rumus bonus, potongan, lembur, dan keterlambatan?
6. Apakah transaksi membutuhkan alur draft–persetujuan–final untuk nominal tertentu?
7. Apakah saldo kas dibedakan berdasarkan kas tunai, bank, dan dompet digital?
8. Metode laba rugi apa yang digunakan: kas atau akrual sederhana?
9. Siapa yang berwenang membuka periode keuangan yang telah dikunci?
10. Berapa lama dokumen, audit log, dan data personal harus disimpan?
11. Berapa lama laporan yang telah diajukan tetapi belum divalidasi boleh menunggu sebelum eskalasi?
12. Siapa penerima cadangan jika validator lokasi tidak aktif?
13. Berapa hari/jam sebelum jatuh tempo notifikasi perawatan dan laporan harian dikirim?
14. Apakah pengguna boleh mengaktifkan push pada beberapa perangkat secara bersamaan?
15. Apakah Admin/Pengelola boleh mengekspor detail gaji atau hanya melihat total agregat?

## 18. Definisi Selesai per Fitur

Sebuah fitur dinyatakan selesai jika:

- Kebutuhan dan kriteria penerimaan telah terpenuhi.
- Hak akses dan validasi sisi server telah diuji.
- Pengujian RBAC mencakup role, permission, kepemilikan data, lokasi, status data, dan upaya akses langsung ke API.
- Alur normal, data kosong, input tidak valid, dan kegagalan relevan telah diuji.
- Tampilan responsif dan aksesibilitas dasar telah diperiksa.
- Untuk fitur PWA, installability, mode standalone, lifecycle service worker, kondisi offline, opt-in/opt-out push, dan deep link notifikasi telah diuji pada perangkat target.
- Audit log tersedia untuk tindakan penting.
- Dokumentasi pengguna dan catatan perubahan diperbarui.
- Tidak terdapat cacat kritis atau tinggi yang masih terbuka.
