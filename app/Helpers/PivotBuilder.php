<?php

namespace App\Helpers;

class PivotBuilder
{
    /**
     * Map status_absen ke kode pivot.
     *
     * @param string|null $status
     * @return string
     */
    public static function mapStatusToCode(?string $status): string
    {
        return match ($status) {
            'Masuk'              => 'H',
            'Cuti'               => 'C',
            'Izin Masuk'         => 'I',
            'Izin Telat'         => 'IT',
            'Izin Pulang Cepat'  => 'IP',
            'Sakit'              => 'S',
            'Libur'              => 'L',
            null                 => 'A',
            default              => '-',
        };
    }

    /**
     * Bangun array kode per tanggal untuk satu pegawai.
     *
     * @param array $shiftsByDate  Key = tanggal string (Y-m-d), value = status_absen string|null
     * @param array $dates         Array string tanggal (Y-m-d) dalam rentang laporan
     * @return array               Array kode, indeks 0..n sejajar dengan $dates
     */
    public static function buildCodes(array $shiftsByDate, array $dates): array
    {
        $codes = [];

        foreach ($dates as $date) {
            // Jika tanggal ada di map, gunakan nilainya (bisa null); jika tidak ada, perlakukan sebagai null → 'A'
            $status = array_key_exists($date, $shiftsByDate) ? $shiftsByDate[$date] : null;
            $codes[] = self::mapStatusToCode($status);
        }

        return $codes;
    }

    /**
     * Bangun ringkasan statistik dari array kode satu pegawai.
     *
     * @param array $codes  Array kode pivot (H, C, I, IT, IP, S, L, A, -)
     * @return array        Associative: hadir, cuti, izin, alfa, sakit, libur, persentase
     */
    public static function buildSummary(array $codes): array
    {
        $hadir  = 0;
        $cuti   = 0;
        $izin   = 0;
        $alfa   = 0;
        $sakit  = 0;
        $libur  = 0;

        foreach ($codes as $code) {
            match ($code) {
                'H', 'IT', 'IP' => $hadir++,
                'C'             => $cuti++,
                'I'             => $izin++,
                'A'             => $alfa++,
                'S'             => $sakit++,
                'L'             => $libur++,
                default         => null,
            };
        }

        // Total untuk persentase = semua kode kecuali Libur
        $total = $hadir + $cuti + $izin + $alfa + $sakit;

        $persentase = $total > 0
            ? round(($hadir / $total) * 100, 1)
            : 0.0;

        return [
            'hadir'      => $hadir,
            'cuti'       => $cuti,
            'izin'       => $izin,
            'alfa'       => $alfa,
            'sakit'      => $sakit,
            'libur'      => $libur,
            'persentase' => $persentase,
        ];
    }

    /**
     * Periksa apakah tanggal tertentu adalah hari Minggu.
     *
     * @param string $date  Format Y-m-d
     * @return bool
     */
    public static function isSunday(string $date): bool
    {
        return (int) date('w', strtotime($date)) === 0;
    }

    /**
     * Bangun data chart dari baris-baris pivot.
     *
     * @param array $rows   Array baris pivot; setiap baris mengandung key 'cells' (array kode)
     *                      dan 'summary' (array dari buildSummary)
     * @param array $dates  Array string tanggal sejajar dengan indeks cells
     * @return array        Array dengan key 'bar' (totals per kategori) dan 'line' (hadir per tanggal)
     */
    public static function buildChartData(array $rows, array $dates): array
    {
        $bar = [
            'Hadir' => 0,
            'Cuti'  => 0,
            'Izin'  => 0,
            'Alfa'  => 0,
            'Sakit' => 0,
            'Libur' => 0,
        ];

        // Inisialisasi line chart dengan 0 untuk setiap tanggal
        $line = array_fill_keys($dates, 0);

        foreach ($rows as $row) {
            // Akumulasi bar chart dari summary
            $summary = $row['summary'] ?? [];
            $bar['Hadir'] += $summary['hadir'] ?? 0;
            $bar['Cuti']  += $summary['cuti']  ?? 0;
            $bar['Izin']  += $summary['izin']  ?? 0;
            $bar['Alfa']  += $summary['alfa']  ?? 0;
            $bar['Sakit'] += $summary['sakit'] ?? 0;
            $bar['Libur'] += $summary['libur'] ?? 0;

            // Akumulasi line chart: hitung hadir per tanggal
            $cells = $row['cells'] ?? [];
            foreach ($dates as $i => $date) {
                $code = $cells[$i] ?? null;
                if (in_array($code, ['H', 'IT', 'IP'], true)) {
                    $line[$date]++;
                }
            }
        }

        return [
            'bar'  => $bar,
            'line' => $line,
        ];
    }

    /**
     * Paginate array tanggal menjadi chunk-chunk untuk kebutuhan PDF multi-halaman.
     *
     * @param array $dates      Array string tanggal
     * @param int   $chunkSize  Jumlah tanggal per halaman (default 15)
     * @return array            Array of arrays (chunks)
     */
    public static function paginateByDates(array $dates, int $chunkSize = 15): array
    {
        return array_chunk($dates, $chunkSize);
    }
}
