@extends('layouts.app')
@section('title', 'My Payslip')
@section('page-title', 'My Payslip')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

.pm-page { display: flex; flex-direction: column; min-height: calc(100vh - 120px); gap: 0; }

.breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #9ca3af; margin-bottom: 20px; flex-wrap: wrap;
}
.breadcrumb a { color: #9ca3af; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #1a3a1a; }
.breadcrumb .sep { color: #e5e7eb; }
.breadcrumb .current { color: #1a3a1a; font-weight: 700; }

.main-card {
    background: #fff; border-radius: 20px;
    border: 1px solid #e9ecef; box-shadow: 0 2px 20px rgba(0,0,0,.07);
    overflow: hidden; flex: 1;
}

.card-topbar {
    padding: 18px 26px 15px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
    border-bottom: 1px solid #f0f2f0;
    background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%);
}
.card-topbar-left { display: flex; align-items: center; gap: 12px; }
.card-topbar-icon {
    width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 8px rgba(26,58,26,.3);
}
.card-topbar-icon svg { width: 20px; height: 20px; color: #fff; }
.card-topbar-title { font-size: 16px; font-weight: 800; color: #111827; margin: 0; letter-spacing: -.3px; }
.card-topbar-sub { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }

.controls-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.period-select-wrap { position: relative; min-width: 220px; }
.period-select {
    width: 100%; padding: 10px 38px 10px 14px;
    font-size: 13px; font-weight: 600;
    border: 1.5px solid #e9ecef; border-radius: 11px;
    color: #111827; background: #fff;
    appearance: none; -webkit-appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center;
    transition: all .15s;
}
.period-select:focus { outline: none; border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }

.stats-bar {
    display: flex; gap: 12px; padding: 12px 26px;
    background: #f9fafb; border-bottom: 1px solid #f0f2f0;
    flex-wrap: wrap; align-items: center;
}
.stat-chip {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 14px; background: #fff; border: 1px solid #e9ecef;
    border-radius: 10px; font-size: 12px;
}
.stat-chip-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.stat-chip-label { color: #6b7280; font-weight: 500; }
.stat-chip-value { color: #111827; font-weight: 700; font-family: 'JetBrains Mono', monospace; }

.table-wrap { overflow-x: auto; }
table.pm-table { width: 100%; border-collapse: collapse; min-width: 900px; }
table.pm-table thead th {
    padding: 10px 14px; font-size: 10.5px; font-weight: 700;
    color: #6b7280; text-align: left; border-bottom: 1px solid #f0f2f0;
    background: #f9fafb; white-space: nowrap; text-transform: uppercase;
    letter-spacing: .4px;
}
table.pm-table thead th.num { text-align: right; }
table.pm-table tbody tr {
    border-bottom: 1px solid #f7f8f7;
    transition: background .12s;
    cursor: pointer;
}
table.pm-table tbody tr:hover { background: #f0fdf4; }
table.pm-table tbody tr.hidden-row { display: none; }
table.pm-table td { padding: 11px 14px; font-size: 12.5px; color: #111827; white-space: nowrap; }
table.pm-table td.num {
    text-align: right; font-family: 'JetBrains Mono', monospace;
    font-size: 12px; font-weight: 600;
}
table.pm-table td.muted { color: #9ca3af; font-size: 11px; }

.emp-badge { display: inline-flex; align-items: center; gap: 8px; }
.emp-avatar {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; letter-spacing: -.3px;
}
.emp-name { font-weight: 700; font-size: 12.5px; color: #111827; }
.emp-id { font-size: 10px; color: #9ca3af; margin-top: 1px; }

.net-chip {
    display: inline-block; padding: 4px 10px; border-radius: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11.5px; font-weight: 700;
    background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
}

.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
}
.status-badge.draft     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-badge.finalized { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

.tbl-btns { display: flex; gap: 6px; align-items: center; }
.btn-tbl {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 11px; font-size: 10.5px; font-weight: 700;
    border-radius: 8px; cursor: pointer; transition: all .15s;
    border: 1.5px solid transparent; white-space: nowrap;
    text-decoration: none;
}
.btn-tbl-pdf  { color: #065f46; background: #f0fdf4; border-color: #a7f3d0; }
.btn-tbl-pdf:hover  { background: #dcfce7; }
.btn-tbl svg { width: 11px; height: 11px; }

.no-records { text-align: center; padding: 80px 20px; color: #9ca3af; }
.no-records-icon {
    width: 64px; height: 64px; border-radius: 16px; background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; color: #9ca3af;
}
.no-records-icon svg { width: 32px; height: 32px; }
.no-records h3 { font-size: 18px; font-weight: 700; color: #6b7280; margin: 0 0 8px; }
.no-records p  { font-size: 13px; color: #9ca3af; margin: 0; }

/* ══ OVERLAY ══ */
#pmOverlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.3);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 40; opacity: 0; pointer-events: none;
    transition: opacity .3s ease;
}
#pmOverlay.show { opacity: 1; pointer-events: all; }

/* ══════════════════════════════════════════
   SLIDE-IN PANEL
══════════════════════════════════════════ */
#payslipPanel {
    position: fixed; top: 0; right: 0; bottom: 0; z-index: 50;
    width: 94vw; min-width: 900px; max-width: 1400px;
    display: flex; flex-direction: column;
    pointer-events: none;
    transform: translateX(100%);
    transition: transform .36s cubic-bezier(.32,.72,0,1);
}
#payslipPanel.open { pointer-events: all; transform: translateX(0); }

.pp-box {
    background: #f4f6f4; width: 100%; height: 100%;
    display: flex; flex-direction: column;
    box-shadow: -16px 0 70px rgba(0,0,0,.25);
    overflow: hidden;
}

/* ── Panel Top Bar ── */
.pp-topbar {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 0 24px;
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 16px; flex-shrink: 0;
    min-height: 56px;
}
.pp-topbar-left { display: flex; align-items: center; gap: 10px; }
.pp-back-btn {
    width: 32px; height: 32px; border-radius: 8px;
    border: 1.5px solid #e5e7eb; background: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #6b7280; transition: all .15s; flex-shrink: 0;
}
.pp-back-btn:hover { background: #f3f4f6; color: #111827; }
.pp-back-btn svg { width: 16px; height: 16px; }
.pp-topbar-title {
    font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -.2px;
}
.pp-topbar-title span { color: #2d5a1b; }
.pp-topbar-sub { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }
.pp-topbar-actions { display: flex; align-items: center; gap: 8px; }

/* Action Buttons */
.btn-preview-pdf {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px; font-size: 12px; font-weight: 700;
    color: #065f46; background: #f0fdf4;
    border: 1.5px solid #a7f3d0; border-radius: 9px; cursor: pointer;
    transition: all .15s; white-space: nowrap; text-decoration: none;
}
.btn-preview-pdf:hover { background: #dcfce7; }
.btn-preview-pdf svg { width: 13px; height: 13px; }

/* ── Panel Body: two-column split ── */
.pp-split {
    display: flex; flex: 1; overflow: hidden;
    min-height: 0;
}

/* LEFT: Data Form */
.pp-form-panel {
    width: 320px; flex-shrink: 0;
    background: #fff; border-right: 1px solid #e5e7eb;
    display: flex; flex-direction: column;
    overflow: hidden;
}
.pp-form-header {
    padding: 14px 18px 12px;
    border-bottom: 1px solid #f0f2f0;
    background: #fafafa; flex-shrink: 0;
}
.pp-form-title { font-size: 12px; font-weight: 800; color: #111827; margin: 0 0 8px; }

.pp-form-body {
    flex: 1; overflow-y: auto;
    scrollbar-width: thin; scrollbar-color: #d1d5db transparent;
}
.pp-form-body::-webkit-scrollbar { width: 4px; }
.pp-form-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }

/* Form Section Groups */
.pf-section { border-bottom: 1px solid #f0f2f0; }
.pf-section-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 18px; cursor: pointer;
    background: #fafafa; user-select: none;
    transition: background .12s;
}
.pf-section-header:hover { background: #f3f4f6; }
.pf-section-title {
    font-size: 11px; font-weight: 800; color: #374151;
    display: flex; align-items: center; gap: 6px;
    text-transform: uppercase; letter-spacing: .5px;
}
.pf-section-dot { width: 7px; height: 7px; border-radius: 50%; }
.pf-section-total {
    font-size: 11px; font-weight: 700; font-family: 'JetBrains Mono', monospace;
    color: #111827;
}
.pf-section-chevron { width: 13px; height: 13px; color: #9ca3af; transition: transform .2s; }
.pf-section-header.collapsed .pf-section-chevron { transform: rotate(-90deg); }
.pf-section-body { padding: 6px 0 4px; }
.pf-section-body.hidden { display: none; }

/* Sub-group */
.pf-subgroup { padding: 0 0 2px; }
.pf-subgroup-label {
    padding: 6px 18px 4px;
    font-size: 9px; font-weight: 800; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .8px;
}

/* Field row */
.pf-field-row {
    display: flex; align-items: center;
    padding: 5px 18px; gap: 8px;
    transition: background .1s;
}
.pf-field-row:hover { background: #f9fafb; }
.pf-field-label {
    flex: 1; font-size: 11.5px; color: #374151;
    font-weight: 500; min-width: 0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pf-field-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.pf-field-input {
    width: 90px; padding: 5px 8px;
    font-size: 12px; font-weight: 600; font-family: 'JetBrains Mono', monospace;
    border: 1.5px solid #e5e7eb; border-radius: 7px;
    color: #111827; background: #f9fafb; outline: none; text-align: right;
    cursor: not-allowed;
}
.pf-note-badge {
    font-size: 9px; font-weight: 700; color: #9ca3af;
    padding: 2px 6px; background: #f3f4f6;
    border-radius: 5px; white-space: nowrap;
    text-transform: lowercase;
}
.pf-note-badge.has-val { color: #065f46; background: #dcfce7; }

/* Total row */
.pf-total-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 18px 10px;
    background: #f9fafb;
    border-top: 1px solid #f0f2f0;
    margin-top: 4px;
}
.pf-total-label { font-size: 11px; font-weight: 700; color: #374151; }
.pf-total-value {
    font-size: 13px; font-weight: 800; font-family: 'JetBrains Mono', monospace;
    color: #111827;
}
.pf-total-value.green { color: #166534; }
.pf-total-value.red   { color: #b91c1c; }

/* RIGHT: Live Preview */
.pp-preview-panel {
    flex: 1; display: flex; flex-direction: column;
    min-width: 0; overflow: hidden; background: #f0f2f0;
}
.pp-preview-header {
    padding: 10px 20px;
    background: #fff; border-bottom: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.pp-preview-label {
    font-size: 11px; font-weight: 800; color: #374151;
    text-transform: uppercase; letter-spacing: .5px;
    display: flex; align-items: center; gap: 6px;
}
.pp-preview-label svg { width: 13px; height: 13px; color: #2d5a1b; }

.pp-preview-body {
    flex: 1; overflow-y: auto; padding: 24px;
    display: flex; justify-content: center;
    scrollbar-width: thin; scrollbar-color: #d1d5db transparent;
}
.pp-preview-body::-webkit-scrollbar { width: 5px; }
.pp-preview-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }

/* ══ PAYSLIP DOCUMENT CARD ══ */
.slip-doc {
    background: #fff; width: 100%; max-width: 560px;
    border-radius: 12px;
    box-shadow: 0 4px 28px rgba(0,0,0,.12), 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12px;
}

.slip-header-band {
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    padding: 16px 20px 14px; text-align: center;
}
.slip-header-band .sh-gov {
    font-size: 8.5px; color: rgba(255,255,255,.55);
    letter-spacing: .8px; text-transform: uppercase; margin-bottom: 3px;
}
.slip-header-band .sh-dept {
    font-size: 13px; font-weight: 800; color: #fff; letter-spacing: -.2px;
}
.slip-header-band .sh-office { font-size: 9.5px; color: rgba(255,255,255,.6); margin-top: 2px; }
.slip-header-band .sh-badge {
    display: inline-block; margin-top: 8px;
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.28);
    color: #fff; font-size: 8px; font-weight: 800;
    letter-spacing: 2.5px; text-transform: uppercase;
    padding: 3px 14px; border-radius: 4px;
}

.slip-period-bar {
    background: #f0fdf4; border-bottom: 1px solid #dcfce7;
    padding: 7px 18px;
    display: flex; align-items: center; justify-content: space-between;
}
.slip-period-bar .spb-label {
    font-size: 8px; font-weight: 700; color: #166534;
    text-transform: uppercase; letter-spacing: .6px;
}
.slip-period-bar .spb-val {
    font-size: 10.5px; font-weight: 800; color: #14532d;
    font-family: 'JetBrains Mono', monospace;
}

/* Employee info grid */
.slip-emp-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0; border-bottom: 1.5px solid #f1f5f9;
}
.slip-emp-cell {
    padding: 10px 18px; border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}
.slip-emp-cell:nth-child(2n) { border-right: none; }
.slip-emp-cell:last-child { grid-column: 1 / -1; border-right: none; border-bottom: none; }
.slip-emp-label {
    font-size: 7.5px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px;
}
.slip-emp-val { font-size: 11.5px; font-weight: 800; color: #0f172a; }

/* Table rows */
.slip-section-hd {
    padding: 8px 18px 5px;
    font-size: 7.5px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 1px;
    display: flex; align-items: center; gap: 5px;
    border-top: 1px solid #f1f5f9;
}
.slip-section-hd-dot { width: 5px; height: 5px; border-radius: 50%; }

.slip-row {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 3px 18px;
}
.slip-row-label { font-size: 10.5px; color: #475569; }
.slip-row-val {
    font-size: 11px; font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    color: #0f172a;
}
.slip-row-val.zero { color: #cbd5e1; font-weight: 400; }
.slip-row-label.primary { font-size: 12px; font-weight: 800; color: #0f172a; }
.slip-row-val.primary   { font-size: 12px; font-weight: 800; color: #0f172a; }

/* Totals band */
.slip-totals-band {
    background: #f8fafc; border-top: 1.5px solid #e2e8f0;
    padding: 8px 18px;
}
.slip-totals-row {
    display: flex; justify-content: space-between;
    align-items: center; padding: 2px 0;
}
.slip-totals-row .stl { font-size: 10px; color: #64748b; font-weight: 500; }
.slip-totals-row .stv {
    font-size: 11px; font-weight: 700; color: #0f172a;
    font-family: 'JetBrains Mono', monospace;
}
.slip-totals-row .stv.green { color: #166534; }
.slip-totals-row .stv.red   { color: #b91c1c; }

/* Net pay */
.slip-net-band {
    margin: 8px 14px 12px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
    border: 1.5px solid #eab308;
    padding: 12px 16px;
    display: flex; align-items: center; justify-content: space-between;
}
.slip-net-band .snl { font-size: 9px; font-weight: 800; color: #713f12; text-transform: uppercase; letter-spacing: .8px; }
.slip-net-band .snv { font-size: 18px; font-weight: 800; color: #713f12; font-family: 'JetBrains Mono', monospace; }

/* Signatory */
.slip-sig {
    text-align: center; padding: 8px 18px 14px;
    border-top: 1px dashed #e2e8f0; background: #fafafa;
}
.slip-sig-line { width: 120px; height: 1px; background: #cbd5e1; margin: 0 auto 6px; }
.slip-sig-name { font-size: 10px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: .4px; }
.slip-sig-title { font-size: 9px; color: #94a3b8; margin-top: 2px; }

/* ── Toast ── */
#toast-container {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    display: flex; flex-direction: column; gap: 8px;
}
.toast {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px; border-radius: 12px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    border: 1.5px solid transparent;
    transform: translateY(20px); opacity: 0;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
    min-width: 240px;
}
.toast.show    { transform: translateY(0); opacity: 1; }
.toast.success { background: #d1fae5; border-color: #10b981; color: #065f46; }
.toast.error   { background: #fee2e2; border-color: #ef4444; color: #991b1b; }
.toast svg { width: 16px; height: 16px; flex-shrink: 0; }

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1100px) {
    #payslipPanel { width: 98vw; min-width: 0; }
    .pp-form-panel { width: 280px; }
}
@media (max-width: 768px) {
    .pp-split { flex-direction: column; }
    .pp-form-panel { width: 100%; height: 50%; border-right: none; border-bottom: 1px solid #e5e7eb; }
    .card-topbar { flex-direction: column; align-items: stretch; }
    .controls-row { flex-direction: column; align-items: stretch; }
}
</style>

<div class="breadcrumb">
    <a href="#">Payroll</a>
    <span class="sep">›</span>
    <span class="current">My Payslip</span>
</div>

@php
    $currentPeriod = $periods->firstWhere('period_id', $selectedPeriodId);
@endphp

<div class="pm-page">
    <div class="main-card">

        <div class="card-topbar">
            <div class="card-topbar-left">
                <div class="card-topbar-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="card-topbar-title">My Payslip</p>
                    <p class="card-topbar-sub">View and export your payslip by period</p>
                </div>
            </div>

            <div class="controls-row">
                <div class="period-select-wrap">
                    <select class="period-select" id="periodSelect"
                        onchange="window.location.href='{{ route('payroll.payslip') }}?period_id='+this.value">
                        <option value="">— Select Period —</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}"
                                {{ $selectedPeriodId == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($records->isNotEmpty())
        <div class="stats-bar">
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#10b981;"></div>
                <span class="stat-chip-label">Gross Salary</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('gross_salary'), 2) }}</span>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#f59e0b;"></div>
                <span class="stat-chip-label">Net Pay</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('net_pay'), 2) }}</span>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#ef4444;"></div>
                <span class="stat-chip-label">Total Deductions</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('total_deductions'), 2) }}</span>
            </div>
            @if($currentPeriod)
            <div style="margin-left:auto;">
                <span class="status-badge {{ strtolower($currentPeriod->status) }}">
                    {{ $currentPeriod->status }}
                </span>
            </div>
            @endif
        </div>

        <div class="table-wrap">
            <table class="pm-table" id="pmTable">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th>Employee</th>
                        <th>Position</th>
                        <th class="num">Gross Salary</th>
                        <th class="num">Total Deductions</th>
                        <th class="num">Net Pay</th>
                        <th class="num">GSIS EE</th>
                        <th class="num">Pag-Ibig</th>
                        <th class="num">PhilHealth</th>
                        <th class="num">W/Tax</th>
                        <th class="num">PERA</th>
                        <th style="width:80px;">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $r)
                    @php
                        $lastName  = strtoupper($r->employee?->last_name ?? '—');
                        $firstName = $r->employee?->first_name ?? '';
                        $fullName  = $lastName.', '.$firstName;
                        $initials  = substr($lastName,0,1).substr($firstName,0,1);
                        $posCode   = $r->designation ?? $r->employee?->position?->position_code ?? 'N/A';
                        $rid       = $r->payroll_id;
                    @endphp
                    <tr data-name="{{ strtolower($fullName) }}"
                        data-id="{{ $r->user_id }}"
                        data-rid="{{ $rid }}"
                        onclick="openPayslipPanel({{ $rid }})">
                        <td class="muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="emp-badge">
                                <div class="emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="emp-name">{{ $fullName }}</div>
                                    <div class="emp-id">ID: {{ $r->user_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $posCode }}</td>
                        <td class="num">{{ number_format($r->gross_salary, 2) }}</td>
                        <td class="num" style="color:#ef4444;">{{ number_format($r->total_deductions, 2) }}</td>
                        <td><span class="net-chip">₱{{ number_format($r->net_pay, 2) }}</span></td>
                        <td class="num">{{ $r->gsis_ee > 0 ? number_format($r->gsis_ee, 2) : '—' }}</td>
                        <td class="num">{{ ($r->pagibig_govt ?? 0) > 0 ? number_format($r->pagibig_govt, 2) : '—' }}</td>
                        <td class="num">{{ ($r->philhealth_ee ?? 0) > 0 ? number_format($r->philhealth_ee, 2) : '—' }}</td>
                        <td class="num">{{ ($r->withholding_tax ?? 0) > 0 ? number_format($r->withholding_tax, 2) : '—' }}</td>
                        <td class="num">{{ ($r->allowance_pera ?? 0) > 0 ? number_format($r->allowance_pera, 2) : '—' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="tbl-btns">
                                <a href="{{ route('payroll.payslip.pdf', $r->period_id) }}?user_id={{ $r->user_id }}"
                                    target="_blank" class="btn-tbl btn-tbl-pdf">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="no-records">
            <div class="no-records-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3>No Records Found</h3>
            <p>Select a payroll period from the dropdown above.</p>
        </div>
        @endif

    </div>
</div>

<div id="pmOverlay" onclick="closePayslipPanel()"></div>

{{-- ═══════════════════════════════════════════════
     SLIDE-IN PAYSLIP PANEL — Read-only UI
═══════════════════════════════════════════════ --}}
<div id="payslipPanel">
    <div class="pp-box">

        {{-- Top Action Bar --}}
        <div class="pp-topbar">
            <div class="pp-topbar-left">
                <button class="pp-back-btn" onclick="closePayslipPanel()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div>
                    <div class="pp-topbar-title">
                        Payslip Details – <span id="ppNameTitle">—</span>
                    </div>
                    <div class="pp-topbar-sub" id="ppMetaSub">—</div>
                </div>
            </div>

            <div class="pp-topbar-actions" id="ppActions">
                <a class="btn-preview-pdf" id="btnPreviewPdf" href="#" target="_blank">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Preview Printable PDF
                </a>
            </div>
        </div>

        {{-- Split body --}}
        <div class="pp-split">

            {{-- LEFT: Payslip Data Form (Read-only) --}}
            <div class="pp-form-panel">
                <div class="pp-form-header">
                    <div class="pp-form-title">Data Breakdown</div>
                </div>

                <div class="pp-form-body" id="pfBody">

                    {{-- EARNINGS --}}
                    <div class="pf-section" id="sec-earnings">
                        <div class="pf-section-header" onclick="toggleSection('earnings')">
                            <div class="pf-section-title">
                                <span class="pf-section-dot" style="background:#16a34a;"></span>
                                Earnings
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="pf-section-total" id="sec-total-earnings">—</span>
                                <svg class="pf-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="pf-section-body" id="secbody-earnings">
                            <div class="pf-field-row" data-label="gross salary">
                                <span class="pf-field-label">Gross Salary</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gross_salary" data-field="gross_salary" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="pera allowance">
                                <span class="pf-field-label">PERA</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_allowance_pera" data-field="allowance_pera" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="rata allowance">
                                <span class="pf-field-label">RATA</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_allowance_rata" data-field="allowance_rata" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="transportation allowance ta">
                                <span class="pf-field-label">Transportation Allowance (TA)</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_allowance_ta" data-field="allowance_ta" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="other allowance">
                                <span class="pf-field-label">Other Allowance</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_allowance_other" data-field="allowance_other" disabled>
                                </div>
                            </div>
                            <div class="pf-total-row">
                                <span class="pf-total-label">Total Earnings</span>
                                <span class="pf-total-value green" id="pf_total_earnings">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- DEDUCTIONS --}}
                    <div class="pf-section" id="sec-deductions">
                        <div class="pf-section-header" onclick="toggleSection('deductions')">
                            <div class="pf-section-title">
                                <span class="pf-section-dot" style="background:#ef4444;"></span>
                                Deductions
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="pf-section-total" id="sec-total-deductions">—</span>
                                <svg class="pf-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="pf-section-body" id="secbody-deductions">

                            <div class="pf-subgroup-label">Statutory Deductions</div>

                            <div class="pf-field-row" data-label="withholding tax">
                                <span class="pf-field-label">Withholding Tax</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_withholding_tax" data-field="withholding_tax" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="gsis employee share">
                                <span class="pf-field-label">GSIS Premium (9%)</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_ee" data-field="gsis_ee" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="pagibig pag-ibig">
                                <span class="pf-field-label">PAG-IBIG</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_pagibig_govt" data-field="pagibig_govt" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="philhealth">
                                <span class="pf-field-label">PhilHealth (2.5%)</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_philhealth_ee" data-field="philhealth_ee" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="medicare">
                                <span class="pf-field-label">Medicare</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_ec" data-field="gsis_ec" disabled>
                                </div>
                            </div>

                            <div class="pf-subgroup-label" style="margin-top:4px;">Cooperative Loans</div>

                            <div class="pf-field-row" data-label="ucpb">
                                <span class="pf-field-label">UCPB</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_loan_cngwmpc" data-field="loan_cngwmpc" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="mpl loan">
                                <span class="pf-field-label">MPL</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_mpl" data-field="gsis_mpl" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="cbcn">
                                <span class="pf-field-label">CBCN</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_conso" data-field="gsis_conso" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="mslap">
                                <span class="pf-field-label">MSLAP</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_gfal" data-field="gsis_gfal" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="lbp loan lbp">
                                <span class="pf-field-label">LBP</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_loan_lbp" data-field="loan_lbp" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="dbp loan">
                                <span class="pf-field-label">DBP</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_loan_dbp" data-field="loan_dbp" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="cngwmpc">
                                <span class="pf-field-label">CNGWMPC</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_loan_paracle" data-field="loan_paracle" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="uoli">
                                <span class="pf-field-label">UOLI</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_overpayment" data-field="overpayment" disabled>
                                </div>
                            </div>

                            <div class="pf-subgroup-label" style="margin-top:4px;">GSIS Loans</div>

                            <div class="pf-field-row" data-label="gsis policy loan">
                                <span class="pf-field-label">GSIS salary Loan</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_policy" data-field="gsis_policy" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="gsis policy loan">
                                <span class="pf-field-label">GSIS Policy Loan</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_emergency" data-field="gsis_emergency" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="gsis real estate loan">
                                <span class="pf-field-label">GSIS Real Estate Loan</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_real_estate" data-field="gsis_real_estate" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="gsis mpl lite">
                                <span class="pf-field-label">GSIS Em. Loan</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_mpl_lite" data-field="gsis_mpl_lite" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="gsis computer loan">
                                <span class="pf-field-label">GSIS Educ Loan</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_gsis_computer" data-field="gsis_computer" disabled>
                                </div>
                            </div>

                            <div class="pf-subgroup-label" style="margin-top:4px;">Other</div>

                            <div class="pf-field-row" data-label="pagibig pag-ibig mpl">
                                <span class="pf-field-label">Nursery</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_pagibig_mpl" data-field="pagibig_mpl" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="pagibig calamity loan">
                                <span class="pf-field-label">PAG IBIG Loyalty Card</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_pagibig_calamity" data-field="pagibig_calamity" disabled>
                                </div>
                            </div>
                            <div class="pf-field-row" data-label="custom other deduction">
                                <span class="pf-field-label" id="pf_other_ded_label_display">Custom Deduction</span>
                                <div class="pf-field-right">
                                    <input type="text" class="pf-field-input" id="f_other_deduction" data-field="other_deduction" disabled>
                                </div>
                            </div>

                            <div class="pf-total-row">
                                <span class="pf-total-label">Total Deduction</span>
                                <span class="pf-total-value red" id="pf_total_deductions">—</span>
                            </div>
                            <div class="pf-total-row" style="background:#fff;border-top:none;padding-top:4px;">
                                <span class="pf-total-label" style="font-weight:800;color:#0f172a;">Net Pay</span>
                                <span class="pf-total-value" style="font-size:14px;color:#166534;" id="pf_net_pay">—</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT: Live PDF Preview --}}
            <div class="pp-preview-panel">
                <div class="pp-preview-header">
                    <div class="pp-preview-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        PDF Preview
                    </div>
                </div>

                <div class="pp-preview-body">
                    <div class="slip-doc" id="slipDoc">
                        <div style="text-align:center;padding:60px 20px;color:#9ca3af;font-size:12px;">
                            Select an entry to preview your payslip.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
const SELECTED_PERIOD_ID = {{ $selectedPeriodId ?? 'null' }};

const RECORDS = {!! \Illuminate\Support\Js::from(
    $records->mapWithKeys(function($r) use ($currentPeriod) {
        $empName     = strtoupper($r->employee?->last_name ?? '—') . ', ' . ($r->employee?->first_name ?? '');
        $posCode     = $r->designation ?? $r->employee?->position?->position_code ?? 'N/A';
        $periodLabel = $r->period?->period_label ?? $currentPeriod?->period_label ?? '—';
        $sigName     = strtoupper($currentPeriod?->sig_clerk_name ?? 'MELINDA R. BARCELONA');
        $sigTitle    = $currentPeriod?->sig_clerk_title ?? 'AO V / Payroll Clerk';

        return [(string)$r->payroll_id => [
            'record_id'             => (int)$r->payroll_id,
            'user_id'               => (string)$r->user_id,
            'period_id'             => (int)$r->period_id,
            'emp_name'              => $empName,
            'position'              => $posCode,
            'period_label'          => $periodLabel,
            'sig_name'              => $sigName,
            'sig_clerk_title'       => $sigTitle,
            'gross_salary'          => (float)$r->gross_salary,
            'gsis_ee'               => (float)($r->gsis_ee ?? 0),
            'gsis_govt'             => (float)($r->gsis_govt ?? 0),
            'gsis_ec'               => (float)($r->gsis_ec ?? 0),
            'gsis_policy'           => (float)($r->gsis_policy ?? 0),
            'gsis_emergency'        => (float)($r->gsis_emergency ?? 0),
            'gsis_real_estate'      => (float)($r->gsis_real_estate ?? 0),
            'gsis_mpl'              => (float)($r->gsis_mpl ?? 0),
            'gsis_mpl_lite'         => (float)($r->gsis_mpl_lite ?? 0),
            'gsis_gfal'             => (float)($r->gsis_gfal ?? 0),
            'gsis_computer'         => (float)($r->gsis_computer ?? 0),
            'gsis_conso'            => (float)($r->gsis_conso ?? 0),
            'pagibig_ee'            => (float)($r->pagibig_ee ?? 0),
            'pagibig_govt'          => (float)($r->pagibig_govt ?? 0),
            'pagibig_mpl'           => (float)($r->pagibig_mpl ?? 0),
            'pagibig_calamity'      => (float)($r->pagibig_calamity ?? 0),
            'philhealth_ee'         => (float)($r->philhealth_ee ?? 0),
            'philhealth_govt'       => (float)($r->philhealth_govt ?? 0),
            'withholding_tax'       => (float)($r->withholding_tax ?? 0),
            'loan_dbp'              => (float)($r->loan_dbp ?? 0),
            'loan_lbp'              => (float)($r->loan_lbp ?? 0),
            'loan_cngwmpc'          => (float)($r->loan_cngwmpc ?? 0),
            'loan_paracle'          => (float)($r->loan_paracle ?? 0),
            'overpayment'           => (float)($r->overpayment ?? 0),
            'other_deduction'       => (float)($r->other_deduction ?? 0),
            'other_deduction_label' => $r->other_deduction_label ?? '',
            'allowance_pera'        => (float)($r->allowance_pera ?? 0),
            'allowance_rata'        => (float)($r->allowance_rata ?? 0),
            'allowance_ta'          => (float)($r->allowance_ta ?? 0),
            'allowance_other'       => (float)($r->allowance_other ?? 0),
            'total_deductions'      => (float)($r->total_deductions ?? 0),
            'total_allowances'      => (float)($r->total_allowances ?? 0),
            'net_pay'               => (float)($r->net_pay ?? 0),
        ]];
    })->toArray()
) !!};

const DEDUCTION_FIELDS = [
    'gsis_ee','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
    'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
    'pagibig_govt','pagibig_mpl','pagibig_calamity',
    'philhealth_ee',
    'withholding_tax','loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle',
    'overpayment','other_deduction',
];
const ALLOWANCE_FIELDS = ['allowance_pera','allowance_rata','allowance_ta','allowance_other'];

let currentRecordId = null;

// ── Format helpers ─────────────────────────────────────────────────────────
function fmt(v) {
    return parseFloat(v || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}
function parseVal(str) {
    return parseFloat((str || '0').replace(/,/g, '')) || 0;
}

// ── Fill form inputs from a record ────────────────────────────────────────
function populateForm(r) {
    document.querySelectorAll('#pfBody .pf-field-input[data-field]').forEach(el => {
        const f = el.dataset.field;
        const v = r[f] || 0;
        el.value = fmt(v);
    });
    refreshFormTotals(r);
}

function refreshFormTotals(r) {
    const gross = r.gross_salary || 0;
    const alw   = ALLOWANCE_FIELDS.reduce((s, f) => s + (r[f] || 0), 0);
    const ded   = DEDUCTION_FIELDS.reduce((s, f) => s + (r[f] || 0), 0);
    const net   = gross - ded + alw;

    el('pf_total_earnings').textContent  = '₱' + fmt(gross + alw);
    el('pf_total_deductions').textContent = '₱' + fmt(ded);
    el('pf_net_pay').textContent         = '₱' + fmt(net);
    el('sec-total-earnings').textContent  = '₱' + fmt(gross);
    el('sec-total-deductions').textContent = '₱' + fmt(ded);
}

function el(id) { return document.getElementById(id); }

// ── Build payslip document HTML ───────────────────────────────────────────
function buildSlipDoc(r) {
    const gross = r.gross_salary || 0;
    const pera  = r.allowance_pera || 0;
    const rata  = r.allowance_rata || 0;
    const ta    = r.allowance_ta || 0;
    const oa    = r.allowance_other || 0;
    const ded   = r.total_deductions || 0;
    const net   = r.net_pay || 0;
    const alw   = pera + rata + ta + oa;

    function row(label, field, val) {
        const isZero = !val || val <= 0;
        return `<div class="slip-row">
            <span class="slip-row-label">${label}</span>
            <span class="slip-row-val${isZero ? ' zero' : ''}">${isZero ? '—' : fmt(val)}</span>
        </div>`;
    }

    return `
    <div class="slip-header-band">
        <div class="sh-gov">Republic of the Philippines</div>
        <div class="sh-dept">Province of Camarines Norte · DAET</div>
        <div class="sh-office">Office of the Provincial Agriculturist</div>
        <div class="sh-badge">Pay Slip</div>
    </div>

    <div class="slip-period-bar">
        <span class="spb-label">For the Period</span>
        <span class="spb-val">${r.period_label}</span>
    </div>

    <div class="slip-emp-grid">
        <div class="slip-emp-cell" style="grid-column:1/-1;">
            <div class="slip-emp-label">Name</div>
            <div class="slip-emp-val">${r.emp_name}</div>
        </div>
        <div class="slip-emp-cell">
            <div class="slip-emp-label">Position</div>
            <div class="slip-emp-val">${r.position}</div>
        </div>
        <div class="slip-emp-cell">
            <div class="slip-emp-label">Employee ID</div>
            <div class="slip-emp-val">${r.user_id}</div>
        </div>
    </div>

    <div class="slip-section-hd">
        <span class="slip-section-hd-dot" style="background:#16a34a;"></span>
        Gross Salary
    </div>
    <div class="slip-row">
        <span class="slip-row-label primary">Gross Salary</span>
        <span class="slip-row-val primary" id="slipval_gross">${fmt(gross)}</span>
    </div>
    ${pera > 0 ? `<div class="slip-row"><span class="slip-row-label">Less Deduction</span><span class="slip-row-val"></span></div>` : ''}
    ${row('UCPB', 'loan_cngwmpc', r.loan_cngwmpc)}
    ${row('PERA', 'allowance_pera', pera)}

    <div class="slip-section-hd">
        <span class="slip-section-hd-dot" style="background:#2563eb;"></span>
        Deductions
    </div>
    ${row('GSIS EE', 'gsis_ee', r.gsis_ee)}
    ${row('GSIS Premium', 'gsis_ec', r.gsis_ec)}
    ${row('Withholding Tax', 'withholding_tax', r.withholding_tax)}
    ${row('GSIS salary Loan', 'gsis_policy', r.gsis_policy)}
    ${row('GSIS Policy Loan', 'gsis_emergency', r.gsis_emergency)}
    ${row('Medicare', 'philhealth_ee', r.philhealth_ee)}
    ${row('PAG-IBIG', 'pagibig_govt', r.pagibig_govt)}
    ${row('MPL', 'gsis_mpl', r.gsis_mpl)}
    ${row('CBCN', 'gsis_conso', r.gsis_conso)}
    ${row('CNGWMPC', 'loan_paracle', r.loan_paracle)}
    ${row('DBP', 'loan_dbp', r.loan_dbp)}
    ${row('LBP', 'loan_lbp', r.loan_lbp)}
    ${row('GSIS Real Estate Loan', 'gsis_real_estate', r.gsis_real_estate)}
    ${row('Nursery', 'pagibig_mpl', r.pagibig_mpl)}
    ${row('GSIS Em. Loan', 'gsis_mpl_lite', r.gsis_mpl_lite)}
    ${row('GSIS Educ Loan', 'gsis_computer', r.gsis_computer)}
    ${row('PAG IBIG Loyalty Card', 'pagibig_calamity', r.pagibig_calamity)}

    <div class="slip-totals-band">
        <div class="slip-totals-row">
            <span class="stl">TOTAL DEDUCTION</span>
            <span class="stv red" id="slipval_ded">${fmt(ded)}</span>
        </div>
        <div class="slip-totals-row" style="margin-top:4px;">
            <span class="stl" style="font-weight:700;color:#0f172a;">NET PAY</span>
            <span class="stv" style="font-size:13px;color:#0f172a;font-weight:800;" id="slipval_net">${fmt(net)}</span>
        </div>
    </div>

    <div class="slip-sig">
        <div class="slip-sig-line"></div>
        <div class="slip-sig-name" id="slipval_signame">${r.sig_name || '—'}</div>
        <div class="slip-sig-title">${r.sig_clerk_title || 'AO V / Payroll Clerk'}</div>
    </div>`;
}

// ── Section toggle ─────────────────────────────────────────────────────────
function toggleSection(name) {
    const hd   = document.querySelector(`#sec-${name} .pf-section-header`);
    const body = el('secbody-' + name);
    if (!hd || !body) return;
    const collapsed = hd.classList.toggle('collapsed');
    body.classList.toggle('hidden', collapsed);
}

// ── Open Panel ─────────────────────────────────────────────────────────────
function openPayslipPanel(recordId) {
    const key = String(recordId);
    const r   = RECORDS[key];
    if (!r) { showToast('Record not found.', 'error'); return; }

    currentRecordId = key;

    // Header
    el('ppNameTitle').textContent = r.emp_name;
    el('ppMetaSub').textContent   = `Employee ID: ${r.user_id}  ·  Period: ${r.period_label}`;
    el('btnPreviewPdf').href      = `/payroll/${r.period_id}/payslip-pdf?user_id=${r.user_id}`;

    // Build form & preview
    populateForm(r);
    el('slipDoc').innerHTML = buildSlipDoc(r);

    // Open
    el('payslipPanel').classList.add('open');
    el('pmOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

// ── Close Panel ───────────────────────────────────────────────────────────
function closePayslipPanel() {
    el('payslipPanel').classList.remove('open');
    el('pmOverlay').classList.remove('show');
    document.body.style.overflow = '';
    currentRecordId = null;
}

// ── Toast ─────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = el('toast-container');
    const icon = type === 'success'
        ? `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
        : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = icon + message;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3500);
}

// ── Keyboard shortcuts ────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePayslipPanel();
});
</script>

@endsection