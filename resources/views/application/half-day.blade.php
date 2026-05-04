@extends('layouts.app')
@section('title', 'Certification for Half Day')
@section('page-title', 'Application')

@section('content')
<style>
    /* ── Balance Cards ── */
    .bal-card-dark {
        background: linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 60%,#3d7a2a 100%);
        border-radius:20px; padding:28px; color:#fff; position:relative; overflow:hidden;
    }
    .bal-card-dark::after {
        content:''; position:absolute; right:-20px; top:-20px;
        width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .bal-card-light {
        background:#fff; border-radius:20px; padding:28px;
        border:1px solid #f0f0f0; box-shadow:0 2px 12px rgba(0,0,0,0.05); position:relative; overflow:hidden;
    }
    .bal-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .bal-icon-dark  { background:rgba(255,255,255,0.18); }
    .bal-icon-green { background:#dcfce7; }

    /* ── Table ── */
    .data-table { width:100%; font-size:13px; border-collapse:collapse; }
    .data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
    .data-table th { padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
    .data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; text-align:center; }
    .data-table tbody .hd-row { cursor:pointer; }
    .data-table tbody .hd-row:hover { background:#f0fdf4; }
    .data-table tbody .hd-row:hover td:nth-child(2) { color:#1a3a1a; }

    /* ── Status Badges ── */
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .status-badge::before { content:'●'; font-size:8px; }
    .badge-pending   { background:#fef9c3; color:#854d0e; }
    .badge-approved  { background:#dcfce7; color:#14532d; }
    .badge-rejected  { background:#fee2e2; color:#991b1b; }
    .badge-cancelled { background:#f3f4f6; color:#6b7280; }

    /* ── Period Pills ── */
    .period-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .period-am { background:#dbeafe; color:#1e40af; }
    .period-pm { background:#ede9fe; color:#5b21b6; }

    /* ── Breadcrumb ── */
    .breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:24px; }
    .breadcrumb a { color:#6b7280; text-decoration:none; }
    .breadcrumb a:hover { color:#1a3a1a; }
    .breadcrumb .sep { color:#d1d5db; }
    .breadcrumb .current { color:#1a3a1a; font-weight:600; }

    /* ── Toast ── */
    #toast { position:fixed; bottom:24px; right:24px; z-index:400; min-width:280px; background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 8px 32px rgba(0,0,0,0.15); display:flex; align-items:center; gap:12px; opacity:0; transform:translateY(16px); transition:all 0.3s ease; pointer-events:none; }
    #toast.show { opacity:1; transform:translateY(0); pointer-events:all; }

    /* ── Action Menu ── */
    .action-menu { position:relative; display:inline-block; }
    .action-menu-btn { background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; color:#9ca3af; }
    .action-menu-btn:hover { background:#f3f4f6; color:#374151; }
    .action-dropdown { position:fixed; z-index:9999; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:160px; display:none; }
    .action-dropdown.open { display:block; }
    .action-item { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:13px; color:#374151; cursor:pointer; border:none; background:none; width:100%; text-align:left; }
    .action-item:hover { background:#f9fafb; }
    .action-item:first-child { border-radius:10px 10px 0 0; }
    .action-item:last-child  { border-radius:0 0 10px 10px; }
    .action-item.danger { color:#ef4444; }
    .action-item.danger:hover { background:#fee2e2; }

    /* ── Overlay ── */
    #panelOverlay { position:fixed; inset:0; background:rgba(0,0,0,0.25); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); z-index:90; opacity:0; pointer-events:none; transition:opacity 0.3s ease; }
    #panelOverlay.show { opacity:1; pointer-events:all; }

    /* ── Slide Panel ── */
    .slide-panel { position:fixed; top:0; right:0; bottom:0; z-index:100; width:52vw; min-width:460px; max-width:780px; display:flex; flex-direction:column; pointer-events:none; transform:translateX(100%); transition:transform 0.36s cubic-bezier(0.32,0.72,0,1); }
    .slide-panel.open { pointer-events:all; transform:translateX(0); }
    .slide-panel-box { background:#fff; width:100%; height:100%; display:flex; flex-direction:column; box-shadow:-12px 0 60px rgba(0,0,0,0.22); overflow:hidden; }
    .panel-header { background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%); padding:22px 28px 20px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
    .panel-header h2 { font-size:18px; font-weight:700; color:#fff; margin:0 0 3px; }
    .panel-header p  { font-size:12px; color:rgba(255,255,255,0.6); margin:0; }
    .panel-close { background:rgba(255,255,255,0.15); border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; color:rgba(255,255,255,0.8); transition:background 0.15s; flex-shrink:0; }
    .panel-close:hover { background:rgba(255,255,255,0.28); color:#fff; }
    .panel-body { flex:1; overflow-y:auto; padding:0; background:#f8f9fa; scrollbar-width:thin; scrollbar-color:#d1d5db transparent; }
    .panel-body::-webkit-scrollbar { width:4px; }
    .panel-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:99px; }
    .panel-footer { flex-shrink:0; padding:16px 28px; border-top:1px solid #f3f4f6; background:#fff; display:flex; align-items:center; justify-content:flex-end; gap:12px; }
    .panel-footer-spaced { justify-content:space-between; }

    /* ── Form Cards ── */
    .form-section-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .section-heading { display:flex; align-items:center; gap:10px; margin-bottom:18px; }
    .section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .section-card-title { font-size:14px; font-weight:700; color:#111827; margin:0; }

    /* ── Detail Panel Cards ── */
    .dp-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .dp-section-heading { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
    .dp-section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .dp-section-title { font-size:14px; font-weight:700; color:#111827; margin:0; }
    .dp-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 24px; }
    .dp-field label { display:block; font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px; }
    .dp-field p { font-size:13px; color:#111827; font-weight:500; margin:0; }
    .dp-field.span2 { grid-column:span 2; }

    /* ── Form Elements ── */
    .form-field { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; transition:border-color 0.15s,background 0.15s; outline:none; }
    .form-field:focus { background:#fff; border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .form-field:disabled { color:#6b7280; cursor:not-allowed; background:#f3f4f6; }
    .field-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:5px; display:block; }
    .field-label .req { color:#ef4444; }

    /* ── AM/PM Toggle ── */
    .period-toggle { display:flex; gap:10px; }
    .period-btn { flex:1; padding:10px; border-radius:10px; border:1.5px solid #e5e7eb; background:#f9fafb; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; text-align:center; color:#6b7280; }
    .period-btn:hover  { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .period-btn.sel-am { border-color:#1e40af; background:#dbeafe; color:#1e40af; }
    .period-btn.sel-pm { border-color:#5b21b6; background:#ede9fe; color:#5b21b6; }

    /* ── Buttons ── */
    .btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .btn-cancel-action { padding:8px 18px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; transition:background 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-cancel-action:hover { background:#b91c1c; }

    /* ══ Calendar Picker ══ */
    .cal-wrap { position:relative; }
    .cal-trigger { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:border-color 0.15s,background 0.15s; outline:none; text-align:left; font-family:inherit; }
    .cal-trigger:focus, .cal-trigger.open { background:#fff; border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .cal-trigger-text { flex:1; color:#111827; font-size:13px; }
    .cal-trigger-text.placeholder { color:#9ca3af; }
    .cal-popup { position:absolute; top:calc(100% + 6px); left:0; z-index:500; width:300px; background:#fff; border:1.5px solid #e5e7eb; border-radius:14px; box-shadow:0 12px 36px rgba(0,0,0,0.15); padding:14px 16px; animation:calFadeIn 0.15s ease; }
    @keyframes calFadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .cal-nav { background:none; border:none; cursor:pointer; padding:5px 8px; border-radius:8px; color:#374151; display:flex; align-items:center; transition:background 0.12s; }
    .cal-nav:hover { background:#f3f4f6; }
    .cal-month-label { font-size:13px; font-weight:800; color:#111827; }
    .cal-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; margin-bottom:4px; }
    .cal-wd { text-align:center; font-size:9px; font-weight:800; color:#9ca3af; padding:3px 0; text-transform:uppercase; }
    .cal-days { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; }
    .cal-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:500; border-radius:7px; cursor:pointer; transition:all 0.12s; border:1.5px solid transparent; color:#374151; background:none; line-height:1; width:100%; font-family:inherit; }
    .cal-day:hover:not(.cal-disabled):not(.cal-other):not(.cal-halfday-conflict):not(.cal-leave-conflict):not(.cal-weekend) { background:#f0fdf4; border-color:#bbf7d0; color:#14532d; }
    .cal-day.cal-selected { background:#1a3a1a !important; color:#fff !important; border-color:#1a3a1a !important; font-weight:700; }
    .cal-day.cal-today { border-color:#2d5a1b; color:#2d5a1b; font-weight:700; }
    .cal-day.cal-today.cal-selected { border-color:#1a3a1a; color:#fff; }
    .cal-day.cal-weekend { color:#d1d5db; cursor:not-allowed; }
    .cal-day.cal-weekend:hover { background:none; border-color:transparent; color:#d1d5db; }
    .cal-day.cal-other { color:#e5e7eb; cursor:default; pointer-events:none; }
    .cal-day.cal-disabled { color:#d1d5db; cursor:not-allowed; background:none; }
    .cal-day.cal-disabled:hover { background:none; border-color:transparent; color:#d1d5db; }
    .cal-day.cal-leave-conflict { background:#fee2e2; color:#991b1b; cursor:not-allowed; border-color:#fecaca; }
    .cal-day.cal-leave-conflict:hover { background:#fee2e2; border-color:#fecaca; color:#991b1b; }
    .cal-day.cal-halfday-conflict { background:#fef9c3; color:#854d0e; cursor:not-allowed; border-color:#fde68a; }
    .cal-day.cal-halfday-conflict:hover { background:#fef9c3; border-color:#fde68a; color:#854d0e; }
    .cal-legend { display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6; }
    .cal-legend-item { display:flex; align-items:center; gap:4px; font-size:10px; color:#6b7280; }
    .cal-legend-dot { width:10px; height:10px; border-radius:3px; flex-shrink:0; }

    /* ── Tab Bar ── */
    .app-tab-bar {
        display:flex; align-items:center; gap:24px; padding:0 20px;
        border-bottom:1px solid #f3f4f6; overflow-x:auto;
        scrollbar-width:none; -webkit-overflow-scrolling:touch;
    }
    .app-tab-bar::-webkit-scrollbar { display:none; }
    .tab-btn {
        padding:12px 4px; font-size:14px; font-weight:500;
        color:#6b7280; border:none; background:none;
        border-bottom:2px solid transparent; cursor:pointer;
        transition:all 0.2s; white-space:nowrap; flex-shrink:0;
    }
    .tab-btn.active { color:#1a3a1a; border-bottom-color:#2d5a1b; font-weight:700; }

    /* ── Filter row ── */
    .filter-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .filter-row .rel { position:relative; }
    .filter-row .rel.rel-search { flex:1 1 160px; min-width:120px; }
    .filter-row .rel:has(select) { flex:0 0 auto; }
    .filter-row input,
    .filter-row select { width:100%; appearance:none; -webkit-appearance:none; padding:7px 10px; font-size:12px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; outline:none; transition:border-color 0.15s; }
    .filter-row select { padding-right:26px; }
    .filter-row input:focus, .filter-row select:focus { border-color:#2d5a1b; }
    .filter-row input { padding-left:32px; }
    .filter-row .chevron { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }
    .filter-row .search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }

    /* ── Panel hidden ── */
    .tab-panel { display:block; }
    .tab-panel.hidden { display:none; }

    /* ── Spinner ── */
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ══ TAB HEADER ROW ══ */
    .cert-header-row {
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:16px 24px 12px;
        border-bottom:1px solid #f3f4f6;
        gap:8px;
    }
    .cert-header-info { flex:1; min-width:0; }

    @media (min-width:641px) {
        .cert-apply-wrap {
            display:flex;
            align-items:center;
            align-self:stretch;
        }
    }

    @media (max-width:640px) {
        /* Header row — stack info on top, button full-width below */
        .cert-header-row {
            flex-wrap:wrap;
            padding:14px 16px 0;
            gap:0;
        }
        .cert-header-info {
            order:1;
            flex:1 1 100%;
            padding-bottom:10px;
        }
        .cert-apply-wrap {
            order:2;
            width:100%;
            padding:8px 0 12px;
            border-top:1px solid #f3f4f6;
        }
        .cert-apply-wrap button {
            width:100%;
            justify-content:center;
        }

        /* Panels */
        .slide-panel { width:100vw; min-width:unset; max-width:100vw; }
        .panel-header { padding:16px 18px 14px; }
        .panel-header h2 { font-size:15px; }
        .panel-footer { padding:12px 16px; gap:8px; flex-wrap:wrap; }
        .panel-footer button { padding:8px 14px !important; font-size:12px !important; }

        /* Form cards */
        .form-section-card { margin:12px 12px; padding:16px 14px 14px; }
        .dp-card { margin:12px 12px; padding:16px 14px 14px; }
        .form-section-card .grid.grid-cols-2 { grid-template-columns:1fr !important; }
        .form-section-card .col-span-2 { grid-column:span 1 !important; }
        .dp-grid { grid-template-columns:1fr !important; }
        .dp-field.span2 { grid-column:span 1 !important; }

        /* Balance cards */
        .bal-card-dark, .bal-card-light { padding:18px 16px; border-radius:14px; }

        /* Tab bar */
        .app-tab-bar { padding:0 12px; gap:16px; }

        /* Filter row */
        .filter-row { flex-wrap:wrap; }
        .filter-row .rel-search { flex:1 1 100% !important; }
        .filter-row .rel:not(.rel-search) { flex:1 1 calc(50% - 4px) !important; }

        /* Active table — hide less critical columns */
        #activeTbody tr td:nth-child(3),
        .data-table thead tr th:nth-child(3),
        #activeTbody tr td:nth-child(5),
        .data-table thead tr th:nth-child(5),
        #activeTbody tr td:nth-child(6),
        .data-table thead tr th:nth-child(6) { display:none; }

        /* History table — hide less critical columns */
        #historyTbody tr td:nth-child(3),
        #historyTbody tr td:nth-child(5),
        #historyTbody tr td:nth-child(6),
        #historyTbody tr td:nth-child(7) { display:none; }

        /* Toast */
        #toast { left:16px; right:16px; min-width:unset; bottom:16px; max-width:unset; }

        /* Modals */
        #cancelModalCard { width:95vw !important; padding:20px !important; }
        #pdfModalCard { width:100vw !important; max-width:100vw !important; border-radius:0 !important; height:100vh !important; max-height:100vh !important; }
    }

    @media (min-width:641px) and (max-width:1024px) {
        .slide-panel { width:80vw; min-width:unset; max-width:680px; }
        .cert-header-row { padding:16px 20px 12px; }
    }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Application</a>
    <span class="sep">›</span>
    <span class="current">Certification for Half Day</span>
</div>

{{-- Balance Cards --}}
@php
    function truncate3($value): string {
        $str = ltrim((string)($value ?? '0'));
        $dot = strpos($str, '.');
        if ($dot === false) {
            return $str . '.000';
        }
        $decimals = substr($str, $dot + 1) . '000';
        return substr($str, 0, $dot + 1) . substr($decimals, 0, 3);
    }

    $vlBal = null; $slBal = null;
    foreach ($leaveTypes as $lt) {
        $cb = $creditBalances[$lt->leave_type_id] ?? null;
        if (str_contains(strtolower($lt->type_name), 'vacation')) $vlBal = $cb;
        if (str_contains(strtolower($lt->type_name), 'sick'))     $slBal = $cb;
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    {{-- Vacation Leave Card --}}
    <div class="bal-card-dark">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium mb-2" style="color:rgba(255,255,255,0.75);">Vacation Leave Balance</p>
                <p class="text-4xl font-bold tracking-tight">{{ truncate3($vlBal ? (string) $vlBal->getRawOriginal('remaining_balance') : 0) }}</p>
                @if($vlBal)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs" style="color:rgba(255,255,255,0.65);">Accrued: <strong>{{ truncate3((string) $vlBal->getRawOriginal('total_accrued')) }}</strong></span>
                    <span style="color:rgba(255,255,255,0.3)">·</span>
                    <span class="text-xs" style="color:rgba(255,255,255,0.65);">Used: <strong>{{ truncate3((string) $vlBal->getRawOriginal('total_used')) }}</strong></span>
                </div>
                @else
                <p class="text-xs mt-3" style="color:rgba(255,255,255,0.5);">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-dark">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    {{-- Sick Leave Card --}}
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Sick Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ truncate3($slBal ? (string) $slBal->getRawOriginal('remaining_balance') : 0) }}</p>
                @if($slBal)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ truncate3((string) $slBal->getRawOriginal('total_accrued')) }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ truncate3((string) $slBal->getRawOriginal('total_used')) }}</strong></span>
                </div>
                @else
                <p class="text-xs text-gray-400 mt-3">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-green">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Header row --}}
    <div class="cert-header-row">
        <div class="cert-header-info">
            <p class="font-bold text-gray-800 text-sm">Half Day Certification List</p>
            <p class="text-xs text-gray-400 mt-0.5">Each half day deducts 0.5 credits from the selected leave type</p>
        </div>
        <div class="cert-apply-wrap">
            <button onclick="openApplyPanel()"
                    class="flex items-center gap-2 px-5 text-sm text-white font-semibold rounded-lg transition"
                    style="background:#1a3a1a; padding-top:8px; padding-bottom:8px;"
                    onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                File Certification
            </button>
        </div>
    </div>

    {{-- Tab Bar --}}
    <div class="app-tab-bar">
        <button class="tab-btn active" id="tabActive" onclick="switchUserTab('active')">My Applications</button>
        <button class="tab-btn"        id="tabHistory" onclick="switchUserTab('history')">History</button>
    </div>

    {{-- ══ TAB: My Applications (PENDING) ══ --}}
    <div id="panelActive" class="tab-panel">
        <div class="px-6 py-4" style="border-bottom:1px solid #f9fafb;">
            <div class="filter-row">
                <div class="rel rel-search">
                    <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search…" id="searchActive" oninput="filterActive()">
                </div>
                <div class="rel">
                    <select id="filterActiveStatus" onchange="filterActive()">
                        <option value="">All Status</option>
                        <option value="PENDING" selected>Pending</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="rel">
                    <select id="filterActiveMonth" onchange="filterActive()">
                        <option value="">All Months</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}">
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Half Day ID</th><th>Name</th><th>Application Date</th>
                        <th>Date of Absence</th><th>Period</th><th>Leave Type</th>
                        <th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="activeTbody">
                    @php $activeHDs = $halfDays->where('status', 'PENDING'); @endphp
                    @forelse($activeHDs as $hd)
                    <tr class="hd-row active-row"
                        data-status="{{ $hd->status }}"
                        data-month="{{ \Carbon\Carbon::parse($hd->application_date)->format('m') }}"
                        data-hd-id="{{ $hd->half_day_id }}"
                        onclick="openDetailPanel({{ $hd->half_day_id }}, event)">
                        <td class="font-bold font-mono">{{ $hd->half_day_id }}</td>
                        <td class="font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                        <td class="text-gray-500">{{ \Carbon\Carbon::parse($hd->application_date)->format('M d, Y') }}</td>
                        <td class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($hd->date_of_absence)->format('M d, Y') }}</td>
                        <td>
                            <span class="period-pill {{ $hd->time_period === 'AM' ? 'period-am' : 'period-pm' }}">
                                {{ $hd->time_period }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-xs">{{ $hd->leaveType->type_name ?? '—' }}</td>
                        <td>
                            @php
                                $sc = match(strtoupper($hd->status)) {
                                    'APPROVED'  => 'badge-approved',
                                    'REJECTED'  => 'badge-rejected',
                                    'CANCELLED' => 'badge-cancelled',
                                    default     => 'badge-pending',
                                };
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                        </td>
                        <td class="text-center pr-4" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this,event)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                </button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')); openDetailPanel({{ $hd->half_day_id }}, event)">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    @if($hd->status === 'PENDING')
                                    <button class="action-item danger" onclick="cancelHd({{ $hd->half_day_id }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="activeEmptyRow">
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No half day certifications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ TAB: History ══ --}}
    <div id="panelHistory" class="tab-panel hidden">
        <div class="px-6 py-4" style="border-bottom:1px solid #f9fafb;">
            <div style="margin-bottom:10px;">
                <p class="font-bold text-gray-800 text-sm">Half Day History</p>
                <p class="text-xs text-gray-400 mt-0.5">All approved, rejected, and cancelled half day certifications</p>
            </div>
            <div class="filter-row">
                <div class="rel rel-search">
                    <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search…" id="searchHistory" oninput="filterHistory()">
                </div>
                <div class="rel">
                    <select id="filterHistoryStatus" onchange="filterHistory()">
                        <option value="">All Status</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="rel">
                    <select id="filterHistoryMonth" onchange="filterHistory()">
                        <option value="">All Months</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}">
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Half Day ID</th><th>Name</th><th>Application Date</th>
                        <th>Date of Absence</th><th>Period</th><th>Leave Type</th>
                        <th>Updated Date</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="historyTbody">
                    @php $historyHDs = $halfDays->whereIn('status', ['APPROVED','REJECTED','CANCELLED']); @endphp
                    @forelse($historyHDs as $hd)
                    @php
                        $histMonth = ($hd->approved_date ?? $hd->date_of_absence)
                            ? \Carbon\Carbon::parse($hd->approved_date ?? $hd->date_of_absence)->format('m')
                            : \Carbon\Carbon::parse($hd->date_of_absence)->format('m');
                    @endphp
                    <tr class="hd-row history-row"
                        data-status="{{ $hd->status }}"
                        data-month="{{ $histMonth }}"
                        data-hd-id="{{ $hd->half_day_id }}"
                        onclick="openDetailPanel({{ $hd->half_day_id }}, event)">
                        <td class="font-bold font-mono">{{ $hd->half_day_id }}</td>
                        <td class="font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                        <td class="text-gray-500">{{ \Carbon\Carbon::parse($hd->application_date)->format('M d, Y') }}</td>
                        <td class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($hd->date_of_absence)->format('M d, Y') }}</td>
                        <td>
                            <span class="period-pill {{ $hd->time_period === 'AM' ? 'period-am' : 'period-pm' }}">
                                {{ $hd->time_period }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-xs">{{ $hd->leaveType->type_name ?? '—' }}</td>
                        <td class="text-gray-500 text-xs">
                            {{ $hd->approved_date ? \Carbon\Carbon::parse($hd->approved_date)->format('M d, Y') : '—' }}
                        </td>
                        <td>
                            @php
                                $sc = match(strtoupper($hd->status)) {
                                    'APPROVED'  => 'badge-approved',
                                    'REJECTED'  => 'badge-rejected',
                                    'CANCELLED' => 'badge-cancelled',
                                    default     => 'badge-pending',
                                };
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                        </td>
                        <td class="text-center pr-4" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this,event)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                </button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')); openDetailPanel({{ $hd->half_day_id }}, event)">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="historyEmptyRow">
                        <td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No approved, rejected, or cancelled certifications yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end Table Card --}}

{{-- Overlay --}}
<div id="panelOverlay" onclick="closeAllPanels()"></div>

{{-- ════ DETAIL PANEL ════ --}}
<div id="detailPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2>Half Day Certification Details</h2>
                <p id="dpSubtitle">Loading…</p>
            </div>
            <button class="panel-close" onclick="closeDetailPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body" id="dpBody"></div>
        <div class="panel-footer panel-footer-spaced">
            <button class="btn-pdf" onclick="viewPdfFromPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                View PDF
            </button>
            <div id="dpActionBtns"></div>
        </div>
    </div>
</div>

{{-- ════ APPLY PANEL ════ --}}
<div id="applyPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2>File Certification for Half Day</h2>
                <p>Deducts 0.5 leave credits upon approval</p>
            </div>
            <button class="panel-close" onclick="closeApplyPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="applyForm">
                @csrf
                {{-- Personal Info --}}
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <p class="section-card-title">Personal Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div><label class="field-label">First Name</label><input type="text" class="form-field" value="{{ $employee->first_name }}" disabled></div>
                        <div><label class="field-label">Office / Department</label><input type="text" class="form-field" value="{{ $employee->department->department_name ?? 'OPAG' }}" disabled></div>
                        <div><label class="field-label">Last Name</label><input type="text" class="form-field" value="{{ $employee->last_name }}" disabled></div>
                        <div><label class="field-label">Position</label><input type="text" class="form-field" value="{{ $employee->position->position_name ?? '—' }}" disabled></div>
                        <div><label class="field-label">Date of Filing</label><input type="text" class="form-field" value="{{ now()->format('F d, Y') }}" disabled></div>
                        <div><label class="field-label">Salary</label><input type="text" class="form-field" value="₱{{ number_format($employee->salary, 2) }}" disabled></div>
                    </div>
                </div>

                {{-- Certification Details --}}
                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <p class="section-card-title">Certification Details</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <label class="field-label">Leave Type <span class="req">*</span></label>
                            <select id="f_leave_type_id" name="leave_type_id" class="form-field" onchange="selectLeaveType(this)">
                                <option value="" disabled>Select Leave Type</option>
                                @foreach($leaveTypes as $lt)
                                @php
                                    $cb  = $creditBalances[$lt->leave_type_id] ?? null;
                                    $rawRemaining = $cb ? $cb->getRawOriginal('remaining_balance') : '0';
                                @endphp
                                <option value="{{ $lt->leave_type_id }}"
                                        data-balance-id="{{ $cb->credit_balance_id ?? '' }}"
                                        data-remaining="{{ $rawRemaining }}"
                                        {{ $lt->type_name == 'Vacation Leave' ? 'selected' : '' }}>
                                    {{ $lt->type_name }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="f_credit_balance_id" name="credit_balance_id">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_leave_type">Please select a leave type.</p>

                            <div id="balanceDisplay" class="hidden mt-2 rounded-xl p-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <p class="text-xs font-semibold text-green-700">Available Balance</p>
                                <p class="text-xl font-bold text-green-800 mt-0.5"><span id="balanceVal">0.000</span> <span class="text-xs font-normal text-green-600">days</span></p>
                                <p class="text-xs text-green-600 mt-0.5">0.5 days will be deducted upon approval</p>
                            </div>
                            <div id="insufficientWarn" class="hidden mt-2 p-3 rounded-xl text-xs font-semibold text-red-600" style="background:#fef2f2;border:1px solid #fecaca;">
                                ⚠ Insufficient balance. You need at least 0.5 days.
                            </div>
                        </div>

                        {{-- Custom Calendar --}}
                        <div>
                            <label class="field-label">Date of Absence <span class="req">*</span></label>
                            <div class="cal-wrap" id="hdCalWrap">
                                <button type="button" class="cal-trigger" id="hdCalTrigger" onclick="hdToggleCalendar(event)">
                                    <span class="cal-trigger-text placeholder" id="hdCalTriggerText">Click to select date…</span>
                                    <svg class="w-4 h-4 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <div class="cal-popup" id="hdCalPopup" style="display:none;">
                                    <div class="cal-header">
                                        <button type="button" class="cal-nav" onclick="hdCalNav(-1)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </button>
                                        <span class="cal-month-label" id="hdCalMonthLabel"></span>
                                        <button type="button" class="cal-nav" onclick="hdCalNav(1)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                    <div class="cal-weekdays">
                                        <div class="cal-wd">Su</div><div class="cal-wd">Mo</div><div class="cal-wd">Tu</div>
                                        <div class="cal-wd">We</div><div class="cal-wd">Th</div><div class="cal-wd">Fr</div><div class="cal-wd">Sa</div>
                                    </div>
                                    <div class="cal-days" id="hdCalDays"></div>
                                    <div class="cal-legend">
                                        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#fee2e2;border:1px solid #fecaca;"></div> Leave conflict</div>
                                        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#fef9c3;border:1px solid #fde68a;"></div> Half-day filed</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="f_date_of_absence" name="date_of_absence">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_date">Please select a date.</p>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_date_conflict"></p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Time Period <span class="req">*</span></label>
                            <div class="period-toggle">
                                <button type="button" class="period-btn" id="btnAM" onclick="selectPeriod('AM')">🌅 AM (Morning)</button>
                                <button type="button" class="period-btn" id="btnPM" onclick="selectPeriod('PM')">🌆 PM (Afternoon)</button>
                            </div>
                            <input type="hidden" id="f_time_period" name="time_period">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_period">Please select a time period.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Reason / Remarks <span class="req">*</span></label>
                            <textarea id="f_reason" name="reason" rows="2" class="form-field" placeholder="State your reason for half day absence…"></textarea>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_reason">Please provide a reason or remarks.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <button onclick="closeApplyPanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitHd()" id="submitBtn"
                    class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition"
                    style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                Submit Certification
            </button>
        </div>
    </div>
</div>

{{-- ════ CANCEL MODAL ════ --}}
<div id="cancelModal" style="position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);opacity:0;transition:opacity 0.2s;">
    <div id="cancelModalCard" style="background:#fff;border-radius:20px;padding:32px;width:420px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div class="flex items-center gap-4 mb-5">
            <div style="width:52px;height:52px;border-radius:16px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:26px;height:26px;color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800" style="font-size:16px;">Cancel Certification?</h3>
                <p class="text-sm text-gray-500 mt-0.5">This action cannot be undone.</p>
            </div>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p class="text-sm font-medium" style="color:#9a3412;">Once cancelled, this certification will be permanently marked as <strong>Cancelled</strong>. No leave credits will be affected.</p>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeCancelModal()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep It</button>
            <button onclick="confirmCancel()" id="confirmCancelBtn"
                    class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl flex items-center gap-2"
                    style="background:#dc2626;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

{{-- ════ SUCCESS MODAL ════ --}}
<div id="successModal" style="position:fixed;inset:0;z-index:250;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);opacity:0;transition:opacity 0.2s;">
    <div id="successModalCard" style="background:#fff;border-radius:24px;padding:36px 32px;width:440px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.28s cubic-bezier(0.34,1.56,0.64,1);text-align:center;">
        <div style="width:72px;height:72px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg style="width:36px;height:36px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 8px;">Certification Submitted!</h3>
        <p style="font-size:13px;color:#6b7280;margin:0 0 6px;">Your half day certification has been filed successfully.</p>
        <p style="font-size:12px;color:#9ca3af;margin:0 0 28px;">It is now <strong style="color:#854d0e;background:#fef9c3;padding:2px 8px;border-radius:6px;">Pending</strong> and awaiting approval from your supervisor.</p>
        <div style="background:#f9fafb;border-radius:12px;padding:14px 16px;margin-bottom:24px;text-align:left;" id="successModalDetails"></div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeSuccessModal(false)"
                    style="flex:1;padding:12px;background:#f3f4f6;color:#374151;border:none;border-radius:12px;font-size:13px;font-weight:600;cursor:pointer;transition:background 0.15s;"
                    onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                Close
            </button>
            <button onclick="closeSuccessModal(true)"
                    style="flex:1;padding:12px;background:#1a3a1a;color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.15s;"
                    onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                View Certificate
            </button>
        </div>
    </div>
</div>

{{-- ════ PDF VIEWER MODAL ════ --}}
<div id="pdfModal" style="position:fixed;inset:0;z-index:300;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);opacity:0;transition:opacity 0.2s;">
    <div id="pdfModalCard" style="background:#fff;border-radius:16px;width:880px;max-width:96vw;height:92vh;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,0.35);transform:scale(0.93);transition:transform 0.28s cubic-bezier(0.34,1.56,0.64,1);overflow:hidden;position:relative;">

        {{-- ── Dark Green Header (matches screenshot) ── --}}
        <div style="background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);padding:18px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <p id="pdfModalTitle" style="font-size:16px;font-weight:700;color:#fff;margin:0 0 3px;">Half Day Certification</p>
                <p id="pdfModalSubtitle" style="font-size:12px;color:rgba(255,255,255,0.6);margin:0;text-transform:uppercase;letter-spacing:0.04em;">{{ strtoupper($employee->last_name) }}, {{ strtoupper($employee->first_name) }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                {{-- Print / Save PDF button --}}
                <button onclick="triggerPdfPrint()"
                        style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;font-size:13px;font-weight:700;background:#fff;color:#1a3a1a;border:none;border-radius:8px;cursor:pointer;transition:background 0.15s;white-space:nowrap;"
                        onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#fff'">
                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print / Save PDF
                </button>
                {{-- Close button --}}
                <button onclick="closePdfModal()"
                        style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.15);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff;transition:background 0.15s;flex-shrink:0;"
                        onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Loading Spinner ── --}}
        <div id="pdfLoading" style="position:absolute;inset:65px 0 0 0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;background:#f8f9fa;z-index:1;">
            <svg style="width:38px;height:38px;color:#2d5a1b;animation:spin 0.9s linear infinite;" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p style="font-size:13px;color:#6b7280;font-weight:500;margin:0;">Loading document…</p>
        </div>

        {{-- ── iFrame ── --}}
        <iframe id="pdfFrame" src=""
                style="flex:1;border:none;background:#f8f9fa;display:block;"
                onload="document.getElementById('pdfLoading').style.display='none';"></iframe>
    </div>
</div>

{{-- Toast --}}
<div id="toast">
    <div id="toastIcon" class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"></div>
    <div>
        <p class="text-sm font-bold text-gray-800" id="toastTitle"></p>
        <p class="text-xs text-gray-500 mt-0.5" id="toastMsg"></p>
    </div>
</div>

<script>
/* ════════════════════════════════════════════════════════
   SERVER DATA
════════════════════════════════════════════════════════ */
const CSRF       = "{{ csrf_token() }}";
const STORE_URL  = "{{ route('halfday.store') }}";
const CANCEL_URL = "{{ url('application/halfday') }}";
const PDF_URL    = "{{ url('application/halfday') }}";

const HD_DATA = {!! json_encode($halfDays->keyBy('half_day_id')->map(function($h) {
    return [
        'half_day_id'      => $h->half_day_id,
        'leave_type'       => optional($h->leaveType)->type_name ?? '—',
        'application_date' => \Carbon\Carbon::parse($h->application_date)->format('M d, Y'),
        'date_of_absence'  => \Carbon\Carbon::parse($h->date_of_absence)->format('M d, Y'),
        'time_period'      => $h->time_period,
        'reason'           => $h->reason ?? '',
        'status'           => $h->status,
        'approved_date'    => $h->approved_date
            ? \Carbon\Carbon::parse($h->approved_date)->format('M d, Y') : '—',
        'updated_at'       => $h->updated_at ? \Carbon\Carbon::parse($h->updated_at)->timestamp : 0,
    ];
})) !!};

const LEAVE_RANGES       = @json($leaveRanges);
const EXISTING_HALF_DAYS = @json($existingHalfDays);

/* ════════════════════════════════════════════════════════
   TRUNCATE helper
════════════════════════════════════════════════════════ */
function truncate3(value) {
    const str = String(value ?? '0');
    const dot  = str.indexOf('.');
    if (dot === -1) return str + '.000';
    const decimals = (str.slice(dot + 1) + '000').slice(0, 3);
    return str.slice(0, dot + 1) + decimals;
}

/* ════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════ */
let activePanelHdId = null;
let pendingCancelId = null;
let successHdId     = null;

/* ════════════════════════════════════════════════════════
   TAB SWITCHING
════════════════════════════════════════════════════════ */
function switchUserTab(tab) {
    ['active','history'].forEach(t => {
        const key = t.charAt(0).toUpperCase() + t.slice(1);
        document.getElementById('tab' + key).classList.toggle('active', t === tab);
        document.getElementById('panel' + key).classList.toggle('hidden', t !== tab);
    });
}

function sortActive() {
    const tbody = document.getElementById('activeTbody');
    if (!tbody) return;
    [...tbody.querySelectorAll('tr.active-row')]
        .sort((a, b) => (HD_DATA[b.dataset.hdId]?.updated_at ?? 0) - (HD_DATA[a.dataset.hdId]?.updated_at ?? 0))
        .forEach(r => tbody.appendChild(r));
}

function sortHistoryRows() {
    const tbody = document.getElementById('historyTbody');
    if (!tbody) return;
    [...tbody.querySelectorAll('tr.history-row')]
        .sort((a, b) => (HD_DATA[b.dataset.hdId]?.updated_at ?? 0) - (HD_DATA[a.dataset.hdId]?.updated_at ?? 0))
        .forEach(r => tbody.appendChild(r));
}

/* ════════════════════════════════════════════════════════
   FILTER — Active tab
════════════════════════════════════════════════════════ */
function filterActive() {
    const q  = document.getElementById('searchActive').value.toLowerCase();
    const st = document.getElementById('filterActiveStatus').value;
    const mo = document.getElementById('filterActiveMonth').value;
    document.querySelectorAll('#activeTbody .active-row').forEach(r => {
        r.style.display = (
            (!q  || r.textContent.toLowerCase().includes(q)) &&
            (!st || r.dataset.status === st) &&
            (!mo || r.dataset.month === mo)
        ) ? '' : 'none';
    });
}

/* ════════════════════════════════════════════════════════
   FILTER — History tab
════════════════════════════════════════════════════════ */
function filterHistory() {
    const q  = document.getElementById('searchHistory').value.toLowerCase();
    const st = document.getElementById('filterHistoryStatus').value;
    const mo = document.getElementById('filterHistoryMonth').value;
    let visible = 0;
    document.querySelectorAll('#historyTbody .history-row').forEach(r => {
        const ok = (
            (!q  || r.textContent.toLowerCase().includes(q)) &&
            (!st || r.dataset.status === st) &&
            (!mo || r.dataset.month === mo)
        );
        r.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });
    const noResults = document.getElementById('historyNoResults');
    if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
}

/* ════════════════════════════════════════════════════════
   CALENDAR PICKER
════════════════════════════════════════════════════════ */
const HD_MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
let hdCalYear, hdCalMonth, hdCalOpen = false, hdCalSelected = null;

function hdCalInit() {
    const now = new Date();
    hdCalYear  = now.getFullYear();
    hdCalMonth = now.getMonth();
    hdCalRender();
}

function hdCalRender() {
    document.getElementById('hdCalMonthLabel').textContent = HD_MONTH_NAMES[hdCalMonth] + ' ' + hdCalYear;
    const grid  = document.getElementById('hdCalDays');
    const today = new Date(); today.setHours(0, 0, 0, 0);
    const todayStr    = hdToYMD(today);
    const firstDay    = new Date(hdCalYear, hdCalMonth, 1).getDay();
    const daysInMonth = new Date(hdCalYear, hdCalMonth + 1, 0).getDate();
    const prevDays    = new Date(hdCalYear, hdCalMonth, 0).getDate();
    grid.innerHTML    = '';

    for (let i = 0; i < firstDay; i++) {
        grid.appendChild(hdMakeBtn(prevDays - firstDay + 1 + i, 'cal-day cal-other'));
    }
    for (let day = 1; day <= daysInMonth; day++) {
        const date    = new Date(hdCalYear, hdCalMonth, day);
        const dateStr = hdToYMD(date);
        const dow     = date.getDay();
        const isPast  = date < today && dateStr !== todayStr;
        const isWknd  = dow === 0 || dow === 6;
        let cls = 'cal-day';
        if (dateStr === todayStr) cls += ' cal-today';
        if (isWknd) { cls += ' cal-weekend'; }
        else if (isPast) { cls += ' cal-disabled'; }
        else {
            if (hdIsLeaveConflict(dateStr))   cls += ' cal-leave-conflict';
            else if (hdIsHdConflict(dateStr)) cls += ' cal-halfday-conflict';
        }
        if (hdCalSelected === dateStr) cls += ' cal-selected';
        const btn = hdMakeBtn(day, cls);
        const isBlocked = isWknd || isPast
            || cls.includes('cal-leave-conflict')
            || cls.includes('cal-halfday-conflict');
        if (!isBlocked) btn.onclick = () => hdCalSelectDate(dateStr);
        grid.appendChild(btn);
    }
    const total = firstDay + daysInMonth;
    const rem   = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let i = 1; i <= rem; i++) grid.appendChild(hdMakeBtn(i, 'cal-day cal-other'));
}

function hdMakeBtn(text, cls) {
    const b = document.createElement('button');
    b.type = 'button'; b.className = cls; b.textContent = text;
    return b;
}

function hdCalSelectDate(dateStr) {
    hdCalSelected = dateStr;
    document.getElementById('f_date_of_absence').value = dateStr;
    const d     = new Date(dateStr + 'T00:00:00');
    const label = d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
    const trig  = document.getElementById('hdCalTriggerText');
    trig.textContent = label;
    trig.classList.remove('placeholder');
    document.getElementById('err_date').classList.add('hidden');
    document.getElementById('err_date_conflict').classList.add('hidden');
    hdCloseCalendar();
    checkDateConflict();
}

function hdCalNav(dir) {
    hdCalMonth += dir;
    if (hdCalMonth > 11) { hdCalMonth = 0; hdCalYear++; }
    if (hdCalMonth < 0)  { hdCalMonth = 11; hdCalYear--; }
    hdCalRender();
}

function hdToggleCalendar(e) {
    if (e) e.stopPropagation();
    hdCalOpen ? hdCloseCalendar() : hdOpenCalendar();
}

function hdOpenCalendar() {
    hdCalInit();
    document.getElementById('hdCalPopup').style.display = 'block';
    document.getElementById('hdCalTrigger').classList.add('open');
    hdCalOpen = true;
}

function hdCloseCalendar() {
    document.getElementById('hdCalPopup').style.display = 'none';
    document.getElementById('hdCalTrigger').classList.remove('open');
    hdCalOpen = false;
}

function hdIsLeaveConflict(dateStr) {
    return LEAVE_RANGES.some(range => dateStr >= range.start && dateStr <= range.end);
}

function hdIsHdConflict(dateStr) {
    return EXISTING_HALF_DAYS.some(hd => hd.date === dateStr);
}

function hdToYMD(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('hdCalWrap')?.contains(e.target)) hdCloseCalendar();
});

/* ════════════════════════════════════════════════════════
   CONFLICT CHECK
════════════════════════════════════════════════════════ */
function checkDateConflict() {
    const dateVal     = document.getElementById('f_date_of_absence').value;
    const errConflict = document.getElementById('err_date_conflict');
    if (!dateVal) { errConflict.classList.add('hidden'); return false; }

    if (LEAVE_RANGES.some(range => dateVal >= range.start && dateVal <= range.end)) {
        errConflict.textContent = 'This date is already covered by an existing leave application.';
        errConflict.classList.remove('hidden');
        return true;
    }
    if (EXISTING_HALF_DAYS.some(hd => hd.date === dateVal)) {
        errConflict.textContent = 'You already have a half-day certification for this date.';
        errConflict.classList.remove('hidden');
        return true;
    }
    errConflict.classList.add('hidden');
    return false;
}

/* ════════════════════════════════════════════════════════
   LEAVE TYPE SELECT
════════════════════════════════════════════════════════ */
function selectLeaveType(sel) {
    const opt       = sel.options[sel.selectedIndex];
    const remaining = parseFloat(opt.dataset.remaining) || 0;
    const balanceId = opt.dataset.balanceId || '';

    document.getElementById('f_credit_balance_id').value = balanceId;
    document.getElementById('err_leave_type').classList.add('hidden');

    const display = document.getElementById('balanceDisplay');
    const warn    = document.getElementById('insufficientWarn');

    if (balanceId) {
        document.getElementById('balanceVal').textContent = truncate3(remaining);
        display.classList.remove('hidden');
        const isInsufficient = remaining < 0.5;
        warn.classList.toggle('hidden', !isInsufficient);
        document.getElementById('submitBtn').disabled      = isInsufficient;
        document.getElementById('submitBtn').style.opacity = isInsufficient ? '0.5' : '1';
    } else {
        display.classList.add('hidden');
        warn.classList.add('hidden');
    }
}

/* ════════════════════════════════════════════════════════
   AM / PM TOGGLE
════════════════════════════════════════════════════════ */
function selectPeriod(period) {
    document.getElementById('f_time_period').value = period;
    document.getElementById('err_period').classList.add('hidden');
    document.getElementById('btnAM').className = 'period-btn' + (period === 'AM' ? ' sel-am' : '');
    document.getElementById('btnPM').className = 'period-btn' + (period === 'PM' ? ' sel-pm' : '');
}

/* ════════════════════════════════════════════════════════
   PANELS
════════════════════════════════════════════════════════ */
function openApplyPanel() {
    resetApplyForm();
    document.getElementById('applyPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeApplyPanel() {
    hdCloseCalendar();
    document.getElementById('applyPanel').classList.remove('open');
    hidePanelOverlayIfNoneOpen();
    document.body.style.overflow = '';
}
function closeAllPanels() {
    hdCloseCalendar();
    ['detailPanel','applyPanel'].forEach(id => document.getElementById(id).classList.remove('open'));
    document.getElementById('panelOverlay').classList.remove('show');
    document.body.style.overflow = '';
    activePanelHdId = null;
}
function hidePanelOverlayIfNoneOpen() {
    if (!document.querySelector('.slide-panel.open')) document.getElementById('panelOverlay').classList.remove('show');
}
function resetApplyForm() {
    hdCalSelected = null;
    hdCalOpen     = false;
    document.getElementById('hdCalPopup').style.display = 'none';
    document.getElementById('hdCalTrigger').classList.remove('open');
    const trigText = document.getElementById('hdCalTriggerText');
    trigText.textContent = 'Click to select date…';
    trigText.classList.add('placeholder');
    document.getElementById('applyForm').reset();
    document.getElementById('f_time_period').value = '';
    document.getElementById('btnAM').className = 'period-btn';
    document.getElementById('btnPM').className = 'period-btn';
    document.getElementById('submitBtn').disabled      = false;
    document.getElementById('submitBtn').style.opacity = '1';
    ['err_leave_type','err_date','err_period','err_date_conflict','err_reason'].forEach(id => {
        const el = document.getElementById(id); if (el) el.classList.add('hidden');
    });
    const sel = document.getElementById('f_leave_type_id');
    if (sel) {
        if (!sel.value) {
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value) { sel.selectedIndex = i; break; }
            }
        }
        if (sel.value) selectLeaveType(sel);
        else {
            document.getElementById('f_credit_balance_id').value = '';
            document.getElementById('balanceDisplay').classList.add('hidden');
            document.getElementById('insufficientWarn').classList.add('hidden');
        }
    }
}

/* ════════════════════════════════════════════════════════
   DETAIL PANEL
════════════════════════════════════════════════════════ */
function openDetailPanel(hdId, e) {
    if (e) e.stopPropagation();
    const d = HD_DATA[hdId]; if (!d) return;
    activePanelHdId = hdId;

    document.getElementById('dpSubtitle').textContent = `${d.leave_type} · ${d.time_period} · ${d.date_of_absence}`;

    const SC = { PENDING:'#fef9c3|#854d0e', APPROVED:'#dcfce7|#14532d', REJECTED:'#fee2e2|#991b1b', CANCELLED:'#f3f4f6|#6b7280' };
    const [sBg,sC] = (SC[d.status] || '#f3f4f6|#6b7280').split('|');
    const sl = d.status.charAt(0) + d.status.slice(1).toLowerCase();
    const [pBg,pC] = (d.time_period === 'AM' ? '#dbeafe|#1e40af' : '#ede9fe|#5b21b6').split('|');

    let html = `<div class="dp-card">
        <div class="dp-section-heading">
            <div class="dp-section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <p class="dp-section-title">Certification Details</p>
        </div>
        <div class="dp-grid">
            <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
            <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>
            <div class="dp-field"><label>Date of Absence</label><p style="font-weight:700;">${d.date_of_absence}</p></div>
            <div class="dp-field"><label>Time Period</label><p>
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${pBg};color:${pC};">● ${d.time_period}</span>
            </p></div>
            <div class="dp-field"><label>Credits to Deduct</label><p>0.5 days upon approval</p></div>
            <div class="dp-field"><label>Status</label><p>
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${sl}</span>
            </p></div>`;

    if (d.status === 'APPROVED') {
        html += `<div class="dp-field"><label>Approved Date</label><p>${d.approved_date}</p></div>`;
    }
    if (d.reason) {
        html += `<div class="dp-field span2"><label>Reason / Remarks</label><p>${d.reason}</p></div>`;
    }
    html += `</div></div><div style="height:8px;"></div>`;
    document.getElementById('dpBody').innerHTML = html;

    const labels = { APPROVED:'Certification Approved', REJECTED:'Certification Rejected', CANCELLED:'Certification Cancelled' };
    document.getElementById('dpActionBtns').innerHTML = d.status === 'PENDING'
        ? `<button class="btn-cancel-action" onclick="cancelHd(${hdId})">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
               Cancel Certification
           </button>`
        : `<span style="font-size:12px;color:#6b7280;font-weight:600;">${labels[d.status] || 'Status: '+sl}</span>`;

    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('open');
    hidePanelOverlayIfNoneOpen();
    document.body.style.overflow = '';
    activePanelHdId = null;
}
function viewPdfFromPanel() {
    if (activePanelHdId) openPdfModal(activePanelHdId);
}

/* ════════════════════════════════════════════════════════
   PDF VIEWER MODAL
════════════════════════════════════════════════════════ */
function openPdfModal(id) {
    successHdId = id;
    const m       = document.getElementById('pdfModal');
    const c       = document.getElementById('pdfModalCard');
    const frame   = document.getElementById('pdfFrame');
    const loading = document.getElementById('pdfLoading');

    loading.style.display = 'flex';
    frame.src = `${PDF_URL}/${id}/pdf`;

     const subtitle = document.getElementById('pdfModalSubtitle');
    if (subtitle && HD_DATA[id]) {
        subtitle.textContent = `${HD_DATA[id].leave_type} · ${HD_DATA[id].date_of_absence} · ${HD_DATA[id].time_period}`;
    }

    m.style.display = 'flex';
    c.style.transform = 'scale(0.93)';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        m.style.opacity = '1';
        c.style.transform = 'scale(1)';
    }));
    document.body.style.overflow = 'hidden';
}

function closePdfModal() {
    const m = document.getElementById('pdfModal');
    m.style.opacity = '0';
    document.getElementById('pdfModalCard').style.transform = 'scale(0.93)';
    setTimeout(() => {
        m.style.display = 'none';
        document.getElementById('pdfFrame').src = '';
    }, 220);
    document.body.style.overflow = '';
}

function triggerPdfPrint() {
    const frame = document.getElementById('pdfFrame');
    if (frame && frame.src) {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    }
}

function openPdfInTab() {
    if (successHdId) window.open(`${PDF_URL}/${successHdId}/pdf`, '_blank');
}

/* ════════════════════════════════════════════════════════
   SUCCESS MODAL
════════════════════════════════════════════════════════ */
function openSuccessModal(details) {
    successHdId = details.hdId ?? null;

    document.getElementById('successModalDetails').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin:0 0 3px;">Date of Absence</p>
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0;">${details.date}</p>
            </div>
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin:0 0 3px;">Time Period</p>
                <p style="font-size:13px;font-weight:600;margin:0;">
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${details.period==='AM'?'#dbeafe':'#ede9fe'};color:${details.period==='AM'?'#1e40af':'#5b21b6'};">
                        ● ${details.period}
                    </span>
                </p>
            </div>
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin:0 0 3px;">Leave Type</p>
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0;">${details.leaveType}</p>
            </div>
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin:0 0 3px;">Credits to Deduct</p>
                <p style="font-size:13px;font-weight:600;color:#dc2626;margin:0;">0.5 days upon approval</p>
            </div>
        </div>`;

    const m = document.getElementById('successModal');
    const c = document.getElementById('successModalCard');
    m.style.display = 'flex';
    c.style.transform = 'scale(0.93)';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        m.style.opacity = '1';
        c.style.transform = 'scale(1)';
    }));
    document.body.style.overflow = 'hidden';
}

function closeSuccessModal(viewPdf = false) {
    const m = document.getElementById('successModal');
    m.style.opacity = '0';
    document.getElementById('successModalCard').style.transform = 'scale(0.93)';

    setTimeout(() => {
        m.style.display = 'none';
        document.body.style.overflow = '';

        if (viewPdf && successHdId) {
            // Open PDF modal first, then reload in background after PDF modal is closed
            openPdfModal(successHdId);
        } else {
            location.reload();
        }
    }, 220);
}

// Override closePdfModal to reload after closing from success flow
const _originalClosePdfModal = closePdfModal;
function closePdfModal() {
    const m = document.getElementById('pdfModal');
    m.style.opacity = '0';
    document.getElementById('pdfModalCard').style.transform = 'scale(0.93)';
    setTimeout(() => {
        m.style.display = 'none';
        document.getElementById('pdfFrame').src = '';
        location.reload();
    }, 220);
    document.body.style.overflow = '';
}

/* ════════════════════════════════════════════════════════
   SUBMIT
════════════════════════════════════════════════════════ */
function submitHd() {
    const ltId    = document.getElementById('f_leave_type_id').value;
    const cbId    = document.getElementById('f_credit_balance_id').value;
    const absDate = document.getElementById('f_date_of_absence').value;
    const period  = document.getElementById('f_time_period').value;
    const reason  = document.getElementById('f_reason').value.trim();

    let hasError = false;

    if (!ltId || !cbId) { document.getElementById('err_leave_type').classList.remove('hidden'); hasError = true; }
    else                 { document.getElementById('err_leave_type').classList.add('hidden'); }

    if (!absDate) { document.getElementById('err_date').classList.remove('hidden'); hasError = true; }
    else          { document.getElementById('err_date').classList.add('hidden'); }

    if (!period) { document.getElementById('err_period').classList.remove('hidden'); hasError = true; }
    else         { document.getElementById('err_period').classList.add('hidden'); }

    if (!reason) { document.getElementById('err_reason').classList.remove('hidden'); hasError = true; }
    else         { document.getElementById('err_reason').classList.add('hidden'); }

    if (hasError) { showToast('Incomplete Form', 'Please fill in all required fields.', 'error'); return; }

    if (LEAVE_RANGES.some(range => absDate >= range.start && absDate <= range.end)) {
        document.getElementById('err_date_conflict').textContent = 'This date is already covered by an existing leave application.';
        document.getElementById('err_date_conflict').classList.remove('hidden');
        showToast('Date Conflict', 'This date is covered by an existing leave application.', 'error');
        return;
    }
    if (EXISTING_HALF_DAYS.some(hd => hd.date === absDate)) {
        document.getElementById('err_date_conflict').textContent = 'You already have a half-day certification for this date.';
        document.getElementById('err_date_conflict').classList.remove('hidden');
        showToast('Duplicate Entry', 'A half-day certification already exists for this date.', 'error');
        return;
    }
    document.getElementById('err_date_conflict').classList.add('hidden');

    const selOpt    = document.getElementById('f_leave_type_id');
    const remaining = parseFloat(selOpt.options[selOpt.selectedIndex]?.dataset.remaining) || 0;
    if (remaining < 0.5) {
        showToast('Insufficient Balance', 'You need at least 0.5 leave credits to file this certification.', 'error');
        return;
    }

    const leaveTypeName = selOpt.options[selOpt.selectedIndex]?.text || '—';
    const displayDate   = new Date(absDate + 'T00:00:00').toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

    const btn = document.getElementById('submitBtn');
    btn.innerHTML = `<svg class="animate-spin w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
    </svg> Submitting…`;
    btn.disabled = true;

    const body = new FormData();
    body.append('leave_type_id',     ltId);
    body.append('credit_balance_id', cbId);
    body.append('date_of_absence',   absDate);
    body.append('time_period',       period);
    body.append('reason',            document.getElementById('f_reason').value);
    body.append('_token',            CSRF);

    fetch(STORE_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body })
    .then(async res => {
        if (!res.ok) {
            const errData = await res.json().catch(() => ({}));
            throw { type: 'http', status: res.status, message: errData.message || `Server error (${res.status}).` };
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            closeApplyPanel();
            openSuccessModal({
                date: displayDate,
                period,
                leaveType: leaveTypeName,
                hdId: data.half_day_id,
            });
        } else {
            showToast('Submission Failed', data.message || 'Something went wrong. Please try again.', 'error');
        }
    })
    .catch(err => {
        if (err.type === 'http') {
            if (err.status === 422)      showToast('Validation Error', err.message || 'Please check your inputs.', 'error');
            else if (err.status === 403) showToast('Unauthorized', 'You do not have permission to perform this action.', 'error');
            else if (err.status === 500) showToast('Server Error', 'An internal server error occurred.', 'error');
            else                         showToast('Request Failed', err.message || 'An unexpected error occurred.', 'error');
        } else {
            showToast('Network Error', 'Could not connect to the server.', 'error');
        }
    })
    .finally(() => {
        btn.innerHTML = 'Submit Certification';
        btn.disabled  = false;
    });
}

/* ════════════════════════════════════════════════════════
   CANCEL MODAL
════════════════════════════════════════════════════════ */
function cancelHd(id) {
    pendingCancelId = id;
    const m = document.getElementById('cancelModal'), c = document.getElementById('cancelModalCard');
    m.style.display = 'flex'; c.style.transform = 'scale(0.93)';
    requestAnimationFrame(() => requestAnimationFrame(() => { m.style.opacity='1'; c.style.transform='scale(1)'; }));
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    const m = document.getElementById('cancelModal'); m.style.opacity = '0';
    document.getElementById('cancelModalCard').style.transform = 'scale(0.93)';
    setTimeout(() => { m.style.display = 'none'; }, 200);
    document.body.style.overflow = ''; pendingCancelId = null;
}
function confirmCancel() {
    if (!pendingCancelId) return;
    const id = pendingCancelId, btn = document.getElementById('confirmCancelBtn');
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Cancelling…';
    btn.disabled = true;
    closeCancelModal(); closeDetailPanel();
    fetch(`${CANCEL_URL}/${id}/cancel`, {
        method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF}, body:new FormData(),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('Certification Cancelled','Your half day certification has been cancelled.','warning'); setTimeout(()=>location.reload(),1800); }
        else showToast('Error', data.message || 'Could not cancel.', 'error');
    })
    .catch(() => showToast('Network Error','Please check your connection.','error'))
    .finally(() => {
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Yes, Cancel It';
        btn.disabled = false;
    });
}

/* ════════════════════════════════════════════════════════
   VIEW PDF (from detail panel button)
════════════════════════════════════════════════════════ */
function viewPdf(id) {
    openPdfModal(id);
}

/* ════════════════════════════════════════════════════════
   ACTION MENUS
════════════════════════════════════════════════════════ */
function toggleMenu(btn, e) {
    if (e) e.stopPropagation();
    const dd = btn.nextElementSibling, isOpen = dd.classList.contains('open');
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
        dd.classList.add('open');
        const rect = btn.getBoundingClientRect();
        dd.style.top  = (rect.bottom + 4) + 'px';
        dd.style.left = (rect.right - 160) + 'px';
        dd.style.transform = window.innerHeight - rect.bottom < 120
            ? `translateY(calc(-100% - ${rect.height + 8}px))` : '';
    }
}
document.addEventListener('click', () => document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open')));
window.addEventListener('scroll', () => document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open')), true);

/* ════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════ */
function showToast(title, msg, type = 'success') {
    const map = {
        success: { bg:'#dcfce7', c:'#16a34a', p:'M5 13l4 4L19 7' },
        error:   { bg:'#fee2e2', c:'#dc2626', p:'M6 18L18 6M6 6l12 12' },
        warning: { bg:'#fef9c3', c:'#ca8a04', p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const s = map[type] || map.success;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    const icon = document.getElementById('toastIcon');
    icon.innerHTML  = `<svg class="w-5 h-5" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    icon.style.background = s.bg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

/* ════════════════════════════════════════════════════════
   KEYBOARD
════════════════════════════════════════════════════════ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        hdCloseCalendar();
        closePdfModal();
        closeDetailPanel();
        closeApplyPanel();
        closeCancelModal();
    }
});

/* ════════════════════════════════════════════════════════
   INIT
════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    filterActive();
    sortActive();
    sortHistoryRows();

    const histBody = document.getElementById('historyTbody');
    const noResultsRow = document.createElement('tr');
    noResultsRow.id = 'historyNoResults';
    noResultsRow.style.display = 'none';
    noResultsRow.innerHTML = `<td colspan="9" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">No records match your filter.</td>`;
    histBody.appendChild(noResultsRow);

    const tabParam = new URLSearchParams(window.location.search).get('tab');
    if (tabParam === 'history') switchUserTab('history');

    const sel = document.getElementById('f_leave_type_id');
    if (sel && sel.value) selectLeaveType(sel);
});

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast('Success','{{ session("success") }}','success'));
@endif
</script>

@endsection