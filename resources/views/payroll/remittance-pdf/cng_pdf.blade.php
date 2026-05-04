<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CNGWMPC Remittance - {{ $period->period_label }}</title>
    <style>
        @page { margin: 0.5in; size: legal landscape; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8px; color: #000; }
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
        .data-table th, .data-table td { border: 1px solid #000; padding: 2px 2px; vertical-align: middle; }
        .data-table th { text-align: center; font-weight: normal; font-size: 7px; background-color: #fff; }
        .data-table td { font-size: 8px; }
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
    <div class="report-title">CAMARINES NORTE GOVERNMENT WORKERS MULTI-PURPOSE COOPERATIVE (CNGWMPC) REMITTANCE</div>
    <div class="report-subtitle">For the period covered {{ strtoupper($period->period_label) }}</div>

    @php
        $filtered = $records->filter(fn($r) => ($r->loan_cngwmpc??0)>0)->values();
        $totCng = $filtered->sum('loan_cngwmpc');
        $fmt = fn($v) => ($v > 0) ? number_format($v, 2) : '';
        
        $t_cap=0; $t_kid=0; $t_sav=0; $t_reg=0; $t_cri=0; $t_can=0; 
        $t_sto=0; $t_cal=0; $t_abu=0; $t_han=0; $t_b2b=0; $t_pet=0; $t_com=0;
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th width="2%">NO</th>
                <th width="8%">LAST NAME</th>
                <th width="8%">FIRST NAME</th>
                <th width="7%">MIDDLE NAME</th>
                <th width="4%">NAME<br>EXT.</th>
                <th width="5%">CAPITAL<br>SHARE</th>
                <th width="5%">kiddie<br>savings</th>
                <th width="5%">SAVINGS</th>
                <th width="6%">REGULAR<br>LOAN</th>
                <th width="6%">IN CRISIS<br>LOAN</th>
                <th width="5%">Coop<br>Canteen</th>
                <th width="5%">Coop<br>Store</th>
                <th width="6%">CALAMITY<br>LOAN</th>
                <th width="6%">ABULOY<br>PROGRAM</th>
                <th width="6%">HANDOG<br>KABUHAYAN/<br>ITURO MO</th>
                <th width="6%">BACK TO BACK<br>LOAN/SPECIAL<br>LOAN</th>
                <th width="5%">PETTY<br>CASH</th>
                <th width="5%">COMMODITY<br>LOAN</th>
                <th width="6%">TOTAL DUE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($filtered as $index => $r)
                @php
                    $t_cap += $r->cng_capital_share ?? 0;
                    $t_kid += $r->cng_kiddie_savings ?? 0;
                    $t_sav += $r->cng_savings ?? 0;
                    $t_reg += $r->cng_regular_loan ?? 0;
                    $t_cri += $r->cng_crisis_loan ?? 0;
                    $t_can += $r->cng_coop_canteen ?? 0;
                    $t_sto += $r->cng_coop_store ?? 0;
                    $t_cal += $r->cng_calamity_loan ?? 0;
                    $t_abu += $r->cng_abuloy ?? 0;
                    $t_han += $r->cng_handog ?? 0;
                    $t_b2b += $r->cng_b2b_loan ?? 0;
                    $t_pet += $r->cng_petty_cash ?? 0;
                    $t_com += $r->cng_commodity_loan ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($r->employee->last_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->first_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->middle_name ?? '') }}</td>
                    <td class="text-center">{{ strtoupper($r->employee->name_extension ?? '') }}</td>
                    
                    <td class="text-right">{{ $fmt($r->cng_capital_share ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_kiddie_savings ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_savings ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_regular_loan ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_crisis_loan ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_coop_canteen ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_coop_store ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_calamity_loan ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_abuloy ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_handog ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_b2b_loan ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_petty_cash ?? 0) }}</td>
                    <td class="text-right">{{ $fmt($r->cng_commodity_loan ?? 0) }}</td>
                    <td class="text-right font-bold">{{ $fmt($r->loan_cngwmpc) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-center">Sub-total</td>
                <td class="text-right">{{ $fmt($t_cap) }}</td><td class="text-right">{{ $fmt($t_kid) }}</td>
                <td class="text-right">{{ $fmt($t_sav) }}</td><td class="text-right">{{ $fmt($t_reg) }}</td>
                <td class="text-right">{{ $fmt($t_cri) }}</td><td class="text-right">{{ $fmt($t_can) }}</td>
                <td class="text-right">{{ $fmt($t_sto) }}</td><td class="text-right">{{ $fmt($t_cal) }}</td>
                <td class="text-right">{{ $fmt($t_abu) }}</td><td class="text-right">{{ $fmt($t_han) }}</td>
                <td class="text-right">{{ $fmt($t_b2b) }}</td><td class="text-right">{{ $fmt($t_pet) }}</td>
                <td class="text-right">{{ $fmt($t_com) }}</td>
                <td class="text-right">{{ number_format($totCng, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-center font-bold">Grand Total</td>
                <td class="text-right font-bold">{{ $fmt($t_cap) }}</td><td class="text-right font-bold">{{ $fmt($t_kid) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_sav) }}</td><td class="text-right font-bold">{{ $fmt($t_reg) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_cri) }}</td><td class="text-right font-bold">{{ $fmt($t_can) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_sto) }}</td><td class="text-right font-bold">{{ $fmt($t_cal) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_abu) }}</td><td class="text-right font-bold">{{ $fmt($t_han) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_b2b) }}</td><td class="text-right font-bold">{{ $fmt($t_pet) }}</td>
                <td class="text-right font-bold">{{ $fmt($t_com) }}</td>
                <td class="text-right font-bold">{{ number_format($totCng, 2) }}</td>
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