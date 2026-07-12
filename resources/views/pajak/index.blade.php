@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">
                        <a href="{{ url('/pajak/export') }}{{ $_GET?'?'.$_SERVER['QUERY_STRING']: '' }}" class="btn btn-success">Export</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ url('/pajak') }}">
                        <div class="row mb-2">
                            <div class="col-3">
                                <select name="bulan" id="bulan" class="form-control selectpicker bulan" data-live-search="true">
                                    <option value="">Bulan</option>
                                    @foreach ($months as $moth_num => $month_name)
                                        <option value="{{ $moth_num }}" {{ $moth_num == request('bulan') ? 'selected="selected"' : '' }}>{{ $month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                @php
                                    $last= 2020;
                                    $now = date('Y') + 5;
                                @endphp
                                <select name="tahun" id="tahun" class="form-control selectpicker tahun" data-live-search="true">
                                    <option value="">Tahun</option>
                                    @for ($i = $now; $i >= $last; $i--)
                                        <option value="{{ $i }}" {{ $i == request('tahun') ? 'selected="selected"' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="border-radius: 10px">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="position: sticky; left: 0; background-color: rgb(215, 215, 215); z-index: 2;">No.</th>
                                    <th style="position: sticky; left: 40px; background-color: rgb(215, 215, 215); z-index: 2; min-width: 230px;" class="text-center">Nama Pegawai</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Bulan</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Tahun</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Status Pajak</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Penghasilan Bruto</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Penghasilan Netto Sebulan</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">Penghasilan Netto Setahun</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">PTKP</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">PKP Setahun</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">PPH 21 Setahun</th>
                                    <th style="min-width: 150px; background-color:rgb(243, 243, 243);" class="text-center">PPH 21 Sebulan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($payroll) <= 0)
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak Ada Data</td>
                                    </tr>
                                @else
                                    @foreach ($payroll as $key => $pay)
                                        @php
                                            Carbon\Carbon::setLocale('id');
                                            $bulan = Carbon\Carbon::createFromFormat('m', $pay->bulan)->translatedFormat('F');

                                            if ($pay->total_penjumlahan > 500000) {
                                                $biaya_jabatan = 500000;
                                            } else {
                                                $biaya_jabatan = $pay->total_penjumlahan * (5/100);
                                            }

                                            $pengurang_netto = $biaya_jabatan + $pay->total_potongan_bpjs_kesehatan + $pay->total_potongan_bpjs_ketenagakerjaan;

                                            $netto = $pay->total_penjumlahan - $pengurang_netto;
                                            $netto_year = $netto * 12;

                                            $pkp = $netto_year - $pay->user->sp->ptkp;
                                            $pkp = $pkp < 0 ? 0 : $pkp;

                                            if ($pkp <= 50000000) {
                                                $tax = 5;
                                            } else if ($pkp > 50000000 && $pkp <= 250000000) {
                                                $tax = 15;
                                            } else if ($pkp > 250000000 && $pkp <= 500000000) {
                                                $tax = 25;
                                            } else {
                                                $tax = 50;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center" style="position: sticky; left: 0; background-color: rgb(235, 235, 235); z-index: 1;">{{ ($payroll->currentpage() - 1) * $payroll->perpage() + $key + 1 }}.</td>
                                            <td class="text-center" style="position: sticky; left: 40px; background-color: rgb(235, 235, 235); z-index: 1;">{{ $pay->user->name ?? '-' }}</td>
                                            <td class="text-center">{{ $bulan }}</td>
                                            <td class="text-center">{{ $pay->tahun ?? '-' }}</td>
                                            <td class="text-center">{{ $pay->user->sp->name ?? '-' }}</td>
                                            <td class="text-center">Rp {{ number_format($pay->total_penjumlahan) }}</td>
                                            <td class="text-center">Rp {{ number_format($netto) }}</td>
                                            <td class="text-center">Rp {{ number_format($netto_year) }}</td>
                                            <td class="text-center">Rp {{ number_format($pay->user->sp->ptkp) }}</td>
                                            <td class="text-center">Rp {{ number_format($pkp) }}</td>
                                            <td class="text-center">Rp {{ number_format($pkp * ($tax/100)) }}</td>
                                            <td class="text-center">Rp {{ number_format(($pkp * ($tax/100)) / 12) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $payroll->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <br>

    @push('script')
        <script>
            $(document).ready(function() {
            });
        </script>
    @endpush
@endsection
