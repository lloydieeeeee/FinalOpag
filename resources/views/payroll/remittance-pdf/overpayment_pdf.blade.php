<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Overpayment Remittance - {{ $period->period_label }}</title>
    <style>
        @page { margin: 0.5in; size: legal landscape; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #000; }
        .header-table { width: 70%; margin: 0 auto 20px auto; border: none; }
        .header-table td { border: none; vertical-align: middle; }
        .header-text { font-family: "Times New Roman", Times, serif; text-align: center; }
        .header-text .rep { font-size: 14px; margin-bottom: 2px; }
        .header-text .prov { font-size: 21px; font-weight: bold; }
        .header-text hr { border: none; border-top: 2px solid #1b5e20; margin: 4px 0; }
        .header-text .office { font-size: 16px; font-weight: bold; }
        .report-title { text-align: center; font-weight: bold; font-size: 12px; margin-top: 10px; text-transform: uppercase; font-family: "Times New Roman", Times, serif; }
        .report-subtitle { text-align: center; font-size: 11px; margin-bottom: 15px; font-family: "Times New Roman", Times, serif; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }
        .data-table th { text-align: center; font-weight: normal; font-size: 10px; text-transform: uppercase; background-color: #fff; font-family: "Times New Roman", Times, serif;}
        .data-table td { font-size: 10px; font-family: "Times New Roman", Times, serif;}
        .text-center { text-align: center; } .text-right { text-align: right; } .font-bold { font-weight: bold; }
        .signatory-table { width: 100%; border: none; margin-top: 40px; font-size: 11px; font-family: Arial, Helvetica, sans-serif; }
        .signatory-table td { border: none; vertical-align: bottom; }
        .sig-name { font-weight: bold; font-size: 12px; text-decoration: underline; margin-bottom: 2px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="20%" class="text-right" style="padding-right: 15px;"><img src="{{ public_path('images/kapitolyo.png') }}" style="height: 75px;"></td>
            <td width="60%" class="header-text">
                <div class="rep">Republic of the Philippines</div><div class="prov">PROVINCE OF CAMARINES NORTE</div><hr><div class="office">OFFICE OF THE PROVINCIAL AGRICULTURIST</div>
            </td>
            <td width="20%" class="text-left" style="padding-left: 15px;"><img src="{{ public_path('images/Bagong_Pilipinas_logo.png') }}" style="height: 75px;"></td>
        </tr>
    </table>
    <div class="report-title">OVERPAYMENT RECOVERY REMITTANCE</div>
    <div class="report-subtitle">For the period covered {{ strtoupper($period->period_label) }}</div>

    @php
        $filtered = $records->filter(fn($r) => ($r->overpayment??0)>0)->values();
        $totOver = $filtered->sum('overpayment');
        $fmt = fn($v) => ($v > 0) ? number_format($v, 2) : '';
        $dtFmt = fn($d) => $d ? date('n/j/Y', strtotime($d)) : '';
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO.</th>
                <th width="20%">LAST NAME</th>
                <th width="20%">FIRST NAME</th>
                <th width="15%">MIDDLE NAME</th>
                <th width="10%">NAME EXT.</th>
                <th width="15%">BIRTHDATE</th>
                <th width="15%">AMOUNT DUE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($filtered as $index => $r)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($r->employee->last_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->first_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->middle_name ?? '') }}</td>
                    <td class="text-center">{{ strtoupper($r->employee->name_extension ?? '') }}</td>
                    <td class="text-center">{{ $dtFmt($r->employee->birthdate) }}</td>
                    <td class="text-right">{{ $fmt($r->overpayment) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" class="text-right font-bold">TOTAL</td>
                <td class="text-right font-bold">{{ $totOver > 0 ? number_format($totOver, 2) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signatory-table">
        <tr>
            <td width="15%"></td>
            <td width="35%">Prepared by:<br><br><br><div class="sig-name">{{ strtoupper($preparedBy->full_name ?? 'MELINDA R. BARCELONA') }}</div><div>{{ $preparedBy->title ?? 'Admin. Officer V' }}</div></td>
            <td width="20%"></td>
            <td width="30%">Certified Correct:<br><br><br><div class="sig-name">{{ strtoupper($certifiedBy->full_name ?? 'ALMIRANTE A. ABAD') }}</div><div>{{ $certifiedBy->title ?? 'Provincial Agriculturist' }}</div></td>
        </tr>
    </table>
</body>
</html>