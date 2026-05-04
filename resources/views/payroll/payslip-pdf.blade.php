@php
    if (isset($records)) {
        $list = $records;
    } elseif (isset($record)) {
        $list = collect([$record]);
    } else {
        $list = collect([]);
    }
    $rows     = $list->chunk(2);
    $rowCount = $rows->count();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslips — {{ $list->first()?->period->period_label ?? '' }}</title>
    <style>
        /*
         * Letter:  215.9mm × 279.4mm  → usable with 6mm margins: 203.9mm × 267.4mm
         * 4 per page: 2 cols × 2 rows
         *   each cell: 101.95mm wide × 133.7mm tall
         *   inner pad: 2mm → content: 97.95mm × 129.7mm
         *
         * All sizes use mm/pt only — no px — so DomPDF renders predictably.
         */

        @page {
            size: letter portrait;
            margin: 6mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width:  203.9mm;   /* 215.9 - 2×6mm */
            height: 267.4mm;   /* 279.4 - 2×6mm */
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.5pt;
            color: #000;
            background: #fff;
        }

        /* ── 2-column page row ── */
        .page-row {
            width:  203.9mm;
            height: 133.7mm;   /* 267.4 / 2 */
            display: table;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .page-col {
            display: table-cell;
            width:   101.95mm; /* 203.9 / 2 */
            height:  133.7mm;
            vertical-align: top;
            padding: 1.5mm;
        }

        /* page break after every 2 rows (every full page) */
        .break-after { page-break-after: always; }

        /* ── Payslip box ── */
        .payslip-doc {
            width:    98.95mm;  /* col - 2×1.5mm padding */
            height:   130.7mm;  /* row - 2×1.5mm padding */
            overflow: hidden;
            padding:  1.8mm 2.5mm;
            border:   0.5pt dashed #888;
        }

        /* ── Header ── */
        .ps-header {
            text-align: center;
            padding-bottom: 0.7mm;
            margin-bottom:  0.6mm;
            border-bottom:  0.4pt solid #555;
        }
        .ps-header p      { font-size: 5.5pt; line-height: 1.3; }
        .ps-header .bold  { font-weight: bold; font-size: 6pt; }
        .ps-header .title { font-weight: bold; font-size: 7pt; letter-spacing: 0.4pt; margin-top: 0.3mm; }

        /* ── Info table (Name / Position / Period) ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 0.6mm; }
        .info-table td {
            font-size:    6pt;
            padding:      0.25mm 0;
            vertical-align: top;
            line-height:  1.25;
        }
        .i-lbl { width: 17mm; color: #444; white-space: nowrap; }
        .i-sep { width:  2mm; }
        .i-val { font-weight: bold; }

        /* ── Main payslip table ── */
        .ps-table { width: 100%; border-collapse: collapse; }
        .ps-table td {
            font-size:    6pt;
            padding:      0.28mm 0.5pt;
            vertical-align: bottom;
            line-height:  1.2;
        }

        /*
         * Three cols:
         *   c-lbl  : item label  — 53%
         *   c-ul   : underline   — 28%  (border-bottom only)
         *   c-amt  : amount      — 19%  (right-aligned)
         */
        .c-lbl { width: 53%; }
        .c-ul  { width: 28%; border-bottom: 0.35pt solid #444; }
        .c-amt {
            width:       19%;
            text-align:  right;
            font-weight: 600;
            white-space: nowrap;
            padding-right: 0.5pt;
        }

        /* bold earning rows */
        .row-bold td { font-weight: bold; font-size: 6.5pt; }

        /* thin divider between earnings and deductions */
        .row-div td {
            border-top:  0.3pt solid #bbb;
            padding-top: 0.3mm;
            font-size:   0; /* zero-height row */
            line-height: 0;
        }

        /* "Less Deduction" label */
        .row-section td {
            font-size:   6pt;
            padding-top: 0.3mm;
            padding-bottom: 0.1mm;
        }

        /* regular deduction rows */
        .row-sub td { font-size: 6pt; }

        /* TOTAL DEDUCTION */
        .row-total td {
            font-weight:  bold;
            font-size:    6.5pt;
            border-top:   0.7pt solid #000;
            padding-top:  0.7mm;
        }

        /* NET PAY */
        .row-net td {
            font-weight:  bold;
            font-size:    7.5pt;
            border-top:   0.9pt solid #000;
            padding-top:  0.7mm;
        }

        /* ── Signatory ── */
        .ps-sig {
            text-align:   center;
            margin-top:   1mm;
            padding-top:  0.5mm;
            border-top:   0.4pt solid #aaa;
        }
        .sig-name  { font-weight: bold; font-size: 6pt; text-transform: uppercase; }
        .sig-title { font-size: 5.5pt; color: #555; }
    </style>
</head>
<body>

@forelse($rows as $rowIndex => $pair)
@php
    $isSecondRow = (($rowIndex + 1) % 2 === 0);
    $isLastRow   = ($rowIndex === $rowCount - 1);
    $breakClass  = ($isSecondRow && !$isLastRow) ? 'break-after' : '';
@endphp

<div class="page-row {{ $breakClass }}">

    @foreach($pair as $record)
    @php
        /* ── Name ── */
        $empName = strtoupper($record->employee->last_name  ?? '')
                 . ', '
                 . strtoupper($record->employee->first_name ?? '')
                 . ($record->employee->extension_name
                     ? ' ' . strtoupper($record->employee->extension_name)
                     : '');

        $designation = $record->designation
            ?? optional($record->employee->position)->position_code
            ?? 'N/A';

        /* ── Allowances ── */
        $pera  = (float)($record->allowance_pera  ?? 0);
        $rata  = (float)($record->allowance_rata  ?? 0);
        $ta    = (float)($record->allowance_ta    ?? 0);
        $other = (float)($record->allowance_other ?? 0);
        $totalAllowances = $pera + $rata + $ta + $other;

        $allowParts = [];
        if ($pera  > 0) $allowParts[] = 'PERA';
        if ($rata  > 0) $allowParts[] = 'RA';
        if ($ta    > 0) $allowParts[] = 'TA';
        if ($other > 0) $allowParts[] = 'Other';
        $allowLabel = count($allowParts) ? implode('/', $allowParts) : 'PERA';

        /* ── GSIS ── */
        $gsisEe        = (float)($record->gsis_ee          ?? 0);
        $gsisPolicy    = (float)($record->gsis_policy      ?? 0);
        $gsisEmergency = (float)($record->gsis_emergency   ?? 0);
        $gsisRealEst   = (float)($record->gsis_real_estate ?? 0);
        $gsisMpl       = (float)($record->gsis_mpl         ?? 0);
        $gsisMplLite   = (float)($record->gsis_mpl_lite    ?? 0);
        $gsisGfal      = (float)($record->gsis_gfal        ?? 0);
        $gsisComputer  = (float)($record->gsis_computer    ?? 0);
        $gsisConso     = (float)($record->gsis_conso       ?? 0);

        /* ── PAG-IBIG (pagibig_govt = employee ₱200) ── */
        $pagibigEe       = (float)($record->pagibig_govt     ?? 0);
        $pagibigMpl      = (float)($record->pagibig_mpl      ?? 0);
        $pagibigCalamity = (float)($record->pagibig_calamity ?? 0);

        /* ── PhilHealth ── */
        $phicEe = (float)($record->philhealth_ee ?? 0);

        /* ── Other ── */
        $wtax        = (float)($record->withholding_tax ?? 0);
        $dbp         = (float)($record->loan_dbp        ?? 0);
        $lbp         = (float)($record->loan_lbp        ?? 0);
        $cngwmpc     = (float)($record->loan_cngwmpc    ?? 0);
        $paracle     = (float)($record->loan_paracle    ?? 0);
        $overpayment = (float)($record->overpayment     ?? 0);
        $otherDed       = (float)($record->other_deduction       ?? 0);
        $otherDedLabel  = $record->other_deduction_label ?? 'Other Deduction';

        /* ── Signatory ── */
        // Use dedicated clerk fields on the period if set; fallback to createdBy user
        if (!empty($record->period->sig_clerk_name)) {
            $sigName  = strtoupper($record->period->sig_clerk_name);
            $sigTitle = $record->period->sig_clerk_title ?? 'AO V/PAYROLL CLERK';
        } else {
            $sigLast  = strtoupper(optional($record->period->createdBy)->last_name  ?? '');
            $sigFirst = strtoupper(optional($record->period->createdBy)->first_name ?? 'MELINDA R. BARCELONA');
            $sigName  = $sigLast ? "{$sigLast}, {$sigFirst}" : $sigFirst;
            $sigTitle = 'AO V/PAYROLL CLERK';
        }

        /* ── Format: blank when zero ── */
        $f = fn($v) => $v > 0 ? number_format($v, 2) : '';
    @endphp

    <div class="page-col">
    <div class="payslip-doc">

        {{-- ═══ HEADER ═══ --}}
        <div class="ps-header">
            <p>Republic of the Philippines</p>
            <p class="bold">PROVINCE OF CAMARINES NORTE</p>
            <p class="bold">D A E T</p>
            <p>OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
            <p class="title">PAY SLIP</p>
        </div>

        {{-- ═══ EMPLOYEE INFO ═══ --}}
        <table class="info-table">
            <tr>
                <td class="i-lbl">Name</td>
                <td class="i-sep">:</td>
                <td class="i-val">{{ $empName }}</td>
            </tr>
            <tr>
                <td class="i-lbl">Position</td>
                <td class="i-sep">:</td>
                <td class="i-val">{{ $designation }}</td>
            </tr>
            <tr>
                <td class="i-lbl">For the Period</td>
                <td class="i-sep">:</td>
                <td class="i-val">{{ $record->period->period_label ?? '' }}</td>
            </tr>
        </table>

        {{-- ═══ EARNINGS + DEDUCTIONS ═══ --}}
        <table class="ps-table">

            {{-- Gross Salary --}}
            <tr class="row-bold">
                <td class="c-lbl">Gross Salary</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ number_format($record->gross_salary, 2) }}</td>
            </tr>

            {{-- PERA / RA / TA --}}
            @if($totalAllowances > 0)
            <tr class="row-bold">
                <td class="c-lbl">{{ $allowLabel }}</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ number_format($totalAllowances, 2) }}</td>
            </tr>
            @endif

            {{-- Divider --}}
            <tr class="row-div"><td colspan="3"></td></tr>

            {{-- Less Deduction --}}
            <tr class="row-section">
                <td colspan="3">Less Deduction</td>
            </tr>

            {{-- UCPB (fixed placeholder) --}}
            <tr class="row-sub">
                <td class="c-lbl">UCPB</td>
                <td class="c-ul"></td>
                <td class="c-amt"></td>
            </tr>

            {{-- MPL --}}
            <tr class="row-sub">
                <td class="c-lbl">MPL</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisMpl) }}</td>
            </tr>

            {{-- CBCN (fixed placeholder) --}}
            <tr class="row-sub">
                <td class="c-lbl">CBCN</td>
                <td class="c-ul"></td>
                <td class="c-amt"></td>
            </tr>

            {{-- MSLAP (fixed placeholder) --}}
            <tr class="row-sub">
                <td class="c-lbl">MSLAP</td>
                <td class="c-ul"></td>
                <td class="c-amt"></td>
            </tr>

            {{-- Withholding Tax --}}
            <tr class="row-sub">
                <td class="c-lbl">Withholding Tax</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($wtax) }}</td>
            </tr>

            {{-- GSIS Salary Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS salary Loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisConso) }}</td>
            </tr>

            {{-- GSIS Policy Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Policy Loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisPolicy) }}</td>
            </tr>

            {{-- Medicare = PhilHealth EE --}}
            <tr class="row-sub">
                <td class="c-lbl">Medicare</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($phicEe) }}</td>
            </tr>

            {{-- GSIS Premium = EE 9% --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Premium</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisEe) }}</td>
            </tr>

            {{-- PAG-IBIG --}}
            <tr class="row-sub">
                <td class="c-lbl">PAG-IBIG</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($pagibigEe) }}</td>
            </tr>

            {{-- LBP --}}
            <tr class="row-sub">
                <td class="c-lbl">LBP</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($lbp) }}</td>
            </tr>

            {{-- DBP --}}
            <tr class="row-sub">
                <td class="c-lbl">DBP</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($dbp) }}</td>
            </tr>

            {{-- CNGWMPC --}}
            <tr class="row-sub">
                <td class="c-lbl">CNGWMPC</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($cngwmpc) }}</td>
            </tr>

            {{-- UOLI = PARACLE --}}
            <tr class="row-sub">
                <td class="c-lbl">UOLI</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($paracle) }}</td>
            </tr>

            {{-- GSIS Real State Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Real State Loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisRealEst) }}</td>
            </tr>

            {{-- GSIS Calamity Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Calamity Loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($pagibigCalamity) }}</td>
            </tr>

            {{-- Nursery (fixed placeholder) --}}
            <tr class="row-sub">
                <td class="c-lbl">Nursery</td>
                <td class="c-ul"></td>
                <td class="c-amt"></td>
            </tr>

            {{-- GSIS Emergency Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Em. Loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisEmergency) }}</td>
            </tr>

            {{-- GSIS Educ Loan --}}
            <tr class="row-sub">
                <td class="c-lbl">GSIS Educ loan</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisComputer) }}</td>
            </tr>

            {{-- PAG-IBIG Loyalty Card = pagibig_mpl --}}
            <tr class="row-sub">
                <td class="c-lbl">PAG IBIG Loyalty Card</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($pagibigMpl) }}</td>
            </tr>

            {{-- GSIS GFAL (only if > 0) --}}
            @if($gsisGfal > 0)
            <tr class="row-sub">
                <td class="c-lbl">GSIS GFAL</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisGfal) }}</td>
            </tr>
            @endif

            {{-- GSIS MPL Lite (only if > 0) --}}
            @if($gsisMplLite > 0)
            <tr class="row-sub">
                <td class="c-lbl">GSIS MPL Lite</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($gsisMplLite) }}</td>
            </tr>
            @endif

            {{-- Overpayment (only if > 0) --}}
            @if($overpayment > 0)
            <tr class="row-sub">
                <td class="c-lbl">Overpayment</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($overpayment) }}</td>
            </tr>
            @endif

            {{-- Other Deduction (only if > 0) — label comes from other_deduction_label --}}
            @if($otherDed > 0)
            <tr class="row-sub">
                <td class="c-lbl">{{ $otherDedLabel }}</td>
                <td class="c-ul"></td>
                <td class="c-amt" style="font-weight:700;">{{ $f($otherDed) }}</td>
            </tr>
            @endif

            {{-- TOTAL DEDUCTION --}}
            <tr class="row-total">
                <td class="c-lbl">TOTAL DEDUCTION</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ number_format($record->total_deductions, 2) }}</td>
            </tr>

            {{-- NET PAY --}}
            <tr class="row-net">
                <td class="c-lbl">NET PAY</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ number_format($record->net_pay, 2) }}</td>
            </tr>

        </table>

        {{-- ═══ SIGNATORY ═══ --}}
        <div class="ps-sig">
            <div class="sig-name">{{ $sigName }}</div>
            <div class="sig-title">{{ strtoupper($sigTitle) }}</div>
        </div>

    </div>
    </div>
    @endforeach

    {{-- Pad odd row with empty column --}}
    @if($pair->count() === 1)
    <div class="page-col"></div>
    @endif

</div>{{-- /.page-row --}}
@empty
    <p style="padding:10mm; font-size:10pt;">No payslip records found.</p>
@endforelse

</body>
</html>