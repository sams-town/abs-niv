# Requirements Document

## Introduction

Dokumen ini mendefinisikan kebutuhan untuk tiga fitur baru pada sistem absensi berbasis Laravel 8 yang sudah berjalan. Sistem saat ini memiliki manajemen karyawan umum (role: admin, hrd, kepala_cabang, general_manager, dan user), pencatatan absensi harian di tabel `mapping_shifts`, serta alur cuti tunggal (Pending → Diterima/Ditolak) yang disetujui oleh semua approver sekaligus.

Tiga fitur yang akan ditambahkan:

1. **Report Absensi Pivot** – Laporan absensi berbentuk tabel silang dengan visualisasi diagram dan export multi-format.
2. **Menu dan Data Dosen** – Tipe pengguna "Dosen" dengan modul manajemen terpisah dari pegawai umum.
3. **Alur Approval Cuti Bertingkat** – Pengajuan cuti melalui dua tahap: persetujuan Manager/Kepala Cabang terlebih dahulu, kemudian Admin/HRD untuk final.

---

## Glossary

- **System**: Sistem absensi Laravel yang sudah berjalan.
- **Report_Generator**: Komponen yang menghasilkan laporan pivot absensi, grafik, dan file export.
- **Pivot_Table**: Tabel silang di mana baris mewakili pegawai atau bulan dan kolom mewakili tanggal atau kategori status absensi.
- **Dosen_Manager**: Komponen yang mengelola data, menu, dan hak akses khusus untuk tipe pengguna Dosen.
- **Cuti_Workflow**: Komponen yang mengatur alur pengajuan dan approval cuti bertingkat.
- **Approval_Level_1**: Tahap pertama approval cuti oleh Kepala Cabang atau Manager yang satu lokasi dengan pengaju.
- **Approval_Level_2**: Tahap kedua approval cuti oleh Admin atau HRD setelah Level 1 disetujui.
- **MappingShift**: Record absensi harian di tabel `mapping_shifts` yang menyimpan jam masuk, jam pulang, status_absen, telat, dan pulang_cepat.
- **Cuti**: Record pengajuan cuti/izin di tabel `cutis`.
- **Dosen**: Tipe pengguna di `users` yang berperan sebagai tenaga pengajar, berbeda dari pegawai administrasi umum.
- **Admin**: Pengguna dengan role `admin` di Spatie Permission.
- **HRD**: Pengguna dengan role `hrd` di Spatie Permission.
- **Kepala_Cabang**: Pengguna dengan role `kepala_cabang` di Spatie Permission, mewakili manajer tingkat cabang.
- **General_Manager**: Pengguna dengan role `general_manager` di Spatie Permission.
- **status_cuti**: Kolom pada tabel `cutis` yang mencatat status pengajuan cuti.
- **Notifikasi**: Pemberitahuan real-time via Pusher, in-app notification, dan opsional WhatsApp API.

---

## Requirements

---

### Requirement 1: Laporan Absensi Pivot Per Pegawai

**User Story:** Sebagai Admin atau HRD, saya ingin melihat dan mengexport laporan absensi berbentuk tabel pivot (baris = pegawai, kolom = tanggal) sehingga saya dapat memantau kehadiran seluruh pegawai dalam satu periode secara visual.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengakses halaman laporan pivot dengan parameter `lokasi_id`, `tanggal_mulai`, dan `tanggal_akhir`, THE Report_Generator SHALL menampilkan tabel pivot di mana setiap baris adalah satu pegawai dan setiap kolom adalah satu tanggal dalam rentang yang dipilih.

2. THE Report_Generator SHALL mengisi setiap sel pivot dengan kode singkat status absensi: `H` (Hadir/Masuk), `C` (Cuti), `I` (Izin Masuk), `IT` (Izin Telat), `IP` (Izin Pulang Cepat), `S` (Sakit), `L` (Libur), `A` (Alfa), atau `-` jika tidak ada record pada tanggal tersebut.

3. WHEN tanggal dalam pivot termasuk hari Minggu berdasarkan kalender Gregorian, THE Report_Generator SHALL menandai kolom tanggal tersebut dengan warna latar berbeda (misalnya abu-abu) agar mudah dibedakan dari hari kerja.

4. THE Report_Generator SHALL menampilkan kolom ringkasan di sisi kanan pivot yang berisi: total Hadir, total Cuti, total Izin Masuk, total Alfa, total Sakit, dan persentase kehadiran untuk setiap pegawai dalam rentang tanggal yang dipilih.

5. WHEN rentang tanggal yang dipilih melebihi 31 hari, THE Report_Generator SHALL tetap menghasilkan pivot yang lengkap tanpa memotong data, dan menyesuaikan lebar tabel secara horizontal. WHEN rentang tanggal 31 hari atau kurang dan kolom tabel memerlukan scroll horizontal, THE Report_Generator SHALL membiarkan lebar tabel mengikuti konten tanpa penyesuaian khusus.

6. WHEN Admin atau HRD memilih filter berdasarkan `lokasi_id`, THE Report_Generator SHALL hanya menampilkan pegawai yang terdaftar di lokasi tersebut.

7. IF parameter `tanggal_mulai` atau `tanggal_akhir` tidak diberikan atau formatnya tidak valid (bukan `Y-m-d`), THEN THE Report_Generator SHALL menampilkan pesan kesalahan yang menjelaskan parameter mana yang tidak valid, menghentikan seluruh proses pembuatan laporan, dan tidak menghasilkan laporan maupun diagram apapun.

---

### Requirement 2: Export Laporan Pivot ke Excel (Format Pivot)

**User Story:** Sebagai Admin atau HRD, saya ingin mengexport laporan pivot absensi ke file Excel sehingga saya dapat menyimpan dan mendistribusikan data kehadiran untuk keperluan administrasi.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengklik tombol "Export Excel" pada halaman laporan pivot, THE Report_Generator SHALL menghasilkan file `.xlsx` yang mempertahankan struktur pivot (baris = pegawai, kolom = tanggal) sesuai dengan tampilan di halaman web.

2. THE Report_Generator SHALL menyertakan baris header di baris pertama file Excel yang berisi: "No", "Nama Pegawai", diikuti setiap tanggal dalam format `DD/MM`, kemudian kolom ringkasan (Hadir, Cuti, Izin, Alfa, Sakit, Persentase).

3. THE Report_Generator SHALL menerapkan border tipis pada seluruh sel data dan menebalkan (bold) baris header serta kolom nama pegawai pada file Excel yang dihasilkan.

4. WHEN file Excel dihasilkan, THE Report_Generator SHALL memberi nama file dengan format `Laporan_Absensi_Pivot_{tanggal_mulai}_{tanggal_akhir}.xlsx`.

5. THE Report_Generator SHALL menerapkan warna latar kuning pada sel yang berisi status `A` (Alfa) dan warna latar merah muda pada sel status `S` (Sakit) dalam file Excel untuk mempermudah identifikasi visual.

---

### Requirement 3: Export Laporan Absensi ke PDF

**User Story:** Sebagai Admin atau HRD, saya ingin mengexport laporan absensi ke format PDF sehingga saya dapat mencetak atau mengirimkan laporan secara formal.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengklik tombol "Export PDF" pada halaman laporan pivot, THE Report_Generator SHALL menghasilkan file PDF menggunakan library Barryvdh DomPDF dengan orientasi landscape.

2. THE Report_Generator SHALL menyertakan header laporan pada file PDF yang berisi: nama institusi (dari tabel `settings`), judul "Laporan Absensi", rentang tanggal, dan nama lokasi.

3. WHEN jumlah kolom tanggal dalam pivot melebihi 15 (yaitu 16 kolom ke atas), THE Report_Generator SHALL memecah laporan PDF menjadi beberapa halaman berdasarkan rentang tanggal agar tabel tidak terpotong. WHEN jumlah kolom tanggal adalah tepat 15 atau kurang, THE Report_Generator SHALL menyimpan seluruh laporan dalam satu halaman tanpa pemecahan.

4. THE Report_Generator SHALL menyertakan ringkasan total di bagian bawah setiap halaman PDF yang menampilkan jumlah total Hadir, Alfa, Cuti, dan Izin untuk seluruh pegawai pada halaman tersebut.

5. WHEN file PDF berhasil dihasilkan, THE Report_Generator SHALL mengirimkan file tersebut ke browser dengan nama `Laporan_Absensi_{tanggal_mulai}_{tanggal_akhir}.pdf`.

---

### Requirement 4: Visualisasi Diagram Kehadiran

**User Story:** Sebagai Admin atau HRD, saya ingin melihat grafik/diagram ringkasan kehadiran pada halaman laporan sehingga saya dapat memahami tren kehadiran secara cepat tanpa harus membaca tabel.

#### Acceptance Criteria

1. THE Report_Generator SHALL menampilkan diagram batang (bar chart) pada halaman laporan pivot yang memvisualisasikan total per kategori status absensi (Hadir, Cuti, Izin, Alfa, Sakit, Libur) untuk seluruh pegawai dalam periode yang dipilih. WHEN semua kategori memiliki nilai nol, THE Report_Generator SHALL tetap menampilkan diagram batang kosong dengan nilai nol untuk semua kategori.

2. THE Report_Generator SHALL menampilkan diagram garis (line chart) pada halaman laporan pivot yang memvisualisasikan tren jumlah pegawai yang hadir per hari selama periode yang dipilih. WHEN semua hari dalam periode memiliki jumlah kehadiran nol, THE Report_Generator SHALL tetap menampilkan diagram garis dengan nilai nol di semua titik.

3. THE Report_Generator SHALL merender diagram menggunakan library JavaScript Chart.js yang sudah tersedia di browser tanpa memerlukan pemasangan dependensi backend tambahan.

4. WHEN tidak ada data MappingShift dalam rentang tanggal yang dipilih, THE Report_Generator SHALL menyembunyikan semua diagram dan menampilkan pesan "Tidak ada data untuk periode ini" sebagai pengganti diagram.

---

### Requirement 5: Laporan Rekap Bulanan Per Pegawai (Pivot Bulan)

**User Story:** Sebagai Admin atau HRD, saya ingin melihat rekap absensi per bulan dengan baris berupa pegawai dan kolom berupa bulan sehingga saya dapat membandingkan pola kehadiran sepanjang tahun.

#### Acceptance Criteria

1. WHEN Admin atau HRD memilih mode "Rekap Bulanan" dan menentukan tahun, THE Report_Generator SHALL menampilkan tabel pivot di mana setiap baris adalah satu pegawai dan setiap kolom adalah satu bulan (Januari–Desember).

2. THE Report_Generator SHALL mengisi setiap sel pivot bulanan dengan jumlah hari hadir (Hadir + Izin Telat + Izin Pulang Cepat dihitung sebagai hadir) dari tabel `mapping_shifts` pada bulan tersebut.

3. THE Report_Generator SHALL menampilkan kolom total di ujung kanan yang menjumlahkan kehadiran pegawai sepanjang tahun.

4. WHERE fitur filter lokasi diaktifkan, THE Report_Generator SHALL hanya menampilkan pegawai yang terdaftar pada `lokasi_id` yang dipilih.

---

### Requirement 6: Manajemen Data Dosen

**User Story:** Sebagai Admin, saya ingin mengelola data dosen dalam modul terpisah sehingga data dosen tidak tercampur dengan data pegawai administrasi umum dan dapat dikelola sesuai kebutuhan institusi pendidikan.

#### Acceptance Criteria

1. THE System SHALL menyediakan kolom `tipe_user` bertipe string (nilai: `pegawai` atau `dosen`, default `pegawai`) pada tabel `users` melalui migration baru, tanpa mengubah data pengguna yang sudah ada.

2. WHEN Admin mengakses menu "Data Dosen", THE Dosen_Manager SHALL menampilkan daftar pengguna yang memiliki `tipe_user = 'dosen'` beserta kolom: Nama, NIDN, Jabatan Akademik, Email, Telepon, dan Status Aktif.

3. WHEN Admin menambahkan data dosen baru, THE Dosen_Manager SHALL menyimpan record ke tabel `users` dengan `tipe_user = 'dosen'` dan secara otomatis menetapkan role Spatie Permission `dosen` pada pengguna tersebut.

4. WHEN Admin mengedit data dosen, THE Dosen_Manager SHALL memperbarui hanya kolom yang diedit tanpa mengubah nilai `tipe_user` dari `dosen` ke nilai lain.

5. WHEN Admin menghapus data dosen, THE Dosen_Manager SHALL menonaktifkan akun (soft delete atau set `status_aktif = false`) alih-alih menghapus secara permanen, untuk menjaga integritas data absensi historis.

6. THE Dosen_Manager SHALL menyediakan kolom tambahan khusus dosen: `nidn` (string, nullable), `jabatan_akademik` (string, nullable), `mata_kuliah` (string, nullable) pada tabel `users` melalui migration yang sama dengan kriteria 1.

7. IF Admin mencoba menyimpan data dosen baru dengan NIDN yang sudah digunakan oleh dosen lain yang aktif, THEN THE Dosen_Manager SHALL menolak penyimpanan dan menampilkan pesan error "NIDN sudah terdaftar".

---

### Requirement 7: Menu Navigasi Khusus Dosen

**User Story:** Sebagai Admin, saya ingin menu navigasi sistem menampilkan section "Dosen" yang terpisah dari section "Pegawai" sehingga pengelolaan keduanya tidak membingungkan.

#### Acceptance Criteria

1. THE System SHALL menampilkan item menu "Data Dosen" pada navigasi sidebar yang hanya terlihat oleh pengguna dengan role `admin` atau `hrd`. Role-based permission ini SHALL selalu diutamakan di atas pengaturan visibilitas menu lainnya, sehingga Admin dan HRD selalu melihat menu Data Dosen tanpa pengecualian.

2. THE System SHALL menampilkan halaman daftar dosen di route `/dosen` yang berbeda dari route `/pegawai` (karyawan umum).

3. WHEN pengguna dengan `tipe_user = 'dosen'` login, THE System SHALL menampilkan menu navigasi yang menyembunyikan menu-menu khusus pegawai administrasi (seperti Payroll, Kasbon, Tunjangan) dan hanya menampilkan menu yang relevan untuk dosen (Absensi, Cuti, Laporan Kerja, Dokumen).

4. WHEN pengguna dengan `tipe_user = 'pegawai'` login, THE System SHALL menampilkan menu navigasi yang sama seperti sebelum fitur ini ditambahkan, tanpa perubahan apapun pada tampilan menu pegawai yang sudah ada.

---

### Requirement 8: Absensi dan Laporan Khusus Dosen

**User Story:** Sebagai Admin atau HRD, saya ingin laporan absensi dapat difilter berdasarkan tipe pengguna (dosen atau pegawai) sehingga laporan tidak tercampur antara dua kelompok yang berbeda.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengakses halaman rekap data atau laporan pivot, THE Report_Generator SHALL menyediakan dropdown filter "Tipe Pegawai" dengan pilihan: Semua, Pegawai, Dosen. IF dropdown gagal dimuat, THE Report_Generator SHALL tetap mengambil dan menampilkan seluruh data (setara dengan pilihan "Semua") tanpa memblokir akses laporan.

2. WHEN filter "Dosen" dipilih, THE Report_Generator SHALL hanya mengambil data dari tabel `mapping_shifts` yang `user_id`-nya merujuk ke pengguna dengan `tipe_user = 'dosen'`.

3. WHEN filter "Pegawai" dipilih, THE Report_Generator SHALL hanya mengambil data dari tabel `mapping_shifts` yang `user_id`-nya merujuk ke pengguna dengan `tipe_user = 'pegawai'`.

---

### Requirement 9: Pengajuan Cuti Bertingkat – Level 1 (Kepala Cabang / Manager)

**User Story:** Sebagai Karyawan, saya ingin pengajuan cuti saya disetujui oleh Kepala Cabang atau Manager saya terlebih dahulu sebelum diteruskan ke HRD/Admin sehingga atasan langsung saya memiliki kendali atas kehadiran tim.

#### Acceptance Criteria

1. THE System SHALL menambahkan kolom `status_approval_1` (string, default `Pending`), `user_approval_1` (foreign key ke `users.id`, nullable), dan `catatan_approval_1` (string, nullable) pada tabel `cutis` melalui migration baru, tanpa mengubah kolom yang sudah ada.

2. WHEN Karyawan mengajukan cuti baru, THE Cuti_Workflow SHALL menyimpan record `cutis` dengan `status_cuti = 'Pending'` dan `status_approval_1 = 'Pending'`, kemudian mengirimkan Notifikasi hanya kepada Kepala_Cabang yang memiliki `lokasi_id` yang sama dengan pengaju.

3. WHEN Karyawan mengajukan cuti baru dan tidak ada Kepala_Cabang aktif di lokasi yang sama, THE Cuti_Workflow SHALL meneruskan pengajuan langsung ke Approval_Level_2 dengan `status_approval_1 = 'Dilewati'` dan mengirimkan Notifikasi kepada Admin dan HRD.

4. WHILE `status_approval_1 = 'Pending'`, THE Cuti_Workflow SHALL menampilkan tombol "Setujui" dan "Tolak" hanya kepada Kepala_Cabang yang berlokasi sama dengan pengaju pada halaman data cuti, dan SHALL menyembunyikan tombol tersebut dari semua pengguna lain yang tidak memiliki otorisasi Kepala_Cabang di lokasi yang sama.

5. WHEN Kepala_Cabang menyetujui pengajuan cuti (memilih "Setujui"), THE Cuti_Workflow SHALL mengubah `status_approval_1 = 'Disetujui'`, mengisi `user_approval_1` dengan ID Kepala_Cabang yang menyetujui, dan mengirimkan Notifikasi kepada Admin dan HRD untuk melakukan Approval_Level_2. Nilai `status_cuti` SHALL tetap `Pending` sampai Approval_Level_2 selesai.

6. WHEN Kepala_Cabang menolak pengajuan cuti (memilih "Tolak"), THE Cuti_Workflow SHALL mengubah `status_approval_1 = 'Ditolak'` dan `status_cuti = 'Ditolak'`, mengisi `user_approval_1` dengan ID Kepala_Cabang yang menolak, dan mengirimkan Notifikasi penolakan kepada Karyawan pengaju.

7. WHILE `status_approval_1 != 'Disetujui'` dan `status_approval_1 != 'Dilewati'`, THE Cuti_Workflow SHALL menyembunyikan tombol approval Level 2 dari halaman Admin dan HRD untuk mencegah approval melompati tahapan.

---

### Requirement 10: Approval Cuti Bertingkat – Level 2 (Admin / HRD)

**User Story:** Sebagai Admin atau HRD, saya ingin melakukan final approval atas pengajuan cuti yang sudah disetujui Manager sehingga keputusan akhir tetap ada di tangan HR.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengakses halaman data cuti, THE Cuti_Workflow SHALL menampilkan badge status dua kolom untuk setiap record: "Approval Manager" (berisi nilai `status_approval_1`) dan "Approval Admin" (berisi nilai `status_cuti`).

2. WHILE `status_approval_1 = 'Disetujui'` atau `status_approval_1 = 'Dilewati'`, THE Cuti_Workflow SHALL menampilkan tombol "Setujui Final" dan "Tolak Final" kepada Admin dan HRD pada baris cuti yang bersangkutan.

3. WHEN Admin atau HRD menyetujui final (memilih "Setujui Final"), THE Cuti_Workflow SHALL mengubah `status_cuti = 'Diterima'`, mengisi `user_approval` dengan ID approver, menjalankan proses debet saldo cuti dan pembaruan `status_absen` di `mapping_shifts` yang sama seperti logika yang sudah ada di `CutiController::editAdminProses`, dan mengirimkan Notifikasi penerimaan kepada Karyawan.

4. WHEN Admin atau HRD menolak final (memilih "Tolak Final"), THE Cuti_Workflow SHALL mengubah `status_cuti = 'Ditolak'`, mengisi `user_approval` dengan ID approver, dan mengirimkan Notifikasi penolakan kepada Karyawan.

5. IF `status_cuti` sudah bernilai `Diterima` atau `Ditolak` (final sudah diputuskan), THEN THE Cuti_Workflow SHALL menonaktifkan semua tombol approval dan menampilkan status final sebagai teks statis, untuk mencegah perubahan ulang.

---

### Requirement 11: Notifikasi Alur Cuti Bertingkat

**User Story:** Sebagai Karyawan, saya ingin mendapatkan notifikasi di setiap tahapan approval cuti sehingga saya dapat memantau status pengajuan saya secara real-time.

#### Acceptance Criteria

1. WHEN pengajuan cuti berhasil disimpan, THE Cuti_Workflow SHALL mengirimkan Notifikasi in-app (database notification Spatie) kepada semua Kepala_Cabang yang memiliki `lokasi_id` yang sama dengan pengaju, dalam waktu tidak lebih dari 5 detik setelah penyimpanan berhasil.

2. WHEN Kepala_Cabang menyetujui pengajuan (Approval_Level_1 disetujui), THE Cuti_Workflow SHALL mengirimkan Notifikasi in-app kepada semua pengguna dengan role `admin` dan `hrd` untuk menginformasikan bahwa pengajuan siap untuk final approval.

3. WHEN keputusan final (Diterima atau Ditolak) diambil oleh Admin atau HRD, THE Cuti_Workflow SHALL mengirimkan Notifikasi in-app kepada Karyawan pengaju yang berisi status akhir cuti dan nama approver.

4. WHERE konfigurasi WhatsApp API pada tabel `settings` aktif (kolom `api_url` terisi), THE Cuti_Workflow SHALL juga mengirimkan notifikasi melalui WhatsApp API kepada penerima notifikasi yang sama dengan notifikasi in-app pada setiap tahapan.

5. WHEN Kepala_Cabang menolak pengajuan (Approval_Level_1 ditolak), THE Cuti_Workflow SHALL mengirimkan Notifikasi in-app kepada Karyawan pengaju yang berisi informasi bahwa pengajuan ditolak pada Level 1 beserta nama Kepala_Cabang yang menolak.

---

### Requirement 12: Visibilitas Status Cuti untuk Karyawan

**User Story:** Sebagai Karyawan, saya ingin melihat status pengajuan cuti saya secara detail di halaman cuti saya, termasuk siapa yang sudah menyetujui dan di tahap mana posisi pengajuan saya.

#### Acceptance Criteria

1. WHEN Karyawan mengakses halaman riwayat cuti (`/cuti`), THE Cuti_Workflow SHALL menampilkan kolom "Status Manager" (berisi `status_approval_1`) dan "Status Final" (berisi `status_cuti`) untuk setiap record cuti milik karyawan tersebut.

2. THE Cuti_Workflow SHALL menampilkan nama Kepala_Cabang yang melakukan approval Level 1 (dari `user_approval_1`) jika `status_approval_1` bukan `Pending`.

3. THE Cuti_Workflow SHALL menampilkan nama Admin/HRD yang melakukan final approval (dari `user_approval`) jika `status_cuti` bukan `Pending`.

4. WHILE `status_cuti = 'Pending'` dan `status_approval_1 = 'Pending'`, THE Cuti_Workflow SHALL menampilkan badge "Menunggu Persetujuan Manager" pada baris cuti yang bersangkutan di halaman karyawan, karena approval Manager harus diselesaikan terlebih dahulu.

5. WHILE `status_approval_1 = 'Disetujui'` dan `status_cuti = 'Pending'`, THE Cuti_Workflow SHALL menampilkan badge "Menunggu Persetujuan Admin/HRD" pada baris cuti yang bersangkutan di halaman karyawan.

---

### Requirement 13: Migrasi Data Cuti Lama (Backward Compatibility)

**User Story:** Sebagai Admin, saya ingin data cuti yang sudah ada sebelum fitur ini diterapkan tetap berfungsi dan tidak mengalami error sehingga riwayat approval sebelumnya tidak terganggu.

#### Acceptance Criteria

1. THE System SHALL menyertakan migrasi database yang menambahkan kolom `status_approval_1`, `user_approval_1`, dan `catatan_approval_1` ke tabel `cutis` dengan nilai default yang tidak mengubah perilaku data lama.

2. WHEN migration dijalankan, THE System SHALL mengisi nilai `status_approval_1 = 'Dilewati'` pada semua record `cutis` yang sudah ada sebelum migration, agar alur Level 2 tetap dapat berjalan pada data lama tanpa memerlukan approval Level 1 ulang.

3. THE System SHALL memastikan bahwa semua query yang sudah ada pada `CutiController` yang hanya membaca `status_cuti` tetap menghasilkan hasil yang sama setelah migration. IF ada query yang perlu diperbarui akibat penambahan kolom baru, maka perubahan tersebut dilakukan secara manual sebagai bagian dari implementasi fitur ini.

---

### Requirement 14: Filter dan Pencarian pada Halaman Data Cuti (Admin)

**User Story:** Sebagai Admin atau HRD, saya ingin dapat memfilter daftar pengajuan cuti berdasarkan status approval sehingga saya dapat fokus menangani pengajuan yang membutuhkan tindakan saya.

#### Acceptance Criteria

1. WHEN Admin atau HRD mengakses halaman `/data-cuti`, THE Cuti_Workflow SHALL menyediakan filter dropdown "Status Approval Manager" dengan opsi: Semua, Pending, Disetujui, Ditolak, Dilewati.

2. WHEN Admin atau HRD mengakses halaman `/data-cuti`, THE Cuti_Workflow SHALL menyediakan filter dropdown "Status Final" dengan opsi: Semua, Pending, Diterima, Ditolak.

3. WHEN kedua filter diterapkan secara bersamaan, THE Cuti_Workflow SHALL menggabungkan keduanya menggunakan operator AND sehingga hanya record yang memenuhi kedua kriteria yang ditampilkan.

4. THE Cuti_Workflow SHALL mempertahankan nilai filter yang dipilih saat halaman di-refresh (menggunakan query string di URL) agar Admin tidak perlu memilih ulang filter setiap kali.

