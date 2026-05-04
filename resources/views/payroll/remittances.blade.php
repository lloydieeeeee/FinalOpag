@extends('layouts.app')
@section('title','Remittances')
@section('page-title','Remittances')
@section('content')

<!-- Master Wrapper to Prevent Table Flexbox Blowout -->
<div style="width: 100%; max-width: 100%; min-width: 0; position: relative;">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
*,*::before,*::after{box-sizing:border-box;}
body,input,select,button,textarea{font-family:'Plus Jakarta Sans',sans-serif;}

.breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:10px;flex-wrap:wrap;}
.breadcrumb a{color:#6b7280;text-decoration:none;}.breadcrumb a:hover{color:#1a3a1a;}
.breadcrumb .sep{color:#d1d5db;}.breadcrumb .current{color:#1a3a1a;font-weight:600;}

.period-select{
    appearance:none;-webkit-appearance:none;
    padding:6px 28px 6px 10px;font-size:12px;font-weight:600;
    border:1.5px solid #e5e7eb;border-radius:8px;
    background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 8px center;
    color:#374151;outline:none;cursor:pointer;transition:border-color .15s;
}
.period-select:hover{border-color:#1a3a1a;}
.period-select:focus{border-color:#1a3a1a;box-shadow:0 0 0 3px rgba(26,58,26,.08);}
.period-status-badge{display:inline-flex;align-items:center;font-size:10px;padding:3px 9px;border-radius:20px;font-weight:700;white-space:nowrap;}

/* =========================================
   MODERN STAT CARDS & AGENCY STRIP
========================================= */
.stats-header-wrap { margin: 0 0 24px; }

.stats-row {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    gap: 16px;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 4px 12px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 10px 20px rgba(0,0,0,0.04);
}
.stat-card.dark {
    background: linear-gradient(135deg, #166534 0%, #14532d 100%);
    border: none;
}

.stat-lbl {
    font-size: 12px; font-weight: 700; color: #6b7280;
    margin-bottom: 6px; text-transform: uppercase; letter-spacing: .05em;
}
.stat-card.dark .stat-lbl { color: rgba(255,255,255,.7); }

.stat-val {
    font-size: 28px; font-weight: 800; color: #111827;
    letter-spacing: -1px; line-height: 1;
}
.stat-card.dark .stat-val { color: #fff; }

.stat-sub {
    font-size: 12px; color: #9ca3af; margin-top: 6px;
}
.stat-card.dark .stat-sub { color: rgba(255,255,255,.5); }

.stat-icon {
    width: 52px; height: 52px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.stat-icon.green { background: #f0fdf4; color: #16a34a; }
.stat-icon.dk    { background: rgba(255,255,255,.15); color: #fff; }
.stat-icon svg { width: 24px; height: 24px; }

.agency-strip-wrap { margin-top: 16px; }
.agency-strip { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; }

.agency-card {
    background: #fff; border-radius: 14px; border: 1px solid #e5e7eb; 
    padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative; overflow: hidden;
}
.agency-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,.05);
}

/* Color accents for agencies */
.agency-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
.agency-card.gsis::before { background: #1e40af; }
.agency-card.pagibig::before { background: #7c3aed; }
.agency-card.phic::before { background: #0891b2; }
.agency-card.wtax::before { background: #ea580c; }
.agency-card.loans::before { background: #059669; }

.agency-card-top { display:flex; align-items:center; gap:8px; margin-bottom:12px; }
.agency-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.agency-name { font-size:11.5px; font-weight:800; color:#4b5563; text-transform:uppercase; letter-spacing:.05em;}

.agency-total { font-size:20px; font-weight:800; color:#111827; letter-spacing:-.5px; font-family:'JetBrains Mono',monospace; margin-bottom: 12px;}

.agency-divider { border: 0; height: 1px; background: #f3f4f6; margin: 0 0 12px 0; }

.agency-rows { display:flex; flex-direction:column; gap:6px; }
.agency-row { display:flex; justify-content:space-between; align-items: center; font-size:11px; }
.agency-row .lbl { color:#6b7280; font-weight: 500; }
.agency-row .val { font-weight:700; color:#374151; font-family:'JetBrains Mono',monospace; }

/* =========================================
   TABLE SCALING & TABS
========================================= */
.rem-tabs-wrap {
    background:#fff;
    border-radius:14px;
    border:1px solid #f0f0f0;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    overflow:hidden;
    width: 100%;
    max-width: 100%;
}
.rem-tabs-header{display:flex;align-items:center;border-bottom:1.5px solid #f0f0f0;padding:0 6px;overflow-x:auto;scrollbar-width:none;}
.rem-tabs-header::-webkit-scrollbar{display:none;}
.rem-tab{display:flex;align-items:center;gap:7px;padding:11px 14px;font-size:12px;font-weight:600;color:#6b7280;cursor:pointer;border-bottom:2.5px solid transparent;margin-bottom:-1.5px;white-space:nowrap;transition:color .15s,border-color .15s;background:none;border-top:none;border-left:none;border-right:none;outline:none;}
.rem-tab:hover{color:#1a3a1a;}
.rem-tab.active{color:#1a3a1a;border-bottom-color:#1a3a1a;}
.tab-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;border-radius:20px;font-size:9px;font-weight:700;background:#f3f4f6;color:#6b7280;}
.rem-tab.active .tab-badge{background:#1a3a1a;color:#fff;}
.rem-tab-panel{display:none;}
.rem-tab-panel.active{display:block;}

.panel-toolbar{padding:10px 14px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;background:linear-gradient(135deg,#fafffe,#f6faf6);}
.ptitle{font-size:13px;font-weight:700;color:#1f2937;margin:0 0 2px;}
.psub{font-size:11px;color:#9ca3af;margin:0;}
.ptoolbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}

.toolbar-period-wrap{display:flex;align-items:center;gap:6px;padding:5px 10px 5px 8px;background:#fff;border:1.5px solid #e5e7eb;border-radius:9px;flex-shrink:0;}
.toolbar-period-label{font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;}
.toolbar-period-divider{width:1px;height:14px;background:#e5e7eb;flex-shrink:0;}

.search-wrap{position:relative;}
.search-wrap svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;}
.search-wrap input{padding:7px 10px 7px 30px;font-size:12px;border:1.5px solid #e5e7eb;border-radius:8px;outline:none;width:190px;transition:border-color .15s;background:#fff;color:#374151;}
.search-wrap input:focus{border-color:#1a3a1a;}
.btn-pdf{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;font-size:11px;font-weight:600;color:#374151;background:#fff;border:1.5px solid #e5e7eb;border-radius:7px;cursor:pointer;text-decoration:none;transition:all .15s;}
.btn-pdf:hover{border-color:#1a3a1a;color:#1a3a1a;}

.tsa{
    overflow-x:auto;
    scrollbar-width:thin;
    scrollbar-color:#d1d5db transparent;
    width: 100%;
    display: block;
}
.tsa::-webkit-scrollbar{height:6px;}.tsa::-webkit-scrollbar-thumb{background:#9ca3af;border-radius:99px;}

/* Shrunk table fonts and paddings to fit screen */
.data-table{width:100%; border-collapse:collapse; font-size:11px;}
.data-table thead tr{background:#fafafa; border-bottom:1px solid #f3f4f6;}
.data-table th{padding:8px 6px; text-align:left; font-size:8.5px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0; white-space:nowrap;}
.data-table th.r{text-align:right;}
.data-table th.c{text-align:center;}
.data-table td{padding:0; border-bottom:1px solid #f9fafb; color:#374151; white-space:nowrap; vertical-align:middle;}
.data-table td.r{text-align:right;}
.data-table td.c{text-align:center;}
.data-table td.pad{padding:6px 8px;}
.data-table tbody tr{transition:background .1s;}
.data-table tbody tr:hover{background:#f0fdf4;}

/* COLOR CODED ROW BORDERS */
.data-table tbody tr {
    border-left: 3px solid transparent; 
}
/* Ensure the first cell doesn't stick too far left because of the border */
.data-table tbody tr td:first-child { padding-left: 5px; }

#tbl-gsis tbody tr { border-left-color: #1e40af; }
#tbl-pagibig tbody tr { border-left-color: #7c3aed; }
#tbl-phic tbody tr { border-left-color: #0891b2; }
#tbl-wtax tbody tr { border-left-color: #ea580c; }
#tbl-dbp tbody tr, #tbl-lbp tbody tr, #tbl-cng tbody tr, #tbl-paracle tbody tr { border-left-color: #059669; }


.emp-name{font-weight:700;color:#111827;font-size:11.5px;}
.emp-id{font-size:9.5px;color:#9ca3af;font-family:'JetBrains Mono',monospace;margin-top:1px;}
.row-num{font-size:10.5px;color:#9ca3af;font-weight:700;}
.mono{font-family:'JetBrains Mono',monospace;}
.amt-red{color:#dc2626;font-weight:700;}
.amt-green{color:#15803d;font-weight:700;}
.amt-blue{color:#1e40af;font-weight:600;}
.amt-purple{color:#5b21b6;font-weight:600;}
.amt-teal{color:#047857;font-weight:600;}
.amt-orange{color:#92400e;font-weight:600;}
.zero{color:#d1d5db;}
.tfoot-row td{font-weight:700;background:#f0fdf4;border-top:2px solid #bbf7d0;border-bottom:none;padding:8px 8px;font-family:'JetBrains Mono',monospace;font-size:11px;}

.btn-del-row{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:6px;border:1px solid #fee2e2;background:#fff5f5;color:#dc2626;cursor:pointer;transition:all .15s;flex-shrink:0;padding:0;}
.btn-del-row:hover{background:#fee2e2;border-color:#fca5a5;transform:scale(1.1);}
.btn-del-row svg{width:11px;height:11px;}
.del-col-header{width:30px;text-align:center;}

/* Shrunk the input boxes heavily */
.ec{position:relative; min-width:65px; display:block;}
.ec input[type="text"]{display:block; width:100%; padding:5px 6px; font-size:10.5px; font-family:'JetBrains Mono',monospace; font-weight:500; text-align:right; border:1px solid transparent; background:transparent; color:#374151; outline:none; cursor:default; margin:0; box-sizing:border-box;}
.ec input[type="text"]:focus{border:2px solid #1a6b1a; background:#fff; cursor:text; border-radius:0; outline:none; box-shadow:none;}
.ec input[type="text"].auto-computed{color:#1e40af; background:#f0f4ff;}
.ec input[type="text"].auto-computed:focus{background:#fff; color:#1e40af;}
.ec.saving input[type="text"]{opacity:.45;}
.ec.saved{background:#dcfce7!important; transition:background .4s;}
.ec.err-cell{background:#fee2e2!important;}
.ec.locked input[type="text"]{color:#6b7280; cursor:default; background:#fafafa;}
.ec.locked input[type="text"]:focus{border:1px solid #e5e7eb; background:#fafafa; cursor:default;}

#overlay{position:fixed;inset:0;z-index:90;background:rgba(0,0,0,.3);backdrop-filter:blur(5px);opacity:0;pointer-events:none;transition:opacity .3s;}
#overlay.show{opacity:1;pointer-events:all;}
#detailPanel{position:fixed;top:0;right:0;bottom:0;z-index:100;width:56vw;min-width:360px;max-width:860px;display:flex;flex-direction:column;pointer-events:none;transform:translateX(100%);transition:transform .36s cubic-bezier(.32,.72,0,1);}
#detailPanel.open{pointer-events:all;transform:translateX(0);}
.detail-box{background:#fff;width:100%;height:100%;display:flex;flex-direction:column;box-shadow:-12px 0 60px rgba(0,0,0,.22);overflow:hidden;}
.detail-header{background:linear-gradient(135deg,#1a3a1a,#2d5a1b);padding:20px 24px 18px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.detail-header h2{font-size:15px;font-weight:700;color:#fff;margin:0 0 3px;}
.detail-header p{font-size:11px;color:rgba(255,255,255,.6);margin:0;}
.detail-close{background:rgba(255,255,255,.15);border:none;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:rgba(255,255,255,.8);transition:background .15s;flex-shrink:0;}
.detail-close:hover{background:rgba(255,255,255,.28);}
.detail-body{flex:1;overflow-y:auto;background:#f8f9fa;scrollbar-width:thin;scrollbar-color:#d1d5db transparent;padding-bottom:8px;}
.detail-footer{flex-shrink:0;padding:12px 20px;border-top:1px solid #f3f4f6;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.dp-card{background:#fff;border-radius:12px;margin:12px 14px;padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.dp-card-head{display:flex;align-items:center;gap:9px;margin-bottom:12px;}
.dp-icon{width:28px;height:28px;border-radius:7px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#2d5a1b;flex-shrink:0;}
.dp-card-title{font-size:12.5px;font-weight:700;color:#111827;margin:0;}
.dp-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px 18px;}
.dp-field label{display:block;font-size:9px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;}
.dp-field p{font-size:12.5px;color:#111827;font-weight:500;margin:0;}
.dp-mini{width:100%;border-collapse:collapse;font-size:11.5px;}
.dp-mini th{padding:5px 9px;font-size:9px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:1px solid #f3f4f6;background:#fafafa;}
.dp-mini th.r{text-align:right;}
.dp-mini td{padding:6px 9px;border-bottom:1px solid #f9fafb;color:#374151;}
.dp-mini td.r{text-align:right;font-weight:600;font-family:'JetBrains Mono',monospace;}
.dp-mini tr:last-child td{border-bottom:none;}
.dp-mini .sub td{background:#f9fafb;font-weight:700;border-top:1.5px solid #e5e7eb;}
.net-bar{text-align:center;padding:16px 0 8px;}
.net-bar .lbl{font-size:10px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px;}
.net-bar .amt{font-size:30px;font-weight:800;color:#4ade80;letter-spacing:-1px;}
.net-bar .per{font-size:10px;color:rgba(255,255,255,.35);margin-top:3px;}
.dp-pills{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:4px;}
.dp-pill{flex:1;min-width:90px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:9px 12px;}
.dp-pill .pl{font-size:9px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;}
.dp-pill .pv{font-size:14px;font-weight:700;color:#fff;}
.dp-pill .pv.r{color:#f87171;}
.dp-pill .pv.g{color:#86efac;}

.del-confirm{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;}
.del-confirm.show{opacity:1;pointer-events:all;}
.del-confirm-box{background:#fff;border-radius:16px;padding:24px 28px;max-width:380px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.25);transform:scale(.95);transition:transform .2s;}
.del-confirm.show .del-confirm-box{transform:scale(1);}
.del-confirm-box h3{font-size:14px;font-weight:700;color:#111827;margin:0 0 6px;}
.del-confirm-box p{font-size:12px;color:#6b7280;margin:0 0 18px;line-height:1.6;}
.del-confirm-actions{display:flex;gap:8px;justify-content:flex-end;}
.del-btn-cancel{padding:8px 16px;font-size:12px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;color:#6b7280;cursor:pointer;}
.del-btn-cancel:hover{border-color:#9ca3af;}
.del-btn-confirm{padding:8px 16px;font-size:12px;font-weight:700;border:none;border-radius:8px;background:#dc2626;color:#fff;cursor:pointer;}
.del-btn-confirm:hover{background:#b91c1c;}

#toast{position:fixed;bottom:20px;right:20px;z-index:500;min-width:210px;background:#fff;border-radius:13px;padding:12px 15px;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;opacity:0;transform:translateY(12px);transition:all .3s;pointer-events:none;}
#toast.show{opacity:1;transform:translateY(0);}

@media(max-width:1024px){.agency-strip{grid-template-columns:repeat(3,1fr);}}
@media(max-width:768px){
    .stats-row{grid-template-columns:1fr 1fr;}
    .stats-row .stat-card:first-child{grid-column:span 2;}
    .agency-strip{grid-template-columns:1fr 1fr;}
    #detailPanel{width:100%;min-width:0;}
}
@media(max-width:480px){
    .stats-row{grid-template-columns:1fr;}
    .stats-row .stat-card:first-child{grid-column:span 1;}
    .agency-strip{grid-template-columns:1fr;}
}
</style>

<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">Remittances</span>
</div>

@if($selectedPeriod)
@php
    $empCount = $records->count();
    $totGross = $records->sum('gross_salary');
    $totNet   = $records->sum('net_pay');
    $totDed   = $records->sum('total_deductions');
    $totAllow = $records->sum('total_allowances');

    $totGsisEe      = $records->sum('gsis_ee');
    $totGsisGovt    = $records->sum('gsis_govt');
    $totGsisEc      = $records->sum('gsis_ec');
    $totGsisConso   = $records->sum('gsis_conso');
    $totGsisPolicy  = $records->sum('gsis_policy');
    $totGsisEmerg   = $records->sum('gsis_emergency');
    $totGsisRE      = $records->sum('gsis_real_estate');
    $totGsisMpl     = $records->sum('gsis_mpl');
    $totGsisMplL    = $records->sum('gsis_mpl_lite');
    $totGsisGfal    = $records->sum('gsis_gfal');
    $totGsisCom     = $records->sum('gsis_computer');
    $totGsisTotal   = $totGsisEe + $totGsisEc + $totGsisConso + $totGsisPolicy
                    + $totGsisEmerg + $totGsisRE + $totGsisMpl + $totGsisMplL
                    + $totGsisGfal + $totGsisCom;

    $totPagibigGov   = $records->sum('pagibig_govt');
    $totPagibigMpl   = $records->sum('pagibig_mpl');
    $totPagibigCal   = $records->sum('pagibig_calamity');
    $totPagibigTotal = $totPagibigGov + $totPagibigMpl + $totPagibigCal;

    $totPhicEe   = $records->sum('philhealth_ee');
    $totPhicGovt = $records->sum('philhealth_govt');
    $totPhicTotal = $totPhicEe + $totPhicGovt;

    $totWtax = $records->sum('withholding_tax');
    $totDbp  = $records->sum('loan_dbp');
    $totLbp  = $records->sum('loan_lbp');
    $totCng  = $records->sum('loan_cngwmpc');
    $totPar  = $records->sum('loan_paracle');
    $totOver = $records->sum('overpayment');

    $totPera = $records->sum('allowance_pera');
    $totRata = $records->sum('allowance_rata');
    $totTa   = $records->sum('allowance_ta');

    $isFinalized = $selectedPeriod->status === 'FINALIZED';
@endphp

<div class="stats-header-wrap">
    <div class="stats-row">
        <div class="stat-card dark">
            <div>
                <div class="stat-lbl">Total Employees</div>
                <div class="stat-val" id="statEmpCount">{{ $empCount }}</div>
                <div class="stat-sub">Active · {{ $selectedPeriod->period_label }}</div>
            </div>
            <div class="stat-icon dk">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Total Gross Salary</div>
                <div class="stat-val" id="hdr-gross-total">₱{{ number_format($totGross,0) }}</div>
                <div class="stat-sub">{{ $selectedPeriod->period_label }}</div>
            </div>
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Total Net Pay</div>
                <div class="stat-val" id="hdr-net-total">₱{{ number_format($totNet,0) }}</div>
                <div class="stat-sub">After all deductions</div>
            </div>
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
    </div>

    <div class="agency-strip-wrap">
        <div class="agency-strip">
            <div class="agency-card gsis">
                <div class="agency-card-top"><div class="agency-dot" style="background:#1e40af;"></div><span class="agency-name">GSIS</span></div>
                <div class="agency-total mono" id="strip-gsis">₱{{ number_format($totGsisTotal,2) }}</div>
                <hr class="agency-divider">
                <div class="agency-rows">
                    <div class="agency-row"><span class="lbl">EE Share (9%)</span><span class="val" id="strip-gsis-ee">{{ number_format($totGsisEe,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">Gov't Share (12%)</span><span class="val">{{ number_format($totGsisGovt,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">EC Fund</span><span class="val" id="strip-gsis-ec">{{ number_format($totGsisEc,2) }}</span></div>
                </div>
            </div>
            <div class="agency-card pagibig">
                <div class="agency-card-top"><div class="agency-dot" style="background:#7c3aed;"></div><span class="agency-name">Pag-IBIG</span></div>
                <div class="agency-total mono" id="strip-pagibig">₱{{ number_format($totPagibigTotal,2) }}</div>
                <hr class="agency-divider">
                <div class="agency-rows">
                    <div class="agency-row"><span class="lbl">Personal Share</span><span class="val" id="strip-pagibig-govt">{{ number_format($totPagibigGov,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">MPL Loan</span><span class="val" id="strip-pagibig-mpl">{{ number_format($totPagibigMpl,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">Calamity Loan</span><span class="val" id="strip-pagibig-cal">{{ number_format($totPagibigCal,2) }}</span></div>
                </div>
            </div>
            <div class="agency-card phic">
                <div class="agency-card-top"><div class="agency-dot" style="background:#0891b2;"></div><span class="agency-name">PhilHealth</span></div>
                <div class="agency-total mono" id="strip-phic">₱{{ number_format($totPhicTotal,2) }}</div>
                <hr class="agency-divider">
                <div class="agency-rows">
                    <div class="agency-row"><span class="lbl">EE Share (2.5%)</span><span class="val" id="strip-phic-ee">{{ number_format($totPhicEe,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">Gov't Share (2.5%)</span><span class="val">{{ number_format($totPhicGovt,2) }}</span></div>
                </div>
            </div>
            <div class="agency-card wtax">
                <div class="agency-card-top"><div class="agency-dot" style="background:#ea580c;"></div><span class="agency-name">Withholding Tax</span></div>
                <div class="agency-total mono" id="strip-wtax">₱{{ number_format($totWtax,2) }}</div>
                <hr class="agency-divider">
                <div class="agency-rows">
                    <div class="agency-row"><span class="lbl">BIR Remittance</span><span class="val" id="strip-wtax-total">{{ number_format($totWtax,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">Employees w/ Tax</span><span class="val" id="strip-wtax-count">{{ $records->filter(fn($r)=>($r->withholding_tax??0)>0)->count() }}</span></div>
                </div>
            </div>
            <div class="agency-card loans">
                <div class="agency-card-top"><div class="agency-dot" style="background:#059669;"></div><span class="agency-name">Bank Loans</span></div>
                <div class="agency-total mono" id="strip-loans">₱{{ number_format($totDbp+$totLbp+$totCng+$totPar,2) }}</div>
                <hr class="agency-divider">
                <div class="agency-rows">
                    <div class="agency-row"><span class="lbl">DBP</span><span class="val" id="strip-dbp">{{ number_format($totDbp,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">LBP</span><span class="val" id="strip-lbp">{{ number_format($totLbp,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">CNGWMPC</span><span class="val" id="strip-cng">{{ number_format($totCng,2) }}</span></div>
                    <div class="agency-row"><span class="lbl">PARACLE</span><span class="val" id="strip-par">{{ number_format($totPar,2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$isFinalized)
<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;padding:10px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:12px;color:#92400e;">
    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    <span><strong>Inline editing enabled.</strong> Click any cell to edit. <span style="color:#1e40af;">Blue cells</span> are auto-computed. Press <kbd>Tab</kbd> or <kbd>Enter</kbd> to save.</span>
</div>
@endif

<div class="rem-tabs-wrap">
    <div class="rem-tabs-header">
        <button class="rem-tab active" onclick="switchTab('all',this)">All Employees <span class="tab-badge" id="badge-all">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('gsis',this)">GSIS <span class="tab-badge" id="badge-gsis">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('pagibig',this)">Pag-IBIG <span class="tab-badge" id="badge-pagibig">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('phic',this)">PHIC <span class="tab-badge" id="badge-phic">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('wtax',this)">W/Tax <span class="tab-badge" id="badge-wtax">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('dbp',this)">DBP <span class="tab-badge" id="badge-dbp">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('lbp',this)">LBP <span class="tab-badge" id="badge-lbp">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('cng',this)">CNGWMPC <span class="tab-badge" id="badge-cng">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('paracle',this)">PARACLE <span class="tab-badge" id="badge-paracle">{{ $empCount }}</span></button>
        <button class="rem-tab" onclick="switchTab('overpayment',this)">Overpayment <span class="tab-badge" id="badge-overpayment">{{ $records->filter(fn($r)=>($r->overpayment??0)>0)->count() }}</span></button>
        <button class="rem-tab" onclick="switchTab('allowances',this)">Allowances <span class="tab-badge" id="badge-allowances">{{ $empCount }}</span></button>
    </div>

    @php
        $periodPillHtml = '
        <div class="toolbar-period-wrap">
            <span class="toolbar-period-label">Period</span>
            <div class="toolbar-period-divider"></div>
            <form method="GET" style="display:contents;">
                <select name="period_id" class="period-select" onchange="this.form.submit()">'
                . collect($periods)->map(fn($p)=>'<option value="'.$p->period_id.'"'.($p->period_id == optional($selectedPeriod)->period_id ? ' selected' : '').'>'.$p->period_label.'</option>')->implode('')
                .'</select>
            </form>
            <span class="period-status-badge" style="'
                .($selectedPeriod->status === 'FINALIZED'
                    ? 'background:#dcfce7;color:#15803d;'
                    : 'background:#fef3c7;color:#92400e;')
                .'">'.$selectedPeriod->status.'</span>
        </div>';
    @endphp

    {{-- ══ ALL EMPLOYEES ══ --}}
    <div class="rem-tab-panel active" id="panel-all">
        <div class="panel-toolbar">
            <div><p class="ptitle">Employee Breakdown — {{ $selectedPeriod->period_label }}</p><p class="psub">Click any row for details · Click any cell to edit</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search employee…" oninput="filterTable('tbl-all',this.value)"></div>
            </div>
        </div>
        <div class="tsa">
        <table class="data-table" id="tbl-all">
            <thead><tr>
                <th class="c">#</th><th>Employee</th>
                <th class="r">Gross</th>
                <th class="r">GSIS EE 9%</th><th class="r">PhilHealth EE</th><th class="r">Pag-IBIG</th>
                <th class="r">W/Tax</th><th class="r">DBP</th><th class="r">LBP</th>
                <th class="r">CNGWMPC</th><th class="r">PARACLE</th>
                <th class="r" style="background:#78350f;color:#fef3c7;">Other Ded.</th>
                <th class="r">PERA</th><th class="r">Total Ded.</th><th class="r">Net Pay</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad" onclick="openPanel({{ $r->payroll_id }},event)" style="cursor:pointer;"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad" style="min-width:160px;cursor:pointer;" onclick="openPanel({{ $r->payroll_id }},event)">
                    <div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div>
                    <div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div>
                </td>
                <td class="r pad mono" style="font-weight:700;color:#111827;" onclick="openPanel({{ $r->payroll_id }},event)" style="cursor:pointer;">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_ee','val'=>$r->gsis_ee,'cls'=>'amt-blue','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'philhealth_ee','val'=>$r->philhealth_ee,'cls'=>'amt-teal','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'pagibig_govt','val'=>$r->pagibig_govt,'cls'=>'amt-purple','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'withholding_tax','val'=>$r->withholding_tax,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_dbp','val'=>$r->loan_dbp,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_lbp','val'=>$r->loan_lbp,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_cngwmpc','val'=>$r->loan_cngwmpc,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_paracle','val'=>$r->loan_paracle,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r" style="{{ ($r->other_deduction ?? 0) > 0 ? 'background:#fffbeb;' : '' }}">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'other_deduction','val'=>$r->other_deduction??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'allowance_pera','val'=>$r->allowance_pera,'cls'=>'amt-green','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r pad mono amt-red" id="totded_{{ $r->payroll_id }}">{{ number_format($r->total_deductions,2) }}</td>
                <td class="r pad mono amt-green" id="netpay_{{ $r->payroll_id }}">{{ number_format($r->net_pay,2) }}</td>
                @if(!$isFinalized)
                <td class="c pad">
                    <button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row" id="foot-all">
                <td colspan="2" id="ft-all-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($totGross,2) }}</td>
                <td class="r" id="ft-all-gsis_ee">{{ number_format($totGsisEe,2) }}</td>
                <td class="r" id="ft-all-philhealth_ee">{{ number_format($totPhicEe,2) }}</td>
                <td class="r" id="ft-all-pagibig_govt">{{ number_format($totPagibigGov,2) }}</td>
                <td class="r" id="ft-all-withholding_tax">{{ number_format($totWtax,2) }}</td>
                <td class="r" id="ft-all-loan_dbp">{{ number_format($totDbp,2) }}</td>
                <td class="r" id="ft-all-loan_lbp">{{ number_format($totLbp,2) }}</td>
                <td class="r" id="ft-all-loan_cngwmpc">{{ number_format($totCng,2) }}</td>
                <td class="r" id="ft-all-loan_paracle">{{ number_format($totPar,2) }}</td>
                <td class="r" id="ft-all-other_deduction">{{ number_format($records->sum('other_deduction'),2) }}</td>
                <td class="r" id="ft-all-allowance_pera">{{ number_format($totPera,2) }}</td>
                <td class="r" id="ft-all-total_deductions" style="color:#dc2626;">{{ number_format($totDed,2) }}</td>
                <td class="r" id="ft-all-net_pay" style="color:#15803d;">{{ number_format($totNet,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table>
        </div>
    </div>

    {{-- ══ GSIS ══ --}}
    <div class="rem-tab-panel" id="panel-gsis">
        <div class="panel-toolbar">
            <div><p class="ptitle">GSIS — Government Service Insurance System</p><p class="psub">EE 9% (auto), Gov't 12% (informational), ECF ₱100, and all GSIS loans · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-gsis',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'gsis']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-gsis">
            <thead><tr>
                <th class="c">#</th><th>Employee</th><th class="r">Basic Salary</th>
                <th class="r">EE 9% <span style="color:#1e40af;font-weight:400;">(auto)</span></th>
                <th class="r">Gov't 12% <span style="color:#9ca3af;font-weight:400;">(info)</span></th>
                <th class="r">ECF ₱100 <span style="color:#9ca3af;font-weight:400;">(fixed)</span></th>
                <th class="r">Conso</th><th class="r">Policy</th><th class="r">Emergency</th>
                <th class="r">Real Estate</th><th class="r">Computer</th><th class="r">GFAL</th>
                <th class="r">MPL</th><th class="r">MPL Lite</th><th class="r">Row Total</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            @php
                $gt = ($r->gsis_ee??0)+($r->gsis_ec??0)+($r->gsis_conso??0)+($r->gsis_policy??0)
                    +($r->gsis_emergency??0)+($r->gsis_real_estate??0)+($r->gsis_computer??0)
                    +($r->gsis_gfal??0)+($r->gsis_mpl??0)+($r->gsis_mpl_lite??0);
            @endphp
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_ee','val'=>$r->gsis_ee,'cls'=>'amt-blue','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r pad mono" style="color:#6b7280;background:#f9fafb;">{{ number_format($r->gsis_govt??0,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_ec','val'=>$r->gsis_ec,'cls'=>'','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_conso','val'=>$r->gsis_conso,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_policy','val'=>$r->gsis_policy,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_emergency','val'=>$r->gsis_emergency,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_real_estate','val'=>$r->gsis_real_estate,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_computer','val'=>$r->gsis_computer,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_gfal','val'=>$r->gsis_gfal,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_mpl','val'=>$r->gsis_mpl,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'gsis_mpl_lite','val'=>$r->gsis_mpl_lite,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r pad mono amt-red gsis-row-total" data-pid="{{ $r->payroll_id }}">{{ number_format($gt,2) }}</td>
                @if(!$isFinalized)
                <td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove from this period"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="2" id="ft-gsis-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($records->sum('gross_salary'),2) }}</td>
                <td class="r" id="ft-gsis-gsis_ee">{{ number_format($totGsisEe,2) }}</td>
                <td class="r">{{ number_format($totGsisGovt,2) }}</td>
                <td class="r" id="ft-gsis-gsis_ec">{{ number_format($totGsisEc,2) }}</td>
                <td class="r" id="ft-gsis-gsis_conso">{{ number_format($totGsisConso,2) }}</td>
                <td class="r" id="ft-gsis-gsis_policy">{{ number_format($totGsisPolicy,2) }}</td>
                <td class="r" id="ft-gsis-gsis_emergency">{{ number_format($totGsisEmerg,2) }}</td>
                <td class="r" id="ft-gsis-gsis_real_estate">{{ number_format($totGsisRE,2) }}</td>
                <td class="r" id="ft-gsis-gsis_computer">{{ number_format($totGsisCom,2) }}</td>
                <td class="r" id="ft-gsis-gsis_gfal">{{ number_format($totGsisGfal,2) }}</td>
                <td class="r" id="ft-gsis-gsis_mpl">{{ number_format($totGsisMpl,2) }}</td>
                <td class="r" id="ft-gsis-gsis_mpl_lite">{{ number_format($totGsisMplL,2) }}</td>
                <td class="r" id="ft-gsis-total" style="color:#dc2626;">{{ number_format($totGsisTotal,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ══ PAG-IBIG ══ --}}
    <div class="rem-tab-panel" id="panel-pagibig">
        <div class="panel-toolbar">
            <div><p class="ptitle">Pag-IBIG — Home Development Mutual Fund (HDMF)</p><p class="psub">Personal Share (deducted), Gov't Share ₱200 (informational), MPL & Calamity loans · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-pagibig',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'pagibig']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-pagibig">
            <thead><tr>
                <th class="c">#</th><th>Employee</th><th class="r">Gross</th>
                <th class="r">Personal Share <span style="color:#9ca3af;font-weight:400;">(deducted)</span></th>
                <th class="r">Gov't Share ₱200 <span style="color:#9ca3af;font-weight:400;">(info)</span></th>
                <th class="r">MPL Loan</th>
                <th class="r">Calamity Loan</th>
                <th class="r">Overpayment</th>
                <th class="r">Row Total</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            @php $pt=($r->pagibig_govt??0)+($r->pagibig_mpl??0)+($r->pagibig_calamity??0)+($r->overpayment??0); @endphp
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'pagibig_govt','val'=>$r->pagibig_govt,'cls'=>'amt-purple','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r pad mono" style="color:#6b7280;background:#f9fafb;">200.00</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'pagibig_mpl','val'=>$r->pagibig_mpl,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'pagibig_calamity','val'=>$r->pagibig_calamity,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'overpayment','val'=>$r->overpayment,'cls'=>'amt-red','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r pad mono amt-red pagibig-row-total" data-pid="{{ $r->payroll_id }}">{{ number_format($pt,2) }}</td>
                @if(!$isFinalized)
                <td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="2" id="ft-pagibig-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($totGross,2) }}</td>
                <td class="r" id="ft-pagibig-pagibig_govt">{{ number_format($totPagibigGov,2) }}</td>
                <td class="r">{{ number_format(200*$empCount,2) }}</td>
                <td class="r" id="ft-pagibig-pagibig_mpl">{{ number_format($totPagibigMpl,2) }}</td>
                <td class="r" id="ft-pagibig-pagibig_calamity">{{ number_format($totPagibigCal,2) }}</td>
                <td class="r" id="ft-pagibig-overpayment">{{ number_format($totOver,2) }}</td>
                <td class="r" id="ft-pagibig-total" style="color:#dc2626;">{{ number_format($totPagibigTotal+$totOver,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ══ PHIC ══ --}}
    <div class="rem-tab-panel" id="panel-phic">
        <div class="panel-toolbar">
            <div><p class="ptitle">PHIC — PhilHealth Contribution Remittance</p><p class="psub">EE share (auto, max ₱2,500) and Government share (informational) · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-phic',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'philhealth']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-phic">
            <thead><tr>
                <th class="c">#</th><th>Employee</th><th class="r">Basic Salary</th>
                <th class="r">Monthly Premium</th>
                <th class="r">EE Share 2.5% <span style="color:#1e40af;font-weight:400;">(auto)</span></th>
                <th class="r">Gov't Share 2.5% <span style="color:#9ca3af;font-weight:400;">(info)</span></th>
                <th class="r">Total Premium</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r pad mono">{{ number_format(($r->philhealth_ee??0)+($r->philhealth_govt??0),2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'philhealth_ee','val'=>$r->philhealth_ee,'cls'=>'amt-teal','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r pad mono" style="color:#6b7280;background:#f9fafb;" id="phic-govt-{{ $r->payroll_id }}">{{ number_format($r->philhealth_govt??0,2) }}</td>
                <td class="r pad mono amt-red">{{ number_format(($r->philhealth_ee??0)+($r->philhealth_govt??0),2) }}</td>
                @if(!$isFinalized)
                <td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="2" id="ft-phic-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($totGross,2) }}</td>
                <td class="r">{{ number_format($totPhicEe+$totPhicGovt,2) }}</td>
                <td class="r" id="ft-phic-philhealth_ee">{{ number_format($totPhicEe,2) }}</td>
                <td class="r">{{ number_format($totPhicGovt,2) }}</td>
                <td class="r" id="ft-phic-total" style="color:#dc2626;">{{ number_format($totPhicTotal,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ══ W/TAX ══ --}}
    <div class="rem-tab-panel" id="panel-wtax">
        <div class="panel-toolbar">
            <div><p class="ptitle">WTAX — BIR Withholding Tax Remittance</p><p class="psub">Manually entered per employee · deducted from net pay · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-wtax',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'wtax']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div style="padding:8px 14px;background:#fef9c3;border-bottom:1px solid #fef08a;font-size:11px;color:#854d0e;">
            <strong>Manual entry.</strong> &nbsp;Withholding tax is entered manually per employee and is deducted from net pay. Leave blank or enter 0 if not applicable.
        </div>
        <div class="tsa"><table class="data-table" id="tbl-wtax">
            <thead><tr>
                <th class="c">#</th><th>Employee</th>
                <th class="r">Gross Salary</th>
                <th class="r">Withholding Tax</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'withholding_tax','val'=>$r->withholding_tax,'cls'=>'amt-red','locked'=>$isFinalized,'auto'=>false])</td>
                @if(!$isFinalized)
                <td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="2" id="ft-wtax-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($totGross,2) }}</td>
                <td class="r" id="ft-wtax-withholding_tax" style="color:#dc2626;">{{ number_format($totWtax,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ══ DBP ══ --}}
    <div class="rem-tab-panel" id="panel-dbp">
        <div class="panel-toolbar">
            <div><p class="ptitle">DBP — Development Bank of the Philippines</p><p class="psub">Loan amortization deductions · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-dbp',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'dbp_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-dbp">
            <thead><tr><th class="c">#</th><th>Employee</th><th class="r">Gross</th><th class="r">Amount Due</th>@if(!$isFinalized)<th class="del-col-header"></th>@endif</tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_dbp','val'=>$r->loan_dbp,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row"><td colspan="2" id="ft-dbp-count">{{ $empCount }} Employees</td><td class="r">{{ number_format($totGross,2) }}</td><td class="r" id="ft-dbp-loan_dbp" style="color:#92400e;">{{ number_format($totDbp,2) }}</td>@if(!$isFinalized)<td></td>@endif</tr></tfoot>
        </table></div>
    </div>

    {{-- ══ LBP ══ --}}
    <div class="rem-tab-panel" id="panel-lbp">
        <div class="panel-toolbar">
            <div><p class="ptitle">LBP — Land Bank of the Philippines</p><p class="psub">Loan amortization deductions · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-lbp',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'lbp_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-lbp">
            <thead><tr><th class="c">#</th><th>Employee</th><th class="r">Gross</th><th class="r">Amount Due</th>@if(!$isFinalized)<th class="del-col-header"></th>@endif</tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_lbp','val'=>$r->loan_lbp,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row"><td colspan="2" id="ft-lbp-count">{{ $empCount }} Employees</td><td class="r">{{ number_format($totGross,2) }}</td><td class="r" id="ft-lbp-loan_lbp" style="color:#92400e;">{{ number_format($totLbp,2) }}</td>@if(!$isFinalized)<td></td>@endif</tr></tfoot>
        </table></div>
    </div>

    {{-- ══ CNGWMPC ══ --}}
    <div class="rem-tab-panel" id="panel-cng">
        <div class="panel-toolbar">
            <div>
                <p class="ptitle">CNGWMPC — Camarines Norte Government Workers Multi-Purpose Cooperative</p>
                <p class="psub">All 13 sub-items inline — total auto-sums · {{ $selectedPeriod->period_label }}</p>
            </div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search…" oninput="filterTable('tbl-cng',this.value)">
                </div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'cng_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-cng">
            <thead><tr>
                <th class="c">#</th><th>Employee</th><th class="r">Gross</th>
                <th class="r">Cap. Share</th><th class="r">Kiddie Sav.</th><th class="r">Savings</th>
                <th class="r">Reg. Loan</th><th class="r">Crisis Loan</th><th class="r">Canteen</th>
                <th class="r">Coop Store</th><th class="r">Calamity</th><th class="r">Abuloy</th>
                <th class="r">Handog/Ituro</th><th class="r">B2B/Special</th><th class="r">Petty Cash</th>
                <th class="r">Commodity</th>
                <th class="r" style="background:#fff3e0;color:#e65100;font-weight:800;">TOTAL</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad">
                    <div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div>
                    <div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div>
                </td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_capital_share','val'=>$r->cng_capital_share??0,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_kiddie_savings','val'=>$r->cng_kiddie_savings??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_savings','val'=>$r->cng_savings??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_regular_loan','val'=>$r->cng_regular_loan??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_crisis_loan','val'=>$r->cng_crisis_loan??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_coop_canteen','val'=>$r->cng_coop_canteen??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_coop_store','val'=>$r->cng_coop_store??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_calamity_loan','val'=>$r->cng_calamity_loan??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_abuloy','val'=>$r->cng_abuloy??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_handog','val'=>$r->cng_handog??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_b2b_loan','val'=>$r->cng_b2b_loan??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_petty_cash','val'=>$r->cng_petty_cash??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'cng_commodity_loan','val'=>$r->cng_commodity_loan??0,'cls'=>'','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r" style="background:#fff8f0;">
                    @include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_cngwmpc','val'=>$r->loan_cngwmpc,'cls'=>'amt-orange','locked'=>true,'auto'=>true])
                </td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="3" id="ft-cng-count">{{ $empCount }} Employees</td>
                @foreach(['cng_capital_share','cng_kiddie_savings','cng_savings','cng_regular_loan','cng_crisis_loan','cng_coop_canteen','cng_coop_store','cng_calamity_loan','cng_abuloy','cng_handog','cng_b2b_loan','cng_petty_cash','cng_commodity_loan'] as $cngf)
                <td class="r" id="ft-cng-{{ $cngf }}">{{ number_format($records->sum($cngf),2) }}</td>
                @endforeach
                <td class="r" id="ft-cng-loan_cngwmpc" style="color:#e65100;font-weight:800;">{{ number_format($totCng,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ══ OVERPAYMENT ══ --}}
    <div class="rem-tab-panel" id="panel-overpayment">
        <div class="panel-toolbar">
            <div><p class="ptitle">Overpayment Recovery</p><p class="psub">Deductions for prior period overpayments · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-overpayment',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'overpayment_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-overpayment">
            <thead><tr><th class="c">#</th><th>Employee</th><th class="r">Gross</th><th class="r">Overpayment</th>@if(!$isFinalized)<th class="del-col-header"></th>@endif</tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'overpayment','val'=>$r->overpayment,'cls'=>'amt-red','locked'=>$isFinalized,'auto'=>false])</td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row"><td colspan="2" id="ft-over-count">{{ $empCount }} Employees</td><td class="r">{{ number_format($totGross,2) }}</td><td class="r" id="ft-over-overpayment" style="color:#dc2626;">{{ number_format($totOver,2) }}</td>@if(!$isFinalized)<td></td>@endif</tr></tfoot>
        </table></div>
    </div>

    {{-- ══ PARACLE ══ --}}
    <div class="rem-tab-panel" id="panel-paracle">
        <div class="panel-toolbar">
            <div><p class="ptitle">PARACLE — Rural Bank of Paracale</p><p class="psub">Loan deductions · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-paracle',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'paracle_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-paracle">
            <thead><tr><th class="c">#</th><th>Employee</th><th class="r">Gross</th><th class="r">Amount Due</th>@if(!$isFinalized)<th class="del-col-header"></th>@endif</tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'loan_paracle','val'=>$r->loan_paracle,'cls'=>'amt-orange','locked'=>$isFinalized,'auto'=>false])</td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row"><td colspan="2" id="ft-par-count">{{ $empCount }} Employees</td><td class="r">{{ number_format($totGross,2) }}</td><td class="r" id="ft-par-loan_paracle" style="color:#92400e;">{{ number_format($totPar,2) }}</td>@if(!$isFinalized)<td></td>@endif</tr></tfoot>
        </table></div>
    </div>

    {{-- ══ ALLOWANCES ══ --}}
    <div class="rem-tab-panel" id="panel-allowances">
        <div class="panel-toolbar">
            <div><p class="ptitle">Allowances — PERA, RATA, TA</p><p class="psub">PERA auto ₱2,000 · RATA ₱9,500 head of office · TA ₱9,500 head of office · {{ $selectedPeriod->period_label }}</p></div>
            <div class="ptoolbar-right">
                {!! $periodPillHtml !!}
                <div class="search-wrap"><svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-allow',this.value)"></div>
                <a href="{{ route('payroll.remittance.pdf',[$selectedPeriod->period_id,'allowances_pdf']) }}" target="_blank" class="btn-pdf">📄 PDF</a>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-allow">
            <thead><tr>
                <th class="c">#</th><th>Employee</th><th class="r">Gross</th>
                <th class="r">PERA <span style="color:#1e40af;font-weight:400;">(₱2,000)</span></th>
                <th class="r">RATA <span style="color:#9ca3af;font-weight:400;">(₱9,500 HOO)</span></th>
                <th class="r">TA <span style="color:#9ca3af;font-weight:400;">(₱9,500 HOO)</span></th>
                <th class="r">Other</th><th class="r">Total Allow.</th>
                @if(!$isFinalized)<th class="del-col-header"></th>@endif
            </tr></thead>
            <tbody>
            @foreach($records as $i => $r)
            @php $ta=($r->allowance_pera??0)+($r->allowance_rata??0)+($r->allowance_ta??0)+($r->allowance_other??0); @endphp
            <tr data-pid="{{ $r->payroll_id }}">
                <td class="c pad"><span class="row-num">{{ $i+1 }}</span></td>
                <td class="pad"><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->employee_id ?? '' }}</div></td>
                <td class="r pad mono">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'allowance_pera','val'=>$r->allowance_pera,'cls'=>'amt-green','locked'=>$isFinalized,'auto'=>true])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'allowance_rata','val'=>$r->allowance_rata,'cls'=>'amt-green','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'allowance_ta','val'=>$r->allowance_ta,'cls'=>'amt-green','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r">@include('payroll._editable_cell',['pid'=>$r->payroll_id,'field'=>'allowance_other','val'=>$r->allowance_other,'cls'=>'amt-green','locked'=>$isFinalized,'auto'=>false])</td>
                <td class="r pad mono amt-green" id="allow-rowtotal-{{ $r->payroll_id }}">{{ number_format($ta,2) }}</td>
                @if(!$isFinalized)<td class="c pad"><button class="btn-del-row" onclick="askDeleteRow({{ $r->payroll_id }}, '{{ addslashes(($r->employee->last_name ?? '') . ', ' . ($r->employee->first_name ?? '')) }}')" title="Remove"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>@endif
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td colspan="2" id="ft-allow-count">{{ $empCount }} Employees</td>
                <td class="r">{{ number_format($totGross,2) }}</td>
                <td class="r" id="ft-allow-allowance_pera">{{ number_format($totPera,2) }}</td>
                <td class="r" id="ft-allow-allowance_rata">{{ number_format($totRata,2) }}</td>
                <td class="r" id="ft-allow-allowance_ta">{{ number_format($totTa,2) }}</td>
                <td class="r" id="ft-allow-allowance_other">{{ number_format($records->sum('allowance_other'),2) }}</td>
                <td class="r" id="ft-allow-total" style="color:#15803d;">{{ number_format($totAllow,2) }}</td>
                @if(!$isFinalized)<td></td>@endif
            </tr></tfoot>
        </table></div>
    </div>

</div>

<div class="del-confirm" id="delConfirm">
    <div class="del-confirm-box">
        <h3 id="delConfirmTitle">Remove Employee?</h3>
        <p id="delConfirmMsg">This will remove this employee from the current remittance period. You can restore them by re-generating payroll for this period.</p>
        <div class="del-confirm-actions">
            <button class="del-btn-cancel" onclick="cancelDelete()">Cancel</button>
            <button class="del-btn-confirm" id="delConfirmBtn" onclick="executeDelete()">Remove</button>
        </div>
    </div>
</div>

<div id="overlay" onclick="closePanel()"></div>
<div id="detailPanel">
    <div class="detail-box">
        <div class="detail-header">
            <div style="min-width:0;"><h2 id="dpTitle">—</h2><p id="dpSub">—</p></div>
            <button class="detail-close" onclick="closePanel()">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="detail-body" id="dpBody"></div>
        <div class="detail-footer">
            <span id="dpPeriod" style="font-size:11px;color:#9ca3af;"></span>
            <button onclick="closePanel()" style="padding:7px 16px;font-size:12px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:8px;color:#6b7280;background:#fff;cursor:pointer;">Close</button>
        </div>
    </div>
</div>

<div id="toast">
    <div id="toastIcon" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
    <div><p style="font-size:12px;font-weight:700;color:#111827;margin:0;" id="toastTitle"></p><p style="font-size:11px;color:#6b7280;margin:1px 0 0;" id="toastMsg"></p></div>
</div>

<script>
const CSRF           = '{{ csrf_token() }}';
const IS_FINALIZED   = {{ $isFinalized ? 'true' : 'false' }};

const SAVE_URL_BASE   = '{{ rtrim(route("payroll.remittance.record.save",  ["id" => "__PID__"]), "__PID__") }}';
const HIDE_URL_BASE   = '{{ rtrim(route("payroll.remittance.record.hide",  ["id" => "__PID__"]), "__PID__") }}';
const PERIOD_ID       = '{{ optional($selectedPeriod)->period_id ?? "" }}';

const PAYROLL_DATA = {!! json_encode($records->keyBy('payroll_id')->map(function($r){
    return [
        'payroll_id'       => $r->payroll_id,
        'employee_id'      => $r->employee->employee_id ?? '—',
        'last_name'        => $r->employee->last_name ?? '—',
        'first_name'       => $r->employee->first_name ?? '',
        'position'         => optional($r->employee->position)->position_name ?? '—',
        'department'       => optional($r->employee->department)->department_name ?? '—',
        'period'           => optional($r->period)->period_label ?? '—',
        'gross_salary'     => (float)$r->gross_salary,
        'allowance_pera'   => (float)($r->allowance_pera??0),
        'allowance_rata'   => (float)($r->allowance_rata??0),
        'allowance_ta'     => (float)($r->allowance_ta??0),
        'allowance_other'  => (float)($r->allowance_other??0),
        'total_allowances' => (float)($r->total_allowances??0),
        'gsis_ee'          => (float)$r->gsis_ee,
        'gsis_govt'        => (float)($r->gsis_govt??0),
        'gsis_ec'          => (float)($r->gsis_ec??0),
        'gsis_conso'       => (float)($r->gsis_conso??0),
        'gsis_mpl'         => (float)($r->gsis_mpl??0),
        'gsis_mpl_lite'    => (float)($r->gsis_mpl_lite??0),
        'gsis_policy'      => (float)($r->gsis_policy??0),
        'gsis_emergency'   => (float)($r->gsis_emergency??0),
        'gsis_real_estate' => (float)($r->gsis_real_estate??0),
        'gsis_computer'    => (float)($r->gsis_computer??0),
        'gsis_gfal'        => (float)($r->gsis_gfal??0),
        'pagibig_govt'     => (float)($r->pagibig_govt??0),
        'pagibig_mpl'      => (float)($r->pagibig_mpl??0),
        'pagibig_calamity' => (float)($r->pagibig_calamity??0),
        'philhealth_ee'    => (float)$r->philhealth_ee,
        'philhealth_govt'  => (float)($r->philhealth_govt??0),
        'withholding_tax'  => (float)($r->withholding_tax??0),
        'loan_dbp'         => (float)($r->loan_dbp??0),
        'loan_lbp'         => (float)($r->loan_lbp??0),
        
        'cng_capital_share'  => (float)($r->cng_capital_share??0),
        'cng_kiddie_savings' => (float)($r->cng_kiddie_savings??0),
        'cng_savings'        => (float)($r->cng_savings??0),
        'cng_regular_loan'   => (float)($r->cng_regular_loan??0),
        'cng_crisis_loan'    => (float)($r->cng_crisis_loan??0),
        'cng_coop_canteen'   => (float)($r->cng_coop_canteen??0),
        'cng_coop_store'     => (float)($r->cng_coop_store??0),
        'cng_calamity_loan'  => (float)($r->cng_calamity_loan??0),
        'cng_abuloy'         => (float)($r->cng_abuloy??0),
        'cng_handog'         => (float)($r->cng_handog??0),
        'cng_b2b_loan'       => (float)($r->cng_b2b_loan??0),
        'cng_petty_cash'     => (float)($r->cng_petty_cash??0),
        'cng_commodity_loan' => (float)($r->cng_commodity_loan??0),
        'loan_cngwmpc'       => (float)($r->loan_cngwmpc??0),
        
        'loan_paracle'     => (float)($r->loan_paracle??0),
        'overpayment'           => (float)($r->overpayment??0),
        'other_deduction'       => (float)($r->other_deduction??0),
        'other_deduction_label' => $r->other_deduction_label ?? '',
        'total_deductions'      => (float)$r->total_deductions,
        'net_pay'               => (float)$r->net_pay,
    ];
})) !!};
</script>
@else
<div style="text-align:center;padding:60px;color:#9ca3af;font-size:14px;">Select a payroll period to view remittances.</div>
@endif

<script>
/* ═══════════════════════════════════════════════════════
   AUTO-COMPUTE HELPERS  (withholding tax removed — manual only)
═══════════════════════════════════════════════════════ */
function computeGsisEe(gross)        { return Math.round(gross * 0.09 * 100) / 100; }
function computeGsisGovt(gross)      { return Math.round(gross * 0.12 * 100) / 100; }
function computeGsisEc()             { return 100.00; }
function computePhilhealthEe(gross)  { return Math.min(Math.round(gross * 0.025 * 100) / 100, 2500); }
function computePhilhealthGovt(gross){ return Math.min(Math.round(gross * 0.025 * 100) / 100, 2500); }
function computePera()               { return 2000.00; }

/* ═══════════════════════════════════════════════════════
   APPLY AUTO-COMPUTE ON PAGE LOAD
   withholding_tax is NOT auto-computed — left as entered
═══════════════════════════════════════════════════════ */
function applyAutoCompute(pid, d) {
    const gross = d.gross_salary;
    const autoValues = {
        'gsis_ee'        : computeGsisEe(gross),
        'gsis_ec'        : computeGsisEc(),
        'philhealth_ee'  : computePhilhealthEe(gross),
        'allowance_pera' : computePera(),
    };

    Object.entries(autoValues).forEach(([field, computed]) => {
        if ((d[field] || 0) === 0 && computed > 0) {
            d[field] = computed;
            updateCellDisplay(pid, field, computed, true);
        }
    });
}

function updateCellDisplay(pid, field, value, isAuto) {
    document.querySelectorAll(`.ec[data-pid="${pid}"][data-field="${field}"] input`).forEach(inp => {
        inp.value = value > 0 ? value.toFixed(2) : '';
        if (isAuto) inp.classList.add('auto-computed');
    });
}

/* ═══════════════════════════════════════════════════════
   FORMAT HELPERS
═══════════════════════════════════════════════════════ */
function fmt(n, d=2)  { return parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:d,maximumFractionDigits:d}); }
function fmtPeso(n)   { return '₱' + fmt(n); }
function pct(p,t)     { if (!t) return '—'; return ((p/t)*100).toFixed(1)+'%'; }

/* ═══════════════════════════════════════════════════════
   REFRESH ALL FOOTER TOTALS
═══════════════════════════════════════════════════════ */
function refreshAllTotals() {
    const rows = Object.values(PAYROLL_DATA);
    const sum  = f => rows.reduce((a,r) => a+(r[f]||0), 0);
    const setEl = (id,val) => { const el = document.getElementById(id); if (el) el.textContent = fmt(val); };

    const cnt = rows.length;

    ['ft-all-count','ft-gsis-count','ft-pagibig-count','ft-phic-count',
     'ft-wtax-count','ft-dbp-count','ft-lbp-count','ft-cng-count',
     'ft-par-count','ft-over-count','ft-allow-count'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { const cols = el.colSpan; el.textContent = cnt + ' Employees'; el.colSpan = cols; }
    });

    ['all','gsis','pagibig','phic','wtax','dbp','lbp','cng','paracle','allowances'].forEach(t => {
        const b = document.getElementById('badge-' + t);
        if (b) b.textContent = cnt;
    });
    const ovBadge = document.getElementById('badge-overpayment');
    if (ovBadge) ovBadge.textContent = rows.filter(r=>(r.overpayment||0)>0).length;

    const statEmpEl = document.getElementById('statEmpCount');
    if (statEmpEl) statEmpEl.textContent = cnt;

    setEl('ft-all-gsis_ee',         sum('gsis_ee'));
    setEl('ft-all-philhealth_ee',   sum('philhealth_ee'));
    setEl('ft-all-pagibig_govt',    sum('pagibig_govt'));
    setEl('ft-all-withholding_tax', sum('withholding_tax'));
    setEl('ft-all-loan_dbp',        sum('loan_dbp'));
    setEl('ft-all-loan_lbp',        sum('loan_lbp'));
    setEl('ft-all-loan_cngwmpc',    sum('loan_cngwmpc'));
    setEl('ft-all-loan_paracle',    sum('loan_paracle'));
    // Other deduction in All tab
    const otherDedTotal = sum('other_deduction');
    const otherDedEl = document.getElementById('ft-all-other_deduction');
    if (otherDedEl) {
        otherDedEl.textContent = otherDedTotal > 0 ? fmt(otherDedTotal) : '—';
        otherDedEl.style.background = otherDedTotal > 0 ? '#fffbeb' : '';
        otherDedEl.style.color = otherDedTotal > 0 ? '#92400e' : '';
        otherDedEl.style.fontWeight = otherDedTotal > 0 ? '700' : '';
    }
    setEl('ft-all-allowance_pera',  sum('allowance_pera'));
    setEl('ft-all-total_deductions',sum('total_deductions'));
    setEl('ft-all-net_pay',         sum('net_pay'));

    const gsisF = ['gsis_ee','gsis_ec','gsis_conso','gsis_policy','gsis_emergency',
                   'gsis_real_estate','gsis_computer','gsis_gfal','gsis_mpl','gsis_mpl_lite'];
    gsisF.forEach(f => setEl('ft-gsis-'+f, sum(f)));
    setEl('ft-gsis-total', gsisF.reduce((a,f)=>a+sum(f),0));

    setEl('ft-pagibig-pagibig_govt',     sum('pagibig_govt'));
    setEl('ft-pagibig-pagibig_mpl',      sum('pagibig_mpl'));
    setEl('ft-pagibig-pagibig_calamity', sum('pagibig_calamity'));
    setEl('ft-pagibig-overpayment',      sum('overpayment'));
    setEl('ft-pagibig-total',            sum('pagibig_govt')+sum('pagibig_mpl')+sum('pagibig_calamity')+sum('overpayment'));

    setEl('ft-phic-philhealth_ee', sum('philhealth_ee'));
    setEl('ft-phic-total',         sum('philhealth_ee')+sum('philhealth_govt'));

    setEl('ft-wtax-withholding_tax', sum('withholding_tax'));
    setEl('ft-dbp-loan_dbp',         sum('loan_dbp'));
    setEl('ft-lbp-loan_lbp',         sum('loan_lbp'));
    setEl('ft-par-loan_paracle',     sum('loan_paracle'));
    setEl('ft-over-overpayment',     sum('overpayment'));

    setEl('ft-allow-allowance_pera',  sum('allowance_pera'));
    setEl('ft-allow-allowance_rata',  sum('allowance_rata'));
    setEl('ft-allow-allowance_ta',    sum('allowance_ta'));
    setEl('ft-allow-allowance_other', sum('allowance_other'));
    setEl('ft-allow-total', sum('allowance_pera')+sum('allowance_rata')+sum('allowance_ta')+sum('allowance_other'));

    const gsisTotal = gsisF.reduce((a,f)=>a+sum(f),0);
    const gsisEl = document.getElementById('strip-gsis');
    if (gsisEl) gsisEl.textContent = '₱'+fmt(gsisTotal);
    const sp = (id,val) => { const e=document.getElementById(id); if(e) e.textContent=fmt(val); };
    sp('strip-gsis-ee', sum('gsis_ee'));
    sp('strip-gsis-ec', sum('gsis_ec'));

    const pagibigT = sum('pagibig_govt')+sum('pagibig_mpl')+sum('pagibig_calamity');
    const piEl = document.getElementById('strip-pagibig');
    if (piEl) piEl.textContent = '₱'+fmt(pagibigT);
    sp('strip-pagibig-govt', sum('pagibig_govt'));
    sp('strip-pagibig-mpl',  sum('pagibig_mpl'));
    sp('strip-pagibig-cal',  sum('pagibig_calamity'));

    const phicEl = document.getElementById('strip-phic');
    if (phicEl) phicEl.textContent = '₱'+fmt(sum('philhealth_ee')+sum('philhealth_govt'));
    sp('strip-phic-ee', sum('philhealth_ee'));

    const wtaxEl = document.getElementById('strip-wtax');
    if (wtaxEl) wtaxEl.textContent = '₱'+fmt(sum('withholding_tax'));
    sp('strip-wtax-total', sum('withholding_tax'));
    const wtaxCnt = document.getElementById('strip-wtax-count');
    if (wtaxCnt) wtaxCnt.textContent = rows.filter(r=>(r.withholding_tax||0)>0).length;

    const loansT = sum('loan_dbp')+sum('loan_lbp')+sum('loan_cngwmpc')+sum('loan_paracle');
    const loansEl = document.getElementById('strip-loans');
    if (loansEl) loansEl.textContent = '₱'+fmt(loansT);
    sp('strip-dbp', sum('loan_dbp'));
    sp('strip-lbp', sum('loan_lbp'));
    sp('strip-cng', sum('loan_cngwmpc'));
    sp('strip-par', sum('loan_paracle'));

    const hdrNet = document.getElementById('hdr-net-total');
    if (hdrNet) hdrNet.textContent = '₱'+parseInt(sum('net_pay')).toLocaleString('en-PH');

    const hdrGross = document.getElementById('hdr-gross-total');
    if (hdrGross) hdrGross.textContent = '₱'+parseInt(sum('gross_salary')).toLocaleString('en-PH');

    const cngF = ['cng_capital_share','cng_kiddie_savings','cng_savings','cng_regular_loan','cng_crisis_loan','cng_coop_canteen','cng_coop_store','cng_calamity_loan','cng_abuloy','cng_handog','cng_b2b_loan','cng_petty_cash','cng_commodity_loan'];
    cngF.forEach(f => {
        const el = document.getElementById('ft-cng-'+f);
        if(el) {
            const s = sum(f);
            el.textContent = s > 0 ? fmt(s) : '—';
            el.style.color = s > 0 ? '#c2410c' : '#9ca3af';
        }
    });
    setEl('ft-cng-loan_cngwmpc', sum('loan_cngwmpc'));
}

function refreshGsisRowTotal(pid) {
    const d = PAYROLL_DATA[pid];
    const el = document.querySelector(`.gsis-row-total[data-pid="${pid}"]`);
    if (!d || !el) return;
    el.textContent = fmt(['gsis_ee','gsis_ec','gsis_conso','gsis_policy','gsis_emergency',
        'gsis_real_estate','gsis_computer','gsis_gfal','gsis_mpl','gsis_mpl_lite']
        .reduce((a,f)=>a+(d[f]||0),0));
}

function refreshPagibigRowTotal(pid) {
    const d = PAYROLL_DATA[pid];
    const el = document.querySelector(`.pagibig-row-total[data-pid="${pid}"]`);
    if (!d || !el) return;
    el.textContent = fmt((d.pagibig_govt||0)+(d.pagibig_mpl||0)+(d.pagibig_calamity||0)+(d.overpayment||0));
}

function refreshAllowRowTotal(pid) {
    const d = PAYROLL_DATA[pid];
    const el = document.getElementById('allow-rowtotal-'+pid);
    if (!d || !el) return;
    el.textContent = fmt((d.allowance_pera||0)+(d.allowance_rata||0)+(d.allowance_ta||0)+(d.allowance_other||0));
}

/* ═══════════════════════════════════════════════════════
   EXCEL-LIKE CELL INTERACTION
═══════════════════════════════════════════════════════ */
function onCellFocus(input) {
    const cell = input.closest('.ec');
    if (cell.classList.contains('locked')) { input.blur(); return; }
    setTimeout(() => input.select(), 10);
}

function onCellBlur(input) {
    const cell  = input.closest('.ec');
    const pid   = cell.dataset.pid;
    const field = cell.dataset.field;
    cell.classList.remove('focused');
    if (cell.classList.contains('locked')) return;

    const rawVal = input.value.replace(/,/g, '').trim();
    const newVal = rawVal === '' ? 0 : (parseFloat(rawVal) || 0);
    const oldVal = parseFloat(cell.dataset.orig || '0') || 0;

    // Normalise display
    input.value = newVal > 0 ? newVal.toFixed(2) : '';

    if (Math.abs(newVal - oldVal) < 0.001) return; // no real change

    const d = PAYROLL_DATA[pid];
    if (d) {
        d[field] = newVal;
    }

    saveToDB(pid, field, newVal, cell);
}

function onCellKeydown(input, e) {
    if (e.key === 'Enter' || e.key === 'Tab') {
        e.preventDefault();
        input.blur();
        const allCells = Array.from(document.querySelectorAll(
            `.ec[data-field="${input.closest('.ec').dataset.field}"] input`
        ));
        const idx = allCells.indexOf(input);
        if (idx >= 0 && idx < allCells.length - 1) allCells[idx+1].focus();
    }
    if (e.key === 'Escape') {
        const cell = input.closest('.ec');
        input.value = parseFloat(cell.dataset.orig||'0') > 0
            ? parseFloat(cell.dataset.orig).toFixed(2) : '';
        input.blur();
    }
}

function saveToDB(pid, field, value, cell) {
    cell.classList.add('saving');
    const inp = cell.querySelector('input');
    if (inp) inp.readOnly = true;

    fetch(`${SAVE_URL_BASE}${pid}`, {
        method: 'PATCH',
        headers: {
            'Content-Type':    'application/json',
            'X-CSRF-TOKEN':    CSRF,
            'X-Requested-With':'XMLHttpRequest',
            'Accept':          'application/json',
        },
        body: JSON.stringify({ field, value }),
    })
    .then(r => {
        // Surface validation (422) and auth (403) errors properly
        if (!r.ok) return r.json().then(e => { e.__status = r.status; throw e; });
        return r.json();
    })
    .then(res => {
        cell.classList.remove('saving');
        if (inp) inp.readOnly = false;

        if (res.success) {
            cell.dataset.orig = value.toFixed ? value.toFixed(2) : String(value);

            // Update in-memory store
            if (PAYROLL_DATA[pid]) {
                PAYROLL_DATA[pid][field]           = value;
                PAYROLL_DATA[pid].total_deductions = res.total_deductions;
                PAYROLL_DATA[pid].total_allowances = res.total_allowances;
                PAYROLL_DATA[pid].net_pay          = res.net_pay;
                
                if (res.philhealth_govt !== undefined) {
                    PAYROLL_DATA[pid].philhealth_govt = res.philhealth_govt;
                }
                
                if (res.loan_cngwmpc !== undefined) {
                    PAYROLL_DATA[pid].loan_cngwmpc = res.loan_cngwmpc;
                    document.querySelectorAll(`.ec[data-pid="${pid}"][data-field="loan_cngwmpc"] input`).forEach(i => {
                        i.value = res.loan_cngwmpc > 0 ? res.loan_cngwmpc.toFixed(2) : '';
                        const ec = i.closest('.ec');
                        if (ec) ec.dataset.orig = res.loan_cngwmpc.toFixed(2);
                    });
                }
            }

            // Refresh Total Deductions + Net Pay columns in "All" tab
            const dedEl = document.getElementById('totded_' + pid);
            const netEl = document.getElementById('netpay_' + pid);
            if (dedEl) dedEl.textContent = fmt(res.total_deductions);
            if (netEl) netEl.textContent = fmt(res.net_pay);

            // When philhealth_ee changes, mirror the govt (employer) share display
            if (field === 'philhealth_ee') {
                const govtVal = res.philhealth_govt !== undefined
                    ? res.philhealth_govt
                    : computePhilhealthGovt(PAYROLL_DATA[pid]?.gross_salary || 0);
                // Static display cell in PHIC tab
                const pgEl = document.getElementById('phic-govt-' + pid);
                if (pgEl) pgEl.textContent = fmt(govtVal);
                // Any editable-cell instances
                document.querySelectorAll(`.ec[data-pid="${pid}"][data-field="philhealth_govt"] input`).forEach(i => {
                    i.value = govtVal > 0 ? govtVal.toFixed(2) : '';
                    const ec = i.closest('.ec');
                    if (ec) ec.dataset.orig = govtVal.toFixed(2);
                });
            }

            refreshGsisRowTotal(pid);
            refreshPagibigRowTotal(pid);
            refreshAllowRowTotal(pid);
            refreshAllTotals();

            cell.classList.add('saved');
            setTimeout(() => cell.classList.remove('saved'), 900);
            showToast('Saved', field.replace(/_/g, ' ') + ' updated.', 'success');

        } else {
            _cellSaveError(cell, inp, res.error || 'Could not save.');
        }
    })
    .catch(err => {
        cell.classList.remove('saving');
        if (inp) {
            inp.readOnly = false;
            inp.value = parseFloat(cell.dataset.orig || '0') > 0
                ? parseFloat(cell.dataset.orig).toFixed(2) : '';
        }
        cell.classList.add('err-cell');
        setTimeout(() => cell.classList.remove('err-cell'), 2000);

        if (err?.__status === 403) {
            showToast('Locked', 'This period is finalized and cannot be edited.', 'error');
        } else if (err?.__status === 422) {
            const msgs = err.errors ? Object.values(err.errors).flat() : [];
            showToast('Validation Error', msgs[0] || err.message || 'Invalid value.', 'error');
        } else {
            showToast('Network Error', 'Could not connect to server.', 'error');
        }
    });
}

function _cellSaveError(cell, inp, msg) {
    cell.classList.remove('saving');
    if (inp) {
        inp.readOnly = false;
        inp.value = parseFloat(cell.dataset.orig || '0') > 0
            ? parseFloat(cell.dataset.orig).toFixed(2) : '';
    }
    cell.classList.add('err-cell');
    setTimeout(() => cell.classList.remove('err-cell'), 2000);
    showToast('Error', msg, 'error');
}

/* ═══════════════════════════════════════════════════════
   DELETE ROW
═══════════════════════════════════════════════════════ */
let _pendingDeletePid  = null;
let _pendingDeleteName = null;

function askDeleteRow(pid, name) {
    _pendingDeletePid  = pid;
    _pendingDeleteName = name;
    document.getElementById('delConfirmTitle').textContent = 'Remove ' + name + '?';
    document.getElementById('delConfirmMsg').textContent   =
        'This will remove ' + name + ' from the current remittance period. ' +
        'Re-generate payroll for this period to restore them.';
    document.getElementById('delConfirm').classList.add('show');
}

function cancelDelete() {
    _pendingDeletePid  = null;
    _pendingDeleteName = null;
    document.getElementById('delConfirm').classList.remove('show');
}

function executeDelete() {
    const pid  = _pendingDeletePid;
    const name = _pendingDeleteName;
    document.getElementById('delConfirm').classList.remove('show');
    if (!pid) return;

    document.querySelectorAll(`tr[data-pid="${pid}"]`).forEach(r => {
        r.style.transition = 'opacity .28s ease, transform .28s ease';
        r.style.opacity    = '0';
        r.style.transform  = 'translateX(16px)';
        setTimeout(() => r.remove(), 300);
    });

    delete PAYROLL_DATA[pid];
    setTimeout(refreshAllTotals, 320);

    fetch(`${HIDE_URL_BASE}${pid}`, {
        method : 'PATCH',
        headers: {
            'Content-Type':    'application/json',
            'X-CSRF-TOKEN':    CSRF,
            'X-Requested-With':'XMLHttpRequest',
        },
        body: JSON.stringify({ hidden: true }),
    }).catch(() => {});

    showToast('Removed', (name || 'Employee') + ' removed from this period.', 'info');
    _pendingDeletePid  = null;
    _pendingDeleteName = null;
}

/* ═══════════════════════════════════════════════════════
   TABS & SEARCH
═══════════════════════════════════════════════════════ */
function switchTab(name, el) {
    document.querySelectorAll('.rem-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.rem-tab-panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('panel-'+name).classList.add('active');
}

function filterTable(id, q) {
    const sq = q.toLowerCase();
    document.querySelectorAll('#'+id+' tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(sq) ? '' : 'none';
    });
}

/* ═══════════════════════════════════════════════════════
   DETAIL PANEL
═══════════════════════════════════════════════════════ */
function openPanel(id, evt) {
    if (evt && evt.target.closest('.ec')) return;
    const d = PAYROLL_DATA[id];
    if (!d) return;

    document.getElementById('dpTitle').textContent = d.last_name + ', ' + d.first_name;
    document.getElementById('dpSub').textContent   = d.position + ' · ' + d.department;
    document.getElementById('dpPeriod').textContent = 'Period: ' + d.period;

    const gsisLoans  = d.gsis_mpl+d.gsis_mpl_lite+d.gsis_policy+d.gsis_emergency+d.gsis_real_estate+d.gsis_computer+d.gsis_gfal+d.gsis_conso;
    const gsisTotal  = d.gsis_ee+d.gsis_ec+gsisLoans;
    const pagibigT   = d.pagibig_govt+d.pagibig_mpl+d.pagibig_calamity;
    const otherLoans = d.loan_dbp+d.loan_lbp+d.loan_cngwmpc+d.loan_paracle+d.overpayment;
    const otherDed   = d.other_deduction || 0;

    document.getElementById('dpBody').innerHTML = `
    <div class="dp-card" style="background:linear-gradient(135deg,#1a3a1a,#2d5a1b);margin-top:12px;">
        <div class="net-bar"><div class="lbl">Net Pay</div><div class="amt">${fmtPeso(d.net_pay)}</div><div class="per">${d.period}</div></div>
        <div class="dp-pills">
            <div class="dp-pill"><div class="pl">Gross</div><div class="pv">${fmtPeso(d.gross_salary)}</div></div>
            ${(d.other_deduction||0)>0 ? `<div class="dp-pill" style="background:#fffbeb;border-color:#f59e0b;"><div class="pl" style="color:#92400e;">${d.other_deduction_label||'Other Ded.'}</div><div class="pv" style="color:#92400e;">−${fmtPeso(d.other_deduction)}</div></div>` : ''}
            <div class="dp-pill"><div class="pl">Deductions</div><div class="pv r">${fmtPeso(d.total_deductions)}</div></div>
            <div class="dp-pill"><div class="pl">Allowances</div><div class="pv g">${fmtPeso(d.total_allowances)}</div></div>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-card-head"><div class="dp-icon"></div><p class="dp-card-title">Employee Information</p></div>
        <div class="dp-grid">
            <div class="dp-field"><label>Employee ID</label><p style="font-family:monospace;">${d.employee_id}</p></div>
            <div class="dp-field"><label>Gross Salary</label><p style="font-family:'JetBrains Mono',monospace;font-weight:700;">${fmtPeso(d.gross_salary)}</p></div>
            <div class="dp-field"><label>Department</label><p>${d.department}</p></div>
            <div class="dp-field"><label>Position</label><p>${d.position}</p></div>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-card-head"><div class="dp-icon" style="background:#e8f5e9;"></div><p class="dp-card-title">GSIS</p></div>
        <table class="dp-mini"><thead><tr><th>Item</th><th class="r">Amount</th><th class="r">% Gross</th></tr></thead><tbody>
            <tr><td>EE Share (9%)</td><td class="r">${fmtPeso(d.gsis_ee)}</td><td class="r">${pct(d.gsis_ee,d.gross_salary)}</td></tr>
            <tr><td style="color:#6b7280;">Gov't Share (12%) <em style="font-size:10px;">[info]</em></td><td class="r" style="color:#6b7280;">${fmtPeso(d.gsis_govt)}</td><td class="r" style="color:#6b7280;">${pct(d.gsis_govt,d.gross_salary)}</td></tr>
            <tr><td>EC Fund (₱100 fixed)</td><td class="r">${fmtPeso(d.gsis_ec)}</td><td class="r">—</td></tr>
            ${d.gsis_conso?`<tr><td>Conso Loan</td><td class="r">${fmtPeso(d.gsis_conso)}</td><td class="r">${pct(d.gsis_conso,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_policy?`<tr><td>Policy Loan</td><td class="r">${fmtPeso(d.gsis_policy)}</td><td class="r">${pct(d.gsis_policy,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_emergency?`<tr><td>Emergency</td><td class="r">${fmtPeso(d.gsis_emergency)}</td><td class="r">${pct(d.gsis_emergency,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_real_estate?`<tr><td>Real Estate</td><td class="r">${fmtPeso(d.gsis_real_estate)}</td><td class="r">${pct(d.gsis_real_estate,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_computer?`<tr><td>Computer Loan</td><td class="r">${fmtPeso(d.gsis_computer)}</td><td class="r">${pct(d.gsis_computer,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_gfal?`<tr><td>GFAL</td><td class="r">${fmtPeso(d.gsis_gfal)}</td><td class="r">${pct(d.gsis_gfal,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_mpl?`<tr><td>MPL</td><td class="r">${fmtPeso(d.gsis_mpl)}</td><td class="r">${pct(d.gsis_mpl,d.gross_salary)}</td></tr>`:''}
            ${d.gsis_mpl_lite?`<tr><td>MPL Lite</td><td class="r">${fmtPeso(d.gsis_mpl_lite)}</td><td class="r">${pct(d.gsis_mpl_lite,d.gross_salary)}</td></tr>`:''}
            <tr class="sub"><td>Total GSIS</td><td class="r">${fmtPeso(gsisTotal)}</td><td class="r">${pct(gsisTotal,d.gross_salary)}</td></tr>
        </tbody></table>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:0 14px 12px;">
        <div class="dp-card" style="margin:0;">
            <div class="dp-card-head"><div class="dp-icon" style="background:#ede9fe;"></div><p class="dp-card-title">Pag-IBIG</p></div>
            <table class="dp-mini"><tbody>
                <tr><td>Personal Share</td><td class="r">${fmtPeso(d.pagibig_govt)}</td></tr>
                <tr><td style="color:#6b7280;">Gov't Share <em style="font-size:10px;">[info]</em></td><td class="r" style="color:#6b7280;">₱200.00</td></tr>
                ${d.pagibig_mpl?`<tr><td>MPL Loan</td><td class="r">${fmtPeso(d.pagibig_mpl)}</td></tr>`:''}
                ${d.pagibig_calamity?`<tr><td>Calamity</td><td class="r">${fmtPeso(d.pagibig_calamity)}</td></tr>`:''}
                ${d.overpayment?`<tr><td>Overpayment</td><td class="r">${fmtPeso(d.overpayment)}</td></tr>`:''}
                <tr class="sub"><td>Total</td><td class="r">${fmtPeso(pagibigT)}</td></tr>
            </tbody></table>
        </div>
        <div class="dp-card" style="margin:0;">
            <div class="dp-card-head"><div class="dp-icon" style="background:#d1fae5;"></div><p class="dp-card-title">PhilHealth</p></div>
            <table class="dp-mini"><tbody>
                <tr><td>EE Share (2.5%)</td><td class="r">${fmtPeso(d.philhealth_ee)}</td></tr>
                <tr><td style="color:#6b7280;">Gov't Share <em style="font-size:10px;">[info]</em></td><td class="r" style="color:#6b7280;">${fmtPeso(d.philhealth_govt)}</td></tr>
                <tr class="sub"><td>Total Premium</td><td class="r">${fmtPeso(d.philhealth_ee+d.philhealth_govt)}</td></tr>
            </tbody></table>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-card-head"><div class="dp-icon" style="background:#fef3c7;"></div><p class="dp-card-title">Tax & Bank Loans</p></div>
        <table class="dp-mini"><tbody>
            <tr><td>Withholding Tax (BIR)</td><td class="r">${fmtPeso(d.withholding_tax)}</td></tr>
            ${d.loan_dbp?`<tr><td>DBP Loan</td><td class="r">${fmtPeso(d.loan_dbp)}</td></tr>`:''}
            ${d.loan_lbp?`<tr><td>LBP Loan</td><td class="r">${fmtPeso(d.loan_lbp)}</td></tr>`:''}
            ${d.loan_cngwmpc?`<tr><td>CNGWMPC</td><td class="r">${fmtPeso(d.loan_cngwmpc)}</td></tr>`:''}
            ${d.loan_paracle?`<tr><td>PARACLE</td><td class="r">${fmtPeso(d.loan_paracle)}</td></tr>`:''}
            ${(d.other_deduction||0)>0?`<tr style="background:#fffbeb;"><td style="color:#92400e;font-weight:700;">${d.other_deduction_label||'Other Deduction'}</td><td class="r" style="color:#92400e;font-weight:700;">${fmtPeso(d.other_deduction)}</td></tr>`:''}
            <tr class="sub"><td>Total</td><td class="r">${fmtPeso(d.withholding_tax+otherLoans+(d.other_deduction||0))}</td></tr>
        </tbody></table>
    </div>
    <div class="dp-card">
        <div class="dp-card-head"><div class="dp-icon" style="background:#dcfce7;"></div><p class="dp-card-title">Allowances</p></div>
        <table class="dp-mini"><tbody>
            <tr><td>PERA (₱2,000 fixed)</td><td class="r" style="color:#15803d;">${fmtPeso(d.allowance_pera)}</td></tr>
            ${d.allowance_rata?`<tr><td>RATA (₱9,500 HOO)</td><td class="r" style="color:#15803d;">${fmtPeso(d.allowance_rata)}</td></tr>`:''}
            ${d.allowance_ta?`<tr><td>TA (₱9,500 HOO)</td><td class="r" style="color:#15803d;">${fmtPeso(d.allowance_ta)}</td></tr>`:''}
            ${d.allowance_other?`<tr><td>Other</td><td class="r" style="color:#15803d;">${fmtPeso(d.allowance_other)}</td></tr>`:''}
            <tr class="sub"><td>Total Allowances</td><td class="r" style="color:#15803d;">${fmtPeso(d.total_allowances)}</td></tr>
        </tbody></table>
    </div>
    <div class="dp-card" style="margin-bottom:16px;">
        <div class="dp-card-head"><div class="dp-icon"></div><p class="dp-card-title">Pay Summary</p></div>
        <table class="dp-mini"><tbody>
            <tr><td style="color:#6b7280;">Gross Salary</td><td class="r">${fmtPeso(d.gross_salary)}</td></tr>
            <tr><td style="color:#6b7280;">+ Allowances</td><td class="r" style="color:#15803d;">${fmtPeso(d.total_allowances)}</td></tr>
            <tr><td style="color:#6b7280;">− Deductions</td><td class="r" style="color:#dc2626;">${fmtPeso(d.total_deductions)}</td></tr>
            <tr class="sub"><td style="font-size:13px;">NET PAY</td><td class="r" style="font-size:14px;color:#15803d;">${fmtPeso(d.net_pay)}</td></tr>
        </tbody></table>
    </div>`;

    document.getElementById('overlay').classList.add('show');
    document.getElementById('detailPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closePanel() {
    document.getElementById('detailPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
}

/* ═══════════════════════════════════════════════════════
   TOAST
═══════════════════════════════════════════════════════ */
let toastTimer;
function showToast(title, msg, type) {
    const map = {
        success:{ bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7' },
        error:  { bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12' },
        info:   { bg:'#dbeafe',c:'#2563eb',p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const s = map[type] || map.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    const icon = document.getElementById('toastIcon');
    icon.innerHTML = `<svg style="width:14px;height:14px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    icon.style.background = s.bg;
    const t = document.getElementById('toast');
    clearTimeout(toastTimer);
    t.classList.add('show');
    toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}

/* ═══════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    Object.entries(PAYROLL_DATA).forEach(([pid, d]) => {
        applyAutoCompute(pid, d);
    });
    refreshAllTotals();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closePanel();
        cancelDelete();
    }
});
</script>

</div>
@endsection