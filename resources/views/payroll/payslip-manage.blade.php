@extends('layouts.app')
@section('title', 'Payslip Management')
@section('page-title', 'Payslip Management')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

.pm-page { display: flex; flex-direction: column; min-height: calc(100vh - 80px); }

/* ── Top bar ── */
.top-bar {
    position: sticky; top: 0; z-index: 100;
    margin-bottom: 12px;
    background: #fff; border-radius: 12px;
    border: 0.5px solid #e5e7eb;
    padding: 12px 16px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
}
.top-bar-left { display: flex; align-items: center; gap: 10px; }
.top-bar-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: #1a3a1a;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.top-bar-icon svg { width: 16px; height: 16px; stroke: #fff; }
.top-bar-title { font-size: 14px; font-weight: 700; color: #111827; }
.top-bar-sub   { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.top-bar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.period-select-wrap { position: relative; min-width: 200px; }
.period-select {
    width: 100%; padding: 8px 32px 8px 12px; font-size: 12.5px; font-weight: 600;
    border: 1px solid #e5e7eb; border-radius: 9px; color: #111827; background: #fff;
    appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
}
.period-select:focus { outline: none; border-color: #2d5a1b; }

.btn-primary {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 14px; font-size: 12px; font-weight: 700;
    color: #fff; background: #1a3a1a;
    border: none; border-radius: 9px; cursor: pointer;
    text-decoration: none; white-space: nowrap; transition: opacity .15s;
}
.btn-primary:hover { opacity: .88; color: #fff; }
.btn-primary svg { width: 13px; height: 13px; flex-shrink: 0; }

.btn-secondary {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 14px; font-size: 12px; font-weight: 600;
    color: #374151; background: #fff;
    border: 1px solid #e5e7eb; border-radius: 9px; cursor: pointer;
    text-decoration: none; white-space: nowrap; transition: background .15s;
}
.btn-secondary:hover { background: #f9fafb; color: #374151; }
.btn-secondary svg { width: 13px; height: 13px; flex-shrink: 0; }

.status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
}
.status-badge.draft     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-badge.finalized { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

/* ── Main 3-col grid ── */
.main-grid {
    display: grid;
    grid-template-columns: 250px 320px 1fr;
    gap: 12px;
    align-items: start;
}

/* ── Employee sidebar ── */
.emp-panel {
    background: #fff; border-radius: 12px;
    border: 0.5px solid #e5e7eb;
    overflow: hidden; display: flex; flex-direction: column;
    position: sticky; top: 16px;
    max-height: calc(100vh - 130px);
}
.emp-panel-header { padding: 12px 14px 10px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
.emp-panel-label {
    font-size: 9px; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
}
.emp-search { position: relative; }
.emp-search svg {
    position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
    width: 12px; height: 12px; stroke: #9ca3af; pointer-events: none;
}
.emp-search input {
    width: 100%; padding: 7px 10px 7px 26px; font-size: 12px;
    border: 1px solid #e5e7eb; border-radius: 8px;
    color: #111827; background: #f9fafb; outline: none;
}
.emp-search input:focus { border-color: #2d5a1b; background: #fff; }

.emp-list { flex: 1; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #e5e7eb transparent; }
.emp-list::-webkit-scrollbar { width: 3px; }
.emp-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 99px; }

.emp-item {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 14px; cursor: pointer;
    border-bottom: 1px solid #f9fafb;
    transition: background .1s;
    user-select: none;
}
.emp-item:hover { background: #f9fafb; }
.emp-item.active { background: #f0fdf4; border-left: 2px solid #1a3a1a; padding-left: 12px; }
.emp-avatar {
    width: 28px; height: 28px; border-radius: 7px; flex-shrink: 0;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700; color: #6b7280;
}
.emp-item.active .emp-avatar { background: #1a3a1a; color: #fff; }
.emp-name { font-size: 12px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.emp-pos  { font-size: 10px; color: #9ca3af; margin-top: 1px; }

.emp-stats {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    border-top: 1px solid #f3f4f6; flex-shrink: 0;
}
.emp-stat { padding: 8px 10px; text-align: center; border-right: 1px solid #f3f4f6; }
.emp-stat:last-child { border-right: none; }
.emp-stat-label { font-size: 9px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
.emp-stat-value { font-size: 11.5px; font-weight: 700; color: #111827; margin-top: 2px; }

/* ── Form panel ── */
.form-panel {
    background: #fff; border-radius: 12px;
    border: 0.5px solid #e5e7eb;
    overflow: hidden; display: flex; flex-direction: column;
    position: sticky; top: 16px;
    max-height: calc(100vh - 130px);
}
.form-panel-header {
    position: sticky; top: 0; z-index: 10;
    padding: 12px 14px; border-bottom: 1px solid #f3f4f6;
    background: #fafffe; flex-shrink: 0;
}
.form-emp-row { display: flex; align-items: center; gap: 9px; }
.form-emp-avatar {
    width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
    background: #1a3a1a; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800;
}
.form-emp-name { font-size: 13px; font-weight: 700; color: #111827; }
.form-emp-meta { font-size: 10px; color: #9ca3af; margin-top: 2px; }
.changes-badge {
    display: none; align-items: center; gap: 4px; margin-left: auto;
    padding: 3px 8px; background: #fef3c7; border: 1px solid #fde68a;
    border-radius: 6px; font-size: 10px; font-weight: 700; color: #92400e; flex-shrink: 0;
}
.changes-badge.show { display: inline-flex; }
.edit-pill {
    display: none; align-items: center; gap: 4px; margin-top: 6px;
    padding: 2px 8px; background: #fef3c7; color: #92400e;
    border: 1px solid #fde68a; border-radius: 20px;
    font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.4px;
}
.edit-pill.show { display: inline-flex; }

.form-search-wrap { padding: 8px 12px 0; flex-shrink: 0; }
.form-search-inner { position: relative; }
.form-search-inner svg {
    position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
    width: 12px; height: 12px; stroke: #9ca3af; pointer-events: none;
}
.form-search-inner input {
    width: 100%; padding: 6px 10px 6px 27px; font-size: 12px;
    border: 1px solid #e5e7eb; border-radius: 8px;
    color: #111827; background: #fff; outline: none;
}
.form-search-inner input:focus { border-color: #2d5a1b; }

.form-scroll {
    flex: 1; overflow-y: auto; padding: 10px 12px 12px;
    scrollbar-width: thin; scrollbar-color: #e5e7eb transparent;
}
.form-scroll::-webkit-scrollbar { width: 3px; }
.form-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 99px; }

.form-section { margin-bottom: 12px; }
.form-section.hidden-section { display: none; }
.section-title {
    font-size: 9px; font-weight: 800; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.8px;
    margin-bottom: 7px; padding-bottom: 5px;
    border-bottom: 1px dashed #e5e7eb;
    display: flex; align-items: center; gap: 5px;
}
.s-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 5px; margin-bottom: 4px; }
.form-row.single { grid-template-columns: 1fr; }
.field-group { display: flex; flex-direction: column; gap: 2px; }
.field-group.hidden-field { display: none; }
.field-label { font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; }
.field-input {
    padding: 6px 9px; font-size: 12px; font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    border: 1px solid #e5e7eb; border-radius: 7px;
    color: #111827; background: #fff; outline: none; width: 100%;
    transition: border-color .15s;
}
.field-input:focus { border-color: #2d5a1b; }
.field-input.label-input { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; font-size: 11.5px; }
.field-input.highlight { border-color: #86efac; background: #f0fdf4; }

.totals-box {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 9px 11px; margin-top: 8px;
}
.totals-row { display: flex; justify-content: space-between; align-items: center; padding: 2px 0; font-size: 11.5px; }
.totals-row .tl { color: #64748b; font-weight: 500; }
.totals-row .tv { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a; font-size: 11px; }
.totals-row .tv.green { color: #166534; }
.totals-row .tv.red   { color: #b91c1c; }
.net-box {
    background: #fef9c3; border: 1.5px solid #eab308; border-radius: 9px;
    padding: 9px 13px; margin-top: 6px;
    display: flex; align-items: center; justify-content: space-between;
}
.net-label { font-size: 9px; font-weight: 800; color: #713f12; text-transform: uppercase; letter-spacing: 0.6px; }
.net-val   { font-size: 17px; font-weight: 800; color: #713f12; font-family: 'JetBrains Mono', monospace; }

.form-footer {
    flex-shrink: 0; padding: 10px 12px;
    border-top: 1px solid #f3f4f6; background: #fafffe;
    display: flex; gap: 7px;
}
.btn-save {
    flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    padding: 8px; font-size: 12px; font-weight: 700;
    color: #fff; background: #059669; border: none; border-radius: 8px; cursor: pointer;
    transition: opacity .15s;
}
.btn-save:hover { opacity: .88; }
.btn-save:disabled { opacity: .45; cursor: not-allowed; }
.btn-save svg { width: 13px; height: 13px; }
.btn-discard {
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    padding: 8px 12px; font-size: 12px; font-weight: 600;
    color: #dc2626; background: #fff; border: 1px solid #fca5a5;
    border-radius: 8px; cursor: pointer; transition: background .15s;
}
.btn-discard:hover { background: #fef2f2; }
.btn-discard:disabled { opacity: .4; cursor: not-allowed; }
.btn-discard svg { width: 13px; height: 13px; }

/* ── Preview panel ── */
.preview-panel {
    background: #fff; border-radius: 12px;
    border: 0.5px solid #e5e7eb;
    overflow: hidden; display: flex; flex-direction: column;
    position: sticky; top: 16px;
    max-height: calc(100vh - 130px);
}
.preview-header {
    position: sticky; top: 0; z-index: 10;
    padding: 11px 16px; border-bottom: 1px solid #f3f4f6;
    background: #fafffe;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0; gap: 8px;
}
.preview-header-left { display: flex; align-items: center; gap: 8px; }
.preview-title { font-size: 12.5px; font-weight: 700; color: #111827; }
.preview-sub   { font-size: 10px; color: #9ca3af; margin-top: 1px; }
.preview-changes-badge {
    display: none; align-items: center; gap: 4px;
    padding: 2px 7px; background: #fef3c7; border: 1px solid #fde68a;
    border-radius: 6px; font-size: 9.5px; font-weight: 700; color: #92400e;
}
.preview-changes-badge.show { display: inline-flex; }

.preview-scroll {
    flex: 1; overflow-y: auto; background: #d1d5db;
    padding: 16px; display: flex; justify-content: center;
    scrollbar-width: thin; scrollbar-color: #b0b7c3 transparent;
}
.preview-scroll::-webkit-scrollbar { width: 4px; }
.preview-scroll::-webkit-scrollbar-thumb { background: #b0b7c3; border-radius: 99px; }

.pdf-page-wrap {
    width: 374px;
    transform-origin: top center;
}
.pdf-doc {
    width: 374px;
    background: #fff;
    padding: 7px 9px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7pt;
    color: #000;
    line-height: 1.3;
}

.no-record {
    text-align: center; padding: 50px 20px; color: #9ca3af;
    display: flex; flex-direction: column; align-items: center; gap: 10px; width: 100%;
}
.no-record-icon { width: 52px; height: 52px; border-radius: 12px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; }
.no-record-icon svg { width: 26px; height: 26px; stroke: #9ca3af; }
.no-record h3 { font-size: 14px; font-weight: 700; color: #6b7280; margin: 0; }
.no-record p  { font-size: 12px; color: #9ca3af; margin: 0; }

/* ── Modal shared styles ── */
.sig-modal-overlay {
    position: fixed; inset: 0; z-index: 1100;
    background: rgba(0,0,0,.45); backdrop-filter: blur(3px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.sig-modal-overlay.open { opacity: 1; pointer-events: all; }
.sig-modal {
    background: #fff; border-radius: 16px;
    box-shadow: 0 24px 64px rgba(0,0,0,.18);
    width: 100%; max-width: 440px;
    transform: translateY(18px) scale(.97);
    transition: transform .25s cubic-bezier(.34,1.56,.64,1); overflow: hidden;
}
.sig-modal-overlay.open .sig-modal { transform: translateY(0) scale(1); }
.sig-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px 12px; background: #fafffe; border-bottom: 1px solid #f0f2f0;
}
.sig-modal-title { font-size: 14px; font-weight: 800; color: #111827; margin: 0; }
.sig-modal-sub   { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }
.sig-modal-body  { padding: 20px; }
.sig-field-group { margin-bottom: 14px; }
.sig-field-label { display: block; font-size: 10px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; }

.sig-modal-footer { padding: 12px 20px; border-top: 1px solid #f0f2f0; display: flex; gap: 8px; justify-content: flex-end; background: #fafffe; }
.btn-sig-save {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 16px; font-size: 12px; font-weight: 700;
    color: #fff; background: #1a3a1a; border: none; border-radius: 8px; cursor: pointer; transition: opacity .15s;
}
.btn-sig-save:hover { opacity: .88; }
.btn-sig-save:disabled { opacity: .55; cursor: not-allowed; }
.btn-sig-cancel { padding: 8px 14px; font-size: 12px; font-weight: 600; color: #6b7280; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: background .15s; }
.btn-sig-cancel:hover { background: #f9fafb; }
.modal-close { background: #f3f4f6; border: 1px solid #e5e7eb; width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; transition: all .15s; }
.modal-close:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }

/* Toast */
#toast-container { position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
.toast {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 600;
    box-shadow: 0 6px 20px rgba(0,0,0,.1); border: 1px solid transparent;
    transform: translateY(14px); opacity: 0; transition: all .3s cubic-bezier(.34,1.56,.64,1);
    min-width: 200px;
}
.toast.show    { transform: translateY(0); opacity: 1; }
.toast.success { background: #d1fae5; border-color: #10b981; color: #065f46; }
.toast.error   { background: #fee2e2; border-color: #ef4444; color: #991b1b; }
.toast svg { width: 14px; height: 14px; flex-shrink: 0; }

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1100px) {
    .main-grid { grid-template-columns: 220px 1fr; }
    .preview-panel { display: none; }
}
@media (max-width: 780px) {
    .main-grid { grid-template-columns: 1fr; }
    .emp-panel, .form-panel { position: static; max-height: none; }
}
</style>

@php
    $signatoryOptions = $signatoryOptions ?? collect();
    $defaultSignatory = $defaultSignatory ?? null;
    $periods          = $periods          ?? collect();
    $records          = $records          ?? collect();
    $selectedPeriodId = $selectedPeriodId ?? null;

    $currentPeriod = $periods->firstWhere('period_id', $selectedPeriodId);
    $isFinalized   = optional($currentPeriod)->status === 'FINALIZED';

    $payrollClerkOption = $signatoryOptions->firstWhere('label', 'Payroll Clerk')
                       ?? $signatoryOptions->last();

    $currentSigName = !empty(optional($currentPeriod)->sig_clerk_name)
        ? strtoupper(trim($currentPeriod->sig_clerk_name))
        : strtoupper(optional($payrollClerkOption)->full_name ?? 'MELINDA R. BARCELONA');

    $currentSigTitle = !empty(optional($currentPeriod)->sig_clerk_title)
        ? strtoupper(trim($currentPeriod->sig_clerk_title))
        : strtoupper(optional($payrollClerkOption)->title ?? 'Administrative Officer V');
@endphp

<div class="pm-page">

    {{-- TOP BAR --}}
    <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="top-bar-title">Payslip Management</p>
                <p class="top-bar-sub">Edit and preview individual employee payslips</p>
            </div>
        </div>
        <div class="top-bar-right">
            <div class="period-select-wrap">
                <select class="period-select" id="periodSelect"
                    onchange="window.location.href='{{ url('payroll/manage') }}?period_id='+this.value">
                    <option value="">— Select Period —</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->period_id }}"
                            {{ $selectedPeriodId == $p->period_id ? 'selected' : '' }}>
                            {{ $p->period_label }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($records->isNotEmpty())
            <a href="{{ url('payroll/'.$selectedPeriodId.'/payslip-pdf') }}" target="_blank" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export All PDF
            </a>

            <a href="{{ url('payroll/'.$selectedPeriodId.'/pdf') }}" target="_blank" class="btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Payroll PDF
            </a>

            <button type="button" class="btn-secondary" onclick="openBulkEditModal()"
                style="border-color:#bbf7d0;color:#1a3a1a;background:#f0fdf4;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Bulk Edit
            </button>

            @if($currentPeriod)
            <span class="status-badge {{ strtolower($currentPeriod->status) }}">
                {{ $currentPeriod->status }}
            </span>
            @endif
            @endif
        </div>
    </div>

    @if($records->isNotEmpty())

    <div class="main-grid">

        {{-- ── COLUMN 1: Employee List ── --}}
        <div class="emp-panel">
            <div class="emp-panel-header">
                <div class="emp-panel-label">Employees · {{ optional($currentPeriod)->period_label }}</div>
                <div class="emp-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="chipSearch" placeholder="Search employee…"
                        oninput="filterChips(this.value)">
                </div>
            </div>

            <div class="emp-list" id="empChips">
                @foreach($records as $r)
                @php
                    $uid = (string)($r->user_id ?? '');
                    $ln  = strtoupper($r->employee->last_name ?? '—');
                    $fn  = $r->employee->first_name ?? '';
                    $ini = substr($ln,0,1).substr($fn,0,1);
                    $pos = $r->designation ?? optional($r->employee->position)->position_code ?? 'N/A';
                @endphp
                <div class="emp-item"
                    data-rid="{{ (string)$r->payroll_id }}"
                    data-name="{{ strtolower($ln.','.$fn) }}"
                    onclick="selectEmployee('{{ $r->payroll_id }}', this)">
                    <div class="emp-avatar">{{ $ini }}</div>
                    <div style="min-width:0;">
                        <div class="emp-name">{{ $ln }}, {{ $fn }}</div>
                        <div class="emp-pos">{{ $pos }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="emp-stats">
                <div class="emp-stat">
                    <div class="emp-stat-label">Total</div>
                    <div class="emp-stat-value" id="statCount">{{ $records->count() }}</div>
                </div>
                <div class="emp-stat">
                    <div class="emp-stat-label">Gross</div>
                    <div class="emp-stat-value" style="font-size:10px;">₱{{ number_format($records->sum('gross_salary')/1000,1) }}K</div>
                </div>
                <div class="emp-stat">
                    <div class="emp-stat-label">Net</div>
                    <div class="emp-stat-value" style="font-size:10px;">₱{{ number_format($records->sum('net_pay')/1000,1) }}K</div>
                </div>
            </div>
        </div>

        {{-- ── COLUMN 2: Edit Form ── --}}
        <div class="form-panel">
            <div class="form-panel-header">
                <div class="form-emp-row">
                    <div class="form-emp-avatar" id="formAvatar">—</div>
                    <div>
                        <div class="form-emp-name" id="formEmpName">Select an employee</div>
                        <div class="form-emp-meta" id="formEmpMeta">—</div>
                    </div>
                    <span class="changes-badge" id="changesBadge">● Changes</span>
                </div>
                <div class="edit-pill" id="editModePill">✎ Edit Mode</div>
            </div>

            <div class="form-search-wrap">
                <div class="form-search-inner">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="fieldSearch" placeholder="Search a field…"
                        oninput="filterFormFields(this.value)">
                </div>
            </div>

            <div class="form-scroll" id="formScroll">

                {{-- EARNINGS --}}
                <div class="form-section" data-section="earnings">
                    <div class="section-title">
                        <span class="s-dot" style="background:#16a34a;"></span>Earnings
                    </div>
                    <div class="form-row single">
                        <div class="field-group" data-fieldname="gross salary">
                            <label class="field-label">Gross Salary</label>
                            <input type="text" class="field-input highlight" id="f_gross_salary" data-field="gross_salary"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>

                    {{-- Designation field added after Gross Salary --}}
                    <div class="form-row single" style="margin-top:4px;">
                        <div class="field-group" data-fieldname="designation position title">
                            <label class="field-label">Designation (shown on payslip)</label>
                            <input type="text" class="field-input label-input" id="f_designation"
                                oninput="onLabelChange()" placeholder="e.g. SR AG">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field-group" data-fieldname="pera label">
                            <label class="field-label">PERA Label</label>
                            <input type="text" class="field-input label-input" id="f_label_pera" data-label="label_pera"
                                oninput="onLabelChange()" placeholder="PERA">
                        </div>
                        <div class="field-group" data-fieldname="pera amount">
                            <label class="field-label">PERA Amount</label>
                            <input type="text" class="field-input" id="f_allowance_pera" data-field="allowance_pera"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="rata label">
                            <label class="field-label">RA Label</label>
                            <input type="text" class="field-input label-input" id="f_label_rata" data-label="label_rata"
                                oninput="onLabelChange()" placeholder="RA">
                        </div>
                        <div class="field-group" data-fieldname="rata amount">
                            <label class="field-label">RA Amount</label>
                            <input type="text" class="field-input" id="f_allowance_rata" data-field="allowance_rata"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="transportation allowance ta label">
                            <label class="field-label">TA Label</label>
                            <input type="text" class="field-input label-input" id="f_label_ta" data-label="label_ta"
                                oninput="onLabelChange()" placeholder="TA">
                        </div>
                        <div class="field-group" data-fieldname="ta allowance amount">
                            <label class="field-label">TA Amount</label>
                            <input type="text" class="field-input" id="f_allowance_ta" data-field="allowance_ta"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="other allowance label">
                            <label class="field-label">Other Alw. Label</label>
                            <input type="text" class="field-input label-input" id="f_label_allowance_other" data-label="label_allowance_other"
                                oninput="onLabelChange()" placeholder="Other Allowance">
                        </div>
                        <div class="field-group" data-fieldname="other allowance amount">
                            <label class="field-label">Other Alw. Amount</label>
                            <input type="text" class="field-input" id="f_allowance_other" data-field="allowance_other"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- STATUTORY --}}
                <div class="form-section" data-section="statutory">
                    <div class="section-title">
                        <span class="s-dot" style="background:#dc2626;"></span>Statutory Deductions
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="withholding tax label">
                            <label class="field-label">Withholding Tax Label</label>
                            <input type="text" class="field-input label-input" id="f_label_withholding_tax" data-label="label_withholding_tax"
                                oninput="onLabelChange()" placeholder="Withholding Tax">
                        </div>
                        <div class="field-group" data-fieldname="withholding tax">
                            <label class="field-label">Withholding Tax</label>
                            <input type="text" class="field-input" id="f_withholding_tax" data-field="withholding_tax"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis premium employee share label">
                            <label class="field-label">GSIS Premium Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_ee" data-label="label_gsis_ee"
                                oninput="onLabelChange()" placeholder="GSIS Premium">
                        </div>
                        <div class="field-group" data-fieldname="gsis premium employee share">
                            <label class="field-label">GSIS Premium (9%)</label>
                            <input type="text" class="field-input" id="f_gsis_ee" data-field="gsis_ee"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="pagibig pag-ibig label">
                            <label class="field-label">PAG-IBIG Label</label>
                            <input type="text" class="field-input label-input" id="f_label_pagibig_govt" data-label="label_pagibig_govt"
                                oninput="onLabelChange()" placeholder="PAG-IBIG">
                        </div>
                        <div class="field-group" data-fieldname="pagibig pag-ibig">
                            <label class="field-label">PAG-IBIG</label>
                            <input type="text" class="field-input" id="f_pagibig_govt" data-field="pagibig_govt"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="medicare philhealth label">
                            <label class="field-label">Medicare (PhilHealth) Label</label>
                            <input type="text" class="field-input label-input" id="f_label_philhealth_ee" data-label="label_philhealth_ee"
                                oninput="onLabelChange()" placeholder="PhilHealth">
                        </div>
                        <div class="field-group" data-fieldname="medicare philhealth">
                            <label class="field-label">(PhilHealth)</label>
                            <input type="text" class="field-input" id="f_philhealth_ee" data-field="philhealth_ee"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis ec ecf emergency contingency label">
                            <label class="field-label">GSIS ECF Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_ec" data-label="label_gsis_ec"
                                oninput="onLabelChange()" placeholder="ECF">
                        </div>
                        <div class="field-group" data-fieldname="gsis ec ecf emergency contingency">
                            <label class="field-label">GSIS ECF</label>
                            <input type="text" class="field-input" id="f_gsis_ec" data-field="gsis_ec"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- COOPERATIVE LOANS --}}
                <div class="form-section" data-section="cooperative">
                    <div class="section-title">
                        <span class="s-dot" style="background:#ea580c;"></span>Cooperative / Bank Loans
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="lbp loan label">
                            <label class="field-label">LBP Label</label>
                            <input type="text" class="field-input label-input" id="f_label_loan_lbp" data-label="label_loan_lbp"
                                oninput="onLabelChange()" placeholder="LBP">
                        </div>
                        <div class="field-group" data-fieldname="lbp loan amount">
                            <label class="field-label">LBP Amount</label>
                            <input type="text" class="field-input" id="f_loan_lbp" data-field="loan_lbp"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="dbp loan label">
                            <label class="field-label">DBP Label</label>
                            <input type="text" class="field-input label-input" id="f_label_loan_dbp" data-label="label_loan_dbp"
                                oninput="onLabelChange()" placeholder="DBP">
                        </div>
                        <div class="field-group" data-fieldname="dbp loan amount">
                            <label class="field-label">DBP Amount</label>
                            <input type="text" class="field-input" id="f_loan_dbp" data-field="loan_dbp"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="cngwmpc label">
                            <label class="field-label">CNGWMPC Label</label>
                            <input type="text" class="field-input label-input" id="f_label_loan_cngwmpc" data-label="label_loan_cngwmpc"
                                oninput="onLabelChange()" placeholder="CNGWMPC">
                        </div>
                        <div class="field-group" data-fieldname="cngwmpc amount">
                            <label class="field-label">CNGWMPC Amount</label>
                            <input type="text" class="field-input" id="f_loan_cngwmpc" data-field="loan_cngwmpc"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="uoli paracle label">
                            <label class="field-label">UOLI/PARACLE Label</label>
                            <input type="text" class="field-input label-input" id="f_label_loan_paracle" data-label="label_loan_paracle"
                                oninput="onLabelChange()" placeholder="UOLI">
                        </div>
                        <div class="field-group" data-fieldname="uoli paracle amount">
                            <label class="field-label">UOLI/PARACLE Amount</label>
                            <input type="text" class="field-input" id="f_loan_paracle" data-field="loan_paracle"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- GSIS LOANS --}}
                <div class="form-section" data-section="gsis">
                    <div class="section-title">
                        <span class="s-dot" style="background:#2563eb;"></span>GSIS Loans
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis salary loan conso label">
                            <label class="field-label">GSIS Salary Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_conso" data-label="label_gsis_conso"
                                oninput="onLabelChange()" placeholder="GSIS salary Loan">
                        </div>
                        <div class="field-group" data-fieldname="gsis salary loan conso">
                            <label class="field-label">GSIS Salary Loan</label>
                            <input type="text" class="field-input" id="f_gsis_conso" data-field="gsis_conso"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis policy loan label">
                            <label class="field-label">GSIS Policy Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_policy" data-label="label_gsis_policy"
                                oninput="onLabelChange()" placeholder="GSIS Policy Loan">
                        </div>
                        <div class="field-group" data-fieldname="gsis policy loan">
                            <label class="field-label">GSIS Policy Loan</label>
                            <input type="text" class="field-input" id="f_gsis_policy" data-field="gsis_policy"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis real estate loan label">
                            <label class="field-label">GSIS Real Estate Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_real_estate" data-label="label_gsis_real_estate"
                                oninput="onLabelChange()" placeholder="GSIS Real State Loan">
                        </div>
                        <div class="field-group" data-fieldname="gsis real estate loan">
                            <label class="field-label">GSIS Real Estate Loan</label>
                            <input type="text" class="field-input" id="f_gsis_real_estate" data-field="gsis_real_estate"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis emergency calamity loan label">
                            <label class="field-label">GSIS Emergency Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_emergency" data-label="label_gsis_emergency"
                                oninput="onLabelChange()" placeholder="GSIS Em. Loan">
                        </div>
                        <div class="field-group" data-fieldname="gsis emergency calamity loan">
                            <label class="field-label">GSIS Emergency Loan</label>
                            <input type="text" class="field-input" id="f_gsis_emergency" data-field="gsis_emergency"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis educ education computer loan label">
                            <label class="field-label">GSIS Educ Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_computer" data-label="label_gsis_computer"
                                oninput="onLabelChange()" placeholder="GSIS Educ loan">
                        </div>
                        <div class="field-group" data-fieldname="gsis educ education computer loan">
                            <label class="field-label">GSIS Educ Loan</label>
                            <input type="text" class="field-input" id="f_gsis_computer" data-field="gsis_computer"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis mpl multi purpose label">
                            <label class="field-label">GSIS MPL Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_mpl" data-label="label_gsis_mpl"
                                oninput="onLabelChange()" placeholder="MPL">
                        </div>
                        <div class="field-group" data-fieldname="gsis mpl multi purpose">
                            <label class="field-label">GSIS MPL</label>
                            <input type="text" class="field-input" id="f_gsis_mpl" data-field="gsis_mpl"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis gfal label">
                            <label class="field-label">GSIS GFAL Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_gfal" data-label="label_gsis_gfal"
                                oninput="onLabelChange()" placeholder="GSIS GFAL">
                        </div>
                        <div class="field-group" data-fieldname="gsis gfal">
                            <label class="field-label">GSIS GFAL</label>
                            <input type="text" class="field-input" id="f_gsis_gfal" data-field="gsis_gfal"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="gsis mpl lite label">
                            <label class="field-label">GSIS MPL Lite Label</label>
                            <input type="text" class="field-input label-input" id="f_label_gsis_mpl_lite" data-label="label_gsis_mpl_lite"
                                oninput="onLabelChange()" placeholder="GSIS MPL Lite">
                        </div>
                        <div class="field-group" data-fieldname="gsis mpl lite">
                            <label class="field-label">GSIS MPL Lite</label>
                            <input type="text" class="field-input" id="f_gsis_mpl_lite" data-field="gsis_mpl_lite"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="pagibig mpl loyalty card label">
                            <label class="field-label">PAG-IBIG Loyalty Card Label</label>
                            <input type="text" class="field-input label-input" id="f_label_pagibig_mpl" data-label="label_pagibig_mpl"
                                oninput="onLabelChange()" placeholder="PAG IBIG Loyalty Card">
                        </div>
                        <div class="field-group" data-fieldname="pagibig mpl loyalty card">
                            <label class="field-label">PAG-IBIG Loyalty Card</label>
                            <input type="text" class="field-input" id="f_pagibig_mpl" data-field="pagibig_mpl"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="pagibig calamity loan label">
                            <label class="field-label">PAG-IBIG Calamity Loan Label</label>
                            <input type="text" class="field-input label-input" id="f_label_pagibig_calamity" data-label="label_pagibig_calamity"
                                oninput="onLabelChange()" placeholder="GSIS Calamity Loan">
                        </div>
                        <div class="field-group" data-fieldname="pagibig calamity loan">
                            <label class="field-label">PAG-IBIG Calamity Loan</label>
                            <input type="text" class="field-input" id="f_pagibig_calamity" data-field="pagibig_calamity"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- OTHER --}}
                <div class="form-section" data-section="other">
                    <div class="section-title">
                        <span class="s-dot" style="background:#7c3aed;"></span>Other
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="overpayment label">
                            <label class="field-label">Overpayment Label</label>
                            <input type="text" class="field-input label-input" id="f_overpayment_label" data-label="overpayment_label"
                                oninput="onLabelChange()" placeholder="Overpayment">
                        </div>
                        <div class="field-group" data-fieldname="overpayment">
                            <label class="field-label">Overpayment</label>
                            <input type="text" class="field-input" id="f_overpayment" data-field="overpayment"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field-group" data-fieldname="custom deduction label">
                            <label class="field-label">Custom Deduction Label</label>
                            <input type="text" class="field-input label-input" id="f_other_deduction_label"
                                data-label="other_deduction_label"
                                oninput="onLabelChange()" placeholder="Other Deduction">
                        </div>
                        <div class="field-group" data-fieldname="custom deduction amount">
                            <label class="field-label">Custom Deduction Amount</label>
                            <input type="text" class="field-input" id="f_other_deduction" data-field="other_deduction"
                                oninput="onFieldChange()" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- LIVE TOTALS --}}
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="tl">Total Allowances</span>
                        <span class="tv green" id="ft_allowances">—</span>
                    </div>
                    <div class="totals-row">
                        <span class="tl">Total Deductions</span>
                        <span class="tv red" id="ft_deductions">—</span>
                    </div>
                </div>
                <div class="net-box">
                    <span class="net-label">Net Pay</span>
                    <span class="net-val" id="ft_net">—</span>
                </div>

            </div>

            <div class="form-footer">
                @if(!$isFinalized)
                <button class="btn-save" id="btnSave" onclick="saveChanges()" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                    Save Changes
                </button>
                <button class="btn-discard" id="btnDiscard" onclick="discardChanges()" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Discard
                </button>
                @else
                <div style="flex:1;display:flex;align-items:center;gap:6px;font-size:12px;color:#065f46;font-weight:600;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;stroke:#065f46;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Period is finalized — read only
                </div>
                @endif
            </div>
        </div>

        {{-- ── COLUMN 3: Live PDF Preview ── --}}
        <div class="preview-panel">
            <div class="preview-header">
                <div class="preview-header-left">
                    <div>
                        <div class="preview-title">Live PDF Preview</div>
                        <div class="preview-sub" id="previewSubtitle">Select an employee</div>
                    </div>
                    <span class="preview-changes-badge" id="previewChangesBadge">● Unsaved</span>
                </div>
                <div style="display:flex; gap:8px;">
                    <a id="btnPreviewPdf" href="#" target="_blank" class="btn-primary"
                        style="pointer-events:none;opacity:.4;font-size:11.5px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Open PDF
                    </a>
                </div>
            </div>
            <div class="preview-scroll" id="previewScroll">
                <div id="pdfDocWrap">
                    <div class="no-record">
                        <div class="no-record-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3>No Employee Selected</h3>
                        <p>Click an employee to preview their payslip.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @else
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:80px 20px;text-align:center;color:#9ca3af;">
        <div style="width:60px;height:60px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:28px;height:28px;stroke:#9ca3af;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 style="font-size:17px;font-weight:700;color:#6b7280;margin:0 0 8px;">No Records Found</h3>
        <p style="font-size:13px;color:#9ca3af;margin:0;">Select a payroll period from the dropdown above.</p>
    </div>
    @endif

</div>

{{-- ── BULK EDIT MODAL ── --}}
<div class="sig-modal-overlay" id="bulkEditModal">
    <div class="sig-modal" style="max-width: 600px;">
        <div class="sig-modal-header">
            <div>
                <p class="sig-modal-title">Bulk Edit Payslips</p>
                <p class="sig-modal-sub">Apply changes to multiple employees at once</p>
            </div>
            <button class="modal-close" onclick="closeBulkEditModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="sig-modal-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="display: flex; flex-direction: column;">
                <label class="sig-field-label">Select Employees</label>
                <div style="margin-bottom: 8px;">
                    <label style="font-size: 11.5px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <input type="checkbox" id="bulkSelectAll" onchange="toggleBulkSelectAll(this)"> Select All
                    </label>
                </div>
                <div class="emp-list" style="border: 1px solid #e5e7eb; border-radius: 8px; flex: 1; max-height: 280px;">
                    @foreach($records as $r)
                    <label class="emp-item" style="padding: 6px 10px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f9fafb; cursor: pointer; margin: 0;">
                        <input type="checkbox" class="bulk-emp-cb" value="{{ $r->payroll_id }}">
                        <span style="font-size: 11.5px; font-weight: 600;">{{ strtoupper($r->employee->last_name ?? '') }}, {{ $r->employee->first_name ?? '' }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="sig-field-label">Field to Update</label>
                <select id="bulkField" class="field-input" style="margin-bottom: 14px;" onchange="toggleBulkLabelInput()">
                    <option value="">-- Select Field --</option>
                    <optgroup label="Earnings">
                        <option value="gross_salary">Gross Salary</option>
                        <option value="designation">Designation</option>
                        <option value="allowance_pera" data-label-field="label_pera">PERA</option>
                        <option value="allowance_rata" data-label-field="label_rata">RA</option>
                        <option value="allowance_ta" data-label-field="label_ta">TA</option>
                        <option value="allowance_other" data-label-field="label_allowance_other">Other Allowance</option>
                    </optgroup>
                    <optgroup label="Deductions">
                        <option value="withholding_tax" data-label-field="label_withholding_tax">Withholding Tax</option>
                        <option value="gsis_ee" data-label-field="label_gsis_ee">GSIS Premium</option>
                        <option value="pagibig_govt" data-label-field="label_pagibig_govt">PAG-IBIG</option>
                        <option value="philhealth_ee" data-label-field="label_philhealth_ee">Philhealth</option>
                        <option value="gsis_ec" data-label-field="label_gsis_ec">GSIS ECF</option>
                        <option value="loan_lbp" data-label-field="label_loan_lbp">LBP</option>
                        <option value="loan_dbp" data-label-field="label_loan_dbp">DBP</option>
                        <option value="loan_cngwmpc" data-label-field="label_loan_cngwmpc">CNGWMPC</option>
                        <option value="loan_paracle" data-label-field="label_loan_paracle">UOLI/PARACLE</option>
                        <option value="gsis_conso" data-label-field="label_gsis_conso">GSIS Salary Loan</option>
                        <option value="gsis_policy" data-label-field="label_gsis_policy">GSIS Policy Loan</option>
                        <option value="gsis_real_estate" data-label-field="label_gsis_real_estate">GSIS Real Estate</option>
                        <option value="gsis_emergency" data-label-field="label_gsis_emergency">GSIS Em. Loan</option>
                        <option value="gsis_computer" data-label-field="label_gsis_computer">GSIS Educ Loan</option>
                        <option value="gsis_mpl" data-label-field="label_gsis_mpl">GSIS MPL</option>
                        <option value="gsis_gfal" data-label-field="label_gsis_gfal">GSIS GFAL</option>
                        <option value="gsis_mpl_lite" data-label-field="label_gsis_mpl_lite">GSIS MPL Lite</option>
                        <option value="pagibig_mpl" data-label-field="label_pagibig_mpl">PAG-IBIG Loyalty Card</option>
                        <option value="pagibig_calamity" data-label-field="label_pagibig_calamity">PAG-IBIG Calamity</option>
                        <option value="overpayment" data-label-field="overpayment_label">Overpayment</option>
                        <option value="other_deduction" data-label-field="other_deduction_label">Other Deduction</option>
                    </optgroup>
                </select>

                <div id="bulkLabelDiv" style="display: none; margin-bottom: 14px;">
                    <label class="sig-field-label">Custom Label (Optional)</label>
                    <input type="text" id="bulkLabelValue" class="field-input label-input" placeholder="Leave blank to keep current">
                </div>

                <label class="sig-field-label">New Amount / Value</label>
                <input type="text" id="bulkValue" class="field-input" placeholder="0.00 or Value">
            </div>
        </div>
        <div class="sig-modal-footer">
            <button class="btn-sig-cancel" onclick="closeBulkEditModal()">Cancel</button>
            <button class="btn-sig-save" id="btnBulkSave" onclick="applyBulkEdit()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Apply Changes
            </button>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
// ── Constants ──────────────────────────────────────────────────────────────
const CSRF                = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const SELECTED_PERIOD_ID  = {{ $selectedPeriodId ? (int)$selectedPeriodId : 'null' }};
const RECORD_UPDATE_URL   = "{{ url('payroll/record') }}";
const PERIOD_IS_FINALIZED = {{ (optional($currentPeriod)->status === 'FINALIZED') ? 'true' : 'false' }};

const currentSigName  = {!! json_encode($currentSigName) !!};
const currentSigTitle = {!! json_encode($currentSigTitle) !!};

// ── Records data ───────────────────────────────────────────────────────────
const RECORDS = {!! json_encode(
    $records->mapWithKeys(function($r) use ($currentPeriod) {
        $ln  = strtoupper($r->employee->last_name ?? '—');
        $fn  = strtoupper($r->employee->first_name ?? '');
        $ext = $r->employee->extension_name ? ' ' . strtoupper($r->employee->extension_name) : '';
        $empName     = $ln . ', ' . $fn . $ext;
        $posCode     = $r->designation ?? optional($r->employee->position)->position_code ?? 'N/A';
        $periodLabel = optional($r->period)->period_label ?? optional($currentPeriod)->period_label ?? '—';

        $userId = (string)($r->user_id ?? '');

        return [(string)$r->payroll_id => [
            'record_id'             => (string)$r->payroll_id,
            'user_id'               => $userId,
            'period_id'             => (int)$r->period_id,
            'emp_name'              => $empName,
            'position'              => $posCode,
            'designation'           => $r->designation ?? optional($r->employee->position)->position_code ?? 'N/A',
            
            'employee_id'           => (string)($r->employee_id ?? ''),
            'tin'                   => $r->employee->tin ?? '',
            'gsis_id'               => $r->employee->gsis_id ?? '',
            'pagibig_id'            => $r->employee->pagibig_id ?? '',
            'philhealth_id'         => $r->employee->philhealth_id ?? '',

            'period_label'          => $periodLabel,
            'gross_salary'          => (float)($r->gross_salary ?? 0),
            'gsis_ee'               => (float)($r->gsis_ee ?? 0),
            'gsis_ec'               => (float)($r->gsis_ec ?? 0),
            'gsis_policy'           => (float)($r->gsis_policy ?? 0),
            'gsis_emergency'        => (float)($r->gsis_emergency ?? 0),
            'gsis_real_estate'      => (float)($r->gsis_real_estate ?? 0),
            'gsis_mpl'              => (float)($r->gsis_mpl ?? 0),
            'gsis_mpl_lite'         => (float)($r->gsis_mpl_lite ?? 0),
            'gsis_gfal'             => (float)($r->gsis_gfal ?? 0),
            'gsis_computer'         => (float)($r->gsis_computer ?? 0),
            'gsis_conso'            => (float)($r->gsis_conso ?? 0),
            'pagibig_govt'          => (float)($r->pagibig_govt ?? 0),
            'pagibig_mpl'           => (float)($r->pagibig_mpl ?? 0),
            'pagibig_calamity'      => (float)($r->pagibig_calamity ?? 0),
            'philhealth_ee'         => (float)($r->philhealth_ee ?? 0),
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
            'label_pera'              => $r->label_pera ?? 'PERA',
            'label_rata'              => $r->label_rata ?? 'RA',
            'label_ta'                => $r->label_ta ?? 'TA',
            'label_allowance_other'   => $r->label_allowance_other ?? 'Other Allowance',
            'label_withholding_tax'   => $r->label_withholding_tax ?? 'Withholding Tax',
            'label_gsis_ee'           => $r->label_gsis_ee ?? 'GSIS Premium',
            'label_gsis_ec'           => $r->label_gsis_ec ?? 'ECF',
            'label_gsis_policy'       => $r->label_gsis_policy ?? 'GSIS Policy Loan',
            'label_gsis_emergency'    => $r->label_gsis_emergency ?? 'GSIS Em. Loan',
            'label_gsis_real_estate'  => $r->label_gsis_real_estate ?? 'GSIS Real State Loan',
            'label_gsis_mpl'          => $r->label_gsis_mpl ?? 'MPL',
            'label_gsis_mpl_lite'     => $r->label_gsis_mpl_lite ?? 'GSIS MPL Lite',
            'label_gsis_gfal'         => $r->label_gsis_gfal ?? 'GSIS GFAL',
            'label_gsis_computer'     => $r->label_gsis_computer ?? 'GSIS Educ loan',
            'label_gsis_conso'        => $r->label_gsis_conso ?? 'GSIS salary Loan',
            'label_pagibig_govt'      => $r->label_pagibig_govt ?? 'PAG-IBIG',
            'label_pagibig_mpl'       => $r->label_pagibig_mpl ?? 'PAG IBIG Loyalty Card',
            'label_pagibig_calamity'  => $r->label_pagibig_calamity ?? 'GSIS Calamity Loan',
            'label_philhealth_ee'     => $r->label_philhealth_ee ?? 'Philhealth',
            'label_loan_lbp'          => $r->label_loan_lbp ?? 'LBP',
            'label_loan_dbp'          => $r->label_loan_dbp ?? 'Development Bank of The Philippines',
            'label_loan_cngwmpc'      => $r->label_loan_cngwmpc ?? 'CNGWMPC',
            'label_loan_paracle'      => $r->label_loan_paracle ?? 'UOLI',
        ]];
    })->toArray()
) !!};

// ── Field registries ───────────────────────────────────────────────────────
const DEDUCTION_FIELDS = [
    'gsis_ee','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
    'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
    'pagibig_govt','pagibig_mpl','pagibig_calamity',
    'philhealth_ee','withholding_tax',
    'loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle',
    'overpayment','other_deduction',
];
const ALLOWANCE_FIELDS = ['allowance_pera','allowance_rata','allowance_ta','allowance_other'];
const NUMERIC_FIELDS   = ['gross_salary', ...DEDUCTION_FIELDS, ...ALLOWANCE_FIELDS];

const LABEL_FIELD_KEYS = [
    'label_pera','label_rata','label_ta','label_allowance_other',
    'label_withholding_tax','label_gsis_ee','label_gsis_ec','label_gsis_policy',
    'label_gsis_emergency','label_gsis_real_estate','label_gsis_mpl','label_gsis_mpl_lite',
    'label_gsis_gfal','label_gsis_computer','label_gsis_conso','label_pagibig_govt',
    'label_pagibig_mpl','label_pagibig_calamity','label_philhealth_ee',
    'label_loan_lbp','label_loan_dbp','label_loan_cngwmpc','label_loan_paracle',
    'overpayment_label',
];

let currentRecordId = null;
let hasChanges      = false;

// ── Helpers ────────────────────────────────────────────────────────────────
function fmt(v) {
    return parseFloat(v || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2, maximumFractionDigits: 2,
    });
}
function parseNum(s) {
    if (!s || String(s).trim() === '' || String(s).trim() === '—') return 0;
    return parseFloat(String(s).replace(/,/g, '')) || 0;
}
function fmtBlank(v) { return v > 0 ? fmt(v) : ''; }

function getLabels() {
    const labels = {};
    LABEL_FIELD_KEYS.forEach(key => {
        const el = document.querySelector(`[data-label="${key}"]`);
        labels[key] = el ? el.value.trim() : '';
    });
    const odlEl = document.querySelector('[data-label="other_deduction_label"]');
    labels.other_deduction_label = odlEl ? odlEl.value.trim() : '';
    return labels;
}

// ── Employee selection ─────────────────────────────────────────────────────
function selectEmployee(recordId, itemEl) {
    const rid = String(recordId);

    if (!RECORDS[rid]) {
        console.error('Record not found', rid);
        showToast('Could not load employee data.', 'error');
        return;
    }

    if (hasChanges && currentRecordId && currentRecordId !== rid) {
        if (!confirm('You have unsaved changes. Discard and switch employee?')) return;
    }

    const r = RECORDS[rid];
    currentRecordId = rid;
    hasChanges = false;

    // Highlight active chip
    document.querySelectorAll('.emp-item').forEach(c => c.classList.remove('active'));
    if (itemEl) itemEl.classList.add('active');

    // Form header
    const nameParts = r.emp_name.split(',');
    const li = (nameParts[0] || '').trim()[0] || '';
    const fi = (nameParts[1] || '').trim()[0] || '';
    document.getElementById('formAvatar').textContent  = (li + fi).toUpperCase() || '?';
    document.getElementById('formEmpName').textContent = r.emp_name;
    document.getElementById('formEmpMeta').textContent = r.position + ' · ' + r.period_label;
    document.getElementById('editModePill').classList.add('show');

    // Populate numeric fields
    NUMERIC_FIELDS.forEach(f => {
        const el = document.getElementById('f_' + f);
        if (el) el.value = (r[f] && r[f] > 0) ? fmt(r[f]) : '';
    });

    // Populate label fields
    LABEL_FIELD_KEYS.forEach(key => {
        const el = document.querySelector(`[data-label="${key}"]`);
        if (el) el.value = r[key] ?? '';
    });
    const odlEl = document.querySelector('[data-label="other_deduction_label"]');
    if (odlEl) odlEl.value = r.other_deduction_label || '';

    // Populate designation field
    const desigEl = document.getElementById('f_designation');
    if (desigEl) desigEl.value = r.designation ?? '';

    // Open PDF button uses user_id
    const pdfLink = document.getElementById('btnPreviewPdf');
    if (pdfLink) {
        pdfLink.href = `/payroll/${r.period_id}/payslip-pdf?user_id=${r.user_id}`;
        pdfLink.style.pointerEvents = '';
        pdfLink.style.opacity = '';
    }

    document.getElementById('previewSubtitle').textContent = r.emp_name + ' · ' + r.period_label;

    updateTotals();
    renderPdf();
    setChangesState(false);
}

// ── Form helpers ───────────────────────────────────────────────────────────
function getFormData() {
    const data = {};
    NUMERIC_FIELDS.forEach(f => {
        const el = document.getElementById('f_' + f);
        data[f] = el ? parseNum(el.value) : 0;
    });
    data.total_deductions = DEDUCTION_FIELDS.reduce((s, f) => s + (data[f] || 0), 0);
    data.total_allowances = ALLOWANCE_FIELDS.reduce((s, f) => s + (data[f] || 0), 0);
    data.net_pay          = data.gross_salary - data.total_deductions + data.total_allowances;
    Object.assign(data, getLabels());
    return data;
}

function onFieldChange() {
    if (!currentRecordId) return;
    setChangesState(true);
    updateTotals();
    renderPdf();
}

function onLabelChange() {
    if (!currentRecordId) return;
    setChangesState(true);
    renderPdf();
}

function updateTotals() {
    const d = getFormData();
    document.getElementById('ft_allowances').textContent = '+ ₱' + fmt(d.total_allowances);
    document.getElementById('ft_deductions').textContent = '− ₱' + fmt(d.total_deductions);
    document.getElementById('ft_net').textContent        = '₱'   + fmt(d.net_pay);
}

function setChangesState(v) {
    hasChanges = v;
    document.getElementById('changesBadge').classList.toggle('show', v);
    document.getElementById('previewChangesBadge').classList.toggle('show', v);
    const btnSave    = document.getElementById('btnSave');
    const btnDiscard = document.getElementById('btnDiscard');
    if (btnSave)    btnSave.disabled    = !v;
    if (btnDiscard) btnDiscard.disabled = !v;
}

// ── Live PDF preview ───────────────────────────────────────────────────────
function renderPdf() {
    if (!currentRecordId) return;
    const r = RECORDS[currentRecordId];
    if (!r) return;
    const d = getFormData();

    const allowParts = [];
    let allowSum = 0;
    if (d.allowance_pera > 0) { allowParts.push(d.label_pera || 'PERA'); allowSum += d.allowance_pera; }
    if (d.allowance_rata > 0) { allowParts.push(d.label_rata || 'RATA'); allowSum += d.allowance_rata; }
    if (d.allowance_ta > 0)   { allowParts.push(d.label_ta || 'TA'); allowSum += d.allowance_ta; }
    
    let allowLabel = '';
    if (allowParts.length > 0) {
        allowLabel = allowParts.join('/');
    } else {
        allowLabel = [d.label_pera || 'PERA', d.label_rata || 'RATA', d.label_ta || 'TA'].filter(Boolean).join('/') || 'PERA/RATA/TA';
    }
    
    const otherAllowLabel = d.label_allowance_other || 'Other Allowance';

    const row = (label, amount) => {
        const val    = fmtBlank(amount);
        const amtCls = val ? '' : 'pdf-zero';
        return `<tr class="pdf-row-sub">
            <td class="pdf-c-lbl">${escHtml(label)}</td>
            <td class="pdf-c-ul" style="border-bottom: 0.5pt solid #000;"></td>
            <td class="pdf-c-amt ${amtCls}">${fmt(amount)}</td>
        </tr>`;
    };

    const displayEmpId = r.employee_id || 'N/A';
    const displayPosition = document.getElementById('f_designation')?.value?.trim() || r.designation || r.position || 'N/A';
    
    const baseFontSize = 7;
    const sSmall = 6.5;
    const sMini  = 6;
    const lineHeight = 1.3;
    const fontFamily = 'Arial, Helvetica, sans-serif';
    
    const dynamicStyles = `
        <style>
            #pdfDocWrap .pdf-doc { font-family: ${fontFamily}; font-size: ${baseFontSize}pt; line-height: ${lineHeight}; }
            #pdfDocWrap .pdf-header p { font-size: ${sSmall}pt; line-height: ${lineHeight}; margin: 0; }
            #pdfDocWrap .pdf-header .bold { font-size: ${baseFontSize}pt; font-weight: bold; }
            #pdfDocWrap .pdf-header .title { font-size: ${baseFontSize + 1.5}pt; font-weight: bold; letter-spacing: 0.5pt; margin-top: 2px; }
            #pdfDocWrap .pdf-info td { font-size: ${sSmall}pt; line-height: ${lineHeight}; }
            #pdfDocWrap .pdf-table td { font-size: ${sSmall}pt; line-height: 1.25; }
            #pdfDocWrap .pdf-row-bold td { font-size: ${baseFontSize}pt; font-weight: bold; }
            #pdfDocWrap .pdf-row-sect td { font-size: ${sSmall}pt; font-weight: bold; }
            #pdfDocWrap .pdf-row-sub td { font-size: ${sSmall}pt; }
            #pdfDocWrap .pdf-row-total td { font-size: ${baseFontSize}pt; font-weight: bold; }
            #pdfDocWrap .pdf-row-net td { font-size: ${baseFontSize + 1.5}pt; font-weight: bold; }
            #pdfDocWrap .pdf-sig-name { font-size: ${sSmall}pt; font-weight: bold; text-transform: uppercase; }
            #pdfDocWrap .pdf-sig-title { font-size: ${sMini}pt; color: #555; margin-top: 1px; }
        </style>
    `;
    
    const trEmpId = `<td class="i-lbl">Employee ID</td><td class="i-sep">:</td><td class="i-val">${escHtml(displayEmpId)}</td>`;
    const trTin = `<td class="i-lbl" style="width:14mm; padding-left:4mm;">TIN</td><td class="i-sep">:</td><td class="i-val">${escHtml(r.tin || 'N/A')}</td>`;
    const trGsis = `<td class="i-lbl" style="padding-left:4mm;">GSIS No.</td><td class="i-sep">:</td><td class="i-val">${escHtml(r.gsis_id || 'N/A')}</td>`;
    const trPagibig = `<td class="i-lbl" style="padding-left:4mm;">PAG-IBIG</td><td class="i-sep">:</td><td class="i-val">${escHtml(r.pagibig_id || 'N/A')}</td>`;
    const trPhilhealth = `<td class="i-lbl" style="padding-left:4mm;">PhilHealth</td><td class="i-sep">:</td><td class="i-val">${escHtml(r.philhealth_id || 'N/A')}</td>`;

    const html = `
    ${dynamicStyles}
    <div class="pdf-page-wrap"><div class="pdf-doc payslip-doc" style="width: 98.95mm; padding: 1.8mm 2.5mm; border: 0.5pt dashed #888; background: #fff; overflow: hidden;">

        <div class="pdf-header" style="text-align: center; padding-bottom: 3px; margin-bottom: 3px; border-bottom: 0.5pt solid #555;">
            <p>Republic of the Philippines</p>
            <p class="bold">PROVINCE OF CAMARINES NORTE</p>
            <p>OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
            <p class="title">PAY SLIP</p>
        </div>

        <table class="pdf-info" style="width: 100%; border-collapse: collapse; margin-bottom: 3px;">
            <tr>
                <td class="i-lbl" style="width: 17mm;">Name</td><td class="i-sep" style="width: 3mm;">:</td><td class="i-val" style="font-weight: bold;">${escHtml(r.emp_name)}</td>
                ${trTin}
            </tr>
            <tr>
                <td class="i-lbl">Position</td><td class="i-sep">:</td><td class="i-val">${escHtml(displayPosition)}</td>
                ${trGsis}
            </tr>
            <tr>
                ${trEmpId}
                ${trPagibig}
            </tr>
            <tr>
                <td class="i-lbl">Period</td><td class="i-sep">:</td><td class="i-val">${escHtml(r.period_label)}</td>
                ${trPhilhealth}
            </tr>
        </table>

        <table class="pdf-table" style="width: 100%; border-collapse: collapse;">

            <tr class="pdf-row-bold">
                <td class="pdf-c-lbl" style="width: 45%;">Gross Salary</td>
                <td class="pdf-c-ul" style="width: 35%; border-bottom: 0.5pt solid #000;"></td>
                <td class="pdf-c-amt" style="width: 20%; text-align: right; font-weight: bold;">${fmt(d.gross_salary)}</td>
            </tr>

            <tr class="pdf-row-bold">
                <td class="pdf-c-lbl">${escHtml(allowLabel)}</td>
                <td class="pdf-c-ul" style="border-bottom: 0.5pt solid #000;"></td>
                <td class="pdf-c-amt" style="font-weight: bold;">${fmt(allowSum)}</td>
            </tr>
            
            ${d.allowance_other > 0 ? `
            <tr class="pdf-row-bold">
                <td class="pdf-c-lbl">${escHtml(otherAllowLabel)}</td>
                <td class="pdf-c-ul" style="border-bottom: 0.5pt solid #000;"></td>
                <td class="pdf-c-amt" style="font-weight: bold;">${fmt(d.allowance_other)}</td>
            </tr>` : ''}

            <tr class="pdf-row-div"><td colspan="3" style="border-top: 0.3pt solid #ccc; padding-top: 1px;"></td></tr>
            <tr class="pdf-row-sect"><td colspan="3" style="padding-top: 1px;">Less Deductions</td></tr>

            ${row('UCPB', 0)}
            ${row(d.label_gsis_mpl        || 'MPL',              d.gsis_mpl)}
            ${row('CBCN', 0)}
            ${row('MSLAP', 0)}
            ${row(d.label_withholding_tax  || 'Withholding Tax',  d.withholding_tax)}
            ${row(d.label_gsis_conso       || 'GSIS salary Loan', d.gsis_conso)}
            ${row(d.label_gsis_policy      || 'GSIS Policy Loan', d.gsis_policy)}
            ${row(d.label_philhealth_ee    || 'Medicare',         d.philhealth_ee)}
            ${row(d.label_gsis_ee          || 'GSIS Premium',     d.gsis_ee)}
            ${row(d.label_pagibig_govt     || 'PAG-IBIG',         d.pagibig_govt)}
            ${row(d.label_loan_lbp         || 'LBP',              d.loan_lbp)}
            ${row(d.label_loan_dbp         || 'Development Bank of The Philippines', d.loan_dbp)}
            ${row(d.label_loan_cngwmpc     || 'CNGWMPC',          d.loan_cngwmpc)}
            ${row(d.label_loan_paracle     || 'UOLI',             d.loan_paracle)}
            ${row(d.label_gsis_real_estate || 'GSIS Real State Loan', d.gsis_real_estate)}
            ${row(d.label_pagibig_calamity || 'GSIS Calamity Loan',   d.pagibig_calamity)}
            ${row('Nursery', 0)}
            ${row(d.label_gsis_emergency   || 'GSIS Em. Loan',    d.gsis_emergency)}
            ${row(d.label_gsis_computer    || 'GSIS Educ loan',   d.gsis_computer)}
            ${row(d.label_pagibig_mpl      || 'PAG IBIG Loyalty Card', d.pagibig_mpl)}
            ${row(d.label_gsis_ec          || 'ECF',              d.gsis_ec)}

            ${d.gsis_gfal     > 0 ? row(d.label_gsis_gfal     || 'GSIS GFAL',     d.gsis_gfal)     : ''}
            ${d.gsis_mpl_lite > 0 ? row(d.label_gsis_mpl_lite || 'GSIS MPL Lite', d.gsis_mpl_lite) : ''}
            ${d.overpayment   > 0 ? row(d.overpayment_label   || 'Overpayment',   d.overpayment)   : ''}
            ${d.other_deduction > 0 ? `<tr class="pdf-row-sub">
                <td class="pdf-c-lbl" style="font-weight:700;">${escHtml(d.other_deduction_label || 'Other Deduction')}</td>
                <td class="pdf-c-ul" style="border-bottom: 0.5pt solid #000;"></td>
                <td class="pdf-c-amt" style="font-weight:700;">${fmt(d.other_deduction)}</td>
            </tr>` : ''}

            <tr class="pdf-row-total">
                <td class="pdf-c-lbl" style="border-top: 0.8pt solid #000; padding-top: 2px;">TOTAL DEDUCTIONS</td>
                <td class="pdf-c-ul" style="border-top: 0.8pt solid #000;"></td>
                <td class="pdf-c-amt" style="border-top: 0.8pt solid #000; padding-top: 2px;">${fmt(d.total_deductions)}</td>
            </tr>
            <tr class="pdf-row-net">
                <td class="pdf-c-lbl" style="border-top: 1pt solid #000; padding-top: 2px;">NET TAKE HOME PAY</td>
                <td class="pdf-c-ul" style="border-top: 1pt solid #000;"></td>
                <td class="pdf-c-amt" style="border-top: 1pt solid #000; padding-top: 2px;">${fmt(d.net_pay)}</td>
            </tr>

        </table>

        <div class="pdf-sig" style="text-align: center; margin-top: 5px; padding-top: 3px; border-top: 0.4pt solid #aaa;">
            <div class="pdf-sig-name">${escHtml(currentSigName)}</div>
            <div class="pdf-sig-title">${escHtml(currentSigTitle)}</div>
        </div>

    </div></div>`;

    document.getElementById('pdfDocWrap').innerHTML = html;
}

function escHtml(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── Save / Discard ─────────────────────────────────────────────────────────
async function saveChanges() {
    if (!currentRecordId || PERIOD_IS_FINALIZED) return;
    const btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;animation:spin .7s linear infinite;stroke:#fff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving…`;

    const d       = getFormData();
    const payload = {};
    NUMERIC_FIELDS.forEach(f => { payload[f] = d[f] || 0; });
    LABEL_FIELD_KEYS.forEach(key => { payload[key] = d[key] || ''; });
    payload.other_deduction_label = d.other_deduction_label || '';
    payload.designation = document.getElementById('f_designation')?.value?.trim() ?? '';

    try {
        const res  = await fetch(`${RECORD_UPDATE_URL}/${currentRecordId}`, {
            method:  'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify(payload),
        });
        const data = await res.json();
        if (!res.ok) {
            if (res.status === 403) throw new Error('This payroll period is finalized and cannot be edited.');
            if (res.status === 422 && data.errors) throw new Error(Object.values(data.errors).flat()[0]);
            throw new Error(data.message ?? data.error ?? 'Save failed.');
        }

        const saved = data.record ?? data;
        if (saved && RECORDS[currentRecordId]) {
            NUMERIC_FIELDS.forEach(f => {
                if (saved[f] !== undefined) RECORDS[currentRecordId][f] = parseFloat(saved[f]) || 0;
            });
            LABEL_FIELD_KEYS.forEach(key => {
                if (saved[key] !== undefined) RECORDS[currentRecordId][key] = saved[key];
            });
            if (saved.other_deduction_label !== undefined)
                RECORDS[currentRecordId].other_deduction_label = saved.other_deduction_label;
            if (saved.total_deductions !== undefined)
                RECORDS[currentRecordId].total_deductions = parseFloat(saved.total_deductions) || 0;
            if (saved.total_allowances !== undefined)
                RECORDS[currentRecordId].total_allowances = parseFloat(saved.total_allowances) || 0;
            if (saved.net_pay !== undefined)
                RECORDS[currentRecordId].net_pay = parseFloat(saved.net_pay) || 0;
            if (saved.designation !== undefined)
                RECORDS[currentRecordId].designation = saved.designation;
        }

        setChangesState(false);
        renderPdf();
        showToast('Payslip saved successfully!', 'success');
    } catch (err) {
        showToast(err.message ?? 'An error occurred.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;stroke:#fff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/></svg> Save Changes`;
        if (hasChanges) btn.disabled = false;
    }
}

function discardChanges() {
    if (!currentRecordId) return;
    hasChanges = false;
    const item = document.querySelector(`.emp-item[data-rid="${currentRecordId}"]`);
    selectEmployee(currentRecordId, item);
}

// ── Search / filter ────────────────────────────────────────────────────────
function filterChips(q) {
    const query = q.toLowerCase().trim();
    let vis = 0;
    document.querySelectorAll('.emp-item').forEach(c => {
        const show = !query || (c.dataset.name || '').includes(query);
        c.style.display = show ? '' : 'none';
        if (show) vis++;
    });
    const el = document.getElementById('statCount');
    if (el) el.textContent = query ? vis : document.querySelectorAll('.emp-item').length;
}

function filterFormFields(q) {
    const query = q.toLowerCase().trim();
    document.querySelectorAll('.form-section').forEach(sec => {
        let secVisible = false;
        sec.querySelectorAll('.field-group').forEach(fg => {
            const name = (fg.dataset.fieldname || '').toLowerCase();
            const show = !query || name.includes(query);
            fg.classList.toggle('hidden-field', !show);
            if (show) secVisible = true;
        });
        sec.classList.toggle('hidden-section', !!query && !secVisible);
    });
}

// ── Bulk Edit Modal ────────────────────────────────────────────────────────
function openBulkEditModal() {
    if (!SELECTED_PERIOD_ID) { showToast('Please select a period first.', 'error'); return; }
    document.getElementById('bulkEditModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeBulkEditModal() {
    document.getElementById('bulkEditModal').classList.remove('open');
    document.body.style.overflow = '';
}

function toggleBulkSelectAll(cb) {
    document.querySelectorAll('.bulk-emp-cb').forEach(el => el.checked = cb.checked);
}

function toggleBulkLabelInput() {
    const sel = document.getElementById('bulkField');
    const opt = sel.options[sel.selectedIndex];
    const hasLabel = opt && opt.dataset.labelField;
    document.getElementById('bulkLabelDiv').style.display = hasLabel ? 'block' : 'none';
    if (!hasLabel) document.getElementById('bulkLabelValue').value = '';
}

async function applyBulkEdit() {
    if (PERIOD_IS_FINALIZED) { showToast('Period is finalized. Cannot edit.', 'error'); return; }

    const selectedIds = Array.from(document.querySelectorAll('.bulk-emp-cb:checked')).map(el => el.value);
    const field = document.getElementById('bulkField').value;
    let value = document.getElementById('bulkValue').value;
    const labelValue = document.getElementById('bulkLabelValue').value;

    if (selectedIds.length === 0) return showToast('Please select at least one employee.', 'error');
    if (!field) return showToast('Please select a field to update.', 'error');

    const sel = document.getElementById('bulkField');
    const opt = sel.options[sel.selectedIndex];
    const labelField = opt.dataset.labelField;

    if (field !== 'designation') {
        value = parseNum(value);
    }

    const btn = document.getElementById('btnBulkSave');
    btn.disabled = true;
    btn.innerHTML = 'Saving...';

    let successCount = 0;
    let failCount = 0;

    for (const rid of selectedIds) {
        const payload = {};
        payload[field] = value;
        if (labelField && labelValue.trim() !== '') {
            payload[labelField] = labelValue.trim();
        }

        try {
            const res = await fetch(`${RECORD_UPDATE_URL}/${rid}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            
            const data = await res.json();
            
            if (res.ok) {
                successCount++;
                const saved = data.record ?? data;
                
                if (RECORDS[rid]) {
                    if (field === 'designation') {
                        RECORDS[rid][field] = saved[field] ?? value;
                    } else {
                        RECORDS[rid][field] = parseFloat(saved[field]) || 0;
                    }

                    if (labelField && saved[labelField] !== undefined) {
                        RECORDS[rid][labelField] = saved[labelField];
                    }
                    
                    if (saved.total_deductions !== undefined) RECORDS[rid].total_deductions = parseFloat(saved.total_deductions) || 0;
                    if (saved.total_allowances !== undefined) RECORDS[rid].total_allowances = parseFloat(saved.total_allowances) || 0;
                    if (saved.net_pay !== undefined) RECORDS[rid].net_pay = parseFloat(saved.net_pay) || 0;
                }
            } else {
                failCount++;
            }
        } catch (e) {
            failCount++;
        }
    }

    btn.disabled = false;
    btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Apply Changes`;

    if (failCount === 0) {
        showToast(`Successfully updated ${successCount} payslips.`, 'success');
        closeBulkEditModal();
        
        if (currentRecordId && selectedIds.includes(currentRecordId)) {
            const item = document.querySelector(`.emp-item[data-rid="${currentRecordId}"]`);
            selectEmployee(currentRecordId, item);
        }
    } else {
        showToast(`Updated ${successCount}, failed ${failCount}.`, 'error');
    }
}

// ── Auto-select first employee on page load ────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const keys = Object.keys(RECORDS);
    if (!keys.length) return;

    const firstKey  = keys[0];
    const firstChip = document.querySelector(`.emp-item[data-rid="${firstKey}"]`);
    if (firstChip) {
        selectEmployee(firstKey, firstChip);
    }
});

// ── Toast ──────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const icon = type === 'success'
        ? `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
        : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = icon + escHtml(message);
    container.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3500);
}

// ── Event Listeners ────────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const bulkModal = document.getElementById('bulkEditModal');
        if (bulkModal && bulkModal.classList.contains('open')) {
            closeBulkEditModal();
        }
    }
});

const bulkModalEl = document.getElementById('bulkEditModal');
if (bulkModalEl) {
    bulkModalEl.addEventListener('click', function(e) {
        if (e.target === this) closeBulkEditModal();
    });
}
</script>
@endsection