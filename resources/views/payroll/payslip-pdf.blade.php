@php
    /* ── Fetch Dynamic Config (Including Inactive/Deleted for History) ── */
    // We remove the 'is_active' filter here so we can resolve names of deleted deductions on old payslips
    $allComponents = \App\Models\PayrollDeduction::orderBy('sort_order')->get();
    
    $allowanceConfig = $allComponents->filter(fn($c) => $c->entry_kind === 'addition');
    $deductionConfig = $allComponents->filter(fn($c) => $c->entry_kind === 'deduction' && $c->parent_id != 9);

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
        @page { size: letter portrait; margin: 6mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 203.9mm; height: 267.4mm; font-family: Arial, sans-serif; font-size: 6.5pt; color: #000; background: #fff; }
        .page-row { width: 203.9mm; height: 133.7mm; display: table; table-layout: fixed; border-collapse: collapse; }
        .page-col { display: table-cell; width: 101.95mm; height: 133.7mm; vertical-align: top; padding: 1.5mm; }
        .break-after { page-break-after: always; }
        .payslip-doc { width: 98.95mm; height: 130.7mm; overflow: hidden; padding: 1.8mm 2.5mm; border: 0.5pt dashed #888; }
        .ps-header { text-align: center; padding-bottom: 0.7mm; margin-bottom: 0.6mm; border-bottom: 0.4pt solid #555; }
        .ps-header p { font-size: 5.5pt; line-height: 1.3; }
        .ps-header .bold { font-weight: bold; font-size: 6pt; }
        .ps-header .title { font-weight: bold; font-size: 7pt; letter-spacing: 0.4pt; margin-top: 0.3mm; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 0.6mm; }
        .info-table td { font-size: 6pt; padding: 0.25mm 0; vertical-align: top; line-height: 1.25; }
        .i-lbl { width: 17mm; color: #444; white-space: nowrap; }
        .i-val { font-weight: bold; }
        .ps-table { width: 100%; border-collapse: collapse; }
        .ps-table td { font-size: 6pt; padding: 0.28mm 0.5pt; vertical-align: bottom; line-height: 1.2; }
        .c-lbl { width: 53%; }
        .c-ul { width: 28%; border-bottom: 0.35pt solid #444; }
        .c-amt { width: 19%; text-align: right; font-weight: 600; white-space: nowrap; }
        .row-bold td { font-weight: bold; font-size: 6.5pt; }
        .row-div td { border-top: 0.3pt solid #bbb; padding-top: 0.3mm; font-size: 0; line-height: 0; }
        .row-section td { font-size: 6pt; padding-top: 0.3mm; padding-bottom: 0.1mm; font-weight: bold; }
        .row-total td { font-weight: bold; font-size: 6.5pt; border-top: 0.7pt solid #000; padding-top: 0.7mm; }
        .row-net td { font-weight: bold; font-size: 7.5pt; border-top: 0.9pt solid #000; padding-top: 0.7mm; }
        .ps-sig { text-align: center; margin-top: 1mm; padding-top: 0.5mm; border-top: 0.4pt solid #aaa; }
        .sig-name { font-weight: bold; font-size: 6pt; text-transform: uppercase; }
        .sig-title { font-size: 5.5pt; color: #555; }
    </style>
</head>
<body>

@forelse($rows as $rowIndex => $pair)
<div class="page-row {{ (($rowIndex + 1) % 2 === 0 && $rowIndex !== $rowCount - 1) ? 'break-after' : '' }}">
    @foreach($pair as $record)
    @php
        $emp = $record->employee;

        // FIX: dynamic_deductions is cast as 'array' in PayrollRecord.
        // The model accessor always returns a PHP array — do NOT manually
        // json_decode here or you get null on double-encoded rows, causing
        // "foreach() argument must be of type array|object, string given".
        $dynData = $record->dynamic_deductions ?? [];
        if (!is_array($dynData)) $dynData = [];

        $f = fn($v) => number_format((float)$v, 2);

        // Returns the custom label stored on the record if set, otherwise $default.
        $lbl = fn(?string $field, string $default) =>
            ($field && !empty(trim($record->{$field} ?? '')))
                ? trim($record->{$field})
                : $default;

        // ── PERA / RA / TA combined allowance logic ──────────────────────
        // Collect each value
        $pera = (float)($record->allowance_pera ?? 0);
        $ra   = (float)($record->allowance_rata ?? 0);
        $ta   = (float)($record->allowance_ta   ?? 0);
        $peraRaTaTotal = $pera + $ra + $ta;

        // Build a smart label showing only parts that have a non-zero value
        $peraRaTaParts = [];
        if ($pera > 0) $peraRaTaParts[] = 'PERA';
        if ($ra   > 0) $peraRaTaParts[] = 'RA';
        if ($ta   > 0) $peraRaTaParts[] = 'TA';

        // If nothing has a value, fall back to showing "PERA/RA/TA" (but amount = 0.00)
        $peraRaTaLabel = count($peraRaTaParts) > 0
            ? implode('/', $peraRaTaParts)
            : 'PERA/RA/TA';

        // Show this combined row only when any of them is active OR has a non-zero value
        $peraRaTaActive = ($peraRaTaTotal > 0)
            || $allComponents->contains(fn($c) => in_array($c->resolveColumn(), ['allowance_pera','allowance_rata','allowance_ta']) && $c->is_active);

        // Gather all OTHER allowances — exclude anything that is PERA, RA, or TA
        // by BOTH column name and deduction name, since resolveColumn() may return null
        // for some DB configurations, causing the filter to miss them.
        $peraRaTaCols  = ['allowance_pera', 'allowance_rata', 'allowance_ta'];
        $peraRaTaNames = ['pera', 'ra', 'ta', 'allowance_pera', 'allowance_rata', 'allowance_ta'];
        $otherAllowances = $allowanceConfig->filter(function($allow) use ($peraRaTaCols, $peraRaTaNames) {
            $col  = $allow->resolveColumn();
            $name = strtolower(trim($allow->name));
            return !in_array($col, $peraRaTaCols) && !in_array($name, $peraRaTaNames);
        });
        // ─────────────────────────────────────────────────────────────────
    @endphp

    <div class="page-col">
    <div class="payslip-doc">
        <div class="ps-header">
            <p>Republic of the Philippines</p>
            <p class="bold">PROVINCE OF CAMARINES NORTE</p>
            <p>OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
            <p class="title">PAY SLIP</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="i-lbl">Name</td><td>:</td><td class="i-val">{{ strtoupper($emp->last_name . ', ' . $emp->first_name) }}</td>
                <td class="i-lbl" style="padding-left:4mm;">TIN</td><td>:</td><td class="i-val">{{ $emp->tin ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="i-lbl">Position</td><td>:</td><td class="i-val">{{ $record->designation }}</td>
                <td class="i-lbl" style="padding-left:4mm;">GSIS No.</td><td>:</td><td class="i-val">{{ $emp->gsis_id ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="i-lbl">Employee ID</td><td>:</td><td class="i-val">{{ $record->employee_id }}</td>
                <td class="i-lbl" style="padding-left:4mm;">PAG-IBIG</td><td>:</td><td class="i-val">{{ $emp->pagibig_id ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="i-lbl">Period</td><td>:</td><td class="i-val">{{ $record->period->period_label }}</td>
                <td class="i-lbl" style="padding-left:4mm;">PhilHealth</td><td>:</td><td class="i-val">{{ $emp->philhealth_id ?: 'N/A' }}</td>
            </tr>
        </table>

        <table class="ps-table">
            <tr class="row-bold">
                <td class="c-lbl">Gross Salary</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($record->gross_salary) }}</td>
            </tr>

            {{-- Combined PERA/RA/TA row — smart label shows only non-zero parts --}}
            @if($peraRaTaActive)
            <tr class="row-bold">
                <td class="c-lbl">{{ $peraRaTaLabel }}</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($peraRaTaTotal) }}</td>
            </tr>
            @endif

            {{-- Any other dynamic allowances (not pera/ra/ta) --}}
            @foreach($otherAllowances as $allow)
                @php
                    $col = $allow->resolveColumn();
                    $val = $col ? (float)($record->{$col} ?? 0) : (float)($dynData[$allow->id] ?? 0);
                @endphp
                @if($allow->is_active || $val > 0)
                <tr class="row-bold">
                    <td class="c-lbl">{{ $allow->name }}</td>
                    <td class="c-ul"></td>
                    <td class="c-amt">{{ $f($val) }}</td>
                </tr>
                @endif
            @endforeach

            <tr class="row-div"><td colspan="3"></td></tr>
            <tr class="row-section"><td colspan="3">Less Deductions</td></tr>

            {{-- Smart Dynamic Deductions --}}
            @foreach($deductionConfig as $ded)
                @php
                    $col = $ded->resolveColumn();
                    $val = $col ? (float)($record->{$col} ?? 0) : (float)($dynData[$ded->id] ?? 0);

                    // Use custom label stored on the record if available,
                    // keyed by the column name the deduction resolves to.
                    // e.g. resolveColumn() = 'gsis_ee' → label field = 'label_gsis_ee'
                    $labelField = $col ? 'label_' . $col : null;
                    $displayName = $lbl($labelField, $ded->name);
                @endphp
                @if($ded->is_active || $val > 0)
                <tr>
                    <td class="c-lbl">{{ $displayName }}</td>
                    <td class="c-ul"></td>
                    <td class="c-amt">{{ $f($val) }}</td>
                </tr>
                @endif
            @endforeach

            <tr class="row-total">
                <td class="c-lbl">TOTAL DEDUCTIONS</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($record->total_deductions) }}</td>
            </tr>
            <tr class="row-net">
                <td class="c-lbl">NET TAKE HOME PAY</td>
                <td class="c-ul"></td>
                <td class="c-amt">{{ $f($record->net_pay) }}</td>
            </tr>
        </table>

        <div class="ps-sig">
            <div class="sig-name">{{ strtoupper($record->period->sig_clerk_name ?? 'MELINDA R. BARCELONA') }}</div>
            <div class="sig-title">{{ strtoupper($record->period->sig_clerk_title ?? 'ADMINISTRATIVE OFFICER V') }}</div>
        </div>
    </div>
    </div>
    @endforeach
</div>
@empty
    <p style="padding:10mm; font-size:10pt;">No payslip records found.</p>
@endforelse

</body>
</html>