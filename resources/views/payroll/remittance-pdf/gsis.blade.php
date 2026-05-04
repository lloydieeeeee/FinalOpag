<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>GSIS Remittance - {{ $period->period_label }}</title>
    <style>
        /* Set page to Legal Landscape for wide tables */
        @page { 
            margin: 0.5in; 
            size: legal landscape; 
        }
        body { 
            /* Default body font for the data tables */
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 9px; 
            color: #000;
        }
        
        /* -- Header Layout -- */
        .header-table { 
            /* Shrinking the width and centering it brings the logos closer to the text */
            width: 70%; 
            margin: 0 auto 20px auto; 
            border: none; 
        }
        .header-table td { 
            border: none; 
            vertical-align: middle; 
        }
        .header-text {
            /* Official headers use a formal serif font */
            font-family: "Times New Roman", Times, serif; 
            text-align: center;
        }
        .header-text .rep { 
            font-size: 14px; 
            margin-bottom: 2px; 
        }
        .header-text .prov { 
            font-size: 21px; 
            font-weight: bold; 
        }
        .header-text hr { 
            border: none; 
            /* Dark green line matching the original */
            border-top: 2px solid #1b5e20; 
            margin: 4px 0; 
        }
        .header-text .office { 
            font-size: 16px; 
            font-weight: bold; 
        }
        
        /* -- Titles -- */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif; 
        }
        .report-subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
            font-family: Arial, Helvetica, sans-serif; 
        }

        /* -- Main Data Table -- */
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 3px 4px; 
            vertical-align: middle;
        }
        .data-table th { 
            text-align: center; 
            font-weight: bold; 
            font-size: 8px;
            text-transform: uppercase;
            background-color: #fff;
        }
        .data-table td { font-size: 9px; }
        
        /* Utilities */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mono { font-family: 'Courier New', Courier, monospace; }
        
        /* Footer/Signatories */
        .signatory-table {
            width: 100%;
            border: none;
            margin-top: 40px;
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif; 
        }
        .signatory-table td {
            border: none;
            vertical-align: bottom;
        }
        .sig-name {
            font-weight: bold;
            font-size: 12px;
            text-decoration: underline;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- Left Logo: Kapitolyo -->
            <td width="20%" class="text-right" style="padding-right: 15px;">
                <img src="{{ public_path('images/kapitolyo.png') }}" style="height: 75px;">
            </td>
            
            <td width="60%" class="header-text">
                <div class="rep">Republic of the Philippines</div>
                <div class="prov">PROVINCE OF CAMARINES NORTE</div>
                <hr>
                <div class="office">OFFICE OF THE PROVINCIAL AGRICULTURIST</div>
            </td>
            
            <!-- Right Logo: Bagong Pilipinas -->
            <td width="20%" class="text-left" style="padding-left: 15px;">
                <img src="{{ public_path('images/Bagong_Pilipinas_logo.png') }}" style="height: 75px;">
            </td>
        </tr>
    </table>

    <div class="report-title">GOVERNMENT SERVICE INSURANCE SYSTEM CONTRIBUTION REMITTANCE</div>
    <div class="report-subtitle">For the period covered {{ strtoupper($period->period_label) }}</div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="2%">NO</th>
                <th colspan="4">NAME</th>
                <th rowspan="2" width="6%">BIRTHDATE</th>
                <th rowspan="2" width="8%">GSIS<br>NUMBER</th>
                <th rowspan="2" width="6%">BASIC<br>SALARY</th>
                <th colspan="2">LIFE/RETIREMENT<br>INSURANCE CONTRIBUTION</th>
                <th rowspan="2" width="5%">ECF</th>
                <th rowspan="2" width="5%">CONSO<br>LOAN</th>
                <th rowspan="2" width="5%">POLICY<br>LOAN</th>
                <th rowspan="2" width="6%">EMERGENCY<br>LOAN</th>
                <th rowspan="2" width="6%">REAL STATE<br>LOAN</th>
            </tr>
            <tr>
                <th width="10%">LAST NAME</th>
                <th width="10%">FIRST NAME</th>
                <th width="8%">MIDDLE NAME</th>
                <th width="3%">EXT.</th>
                <th width="6%">PS</th>
                <th width="6%">GS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totGross  = 0;
                $totPs     = 0;
                $totGs     = 0;
                $totEcf    = 0;
                $totConso  = 0;
                $totPolicy = 0;
                $totEmerg  = 0;
                $totRealE  = 0;
            @endphp

            @foreach($records as $index => $r)
                @php
                    $totGross  += $r->gross_salary;
                    $totPs     += $r->gsis_ee;
                    $totGs     += $r->gsis_govt;
                    $totEcf    += $r->gsis_ec;
                    $totConso  += $r->gsis_conso;
                    $totPolicy += $r->gsis_policy;
                    $totEmerg  += $r->gsis_emergency;
                    $totRealE  += $r->gsis_real_estate;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($r->employee->last_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->first_name ?? '') }}</td>
                    <td>{{ strtoupper($r->employee->middle_name ?? '') }}</td>
                    <td class="text-center">{{ strtoupper($r->employee->name_extension ?? '') }}</td>
                    
                    {{-- Formats the birthdate if it exists, otherwise leaves blank --}}
                    <td class="text-center">{{ $r->employee->birthdate ? date('m/d/Y', strtotime($r->employee->birthdate)) : '' }}</td>
                    
                    <td class="text-center">{{ $r->employee->gsis_number ?? '' }}</td>
                    <td class="text-right mono">{{ number_format($r->gross_salary, 2) }}</td>
                    <td class="text-right mono">{{ number_format($r->gsis_ee, 2) }}</td>
                    <td class="text-right mono">{{ number_format($r->gsis_govt, 2) }}</td>
                    <td class="text-right mono">{{ number_format($r->gsis_ec, 2) }}</td>
                    
                    <td class="text-right mono">{{ ($r->gsis_conso ?? 0) > 0 ? number_format($r->gsis_conso, 2) : '' }}</td>
                    <td class="text-right mono">{{ ($r->gsis_policy ?? 0) > 0 ? number_format($r->gsis_policy, 2) : '' }}</td>
                    <td class="text-right mono">{{ ($r->gsis_emergency ?? 0) > 0 ? number_format($r->gsis_emergency, 2) : '' }}</td>
                    <td class="text-right mono">{{ ($r->gsis_real_estate ?? 0) > 0 ? number_format($r->gsis_real_estate, 2) : '' }}</td>
                </tr>
            @endforeach

            <!-- Subtotal / Page Total Row -->
            <tr>
                <td colspan="7" class="text-right font-bold">PAGE TOTAL</td>
                <td class="text-right font-bold mono">{{ number_format($totGross, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totPs, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totGs, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totEcf, 2) }}</td>
                <td class="text-right font-bold mono">{{ $totConso > 0 ? number_format($totConso, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totPolicy > 0 ? number_format($totPolicy, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totEmerg > 0 ? number_format($totEmerg, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totRealE > 0 ? number_format($totRealE, 2) : '-' }}</td>
            </tr>
            
            <!-- Grand Total Row -->
            <tr>
                <td colspan="7" class="text-right font-bold">Grand Total</td>
                <td class="text-right font-bold mono">{{ number_format($totGross, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totPs, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totGs, 2) }}</td>
                <td class="text-right font-bold mono">{{ number_format($totEcf, 2) }}</td>
                <td class="text-right font-bold mono">{{ $totConso > 0 ? number_format($totConso, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totPolicy > 0 ? number_format($totPolicy, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totEmerg > 0 ? number_format($totEmerg, 2) : '-' }}</td>
                <td class="text-right font-bold mono">{{ $totRealE > 0 ? number_format($totRealE, 2) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signatories -->
    <table class="signatory-table">
        <tr>
            <td width="15%"></td>
            <td width="35%">
                Prepared by:<br><br><br>
                <div class="sig-name">{{ strtoupper($preparedBy->full_name ?? 'MELINDA R. BARCELONA') }}</div>
                <div>{{ $preparedBy->title ?? 'Admin. Officer V' }}</div>
            </td>
            <td width="20%"></td>
            <td width="30%">
                Certified Correct:<br><br><br>
                <div class="sig-name">{{ strtoupper($certifiedBy->full_name ?? 'ALMIRANTE A. ABAD') }}</div>
                <div>{{ $certifiedBy->title ?? 'Provincial Agriculturist' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>