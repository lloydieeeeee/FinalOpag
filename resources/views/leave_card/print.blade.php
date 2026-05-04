<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leave Card — {{ strtoupper($employee->last_name) }}, {{ strtoupper($employee->first_name) }} — {{ $year }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ══════════════════════════════════════════════
   FLUID SCALE TOKENS
══════════════════════════════════════════════ */
:root {
  --fs-xxs:  clamp(5.5pt, 1.1vw, 7pt);
  --fs-xs:   clamp(6.5pt, 1.3vw, 8pt);
  --fs-sm:   clamp(7pt,   1.5vw, 9.5pt);
  --fs-base: clamp(7.5pt, 1.7vw, 10pt);
  --fs-md:   clamp(8.5pt, 1.9vw, 12pt);
  --fs-lg:   clamp(10pt,  2.3vw, 14pt);
  --fs-title:clamp(10pt,  2.8vw, 13pt);
  --fs-ob:   clamp(9pt,   2.3vw, 12pt);
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: var(--fs-sm);
    color: #111;
    background: #e8ecee;
    padding: clamp(6px, 2.5vw, 20px) clamp(4px, 1.5vw, 12px) 48px;
}

/* ══════════════════════════════════════════════
   TOOLBAR
══════════════════════════════════════════════ */
.toolbar {
    max-width: 1100px; margin: 0 auto 12px;
    background: #fff; border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,.10); border: 1px solid #e5e7eb;
    padding: 8px 12px;
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 8px; flex-wrap: wrap;
}
.toolbar-left  { display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1 1 180px; }
.toolbar-icon  {
    width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
    background: linear-gradient(135deg,#1a3a1a,#2d5a1b);
    display: flex; align-items: center; justify-content: center;
}
.toolbar-title { font-size: clamp(10px, 1.8vw, 13px); font-weight: 700; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.toolbar-sub   { font-size: clamp(8px, 1.4vw, 11px); color: #9ca3af; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.toolbar-actions { display: flex; gap: 6px; flex-shrink: 0; }
.btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: clamp(5px,1.2vw,7px) clamp(8px,2vw,14px);
    border-radius: 8px; font-size: clamp(10px,1.6vw,12px);
    font-weight: 700; cursor: pointer; border: none; transition: all .15s;
    white-space: nowrap;
}
.btn-primary   { background: linear-gradient(135deg,#1a3a1a,#2d5a1b); color: #fff; }
.btn-primary:hover { opacity: .88; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1.5px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }

/* ══════════════════════════════════════════════
   PAPER
══════════════════════════════════════════════ */
.paper {
    max-width: 1100px; margin: 0 auto;
    background: #fff; border: 1px solid #bbb;
    box-shadow: 0 4px 24px rgba(0,0,0,.13);
    border-radius: 2px;
}

/* ══════════════════════════════════════════════
   LETTERHEAD
══════════════════════════════════════════════ */
.doc-letterhead {
    display: flex; align-items: center; justify-content: center;
    gap: clamp(6px, 1.5vw, 14px);
    padding: clamp(6px, 1.5vw, 10px) clamp(10px, 2.5vw, 20px) clamp(5px, 1vw, 8px);
    border-bottom: 2.5px solid #111;
}
.doc-seal {
    flex-shrink: 0;
    width: clamp(42px, 7vw, 68px);
    height: clamp(42px, 7vw, 68px);
    border-radius: 50%; overflow: hidden;
    background: #f0f0f0;
    display: flex; align-items: center; justify-content: center;
}
.doc-seal img { width: 100%; height: 100%; object-fit: cover; display: block; }
.doc-org { text-align: center; min-width: 0; }
.doc-republic  { font-size: clamp(7pt, 1.3vw, 9pt); color: #555; margin-bottom: 1px; }
.doc-org-name  { font-size: clamp(9.5pt, 2.2vw, 14pt); font-weight: 900; letter-spacing: .02em; color: #111; line-height: 1.1; }
.doc-org-sub   { font-size: clamp(6.5pt, 1.2vw, 8.5pt); color: #555; margin-top: 2px; }

.doc-title { text-align: center; padding: clamp(4px, 1vw, 7px) 12px; border-bottom: 2px solid #111; }
.doc-title h1 {
    font-size: var(--fs-title); font-weight: 900;
    text-transform: uppercase; letter-spacing: .04em; color: #111; line-height: 1;
}

/* ══════════════════════════════════════════════
   INFO TABLE
══════════════════════════════════════════════ */
.info-table { width: 100%; border-collapse: collapse; }
.info-table td { border: 1px solid #999; padding: clamp(3px,.8vw,4px) clamp(5px,1.2vw,8px); vertical-align: top; }
.fl { font-size: var(--fs-xxs); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #555; display: block; margin-bottom: 2px; }
.fv { font-size: var(--fs-xs); font-weight: 700; color: #111; text-transform: uppercase; display: block; }

/* ══════════════════════════════════════════════
   OPENING BALANCE
══════════════════════════════════════════════ */
.opening-band {
    display: flex; align-items: center; gap: clamp(6px,1.5vw,18px);
    padding: clamp(4px,1vw,5px) clamp(8px,2vw,10px);
    background: #f7fdf7; border-bottom: 1px solid #999;
    flex-wrap: wrap; row-gap: 3px;
}
.ob-lbl  { font-size: var(--fs-xxs); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #555; }
.ob-item { display: flex; align-items: baseline; gap: 4px; }
.ob-type { font-size: var(--fs-xs); font-weight: 700; color: #555; text-transform: uppercase; }
.ob-val  { font-size: var(--fs-ob); font-weight: 900; color: #1a3a1a; font-family: 'Courier New', monospace; text-decoration: underline; }
.ob-div  { color: #ccc; }

/* ══════════════════════════════════════════════
   SCROLL WRAPPER + FADE HINT
══════════════════════════════════════════════ */
.ledger-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    position: relative;
}

/* Right-edge fade — shows there's more to scroll */
.ledger-wrap::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0;
    width: 28px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,.07));
    pointer-events: none;
    transition: opacity .2s;
}
.ledger-wrap.scrolled-end::after { opacity: 0; }


/* ══════════════════════════════════════════════
   LEDGER TABLE
══════════════════════════════════════════════ */
.ledger { width: 100%; border-collapse: collapse; font-size: var(--fs-xs); min-width: 860px; }

.ledger thead tr.hdr-top th {
    border: 1px solid #888; padding: clamp(3px,.7vw,5px);
    text-align: center; font-size: var(--fs-xxs); font-weight: 900;
    text-transform: uppercase; letter-spacing: .03em;
    background: #f0f0f0; color: #111; vertical-align: middle;
}
.ledger thead tr.hdr-top th.al { text-align: left; padding-left: clamp(4px,1vw,8px); }
.ledger thead tr.hdr-sub th {
    border: 1px solid #888; padding: clamp(2px,.5vw,3px) clamp(2px,.6vw,5px);
    text-align: center; font-size: var(--fs-xxs); font-weight: 700;
    background: #e5e5e5; color: #333;
    text-transform: uppercase; letter-spacing: .03em;
}

/* Colour bands */
.th-e  { background: #dbeafe !important; color: #1e40af !important; }
.th-t  { background: #dcfce7 !important; color: #166534 !important; }
.th-w  { background: #fef9c3 !important; color: #854d0e !important; }
.th-ta { background: #fee2e2 !important; color: #991b1b !important; }
.th-b  { background: #f0fdf4 !important; color: #166534 !important; }



/* ── BODY CELLS ── */
.ledger tbody td {
    border: 1px solid #bbb; padding: clamp(2px,.5vw,3px) clamp(3px,.7vw,5px);
    vertical-align: middle; text-align: center; color: #111;
    font-size: var(--fs-xs); height: clamp(18px,2.5vw,22px);
}
.ledger tbody td.al  { text-align: left; padding-left: clamp(4px,1vw,8px); }
.ledger tbody td.idx { color: #888; font-size: var(--fs-xxs); }
.ledger tbody td.mn  { font-family: 'Courier New', monospace; text-align: right; padding-right: clamp(3px,.7vw,6px); }
.ledger tbody td.ce  { background: #f0f7ff; }
.ledger tbody td.ct  { background: #f0fdf4; }
.ledger tbody td.cw  { background: #fffde7; }
.ledger tbody td.cta { background: #fff5f5; }
.ledger tbody td.bvl,
.ledger tbody td.bsl {
    background: #f0fdf4; color: #166534; font-weight: 700;
    font-family: 'Courier New', monospace; text-align: right; padding-right: clamp(4px,1vw,7px);
}
.ledger tbody td.bvl.neg, .ledger tbody td.bsl.neg { background: #fef2f2; color: #991b1b; }
.ledger tbody td.erow { height: clamp(18px,2.5vw,22px); }

/* ── HALF-DAY ROW ── */
.ledger tbody tr.hd-row td     { background: #faf5ff; }
.ledger tbody tr.hd-row td.bvl { background: #f3e8ff; color: #7e22ce; }
.ledger tbody tr.hd-row td.bsl { background: #f3e8ff; color: #7e22ce; }
.ledger tbody tr.hd-row td.bvl.neg,
.ledger tbody tr.hd-row td.bsl.neg { background: #fef2f2; color: #991b1b; }

/* ══════════════════════════════════════════════
   RESPONSIVE — Small screens (≤ 600px)
══════════════════════════════════════════════ */
@media (max-width: 600px) {


    /* Letterhead: tighter */
    .doc-org-name { font-size: clamp(8.5pt, 3.8vw, 11pt); }
    .doc-org-sub  { display: none; }
    .doc-republic { font-size: 7pt; }
    .doc-seal     { width: clamp(36px, 9vw, 52px); height: clamp(36px, 9vw, 52px); }

    /* Title */
    .doc-title h1 { font-size: clamp(8.5pt, 3.2vw, 11pt); }

    /* Info table: stack rows */
    .info-table { display: block; }
    .info-table tbody, .info-table tr { display: block; width: 100%; }
    .info-table td {
        display: block; width: 100% !important;
        border-bottom: none;
    }
    .info-table tr:last-child td:last-child { border-bottom: 1px solid #999; }

    /* Opening balance: wrap items */
    .opening-band { flex-direction: column; align-items: flex-start; gap: 2px; padding: 5px 8px; }
    .ob-div { display: none; }

    /* Toolbar */
    .toolbar { padding: 8px; gap: 6px; }
    .toolbar-left { flex: 1 1 100%; }
    .toolbar-actions { width: 100%; }
    .toolbar-actions .btn { flex: 1; justify-content: center; font-size: 10px; }

    /* Full table scrolls horizontally on mobile */
    .ledger { min-width: 860px; }
}

/* ══════════════════════════════════════════════
   Tablet (601–900px)
══════════════════════════════════════════════ */
@media (min-width: 601px) and (max-width: 900px) {
    .ledger { min-width: 860px; }
    .doc-org-name { font-size: clamp(10pt, 2.5vw, 13pt); }
}

/* ══════════════════════════════════════════════
   PRINT — A4 landscape
══════════════════════════════════════════════ */
@media print {
    body  { background: #fff; padding: 0; }
    .toolbar { display: none !important; }
    .paper { box-shadow: none; border: none; max-width: none; margin: 0; border-radius: 0; }
    @page  { size: A4 landscape; margin: 7mm 9mm; }
    .page-break { page-break-before: always; }

    :root {
        --fs-xxs:   6.5pt;
        --fs-xs:    7.5pt;
        --fs-sm:    8.5pt;
        --fs-base:  8.5pt;
        --fs-md:    10pt;
        --fs-lg:    12pt;
        --fs-title: 13pt;
        --fs-ob:    12pt;
    }

    .ledger-wrap::after { display: none; }

    .ledger { min-width: unset; width: 100%; }
    .ledger-wrap { overflow: visible; }

    /* Restore info table */
    .info-table, .info-table tbody, .info-table tr, .info-table td {
        display: table-cell !important; width: auto !important;
    }
    .info-table { display: table !important; }
    .info-table tbody { display: table-row-group !important; }
    .info-table tr { display: table-row !important; }

    /* Print colors */
    .ledger tbody tr.hd-row td     { background: #faf5ff !important; }
    .ledger tbody tr.hd-row td.bvl { background: #f3e8ff !important; color: #7e22ce !important; }
    .ledger tbody tr.hd-row td.bsl { background: #f3e8ff !important; color: #7e22ce !important; }
    .ledger tbody tr.hd-row td.bvl.neg,
    .ledger tbody tr.hd-row td.bsl.neg { background: #fef2f2 !important; color: #991b1b !important; }
    .ledger tbody td.ce { background: #f0f7ff !important; }
    .ledger tbody td.ct { background: #f0fdf4 !important; }
    .ledger tbody td.cw { background: #fffde7 !important; }
    .ledger tbody td.cta { background: #fff5f5 !important; }
}
</style>
</head>
<body>

@php
    use Carbon\Carbon;

    $MONTHS = [
        1=>'January',  2=>'February', 3=>'March',     4=>'April',
        5=>'May',      6=>'June',     7=>'July',       8=>'August',
        9=>'September',10=>'October', 11=>'November',  12=>'December',
    ];

    $fmtN  = fn($v) => ($v !== null && $v !== '') ? number_format((float)$v, 3) : '—';
    $isNeg = fn($v) => ($v !== null && $v !== '') && (float)$v < 0;

    $openVl = $card ? (float)$card->opening_vl : 0;
    $openSl = $card ? (float)$card->opening_sl : 0;

    $isSeparator = function($e) {
        if (!empty($e->is_separator)) return true;
        $p = $e->date_particulars ?? '';
        return str_contains($p, '--- MONTH SEPARATOR ---')
            || str_contains($p, '---MONTH SEPARATOR---');
    };

    $isExcluded = fn($e) => str_contains($e->date_particulars ?? '', '--- EXCLUDED ---');

    $isHalfDayEntry = function($e) {
        $p = strtolower($e->date_particulars ?? '');
        return str_contains($p, 'am half day')
            || str_contains($p, 'pm half day')
            || str_contains($p, '(half day)');
    };

    $tevl=0; $tesl=0; $ttvl=0; $ttsl=0; $twop=0; $ttar=0;
    $lVl=null; $lSl=null;
    foreach ($entries as $e) {
        if ($isSeparator($e) || $isExcluded($e)) continue;
        $tevl += (float)($e->earned_vl      ?? 0);
        $tesl += (float)($e->earned_sl      ?? 0);
        $ttvl += (float)($e->taken_vl       ?? 0);
        $ttsl += (float)($e->taken_sl       ?? 0);
        $twop += (float)($e->leave_wop      ?? 0);
        $ttar += (float)($e->tardy_undertime ?? 0);
        if ($e->balance_vl !== null && $e->balance_vl !== '') $lVl = (float)$e->balance_vl;
        if ($e->balance_sl !== null && $e->balance_sl !== '') $lSl = (float)$e->balance_sl;
    }
    $fVl = $lVl ?? ($openVl + $tevl - $ttvl - $twop - $ttar);
    $fSl = $lSl ?? ($openSl + $tesl - $ttsl);

    $lname = strtoupper($employee->last_name      ?? '');
    $fname = strtoupper($employee->first_name     ?? '');
    $mname = strtoupper($employee->middle_name    ?? '');
    $ename = strtoupper($employee->extension_name ?? '');
    $pos   = strtoupper($employee->position->position_name ?? '—');
    $empId = $employee->formatted_employee_id ?? $employee->employee_id;
@endphp

{{-- ══ TOOLBAR ══ --}}
<div class="toolbar">
    <div class="toolbar-left">
        <div class="toolbar-icon">
            <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div style="min-width:0;">
            <div class="toolbar-title">Record of Leave of Absence — {{ $year }}</div>
            <div class="toolbar-sub">
                {{ $lname }}, {{ $fname }} &nbsp;·&nbsp; {{ $pos }} &nbsp;·&nbsp; ID: {{ $empId }}
            </div>
        </div>
    </div>
    <div class="toolbar-actions">
        <button class="btn btn-secondary" onclick="window.close()">✕ Close</button>
        <button class="btn btn-primary" onclick="window.print()">
            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save PDF
        </button>
    </div>
</div>

{{-- ══ PAPER ══ --}}
<div class="paper">

    <div class="doc-letterhead">
        <div class="doc-seal">
            <img src="{{ asset('images/kapitolyo.png') }}" alt="Seal"
                 onerror="this.style.display='none';">
        </div>
        <div class="doc-org">
            <div class="doc-republic">Republic of the Philippines</div>
            <div class="doc-org-name">PROVINCIAL GOVERNMENT OF CAMARINES NORTE</div>
            <div class="doc-org-sub">Provincial Capitol Bldg., Daet, Camarines Norte</div>
        </div>
    </div>

    <div class="doc-title" style="margin-bottom:8px;">
        <h1>Record of Leave of Absence</h1>
    </div>

    <table class="info-table">
        <tbody>
        <tr>
            <td colspan="3">
                <div style="display:flex;gap:0;">
                    <span class="fl" style="white-space:nowrap;padding-right:5px;flex-shrink:0;align-self:flex-start;">1. Name:</span>
                    <div style="flex:1;display:flex;flex-direction:column;min-width:0;">
                        <div style="display:flex;gap:0;flex-wrap:wrap;">
                            <span style="flex:1.2;min-width:55px;font-weight:400;font-size:var(--fs-xxs);color:#555;text-align:center;">(Last Name)</span>
                            <span style="flex:1;min-width:45px;font-weight:400;font-size:var(--fs-xxs);color:#555;text-align:center;">(First Name)</span>
                            <span style="flex:.8;min-width:38px;font-weight:400;font-size:var(--fs-xxs);color:#555;text-align:center;">(Middle Name)</span>
                            <span style="flex:.2;min-width:22px;font-weight:400;font-size:var(--fs-xxs);color:#555;text-align:center;">(Ext.)</span>
                        </div>
                        <div style="display:flex;gap:0;margin-top:1px;flex-wrap:wrap;">
                            <span class="fv" style="flex:1.2;min-width:55px;padding:1px 4px;text-align:center;word-break:break-word;">{{ $lname }}</span>
                            <span class="fv" style="flex:1;min-width:45px;padding:1px 4px;text-align:center;word-break:break-word;">{{ $fname }}</span>
                            <span class="fv" style="flex:.8;min-width:38px;padding:1px 4px;text-align:center;word-break:break-word;">{{ $mname }}</span>
                            <span class="fv" style="flex:.2;min-width:22px;padding:1px 4px;text-align:center;">{{ $ename }}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td style="width:30%;">
                <span class="fl">2. Office / Department</span>
                <span class="fv" style="word-break:break-word;">Office of the Provincial Agriculturist</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:50%;">
                <span class="fl">3. Calendar Year</span>
                <span class="fv" style="margin-left:clamp(8px,4vw,40px);">{{ $year }}</span>
            </td>
            <td colspan="2" style="width:50%;">
                <span class="fl">4. Position / Designation</span>
                <span class="fv" style="margin-left:clamp(8px,4vw,40px);word-break:break-word;">{{ $pos }}</span>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="opening-band">
        <span class="ob-lbl">Opening Balance (As Carried Forward):</span>
        <span class="ob-item">
            <span class="ob-type">Vacation Leave</span>
            <span class="ob-val">{{ number_format($openVl, 3) }}</span>
        </span>
        <span class="ob-div">|</span>
        <span class="ob-item">
            <span class="ob-type">Sick Leave</span>
            <span class="ob-val">{{ number_format($openSl, 3) }}</span>
        </span>
    </div>


    <div class="ledger-wrap" id="ledgerWrap">
        <table class="ledger">
            <thead>
                {{--
                    Column order (13 total):
                    1:#  2:Month  3:Date/Particulars
                    4:EarnedVL  5:EarnedSL   ← hide on sm
                    6:TakenVL   7:TakenSL    ← hide on sm
                    8:W/O Pay                ← hide on sm
                    9:Tardy/UT               ← hide on sm+md
                    10:VL Bal   11:SL Bal
                    12:Remarks               ← hide on sm+md
                    13:Status
                --}}
                <tr class="hdr-top">
                    <th rowspan="2" class="" style="width:22px;">#</th>
                    <th rowspan="2" class="" style="width:clamp(44px,7vw,64px);">Month</th>
                    <th rowspan="2" class="al" style="min-width:clamp(120px,18vw,200px);">Date / Particulars</th>
                    <th colspan="2" class="th-e">Leave Credits Earned</th>
                    <th colspan="2" class="th-t">Leave Taken (With Pay)</th>
                    <th rowspan="2" class="th-w" style="width:clamp(34px,4.5vw,52px);">Leave<br>W/O Pay</th>
                    <th rowspan="2" class="th-ta" style="width:clamp(38px,5vw,56px);">Tardy /<br>Undertime</th>
                    <th colspan="2" class="th-b">Balance of Leave Credits</th>
                    <th rowspan="2" class="al" style="min-width:clamp(64px,9vw,105px);">Remarks</th>
                    <th rowspan="2" class="al" style="min-width:clamp(42px,5.5vw,62px);">Status</th>
                </tr>
                <tr class="hdr-sub">

                    <th class="th-e">Vacation<br>Leave</th>
                    <th class="th-e">Sick<br>Leave</th>
                    <th class="th-t">Vacation<br>Leave</th>
                    <th class="th-t">Sick<br>Leave</th>
                    <th class="th-b">VL Balance</th>
                    <th class="th-b">SL Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNum = 0; @endphp

                @forelse($entries as $entry)

                    @if($isSeparator($entry) || $isExcluded($entry))
                        {{-- Skip separators and excluded entries --}}
                        {{-- Skip excluded --}}

                    @else
                        @php
                            $rowNum++;
                            $isHd = $isHalfDayEntry($entry);
                            $bvl = ($entry->balance_vl !== null && $entry->balance_vl !== '')
                                   ? number_format((float)$entry->balance_vl, 3) : '—';
                            $bsl = ($entry->balance_sl !== null && $entry->balance_sl !== '')
                                   ? number_format((float)$entry->balance_sl, 3) : '—';
                        @endphp
                        <tr class="{{ $isHd ? 'hd-row' : '' }}">
                            <td class="idx">{{ $rowNum }}</td>
                            <td class="" style="font-size:var(--fs-xxs);text-align:center;">
                                {{ isset($entry->month) && $entry->month ? ($MONTHS[(int)$entry->month] ?? '') : '' }}
                            </td>
                            <td class="al" style="font-size:var(--fs-xs);">{{ $entry->date_particulars }}</td>
                            <td class="mn ce">{{ $fmtN($entry->earned_vl) }}</td>
                            <td class="mn ce">{{ $fmtN($entry->earned_sl) }}</td>
                            <td class="mn ct">{{ $fmtN($entry->taken_vl)  }}</td>
                            <td class="mn ct">{{ $fmtN($entry->taken_sl)  }}</td>
                            <td class="mn cw">{{ $fmtN($entry->leave_wop) }}</td>
                            <td class="mn cta">{{ $fmtN($entry->tardy_undertime) }}</td>
                            <td class="bvl {{ $isNeg($entry->balance_vl) ? 'neg' : '' }}">{{ $bvl }}</td>
                            <td class="bsl {{ $isNeg($entry->balance_sl) ? 'neg' : '' }}">{{ $bsl }}</td>
                            <td class="al" style="font-size:var(--fs-xxs);">{{ $entry->remarks }}</td>
                            <td class="al" style="font-size:var(--fs-xxs);">{{ $entry->status }}</td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="13" style="padding:32px;text-align:center;color:#9ca3af;font-style:italic;">
                            No leave card entries recorded for {{ $year }}.
                        </td>
                    </tr>
                @endforelse

                @if($rowNum < 4)
                    @for($p = 0; $p < (5 - $rowNum); $p++)
                        <tr>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                            <td class="erow"></td>
                        </tr>
                    @endfor
                @endif
            </tbody>

            <tbody>
                <tr style="background:#f0fdf4;border:1.5px solid #000;">
                    <td class="" style="border:1px solid #999;background:#f0fdf4;"></td>
                    <td class="" style="border:1px solid #999;background:#f0fdf4;"></td>
                    <td class="al" style="border:1px solid #999;background:#f0fdf4;font-size:var(--fs-xs);font-weight:900;
                                          text-transform:uppercase;letter-spacing:.05em;
                                          color:#1a3a1a;padding:6px 10px;">
                        Closing Balance for {{ $year }}
                    </td>
                    <td style="border:1px solid #999;background:#f0f7ff;"></td>
                    <td style="border:1px solid #999;background:#f0f7ff;"></td>
                    <td style="border:1px solid #999;background:#f0fdf4;"></td>
                    <td style="border:1px solid #999;background:#f0fdf4;"></td>
                    <td style="border:1px solid #999;background:#fffde7;"></td>
                    <td style="border:1px solid #999;background:#fff5f5;"></td>
                    <td class="bvl {{ $isNeg($lVl ?? $fVl) ? 'neg' : '' }}"
                        style="border:1px solid #999;font-size:var(--fs-md);font-weight:900;text-align:right;color:#991b1b;">
                        {{ $lVl !== null ? number_format($lVl, 3) : number_format($fVl, 3) }}
                    </td>
                    <td class="bsl {{ $isNeg($lSl ?? $fSl) ? 'neg' : '' }}"
                        style="border:1px solid #999;font-size:var(--fs-md);font-weight:900;text-align:right;color:#166534;">
                        {{ $lSl !== null ? number_format($lSl, 3) : number_format($fSl, 3) }}
                    </td>
                    <td style="border:1px solid #999;"></td>
                    <td style="border:1px solid #999;"></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>{{-- end .paper --}}

<script>
(function() {
    var wrap = document.getElementById('ledgerWrap');
    if (!wrap) return;
    wrap.addEventListener('scroll', function() {
        var atEnd = wrap.scrollLeft + wrap.clientWidth >= wrap.scrollWidth - 4;
        wrap.classList.toggle('scrolled-end', atEnd);
    });
})();
</script>
</body>
</html>