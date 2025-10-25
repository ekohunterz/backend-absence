@php
    use Carbon\Carbon;

    $monthName = $month ? Carbon::create()->month($month)->translatedFormat('F') : 'Semua Bulan';
@endphp

<table>
    {{-- Judul --}}
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold; font-size: 16px;">
            LAPORAN ABSENSI SISWA
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center; font-size: 14px;">
            Kelas: {{ $grade->name }} — Bulan: {{ $monthName }} — Tahun Ajaran: {{ $academic_year }}
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr> {{-- Spasi --}}

    {{-- Header --}}
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center;">
        <th style="border: 1px solid #ccc; padding: 5px;">Nama</th>
        <th style="border: 1px solid #ccc; padding: 5px;">NIS</th>
        <th style="border: 1px solid #ccc; padding: 5px;">Hadir</th>
        <th style="border: 1px solid #ccc; padding: 5px;">Izin</th>
        <th style="border: 1px solid #ccc; padding: 5px;">Sakit</th>
        <th style="border: 1px solid #ccc; padding: 5px;">Alpa</th>
    </tr>

    {{-- Data --}}
    @foreach ($reports as $r)
        <tr>
            <td style="border: 1px solid #ccc; padding: 5px;">{{ $r['name'] }}</td>
            <td style="border: 1px solid #ccc; padding: 5px;">{{ $r['nis'] }}</td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">{{ $r['hadir'] }}</td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">{{ $r['izin'] }}</td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">{{ $r['sakit'] }}</td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">{{ $r['alpa'] }}</td>
        </tr>
    @endforeach

    {{-- Total --}}
    <tr style="font-weight: bold; background-color: #f9f9f9;">
        <td style="border: 1px solid #ccc; padding: 5px; font-weight: 900;">Total</td>
        <td style="border: 1px solid #ccc; padding: 5px;"></td>
        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
            {{ $reports->sum('hadir') }}
        </td>
        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
            {{ $reports->sum('izin') }}
        </td>
        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
            {{ $reports->sum('sakit') }}
        </td>
        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
            {{ $reports->sum('alpa') }}
        </td>
    </tr>
</table>
