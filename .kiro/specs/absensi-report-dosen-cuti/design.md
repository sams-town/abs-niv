# Design Document: Absensi Report, Dosen, dan Cuti Bertingkat

## Overview

Dokumen ini mendeskripsikan desain teknis untuk tiga fitur baru pada sistem absensi Laravel 8:

1. **Report Absensi Pivot** – Laporan tabel silang dengan visualisasi Chart.js dan export Excel/PDF.
2. **Modul Dosen** – Tipe pengguna dosen dengan manajemen data dan menu terpisah.
3. **Approval Cuti Bertingkat** – Dua tahap persetujuan: Level 1 (Kepala Cabang) → Level 2 (Admin/HRD).

Semua fitur dibangun di atas stack yang sudah ada: Laravel 8, PHP 7.3/8.0, Blade views, Spatie Permission,
Maatwebsite Excel ^3.1, Barryvdh DomPDF, dan Pusher untuk notifikasi real-time.

---

## Architecture

Sistem mengikuti arsitektur MVC Laravel yang sudah ada. Setiap fitur menambahkan layer-layer berikut:

```
Browser (Blade + Chart.js)
    │
    ▼
Routes (web.php)
    │
    ▼
Controllers (app/Http/Controllers/)
    │            │
    ▼            ▼
Models       Export Classes
(Eloquent)   (Maatwebsite / DomPDF)
    │
    ▼
Database (MySQL)
```

**Prinsip desain:**
- Tidak ada API endpoint baru — semua server-side Blade.
- Export (Excel/PDF) menggunakan class terpisah di `app/Exports/` sesuai pola yang sudah ada (`AbsenExport`, `RekapExport`).
- Logika pivot dienkapsulasi dalam method model dan service class ringan agar mudah diuji.
- Perubahan database menggunakan migration baru, tidak memodifikasi migration lama.
- Backward compatibility dijaga: kolom baru punya `nullable()` atau `default()` yang aman.

---

## Components and Interfaces

### Fitur 1: Report Absensi Pivot

#### Controllers
- **`AbsenPivotController`** (`app/Http/Controllers/AbsenPivotController.php`)
  - `index()` – Menampilkan form filter; route `GET /laporan-pivot`
  - `generate()` – Memvalidasi input, membangun data pivot, merender view; route `GET /laporan-pivot/generate`
  - `exportExcel()` – Menginisiasi download Excel; route `GET /laporan-pivot/export-excel`
  - `exportPdf()` – Menginisiasi download PDF; route `GET /laporan-pivot/export-pdf`
  - `rekapBulanan()` – Menampilkan pivot per bulan; route `GET /laporan-pivot/rekap-bulanan`

#### Export Classes
- **`AbsenPivotExport`** (`app/Exports/AbsenPivotExport.php`)
  - Implements `FromArray`, `WithHeadings`, `WithStyles`, `WithColumnFormatting`, `ShouldAutoSize`
  - Menerima array pivot dan metadata tanggal via constructor
  - Menerapkan warna kuning pada sel `A` (Alfa) dan merah muda pada `S` (Sakit)

#### Views (Blade)
- `resources/views/laporan-pivot/index.blade.php` – Form filter (lokasi, tanggal, mode)
- `resources/views/laporan-pivot/result.blade.php` – Tabel pivot + Chart.js + tombol export
- `resources/views/laporan-pivot/rekap-bulanan.blade.php` – Pivot per bulan per tahun
- `resources/views/laporan-pivot/pdf.blade.php` – Template DomPDF landscape

### Fitur 2: Modul Dosen

#### Controllers
- **`DosenController`** (`app/Http/Controllers/DosenController.php`)
  - `index()` – Daftar dosen; route `GET /dosen`
  - `create()` – Form tambah dosen; route `GET /dosen/tambah`
  - `store()` – Simpan dosen baru + assign role `dosen`; route `POST /dosen/store`
  - `edit($id)` – Form edit; route `GET /dosen/edit/{id}`
  - `update($id)` – Update data; route `PUT /dosen/update/{id}`
  - `deactivate($id)` – Soft deactivate (`status_aktif = false`); route `DELETE /dosen/delete/{id}`

#### Views (Blade)
- `resources/views/dosen/index.blade.php` – Tabel daftar dosen
- `resources/views/dosen/tambah.blade.php` – Form tambah
- `resources/views/dosen/edit.blade.php` – Form edit

### Fitur 3: Approval Cuti Bertingkat

#### Controllers
- **`CutiController`** (modifikasi file yang sudah ada)
  - `tambah()` – Diubah: notifikasi hanya ke Kepala Cabang lokasi yang sama (bukan semua role)
  - `dataCuti()` – Diubah: menampilkan badge dua-level + filter baru
  - `approvalLevel1($id)` – Baru: Kepala Cabang setujui/tolak; route `POST /data-cuti/approval-1/{id}`
  - `approvalLevel2($id)` – Baru: Admin/HRD final approval; route `POST /data-cuti/approval-2/{id}`
  - `editAdminProses()` – Dipertahankan untuk backward compatibility, tapi logika approval dipindah ke `approvalLevel2`

---

## Data Models

### Perubahan Tabel `users` (Migration Baru)

```php
// database/migrations/YYYY_MM_DD_add_dosen_fields_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('tipe_user')->default('pegawai')->after('is_admin');
    // nilai: 'pegawai' | 'dosen'
    $table->string('nidn')->nullable()->after('tipe_user');
    $table->string('jabatan_akademik')->nullable()->after('nidn');
    $table->string('mata_kuliah')->nullable()->after('jabatan_akademik');
    $table->boolean('status_aktif')->default(true)->after('mata_kuliah');
});
```

**Rationale:** `tipe_user` dengan default `pegawai` memastikan semua user lama tidak terpengaruh.
`status_aktif` digunakan untuk soft-delete dosen (Req 6.5).

### Perubahan Tabel `cutis` (Migration Baru)

```php
// database/migrations/YYYY_MM_DD_add_approval_bertingkat_to_cutis_table.php
Schema::table('cutis', function (Blueprint $table) {
    $table->string('status_approval_1')->default('Pending')->after('status_cuti');
    // nilai: 'Pending' | 'Disetujui' | 'Ditolak' | 'Dilewati'
    $table->unsignedBigInteger('user_approval_1')->nullable()->after('status_approval_1');
    $table->foreign('user_approval_1')->references('id')->on('users');
    $table->string('catatan_approval_1')->nullable()->after('user_approval_1');
});
```

Setelah menambahkan kolom, migration mengisi data lama:
```php
DB::table('cutis')->update(['status_approval_1' => 'Dilewati']);
```

**Rationale:** Data lama di-set `'Dilewati'` agar Level 2 approval tetap bisa dijalankan tanpa memerlukan
ulang approval Level 1 (Req 13.2).

### Model `Cuti` (Modifikasi)

Tambahkan relasi baru:
```php
// app/Models/Cuti.php
public function approver1()
{
    return $this->belongsTo(User::class, 'user_approval_1');
}
```

### Model `User` (Modifikasi)

Tambahkan scope helper:
```php
// app/Models/User.php
public function scopeDosen($query)
{
    return $query->where('tipe_user', 'dosen');
}

public function scopePegawai($query)
{
    return $query->where('tipe_user', 'pegawai');
}
```

### Struktur Data Pivot (In-Memory)

Data pivot dibangun di controller dan dikirim ke view sebagai array PHP, bukan disimpan ke DB:

```php
// Struktur yang dibangun oleh AbsenPivotController::generate()
$pivot = [
    'dates'   => ['2025-01-01', '2025-01-02', ...],   // array string tanggal dalam rentang
    'rows'    => [
        [
            'user'    => User $user,
            'cells'   => ['H', 'A', 'C', '-', ...],    // kode per tanggal, indeks sejajar dengan $dates
            'summary' => [
                'hadir'      => 18,
                'cuti'       => 2,
                'izin'       => 1,
                'alfa'       => 3,
                'sakit'      => 0,
                'persentase' => 85.7,
            ],
        ],
        ...
    ],
    'chart_bar'  => ['Hadir' => 180, 'Cuti' => 20, 'Alfa' => 30, ...],
    'chart_line' => ['2025-01-01' => 12, '2025-01-02' => 10, ...],
];
```

**Mapping `status_absen` → kode:**

| status_absen (DB)  | Kode |
|--------------------|------|
| Masuk              | H    |
| Cuti               | C    |
| Izin Masuk         | I    |
| Izin Telat         | IT   |
| Izin Pulang Cepat  | IP   |
| Sakit              | S    |
| Libur              | L    |
| null / tidak ada   | A    |
| (lainnya)          | -    |

---

## Data Flow

### Alur 1: Generate Laporan Pivot

```
User (Admin/HRD) → GET /laporan-pivot (form filter)
    ↓ submit form
GET /laporan-pivot/generate?lokasi_id=X&mulai=Y&akhir=Z&tipe_user=semua
    ↓
AbsenPivotController::generate()
    1. Validasi tanggal (format Y-m-d, mulai ≤ akhir)
    2. Buat array $dates (DatePeriod dari mulai ke akhir)
    3. Query MappingShift: JOIN users, filter lokasi_id dan tipe_user
    4. Group by user_id, lalu by tanggal → indexing O(1)
    5. Loop setiap $user × setiap $date → ambil kode atau 'A'
    6. Hitung summary per pegawai
    7. Aggregate data untuk bar chart dan line chart
    ↓
View result.blade.php:
    - Render tabel pivot (kolom Minggu diberi class CSS 'bg-gray-100')
    - Inject $chartBar dan $chartLine ke <script> Chart.js
    - Tombol Export Excel & PDF (link dengan query string yang sama)
```

### Alur 2: Export Excel Pivot

```
GET /laporan-pivot/export-excel?lokasi_id=X&mulai=Y&akhir=Z
    ↓
AbsenPivotController::exportExcel()
    1. Jalankan logika pivot yang sama dengan generate()
    2. Buat instance AbsenPivotExport($pivot, $mulai, $akhir)
    3. Return: (new AbsenPivotExport(...))->download("Laporan_Absensi_Pivot_{$mulai}_{$akhir}.xlsx")
```

### Alur 3: Export PDF Pivot

```
GET /laporan-pivot/export-pdf?lokasi_id=X&mulai=Y&akhir=Z
    ↓
AbsenPivotController::exportPdf()
    1. Jalankan logika pivot
    2. Tentukan jumlah halaman: $pages = array_chunk($pivot['dates'], 15)
    3. Pdf::loadView('laporan-pivot.pdf', compact('pages', 'pivot', ...))
         ->setPaper('a4', 'landscape')
         ->stream("Laporan_Absensi_{$mulai}_{$akhir}.pdf")
```

### Alur 4: Tambah Dosen

```
Admin → GET /dosen/tambah (form)
    ↓ submit
POST /dosen/store
    ↓
DosenController::store()
    1. Validasi: name, email unik, nidn unik (dosen aktif), ...
    2. Buat User dengan tipe_user = 'dosen', status_aktif = true
    3. $user->assignRole('dosen')  ← Spatie Permission
    4. Redirect /dosen dengan flash success
```

### Alur 5: Pengajuan Cuti Bertingkat

```
Karyawan → POST /cuti/tambah
    ↓
CutiController::tambah() [DIMODIFIKASI]
    1. Simpan Cuti: status_cuti='Pending', status_approval_1='Pending'
    2. Cek apakah ada kepala_cabang dengan lokasi_id sama
       - Ada  → Notifikasi ke kepala_cabang lokasi tersebut
       - Tidak → status_approval_1='Dilewati', notifikasi ke admin+hrd
    ↓
Kepala Cabang → POST /data-cuti/approval-1/{id}
    ↓
CutiController::approvalLevel1()
    - Setujui: status_approval_1='Disetujui', user_approval_1=auth()->id()
               → Notifikasi ke admin+hrd
    - Tolak:   status_approval_1='Ditolak', status_cuti='Ditolak', user_approval_1=auth()->id()
               → Notifikasi ke karyawan
    ↓ (jika disetujui)
Admin/HRD → POST /data-cuti/approval-2/{id}
    ↓
CutiController::approvalLevel2()
    - Setujui Final: status_cuti='Diterima', user_approval=auth()->id()
                    → Jalankan debet saldo + update mapping_shifts (logika dari editAdminProses)
                    → Notifikasi ke karyawan
    - Tolak Final:  status_cuti='Ditolak', user_approval=auth()->id()
                    → Notifikasi ke karyawan
```

---

## Route Definitions

### Fitur 1: Laporan Pivot

```php
// Semua route di bawah middleware 'role:admin|hrd'
Route::prefix('laporan-pivot')->middleware('role:admin|hrd')->group(function () {
    Route::get('/', [AbsenPivotController::class, 'index']);
    Route::get('/generate', [AbsenPivotController::class, 'generate']);
    Route::get('/export-excel', [AbsenPivotController::class, 'exportExcel']);
    Route::get('/export-pdf', [AbsenPivotController::class, 'exportPdf']);
    Route::get('/rekap-bulanan', [AbsenPivotController::class, 'rekapBulanan']);
});
```

### Fitur 2: Modul Dosen

```php
Route::prefix('dosen')->middleware('role:admin|hrd')->group(function () {
    Route::get('/', [DosenController::class, 'index']);
    Route::get('/tambah', [DosenController::class, 'create']);
    Route::post('/store', [DosenController::class, 'store']);
    Route::get('/edit/{id}', [DosenController::class, 'edit']);
    Route::put('/update/{id}', [DosenController::class, 'update']);
    Route::delete('/delete/{id}', [DosenController::class, 'deactivate']);
});
```

### Fitur 3: Cuti Bertingkat (tambahan pada route yang sudah ada)

```php
// Approval Level 1 — hanya kepala_cabang
Route::post('/data-cuti/approval-1/{id}', [CutiController::class, 'approvalLevel1'])
    ->middleware('role:kepala_cabang');

// Approval Level 2 — admin atau hrd
Route::post('/data-cuti/approval-2/{id}', [CutiController::class, 'approvalLevel2'])
    ->middleware('role:admin|hrd');
```

---

## Blade View Structure

### Pivot Report Views

```
resources/views/laporan-pivot/
├── index.blade.php          ← Form filter: lokasi, tanggal mulai/akhir, tipe user
├── result.blade.php         ← Pivot table + chart + tombol export
│   ├── @include('.._pivot_table')   ← sub-partial untuk tabel
│   └── @push('scripts') Chart.js initialization
├── rekap-bulanan.blade.php  ← Pivot per bulan
└── pdf.blade.php            ← Template PDF (DomPDF, landscape A4)
```

Tabel pivot di `result.blade.php`:
- Kolom hari Minggu: `<th class="bg-gray-200 text-gray-500">{{ $date }}</th>`
- Sel Alfa: `<td class="bg-yellow-100 text-center">A</td>`
- Sel Sakit: `<td class="bg-red-100 text-center">S</td>`

### Dosen Views

```
resources/views/dosen/
├── index.blade.php   ← Tabel dosen: Nama, NIDN, Jabatan, Email, Telp, Status, Aksi
├── tambah.blade.php  ← Form: name, email, nidn, jabatan_akademik, mata_kuliah, telepon, password
└── edit.blade.php    ← Form edit (tidak ada field tipe_user)
```

### Cuti Views (modifikasi yang sudah ada)

`resources/views/cuti/datacuti.blade.php` — tambahkan:
- Kolom "Approval Manager" (badge `status_approval_1`)
- Kolom "Approval Admin" (badge `status_cuti`)
- Tombol "Setujui" / "Tolak" hanya untuk kepala_cabang jika `status_approval_1 = 'Pending'`
- Tombol "Setujui Final" / "Tolak Final" hanya untuk admin/hrd jika `status_approval_1` in `['Disetujui','Dilewati']` dan `status_cuti = 'Pending'`
- Filter dropdown: Status Approval Manager + Status Final (query string)

`resources/views/cuti/indexuser.blade.php` — tambahkan:
- Kolom "Status Manager" (nilai `status_approval_1`)
- Kolom "Status Final" (nilai `status_cuti`)
- Nama approver Level 1 dan Level 2 jika bukan `Pending`
- Badge "Menunggu Persetujuan Manager" saat kedua status `Pending`
- Badge "Menunggu Persetujuan Admin/HRD" saat `status_approval_1='Disetujui'` dan `status_cuti='Pending'`

### Navigasi Sidebar (Modifikasi `layouts/sidebar.blade.php` atau setara)

Tambahkan kondisi visibilitas menu:

```blade
{{-- Menu Data Dosen — hanya admin dan hrd --}}
@role('admin|hrd')
<li>
    <a href="/dosen">
        <i class="fas fa-chalkboard-teacher"></i> Data Dosen
    </a>
</li>
@endrole

{{-- Menu khusus pegawai — sembunyikan untuk dosen --}}
@if(auth()->user()->tipe_user !== 'dosen')
<li><a href="/payroll">Payroll</a></li>
<li><a href="/kasbon">Kasbon</a></li>
<li><a href="/tunjangan">Tunjangan</a></li>
{{-- dst. --}}
@endif
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a
system — essentially, a formal statement about what the system should do. Properties serve as the bridge
between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Pivot Completeness dengan Filter Lokasi

*For any* set of users yang terdaftar di suatu `lokasi_id` dan *any* rentang tanggal yang valid, tabel pivot
yang dihasilkan SHALL mengandung tepat satu baris per user dari lokasi tersebut dan tepat satu sel per tanggal
dalam rentang tersebut. Tidak ada user dari lokasi lain yang muncul.

**Validates: Requirements 1.1, 1.6**

### Property 2: Validitas Kode Status Sel Pivot

*For any* record `MappingShift` dengan nilai `status_absen` apapun (termasuk null), fungsi pemetaan status
SHALL menghasilkan kode yang berada dalam himpunan `{H, C, I, IT, IP, S, L, A, -}` dan tidak pernah menghasilkan
nilai di luar himpunan tersebut.

**Validates: Requirements 1.2**

### Property 3: Penandaan Kolom Hari Minggu

*For any* rentang tanggal yang valid, himpunan tanggal yang ditandai sebagai "hari Minggu" dalam pivot SHALL
sama persis dengan himpunan tanggal dalam rentang tersebut yang merupakan hari Minggu menurut kalender Gregorian.

**Validates: Requirements 1.3**

### Property 4: Keakuratan Ringkasan Per Pegawai

*For any* pegawai dan *any* set record `MappingShift` dalam rentang tanggal, nilai ringkasan (total Hadir,
total Cuti, total Alfa, dll.) yang ditampilkan di kolom ringkasan pivot SHALL sama dengan hasil penghitungan
manual atas record-record tersebut, tanpa selisih.

**Validates: Requirements 1.4**

### Property 5: Validasi Input Tanggal

*For any* string yang bukan tanggal valid berformat `Y-m-d` (misalnya string kosong, format salah, atau
`mulai > akhir`), proses pembuatan laporan SHALL gagal dengan pesan error dan tidak menghasilkan data pivot
maupun file export apapun.

**Validates: Requirements 1.7**

### Property 6: Keakuratan Pivot Rekap Bulanan

*For any* pegawai dan *any* bulan, jumlah hari kehadiran yang ditampilkan di sel pivot bulanan SHALL
sama dengan jumlah record `MappingShift` milik pegawai tersebut pada bulan tersebut yang memiliki
`status_absen` dalam himpunan `{'Masuk', 'Izin Telat', 'Izin Pulang Cepat'}`.

**Validates: Requirements 5.2**

### Property 7: Filter Tipe User pada Laporan

*For any* query laporan dengan filter `tipe_user = 'dosen'` atau `tipe_user = 'pegawai'`, semua baris
yang muncul dalam laporan SHALL memiliki nilai `tipe_user` yang sesuai dengan filter yang dipilih.
Tidak ada baris dari tipe yang tidak dipilih yang muncul dalam hasil.

**Validates: Requirements 8.2, 8.3**

### Property 8: Keunikan NIDN Dosen Aktif

*For any* dua dosen yang memiliki `status_aktif = true`, nilai `nidn` mereka SHALL tidak pernah
identik (jika keduanya non-null). Sistem SHALL menolak penyimpanan dosen baru dengan NIDN yang
sudah dimiliki dosen aktif lain.

**Validates: Requirements 6.7**

### Property 9: Cuti Approval State Machine — Level 1

*For any* record cuti, tombol "Setujui"/"Tolak" Level 1 SHALL tampil kepada Kepala Cabang yang
berlokasi sama **jika dan hanya jika** `status_approval_1 = 'Pending'`. Setelah transisi ke
`'Disetujui'` atau `'Ditolak'`, tombol tersebut SHALL tidak tampil lagi.

**Validates: Requirements 9.4, 9.5, 9.6**

### Property 10: Cuti Approval State Machine — Level 2

*For any* record cuti, tombol "Setujui Final"/"Tolak Final" SHALL tampil kepada Admin/HRD **jika
dan hanya jika** `status_approval_1` bernilai `'Disetujui'` atau `'Dilewati'` DAN `status_cuti = 'Pending'`.
Jika `status_cuti` sudah `'Diterima'` atau `'Ditolak'`, semua tombol approval SHALL dinonaktifkan.

**Validates: Requirements 9.7, 10.2, 10.5**

### Property 11: Badge Status Karyawan Mencerminkan State

*For any* kombinasi `(status_approval_1, status_cuti)`, badge yang ditampilkan kepada karyawan
di halaman `/cuti` SHALL tepat sesuai aturan berikut:
- `('Pending', 'Pending')` → badge "Menunggu Persetujuan Manager"
- `('Disetujui', 'Pending')` atau `('Dilewati', 'Pending')` → badge "Menunggu Persetujuan Admin/HRD"
- `(_, 'Diterima')` → badge "Diterima"
- `('Ditolak', _)` atau `(_, 'Ditolak')` → badge "Ditolak"

**Validates: Requirements 12.4, 12.5**

### Property 12: Filter Data Cuti dengan AND Semantics

*For any* kombinasi nilai filter `status_approval_1` dan `status_cuti` yang diterapkan bersamaan pada
halaman `/data-cuti`, setiap record yang dikembalikan SHALL memenuhi **kedua** kriteria filter secara
bersamaan. Tidak ada record yang memenuhi hanya satu kriteria yang muncul dalam hasil.

**Validates: Requirements 14.3**

---

## Error Handling

### Laporan Pivot

- **Tanggal tidak valid / tidak diberikan:** Controller mengembalikan `redirect()->back()->withErrors([...])` dengan pesan yang menyebutkan parameter mana yang bermasalah (Req 1.7). Tidak ada query ke DB yang dijalankan.
- **Rentang tanggal terbalik (`mulai > akhir`):** Ditangkap oleh validasi `'akhir' => 'after_or_equal:mulai'`.
- **Tidak ada data:** View menampilkan pesan "Tidak ada data untuk periode ini" dan menyembunyikan semua chart (Req 4.4).

### Modul Dosen

- **NIDN duplikat:** Validasi `Rule::unique('users','nidn')->where('status_aktif', true)->ignore($id)` pada store/update. Response: redirect back dengan error flash.
- **Email duplikat:** Validasi `unique:users,email` standar Laravel.
- **Hapus dosen:** Tidak melakukan `delete()`, hanya `update(['status_aktif' => false])`. Jika record tidak ditemukan, abort 404.

### Approval Cuti Bertingkat

- **Akses tidak sah (Level 1 oleh non-kepala_cabang):** Middleware `role:kepala_cabang` mengembalikan 403.
- **Akses Level 2 sebelum Level 1 selesai:** Controller `approvalLevel2()` memeriksa `status_approval_1` tidak dalam `['Disetujui','Dilewati']` dan mengembalikan redirect dengan error flash "Approval Level 1 belum selesai."
- **Double approval (status sudah final):** Controller memeriksa `status_cuti` tidak `'Diterima'` atau `'Ditolak'` sebelum memproses; jika sudah final, mengembalikan redirect dengan pesan "Status cuti sudah final."
- **Izin Telat/Pulang Cepat tanpa mapping_shift:** Logika sudah ada di `editAdminProses` — dikopi ke `approvalLevel2` dengan behavior yang sama: revert status ke `Pending` dan tampilkan error.

---

## Testing Strategy

### Unit Tests

Digunakan untuk fungsi-fungsi logika diskrit:

- `PivotBuilder::buildCodes(array $mappingShifts, array $dates): array` — uji pemetaan status ke kode
- `PivotBuilder::buildSummary(array $codes): array` — uji agregasi ringkasan
- `PivotBuilder::isSunday(string $date): bool` — uji penandaan hari Minggu
- `PivotBuilder::paginateByDates(array $dates, int $chunkSize): array` — uji logika pagination PDF
- `CutiStateHelper::canApproveLevel1(Cuti $cuti, User $user): bool` — uji visibilitas tombol Level 1
- `CutiStateHelper::canApproveLevel2(Cuti $cuti, User $user): bool` — uji visibilitas tombol Level 2

Fokus unit test: contoh spesifik, nilai batas (31 vs 32 hari, 15 vs 16 kolom), dan kondisi error.

### Property-Based Tests

Library: **[pest-plugin-arch](https://pestphp.com/)** atau **[eris/eris](https://github.com/giorgiosironi/eris)** (PHP property-based testing).
Alternatif yang lebih praktis untuk PHP: menggunakan Pest dengan data provider generatif custom.

Minimum 100 iterasi per property test. Setiap test diberi komentar:
```php
// Feature: absensi-report-dosen-cuti, Property 1: Pivot Completeness dengan Filter Lokasi
```

**Property tests yang diimplementasikan:**

| Property | Fungsi yang ditest | Generator |
|----------|--------------------|-----------|
| P1 | `buildPivot(users, dates, shifts)` | users acak, rentang tanggal acak 1-365 hari |
| P2 | `mapStatusToCode(status)` | string status_absen acak |
| P3 | `markSundays(dates)` | rentang tanggal acak |
| P4 | `buildSummary(codes)` | array kode acak |
| P5 | `validateDateRange(mulai, akhir)` | string acak termasuk format tidak valid |
| P6 | `buildMonthlyCount(shifts, year)` | shifts dengan berbagai status_absen |
| P7 | `filterByTipeUser(users, tipe)` | campuran user dosen dan pegawai |
| P8 | `validateNidnUniqueness(nidn, existingDosens)` | NIDN string acak |
| P9-P10 | `canApprove(cuti, user)` | cuti dengan kombinasi status acak |
| P11 | `getBadgeForKaryawan(cuti)` | kombinasi (status_approval_1, status_cuti) |
| P12 | `filterCuti(records, filter1, filter2)` | records dengan status acak, filter acak |

### Integration Tests

- Export Excel: Generate file, buka dengan PhpSpreadsheet, verifikasi header dan nama file.
- Export PDF: Verifikasi response `Content-Type: application/pdf` dan header `Content-Disposition`.
- Migration backward compatibility: Jalankan migration pada DB test dengan data lama, verifikasi `status_approval_1 = 'Dilewati'`.
- Notifikasi: Mock `NotifApproval` event dan `UserNotification`, verifikasi dispatch ke penerima yang tepat di tiap tahap.
- Spatie Role assignment: Verifikasi `$user->hasRole('dosen')` setelah DosenController::store().
