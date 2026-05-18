<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provincial Payroll — {{ $period->period_label }}</title>
<style>
  @page {
    size: 16.5in 11.7in landscape;
    margin: 0;
  }

  html, body {
    width: 16.5in;
    height: 11.7in;
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: Arial, sans-serif;
    font-size: 5pt;
    color: #000;
    background: #fff;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  #page-wrap {
    width: 16.26in;
    margin: 0.12in 0.12in 0 0.12in;
    box-sizing: border-box;
  }

  /* ═══ HEADER ═══ */
  .header-wrap {
    width: 100%;
    display: flex;
    align-items: flex-start;
    margin-bottom: 1px;
    gap: 3px;
  }
  .header-left {
    width: 120px;
    flex-shrink: 0;
    font-size: 5pt;
    line-height: 1.25;
  }
  .header-left strong {
    display: block;
    font-size: 5pt;
    font-weight: bold;
    margin-top: 1px;
  }
  .header-center {
    flex: 1;
    text-align: center;
  }
  .header-center .title {
    font-size: 12pt;
    font-weight: bold;
    letter-spacing: 0.5px;
    line-height: 1;
  }
  .header-center .ack {
    font-size: 4.5pt;
    line-height: 1.25;
    margin-top: 2px;
  }
  .period-row {
    text-align: center;
    font-size: 6pt;
    font-weight: bold;
    margin: 1px 0 2px;
  }

  /* ═══ MAIN TABLE ═══ */
  table.pr {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: 4pt;
    line-height: 1;
  }
  .pr th, .pr td {
    border: 0.4px solid #000;
    padding: 0.5px 0.5px;
    vertical-align: middle;
    overflow: hidden;
    word-break: break-word;
  }
  .pr th {
    text-align: center;
    font-weight: bold;
    line-height: 1.0;
    font-size: 3.8pt;
    background-color: #fff;
  }
  .pr td.r { text-align: right; }
  .pr td.c { text-align: center; }
  .pr td.l { text-align: left; padding-left: 1px; }

  /* ── Group header colours ── */
  .th-ded {
    background-color: #ffffff !important;
    color: #000;
    font-weight: bold;
    font-size: 4.5pt;
    letter-spacing: 2px;
  }
  .th-gsis,    .pr th.th-gsis    { background-color: #f4a840 !important; color: #000 !important; }
  .th-pagibig, .pr th.th-pagibig { background-color: #5fbebe !important; color: #000 !important; }
  .th-ph,      .pr th.th-ph      { background-color: #ffff00 !important; color: #000 !important; }
  .th-cng,     .pr th.th-cng     { background-color: #800000 !important; color: #fff !important; }

  .totals-row td {
    border-top: 1.2px solid #000 !important;
    font-weight: bold;
    font-size: 3.8pt;
  }

  /* ═══ FOOTER ═══ */
  .footer-outer {
    width: 100%;
    margin-top: 2px;
  }

  /* Strip 1 — no outer borders on cells by default */
  table.ft1 {
    width: 100%;
    border-collapse: collapse;
    font-size: 4pt;
    margin-bottom: 1px;
    table-layout: fixed;
  }
  table.ft1 > tbody > tr > td {
    border: none;
    vertical-align: top;
    padding: 1px 2px 1px 0;
    line-height: 1.3;
  }

  /* Strip 2 — all cells fully bordered */
  table.ft2 {
    width: 100%;
    border-collapse: collapse;
    font-size: 4pt;
    table-layout: fixed;
  }
  table.ft2 td {
    border: 0.7px solid #000;
    vertical-align: top;
    padding: 2px 3px;
    line-height: 1.2;
  }

  .sig-lbl  { font-weight: bold; font-size: 4pt; }
  .sig-body { font-size: 3.8pt; text-align: justify; line-height: 1.2; margin-top: 1px; }
  .sig-nm {
    margin-top: 12px;
    border-top: 0.7px solid #000;
    font-weight: bold;
    font-size: 5pt;
    text-align: center;
    padding-top: 1px;
  }
  .sig-rl { font-size: 3.8pt; text-align: center; }

  @media print {
    html, body { overflow: hidden; }
  }
</style>
</head>
<body>

@php
  use Illuminate\Support\Facades\DB;
  $clerk   = DB::table('signatory_options')->where('label','Payroll Clerk')->first();
  $phrmo   = DB::table('signatory_options')->where('label','PHRMO')->first();
  $pa      = DB::table('signatory_options')->where('label','Provincial Agriculturist')->first();
  $gov     = DB::table('signatory_options')->where('label','Governor')->first();
  $treas   = DB::table('signatory_options')->where('label','Provincial Treasurer')->first();
  $acct    = DB::table('signatory_options')->where('label','Provincial Accountant')->first();
  $auditor = DB::table('signatory_options')->where('label','State Auditor')->first();

  $clerkName   = strtoupper($period->sig_clerk_name  ?: ($clerk->full_name   ?? ''));
  $clerkTitle  = $period->sig_clerk_title             ?: ($clerk->title       ?? '');
  $phrmoName   = strtoupper($phrmo->full_name   ?? ''); $phrmoTitle  = $phrmo->title   ?? '';
  $paName      = strtoupper($pa->full_name      ?? ''); $paTitle     = $pa->title      ?? '';
  $govName     = strtoupper($gov->full_name     ?? ''); $govTitle    = $gov->title     ?? '';
  $acctName    = strtoupper($acct->full_name    ?? ''); $acctTitle   = $acct->title    ?? '';
  $auditorName = strtoupper($auditor->full_name ?? ''); $auditorTitle= $auditor->title ?? '';
  $treasName   = strtoupper($treas->full_name   ?? '');
  $treasTitle  = $treas->title ?? '';
  $icoLine     = $treasName ? $treasName.', '.$treasTitle : '______________________';
@endphp

<div id="page-wrap">

  {{-- ══ PAGE HEADER ══ --}}
  <div class="header-wrap">
    <div class="header-left">
      Republic of the Philippines<br>
      Province of Camarines Norte<br>
      Daet
      <strong>OFFICE OF THE PROVINCIAL AGRICULTURIST</strong>
    </div>
    <div class="header-center">
      <div class="title">PROVINCIAL PAYROLL</div>
      <div class="ack">
        We hereby acknowledge to have received from <u>&nbsp;{{ $icoLine }}&nbsp;</u>, ICO of Camarines Norte, the sums herein specified opposite our respective
        names, the same being full compensation for our services rendered during the period stated below, to the correctness of which we hereby severally certify.
      </div>
    </div>
  </div>
  <div class="period-row">FOR THE PERIOD OF: {{ strtoupper($period->period_label) }}</div>

  <table class="pr">
    <colgroup>
      <col style="width:8px">
      <col style="width:62px">
      <col style="width:16px">
      <col style="width:18px">
      <col style="width:26px">
      <col style="width:16px">
      <col style="width:16px">
      <col style="width:9px">
      <col style="width:16px">
      <col style="width:14px">
      <col style="width:16px">
      <col style="width:16px">
      <col style="width:16px">
      <col style="width:13px">
      <col style="width:16px">
      <col style="width:14px">
      <col style="width:15px">
      <col style="width:15px">
      <col style="width:16px">
      <col style="width:16px">
      <col style="width:17px">
      <col style="width:15px">
      <col style="width:15px">
      <col style="width:16px">
      <col style="width:14px">
      <col style="width:14px">
      <col style="width:20px">
      <col style="width:17px">
      <col style="width:17px">
      <col style="width:17px">
      <col style="width:30px">
      <col style="width:16px">
    </colgroup>

    <thead>
      <tr>
        <th rowspan="4">No.</th>
        <th rowspan="4">NAME<br>EMPLOYEE</th>
        <th rowspan="4">DESIG-<br>NA-<br>TION</th>
        <th rowspan="4">CODE</th>
        <th rowspan="4">SALARY</th>
        <th colspan="21" class="th-ded">D &nbsp; E &nbsp; D &nbsp; U &nbsp; C &nbsp; T &nbsp; I &nbsp; O &nbsp; N &nbsp; S</th>
        <th colspan="4" class="th-cng">C N G W M P C</th>
        <th rowspan="4">Amount<br>Paid in<br>Cash<br>(Cr. A-1)</th>
        <th rowspan="4" style="writing-mode:vertical-rl; text-orientation:mixed; white-space:nowrap; font-size:3.8pt; padding:2px;">Number</th>
      </tr>
      <tr>
        <th colspan="11" class="th-gsis">G &nbsp; S &nbsp; I &nbsp; S</th>
        <th colspan="4"  class="th-pagibig">PAG-IBIG</th>
        <th rowspan="3"  style="font-size:3.2pt;">Over-<br>payment<br>{{ $period->overpayment_label ?? '' }}</th>
        <th colspan="2"  class="th-ph">Philhealth</th>
        <th rowspan="3">With-<br>holding<br>Tax</th>
        <th rowspan="3">DBP<br>Loan</th>
        <th rowspan="3">LBP<br>Loan</th>
        <th rowspan="3" class="th-cng">CNGWMPC</th>
        <th rowspan="3" class="th-cng">PERA<br>{{ $period->pera_label ?? '' }}</th>
        <th rowspan="3" class="th-cng">RA</th>
        <th rowspan="3" class="th-cng">TA</th>
      </tr>
      <tr>
        <th colspan="3" class="th-gsis">Life &amp; Retirement</th>
        <th rowspan="2" class="th-gsis">Real Estate Loan</th>
        <th rowspan="2" class="th-gsis">Conso Loan</th>
        <th rowspan="2" class="th-gsis">Emergency Loan</th>
        <th rowspan="2" class="th-gsis">GSIS MPL</th>
        <th rowspan="2" class="th-gsis">GSIS MPL LITE</th>
        <th rowspan="2" class="th-gsis">GFAL</th>
        <th rowspan="2" class="th-gsis">COMPUTER LOAN</th>
        <th rowspan="2" class="th-gsis">Reg Policy</th>
        <th rowspan="2" class="th-pagibig">Personal Share</th>
        <th rowspan="2" class="th-pagibig">Gov't Share</th>
        <th rowspan="2" class="th-pagibig">Multi-Purpose Loan</th>
        <th rowspan="2" class="th-pagibig">Calamity Loan</th>
        <th rowspan="2" class="th-ph">Personal Share</th>
        <th rowspan="2" class="th-ph">Gov't Share</th>
      </tr>
      <tr>
        <th class="th-gsis">Personal Share</th>
        <th class="th-gsis">Govt Share</th>
        <th class="th-gsis">EC</th>
      </tr>
    </thead>

    <tbody>
      @php
      $tot = array_fill_keys([
        'gross','gsis_ee','gsis_govt','gsis_ec',
        'gsis_real_estate','gsis_conso','gsis_emergency',
        'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_policy',
        'pagibig_ee','pagibig_govt','pagibig_mpl','pagibig_calamity',
        'overpayment','philhealth_ee','philhealth_govt',
        'wtax','dbp','lbp','cngwmpc','cng_pera','cng_ra',
        'other_ded','pera','rata','ta','net'
      ], 0);
      $n = fn($v) => ($v ?? 0) > 0 ? number_format($v, 2) : '';
      @endphp

      @foreach($records as $i => $r)
      @php
        $rata = ($r->allowance_rata ?? 0) + ($r->allowance_other ?? 0);
        $ta   = $r->allowance_ta ?? 0;
        $tot['gross']            += $r->gross_salary ?? 0;
        $tot['gsis_ee']          += $r->gsis_ee ?? 0;
        $tot['gsis_govt']        += $r->gsis_govt ?? 0;
        $tot['gsis_ec']          += $r->gsis_ec ?? 0;
        $tot['gsis_real_estate'] += $r->gsis_real_estate ?? 0;
        $tot['gsis_conso']       += $r->gsis_conso ?? 0;
        $tot['gsis_emergency']   += $r->gsis_emergency ?? 0;
        $tot['gsis_mpl']         += $r->gsis_mpl ?? 0;
        $tot['gsis_mpl_lite']    += $r->gsis_mpl_lite ?? 0;
        $tot['gsis_gfal']        += $r->gsis_gfal ?? 0;
        $tot['gsis_computer']    += $r->gsis_computer ?? 0;
        $tot['gsis_policy']      += $r->gsis_policy ?? 0;
        $tot['pagibig_ee']       += $r->pagibig_ee ?? 0;
        $tot['pagibig_govt']     += $r->pagibig_govt ?? 0;
        $tot['pagibig_mpl']      += $r->pagibig_mpl ?? 0;
        $tot['pagibig_calamity'] += $r->pagibig_calamity ?? 0;
        $tot['overpayment']      += $r->overpayment ?? 0;
        $tot['philhealth_ee']    += $r->philhealth_ee ?? 0;
        $tot['philhealth_govt']  += $r->philhealth_govt ?? 0;
        $tot['wtax']             += $r->withholding_tax ?? 0;
        $tot['dbp']              += $r->loan_dbp ?? 0;
        $tot['lbp']              += $r->loan_lbp ?? 0;
        $tot['cngwmpc']          += $r->loan_cngwmpc ?? 0;
        $tot['cng_pera']         += $r->allowance_pera ?? 0;
        $tot['cng_ra']           += $rata;
        $tot['other_ded']        += $r->other_deduction ?? 0;
        $tot['pera']             += $r->allowance_pera ?? 0;
        $tot['rata']             += $rata;
        $tot['ta']               += $ta;
        $tot['net']              += $r->net_pay ?? 0;
      @endphp
      <tr>
        <td class="c">{{ $i+1 }}</td>
        <td class="l" style="font-size:3.8pt;">
          <strong>{{ strtoupper($r->employee->last_name ?? '—') }}</strong>,
          {{ strtoupper($r->employee->first_name ?? '') }}
        </td>
        <td class="c">{{ $r->designation ?? optional($r->employee->position)->position_code }}</td>
        <td class="c" style="font-size:3.4pt;">{{ $r->employee->employee_code ?? '' }}</td>
        <td class="r">{{ number_format($r->gross_salary ?? 0, 2) }}</td>
        <td class="r">{{ $n($r->gsis_ee) }}</td>
        <td class="r">{{ $n($r->gsis_govt) }}</td>
        <td class="r">{{ $n($r->gsis_ec) }}</td>
        <td class="r">{{ $n($r->gsis_real_estate) }}</td>
        <td class="r">{{ $n($r->gsis_conso) }}</td>
        <td class="r">{{ $n($r->gsis_emergency) }}</td>
        <td class="r">{{ $n($r->gsis_mpl) }}</td>
        <td class="r">{{ $n($r->gsis_mpl_lite) }}</td>
        <td class="r">{{ $n($r->gsis_gfal) }}</td>
        <td class="r">{{ $n($r->gsis_computer) }}</td>
        <td class="r">{{ $n($r->gsis_policy) }}</td>
        <td class="r">{{ $n($r->pagibig_ee) }}</td>
        <td class="r">{{ $n($r->pagibig_govt) }}</td>
        <td class="r">{{ $n($r->pagibig_mpl) }}</td>
        <td class="r">{{ $n($r->pagibig_calamity) }}</td>
        <td class="r">{{ $n($r->overpayment) }}</td>
        <td class="r">{{ $n($r->philhealth_ee) }}</td>
        <td class="r">{{ $n($r->philhealth_govt) }}</td>
        <td class="r">{{ $n($r->withholding_tax) }}</td>
        <td class="r">{{ $n($r->loan_dbp) }}</td>
        <td class="r">{{ $n($r->loan_lbp) }}</td>
        <td class="r">{{ $n($r->loan_cngwmpc) }}</td>
        <td class="r">{{ $n($r->allowance_pera) }}</td>
        <td class="r">{{ $rata > 0 ? number_format($rata, 2) : '' }}</td>
        <td class="r">{{ $ta > 0 ? number_format($ta, 2) : '' }}</td>
        <td class="r"><strong>{{ number_format($r->net_pay ?? 0, 2) }}</strong></td>
        <td class="c" style="font-size:3.5pt;">{{ $i+1 }}</td>
      </tr>
      @endforeach
    </tbody>

    <tfoot>
      <tr class="totals-row">
        <td colspan="4" class="r" style="font-size:3.5pt; font-weight:normal;">
          TOTAL ({{ $records->count() }} Employees)
        </td>
        <td class="r">{{ number_format($tot['gross'],2) }}</td>
        <td class="r">{{ number_format($tot['gsis_ee'],2) }}</td>
        <td class="r">{{ number_format($tot['gsis_govt'],2) }}</td>
        <td class="r">{{ $n($tot['gsis_ec']) }}</td>
        <td class="r">{{ $n($tot['gsis_real_estate']) }}</td>
        <td class="r">{{ $n($tot['gsis_conso']) }}</td>
        <td class="r">{{ $n($tot['gsis_emergency']) }}</td>
        <td class="r">{{ $n($tot['gsis_mpl']) }}</td>
        <td class="r">{{ $n($tot['gsis_mpl_lite']) }}</td>
        <td class="r">{{ $n($tot['gsis_gfal']) }}</td>
        <td class="r">{{ $n($tot['gsis_computer']) }}</td>
        <td class="r">{{ $n($tot['gsis_policy']) }}</td>
        <td class="r">{{ number_format($tot['pagibig_ee'],2) }}</td>
        <td class="r">{{ number_format($tot['pagibig_govt'],2) }}</td>
        <td class="r">{{ $n($tot['pagibig_mpl']) }}</td>
        <td class="r">{{ $n($tot['pagibig_calamity']) }}</td>
        <td class="r">{{ $n($tot['overpayment']) }}</td>
        <td class="r">{{ number_format($tot['philhealth_ee'],2) }}</td>
        <td class="r">{{ number_format($tot['philhealth_govt'],2) }}</td>
        <td class="r">{{ number_format($tot['wtax'],2) }}</td>
        <td class="r">{{ $n($tot['dbp']) }}</td>
        <td class="r">{{ $n($tot['lbp']) }}</td>
        <td class="r">{{ $n($tot['cngwmpc']) }}</td>
        <td class="r">{{ $n($tot['cng_pera']) }}</td>
        <td class="r">{{ $n($tot['cng_ra']) }}</td>
        <td class="r">{{ $n($tot['ta']) }}</td>
        <td class="r"><strong>{{ number_format($tot['net'],2) }}</strong></td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  {{-- ══ FOOTER STRIPS ══ --}}
  <div class="footer-outer">

    {{-- ── Strip 1 ──
         Col A (~18%) : (2) Approved  +  (3) Preaudited   — NO border
         Col B (~20%) : Reviewed / Prepared by             — NO border
         Col C (~24%) : CERTIFICATION                      — BORDERED box
         Col D (~38%) : Certify (5) & (6) plain text       — NO border
    --}}
    <table class="ft1">
      <tbody>
        <tr>

          {{-- Col A: Approved for payment + Preaudited --}}
          <td style="width:18%; vertical-align:top; padding-right:4px;">
            <div style="font-size:3.8pt; font-weight:bold;">(2) APPROVED for payment subject to preaudit:</div>
            <div style="margin-top:10px; border-bottom:0.7px solid #000; font-weight:bold; font-size:4.5pt; text-align:center; min-width:80px;">{{ $acctName }}</div>
            <div style="font-size:3.2pt; text-align:center;">{{ $acctTitle }}</div>
            <div style="margin-top:6px; font-size:3.8pt;">(3) Preaudited and approved for payment in the month of</div>
            <div style="font-size:3.2pt; margin-top:1px;">State Auditor IV</div>
            <div style="margin-top:6px; border-bottom:0.7px solid #000; font-weight:bold; font-size:4.5pt; text-align:center; min-width:80px;">{{ $auditorName }}</div>
            <div style="font-size:3.2pt; text-align:center;">{{ $auditorTitle }}</div>
          </td>

          {{-- Col B: Reviewed / Prepared by --}}
          <td style="width:20%; vertical-align:top; padding-right:4px;">
            <div style="font-size:3.8pt;">Reviewed as to attached dtr and approved leave:</div>
            <div style="margin-top:4px; font-size:3.8pt;">Prepared by:</div>
            <div style="margin-top:14px; border-bottom:0.7px solid #000; font-weight:bold; font-size:5pt; text-align:center;">{{ $clerkName }}</div>
            <div style="font-size:3.2pt; text-align:center;">{{ $clerkTitle }}</div>
          </td>

          {{-- Col C: CERTIFICATION — bordered box --}}
          <td style="width:24%; vertical-align:top; border:0.7px solid #000 !important; padding:3px;">
            <div style="font-weight:bold; text-align:center; font-size:4pt; margin-bottom:2px; letter-spacing:0.5px;">CERTIFICATION:</div>
            <div style="text-align:justify; font-size:3.5pt; line-height:1.35;">
              This is to certify that the amount herein collected for RA/TA represents expenses incurred in the discharge of my official functions and duties as Provincial Agriculturist.
            </div>
            <div style="margin-top:2px; text-align:justify; font-size:3.5pt; line-height:1.35;">
              This certifies further that I am not assigned nor used government vehicle in the discharge of my official functions and duties.
            </div>
            <div style="margin-top:8px; border-top:0.7px solid #000; font-weight:bold; font-size:4.5pt; text-align:center; padding-top:1px;">{{ $paName }}</div>
            <div style="font-size:3.2pt; text-align:center;">{{ $paTitle }}</div>
          </td>

          {{-- Col D: Certify (5) & (6) — NO border, plain text --}}
          <td style="width:38%; vertical-align:top; border:none !important; padding-left:4px; padding-right:0;">
            <div style="text-align:justify; font-size:3.5pt; line-height:1.35; margin-bottom:4px;">
              (5) I HEREBY CERTIFY on my official oath that I have paid in cash to each official and employee whose name appears on the above roll the amount set opposite his name, under column 21, he having signed or marked his name under column 24 above, in my presence and at the time that payment was made to him, in acknowledgement of receipt of the money paid him.
            </div>
            <div style="text-align:justify; font-size:3.5pt; line-height:1.35;">
              (6) I HEREBY CERTIFY on my official oath that each employee whose name appears on the above roll has been paid in cash or in check, and in no other mode, the amount shown under column 21 above, opposite his name. The total of the payments made by means this pay.
            </div>
          </td>

        </tr>
      </tbody>
    </table>

    {{-- ── Strip 2: 4 equal bordered signature blocks ── --}}
    <table class="ft2">
      <tbody>
        <tr>

          {{-- Block 1: Certified (Provincial Agriculturist) --}}
          <td style="width:25%;">
            <div class="sig-lbl">CERTIFIED:</div>
            <div class="sig-body">
              I HEREBY CERTIFY on my official oath that the above PAYROLL is correct
              and that services above stated have been duly rendered. Payment for such
              services is also hereby approved from the appropriation indicated
            </div>
            <div class="sig-nm">{{ $paName }}</div>
            <div class="sig-rl">{{ $paTitle }}</div>
          </td>

          {{-- Block 2: Certified Correct (PHRMO) --}}
          <td style="width:25%;">
            <div class="sig-lbl">CERTIFIED CORRECT AS PER APPROVED APPOINTMENT &amp; SALARY RATE:</div>
            <div style="margin-top:24px;">
              <div class="sig-nm">{{ $phrmoName }}</div>
              <div class="sig-rl">{{ $phrmoTitle }}</div>
            </div>
          </td>

          {{-- Block 3: Approved for payment (Governor) --}}
          <td style="width:25%;">
            <div class="sig-lbl">Approved for payment:</div>
            <div style="margin-top:32px;">
              <div class="sig-nm">{{ $govName }}</div>
              <div class="sig-rl">{{ $govTitle }}</div>
            </div>
          </td>

          {{-- Block 4: Certified (Disbursing Officer) --}}
          <td style="width:25%;">
            <div class="sig-lbl">CERTIFIED:</div>
            <div class="sig-body">
              Each person whose name appear on the above roll has been paid the amount
              stated opposite his name after identifying him.
            </div>
            <div style="margin-top:24px; text-align:center;">
              <span style="display:inline-block; border-top:0.7px solid #000; min-width:130px; font-size:4pt; padding-top:1px; text-align:center;">NAME</span>
            </div>
            <div class="sig-rl">Name and Signature of Disbursing Officer</div>
          </td>

        </tr>
      </tbody>
    </table>

  </div>{{-- /footer-outer --}}

</div>{{-- /page-wrap --}}

{{-- ══ Auto-scale to fit 1 page ══ --}}
<script>
(function(){
  var wrap   = document.getElementById('page-wrap');
  var DPI    = 96;
  var pageH  = 11.7 * DPI;
  var marg   = 0.12 * DPI * 2;
  var avail  = pageH - marg;
  var actual = wrap.scrollHeight;
  if (actual > avail) {
    var scale = avail / actual;
    wrap.style.transformOrigin = 'top left';
    wrap.style.transform       = 'scale(' + scale + ')';
    wrap.style.marginBottom    = '-' + Math.round(actual * (1 - scale)) + 'px';
  }
})();
</script>

</body>
</html>