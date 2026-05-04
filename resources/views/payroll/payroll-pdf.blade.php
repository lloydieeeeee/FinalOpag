<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provincial Payroll — {{ $period->period_label }}</title>
<style>
  @page { size: 13in 8.5in landscape; margin: 0.4in 0.5in; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 7.5pt; color: #000; background: #fff; }

  .header { text-align: center; margin-bottom: 10px; }
  .header .title { font-size: 11pt; font-weight: 900; text-transform: uppercase; }
  .header .sub   { font-size: 8pt; }
  .header .intro { font-size: 6.5pt; margin: 6px 0 4px; font-style: italic; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 0; }

  table { width: 100%; border-collapse: collapse; }
  th { background: #1a3a1a; color: #fff; padding: 3px 4px; font-size: 7pt; text-align: center; border: 1px solid #000; white-space: nowrap; }
  td { padding: 3px 4px; border: 1px solid #ccc; font-size: 7pt; white-space: nowrap; }
  td.num { text-align: right; }
  td.ctr { text-align: center; }
  tr:nth-child(even) { background: #f9fafb; }
  tfoot td { font-weight: 900; font-size: 7.5pt; background: #e5f3e5; border: 1px solid #000; }
  tfoot td.num { text-align: right; }

  .th-group { background: #2d5a1b; }
  .signatures { display: flex; justify-content: space-between; margin-top: 18px; }
  .sig { text-align: center; }
  .sig .line { border-top: 1.5px solid #000; padding-top: 3px; font-size: 7.5pt; font-weight: bold; width: 180px; margin: 0 auto; }
  .sig .role  { font-size: 6.5pt; color: #555; }
</style>
</head>
<body>

<div class="header">
  <p class="sub">Republic of the Philippines &bull; Province of Camarines Norte &bull; Daet</p>
  <p class="title">PROVINCIAL PAYROLL</p>
  <p class="sub">OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
  <p class="intro">
    We hereby acknowledge to have received from __________________________, ICO of Camarines Norte, the sums herein specified opposite our
    respective names, the same being full compensation for our services rendered during the period stated below.
  </p>
  <p class="sub" style="font-weight:bold; font-size:8.5pt;">For the period of {{ strtoupper($period->period_label) }}</p>
</div>

<table>
  <thead>
    <tr>
      <th rowspan="2" style="width:22px;">#</th>
      <th rowspan="2">EMPLOYEE NAME</th>
      <th rowspan="2">DESIG.</th>
      <th rowspan="2" class="num">BASIC<br>SALARY</th>
      {{-- GSIS: EE(9%), Govt(12%), ECF, Policy, Emergency, Real Estate, MPL, MPL Lite, GFAL, Computer, Conso --}}
      <th colspan="11" class="th-group">GSIS DEDUCTIONS</th>
      {{-- Pag-Ibig: EE, Govt, MPL, Calamity --}}
      <th colspan="4" class="th-group" style="background:#3b6e3b;">PAG-IBIG</th>
      {{-- PhilHealth: EE, Govt --}}
      <th colspan="2" class="th-group" style="background:#2d6b5a;">PHILHEALTH</th>
      {{-- Other --}}
      <th rowspan="2">W/TAX</th>
      <th rowspan="2">DBP</th>
      <th rowspan="2">LBP</th>
      <th rowspan="2">CNGWMPC</th>
      <th rowspan="2">PARACLE</th>
      <th rowspan="2">OVER<br>PAYMENT</th>
      <th rowspan="2" style="background:#78350f;color:#fef3c7;">OTHER<br>DED.</th>
      {{-- Allowances: PERA, RATA, TA --}}
      <th colspan="3" class="th-group" style="background:#4a3b7a;">ALLOWANCES</th>
      <th rowspan="2" style="background:#c7d5e0;color:#000;">TOTAL<br>DED.</th>
      <th rowspan="2" style="background:#1a3a1a;">NET PAY</th>
      <th rowspan="2">SIG.</th>
    </tr>
    <tr>
      {{-- GSIS subs (11) --}}
      <th>EE 9%</th><th>GOVT 12%</th><th>ECF</th>
      <th>POLICY</th><th>EMERG.</th><th>REAL EST.</th>
      <th>MPL</th><th>MPL LITE</th><th>GFAL</th><th>COMPUTER</th><th>CONSO</th>
      {{-- Pag-Ibig subs (4): EE col = pagibig_govt (₱200 employee deduction, stored in pagibig_govt by system design) --}}
      <th>EE ₱200</th><th>GOVT ₱200</th><th>MPL</th><th>CAL.</th>
      {{-- PhilHealth subs (2) --}}
      <th>EE 2.5%</th><th>GOVT 2.5%</th>
      {{-- Allowance subs (3) --}}
      <th>PERA</th><th>RATA</th><th>TA</th>
    </tr>
  </thead>
  <tbody>
    @php
    $tot = array_fill_keys([
      'gross',
      'gsis_ee','gsis_govt','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
      'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
      'pagibig_ee','pagibig_govt','pagibig_mpl','pagibig_calamity',
      'philhealth_ee','philhealth_govt',
      'wtax','dbp','lbp','cngwmpc','paracle','overpayment','other_deduction',
      'pera','rata','ta',
      'total_ded','net'
    ], 0);
    $tot['other_deduction_labels'] = []; // collect labels for reference
    @endphp

    @foreach($records as $i => $r)
    @php
      $rata = ($r->allowance_rata ?? 0) + ($r->allowance_other ?? 0);
      $ta   = $r->allowance_ta ?? 0;
      $tot['gross']          += $r->gross_salary;
      $tot['gsis_ee']        += $r->gsis_ee;
      $tot['gsis_govt']      += $r->gsis_govt;
      $tot['gsis_ec']        += $r->gsis_ec ?? 0;
      $tot['gsis_policy']    += $r->gsis_policy ?? 0;
      $tot['gsis_emergency'] += $r->gsis_emergency ?? 0;
      $tot['gsis_real_estate']+=$r->gsis_real_estate ?? 0;
      $tot['gsis_mpl']       += $r->gsis_mpl ?? 0;
      $tot['gsis_mpl_lite']  += $r->gsis_mpl_lite ?? 0;
      $tot['gsis_gfal']      += $r->gsis_gfal ?? 0;
      $tot['gsis_computer']  += $r->gsis_computer ?? 0;
      $tot['gsis_conso']     += $r->gsis_conso ?? 0;
      $tot['pagibig_ee']     += $r->pagibig_ee;
      $tot['pagibig_govt']   += $r->pagibig_govt;
      $tot['pagibig_mpl']    += $r->pagibig_mpl ?? 0;
      $tot['pagibig_calamity']+=$r->pagibig_calamity ?? 0;
      $tot['philhealth_ee']  += $r->philhealth_ee;
      $tot['philhealth_govt']+= $r->philhealth_govt;
      $tot['wtax']           += $r->withholding_tax;
      $tot['dbp']            += $r->loan_dbp;
      $tot['lbp']            += $r->loan_lbp;
      $tot['cngwmpc']        += $r->loan_cngwmpc;
      $tot['paracle']        += $r->loan_paracle;
      $tot['overpayment']    += $r->overpayment ?? 0;
      $tot['other_deduction'] = ($tot['other_deduction'] ?? 0) + ($r->other_deduction ?? 0);
      if (!empty($r->other_deduction_label) && ($r->other_deduction ?? 0) > 0) {
          $tot['other_deduction_labels'][] = $r->other_deduction_label;
      }
      $tot['pera']           += $r->allowance_pera;
      $tot['rata']           += $rata;
      $tot['ta']             += $ta;
      $tot['total_ded']      += $r->total_deductions;
      $tot['net']            += $r->net_pay;
    @endphp
    <tr>
      <td class="ctr">{{ $i + 1 }}</td>
      <td><strong>{{ strtoupper($r->employee->last_name ?? '—') }}</strong>, {{ $r->employee->first_name ?? '' }}</td>
      <td class="ctr">{{ $r->designation ?? optional($r->employee->position)->position_code }}</td>
      <td class="num">{{ number_format($r->gross_salary, 2) }}</td>
      {{-- GSIS (11) --}}
      <td class="num">{{ $r->gsis_ee > 0 ? number_format($r->gsis_ee,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_govt ?? 0) > 0 ? number_format($r->gsis_govt,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_ec ?? 0) > 0 ? number_format($r->gsis_ec,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_policy ?? 0) > 0 ? number_format($r->gsis_policy,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_emergency ?? 0) > 0 ? number_format($r->gsis_emergency,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_real_estate ?? 0) > 0 ? number_format($r->gsis_real_estate,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_mpl ?? 0) > 0 ? number_format($r->gsis_mpl,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_mpl_lite ?? 0) > 0 ? number_format($r->gsis_mpl_lite,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_gfal ?? 0) > 0 ? number_format($r->gsis_gfal,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_computer ?? 0) > 0 ? number_format($r->gsis_computer,2) : '' }}</td>
      <td class="num">{{ ($r->gsis_conso ?? 0) > 0 ? number_format($r->gsis_conso,2) : '' }}</td>
      {{-- Pag-Ibig (4): pagibig_govt = employee ₱200 deduction; pagibig_ee = employer match --}}
      <td class="num">{{ ($r->pagibig_govt ?? 0) > 0 ? number_format($r->pagibig_govt,2) : '' }}</td>
      <td class="num">{{ ($r->pagibig_ee ?? 0) > 0 ? number_format($r->pagibig_ee,2) : '' }}</td>
      <td class="num">{{ ($r->pagibig_mpl ?? 0) > 0 ? number_format($r->pagibig_mpl,2) : '' }}</td>
      <td class="num">{{ ($r->pagibig_calamity ?? 0) > 0 ? number_format($r->pagibig_calamity,2) : '' }}</td>
      {{-- PhilHealth (2) --}}
      <td class="num">{{ $r->philhealth_ee > 0 ? number_format($r->philhealth_ee,2) : '' }}</td>
      <td class="num">{{ ($r->philhealth_govt ?? 0) > 0 ? number_format($r->philhealth_govt,2) : '' }}</td>
      {{-- Other deductions --}}
      <td class="num">{{ $r->withholding_tax > 0 ? number_format($r->withholding_tax,2) : '' }}</td>
      <td class="num">{{ $r->loan_dbp > 0 ? number_format($r->loan_dbp,2) : '' }}</td>
      <td class="num">{{ $r->loan_lbp > 0 ? number_format($r->loan_lbp,2) : '' }}</td>
      <td class="num">{{ $r->loan_cngwmpc > 0 ? number_format($r->loan_cngwmpc,2) : '' }}</td>
      <td class="num">{{ $r->loan_paracle > 0 ? number_format($r->loan_paracle,2) : '' }}</td>
      <td class="num">{{ ($r->overpayment ?? 0) > 0 ? number_format($r->overpayment,2) : '' }}</td>
      {{-- OTHER DED: show amount; tooltip shows label --}}
      <td class="num" style="{{ ($r->other_deduction ?? 0) > 0 ? 'background:#fffbeb;color:#92400e;font-weight:700;' : '' }}"
          title="{{ $r->other_deduction_label ?? '' }}">
        {{ ($r->other_deduction ?? 0) > 0 ? number_format($r->other_deduction, 2) : '' }}
      </td>
      {{-- Allowances (3) --}}
      <td class="num">{{ $r->allowance_pera > 0 ? number_format($r->allowance_pera,2) : '' }}</td>
      <td class="num">{{ $rata > 0 ? number_format($rata,2) : '' }}</td>
      <td class="num">{{ $ta > 0 ? number_format($ta,2) : '' }}</td>
      {{-- Totals --}}
      <td class="num"><strong>{{ number_format($r->total_deductions, 2) }}</strong></td>
      <td class="num" style="font-weight:900;background:#e8f5e9;">{{ number_format($r->net_pay, 2) }}</td>
      <td></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" style="text-align:right;font-size:8pt;">TOTAL ({{ $records->count() }} employees)</td>
      <td class="num">{{ number_format($tot['gross'],2) }}</td>
      <td class="num">{{ number_format($tot['gsis_ee'],2) }}</td>
      <td class="num">{{ number_format($tot['gsis_govt'],2) }}</td>
      <td class="num">{{ $tot['gsis_ec']>0?number_format($tot['gsis_ec'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_policy']>0?number_format($tot['gsis_policy'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_emergency']>0?number_format($tot['gsis_emergency'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_real_estate']>0?number_format($tot['gsis_real_estate'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_mpl']>0?number_format($tot['gsis_mpl'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_mpl_lite']>0?number_format($tot['gsis_mpl_lite'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_gfal']>0?number_format($tot['gsis_gfal'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_computer']>0?number_format($tot['gsis_computer'],2):'' }}</td>
      <td class="num">{{ $tot['gsis_conso']>0?number_format($tot['gsis_conso'],2):'' }}</td>
      {{-- pagibig_govt = employee ₱200 deduction (EE col); pagibig_ee = employer match (GOVT col) --}}
      <td class="num">{{ number_format($tot['pagibig_govt'],2) }}</td>
      <td class="num">{{ $tot['pagibig_ee']>0?number_format($tot['pagibig_ee'],2):'' }}</td>
      <td class="num">{{ $tot['pagibig_mpl']>0?number_format($tot['pagibig_mpl'],2):'' }}</td>
      <td class="num">{{ $tot['pagibig_calamity']>0?number_format($tot['pagibig_calamity'],2):'' }}</td>
      <td class="num">{{ number_format($tot['philhealth_ee'],2) }}</td>
      <td class="num">{{ number_format($tot['philhealth_govt'],2) }}</td>
      <td class="num">{{ number_format($tot['wtax'],2) }}</td>
      <td class="num">{{ $tot['dbp']>0?number_format($tot['dbp'],2):'' }}</td>
      <td class="num">{{ $tot['lbp']>0?number_format($tot['lbp'],2):'' }}</td>
      <td class="num">{{ $tot['cngwmpc']>0?number_format($tot['cngwmpc'],2):'' }}</td>
      <td class="num">{{ $tot['paracle']>0?number_format($tot['paracle'],2):'' }}</td>
      <td class="num">{{ $tot['overpayment']>0?number_format($tot['overpayment'],2):'' }}</td>
      <td class="num" style="{{ ($tot['other_deduction']??0)>0?'background:#fffbeb;color:#92400e;font-weight:700;':'' }}">
        {{ ($tot['other_deduction']??0)>0?number_format($tot['other_deduction'],2):'' }}
      </td>
      <td class="num">{{ number_format($tot['pera'],2) }}</td>
      <td class="num">{{ $tot['rata']>0?number_format($tot['rata'],2):'' }}</td>
      <td class="num">{{ $tot['ta']>0?number_format($tot['ta'],2):'' }}</td>
      <td class="num"><strong>{{ number_format($tot['total_ded'],2) }}</strong></td>
      <td class="num" style="background:#c8e6c9;font-size:8.5pt;"><strong>{{ number_format($tot['net'],2) }}</strong></td>
      <td></td>
    </tr>
  </tfoot>
</table>

@php
  $clerkName  = strtoupper($period->sig_clerk_name  ?? 'MELINDA R. BARCELONA');
  $clerkTitle = $period->sig_clerk_title ?? 'Administrative Officer V / Prepared by';
@endphp
<div class="signatures">
  <div class="sig">
    <p class="line">{{ $clerkName }}</p>
    <p class="role">{{ $clerkTitle }}</p>
  </div>
  <div class="sig">
    <p class="line">ALMIRANTE A. ABAD</p>
    <p class="role">Provincial Agriculturist / Certified Correct</p>
  </div>
  <div class="sig">
    <p class="line">ENGR. JOSEPH V. ASCUTIA</p>
    <p class="role">Acting Governor / Approved</p>
  </div>
</div>

</body>
</html>