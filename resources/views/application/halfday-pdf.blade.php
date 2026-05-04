<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Certification — {{ $halfDay->employee->last_name ?? 'Unknown' }}, {{ $halfDay->employee->first_name ?? '' }}</title>

<style>
    @page {
        size: 8.5in 14in portrait;
        margin: 0.5in 0.75in 0.5in 0.75in;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: "Times New Roman", serif;
        font-size: 12pt;
        background: #fff;
        color: #000;
        max-width: 780px;
        margin: 0 auto;
        padding: 36px 54px;
        line-height: 1.8;
    }

    @media print {
        .print-btn { display: none; }
        body { padding: 0; max-width: 100%; margin: 0; }
    }
</style>
</head>
<body>

@php
    $employee = $halfDay->employee;

    $sealPath = public_path('images/kapitolyo.png');
    $sealSrc  = file_exists($sealPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($sealPath))
        : null;

    $middleInit   = ($employee && $employee->middle_name) ? strtoupper($employee->middle_name[0]) . '. ' : '';
    $employeeName = $employee
        ? strtoupper($employee->first_name . ' ' . $middleInit . $employee->last_name)
        : 'UNKNOWN EMPLOYEE';
    $absDate    = \Carbon\Carbon::parse($halfDay->date_of_absence);
    $issuedDate = now();
    $leaveType  = optional($halfDay->leaveType)->type_name ?? 'leave';
    $reason     = $halfDay->reason ?? '';

    // $officeHead is passed from the controller — always the current
    // Provincial Agriculturist resolved live from the employees table.
@endphp

{{-- ── Letterhead ── --}}
<div style="text-align:center; margin-bottom:24px; line-height:1.3;">
    @if($sealSrc)
        <img src="{{ $sealSrc }}" alt="Seal"
             style="width:70px;height:70px;object-fit:contain;display:block;margin:0 auto 6px;">
    @endif
    <div style="font-size:11pt;">Republic of the Philippines</div>
    <div style="font-size:13pt;font-weight:bold;letter-spacing:2px;">PROVINCE OF CAMARINES NORTE</div>
    <div>-Daet-</div>
    <div style="font-size:12pt;font-weight:bold;">OFFICE OF THE PROVINCIAL AGRICULTURIST</div>
    <div style="font-size:16pt;font-weight:bold;letter-spacing:3px;margin-top:18px;text-decoration:underline;">
        CERTIFICATION
    </div>
</div>

{{-- ── Body ── --}}
<div style="text-align:justify;">

    <p style="margin-bottom:12px;">To whom it may concern:</p>

    <p style="text-indent:60px; margin-bottom:0;">
        This is to certify that Mr./Ms.
        <u>&nbsp;&nbsp;{{ $employeeName }}&nbsp;&nbsp;</u>
        was absent on
        <u>&nbsp;&nbsp;{{ $absDate->format('F d, Y') }}&nbsp;&nbsp;</u>,
        {{ $halfDay->time_period }}
        ({{ $halfDay->time_period === 'AM' ? 'morning' : 'afternoon' }})

        @if($reason)
            for <u style="padding:0 4px;">{{ $reason }}</u>,
        @endif

        and same has been deducted from his/her
        {{ $leaveType }} credits equivalent to one-half (0.5) day.
    </p>

</div>

{{-- ── Issued ── --}}
<p style="text-indent:60px; margin-top:24px;">
    Issued this <u>&nbsp;&nbsp;{{ $issuedDate->format('d') }}&nbsp;&nbsp;</u>
    day of <u>&nbsp;&nbsp;{{ $issuedDate->format('F') }}&nbsp;&nbsp;</u>
    {{ $issuedDate->format('Y') }}, at Daet, Camarines Norte.
</p>

{{-- ── Signature ── --}}
<div style="text-align:right; margin-top:60px;">
    <div style="display:inline-block; text-align:center; line-height:1;">
        <div style="font-weight:bold; text-transform:uppercase; font-size:13pt; margin-bottom:2px;">
            {{ $officeHead->full_name ?? 'ALMIRANTE A. ABADS' }}
        </div>
        <div style="font-style:italic; font-size:12pt;">
            {{ $officeHead->title ?? 'Provincial Agriculturist' }}
        </div>
    </div>
</div>


</body>
</html>