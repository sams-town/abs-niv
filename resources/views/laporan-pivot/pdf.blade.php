<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;font-size:9px;}
table{width:100%;border-collapse:collapse;margin-bottom:8px;}
th,td{border:1px solid #444;padding:2px 3px;text-align:center;}
th{background:#4472C4;color:#fff;}
.hdr{text-align:center;margin-bottom:8px;}
.hdr h2{margin:0;font-size:13px;}
.hdr p{margin:2px 0;font-size:10px;}
.tfoot-row{background:#f2f2f2;font-weight:bold;}
.pb{page-break-after:always;}
</style>
</head>
<body>
@foreach($pages as $pageIdx => $pageDates)
@if($pageIdx > 0)<div class="pb"></div>@endif
<div class="hdr">
    <h2>{{ optional($settings)->nama_perusahaan ?? 'Laporan Absensi' }}</h2>
    <p>Laporan Absensi | Periode: {{ $mulai }} s/d {{ $akhir }}@if($lokasi) | Lokasi: {{ $lokasi->nama_lokasi }}@endif | Hal.{{ $pageIdx+1 }}/{{ count($pages) }}</p>
</div>
<table>
    <thead>
        <tr>
            <th>No</th><th>Nama Pegawai</th>
            @foreach($pageDates as $d)<th>{{ date('d/m',strtotime($d)) }}</th>@endforeach
            <th>H</th><th>C</th><th>I</th><th>A</th><th>S</th><th>%</th>
        </tr>
    </thead>
    <tbody>
        @php $tH=0;$tA=0;$tC=0;$tI=0; @endphp
        @foreach($rows as $i => $row)
        @php $startIdx=$pageIdx*15; $pageCodes=array_slice($row['codes'],$startIdx,count($pageDates)); @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td style="text-align:left">{{ $row['user']->name }}</td>
            @foreach($pageCodes as $code)<td>{{ $code }}</td>@endforeach
            <td>{{ $row['summary']['hadir'] }}</td>
            <td>{{ $row['summary']['cuti'] }}</td>
            <td>{{ $row['summary']['izin'] }}</td>
            <td>{{ $row['summary']['alfa'] }}</td>
            <td>{{ $row['summary']['sakit'] }}</td>
            <td>{{ $row['summary']['persentase'] }}%</td>
        </tr>
        @php $tH+=$row['summary']['hadir'];$tA+=$row['summary']['alfa'];$tC+=$row['summary']['cuti'];$tI+=$row['summary']['izin']; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="tfoot-row">
            <td colspan="{{ count($pageDates)+2 }}">TOTAL</td>
            <td>{{ $tH }}</td><td>{{ $tC }}</td><td>{{ $tI }}</td><td>{{ $tA }}</td><td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endforeach
</body>
</html>
