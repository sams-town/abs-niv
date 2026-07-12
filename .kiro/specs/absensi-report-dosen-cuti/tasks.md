# Implementation Plan: Absensi Report, Dosen, dan Cuti Bertingkat

## Overview

Implementasi tiga fitur baru pada sistem absensi Laravel 8: (1) Report Absensi Pivot dengan visualisasi Chart.js dan export Excel/PDF, (2) Modul Dosen dengan manajemen data dan menu navigasi terpisah, dan (3) Approval Cuti Bertingkat dua tahap (Kepala Cabang → Admin/HRD). Urutan pengerjaan mengikuti dependency: migrations → models → helpers/services → controllers → export classes → views → routes → sidebar.

## Tasks

- [x] 1. Database Migrations
  - [x] 1.1 Buat migration `add_dosen_fields_to_users_table`
    - Buat file `database/migrations/YYYY_MM_DD_add_dosen_fields_to_users_table.php`
    - Tambahkan kolom `tipe_user` (string, default `'pegawai'`), `nidn` (string, nullable), `jabatan_akademik` (string, nullable), `mata_kuliah` (string, nullable), `status_aktif` (boolean, default `true`) ke tabel `users`
    - Pastikan `down()` me-drop semua kolom yang ditambahkan
    - _Requirements: 6.1, 6.6_

  - [x] 1.2 Buat migration `add_approval_bertingkat_to_cutis_table`
    - Buat file `database/migrations/YYYY_MM_DD_add_approval_bertingkat_to_cutis_table.php`
    - Tambahkan kolom `status_approval_1` (string, default `'Pending'`), `user_approval_1` (unsignedBigInteger, nullable, foreign key ke `users.id`), `catatan_approval_1` (string, nullable) setelah kolom `status_cuti`
    - Setelah `Schema::table`, jalankan `DB::table('cutis')->update(['status_approval_1' => 'Dilewati'])` untuk backward compatibility
    - Pastikan `down()` me-drop kolom dan foreign key dengan benar
    - _Requirements: 9.1, 13.1, 13.2_

- [x] 2. Model Updates
  - [x] 2.1 Modifikasi `app/Models/User.php`
    - Tambahkan scope `scopeDosen($query)` yang memfilter `tipe_user = 'dosen'`
    - Tambahkan scope `scopePegawai($query)` yang memfilter `tipe_user = 'pegawai'`
    - _Requirements: 8.2, 8.3_

  - [x] 2.2 Modifikasi `app/Models/Cuti.php`
    - Tambahkan relasi `approver1()` sebagai `belongsTo(User::class, 'user_approval_1')`
    - _Requirements: 10.1, 12.2_

- [x] 3. Helper / Service Classes
  - [x] 3.1 Buat `app/Helpers/PivotBuilder.php`
    - Buat class `PivotBuilder` dengan method-method statis:
      - `mapStatusToCode(?string $status): string` — mapping `status_absen` ke kode `H/C/I/IT/IP/S/L/A/-`
      - `buildCodes(array $shiftsByDate, array $dates): array` — membangun array kode per tanggal untuk satu user
      - `buildSummary(array $codes): array` — menghitung total Hadir, Cuti, Izin, Alfa, Sakit, Persentase
      - `isSunday(string $date): bool` — mengecek apakah tanggal adalah hari Minggu
      - `buildChartData(array $rows, array $dates): array` — mengagregasi data untuk bar chart dan line chart
      - `paginateByDates(array $dates, int $chunkSize = 15): array` — memecah array tanggal untuk paginasi PDF
    - _Requirements: 1.2, 1.3, 1.4, 3.3, 4.1, 4.2_

  - [ ]* 3.2 Tulis property test untuk `PivotBuilder::mapStatusToCode` (Property 2)
    - **Property 2: Validitas Kode Status Sel Pivot**
    - Verifikasi bahwa setiap input `status_absen` menghasilkan kode dalam himpunan `{H, C, I, IT, IP, S, L, A, -}`
    - **Validates: Requirements 1.2**

  - [ ]* 3.3 Tulis property test untuk `PivotBuilder::isSunday` (Property 3)
    - **Property 3: Penandaan Kolom Hari Minggu**
    - Verifikasi bahwa hanya tanggal yang merupakan hari Minggu Gregorian yang ditandai
    - **Validates: Requirements 1.3**

  - [ ]* 3.4 Tulis property test untuk `PivotBuilder::buildSummary` (Property 4)
    - **Property 4: Keakuratan Ringkasan Per Pegawai**
    - Verifikasi bahwa nilai summary sama dengan hasil hitung manual
    - **Validates: Requirements 1.4**

  - [x] 3.5 Buat `app/Helpers/CutiStateHelper.php`
    - Buat class `CutiStateHelper` dengan method-method statis:
      - `canApproveLevel1(Cuti $cuti, User $user): bool` — cek apakah user boleh approve Level 1
      - `canApproveLevel2(Cuti $cuti, User $user): bool` — cek apakah user boleh approve Level 2
      - `getBadgeForKaryawan(Cuti $cuti): string` — mengembalikan string badge berdasarkan kombinasi status
    - _Requirements: 9.4, 10.2, 10.5, 12.4, 12.5_

  - [ ]* 3.6 Tulis property test untuk `CutiStateHelper::canApproveLevel1` dan `canApproveLevel2` (Property 9, 10)
    - **Property 9: Cuti Approval State Machine — Level 1**
    - **Property 10: Cuti Approval State Machine — Level 2**
    - Verifikasi visibilitas tombol sesuai state machine dengan kombinasi status acak
    - **Validates: Requirements 9.4, 9.5, 9.6, 9.7, 10.2, 10.5**

  - [ ]* 3.7 Tulis property test untuk `CutiStateHelper::getBadgeForKaryawan` (Property 11)
    - **Property 11: Badge Status Karyawan Mencerminkan State**
    - Verifikasi keempat kombinasi status menghasilkan badge yang tepat
    - **Validates: Requirements 12.4, 12.5**

- [x] 4. Checkpoint — Migrations, Models, Helpers
  - Jalankan `php artisan migrate` dan pastikan tidak ada error. Pastikan semua unit/property test pada task 3 lulus. Tanyakan kepada user jika ada pertanyaan.

- [ ] 5. AbsenPivotController dan Export Classes
  - [-] 5.1 Buat `app/Http/Controllers/AbsenPivotController.php`
    - Buat method `index()`: render `laporan-pivot.index` dengan daftar lokasi
    - Buat method `generate()`: validasi `lokasi_id`, `tanggal_mulai` (format Y-m-d), `tanggal_akhir` (after_or_equal:tanggal_mulai), `tipe_user`; query `MappingShift` JOIN `users`; gunakan `PivotBuilder` untuk membangun `$pivot`; render `laporan-pivot.result`
    - Buat method `exportExcel()`: jalankan logika pivot yang sama, buat instance `AbsenPivotExport`, return download
    - Buat method `exportPdf()`: jalankan logika pivot, gunakan `PivotBuilder::paginateByDates`, load view `laporan-pivot.pdf` via DomPDF landscape, return stream
    - Buat method `rekapBulanan()`: query `mapping_shifts` GROUP BY `user_id` dan bulan untuk tahun yang dipilih; render `laporan-pivot.rekap-bulanan`
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.1, 3.1, 4.1, 4.2, 4.4, 5.1, 5.2, 5.3, 5.4, 8.1, 8.2, 8.3_

  - [ ]* 5.2 Tulis property test untuk logika pivot di `AbsenPivotController` (Property 1)
    - **Property 1: Pivot Completeness dengan Filter Lokasi**
    - Verifikasi bahwa pivot mengandung tepat satu baris per user dan satu sel per tanggal, tanpa user dari lokasi lain
    - **Validates: Requirements 1.1, 1.6**

  - [ ]* 5.3 Tulis property test untuk validasi input tanggal (Property 5)
    - **Property 5: Validasi Input Tanggal**
    - Verifikasi bahwa input tanggal tidak valid menghasilkan redirect with errors, bukan data pivot
    - **Validates: Requirements 1.7**

  - [ ]* 5.4 Tulis property test untuk rekap bulanan (Property 6)
    - **Property 6: Keakuratan Pivot Rekap Bulanan**
    - Verifikasi bahwa jumlah hari kehadiran bulanan sesuai dengan hitung manual dari `mapping_shifts`
    - **Validates: Requirements 5.2**

  - [ ]* 5.5 Tulis property test untuk filter tipe user (Property 7)
    - **Property 7: Filter Tipe User pada Laporan**
    - Verifikasi bahwa semua baris hasil filter `dosen`/`pegawai` memiliki `tipe_user` yang sesuai
    - **Validates: Requirements 8.2, 8.3**

  - [-] 5.6 Buat `app/Exports/AbsenPivotExport.php`
    - Implementasi interface `FromArray`, `WithHeadings`, `WithStyles`, `WithColumnFormatting`, `ShouldAutoSize`
    - Constructor menerima `array $pivot`, `string $mulai`, `string $akhir`
    - Method `headings()`: kembalikan `['No', 'Nama Pegawai', ...tanggal DD/MM..., 'Hadir', 'Cuti', 'Izin', 'Alfa', 'Sakit', 'Persentase']`
    - Method `array()`: build array data dari `$pivot['rows']`
    - Method `styles()`: terapkan border tipis seluruh data, bold baris header dan kolom nama
    - Terapkan warna kuning pada sel `A` (Alfa) dan merah muda pada sel `S` (Sakit) via `registerEvents()` + `AfterSheet`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [ ] 6. DosenController
  - [-] 6.1 Buat `app/Http/Controllers/DosenController.php`
    - Method `index()`: query `User::dosen()->with('jabatan')->paginate(20)`, render `dosen.index`
    - Method `create()`: render `dosen.tambah`
    - Method `store(Request $request)`: validasi `name` required, `email` unique:users, `nidn` unique pada dosen aktif (`Rule::unique('users','nidn')->where('status_aktif',true)`), `jabatan_akademik`, `mata_kuliah`, `telepon`, `password`; buat `User` dengan `tipe_user='dosen'`, `status_aktif=true`; `$user->assignRole('dosen')`; redirect `/dosen` dengan flash success
    - Method `edit($id)`: find user atau abort 404, render `dosen.edit`
    - Method `update(Request $request, $id)`: validasi sama dengan store tapi ignore ID untuk nidn; update kolom yang relevan; redirect dengan flash success
    - Method `deactivate($id)`: find user atau abort 404; `$user->update(['status_aktif' => false])`; redirect dengan flash success
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7_

  - [ ]* 6.2 Tulis property test untuk keunikan NIDN (Property 8)
    - **Property 8: Keunikan NIDN Dosen Aktif**
    - Verifikasi bahwa dua dosen aktif tidak bisa memiliki NIDN yang sama
    - **Validates: Requirements 6.7**

- [ ] 7. Modifikasi CutiController
  - [-] 7.1 Modifikasi method `tambah()` di `app/Http/Controllers/CutiController.php`
    - Simpan cuti dengan `status_cuti='Pending'` dan `status_approval_1='Pending'`
    - Cek apakah ada user dengan role `kepala_cabang` dan `lokasi_id` yang sama dengan pengaju
    - Jika ada: kirim notifikasi in-app ke kepala_cabang di lokasi tersebut
    - Jika tidak ada: set `status_approval_1='Dilewati'`; kirim notifikasi ke semua user role `admin` dan `hrd`
    - _Requirements: 9.2, 9.3, 11.1_

  - [-] 7.2 Modifikasi method `dataCuti()` di `app/Http/Controllers/CutiController.php`
    - Tambahkan query filter `status_approval_1` dari request (Semua/Pending/Disetujui/Ditolak/Dilewati)
    - Tambahkan query filter `status_cuti` dari request (Semua/Pending/Diterima/Ditolak)
    - Gabungkan dua filter dengan AND semantics
    - Pertahankan nilai filter di query string (pass ke view sebagai `$filters`)
    - Eager load relasi `approver1` dan `ua`
    - _Requirements: 14.1, 14.2, 14.3, 14.4_

  - [ ]* 7.3 Tulis property test untuk filter cuti dengan AND semantics (Property 12)
    - **Property 12: Filter Data Cuti dengan AND Semantics**
    - Verifikasi bahwa setiap record hasil filter memenuhi kedua kriteria sekaligus
    - **Validates: Requirements 14.3**

  - [~] 7.4 Tambahkan method `approvalLevel1(Request $request, $id)` di `app/Http/Controllers/CutiController.php`
    - Find cuti atau abort 404; periksa `status_approval_1 === 'Pending'`
    - Validasi `$request->action` (`setujui` atau `tolak`)
    - Jika setujui: `status_approval_1='Disetujui'`, isi `user_approval_1=auth()->id()`; kirim notifikasi ke admin+hrd
    - Jika tolak: `status_approval_1='Ditolak'`, `status_cuti='Ditolak'`, isi `user_approval_1=auth()->id()`; kirim notifikasi penolakan ke karyawan pengaju
    - Redirect ke `/data-cuti` dengan flash message
    - _Requirements: 9.5, 9.6, 11.2, 11.5_

  - [~] 7.5 Tambahkan method `approvalLevel2(Request $request, $id)` di `app/Http/Controllers/CutiController.php`
    - Find cuti atau abort 404
    - Guard: jika `status_approval_1` bukan `'Disetujui'` atau `'Dilewati'`, redirect back dengan error "Approval Level 1 belum selesai"
    - Guard: jika `status_cuti` sudah `'Diterima'` atau `'Ditolak'`, redirect back dengan error "Status cuti sudah final"
    - Jika setujui final: `status_cuti='Diterima'`, isi `user_approval=auth()->id()`; jalankan logika debet saldo + update `mapping_shifts` (adaptasi dari `editAdminProses`); kirim notifikasi penerimaan ke karyawan
    - Jika tolak final: `status_cuti='Ditolak'`, isi `user_approval=auth()->id()`; kirim notifikasi penolakan ke karyawan
    - Redirect ke `/data-cuti` dengan flash message
    - _Requirements: 10.3, 10.4, 10.5, 11.3_

- [~] 8. Checkpoint — Controllers
  - Pastikan semua controller dapat di-resolve tanpa error sintaks (`php artisan route:list`). Jalankan property test task 7.3. Tanyakan kepada user jika ada pertanyaan.

- [ ] 9. Blade Views — Fitur 1: Laporan Pivot
  - [~] 9.1 Buat `resources/views/laporan-pivot/index.blade.php`
    - Extends layout dashboard
    - Form GET ke `/laporan-pivot/generate` dengan input: `lokasi_id` (dropdown dari tabel lokasi), `tanggal_mulai` (date input), `tanggal_akhir` (date input), `tipe_user` (dropdown: Semua/Pegawai/Dosen)
    - Tampilkan error validasi jika ada
    - _Requirements: 1.1, 1.6, 1.7, 8.1_

  - [~] 9.2 Buat `resources/views/laporan-pivot/result.blade.php`
    - Extends layout dashboard
    - Render tabel pivot: header kolom tanggal (kolom Minggu beri class `bg-gray-200 text-gray-500`), sel `A` beri class `bg-yellow-100`, sel `S` beri class `bg-red-100`, kolom ringkasan (Hadir, Cuti, Izin, Alfa, Sakit, Persentase) di sisi kanan
    - Tombol "Export Excel" (`/laporan-pivot/export-excel` + query string yang sama) dan "Export PDF" (`/laporan-pivot/export-pdf` + query string yang sama)
    - Jika tidak ada data, sembunyikan semua chart dan tampilkan pesan "Tidak ada data untuk periode ini"
    - `@push('scripts')`: inisialisasi Chart.js bar chart (`$chartBar`) dan line chart (`$chartLine`) menggunakan data yang di-inject dari controller
    - _Requirements: 1.2, 1.3, 1.4, 1.5, 2.4, 4.1, 4.2, 4.3, 4.4_

  - [~] 9.3 Buat `resources/views/laporan-pivot/rekap-bulanan.blade.php`
    - Extends layout dashboard
    - Form filter tahun dan `lokasi_id`
    - Tabel pivot baris = pegawai, kolom = bulan (Jan–Des), kolom total kehadiran tahunan di sisi kanan
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [~] 9.4 Buat `resources/views/laporan-pivot/pdf.blade.php`
    - Template standalone (tanpa extends) untuk DomPDF landscape A4
    - Header laporan: nama institusi (dari `$settings`), judul "Laporan Absensi", rentang tanggal, nama lokasi
    - Loop `$pages` (array dari `PivotBuilder::paginateByDates`): setiap halaman memuat tabel pivot untuk subset tanggal + ringkasan total di bagian bawah halaman (total Hadir, Alfa, Cuti, Izin)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 10. Blade Views — Fitur 2: Modul Dosen
  - [~] 10.1 Buat `resources/views/dosen/index.blade.php`
    - Extends layout dashboard
    - Tabel daftar dosen: kolom No, Nama, NIDN, Jabatan Akademik, Email, Telepon, Status Aktif, Aksi (Edit, Nonaktifkan)
    - Tombol "Tambah Dosen" di bagian atas
    - Tampilkan flash success/error jika ada
    - _Requirements: 6.2_

  - [~] 10.2 Buat `resources/views/dosen/tambah.blade.php`
    - Extends layout dashboard
    - Form POST ke `/dosen/store` dengan field: `name`, `email`, `nidn`, `jabatan_akademik`, `mata_kuliah`, `telepon`, `password`, `password_confirmation`
    - Tampilkan error validasi per field
    - _Requirements: 6.3, 6.6, 6.7_

  - [~] 10.3 Buat `resources/views/dosen/edit.blade.php`
    - Extends layout dashboard
    - Form PUT ke `/dosen/update/{id}` dengan field yang sama seperti tambah (kecuali `tipe_user`)
    - Pre-populate semua field dengan data dosen yang ada
    - Tampilkan error validasi per field
    - _Requirements: 6.4_

- [ ] 11. Blade Views — Fitur 3: Approval Cuti Bertingkat
  - [~] 11.1 Modifikasi `resources/views/cuti/datacuti.blade.php`
    - Tambahkan dua kolom badge di tabel: "Approval Manager" (nilai `status_approval_1` dengan warna badge sesuai status) dan "Approval Admin" (nilai `status_cuti`)
    - Tambahkan filter dropdown di atas tabel: "Status Approval Manager" (Semua/Pending/Disetujui/Ditolak/Dilewati) dan "Status Final" (Semua/Pending/Diterima/Ditolak); gunakan query string agar nilai filter dipertahankan saat refresh
    - Tampilkan tombol "Setujui" dan "Tolak" (form POST ke `/data-cuti/approval-1/{id}`) hanya jika `@role('kepala_cabang')` DAN `$cuti->status_approval_1 === 'Pending'` DAN `$cuti->user->lokasi_id === auth()->user()->lokasi_id`
    - Tampilkan tombol "Setujui Final" dan "Tolak Final" (form POST ke `/data-cuti/approval-2/{id}`) hanya jika `@role('admin|hrd')` DAN `$cuti->status_approval_1` in `['Disetujui','Dilewati']` DAN `$cuti->status_cuti === 'Pending'`
    - Jika `status_cuti` sudah final (`Diterima`/`Ditolak`), tampilkan status sebagai teks statis (nonaktifkan semua tombol)
    - _Requirements: 9.4, 9.7, 10.1, 10.2, 10.5, 14.1, 14.2, 14.3, 14.4_

  - [~] 11.2 Modifikasi `resources/views/cuti/indexuser.blade.php`
    - Tambahkan kolom "Status Manager" (nilai `status_approval_1`) dan "Status Final" (nilai `status_cuti`)
    - Tampilkan nama approver Level 1 (dari relasi `approver1`) jika `status_approval_1 !== 'Pending'`
    - Tampilkan nama approver Level 2 (dari relasi `ua`) jika `status_cuti !== 'Pending'`
    - Tampilkan badge "Menunggu Persetujuan Manager" jika `status_approval_1 === 'Pending'` DAN `status_cuti === 'Pending'`
    - Tampilkan badge "Menunggu Persetujuan Admin/HRD" jika `status_approval_1 === 'Disetujui'` DAN `status_cuti === 'Pending'`
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

- [ ] 12. Routes dan Sidebar
  - [~] 12.1 Tambahkan routes Laporan Pivot dan Dosen di `routes/web.php`
    - Tambahkan `use App\Http\Controllers\AbsenPivotController;` dan `use App\Http\Controllers\DosenController;`
    - Daftarkan route group `prefix('laporan-pivot')->middleware('role:admin|hrd')` dengan 5 route (index, generate, export-excel, export-pdf, rekap-bulanan)
    - Daftarkan route group `prefix('dosen')->middleware('role:admin|hrd')` dengan 6 route (index, tambah, store, edit/{id}, update/{id}, delete/{id})
    - _Requirements: 7.1, 7.2_

  - [~] 12.2 Tambahkan routes Approval Cuti Bertingkat di `routes/web.php`
    - Tambahkan route `POST /data-cuti/approval-1/{id}` dengan middleware `role:kepala_cabang`
    - Tambahkan route `POST /data-cuti/approval-2/{id}` dengan middleware `role:admin|hrd`
    - _Requirements: 9.4, 10.2_

  - [~] 12.3 Modifikasi `resources/views/partials/sidebar.blade.php`
    - Tambahkan menu item "Laporan Pivot" (link ke `/laporan-pivot`) di dalam blok `@role('admin|hrd')` pada section laporan
    - Tambahkan menu item "Data Dosen" (link ke `/dosen`, icon `fas fa-chalkboard-teacher`) di dalam blok `@role('admin|hrd')`
    - Bungkus menu-menu pegawai eksklusif (Payroll, Kasbon, Tunjangan) dalam kondisi `@if(auth()->user()->tipe_user !== 'dosen')` untuk menyembunyikannya dari user dosen
    - Pastikan menu pegawai umum tidak berubah untuk user dengan `tipe_user = 'pegawai'`
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [~] 13. Final Checkpoint
  - Pastikan semua route terdaftar (`php artisan route:list`). Pastikan semua test lulus. Uji alur end-to-end: pivot generate → export → rekap bulanan; tambah/edit/nonaktifkan dosen; pengajuan cuti → approval Level 1 → approval Level 2. Tanyakan kepada user jika ada pertanyaan.

## Notes

- Tasks bertanda `*` bersifat opsional dan dapat dilewati untuk MVP yang lebih cepat
- Setiap task mereferensikan requirement spesifik untuk traceability
- Migration backward compatibility: semua record `cutis` lama di-set `status_approval_1 = 'Dilewati'` agar alur Level 2 tetap berjalan
- `PivotBuilder` dan `CutiStateHelper` diletakkan di `app/Helpers/` sesuai pola `ApiFormatter.php` yang sudah ada
- Export Excel mengikuti pola `AbsenExport` yang sudah ada di `app/Exports/`
- Notifikasi menggunakan event/class `NotifApproval` yang sudah ada di `app/Events/`
- Role `dosen` perlu dibuat di Spatie Permission sebelum `assignRole('dosen')` bisa dijalankan — tambahkan ke seeder atau buat via tinker

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2"] },
    { "id": 1, "tasks": ["2.1", "2.2"] },
    { "id": 2, "tasks": ["3.1", "3.5"] },
    { "id": 3, "tasks": ["3.2", "3.3", "3.4", "3.6", "3.7"] },
    { "id": 4, "tasks": ["5.1", "5.6", "6.1"] },
    { "id": 5, "tasks": ["5.2", "5.3", "5.4", "5.5", "6.2", "7.1", "7.2"] },
    { "id": 6, "tasks": ["7.3", "7.4", "7.5"] },
    { "id": 7, "tasks": ["9.1", "9.2", "9.3", "9.4", "10.1", "10.2", "10.3", "11.1", "11.2"] },
    { "id": 8, "tasks": ["12.1", "12.2"] },
    { "id": 9, "tasks": ["12.3"] }
  ]
}
```
