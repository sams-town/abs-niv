<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\Jabatan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $defaultTipeUser;
    protected $validColumns;

    public function __construct($defaultTipeUser = 'pegawai')
    {
        $this->defaultTipeUser = $defaultTipeUser;
        $this->validColumns = Schema::getColumnListing('users');
    }

    public function model(array $row)
    {
        try {
            // --- Step 1: Get and validate required fields ---
            $name = trim($this->getValue($row, ['nama', 'name', 'namalengkap']));
            $email = trim($this->getValue($row, ['email', 'email']));
            $username = trim($this->getValue($row, ['username', 'user']));
            $password = trim($this->getValue($row, ['password', 'pass', 'katasandi']));
            $telepon = trim($this->getValue($row, ['telepon', 'phone', 'nohp', 'hp']));
            $lokasiName = trim($this->getValue($row, ['lokasi', 'lokasi']));
            $tglLahir = $this->getValue($row, ['tanggallahir', 'tgllahir', 'tgl_lahir', 'tanggal_lahir']);
            $jenisKelamin = trim($this->getValue($row, ['jeniskelamin', 'gender', 'jk', 'jenis_kelamin']));
            $tglMasuk = $this->getValue($row, ['tanggalmasuk', 'tglmasuk', 'tgl_join', 'tanggal_masuk']);
            $roleName = trim($this->getValue($row, ['role', 'peran', 'akses']));
            $jabatanName = trim($this->getValue($row, ['divisi', 'jabatan', 'namajabatan', 'nama_jabatan']));
            $isAdmin = trim($this->getValue($row, ['isadmin', 'is_admin', 'admin']));
            $namaIbuKandung = trim($this->getValue($row, ['namaibukandung', 'nama_ibu_kandung']));

            // Validate required fields
            if (empty($name) || empty($email) || empty($username) || empty($password) || empty($telepon) || 
                empty($lokasiName) || empty($tglLahir) || empty($jenisKelamin) || empty($tglMasuk) || 
                empty($roleName) || empty($jabatanName) || empty($namaIbuKandung)) {
                Log::warning('Skipping row: Missing required fields', $row);
                return null;
            }

            // --- Step 2: Resolve related models (Lokasi, Jabatan, Role) ---
            $lokasi = Lokasi::where('nama_lokasi', 'LIKE', "%$lokasiName%")->first();
            if (!$lokasi) {
                $lokasi = Lokasi::create([
                    'nama_lokasi' => $lokasiName,
                    'status' => 'approved',
                    'keterangan' => 'Office',
                    'created_by' => auth()->id() ?? 1
                ]);
            }

            $jabatan = Jabatan::where('nama_jabatan', 'LIKE', "%$jabatanName%")->first();
            if (!$jabatan) {
                $jabatan = Jabatan::create([
                    'nama_jabatan' => $jabatanName
                ]);
            }

            $role = Role::where('name', 'LIKE', "%$roleName%")->first();
            if (!$role) {
                $role = Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);
            }

            // --- Step 3: Prepare data array ---
            $data = [
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'telepon' => $telepon,
                'lokasi_id' => $lokasi->id,
                'tgl_lahir' => $this->transformDate($tglLahir),
                'gender' => $jenisKelamin,
                'tgl_join' => $this->transformDate($tglMasuk),
                'is_admin' => in_array(strtolower($isAdmin), ['admin', '1', 'yes']) ? 'admin' : 'user',
                'nama_ibu_kandung' => $namaIbuKandung,
                'status_pajak_id' => $this->transformNumber($this->getValue($row, ['statuspajak', 'statuspajakid', 'status_pajak_id']), null),
                'alamat' => $this->getValue($row, ['alamat', 'alamat']),
                'alamat_domisili' => $this->getValue($row, ['alamatdomisili', 'alamat_domisili']),
                'kontak_darurat_nama' => $this->getValue($row, ['kontakdaruratnama', 'kontak_darurat_nama']),
                'kontak_darurat_hp' => $this->getValue($row, ['kontakdarurathp', 'kontak_darurat_hp']),
                'kontak_darurat_hubungan' => $this->getValue($row, ['kontakdarurathubungan', 'kontak_darurat_hubungan']),
                'ktp' => (string)$this->getValue($row, ['ktp', 'ktp', 'noktp', 'no_ktp', 'nik']),
                'kartu_keluarga' => (string)$this->getValue($row, ['kartukeluarga', 'kartu_keluarga', 'nokk', 'no_kk']),
                'bpjs_kesehatan' => (string)$this->getValue($row, ['bpjskesehatan', 'bpjs_kesehatan']),
                'bpjs_ketenagakerjaan' => (string)$this->getValue($row, ['bpjsketenagakerjaan', 'bpjs_ketenagakerjaan']),
                'npwp' => (string)$this->getValue($row, ['npwp', 'npwp', 'nonpwp', 'no_npwp']),
                'sim' => (string)$this->getValue($row, ['sim', 'sim', 'nosim', 'no_sim']),
                'no_pkwt' => (string)$this->getValue($row, ['nip', 'nip', 'nopkwt', 'no_pkwt']),
                'no_kontrak' => (string)$this->getValue($row, ['nokontrak', 'no_kontrak']),
                'tanggal_mulai_pkwt' => $this->transformDate($this->getValue($row, ['tanggalmulaikontrak', 'tanggal_mulai_pkwt', 'tgl_mulai_pkwt'])),
                'tanggal_berakhir_pkwt' => $this->transformDate($this->getValue($row, ['tanggalberakhirkontrak', 'tanggal_berakhir_pkwt', 'tgl_berakhir_pkwt'])),
                'rekening' => (string)$this->getValue($row, ['rekening', 'norekening', 'no_rekening']),
                'nama_rekening' => $this->getValue($row, ['namarekening', 'nama_rekening', 'pemilikrekening', 'pemilik_rekening']),
                'izin_cuti' => $this->transformNumber($this->getValue($row, ['cuti', 'izincuti', 'izin_cuti']), 12),
                'izin_lainnya' => $this->transformNumber($this->getValue($row, ['izinmasuk', 'izin_lainnya']), 3),
                'izin_telat' => $this->transformNumber($this->getValue($row, ['izintelat', 'izin_telat']), 3),
                'izin_pulang_cepat' => $this->transformNumber($this->getValue($row, ['izinpulangcepat', 'izin_pulang_cepat']), 3),
                'cuti_melahirkan' => $this->transformNumber($this->getValue($row, ['cutimelahirkan', 'cuti_melahirkan']), 90),
                'cuti_kematian' => $this->transformNumber($this->getValue($row, ['cutikematian', 'cuti_kematian']), 3),
                'gaji_pokok' => $this->transformNumber($this->getValue($row, ['gajipokok', 'gaji_pokok', 'gapok']), 0),
                'tunjangan_makan' => $this->transformNumber($this->getValue($row, ['tunjanganmakan', 'tunjangan_makan']), 0),
                'tunjangan_transport' => $this->transformNumber($this->getValue($row, ['tunjangantransport', 'tunjangan_transport']), 0),
                'tunjangan_bpjs_kesehatan' => $this->transformNumber($this->getValue($row, ['tunjanganbpjskesehatan', 'tunjangan_bpjs_kesehatan']), 0),
                'tunjangan_bpjs_ketenagakerjaan' => $this->transformNumber($this->getValue($row, ['tunjanganbpjsketenagakerjaan', 'tunjangan_bpjs_ketenagakerjaan']), 0),
                'lembur' => $this->transformNumber($this->getValue($row, ['lembur', 'lembur']), 0),
                'kehadiran' => $this->transformNumber($this->getValue($row, ['kehadiran', 'kehadiran']), 0),
                'thr' => $this->transformNumber($this->getValue($row, ['thr', 'thr']), 0),
                'bonus_pribadi' => $this->transformNumber($this->getValue($row, ['bonuspribadi', 'bonus_pribadi']), 0),
                'bonus_team' => $this->transformNumber($this->getValue($row, ['bonusteam', 'bonus_team']), 0),
                'bonus_jackpot' => $this->transformNumber($this->getValue($row, ['bonusjackpot', 'bonus_jackpot']), 0),
                'terlambat' => $this->transformNumber($this->getValue($row, ['terlambat', 'terlambat']), 0),
                'batas_terlambat' => $this->transformNumber($this->getValue($row, ['batasterlambat', 'batas_terlambat']), 5),
                'mangkir' => $this->transformNumber($this->getValue($row, ['mangkir', 'mangkir']), 0),
                'potongan_bpjs_kesehatan' => $this->transformNumber($this->getValue($row, ['potonganbpjskesehatan', 'potongan_bpjs_kesehatan']), 0),
                'potongan_koperasi' => $this->transformNumber($this->getValue($row, ['potongankoperasi', 'potongan_koperasi']), 0)
            ];

            // --- Step 4: Check if user exists, update or create ---
            $user = User::where('email', $email)->orWhere('username', $username)->first();
            if ($user) {
                // If user exists, update (don't change password if not provided)
                if (empty($password)) {
                    unset($data['password']);
                }
                $user->update(array_filter($data, function($val) {
                    return $val !== null;
                }));
            } else {
                $user = User::create($data);
            }

            // --- Step 5: Assign role ---
            $user->syncRoles([$roleName]);

            return $user;
        } catch (\Throwable $e) {
            Log::error('UsersImport Error for row: ' . json_encode($row) . ' Message: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            throw $e;
        }
    }

    private function columnExists(string $column): bool
    {
        return in_array($column, $this->validColumns);
    }

    private function getValue(array $row, array $keys, $default = null)
    {
        $normalizedRow = [];
        foreach ($row as $k => $v) {
            $normKey = preg_replace('/[^a-z0-9]/', '', strtolower((string)$k));
            if (!isset($normalizedRow[$normKey]) || ($normalizedRow[$normKey] === null || $normalizedRow[$normKey] === '')) {
                $normalizedRow[$normKey] = $v;
            }
        }

        foreach ($keys as $key) {
            $normKey = preg_replace('/[^a-z0-9]/', '', strtolower($key));
            if (isset($normalizedRow[$normKey]) && $normalizedRow[$normKey] !== '' && $normalizedRow[$normKey] !== null) {
                return trim((string)$normalizedRow[$normKey]);
            }
        }

        return $default;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                //
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function transformNumber($value, $default = 0)
    {
        if ($value === null || $value === '') {
            return $default;
        }
        if (is_numeric($value)) {
            return $value;
        }
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
        return is_numeric($cleaned) ? $cleaned : $default;
    }
}
