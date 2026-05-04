{{--
    resources/views/leave/pdf.blade.php
    CS Form No. 6 – Application for Leave
--}}
@php
    $appStatus      = $app->status;
    $isPrintable    = !in_array($appStatus, ['CANCELLED', 'REJECTED']);
    $noBodyPrint    = in_array($appStatus, ['CANCELLED', 'REJECTED']) ? 'no-print' : '';

    /*
    ┌──────────────────────────────────────────────────────────────────┐
    │  SIGNATORY SNAPSHOT                                              │
    │  If the application has a frozen snapshot (stored when status    │
    │  was set to APPROVED / REJECTED), use those names/titles.       │
    │  Otherwise fall back to the current signatory_options rows.      │
    └──────────────────────────────────────────────────────────────────┘
    */
    $snap    = null;
    $rawSnap = $app->signatory_snapshot ?? null;
    if ($rawSnap) {
        $snap = is_array($rawSnap) ? $rawSnap : json_decode($rawSnap, true);
    }

    $sigsAll = DB::table('signatory_options')->orderBy('sort_order')->get();

    $sigHrmo = $snap['hrmo'] ?? ($sigsAll->get(0)
        ? ['name' => strtoupper($sigsAll->get(0)->full_name), 'title' => $sigsAll->get(0)->title]
        : ['name' => 'MAGDALENA B. TOLEDANA', 'title' => 'PHRM Officer']);

    $sigProv = $snap['recommender'] ?? ($sigsAll->get(1)
        ? ['name' => strtoupper($sigsAll->get(1)->full_name), 'title' => $sigsAll->get(1)->title]
        : ['name' => 'ENGR. ALMIRANTE A. ABAD', 'title' => 'Provincial Agriculturist']);

    $sigGov  = $snap['approver'] ?? ($sigsAll->get(2)
        ? ['name' => strtoupper($sigsAll->get(2)->full_name), 'title' => $sigsAll->get(2)->title]
        : ['name' => 'ENGR. JOSEPH V. ASCUTIA', 'title' => 'Acting Governor']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application for Leave – Civil Service Form No. 6</title>
<link href="https://fonts.googleapis.com/css2?family=IM+Fell+English:ital@0;1&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root {
    --ink:       #000000;
    --soft:      #444444;
    --rule:      #000000;
    --paper:     #ffffff;
    --chk:       11px;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: #d0d0d0;
    min-height: 100vh;
    display: flex; flex-direction: column; align-items: center;
    padding: 0;
    font-family: 'Libre Baskerville', Georgia, serif;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* ── SCREEN BAR ── */
  #screenBar {
    position: sticky; top: 0; z-index: 200; width: 100%;
    background: rgba(10,10,10,0.97); backdrop-filter: blur(12px);
    padding: 9px 22px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 2px 18px rgba(0,0,0,.55);
    border-bottom: 1px solid rgba(255,255,255,.08);
    font-family: 'Libre Baskerville', Georgia, serif;
  }
  .sb-info { color: rgba(255,255,255,.5); font-size: 10.5px; line-height: 1.65; }
  .sb-info strong { color: #fff; font-weight: 700; }
  .sb-btns { display: flex; gap: 8px; }
  .sb-btn {
    padding: 6px 16px; border-radius: 6px; font-size: 11px;
    font-weight: 700; cursor: pointer; border: none;
    font-family: 'Libre Baskerville', Georgia, serif; transition: all .15s;
  }
  .sb-back { background: transparent; color: rgba(255,255,255,.6);
             border: 1.5px solid rgba(255,255,255,.2); }
  .sb-back:hover { background: rgba(255,255,255,.1); color: #fff; }
  .sb-print { background: #f0f0f0; color: #111; }
  .sb-print:hover { background: #fff; transform: translateY(-1px); }

  /* Hide print button when status is not printable */
  body.no-print .sb-print { display: none; }

  .spill {
    display: inline-block; padding: 1px 9px; border-radius: 20px;
    font-size: 9.5px; font-weight: 800; letter-spacing: .04em;
  }
  .spill-APPROVED   { background: rgba(74,222,128,.18);  color: #4ade80; }
  .spill-REJECTED   { background: rgba(248,113,113,.18); color: #f87171; }
  .spill-PENDING    { background: rgba(253,224,71,.18);  color: #fde047; }
  .spill-RECEIVED   { background: rgba(96,165,250,.18);  color: #60a5fa; }
  .spill-ON-PROCESS { background: rgba(167,139,250,.18); color: #a78bfa; }
  .spill-CANCELLED  { background: rgba(156,163,175,.18); color: #9ca3af; }

  /* ── PAGE WRAP ── */
  .page-wrap {
    display: flex; justify-content: center;
    padding: 10px 5px 40px; width: 100%;
  }

  /* ── FORM CARD ── */
  .page {
    background: var(--paper);
    width: 816px;
    padding: 8px 18px 12px;           /* ← reduced from 14px 26px 20px */
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    border: 1px solid #999;
  }

  /* ── HEADER ── */
  .form-ref-top {
    font-family: 'Courier Prime', monospace;
    font-size: 8.5px; color: var(--ink); line-height: 1.6;
    margin-bottom: 4px;               /* ← reduced from 6px */
  }
  .top-bar { display: flex; align-items: flex-start; margin-bottom: 2px; } /* ← reduced from 4px */
  .header-center {
    flex: 1; display: flex; align-items: center;
    justify-content: center; gap: 12px;
  }
  .seal-wrap {
    flex-shrink: 0; width: 72px; height: 72px;
    border-radius: 50%; overflow: hidden;
    background: #f0f0f0;
    display: flex; align-items: center; justify-content: center;
  }
  .seal-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .seal-ph { font-size: 7.5px; color: #888; text-align: center;
             padding: 4px; line-height: 1.4; font-family: 'Courier Prime',monospace; display:none; }
  .hdr-text { text-align: center; }
  .hdr-text .rep  { font-size: 11px; color: var(--ink); line-height: 1.5; }
  .hdr-text .org  { font-size: 13.5px; font-weight: bold; color: var(--ink); line-height: 1.4; }
  .hdr-text .addr { font-size: 10px; color: var(--ink); margin-top: 1px; }

  /* ── TITLE ── */
  .form-title {
    text-align: center;
    font-family: 'IM Fell English', Georgia, serif;
    font-size: 24px; letter-spacing: 0.05em;
    color: var(--ink); margin: 3px 0 3px; font-weight: bold; /* ← reduced from 5px 0 4px */
  }

  /* ── TABLES ── */
  table { width: 100%; border-collapse: collapse; }
  td, th {
    border: 1px solid var(--rule);
    font-size: 10px; color: var(--ink);
    vertical-align: top; padding: 4px 6px; line-height: 1.4; /* ← reduced from 5px 7px */
  }

  .fl {
    font-family: 'Courier Prime', monospace;
    font-size: 11px; font-weight: 700;
    color: var(--ink); display: block;
    margin-bottom: 3px; text-transform: uppercase;
  }
  .fv {
    font-family: 'Libre Baskerville', Georgia, serif;
    font-size: 10.5px; font-weight: 700; color: var(--ink);
    display: block;
  }

  .sec-hdr {
    text-align: center;
    font-family: 'Courier Prime', monospace;
    font-size: 14px; font-weight: bold;
    letter-spacing: 0.08em; padding: 4px 6px; /* ← reduced from 5px 6px */
    text-transform: uppercase;
  }

  .sub-hdr {
    font-family: 'Courier Prime', monospace;
    font-size: 13px; font-weight: 800;
    color: var(--ink); display: block;
    text-transform: uppercase;
    margin-bottom: 4px;
  }
  .sub-body { padding-left: 10px; }

  /* ── CHECKBOXES ── */
  .cr {
    display: flex; align-items: flex-start;
    gap: 4px; margin: 2px 0; line-height: 1.35;
    pointer-events: none; user-select: none;
  }
  .cb {
    display: inline-block;
    width: var(--chk); height: var(--chk);
    border: 1px solid var(--ink);
    flex-shrink: 0; margin-top: 2px;
    background: #fff; position: relative;
    -webkit-print-color-adjust: exact; print-color-adjust: exact;
  }
  .cb.on::after {
    content: '✓'; color: var(--ink);
    font-size: 12px; line-height: 1;
    display: block; text-align: center; margin-top: -0.5px;
  }
  .cl {
    font-family: 'Libre Baskerville', Georgia, serif;
    font-size: 10.5px; color: var(--ink);
  }
  .cl-ref {
    font-style: italic; font-size: 8px; color: var(--soft);
    margin-left: 2px;
  }

  .bl {
    display: inline-block;
    border-bottom: 1px solid var(--ink);
    vertical-align: bottom;
    min-width: 80px; height: 11px;
  }
  .bl-val {
    display: inline-block;
    border-bottom: 1px solid var(--ink);
    vertical-align: bottom;
    min-width: 80px; height: 11px;
    font-size: 9.5px; font-weight: 600;
    font-family: 'Libre Baskerville', Georgia, serif;
    padding: 0 2px;
  }

  /* ── SIGNATURES ── */
  .sig-blk { text-align: center; }
  .sig-name {
    font-family: 'IM Fell English', Georgia, serif;
    font-size: 13px; font-weight: bold; color: var(--ink);
    border-bottom: 1.5px solid var(--ink);
    display: inline-block; padding: 0 8px 2px;
  }
  .sig-ttl {
    font-family: 'Libre Baskerville', Georgia, serif;
    font-size: 9px; color: var(--ink); margin-top: 2px;
  }
  .sig-app {
    font-family: 'IM Fell English', Georgia, serif;
    font-size: 13px; font-weight: bold; color: var(--ink);
    text-decoration: underline; text-transform: uppercase;
    display: inline-block;
  }

  .days-num {
    font-family: 'IM Fell English', Georgia, serif;
    font-size: 19px; font-weight: bold; color: var(--ink);
    border-bottom: 1px solid var(--ink);
    display: inline-block; min-width: 40px;
    text-align: left; padding-bottom: 1px;
  }
  .dates-lbl {
    font-family: 'Courier Prime', monospace;
    font-size: 8.5px; color: var(--ink);
    text-transform: uppercase; display: block; margin-top: 2px;
  }
  .dates-val {
    font-family: 'Libre Baskerville', Georgia, serif;
    font-size: 10px; font-weight: 600; color: var(--ink);
    margin-top: 1px; line-height: 1.5; word-break: break-word;
  }

  .crt { width: 100%; border-collapse: collapse; margin-top: 3px; }
  .crt td, .crt th {
    border: 1px solid var(--ink); font-size: 9.5px;
    padding: 2px 4px; text-align: center;
  }
  .crt th {
    font-family: 'Courier Prime', monospace;
    font-size: 8.5px; font-weight: bold;
    background: #f0f0f0 !important;
    -webkit-print-color-adjust: exact; print-color-adjust: exact;
  }

  .appr {
    font-family: 'IM Fell English', Georgia, serif;
    font-size: 19px; font-weight: bold; color: var(--ink);
    border-bottom: 1.5px solid var(--ink);
    display: inline-block; min-width: 28px;
    text-align: center; margin-right: 6px; vertical-align: middle;
  }

  .rr {
    font-family: 'Libre Baskerville', Georgia, serif;
    font-size: 10px; color: var(--ink);
    line-height: 1.5; word-break: break-word;
  }

  .dln {
    display: block; width: 100%;
    border-bottom: 1px solid #999;
    min-height: 16px; margin: 5px 0;
  }

  .tint { background: #f8f8f8 !important;
          -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  /* ─────────────────────────────────────────────────────────
     PRINT RULES
  ───────────────────────────────────────────────────────── */
  @media print {
    body { background: white; padding: 0; display: block; }
    #screenBar { display: none !important; }
    .page-wrap { display: block; padding: 0; }
    .page { box-shadow: none; width: 100%; padding: 8px 12px; border: none; }
    @page { size: 8.5in 14in portrait; margin: 5mm 7mm; } /* ← reduced from 8mm 10mm */
    .cb.on::after { content: '✓'; color: #000; }
    .tint { background: #f8f8f8 !important; }
    .crt th { background: #f0f0f0 !important; }

    /* ── Block printing for CANCELLED / REJECTED ── */
    body.no-print .page-wrap { display: none !important; }
    body.no-print::after {
      content: "This application cannot be printed.\A Status: {{ $appStatus }}";
      white-space: pre;
      display: block;
      margin: 3in auto 0;
      text-align: center;
      font-family: Georgia, serif;
      font-size: 18px;
      color: #555;
    }
  }
</style>
</head>
<body class="{{ $noBodyPrint }}">

<!-- ══ SCREEN BAR ══ -->
<div id="screenBar">
  <div class="sb-info">
    <strong>Application for Leave — CS Form No. 6</strong><br>
    <span>{{ strtoupper($app->employee->last_name ?? '') }}, {{ $app->employee->first_name ?? '' }}</span>
    &nbsp;·&nbsp; Leave #{{ $app->leave_id }}
    &nbsp;·&nbsp; Status:
    <span class="spill spill-{{ $appStatus }}">{{ $appStatus }}</span>
    @if(!$isPrintable)
      &nbsp;·&nbsp; <span style="color:#f87171; font-size:10px;">⚠ Printing disabled for {{ $appStatus }} applications</span>
    @endif
    @if($snap)
      &nbsp;·&nbsp; <span style="color:#a78bfa; font-size:10px;">🔒 Signatories frozen at time of decision</span>
    @endif
  </div>
  <div class="sb-btns">
    <button class="sb-btn sb-back"  onclick="history.back()">← Back</button>
    @if($isPrintable)
      <button class="sb-btn sb-print" onclick="window.print()">🖨&nbsp; Print / Save PDF</button>
    @endif
  </div>
</div>

<div class="page-wrap">
<div class="page">

  <!-- HEADER -->
  <div class="form-ref-top">Civil Service Form No. 6<br>Revise 2020</div>
  <div class="top-bar">
    <div class="header-center">
      <div class="seal-wrap">
        <img src="{{ asset('images/kapitolyo.png') }}" alt="Seal"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
        <div class="seal-ph">📷 Seal</div>
      </div>
      <div class="hdr-text">
        <div class="rep">Republic of the Philippines</div>
        <div class="org">PROVINCIAL GOVERNMENT OF CAMARINES NORTE</div>
        <div class="addr">Provincial Capitol Bldg., Daet, Camarines Norte</div>
      </div>
    </div>
  </div>

  <div class="form-title">APPLICATION FOR LEAVE</div>

  {{--
    ══════════════════════════════════════════════════════
    SINGLE COMBINED TABLE
    4 columns via <colgroup>:
      col1 = 21%  (Office / Date of Filing)
      col2 = 17%  \  together = 31% → Position colspan=2
      col3 = 14%  /
      col4 = 48%  (Salary / right-side panels)

    Sections 6 & 7 left panel  → colspan="3" (21+17+14 = 52%)
    Sections 6 & 7 right panel → col4         (48%)
    ══════════════════════════════════════════════════════
  --}}
  <table>
    <colgroup>
      <col style="width:21%">
      <col style="width:17%">
      <col style="width:14%">
      <col style="width:48%">
    </colgroup>

    <!-- ROW 1 -->
<tr>
    <td style="padding:0px 0 0px 0px; vertical-align:top; border-right:none;">
        <div style="border-bottom:.5px solid #000; margin-bottom:1px; border-right:1px solid #000; margin-right:0px; padding-right:6px;">
            <span class="fl">1. OFFICE/DEPARTMENT</span>
        </div>
        <span class="fv" style="text-align:center; display:block; word-break:break-word; white-space:normal; font-size:9px; line-height:1.3;">{{ strtoupper($app->employee->department->department_name ?? '') }}</span>
    </td>
    <td colspan="3" style="padding:0px 0 0px 0px; vertical-align:top; border-left:none; ">
        <div style="border-bottom:.5px solid #000; margin-bottom:1px;">
            <div style="display:grid; grid-template-columns:auto 25% 25% 25% 25%; align-items:end;">
                <span class="fl" style="white-space:nowrap; padding-right:4px;">2. NAME:</span>
                <span style="font-family:'Courier Prime',monospace; font-size:8.5px; color:var(--soft); text-align:center; display:block;margin-left:-60px;">(Last)</span>
                <span style="font-family:'Courier Prime',monospace; font-size:8.5px; color:var(--soft); text-align:center; display:block;margin-left:10;">(First)</span>
                <span style="font-family:'Courier Prime',monospace; font-size:8.5px; color:var(--soft); text-align:center; display:block;margin-left:85px;">(Middle)</span>
                <span style="font-family:'Courier Prime',monospace; font-size:8.5px; color:var(--soft); text-align:center; display:block; margin-left:-29px;">(Extension)</span>
            </div>
        </div>
        <div style="display:grid; grid-template-columns:auto 22% 41% 22% 10%; align-items:center;">
            <span></span>
            <span class="fv" style="text-align:center; word-break:break-word; white-space:normal; font-size:10.5px; line-height:1.3;">{{ strtoupper($app->employee->last_name ?? '') }}</span>
            <span class="fv" style="text-align:center; word-break:break-word; white-space:normal; font-size:10.5px; line-height:1.3;">{{ strtoupper($app->employee->first_name ?? '') }}</span>
            <span class="fv" style="text-align:center; word-break:break-word; white-space:normal; font-size:10.5px; line-height:1.3;">{{ strtoupper($app->employee->middle_name ?? '') }}</span>
            <span class="fv" style="text-align:center; word-break:break-word; white-space:normal; font-size:10.5px; line-height:1.3;">{{ strtoupper($app->employee->extension_name ?? '') }}</span>
        </div>
    </td>
</tr>

<!-- ROW 2 — independent table -->
<tr>
    <td colspan="4" style="padding:0; border-bottom:none;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:4px 6px; vertical-align:top; width:30%; border:none; ">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="fl">3. DATE OF FILING</span>
                        <span class="fv">{{ $app->application_date ? $app->application_date->format('m/d/Y') : now()->format('m/d/Y') }}</span>
                    </div>
                </td>
                <td style="padding:4px 6px; vertical-align:top; width:45%; border:none;">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="fl">4. POSITION</span>
                        <span class="fv" style="word-break:break-word; white-space:normal; font-size:9px;">{{ strtoupper($app->employee->position->position_name ?? '') }}</span>
                    </div>
                </td>
                <td style="padding:4px 6px; vertical-align:top; width:25%; border:none;">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="fl">5. SALARY</span>
                        <span class="fv">{{ $app->employee->salary ? '₱' . number_format($app->employee->salary, 2) : '' }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>

    <!-- ── SECTION 6 ── -->
    <tr>
      <td colspan="4" class="sec-hdr">6.DETAILS OF APPLICATION</td>
    </tr>

    <tr>
      <!-- 6A -->
      <td colspan="3" style="vertical-align:top; padding:4px 3px;">
        <span class="sub-hdr">6.A TYPE OF LEAVE TO BE AVAILED OF</span>
        <div class="sub-body">

        @php
          $appliedTypeId = $app->leave_type_id;
          /*
          ┌──────────────────────────────────────────────────────────────┐
          │  "Others" is checked when the applied leave_type_id is NOT   │
          │  found in the $allLeaveTypes collection that was passed to   │
          │  the view (i.e. it is a custom / unlisted type).             │
          │  We also capture the type_name for display in the text field.│
          └──────────────────────────────────────────────────────────────┘
          */
          $knownTypeIds   = $allLeaveTypes->pluck('leave_type_id')->toArray();
          $isOthers       = !in_array($appliedTypeId, $knownTypeIds);
          $othersTypeName = $isOthers ? strtoupper($app->leaveType->type_name ?? '') : '';
        @endphp

        @foreach($allLeaveTypes as $lt)
        <div class="cr">
          <span class="cb {{ $appliedTypeId === $lt->leave_type_id ? 'on' : '' }}"></span>
          <span class="cl">
            <strong>{{ $lt->type_name }}</strong>
            @if(!empty($lt->legal_reference))
              <span class="cl-ref">({{ $lt->legal_reference }})</span>
            @endif
          </span>
        </div>
        @endforeach

        {{--
          ── Others (Specify) ──────────────────────────────────────────
          Shown as checked when the applied leave type is not one of the
          standard types listed in $allLeaveTypes. The type name is
          printed in the blank field so the reader can see what it is.
        --}}
        <div class="cr">
          <span class="cb {{ $isOthers ? 'on' : '' }}"></span>
          <span class="cl">
            <strong>Others (Specify)</strong>
            @if($isOthers && $othersTypeName)
              <span class="bl-val" style="min-width:140px;">{{ $othersTypeName }}</span>
            @else
              <span class="bl" style="min-width:140px;"></span>
            @endif
          </span>
        </div>

        </div>
      </td>

      <!-- 6B -->
      <td style="vertical-align:top; padding:4px 5px;">
        <span class="sub-hdr">6.B DETAILS OF LEAVE</span>
        <div class="sub-body">

        @php
          $det            = $app->details_of_leave ?? '';
          $isMonetization = !empty($app->is_monetization);   // ← dedicated DB flag
        @endphp

        @forelse($detailGroups as $group)

          <div style="font-size:8px; font-style:italic; margin-bottom:2px; margin-top:{{ $loop->first ? '0' : '4px' }};">
            {{ $group->group_name }}:
          </div>

          @forelse($group->items as $item)
          @php
            $keyword   = strtolower(trim(preg_replace('/\s*\(.*?\)/', '', $item->label)));
            $isChecked = $keyword !== '' && str_contains(strtolower($det), $keyword);

            /*
            ┌──────────────────────────────────────────────────────────────┐
            │  AUTO-CHECK "Monetization of Leave Credits"                  │
            │  If the application has is_monetization = 1 (the dedicated  │
            │  DB flag), force-check this item regardless of whether the  │
            │  keyword appears in details_of_leave.                        │
            └──────────────────────────────────────────────────────────────┘
            */
            if ($isMonetization && str_contains(strtolower($item->label), 'monetization')) {
                $isChecked = true;
            }

            $textVal = '';
            if ($item->has_text_input && $isChecked) {
                $pat     = '/' . preg_quote($keyword, '/') . '[:\s]*([^\n]+)/i';
                $textVal = preg_match($pat, $det, $m) ? trim($m[1]) : '';
            }
          @endphp
          <div class="cr">
            <span class="cb {{ $isChecked ? 'on' : '' }}"></span>
            <span class="cl">
              {{ $item->label }}
              @if($item->has_text_input)
                @if($textVal)
                  <span class="bl-val" style="min-width:160px;">{{ $textVal }}</span>
                @else
                  <span class="bl" style="min-width:160px;"></span>
                @endif
              @endif
            </span>
          </div>
          @empty
          <div style="border-bottom:1px solid var(--ink); min-height:14px; margin-bottom:5px;"></div>
          @endforelse

        @empty
        <span class="dln"></span><span class="dln"></span><span class="dln"></span>
        @endforelse

        </div>
      </td>
    </tr>

    <!-- 6C & 6D -->
    <tr>
      <td colspan="3" style="padding:4px 5px; vertical-align:top;">
        <span class="sub-hdr">6.C NUMBER OF WORKING DAYS APPLIED FOR</span>
        <div class="sub-body">

       @php
    $isMonetization = !empty($app->is_monetization);
    $nodays         = $app->no_of_days ?? 0;
    $workingDates   = ($leaveDates ?? collect())
        ->sortBy(fn($d) => $d->timestamp)
        ->values();
@endphp

<div style="margin-top:3px;">
  <span class="days-num">{{ $nodays }}</span>
  <span style="font-size:11px; font-weight:bold; margin-left:4px;">Days</span>
</div>
<span class="dates-lbl" style="font-size:15px;">INCLUSIVE DATES</span>

@if(!$isMonetization && $workingDates->isNotEmpty())
@php
    // Determine if this is a range-mode leave (includes weekends)
    // by checking if any stored date is a weekend
    $hasWeekends = $workingDates->contains(
        fn($d) => in_array($d->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])
    );

    $groups = [];
    $i      = 0;
    $all    = $workingDates->values();
    $total  = $all->count();

    while ($i < $total) {
        $groupStart = $all[$i];
        $groupEnd   = $all[$i];

        while ($i + 1 < $total) {
            // Next expected calendar day (no skipping — works for both modes)
            $next = $groupEnd->copy()->addDay();

            if ($hasWeekends) {
                // Range mode: consecutive means literally the next calendar day
                if ($next->toDateString() === $all[$i + 1]->toDateString()) {
                    $i++;
                    $groupEnd = $all[$i];
                } else {
                    break;
                }
            } else {
                // Multi mode: skip weekends when checking consecutive
                while (in_array($next->dayOfWeek, [
                    \Carbon\Carbon::SATURDAY,
                    \Carbon\Carbon::SUNDAY,
                ])) { $next->addDay(); }
                if ($next->toDateString() === $all[$i + 1]->toDateString()) {
                    $i++;
                    $groupEnd = $all[$i];
                } else {
                    break;
                }
            }
        }

        $groups[] = [$groupStart, $groupEnd];
        $i++;
    }

    $parts = []; $years = [];
    foreach ($groups as [$startDate, $endDate]) {
        $years[] = $startDate->format('Y');
        if ($endDate->format('Y') !== $startDate->format('Y')) {
            $years[] = $endDate->format('Y');
        }
        if ($startDate->toDateString() === $endDate->toDateString()) {
            $parts[] = $startDate->format('F j');
        } elseif ($startDate->format('Y') === $endDate->format('Y')
               && $startDate->format('m') === $endDate->format('m')) {
            $parts[] = $startDate->format('F j') . '–' . $endDate->format('j');
        } else {
            $parts[] = $startDate->format('F j') . ' – ' . $endDate->format('F j');
        }
    }

    $uniqueYears = array_unique($years);
    sort($uniqueYears);
    $formattedDates = implode(', ', $parts) . ', ' . implode('/', $uniqueYears);
@endphp
<div class="dates-val"><strong>{{ $formattedDates }}</strong></div>
@endif


        </div>
      </td>

      <td style="padding:4px 5px; vertical-align:top;">
        <span class="sub-hdr">6.D COMMUTATION</span>
        <div class="sub-body">

        @php 
            $commutVal = !empty($app->is_monetization) 
                ? 'REQUESTED' 
                : ($app->commutation ?? 'NOT_REQUESTED'); 
        @endphp

        @if(isset($commutationOptions) && count($commutationOptions) > 0)
          @foreach($commutationOptions as $opt)
          @php $isTicked = ($commutVal === strtoupper(str_replace([' ','-'],'_',$opt->label))); @endphp
          <div class="cr">
            <span class="cb {{ $isTicked ? 'on' : '' }}"></span>
            <span class="cl">{{ $opt->label }}</span>
          </div>
          @endforeach
        @else
          <div class="cr">
            <span class="cb {{ $commutVal === 'NOT_REQUESTED' ? 'on' : '' }}"></span>
            <span class="cl">Not Requested</span>
          </div>
          <div class="cr">
            <span class="cb {{ $commutVal === 'REQUESTED' ? 'on' : '' }}"></span>
            <span class="cl">Requested</span>
          </div>
        @endif

        @php
          $sigFull = collect([
            strtoupper($app->employee->first_name ?? ''),
            $app->employee->middle_name ? strtoupper(substr($app->employee->middle_name,0,1)).'.' : '',
            strtoupper($app->employee->last_name ?? ''),
            $app->employee->extension_name ? strtoupper($app->employee->extension_name) : '',
          ])->filter()->implode(' ');
        @endphp

        <div style="margin-top:16px;" class="sig-blk">
          <span class="sig-app">{{ $sigFull }}</span>
          <div class="sig-ttl" style="margin-top:-3px;">(Signature of Applicant)</div>
        </div>

        </div>
      </td>
    </tr>

    <!-- ── SECTION 7 ── -->
    <tr>
      <td colspan="4" class="sec-hdr">7.DETAILS OF ACTION ON APPLICATION</td>
    </tr>

    <!-- 7A & 7B -->
    <tr>
      <td colspan="3" style="vertical-align:top; padding:4px 5px;" class="tint">
        <span class="sub-hdr">7.A CERTIFICATION OF LEAVE CREDITS</span>
        <div class="sub-body">

        <div style="font-size:8.5px; margin-bottom:3px;">
          As of
          <span style="font-weight:700; font-family:'Libre Baskerville',Georgia,serif; text-decoration:underline;">
            {{ $app->application_date ? $app->application_date->format('F j, Y') : now()->format('F j, Y') }}
          </span>
        </div>

        @php
        $ltc = $app->leaveType->type_code ?? '';

        // remaining_balance is AFTER this application, so reverse-calculate earned
        $vlBase = isset($vlBalance) && $vlBalance ? $vlBalance->remaining_balance : null;
        $slBase = isset($slBalance) && $slBalance ? $slBalance->remaining_balance : null;

        // Total Earned = remaining_balance + days applied (only for the matching type)
        $vlEarned = $vlBase !== null
                    ? number_format($vlBase + ($ltc === 'VL' ? $app->no_of_days : 0), 3) : '';
        $slEarned = $slBase !== null
                    ? number_format($slBase + ($ltc === 'SL' ? $app->no_of_days : 0), 3) : '';

        // Less this application (only for the matching type)
        $vlLess = ($ltc === 'VL' && $vlBase !== null)
                  ? number_format($app->no_of_days, 3) : '';
        $slLess = ($ltc === 'SL' && $slBase !== null)
                  ? number_format($app->no_of_days, 3) : '';

        // Balance = remaining_balance as-is (already post-deduction)
        $vlBal = $vlBase !== null ? number_format($vlBase, 3) : '';
        $slBal = $slBase !== null ? number_format($slBase, 3) : '';
      @endphp

        <table class="crt">
          <tr>
            <th style="text-align:left; width:42%;"></th>
            <th>Vacation Leave</th>
            <th>Sick Leave</th>
          </tr>
          <tr>
            <td style="text-align:left; font-style:italic;">Total Earned</td>
            <td>{{ $vlEarned }}</td>
            <td>{{ $slEarned }}</td>
          </tr>
          <tr>
            <td style="text-align:left; font-style:italic;">Less this application</td>
            <td>{{ $vlLess }}</td>
            <td>{{ $slLess }}</td>
          </tr>
          <tr>
            <td style="text-align:left;"><strong>Balance</strong></td>
            <td><strong>{{ $vlBal }}</strong></td>
            <td><strong>{{ $slBal }}</strong></td>
          </tr>
        </table>
        <div style="display:flex; justify-content:space-between; margin-top:8px; gap:30px;">
    <span style="border-bottom:1px solid #000; flex:1; display:inline-block;">&nbsp;</span>
    <span style="border-bottom:1px solid #000; flex:1; display:inline-block;">&nbsp;</span>
</div>

        <div style="margin-top:40px;" class="sig-blk">
          <span class="sig-name" style="text-decoration:underline; border-bottom:none;">{{ $sigHrmo['name'] }}</span>
          <div class="sig-ttl" style="margin-top:-3px;">{{ $sigHrmo['title'] }}</div>
        </div>

        </div>
      </td>

      <!-- 7B -->
      <td style="vertical-align:top; padding:4px 5px;">
        <span class="sub-hdr">7.B RECOMMENDATION</span>
        <div class="sub-body">

        {{--
          ┌────────────────────────────────────────────────────────────┐
          │  BUG FIX: use ONLY status comparison — mutually exclusive. │
          │  position 0 → ticked only when APPROVED                   │
          │  position 1 → ticked only when REJECTED                   │
          └────────────────────────────────────────────────────────────┘
        --}}
        @if(isset($recommendationOptions) && count($recommendationOptions) > 0)
          @foreach($recommendationOptions as $idx => $opt)
          @php
            $isTicked = ($idx === 0 && $appStatus === 'APPROVED')
                     || ($idx === 1 && $appStatus === 'REJECTED');
          @endphp
          <div class="cr">
            <span class="cb {{ $isTicked ? 'on' : '' }}"></span>
            <span class="cl">{{ $opt->label }}
              @if($idx === 1) due to @endif
            </span>
          </div>
          @endforeach
        @else
          <div class="cr">
            <span class="cb {{ $appStatus === 'APPROVED' ? 'on' : '' }}"></span>
            <span class="cl">For Approval</span>
          </div>
          <div class="cr">
            <span class="cb {{ $appStatus === 'REJECTED' ? 'on' : '' }}"></span>
            <span class="cl">For Disapproval due to</span>
          </div>
        @endif

        @if(!empty($app->reject_reason))
          <span class="bl-val" style="width:100%; display:block; margin-top:4px;">{{ $app->reject_reason }}</span>
        @else
          <span class="dln"></span>
        @endif
        <span class="dln"></span>
        <span class="dln"></span>

         <div style="margin-top:57px;" class="sig-blk">
          <div class="sig-name" style="text-decoration:underline; font-weight:bold; border-bottom:none;">{{ $sigProv['name'] }}</div>
          <div class="sig-ttl" style="margin-top:-4px;">{{ $sigProv['title'] }}</div>
        </div>

        </div>
      </td>
    </tr>

<!-- 7C & 7D -->
    <tr>
      <td colspan="3" style="padding:4px 5px; vertical-align:top; border-bottom:none; border-right:none;" class="tint">
        <span class="sub-hdr">7.C APPROVED FOR</span>
        <div class="sub-body" style="margin-top:3px;">

        @php $approvedDays = ($appStatus === 'APPROVED') ? $app->no_of_days : ''; @endphp

        <div style="display:flex; align-items:center; gap:4px; margin-bottom:2px;">
          <span class="bl" style="min-width:40px;  text-align:center; display:block;" >{{ $approvedDays }}</span>
          <span class="cl">days with pay</span>
        </div>
        <div style="display:flex; align-items:baseline; gap:4px; margin-bottom:2px;">
          <span class="bl" style="min-width:40px;"></span>
          <span class="cl">days without pay</span>
        </div>
        <div style="display:flex; align-items:baseline; gap:4px;">
          <span class="bl" style="min-width:40px;"></span>
          <span class="cl">others (Specify) </span>
        </div>

        </div>
      </td>

      <td style="padding:4px 5px; vertical-align:top; border-bottom:none; border-left:none">
        <span class="sub-hdr">7.D DISAPPROVED DUE TO:</span>
        <div class="sub-body">

        @if(!empty($app->reject_reason))
          <span class="bl-val" style="width:100%; display:block; margin-top:4px;">{{ $app->reject_reason }}</span>
        @else
          <span class="dln"></span>
        @endif
        <span class="dln"></span>
        <span class="dln"></span>

        </div>
      </td>
    </tr>

    <!-- Governor / Approving Authority — from snapshot or current signatory_options -->
    <tr>
      <td colspan="4" style="text-align:center; padding:30px 8px 10px; border-top:none;" class="tint">
        <div class="sig-name" style="text-decoration:underline; font-weight:bold; border-bottom:none;">{{ $sigGov['name'] }}</div>
        <div class="sig-ttl" style="margin-top:-2px;">{{ $sigGov['title'] }}</div>
      </td>
    </tr>
  </table><!-- end single combined table -->

</div>
</div>

<script>
if (new URLSearchParams(window.location.search).get('print') === '1') {
  // Only auto-print if status allows it
  var isPrintable = !document.body.classList.contains('no-print');
  if (isPrintable) {
    window.addEventListener('load', function () { setTimeout(window.print, 600); });
  }
}
(function () {
  if (window.self !== window.top) {
    var bar  = document.getElementById('screenBar');
    var wrap = document.querySelector('.page-wrap');
    if (bar)  bar.style.display     = 'none';
    if (wrap) wrap.style.paddingTop = '10px';
  }
})();
</script>
</body>
</html> 