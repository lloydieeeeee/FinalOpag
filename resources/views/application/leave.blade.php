@extends('layouts.app')
@section('title', 'Application for Leave')
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
        border:1px solid #f0f0f0; box-shadow:0 2px 12px rgba(0,0,0,0.05);
        position:relative; overflow:hidden;
    }
    .bal-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .bal-icon-dark  { background:rgba(255,255,255,0.18); }
    .bal-icon-green { background:#dcfce7; }

    /* ── Tabs ── */
    .tab-btn {
        flex-shrink:0;
        padding:10px 4px;
        font-size:14px;
        font-weight:500;
        color:#6b7280;
        border:none;
        background:none;
        border-bottom:2px solid transparent;
        cursor:pointer;
        transition:all 0.2s;
        white-space:nowrap;
    }
    .tab-btn.active { color:#1a3a1a; border-bottom-color:#2d5a1b; font-weight:700; }
    .tab-btn:hover:not(.active) { color:#374151; }

    /* ── Table ── */
    .data-table { width:100%; font-size:13px; border-collapse:collapse; }
    .data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
    .data-table th { padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
    .data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; text-align:center; vertical-align:middle; }
    .data-table tbody .leave-row { cursor:pointer; }
    .data-table tbody .leave-row:hover { background:#f0fdf4; }
    .data-table tbody .leave-row:hover td:nth-child(2) { color:#1a3a1a; }
    .data-table tbody .history-row { cursor:pointer; }
    .data-table tbody .history-row:hover { background:#f9fafb; }

    /* ── Status Badges ── */
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .status-badge::before { content:'●'; font-size:8px; }
    .badge-pending    { background:#fef9c3; color:#854d0e; }
    .badge-approved   { background:#dcfce7; color:#14532d; }
    .badge-rejected   { background:#fee2e2; color:#991b1b; }
    .badge-cancelled  { background:#f3f4f6; color:#6b7280; }
    .badge-received   { background:#dbeafe; color:#1e40af; }
    .badge-on-process { background:#ede9fe; color:#5b21b6; }
    .badge-recalled   { background:#ede9fe; color:#5b21b6; }

    /* ── Breadcrumb ── */
    .breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:24px; flex-wrap:wrap; }
    .breadcrumb a { color:#6b7280; text-decoration:none; }
    .breadcrumb a:hover { color:#1a3a1a; }
    .breadcrumb .sep { color:#d1d5db; }
    .breadcrumb .current { color:#1a3a1a; font-weight:600; }

    /* ── Toast ── */
    #toast { position:fixed; bottom:24px; right:24px; z-index:300; min-width:280px; background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 8px 32px rgba(0,0,0,0.15); display:flex; align-items:center; gap:12px; opacity:0; transform:translateY(16px); transition:all 0.3s ease; pointer-events:none; max-width:calc(100vw - 32px); }
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
    .slide-panel { position:fixed; top:0; right:0; bottom:0; z-index:100; width:55vw; min-width:480px; max-width:860px; display:flex; flex-direction:column; pointer-events:none; transform:translateX(100%); transition:transform 0.36s cubic-bezier(0.32,0.72,0,1); }
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

    /* ── Panel Footer ── */
    .panel-footer { flex-shrink:0; padding:16px 28px; border-top:1px solid #f3f4f6; background:#fff; display:flex; align-items:center; justify-content:flex-end; gap:12px; transition:opacity 0.2s, filter 0.2s; }
    .panel-footer-spaced { justify-content:space-between; }
    .panel-footer.cal-active { opacity:0.4; filter:blur(2px); pointer-events:none; }

    /* ── Form Section ── */
    .form-section-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .specify-wrap { animation:fadeIn 0.15s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
    .section-heading { display:flex; align-items:center; gap:10px; margin-bottom:18px; }
    .section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .section-card-title { font-size:14px; font-weight:700; color:#111827; margin:0; }

    /* ── Detail Panel ── */
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
    .btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .btn-cancel-action { padding:8px 18px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; transition:background 0.15s; display:inline-flex; align-items:center; gap:6px; margin-left:auto; }
    .btn-cancel-action:hover { background:#b91c1c; }

    /* ── Max Days Banner ── */
    .max-days-banner { display:flex; align-items:flex-start; gap:10px; border-radius:10px; padding:10px 14px; margin-top:10px; font-size:12px; }
    .max-days-banner.ok      { background:#f0fdf4; border:1px solid #bbf7d0; color:#14532d; }
    .max-days-banner.warning { background:#fefce8; border:1px solid #fde68a; color:#92400e; }
    .max-days-banner.danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    .max-days-banner svg     { flex-shrink:0; margin-top:1px; }
    .max-days-bar-track { height:5px; border-radius:99px; background:#e5e7eb; margin-top:6px; overflow:hidden; }
    .max-days-bar-fill  { height:100%; border-radius:99px; transition:width 0.3s; }

    /* ══ CALENDAR TRIGGER & SHARED ══ */
    .cal-wrap { position:relative; }
    .cal-trigger { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:border-color 0.15s,background 0.15s; outline:none; text-align:left; font-family:inherit; }
    .cal-trigger:focus, .cal-trigger.open { background:#fff; border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .cal-trigger-text { flex:1; color:#111827; font-size:13px; }
    .cal-trigger-text.placeholder { color:#9ca3af; }

    /* ══ POPUP BASE ══ */
    .cal-popup {
        position:absolute; top:calc(100% + 6px); left:0; z-index:500;
        width:300px; background:#fff; border:1.5px solid #e5e7eb; border-radius:14px;
        box-shadow:0 12px 36px rgba(0,0,0,0.15); padding:14px 16px;
        animation:calFadeIn 0.15s ease;
    }
    .cal-popup.dual-mode {
        width:580px;
        max-width:calc(100vw - 56px);
    }
    @keyframes calFadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

    /* ══ DUAL CALENDAR LAYOUT ══ */
    #calDualWrap { display:none; flex-direction:row; gap:0; }
    .cal-single-pane { flex:1; min-width:0; }
    .cal-single-pane + .cal-single-pane { border-left:1px solid #f3f4f6; padding-left:14px; }

    /* ══ SINGLE / SHARED CALENDAR ELEMENTS ══ */
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

    /* ══ RANGE CALENDAR MODE ══ */
    .cal-day.cal-in-range {
        background:#dcfce7 !important; color:#14532d !important;
        border-color:#bbf7d0 !important; border-radius:0 !important;
    }
    .cal-day.cal-range-start {
        background:#1a3a1a !important; color:#fff !important;
        border-color:#1a3a1a !important; border-radius:7px 0 0 7px !important;
    }
    .cal-day.cal-range-end {
        background:#1a3a1a !important; color:#fff !important;
        border-color:#1a3a1a !important; border-radius:0 7px 7px 0 !important;
    }
    .cal-day.cal-range-preview {
        background:#2d5a1b !important; color:#fff !important;
        border-color:#2d5a1b !important; border-radius:0 7px 7px 0 !important;
        opacity:0.7;
    }
    .cal-range-indicator {
        font-size:11px; font-weight:600; padding:5px 10px;
        border-radius:8px; margin-bottom:8px; text-align:center;
        display:none; line-height:1.4;
    }
    .cal-range-indicator.step-start { background:#f0fdf4; color:#374151; display:block; }
    .cal-range-indicator.step-end   { background:#fefce8; color:#854d0e; display:block; }
    .cal-range-indicator.step-done  { background:#dcfce7; color:#14532d; display:block; }

    /* ══ CALENDAR FOOTER / LEGEND ══ */
    .cal-footer { margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; gap:6px; }
    .cal-selected-count { font-size:11px; color:#6b7280; }
    .cal-selected-count strong { color:#1a3a1a; font-weight:800; }
    .cal-clear-btn { font-size:11px; color:#dc2626; background:none; border:none; cursor:pointer; font-weight:700; padding:4px 8px; border-radius:6px; font-family:inherit; }
    .cal-clear-btn:hover { background:#fee2e2; }
    .cal-done-btn { font-size:12px; font-weight:700; color:#fff; background:#1a3a1a; border:none; border-radius:8px; padding:6px 16px; cursor:pointer; font-family:inherit; transition:background 0.12s; }
    .cal-done-btn:hover { background:#2d5a1b; }
    .cal-done-btn:disabled { background:#9ca3af; cursor:not-allowed; opacity:0.6; }
    .cal-done-btn:disabled:hover { background:#9ca3af; }
    #calCountLabel { font-size:11px; color:#6b7280; }
    #calCountLabel.preview { color:#ca8a04; font-weight:700; }
    .cal-chips-area { margin-top:8px; display:flex; flex-wrap:wrap; gap:4px; }
    .cal-chip { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; background:#f0fdf4; border:1.5px solid #bbf7d0; border-radius:20px; font-size:11px; color:#14532d; font-weight:700; }
    .cal-chip-x { cursor:pointer; color:#9ca3af; font-size:13px; line-height:1; transition:color 0.1s; display:flex; align-items:center; }
    .cal-chip-x:hover { color:#dc2626; }
    .cal-legend { display:flex; gap:10px; flex-wrap:wrap; margin-top:8px; }
    .cal-legend-item { display:flex; align-items:center; gap:4px; font-size:10px; color:#6b7280; }
    .cal-legend-dot { width:10px; height:10px; border-radius:3px; flex-shrink:0; }

    /* ══ SUCCESS MODAL ══ */
    #successModal { position:fixed; inset:0; z-index:210; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.45); backdrop-filter:blur(3px); opacity:0; transition:opacity 0.25s ease; }
    #successModalCard { background:#fff; border-radius:24px; padding:36px 32px 28px; width:420px; max-width:94vw; box-shadow:0 24px 64px rgba(0,0,0,0.2); transform:scale(0.88); transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1); text-align:center; }
    .success-modal-icon-ring { width:76px; height:76px; border-radius:50%; background:linear-gradient(135deg,#dcfce7,#bbf7d0); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; position:relative; }
    .success-modal-icon-ring::after { content:''; position:absolute; inset:-5px; border-radius:50%; border:2px dashed #86efac; animation:spinSlow 8s linear infinite; }
    @keyframes spinSlow { to { transform:rotate(360deg); } }
    .success-modal-check { animation:popIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.15s both; }
    @keyframes popIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
    .success-modal-divider { height:1px; background:linear-gradient(90deg,transparent,#e5e7eb,transparent); margin:20px 0; }
    .success-modal-meta { display:flex; flex-direction:column; gap:8px; background:#f8fdf9; border:1px solid #dcfce7; border-radius:12px; padding:14px 16px; text-align:left; margin-bottom:22px; }
    .success-modal-meta-row { display:flex; align-items:center; justify-content:space-between; font-size:12px; }
    .success-modal-meta-label { color:#6b7280; font-weight:600; }
    .success-modal-meta-value { color:#14532d; font-weight:700; }
    .success-modal-btn-primary { width:100%; padding:12px; font-size:14px; font-weight:700; color:#fff; border:none; border-radius:12px; cursor:pointer; background:linear-gradient(135deg,#1a3a1a,#2d5a1b); transition:opacity 0.15s, transform 0.15s; }
    .success-modal-btn-primary:hover { opacity:0.9; transform:translateY(-1px); }
    .success-modal-btn-primary:active { transform:scale(0.98); }
    .success-modal-hint { font-size:11px; color:#9ca3af; margin-top:12px; }

    /* ══ HISTORY TABLE SPECIFIC ══ */
    .hist-type-badge-leave { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; background:#f0fdf4; color:#166534; }
    .hist-type-badge-monetize { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; background:#ede9fe; color:#5b21b6; }
    .hist-status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .hist-status-badge::before { content:'●'; font-size:8px; }
    .hist-badge-approved  { background:#dcfce7; color:#14532d; }
    .hist-badge-rejected  { background:#fee2e2; color:#991b1b; }
    .hist-badge-cancelled { background:#f3f4f6; color:#6b7280; }
    .hist-badge-recalled  { background:#ede9fe; color:#5b21b6; }

    /* ══ PANEL FILTER HEADER ══ */
    .panel-filter-header { display:flex; flex-direction:column; gap:12px; padding:14px 16px; border-bottom:1px solid #f9fafb; }

    /* ── filter bar (leave & monetize) ── */
    .filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; width:100%; }
    .filter-bar > div { position:relative; flex:1 1 140px; min-width:0; }
    .filter-bar > div:first-child { flex:1 1 100%; }
    .filter-bar input,
    .filter-bar select { width:100%; appearance:none; -webkit-appearance:none; padding:8px 10px; font-size:13px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; outline:none; transition:border-color 0.15s; min-height:38px; }
    .filter-bar select { padding-right:28px; }
    .filter-bar input:focus,
    .filter-bar select:focus { border-color:#2d5a1b; }
    .filter-bar input { padding-left:34px; }
    .filter-search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
    .filter-chevron { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }

    /* ── filter bar (history) ── */
    .history-filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; width:100%; }
    .history-filter-bar .rel { position:relative; flex:1 1 140px; min-width:0; }
    .history-filter-bar .rel-search { flex:1 1 100%; min-width:0; }
    .history-filter-bar input,
    .history-filter-bar select { width:100%; appearance:none; -webkit-appearance:none; padding:8px 10px; font-size:13px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; outline:none; transition:border-color 0.15s; min-height:38px; }
    .history-filter-bar select { padding-right:28px; }
    .history-filter-bar input:focus,
    .history-filter-bar select:focus { border-color:#2d5a1b; }
    .history-filter-bar input { padding-left:34px; }
    .history-filter-bar .chevron { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }
    .history-filter-bar .search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }

    /* ══ SORT HEADER ══ */
    .data-table th.sortable { cursor:pointer; user-select:none; }
    .data-table th.sortable:hover { color:#1a3a1a; background:#f0fdf4; }
    .sort-icon { display:inline-block; margin-left:3px; font-size:9px; opacity:0.4; vertical-align:middle; }
    .data-table th.sort-asc .sort-icon,
    .data-table th.sort-desc .sort-icon { opacity:1; color:#2d5a1b; }

    /* ══ TAB HEADER ROW ══ */
    .tab-header-row {
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:0 16px;
        padding-top:4px;
        padding-bottom:0;
        border-bottom:1px solid #f3f4f6;
        gap:8px;
        overflow:hidden;
    }
    .tab-btn-wrap {
        display:flex;
        align-items:center;
        gap:16px;
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
        scrollbar-width:none;
        flex:1;
        min-width:0;
    }
    .tab-btn-wrap::-webkit-scrollbar { display:none; }

    .tab-apply-btn-wrap {
        flex-shrink:0;
        padding:8px 0;
    }

    .table-scroll-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .table-scroll-wrap .data-table { min-width:620px; }
    #historyTableWrap .data-table { min-width:780px; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 640px) {
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

        /* Tab header — stack tabs on top row, apply button on its own row below */
        .tab-header-row {
            flex-wrap:wrap;
            padding:0 10px;
            gap:0;
            overflow:visible;
        }
        .tab-btn-wrap {
            order:1;
            flex:1 1 100%;
            gap:12px;
        }
        .tab-btn { font-size:12px; padding:10px 3px; }
        .pb-3.flex-shrink-0 {
            order:2;
            width:100%;
            padding:8px 0 10px;
            border-top:1px solid #f3f4f6;
            display:flex;
            justify-content:stretch;
        }
        
        .pb-3.flex-shrink-0 #applyBtn {
            width:100%;
            justify-content:center;
        }

        /* Leave / Monetize filter bar — search full width, dropdowns side by side */
        .filter-bar > div { flex:1 1 calc(50% - 4px); }
        .filter-bar > div:first-child { flex:1 1 100%; }

        /* History filter bar — search full width, dropdowns side by side */
        .history-filter-bar .rel-search { flex:1 1 100% !important; }
        .history-filter-bar .rel:not(.rel-search) { flex:1 1 calc(50% - 4px) !important; min-width:0 !important; }

        /* History table — show only: Leave ID, App Date, Type, Status, Action */
        #historyTable th:nth-child(4),  #historyTable td:nth-child(4),
        #historyTable th:nth-child(5),  #historyTable td:nth-child(5),
        #historyTable th:nth-child(6),  #historyTable td:nth-child(6),
        #historyTable th:nth-child(7),  #historyTable td:nth-child(7),
        #historyTable th:nth-child(9),  #historyTable td:nth-child(9),
        #historyTable th:nth-child(10), #historyTable td:nth-child(10) { display:none; }

        /* Allow history table to shrink naturally — no forced min-width */
        #historyTableWrap .data-table { min-width:unset; width:100%; }

        /* Leave / Monetize table — hide start/end date columns */
        .data-table th:nth-child(5), .data-table td:nth-child(5),
        .data-table th:nth-child(6), .data-table td:nth-child(6) { display:none; }

        /* Modals */
        #cancelModalCard { width:95vw !important; padding:20px !important; }
        #successModalCard { width:95vw !important; padding:24px 18px 20px !important; }

        /* Dual calendar */
        #calDualWrap { flex-direction:column !important; gap:12px !important; }
        .cal-single-pane + .cal-single-pane { border-left:none !important; padding-left:0 !important; border-top:1px solid #f3f4f6; padding-top:12px; }
        .cal-popup.dual-mode { width:calc(100vw - 40px) !important; }
        .cal-popup { width:calc(100vw - 40px); min-width:240px; }

        /* Toast */
        #toast { left:16px; right:16px; min-width:unset; bottom:16px; }
    }

    @media (max-width: 400px) {
        .panel-footer { flex-direction:column; align-items:stretch; }
        .panel-footer button { width:100%; justify-content:center; }
    }

    @media (min-width:641px) and (max-width:1024px) {
        .slide-panel { width:80vw; min-width:unset; max-width:680px; }
        .filter-bar { flex-wrap:wrap !important; gap:8px !important; }
        .tab-header-row { padding:0 20px; }

        /* History table — hide less critical columns on tablet */
        #historyTable th:nth-child(5),  #historyTable td:nth-child(5),
        #historyTable th:nth-child(6),  #historyTable td:nth-child(6),
        #historyTable th:nth-child(9),  #historyTable td:nth-child(9),
        #historyTable th:nth-child(10), #historyTable td:nth-child(10) { display:none; }
        #historyTableWrap .data-table { min-width:500px; }

        .data-table th:nth-child(6), .data-table td:nth-child(6) { display:none; }
        .cal-popup.dual-mode { width:min(580px, calc(100vw - 56px)); }
    }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Application</a>
    <span class="sep">›</span>
    <span class="current" id="breadcrumbCurrent">Leave Application</span>
</div>

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

    $vlRem    = $vlBalance ? truncate3((string) $vlBalance->getRawOriginal('remaining_balance')) : '0.000';
    $slRem    = $slBalance ? truncate3((string) $slBalance->getRawOriginal('remaining_balance')) : '0.000';
    $totalBal = number_format((float)$vlRem + (float)$slRem, 3, '.', '');
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bal-card-dark">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium mb-2" style="color:rgba(255,255,255,0.75);">Total Leave Balance</p>
                <p class="text-4xl font-bold tracking-tight">{{ $totalBal }}</p>
                <p class="text-xs mt-2" style="color:rgba(255,255,255,0.55);">Vacation + Sick Leave</p>
            </div>
            <div class="bal-icon bal-icon-dark">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Vacation Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ $vlRem }}</p>
                @if($vlBalance)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ truncate3((string) $vlBalance->getRawOriginal('total_accrued')) }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ truncate3((string) $vlBalance->getRawOriginal('total_used')) }}</strong></span>
                </div>
                @else
                <p class="text-xs text-gray-400 mt-3">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-green">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Sick Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ $slRem }}</p>
                @if($slBalance)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ truncate3((string) $slBalance->getRawOriginal('total_accrued')) }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ truncate3((string) $slBalance->getRawOriginal('total_used')) }}</strong></span>
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

{{-- Tabs + Table Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="tab-header-row">
        <div class="tab-btn-wrap">
            <button class="tab-btn active" id="tabLeave"    onclick="switchTab('leave')">Leave Application List</button>
            <button class="tab-btn"        id="tabMonetize" onclick="switchTab('monetize')">Monetization Request List</button>
            <button class="tab-btn"        id="tabHistory"  onclick="switchTab('history')">History</button>
        </div>
        <div class="pb-3 flex-shrink-0">
            <button id="applyBtn" onclick="openLeavePanel()"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm text-white font-semibold rounded-lg transition"
                    style="background:#1a3a1a; " onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span id="applyBtnText">Apply for Leave</span>
            </button>
        </div>
    </div>

{{-- Leave Table --}}
    <div id="panelLeave">
        <div class="panel-filter-header">
            <div>
                <p class="font-bold text-gray-800 text-sm">Leave Application List</p>
                <p class="text-xs text-gray-400 mt-0.5">Click a row to view full details</p>
            </div>
            <div class="filter-bar">
                <div class="relative">
                    <svg class="filter-search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search..." id="searchLeave" oninput="filterLeave()">
                </div>
                <div class="relative">
                    <select id="filterStatus" onchange="filterLeave()">
                        <option value="">All Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="RECEIVED">Received</option>
                    </select>
                    <svg class="filter-chevron w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filterMonth" onchange="filterLeave()">
                        <option value="">All Months</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ now()->month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <svg class="filter-chevron w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
        <div class="table-scroll-wrap">
            <table class="data-table" id="leaveTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('leave','leave_id')">Leave ID <span class="sort-icon">↕</span></th>
                        <th>Name</th>
                        <th class="sortable" onclick="sortTable('leave','application_date_raw')">Application Date <span class="sort-icon">↕</span></th>
                        <th>Duration</th>
                        <th class="sortable" onclick="sortTable('leave','start_date_raw')">Start Date <span class="sort-icon">↕</span></th>
                        <th class="sortable" onclick="sortTable('leave','end_date_raw')">End Date <span class="sort-icon">↕</span></th>
                        <th>Leave Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="leaveTbody">
                    {{-- Rows rendered by JS --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monetization Table --}}
    <div id="panelMonetize" class="hidden">
        <div class="panel-filter-header">
            <div>
                <p class="font-bold text-gray-800 text-sm">Monetization Request List</p>
                <p class="text-xs text-gray-400 mt-0.5">Click a row to view details</p>
            </div>
            <div class="filter-bar">
                <div class="relative">
                    <svg class="filter-search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search..." id="searchMonetize" oninput="filterMonetize()">
                </div>
                <div class="relative">
                    <select id="filterMonetizeStatus" onchange="filterMonetize()">
                        <option value="">All Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="RECEIVED">Received</option>
                    </select>
                    <svg class="filter-chevron w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filterMonetizeMonth" onchange="filterMonetize()">
                        <option value="">All Months</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ now()->month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <svg class="filter-chevron w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
        <div class="table-scroll-wrap">
            <table class="data-table" id="monetizeTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('monetize','leave_id')">Leave ID <span class="sort-icon">↕</span></th>
                        <th>Name</th>
                        <th class="sortable" onclick="sortTable('monetize','application_date_raw')">Application Date <span class="sort-icon">↕</span></th>
                        <th>Leave Type</th>
                        <th>Days</th>
                        <th>Est. Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="monetizeTbody">
                    {{-- Rows rendered by JS --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ HISTORY TAB ══ --}}
    <div id="panelHistory" class="hidden">
        <div class="panel-filter-header">
            <div>
                <p class="font-bold text-gray-800 text-sm">Application History</p>
                <p class="text-xs text-gray-400 mt-0.5">All processed applications (approved, rejected, recalled &amp; cancelled)</p>
            </div>
            <div class="history-filter-bar">
                <div class="rel rel-search">
                    <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search leave type, date…" id="searchHistory" oninput="filterHistory()">
                </div>
                <div class="rel">
                    <select id="filterHistoryStatus" onchange="filterHistory()">
                        <option value="">All Status</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="RECALLED">Recalled</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="rel">
                    <select id="filterHistoryType" onchange="filterHistory()">
                        <option value="">All Types</option>
                        <option value="leave">Leave</option>
                        <option value="monetization">Monetization</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
        <div class="table-scroll-wrap" id="historyTableWrap">
            <table class="data-table" id="historyTable">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('history','leave_id')">Leave ID <span class="sort-icon">↕</span></th>
                        <th class="sortable" onclick="sortTable('history','application_date_raw')">Application Date <span class="sort-icon">↕</span></th>
                        <th>Type</th>
                        <th>Leave Type</th>
                        <th class="sortable" onclick="sortTable('history','start_date_raw')">Start Date <span class="sort-icon">↕</span></th>
                        <th class="sortable" onclick="sortTable('history','end_date_raw')">End Date <span class="sort-icon">↕</span></th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Action By</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="historyTbody">
                    {{-- Rows rendered by JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Overlay --}}
<div id="panelOverlay" onclick="closeAllPanels()"></div>

{{-- Detail Panel --}}
<div id="detailPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2 id="dpTitle">Leave Application Details</h2>
                <p id="dpSubtitle">Loading…</p>
            </div>
            <button class="panel-close" onclick="closeDetailPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body" id="dpBody"></div>
        <div class="panel-footer" id="dpFooter" style="justify-content:flex-end;">
        <div class="flex gap-3" id="dpActionBtns" style="width:100%;justify-content:flex-end;"></div>
    </div>
    </div>
</div>

{{-- Leave Application Panel --}}
<div id="leavePanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div><h2>Application for Leave</h2><p>CS Form No. 6 — Please fill in all required fields</p></div>
            <button class="panel-close" onclick="closeLeavePanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="leaveForm">
                @csrf
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <p class="section-card-title">Personal Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div><label class="field-label">First Name</label><input type="text" class="form-field" value="{{ $employee->first_name }}" disabled></div>
                        <div><label class="field-label">Office / Department</label><input type="text" class="form-field" value="{{ $employee->department->department_name ?? 'OPAG' }}" disabled></div>
                        <div><label class="field-label">Middle Name</label><input type="text" class="form-field" value="{{ $employee->middle_name ?? '' }}" disabled></div>
                        <div><label class="field-label">Date of Filing</label><input type="text" class="form-field" value="{{ now()->format('F d, Y') }}" disabled></div>
                        <div><label class="field-label">Last Name</label><input type="text" class="form-field" value="{{ $employee->last_name }}" disabled></div>
                        <div><label class="field-label">Position</label><input type="text" class="form-field" value="{{ $employee->position->position_name ?? '—' }}" disabled></div>
                        <div class="col-span-2"><label class="field-label">Salary</label><input type="text" class="form-field" value="₱{{ number_format($employee->salary, 2) }}" disabled></div>
                    </div>
                </div>

                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <p class="section-card-title">Details of Application</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <label class="field-label">A. Type of Leave <span class="req">*</span></label>
                            <select id="f_leave_type_id" name="leave_type_id" class="form-field" onchange="onLeaveTypeChange(this)">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach($leaveTypes as $lt)
                                @php
                                    $rawRemaining = isset($creditBalancesJson[$lt->leave_type_id])
                                        ? $creditBalancesJson[$lt->leave_type_id]['remaining_balance']
                                        : '0';
                                @endphp
                                <option value="{{ $lt->leave_type_id }}"
                                        data-accrual="{{ $lt->is_accrual_based }}"
                                        data-code="{{ $lt->type_code }}"
                                        data-maxdays="{{ $lt->max_days ?? '' }}"
                                        data-remaining="{{ $rawRemaining }}"
                                        data-noticedays="{{ $lt->notice_days ?? 0 }}"
                                        data-allowpast="{{ $lt->allow_past_filing ?? 0 }}">{{ $lt->type_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_leave_type">Please select a leave type.</p>
                            <div id="maxDaysBanner" style="display:none;"></div>
                        </div>
                        <div>
                            <label class="field-label">C. Number of Working Days <span class="req">*</span></label>
                            <input type="text" id="f_no_days_display" class="form-field" placeholder="Auto-calculated from selected dates" disabled>
                            <p class="text-xs mt-1" id="balanceHint"></p>
                        </div>
                        <div>
                            <label class="field-label">B. Details of Leave</label>
                            <div id="detailsOfLeaveWrapper">
                                <p class="text-xs text-gray-400 mt-1" id="detailsHint">Select a leave type to see available options.</p>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">D. Commutation</label>
                            <select id="f_commutation" name="commutation" class="form-field">
                                <option value="NOT_REQUESTED">Not Requested</option>
                                <option value="REQUESTED">Requested</option>
                            </select>
                        </div>

                        {{-- Calendar Picker --}}
                        <div class="col-span-2">
                            <label class="field-label">Leave Dates <span class="req">*</span></label>
                            <div class="cal-wrap" id="calWrap">
                                <button type="button" class="cal-trigger" id="calTrigger" onclick="toggleCalendar(event)">
                                    <span class="cal-trigger-text placeholder" id="calTriggerText">Click to select leave dates…</span>
                                    <svg class="w-4 h-4 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>

                                <div class="cal-popup" id="calPopup" style="display:none;">
                                    <div class="cal-range-indicator" id="calRangeIndicator"></div>

                                    <div id="calDualWrap" style="display:none; flex-direction:row; gap:0;">
                                        <div class="cal-single-pane" id="calPane0">
                                            <div class="cal-header">
                                                <button type="button" class="cal-nav" onclick="calNav(-1)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <span class="cal-month-label" id="calMonthLabel0"></span>
                                                <div style="width:32px;"></div>
                                            </div>
                                            <div class="cal-weekdays">
                                                <div class="cal-wd">Su</div><div class="cal-wd">Mo</div><div class="cal-wd">Tu</div>
                                                <div class="cal-wd">We</div><div class="cal-wd">Th</div><div class="cal-wd">Fr</div><div class="cal-wd">Sa</div>
                                            </div>
                                            <div class="cal-days" id="calDays0"></div>
                                        </div>
                                        <div class="cal-single-pane" id="calPane1">
                                            <div class="cal-header">
                                                <div style="width:32px;"></div>
                                                <span class="cal-month-label" id="calMonthLabel1"></span>
                                                <button type="button" class="cal-nav" onclick="calNav(1)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                            </div>
                                            <div class="cal-weekdays">
                                                <div class="cal-wd">Su</div><div class="cal-wd">Mo</div><div class="cal-wd">Tu</div>
                                                <div class="cal-wd">We</div><div class="cal-wd">Th</div><div class="cal-wd">Fr</div><div class="cal-wd">Sa</div>
                                            </div>
                                            <div class="cal-days" id="calDays1"></div>
                                        </div>
                                    </div>

                                    <div id="calSingleWrap">
                                        <div class="cal-header">
                                            <button type="button" class="cal-nav" onclick="calNav(-1)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                            </button>
                                            <span class="cal-month-label" id="calMonthLabel"></span>
                                            <button type="button" class="cal-nav" onclick="calNav(1)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                        <div class="cal-weekdays">
                                            <div class="cal-wd">Su</div><div class="cal-wd">Mo</div><div class="cal-wd">Tu</div>
                                            <div class="cal-wd">We</div><div class="cal-wd">Th</div><div class="cal-wd">Fr</div><div class="cal-wd">Sa</div>
                                        </div>
                                        <div class="cal-days" id="calDays"></div>
                                    </div>

                                    <div class="cal-footer">
                                        <p class="cal-selected-count"><strong id="calCount">0</strong><span id="calCountLabel"> date(s)</span></p>
                                        <div class="flex gap-2 items-center">
                                            <button type="button" class="cal-clear-btn" onclick="calClearAll()">Clear</button>
                                            <button type="button" class="cal-done-btn" id="calDoneBtn" onclick="calDone()">Done ✓</button>
                                        </div>
                                    </div>
                                    <div class="cal-legend" id="calLegend">
                                        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#fee2e2;border:1px solid #fecaca;"></div> Leave conflict</div>
                                        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#fef9c3;border:1px solid #fde68a;"></div> Half-day filed</div>
                                        <div class="cal-legend-item" id="rangeLegendItem" style="display:none;"><div class="cal-legend-dot" style="background:#dcfce7;border:1px solid #bbf7d0;"></div> In range</div>
                                    </div>
                                </div>

                                <div class="cal-chips-area" id="calChipsArea"></div>
                                <div id="calHiddenInputs"></div>
                            </div>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_dates">Please select at least one leave date.</p>
                            <p class="text-xs text-gray-400 mt-1" id="calModeHint">Weekends are disabled. Red = existing leave. Yellow = existing half-day.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Reason / Remarks</label>
                            <textarea id="f_reason" name="reason" rows="2" class="form-field" placeholder="Optional remarks..."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer" id="leavePanelFooter">
            <button onclick="closeLeavePanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitLeave()" id="submitLeaveBtn" class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition" style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">Confirm Application</button>
        </div>
    </div>
</div>

{{-- Monetization Panel --}}
<div id="monetizePanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div><h2>Request Monetization</h2><p>Convert leave days to cash</p></div>
            <button class="panel-close" onclick="closeMonetizePanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="monetizeForm">
                @csrf
                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <p class="section-card-title">Monetization Details</p>
                    </div>
                    <div class="grid grid-cols-1 gap-y-4">
                        <div>
                            <label class="field-label">Leave Type <span class="req">*</span></label>
                            <select id="fm_leave_type_id" name="leave_type_id" class="form-field" onchange="updateMonetizeBalance(this)">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach($leaveTypes->where('is_accrual_based',1) as $lt)
                                @php
                                    $rawRem = isset($creditBalancesJson[$lt->leave_type_id])
                                        ? $creditBalancesJson[$lt->leave_type_id]['remaining_balance']
                                        : '0';
                                @endphp
                                <option value="{{ $lt->leave_type_id }}"
                                        data-code="{{ $lt->type_code }}"
                                        data-remaining="{{ $rawRem }}">{{ $lt->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="monetizeBalanceBox" class="hidden rounded-xl p-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <p class="text-xs font-semibold text-green-700 mb-1">AVAILABLE BALANCE</p>
                            <p class="text-2xl font-bold text-green-800" id="monetizeBalanceVal">0</p>
                            <p class="text-xs text-green-600 mt-1">days remaining</p>
                        </div>
                        <div>
                            <label class="field-label">Number of Days to Monetize <span class="req">*</span></label>
                            <input type="number" id="fm_no_of_days" name="no_of_days" class="form-field" min="1" placeholder="Enter number of days" oninput="calcMonetizeAmount()">
                            <p class="text-xs text-gray-400 mt-1">Daily rate: <strong id="dailyRateDisplay">₱{{ number_format($dailyRate, 2) }}</strong> &nbsp;·&nbsp; Salary × Days × 0.0481927</p>
                            <div id="monetizeLimitWarn" class="hidden mt-2 rounded-lg px-3 py-2.5 flex items-start gap-2" style="background:#fff7ed;border:1px solid #fed7aa;">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs" style="color:#9a3412;">Monetization is limited to <strong>10 days</strong>.</p>
                            </div>
                        </div>
                        <div id="monetizeAmountBox" class="rounded-xl p-4 hidden" style="background:#f3f4f6;">
                            <p class="text-xs font-semibold text-gray-500 mb-1">ESTIMATED AMOUNT</p>
                            <p class="text-2xl font-bold text-gray-800" id="monetizeAmountVal">₱0.00</p>
                            <p class="text-xs text-gray-400 mt-1">Subject to approval and final computation</p>
                        </div>
                        <div>
                            <label class="field-label">Reason / Remarks <span class="req">*</span></label>
                            <textarea id="fm_reason" name="reason" rows="2" class="form-field" placeholder="to be used for... "></textarea>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_monetize_reason">Please provide a reason.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <button onclick="closeMonetizePanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitMonetize()" id="submitMonetizeBtn" class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition" style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">Submit Request</button>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" style="position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);opacity:0;transition:opacity 0.2s;">
    <div id="cancelModalCard" style="background:#fff;border-radius:20px;padding:32px;width:420px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div class="flex items-center gap-4 mb-5">
            <div style="width:52px;height:52px;border-radius:16px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:26px;height:26px;color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800" style="font-size:16px;">Cancel Application?</h3>
                <p class="text-sm text-gray-500 mt-0.5">This action cannot be undone.</p>
            </div>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p class="text-sm font-medium" style="color:#9a3412;">Once cancelled, this leave application will be permanently marked as <strong>Cancelled</strong>.</p>
        </div>
        <div class="flex gap-3 justify-end flex-wrap">
            <button onclick="closeCancelModal()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep Application</button>
            <button onclick="confirmCancelApp()" id="confirmCancelBtn" class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl flex items-center gap-2" style="background:#dc2626;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div id="successModal">
    <div id="successModalCard">
        <div class="success-modal-icon-ring">
            <svg class="success-modal-check" style="width:36px;height:36px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 6px;"><span id="successModalTitle">Application Submitted!</span></h2>
        <p style="font-size:13px;color:#6b7280;margin:0 0 16px;" id="successModalSubtitle">Your application has been received and is now pending for review.</p>
        <div class="success-modal-divider"></div>
        <div class="success-modal-meta" id="successModalMeta"></div>
        <button class="success-modal-btn-primary" onclick="closeSuccessModal()">Got it, thank you!</button>
        <p class="success-modal-hint">Page will refresh automatically in a moment.</p>
    </div>
</div>

{{-- Toast --}}
<div id="toast">
    <div id="toastIcon" class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#dcfce7;"></div>
    <div>
        <p class="text-sm font-bold text-gray-800" id="toastTitle"></p>
        <p class="text-xs text-gray-500 mt-0.5" id="toastMsg"></p>
    </div>
</div>

<script>
/* ════════════════════════════════════════════════════════
   SERVER DATA
════════════════════════════════════════════════════════ */
const CREDIT_BALANCES = @json($creditBalancesJson);
const MAX_DAYS_DATA   = @json($maxDaysJson);
const DAILY_RATE      = {{ $dailyRate }};
const CSRF            = "{{ csrf_token() }}";
const SIGNATORIES     = @json($signatories); 
const LEAVE_URL       = "{{ route('leave.store') }}";
const MONETIZE_URL    = "{{ route('leave.monetize') }}";
const CANCEL_URL      = "{{ url('application/leave') }}";
const PDF_URL         = "{{ url('application/leave') }}";
const HALF_DAY_DATES  = @json($halfDayDates);
const EMPLOYEE_NAME   = "{{ $employee->last_name }}, {{ $employee->first_name }}";
const EMPLOYEE_ID_FMT = "{{ $employee->formatted_employee_id }}";
const EMPLOYEE_SALARY = {{ $employee->salary ?? 0 }};

{{-- ── LEAVE DATA (active + history) with updated_at ── --}}
const LEAVE_DATA = {!! json_encode($leaveApps->keyBy('leave_id')->map(function($a) {
    return [
        'leave_id'              => $a->leave_id,
        'leave_type'            => optional($a->leaveType)->type_name ?? '—',
        'application_date'      => $a->application_date ? $a->application_date->format('M d, Y') : '—',
        'application_date_raw'  => $a->application_date ? $a->application_date->toDateString() : '',
        'start_date'            => $a->start_date ? $a->start_date->format('M d, Y') : '—',
        'end_date'              => $a->end_date   ? $a->end_date->format('M d, Y')   : '—',
        'start_date_raw'        => $a->start_date ? $a->start_date->toDateString() : '',
        'end_date_raw'          => $a->end_date   ? $a->end_date->toDateString()   : '',
        'no_of_days'            => $a->no_of_days,
        'details_of_leave'      => $a->details_of_leave ?? '—',
        'commutation'           => $a->commutation ?? '—',
        'reason'                => $a->reason ?? '',
        'status'                => $a->status,
        'is_monetization'       => $a->is_monetization,
        'reject_reason'         => $a->reject_reason ?? '',
        'updated_at'            => $a->updated_at ? $a->updated_at->toIso8601String() : '',
        'updated_at_display'    => $a->updated_at ? $a->updated_at->format('M d, Y g:i A') : '—',
        'actioned_by_name' => $a->approvedBy? (($a->approvedBy->last_name ?? '') . ', ' . ($a->approvedBy->first_name ?? '')): null,
        'month'                 => $a->application_date ? $a->application_date->format('m') : '',
    ];
})) !!};

{{-- ── MONETIZE DATA with updated_at ── --}}
const MONETIZE_DATA = {!! json_encode($monetizationApps->keyBy('leave_id')->map(function($a) use ($employee) {
    $amt = ($employee->salary ?? 0) * $a->no_of_days * 0.0481927;
    return [
        'leave_id'              => $a->leave_id,
        'leave_type'            => optional($a->leaveType)->type_name ?? '—',
        'application_date'      => $a->application_date ? $a->application_date->format('M d, Y') : '—',
        'application_date_raw'  => $a->application_date ? $a->application_date->toDateString() : '',
        'no_of_days'            => $a->no_of_days,
        'est_amount'            => '₱' . number_format($amt, 2),
        'status'                => $a->status,
        'reason'                => $a->reason ?? '',
        'reject_reason'         => $a->reject_reason ?? '',
        'updated_at'            => $a->updated_at ? $a->updated_at->toIso8601String() : '',
        'updated_at_display'    => $a->updated_at ? $a->updated_at->format('M d, Y g:i A') : '—',
        'actioned_by_name' => $a->approvedBy? (($a->approvedBy->last_name ?? '') . ', ' . ($a->approvedBy->first_name ?? '')): null,
        'month'                 => $a->application_date ? $a->application_date->format('m') : '',
        'start_date_raw'        => '',
        'end_date_raw'          => '',
    ];
})) !!};

const ALL_DATA = {...LEAVE_DATA, ...MONETIZE_DATA};

/* ════════════════════════════════════════════════════════
   SORT STATE — per table
════════════════════════════════════════════════════════ */
const sortState = {
    leave:    { key: 'updated_at', dir: 'desc' },
    monetize: { key: 'updated_at', dir: 'desc' },
    history:  { key: 'updated_at', dir: 'desc' },
};

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
   SORT TABLE
════════════════════════════════════════════════════════ */
function sortTable(table, key) {
    const state = sortState[table];
    if (state.key === key) {
        state.dir = state.dir === 'asc' ? 'desc' : 'asc';
    } else {
        state.key = key;
        state.dir = 'desc';
    }
    updateSortHeaders(table);
    if (table === 'leave')    renderLeaveRows();
    if (table === 'monetize') renderMonetizeRows();
    if (table === 'history')  renderHistoryRows();
}

function updateSortHeaders(table) {
    const tableIds = { leave: 'leaveTable', monetize: 'monetizeTable', history: 'historyTable' };
    const tbl = document.getElementById(tableIds[table]);
    if (!tbl) return;
    const state = sortState[table];
    tbl.querySelectorAll('th.sortable').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
        const icon = th.querySelector('.sort-icon');
        if (icon) icon.textContent = '↕';
    });
    // Find the clicked th by its onclick attribute matching the key
    tbl.querySelectorAll('th.sortable').forEach(th => {
        const fn = th.getAttribute('onclick') || '';
        if (fn.includes(`'${state.key}'`)) {
            th.classList.add(state.dir === 'asc' ? 'sort-asc' : 'sort-desc');
            const icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = state.dir === 'asc' ? '↑' : '↓';
        }
    });
}

function compareRows(a, b, key, dir) {
    let av = a[key] ?? '', bv = b[key] ?? '';
    // Numeric keys
    if (key === 'leave_id' || key === 'no_of_days') {
        av = parseFloat(av) || 0;
        bv = parseFloat(bv) || 0;
    }
    // String / ISO date comparison
    if (av < bv) return dir === 'asc' ? -1 : 1;
    if (av > bv) return dir === 'asc' ? 1 : -1;
    return 0;
}

/* ════════════════════════════════════════════════════════
   BADGE HELPERS
════════════════════════════════════════════════════════ */
function statusBadge(status) {
    const map = {
        PENDING:    'badge-pending',
        RECEIVED:   'badge-received',
        'ON-PROCESS': 'badge-on-process',
        APPROVED:   'badge-approved',
        REJECTED:   'badge-rejected',
        CANCELLED:  'badge-cancelled',
        RECALLED:   'badge-recalled',
    };
    const label = status === 'ON-PROCESS' ? 'On Process' : status.charAt(0) + status.slice(1).toLowerCase();
    return `<span class="status-badge ${map[status] || 'badge-pending'}">${label}</span>`;
}
function histStatusBadge(status) {
    const map = { APPROVED:'hist-badge-approved', REJECTED:'hist-badge-rejected', CANCELLED:'hist-badge-cancelled', RECALLED:'hist-badge-recalled' };
    const label = status.charAt(0) + status.slice(1).toLowerCase();
    return `<span class="hist-status-badge ${map[status] || 'hist-badge-cancelled'}">${label}</span>`;
}
function actionMenu(leaveId, showCancel, isMonetize = false, status = '', showPdf = true) {
    return `<div class="action-menu">
        <button class="action-menu-btn" onclick="toggleMenu(this,event)">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
        </button>
        <div class="action-dropdown">
            <button class="action-item" onclick="document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')); openDetailPanel(${leaveId}, event)">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View Details
            </button>
            ${isMonetize ? `<button class="action-item" onclick="viewMonetizeRequest(${leaveId})">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                View Request
            </button>` : ''}
            ${showPdf ? `<button class="action-item" onclick="viewPdf(${leaveId})">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                View / PDF
            </button>` : ''}
            ${showCancel ? `<button class="action-item danger" onclick="cancelApp(${leaveId})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </button>` : ''}
        </div>
    </div>`;
}

/* ════════════════════════════════════════════════════════
   RENDER LEAVE ROWS
════════════════════════════════════════════════════════ */
function renderLeaveRows() {
    const tbody     = document.getElementById('leaveTbody');
    const q         = (document.getElementById('searchLeave')?.value || '').toLowerCase();
    const stFilter  = document.getElementById('filterStatus')?.value || '';
    const moFilter  = document.getElementById('filterMonth')?.value || '';
    const { key, dir } = sortState.leave;
    const ACTIVE_STATUSES = ['PENDING','RECEIVED','ON-PROCESS'];

    let rows = Object.values(LEAVE_DATA)
        .filter(d => ACTIVE_STATUSES.includes(d.status));

    // Filter
    rows = rows.filter(d => {
        const text = [d.leave_id, EMPLOYEE_NAME, d.application_date, d.no_of_days, d.start_date, d.end_date, d.leave_type, d.status].join(' ').toLowerCase();
        return (!q || text.includes(q)) && (!stFilter || d.status === stFilter) && (!moFilter || d.month === moFilter);
    });

    // Sort
    rows.sort((a, b) => compareRows(a, b, key, dir));

    if (rows.length === 0) {
       tbody.innerHTML = `<tr><td colspan="12" class="px-6 py-12 text-center text-gray-400 text-sm">No processed applications yet.</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(d => `
        <tr class="leave-row" data-status="${d.status}" data-month="${d.month}" data-leave-id="${d.leave_id}"
            onclick="openDetailPanel(${d.leave_id}, event)">
            <td class="font-bold font-mono text-sm" style="color:#2d5a1b;">${d.leave_id}</td>
            <td class="font-medium">${EMPLOYEE_NAME}</td>
            <td class="text-gray-500">${d.application_date}</td>
            <td class="text-gray-500">${d.no_of_days} day(s)</td>
            <td class="text-gray-500">${d.start_date}</td>
            <td class="text-gray-500">${d.end_date}</td>
            <td class="text-gray-500 text-xs">${d.leave_type}</td>
            <td>${statusBadge(d.status)}</td>
            <td class="text-right pr-4" onclick="event.stopPropagation()">${actionMenu(d.leave_id, d.status === 'PENDING', false, '', false)}</td>
        </tr>`).join('');
}

/* ════════════════════════════════════════════════════════
   RENDER MONETIZE ROWS
════════════════════════════════════════════════════════ */
function renderMonetizeRows() {
    const tbody    = document.getElementById('monetizeTbody');
    const q        = (document.getElementById('searchMonetize')?.value || '').toLowerCase();
    const stFilter = document.getElementById('filterMonetizeStatus')?.value || '';
    const moFilter = document.getElementById('filterMonetizeMonth')?.value || '';
    const { key, dir } = sortState.monetize;
    const ACTIVE_STATUSES = ['PENDING','RECEIVED','ON-PROCESS'];

    let rows = Object.values(MONETIZE_DATA)
        .filter(d => ACTIVE_STATUSES.includes(d.status));

    rows = rows.filter(d => {
        const text = [d.leave_id, EMPLOYEE_NAME, d.application_date, d.leave_type, d.no_of_days, d.est_amount, d.status].join(' ').toLowerCase();
        return (!q || text.includes(q)) && (!stFilter || d.status === stFilter) && (!moFilter || d.month === moFilter);
    });

    rows.sort((a, b) => compareRows(a, b, key, dir));

    if (rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No monetization requests found.</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(d => `
        <tr class="monetize-row leave-row" data-status="${d.status}" data-month="${d.month}" data-leave-id="${d.leave_id}"
            onclick="openDetailPanel(${d.leave_id}, event)">
            <td class="font-bold font-mono text-sm" style="color:#2d5a1b;">${d.leave_id}</td>
            <td class="font-medium">${EMPLOYEE_NAME}</td>
            <td class="text-gray-500">${d.application_date}</td>
            <td class="text-xs text-gray-500">${d.leave_type}</td>
            <td class="text-gray-500">${d.no_of_days}</td>
            <td class="text-gray-700 font-medium">${d.est_amount}</td>
            <td>${statusBadge(d.status)}</td>
            <td class="text-right pr-4" onclick="event.stopPropagation()">${actionMenu(d.leave_id, d.status === 'PENDING', true, d.status, true)}</td>
        </tr>`).join('');
}

/* ════════════════════════════════════════════════════════
   RENDER HISTORY ROWS
════════════════════════════════════════════════════════ */
function renderHistoryRows() {
    const tbody    = document.getElementById('historyTbody');
    const rawQ     = (document.getElementById('searchHistory')?.value || '').toLowerCase().trim();
    const stFilter = document.getElementById('filterHistoryStatus')?.value || '';
    const tyFilter = document.getElementById('filterHistoryType')?.value || '';
    const words    = rawQ.split(/\s+/).filter(Boolean);
    const { key, dir } = sortState.history;
    const HIST_STATUSES = ['APPROVED','REJECTED','CANCELLED','RECALLED'];
    

    // Build combined list of leave + monetize history rows
    const leaveHistory    = Object.values(LEAVE_DATA).filter(d => HIST_STATUSES.includes(d.status)).map(d => ({...d, record_type:'leave'}));
    const monetizeHistory = Object.values(MONETIZE_DATA).filter(d => HIST_STATUSES.includes(d.status)).map(d => ({...d, record_type:'monetization'}));
    let rows = [...leaveHistory, ...monetizeHistory];

    rows = rows.filter(d => {
        const haystack = [d.leave_id, d.application_date, d.leave_type, d.start_date, d.end_date, d.no_of_days, d.status, d.record_type, d.updated_at_display].join(' ').toLowerCase();
        const matchQ      = !words.length || words.every(w => haystack.includes(w));
        const matchStatus = !stFilter || d.status === stFilter;
        const matchType   = !tyFilter || d.record_type === tyFilter;
        return matchQ && matchStatus && matchType;
    });

    rows.sort((a, b) => compareRows(a, b, key, dir));

    if (rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="px-6 py-12 text-center text-gray-400 text-sm">No processed applications yet.</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(d => {
        const typeBadge = d.record_type === 'leave'
            ? `<span class="hist-type-badge-leave">Leave</span>`
            : `<span class="hist-type-badge-monetize">Monetization</span>`;
        return `
        <tr class="history-row" data-status="${d.status}" data-record-type="${d.record_type}" data-leave-id="${d.leave_id}"
            onclick="openDetailPanel(${d.leave_id}, event)">
            <td class="font-bold font-mono text-sm" style="color:#2d5a1b;">${d.leave_id}</td>
            <td class="text-gray-500">${d.application_date}</td>
            <td>${typeBadge}</td>
            <td class="text-xs text-gray-500">${d.leave_type}</td>
            <td class="text-gray-500">${d.start_date || '—'}</td>
            <td class="text-gray-500">${d.end_date || '—'}</td>
            <td class="text-gray-500">${d.no_of_days}</td>
            <td>${histStatusBadge(d.status)}</td>
           <td class="text-gray-500 text-xs" onclick="event.stopPropagation()" style="white-space:nowrap;">
                ${d.actioned_by_name || '—'}
            </td>
            <td class="text-gray-500" style="font-size:12px;white-space:nowrap;">${d.updated_at_display || '—'}</td>
            <td class="text-right pr-4" onclick="event.stopPropagation()">${actionMenu(d.leave_id, false, false, '', d.record_type === 'monetization')}</td>
        </tr>`;
    }).join('');
}

/* ════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════ */
let activePanelLeaveId = null;
let pendingCancelId    = null;

let activeNoticeDays   = 0;
let activeAllowPast    = false;

/* ════════════════════════════════════════════════════════
   MAX DAYS BANNER
════════════════════════════════════════════════════════ */
function renderMaxDaysBanner(ltId) {
    const banner = document.getElementById('maxDaysBanner');
    
    // FIX: If there is no max_days data OR max_days is 0/null, hide the banner completely.
    if (!MAX_DAYS_DATA[ltId] || !MAX_DAYS_DATA[ltId].max_days || MAX_DAYS_DATA[ltId].max_days <= 0) { 
        banner.style.display = 'none'; 
        return; 
    }
    
    const mx  = MAX_DAYS_DATA[ltId];
    const pct = mx.max_days > 0 ? Math.min(100, (mx.used_days / mx.max_days) * 100) : 0;
    const exhausted = mx.remaining <= 0;
    const warning   = !exhausted && mx.remaining < mx.max_days * 0.4;
    const cls       = exhausted ? 'danger' : (warning ? 'warning' : 'ok');
    const barColor  = exhausted ? '#dc2626' : (warning ? '#ca8a04' : '#16a34a');
    const icon = exhausted
        ? `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`
        : (warning
            ? `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            : `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`);
    const headline = exhausted
        ? `Annual limit reached — no more days available for ${mx.type_name} this year.`
        : `Annual limit: <strong>${mx.max_days} day(s)</strong> &nbsp;·&nbsp; Used: <strong>${mx.used_days}</strong> &nbsp;·&nbsp; Remaining: <strong>${mx.remaining}</strong>`;
    banner.innerHTML = `
        <div class="max-days-banner ${cls}">
            ${icon}
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-weight:600;font-size:12px;">${headline}</p>
                <div class="max-days-bar-track" style="margin-top:6px;">
                    <div class="max-days-bar-fill" style="width:${pct}%;background:${barColor};"></div>
                </div>
            </div>
        </div>`;
    banner.style.display = 'block';
}

/* ════════════════════════════════════════════════════════
   SUCCESS MODAL
════════════════════════════════════════════════════════ */
function showSuccessModal(type, meta) {
    const modal = document.getElementById('successModal');
    const card  = document.getElementById('successModalCard');
    const configs = {
        leave:    { title:'Application Submitted!', subtitle:'Your leave application has been received and is now pending for review.' },
        monetize: { title:'Request Submitted!',     subtitle:'Your monetization request has been received and is now pending for approval.' },
    };
    const cfg = configs[type] || configs.leave;
    document.getElementById('successModalTitle').textContent    = cfg.title;
    document.getElementById('successModalSubtitle').textContent = cfg.subtitle;
    const metaEl = document.getElementById('successModalMeta');
    metaEl.innerHTML = '';
    (meta || []).forEach(row => {
        metaEl.innerHTML += `<div class="success-modal-meta-row"><span class="success-modal-meta-label">${row.label}</span><span class="success-modal-meta-value">${row.value}</span></div>`;
    });
    modal.style.display = 'flex';
    card.style.transform = 'scale(0.88)';
    requestAnimationFrame(() => requestAnimationFrame(() => { modal.style.opacity='1'; card.style.transform='scale(1)'; }));
    document.body.style.overflow = 'hidden';
}
function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    const card  = document.getElementById('successModalCard');
    modal.style.opacity='0'; card.style.transform='scale(0.88)';
    setTimeout(() => { modal.style.display='none'; }, 250);
    document.body.style.overflow='';
}

/* ════════════════════════════════════════════════════════
   CALENDAR PICKER
════════════════════════════════════════════════════════ */
let calSelectedDates  = new Set();
let calYear, calMonth, calIsOpen = false;
let calMode           = 'multi';
let calWeekendAllowed = false;
let calRangeStart     = null;
let calRangeEnd       = null;
let calRangePicking   = 'start';
let calHoverDate      = null;
const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function setCalMode(mode) {
    calMode           = mode;
    calWeekendAllowed = (mode === 'range');
    calRangeStart     = null;
    calRangeEnd       = null;
    calRangePicking   = 'start';
    calHoverDate      = null;
    calSelectedDates.clear();

    const ind        = document.getElementById('calRangeIndicator');
    const leg        = document.getElementById('rangeLegendItem');
    const hint       = document.getElementById('calModeHint');
    const popup      = document.getElementById('calPopup');
    const dualWrap   = document.getElementById('calDualWrap');
    const singleWrap = document.getElementById('calSingleWrap');

    if (mode === 'range') {
        dualWrap.style.display   = 'flex';
        singleWrap.style.display = 'none';
        popup.classList.add('dual-mode');
        ind.className            = 'cal-range-indicator step-start';
        ind.textContent          = 'Click the start date on either calendar';
        ind.style.display        = '';
        if (leg)  leg.style.display  = '';
        if (hint) hint.textContent   = 'Range mode: click a start date, then an end date. Weekends are included. Conflict dates are skipped automatically.';
        const done = document.getElementById('calDoneBtn');
        if (done) done.disabled = true;
    } else {
        dualWrap.style.display   = 'none';
        singleWrap.style.display = '';
        popup.classList.remove('dual-mode');
        ind.className            = 'cal-range-indicator';
        ind.style.display        = 'none';
        if (leg)  leg.style.display  = 'none';
        if (hint) hint.textContent   = 'Weekends and dates blocked by notice periods are disabled.';
        const done = document.getElementById('calDoneBtn');
        if (done) done.disabled = false;
    }
    renderCalChips();
    updateCalDisplay();
}

function calInit() {
    const now = new Date();
    calYear  = now.getFullYear();
    calMonth = now.getMonth();
    calRender();
}

// RESTORED: Centralized check for all rules (Past Filing, Notice Days, Weekends, Conflicts)
function isDateBlockedByRules(dateObj, dateStr) {
    const today = new Date(); 
    today.setHours(0,0,0,0);
    const earliest = new Date(today); 
    earliest.setDate(today.getDate() + activeNoticeDays);

    const dow = dateObj.getDay();
    const isWknd = dow === 0 || dow === 6;

    if (isWknd && !calWeekendAllowed) return true;
    
    const isPastDate = dateObj < today && dateStr !== toYMD(today);
    if (isPastDate && !activeAllowPast) return true;
    
    // Notice Days check: If it's not a past filing, but it violates the future notice gap
    if (activeNoticeDays > 0 && dateObj < earliest && !isPastDate) return true;
    
    if (isDuplicateLeaveDate(dateStr) || HALF_DAY_DATES.includes(dateStr)) return true;

    return false;
}

function buildMonthGrid(gridEl, year, month) {
    const today       = new Date(); today.setHours(0,0,0,0);
    const todayStr    = toYMD(today);
    const earliest    = new Date(today); earliest.setDate(today.getDate() + activeNoticeDays);
    
    const firstDay    = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month+1, 0).getDate();
    const prevDays    = new Date(year, month, 0).getDate();
    gridEl.innerHTML  = '';

    for (let i=0; i<firstDay; i++)
        gridEl.appendChild(makeCalBtn(prevDays - firstDay + 1 + i, 'cal-day cal-other', null));

    for (let day=1; day<=daysInMonth; day++) {
        const date    = new Date(year, month, day);
        const dateStr = toYMD(date);
        const dow     = date.getDay();
        
        const isPastDate = date < today && dateStr !== todayStr;
        const isWknd     = dow === 0 || dow === 6;

        let cls = 'cal-day';
        if (dateStr === todayStr) cls += ' cal-today';

        let isBlocked = false;

        // RESTORED: Evaluate visual CSS class based on rules
        if (isWknd && !calWeekendAllowed) {
            cls += ' cal-weekend';
            isBlocked = true;
        } else if (isPastDate && !activeAllowPast) {
            cls += ' cal-disabled';
            isBlocked = true;
        } else if (activeNoticeDays > 0 && date < earliest && !isPastDate) {
            cls += ' cal-disabled';
            isBlocked = true;
        } else if (isDuplicateLeaveDate(dateStr)) {
            cls += ' cal-leave-conflict';
            isBlocked = true;
        } else if (HALF_DAY_DATES.includes(dateStr)) {
            cls += ' cal-halfday-conflict';
            isBlocked = true;
        }

        if (calMode === 'multi' && calSelectedDates.has(dateStr)) cls += ' cal-selected';

        if (calMode === 'range') {
            const [lo, hi] = calRangeEnds(calRangeStart, calRangeEnd ?? calHoverDate);
            if (lo) {
                if (dateStr === lo) {
                    cls += ' cal-range-start';
                } else if (hi && dateStr === hi) {
                    cls += calRangeEnd ? ' cal-range-end' : ' cal-range-preview';
                } else if (hi && dateStr > lo && dateStr < hi && !isPastDate
                    && !cls.includes('cal-leave-conflict')
                    && !cls.includes('cal-halfday-conflict')) {
                    cls += ' cal-in-range';
                }
            }
        }

        const btn = makeCalBtn(day, cls, dateStr);
        if (!isBlocked) {
            btn.onclick = () => calToggleDate(dateStr);
            if (calMode === 'range') {
                btn.onmouseenter = () => {
                    calHoverDate = dateStr;
                    updateRangeHighlightAll();
                    if (calRangePicking === 'end' && calRangeStart) {
                        const [lo, hi] = calRangeEnds(calRangeStart, dateStr);
                        const previewCount = countRangeDays(lo, hi);
                        document.getElementById('calCount').textContent = previewCount;
                        const lbl = document.getElementById('calCountLabel');
                        lbl.textContent = ' day(s) preview';
                        lbl.classList.add('preview');
                    }
                };
                btn.onmouseleave = () => {
                    calHoverDate = null;
                    updateRangeHighlightAll();
                    document.getElementById('calCount').textContent = calSelectedDates.size;
                    const lbl = document.getElementById('calCountLabel');
                    lbl.textContent = ' date(s)';
                    lbl.classList.remove('preview');
                };
            }
        }
        gridEl.appendChild(btn);
    }

    const total = firstDay + daysInMonth;
    const rem   = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let i=1; i<=rem; i++)
        gridEl.appendChild(makeCalBtn(i, 'cal-day cal-other', null));
}

function calRender() {
    if (calMode === 'range') {
        let m2 = calMonth + 1, y2 = calYear;
        if (m2 > 11) { m2 = 0; y2++; }
        document.getElementById('calMonthLabel0').textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
        document.getElementById('calMonthLabel1').textContent = MONTH_NAMES[m2] + ' ' + y2;
        buildMonthGrid(document.getElementById('calDays0'), calYear, calMonth);
        buildMonthGrid(document.getElementById('calDays1'), y2, m2);
    } else {
        document.getElementById('calMonthLabel').textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
        buildMonthGrid(document.getElementById('calDays'), calYear, calMonth);
    }
    document.getElementById('calCount').textContent = calSelectedDates.size;
    document.getElementById('calCountLabel').textContent = ' date(s)';
}

function makeCalBtn(text, cls, dateStr) {
    const b = document.createElement('button');
    b.type = 'button'; b.className = cls; b.textContent = text;
    if (dateStr) b.dataset.date = dateStr;
    return b;
}

function calRangeEnds(a, b) {
    if (!a || !b) return [a, b];
    return a <= b ? [a, b] : [b, a];
}

function updateRangeHighlightAll() {
    const grids = calMode === 'range'
        ? [document.getElementById('calDays0'), document.getElementById('calDays1')]
        : [document.getElementById('calDays')];

    const [lo, hi] = calRangeEnds(calRangeStart, calRangeEnd ?? calHoverDate);

    grids.forEach(grid => {
        if (!grid) return;
        grid.querySelectorAll('.cal-day[data-date]').forEach(btn => {
            const ds = btn.dataset.date;
            btn.classList.remove('cal-range-start','cal-range-end','cal-in-range','cal-range-preview');
            if (!lo) return;
            if (ds === lo) {
                btn.classList.add('cal-range-start');
            } else if (hi && ds === hi) {
                btn.classList.add(calRangeEnd ? 'cal-range-end' : 'cal-range-preview');
            } else if (hi && ds > lo && ds < hi
                && !btn.classList.contains('cal-disabled')
                && !btn.classList.contains('cal-leave-conflict')
                && !btn.classList.contains('cal-halfday-conflict')) {
                btn.classList.add('cal-in-range');
            }
        });
    });
}

function fillRangeDates(startStr, endStr) {
    calSelectedDates.clear();
    const cur = new Date(startStr + 'T00:00:00');
    const end = new Date(endStr   + 'T00:00:00');
    while (cur <= end) {
        const ds = toYMD(cur);
        // RESTORED: Use centralized rule checker
        if (!isDateBlockedByRules(cur, ds)) {
            calSelectedDates.add(ds);
        }
        cur.setDate(cur.getDate() + 1);
    }
}

function countRangeDays(startStr, endStr) {
    let count = 0;
    const cur = new Date(startStr + 'T00:00:00');
    const end = new Date(endStr   + 'T00:00:00');
    while (cur <= end) {
        const ds = toYMD(cur);
        // RESTORED: Use centralized rule checker
        if (!isDateBlockedByRules(cur, ds)) {
            count++;
        }
        cur.setDate(cur.getDate() + 1);
    }
    return count;
}

function calToggleDate(dateStr) {
    if (calMode === 'range') {
        calToggleRange(dateStr);
    } else {
        calSelectedDates.has(dateStr) ? calSelectedDates.delete(dateStr) : calSelectedDates.add(dateStr);
        document.getElementById('calCount').textContent = calSelectedDates.size;
        document.getElementById('calCountLabel').textContent = ' date(s)';
        calRender();
    }
}

function calToggleRange(dateStr) {
    const ind  = document.getElementById('calRangeIndicator');
    const done = document.getElementById('calDoneBtn');
    const lbl  = document.getElementById('calCountLabel');

    if (calRangePicking === 'start') {
        calRangeStart   = dateStr;
        calRangeEnd     = null;
        calRangePicking = 'end';
        calSelectedDates.clear();
        calSelectedDates.add(dateStr);
        ind.className   = 'cal-range-indicator step-end';
        ind.textContent = `Start: ${formatDateDisplay(dateStr)} — now click the end date`;
        if (done) done.disabled = true;
        if (lbl)  { lbl.textContent = ' date(s)'; lbl.classList.remove('preview'); }
    } else {
        const [lo, hi]  = calRangeEnds(calRangeStart, dateStr);
        calRangeStart   = lo;
        calRangeEnd     = hi;
        calRangePicking = 'start';
        fillRangeDates(lo, hi);
        ind.className   = 'cal-range-indicator step-done';
        ind.textContent = `${calSelectedDates.size} working day(s) selected — click any date to reselect`;
        if (done) done.disabled = false;
        if (lbl)  { lbl.classList.remove('preview'); }
    }

    document.getElementById('calCount').textContent = calSelectedDates.size;
    document.getElementById('calCountLabel').textContent = ' date(s)';
    calRender();
}

function isDuplicateLeaveDate(dateStr) {
    for (const data of Object.values(LEAVE_DATA)) {
        const s = data.status;
        if (s === 'CANCELLED' || s === 'REJECTED') continue;
        const st = data.start_date_raw, en = data.end_date_raw;
        if (st && en && dateStr >= st && dateStr <= en) return true;
    }
    return false;
}

function calNav(dir) {
    calMonth += dir;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    if (calMonth < 0)  { calMonth = 11; calYear--; }
    calRender();
}

function calClearAll() {
    calSelectedDates.clear();
    calRangeStart   = null;
    calRangeEnd     = null;
    calRangePicking = 'start';
    calHoverDate    = null;
    const ind = document.getElementById('calRangeIndicator');
    if (calMode === 'range') {
        ind.className   = 'cal-range-indicator step-start';
        ind.textContent = 'Click the start date on either calendar';
        const done = document.getElementById('calDoneBtn');
        if (done) done.disabled = true;
    }
    document.getElementById('calCountLabel').textContent = ' date(s)';
    calRender(); renderCalChips(); updateCalDisplay();
}

function calDone() {
    if (calMode === 'range' && calRangePicking === 'end') {
        showToast('Select End Date', 'Please click an end date to complete your range.', 'warning');
        return;
    }
    renderCalChips(); updateCalDisplay(); forceCloseCalendar(); updateDaysFromCalendar();
}

function renderCalChips() {
    const area = document.getElementById('calChipsArea');
    area.innerHTML = '';
    [...calSelectedDates].sort().forEach(ds => {
        const c = document.createElement('div');
        c.className = 'cal-chip';
        c.innerHTML = `<span>${formatDateDisplay(ds)}</span><span class="cal-chip-x" onclick="calRemoveDate('${ds}')">×</span>`;
        area.appendChild(c);
    });
}

function calRemoveDate(ds) {
    calSelectedDates.delete(ds);
    if (calMode === 'range') {
        calRangeStart   = null;
        calRangeEnd     = null;
        calRangePicking = 'start';
        calHoverDate    = null;
        const ind       = document.getElementById('calRangeIndicator');
        ind.className   = 'cal-range-indicator step-start';
        ind.textContent = 'Click the start date on either calendar';
    }
    renderCalChips(); updateCalDisplay(); calRender(); updateDaysFromCalendar();
}

function updateCalDisplay() {
    const t = document.getElementById('calTriggerText'), n = calSelectedDates.size;
    t.textContent = n === 0 ? 'Click to select leave dates…' : (n === 1 ? '1 date selected' : `${n} dates selected`);
    t.classList.toggle('placeholder', n === 0);
}

function updateDaysFromCalendar() {
    const n = calSelectedDates.size;
    document.getElementById('f_no_days_display').value = n > 0 ? n + ' day(s)' : '';
    const c = document.getElementById('calHiddenInputs');
    c.innerHTML = '';
    const sorted = [...calSelectedDates].sort();
    sorted.forEach(ds => addHidden(c, 'leave_dates[]', ds));
    if (sorted.length > 0) {
        addHidden(c, 'start_date', sorted[0]);
        addHidden(c, 'end_date', sorted[sorted.length-1]);
    }
}

function addHidden(p, name, val) {
    const i = document.createElement('input');
    i.type='hidden'; i.name=name; i.value=val;
    p.appendChild(i);
}

function toggleCalendar(e) { if(e) e.stopPropagation(); calIsOpen ? forceCloseCalendar() : openCalendar(); }
function openCalendar() {
    document.getElementById('calPopup').style.display = 'block';
    document.getElementById('calTrigger').classList.add('open');
    document.getElementById('leavePanelFooter').classList.add('cal-active');
    calIsOpen = true; calRender();
}
function forceCloseCalendar() {
    document.getElementById('calPopup').style.display = 'none';
    document.getElementById('calTrigger').classList.remove('open');
    document.getElementById('leavePanelFooter').classList.remove('cal-active');
    calIsOpen = false;
}

function toYMD(d) { return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
function formatDateDisplay(ds) { const[y,m,d]=ds.split('-'); return new Date(+y,+m-1,+d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}); }

/* ════════════════════════════════════════════════════════
   DETAIL PANEL
════════════════════════════════════════════════════════ */
function openDetailPanel(leaveId, e) {
    if(e) e.stopPropagation();
    const d = ALL_DATA[leaveId]; if(!d) return;
    activePanelLeaveId = leaveId;
    const isMonetize = !!MONETIZE_DATA[leaveId];
    document.getElementById('dpTitle').textContent = isMonetize ? 'Monetization Request Details' : 'Leave Application Details';
    document.getElementById('dpSubtitle').textContent = `${d.leave_type} · Filed: ${d.application_date}`;
    const SC = {PENDING:'#fef9c3|#854d0e',RECEIVED:'#dbeafe|#1e40af','ON-PROCESS':'#ede9fe|#5b21b6',APPROVED:'#dcfce7|#14532d',REJECTED:'#fee2e2|#991b1b',CANCELLED:'#f3f4f6|#6b7280',RECALLED:'#ede9fe|#5b21b6'};
    const [sBg,sC] = (SC[d.status] || '#f3f4f6|#6b7280').split('|');
    const sl = d.status === 'ON-PROCESS' ? 'On Process' : d.status.charAt(0) + d.status.slice(1).toLowerCase();
    let html = `<div class="dp-card"><div class="dp-section-heading"><div class="dp-section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><p class="dp-section-title">${isMonetize?'Monetization':'Application'} Details</p></div><div class="dp-grid">
    <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
    <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>`;
    if (!isMonetize) {
        html += `<div class="dp-field"><label>Start Date</label><p>${d.start_date}</p></div>
        <div class="dp-field"><label>End Date</label><p>${d.end_date}</p></div>
        <div class="dp-field"><label>Number of Days</label><p>${d.no_of_days} day(s)</p></div>
        <div class="dp-field"><label>Details of Leave</label><p>${d.details_of_leave}</p></div>
        <div class="dp-field"><label>Commutation</label><p>${d.commutation==='REQUESTED'?'Requested':'Not Requested'}</p></div>`;
    } else {
        html += `<div class="dp-field"><label>Days to Monetize</label><p>${d.no_of_days} day(s)</p></div>
        <div class="dp-field"><label>Estimated Amount</label><p style="font-weight:700;color:#15803d;">${d.est_amount}</p></div>`;
    }
    html += `<div class="dp-field"><label>Status</label><p><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${sl}</span></p></div>`;
    html += `<div class="dp-field"><label>Last Updated</label><p>${d.updated_at_display}</p></div>`;
    if (d.reason)        html += `<div class="dp-field span2"><label>Reason / Remarks</label><p>${d.reason}</p></div>`;
    if (d.reject_reason) html += `<div class="dp-field span2"><label>Rejection Reason</label><p style="color:#dc2626;">${d.reject_reason}</p></div>`;
    html += `</div></div><div style="height:8px;"></div>`;
    document.getElementById('dpBody').innerHTML = html;
    const isLocked = ['APPROVED','REJECTED','CANCELLED','RECALLED'].includes(d.status);
    const labels = {APPROVED:'Application Approved',REJECTED:'Application Rejected',CANCELLED:'Application Cancelled',RECALLED:'Application Recalled'};
    document.getElementById('dpActionBtns').innerHTML = (!isLocked && d.status === 'PENDING')
        ? `<button class="btn-cancel-action" onclick="cancelApp(${leaveId})"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Cancel Application</button>`
        : `<span style="font-size:12px;color:#6b7280;font-weight:600;">${labels[d.status] || 'Status: '+sl}</span>`;
    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeDetailPanel() { document.getElementById('detailPanel').classList.remove('open'); hidePanelOverlayIfNoneOpen(); document.body.style.overflow=''; activePanelLeaveId=null; }
function viewPdfFromPanel() { if(activePanelLeaveId) viewPdf(activePanelLeaveId); }

/* ════════════════════════════════════════════════════════
   PANELS
════════════════════════════════════════════════════════ */
function openLeavePanel() { resetLeaveForm(); closeDetailPanel(); document.getElementById('leavePanel').classList.add('open'); document.getElementById('panelOverlay').classList.add('show'); document.body.style.overflow='hidden'; calInit(); }
function closeLeavePanel() { forceCloseCalendar(); document.getElementById('leavePanel').classList.remove('open'); hidePanelOverlayIfNoneOpen(); document.body.style.overflow=''; }
function openMonetizePanel() { closeDetailPanel(); document.getElementById('monetizePanel').classList.add('open'); document.getElementById('panelOverlay').classList.add('show'); document.body.style.overflow='hidden'; }
function closeMonetizePanel() { document.getElementById('monetizePanel').classList.remove('open'); hidePanelOverlayIfNoneOpen(); document.body.style.overflow=''; }
function closeAllPanels() { forceCloseCalendar(); ['leavePanel','monetizePanel','detailPanel'].forEach(id=>document.getElementById(id).classList.remove('open')); document.getElementById('panelOverlay').classList.remove('show'); document.body.style.overflow=''; activePanelLeaveId=null; }
function hidePanelOverlayIfNoneOpen() { if(!document.querySelector('.slide-panel.open')) document.getElementById('panelOverlay').classList.remove('show'); }

function resetLeaveForm() {
    document.getElementById('leaveForm').reset();
    document.getElementById('f_no_days_display').value = '';
    document.getElementById('balanceHint').textContent = '';
    document.getElementById('balanceHint').className = 'text-xs mt-1';
    document.getElementById('maxDaysBanner').style.display = 'none';
    clearDetailsWrapper();

    calMode           = 'multi';
    calWeekendAllowed = false;
    calRangeStart     = null;
    calRangeEnd       = null;
    calRangePicking   = 'start';
    calHoverDate      = null;
    calSelectedDates.clear();
    
    // RESTORED: Reset global rules on form reset
    activeNoticeDays  = 0;
    activeAllowPast   = false;

    const popup      = document.getElementById('calPopup');
    const dualWrap   = document.getElementById('calDualWrap');
    const singleWrap = document.getElementById('calSingleWrap');
    if (popup)      popup.classList.remove('dual-mode');
    if (dualWrap)   dualWrap.style.display   = 'none';
    if (singleWrap) singleWrap.style.display = '';

    const ind = document.getElementById('calRangeIndicator');
    if (ind) { ind.className = 'cal-range-indicator'; ind.style.display = 'none'; }
    const leg = document.getElementById('rangeLegendItem');
    if (leg) leg.style.display = 'none';
    const hint = document.getElementById('calModeHint');
    if (hint) hint.textContent = 'Weekends and dates blocked by notice periods are disabled.';

    document.getElementById('calChipsArea').innerHTML   = '';
    document.getElementById('calHiddenInputs').innerHTML = '';
    const t = document.getElementById('calTriggerText');
    t.textContent = 'Click to select leave dates…'; t.classList.add('placeholder');
    forceCloseCalendar();
}

/* ════════════════════════════════════════════════════════
   TAB SWITCH
════════════════════════════════════════════════════════ */
function switchTab(tab) {
    const tabs   = ['leave','monetize','history'];
    const panels = { leave:'panelLeave', monetize:'panelMonetize', history:'panelHistory' };
    const tabIds = { leave:'tabLeave',   monetize:'tabMonetize',   history:'tabHistory'   };
    const crumbs = { leave:'Leave Application', monetize:'Monetization Request List', history:'Application History' };
    const btnText= { leave:'Apply for Leave',   monetize:'Request Monetization',      history:null };
    const btnFns = { leave:openLeavePanel,       monetize:openMonetizePanel,           history:null };

    tabs.forEach(t => {
        document.getElementById(tabIds[t]).classList.toggle('active', t === tab);
        document.getElementById(panels[t]).classList.toggle('hidden', t !== tab);
    });

    document.getElementById('breadcrumbCurrent').textContent = crumbs[tab];
    const applyBtn     = document.getElementById('applyBtn');
    const applyBtnText = document.getElementById('applyBtnText');

    if (tab === 'history') {
        applyBtn.style.display = 'none';
    } else {
        applyBtn.style.display = '';
        applyBtnText.textContent = btnText[tab];
        applyBtn.onclick = btnFns[tab];
    }

    // Re-render the appropriate table to apply sort
    if (tab === 'leave')    { updateSortHeaders('leave');    renderLeaveRows(); }
    if (tab === 'monetize') { updateSortHeaders('monetize'); renderMonetizeRows(); }
    if (tab === 'history')  { updateSortHeaders('history');  renderHistoryRows(); }
}

/* ════════════════════════════════════════════════════════
   LEAVE TYPE → DETAILS + BALANCE HINT + MAX DAYS + CAL MODE
════════════════════════════════════════════════════════ */
const DETAILS_CONFIG = {
    vacation:         {type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    special_privilege:{type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    mandatory:        {type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    sick:             {type:'radio',options:[{value:'In Hospital',label:'In Hospital',specify:true,placeholder:'Specify illness…'},{value:'Out Patient',label:'Out Patient',specify:true,placeholder:'Specify illness…'}]},
    study:            {type:'radio',options:[{value:"Completion of Master's Degree",label:"Completion of Master's Degree",specify:false},{value:'BAR/Board Examination Review',label:'BAR/Board Examination Review',specify:false}]},
    women:            {type:'text',placeholder:'Specify details…'},
    none:             {type:'none'},
};

function resolveDetailsKey(code, name) {
    const c = (code||'').toLowerCase(), n = (name||'').toLowerCase();
    if (c.includes('vl')||n.includes('vacation'))           return 'vacation';
    if (c.includes('spl')||n.includes('special privilege')) return 'special_privilege';
    if (c.includes('mfl')||n.includes('mandatory')||n.includes('forced')) return 'mandatory';
    if ((c==='sl'||c.startsWith('sl_')||c.startsWith('sl-'))||n.includes('sick')) return 'sick';
    if (c.includes('stl')||n.includes('study'))             return 'study';
    if (n.includes('women')||c.includes('slbw'))            return 'women';
    return 'none';
}

function clearDetailsWrapper() {
    document.getElementById('detailsOfLeaveWrapper').querySelectorAll('.dynamic-detail').forEach(el => el.remove());
    const h = document.getElementById('detailsHint');
    if (h) { h.textContent = 'Select a leave type to see available options.'; h.className = 'text-xs text-gray-400 mt-1'; }
}

function onLeaveTypeChange(sel) {
    const opt       = sel.options[sel.selectedIndex];
    const ltId      = parseInt(sel.value);
    const isAccrual = opt.dataset.accrual === '1';
    const code      = opt.dataset.code || '';
    const name      = opt.text || '';
    const hint      = document.getElementById('balanceHint');
    
    // RESTORED: Update the global rule variables whenever a leave type is selected
    activeNoticeDays = parseInt(opt.dataset.noticedays) || 0;
    activeAllowPast  = (opt.dataset.allowpast === '1' || opt.dataset.allowpast === 'true');

    if (isAccrual && CREDIT_BALANCES[ltId]) {
        const rawRem = CREDIT_BALANCES[ltId].remaining_balance;
        hint.innerHTML  = `Balance: <strong>${truncate3(rawRem)} days</strong>`;
        hint.className  = 'text-xs text-green-600 mt-1';
    } else if (isAccrual) {
        hint.textContent = 'No balance record found for this year.';
        hint.className   = 'text-xs text-red-400 mt-1';
    } else {
        hint.textContent = 'No balance required for this leave type.';
        hint.className   = 'text-xs text-gray-400 mt-1';
    }

    renderMaxDaysBanner(ltId);

    const rawMax   = parseFloat(opt.dataset.maxdays);
    const useRange = !isNaN(rawMax) && rawMax > 10;
    setCalMode(useRange ? 'range' : 'multi');

    if (calIsOpen) calRender();

    clearDetailsWrapper();
    const wrapper = document.getElementById('detailsOfLeaveWrapper');
    const dh      = document.getElementById('detailsHint');
    const cfg     = DETAILS_CONFIG[resolveDetailsKey(code, name)];

    if (!cfg || cfg.type === 'none') {
        if (dh) dh.textContent = 'No additional details required for this leave type.';
        const inp = document.createElement('input');
        inp.type='hidden'; inp.name='details_of_leave'; inp.value=''; inp.className='dynamic-detail';
        wrapper.appendChild(inp);
    } else if (cfg.type === 'text') {
        if (dh) dh.textContent = '';
        const inp = document.createElement('input');
        inp.type='text'; inp.name='details_of_leave'; inp.className='form-field dynamic-detail';
        inp.placeholder = cfg.placeholder || 'Specify details…';
        wrapper.appendChild(inp);
    } else if (cfg.type === 'radio') {
        if (dh) dh.textContent = '';
        const rw = document.createElement('div');
        rw.className = 'dynamic-detail flex flex-col gap-1 mt-1';
        cfg.options.forEach((o, i) => {
            const row = document.createElement('div'); row.className = 'flex flex-col';
            const lbl = document.createElement('label');
            lbl.className = 'flex items-center gap-2.5 cursor-pointer text-sm text-gray-700 py-1';
            lbl.innerHTML = `<input type="radio" name="details_of_leave" value="${o.value}" id="detail_${i}" class="w-3.5 h-3.5 accent-green-700 cursor-pointer"><span>${o.label}${o.specify?' <span class="text-gray-400 text-xs">(specify below)</span>':''}</span>`;
            row.appendChild(lbl);
            if (o.specify) {
                const sw = document.createElement('div');
                sw.className = 'dynamic-detail specify-wrap mt-2 ml-6';
                sw.dataset.forValue = o.value; sw.style.display = 'none';
                const si = document.createElement('input');
                si.type='text'; si.name='details_specify'; si.className='form-field';
                si.placeholder = o.placeholder || 'Specify…'; si.style.fontSize='12px'; si.style.padding='7px 10px';
                sw.appendChild(si); row.appendChild(sw);
                lbl.querySelector('input').addEventListener('change', () => {
                    wrapper.querySelectorAll('.specify-wrap').forEach(w => { w.style.display='none'; w.querySelector('input').value=''; });
                    sw.style.display='block'; si.focus();
                });
            } else {
                lbl.querySelector('input').addEventListener('change', () => {
                    wrapper.querySelectorAll('.specify-wrap').forEach(w => { w.style.display='none'; w.querySelector('input').value=''; });
                });
            }
            rw.appendChild(row);
        });
        wrapper.appendChild(rw);
    }
}

/* ════════════════════════════════════════════════════════
   MONETIZE
════════════════════════════════════════════════════════ */
function updateMonetizeBalance(sel) {
    const ltId      = parseInt(sel.value);
    const rawRem    = sel.options[sel.selectedIndex]?.dataset.remaining ?? '0';
    const box       = document.getElementById('monetizeBalanceBox');

    if (CREDIT_BALANCES[ltId]) {
        document.getElementById('monetizeBalanceVal').textContent = truncate3(rawRem);
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
    calcMonetizeAmount();
}

function calcMonetizeAmount() {
    const inp = document.getElementById('fm_no_of_days'); let days = parseFloat(inp.value) || 0;
    const warn = document.getElementById('monetizeLimitWarn');
    warn.classList.add('hidden');
    const box = document.getElementById('monetizeAmountBox');
    if (days > 0) {
        const amt = EMPLOYEE_SALARY * days * 0.0481927;
        document.getElementById('monetizeAmountVal').textContent = '₱' + amt.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
        box.classList.remove('hidden');
    } else box.classList.add('hidden');
}

/* ════════════════════════════════════════════════════════
   SUBMIT LEAVE
════════════════════════════════════════════════════════ */
function submitLeave() {
    const ltId = parseInt(document.getElementById('f_leave_type_id').value);
    let ok = true;
    document.getElementById('err_leave_type').classList.toggle('hidden', !!ltId);
    document.getElementById('err_dates').classList.toggle('hidden', calSelectedDates.size > 0);
    if (!ltId || calSelectedDates.size === 0) ok = false;
    if (!ok) return;

    const conflictingLeave   = [...calSelectedDates].find(ds => isDuplicateLeaveDate(ds));
    const conflictingHalfDay = [...calSelectedDates].find(ds => HALF_DAY_DATES.includes(ds));
    if (conflictingLeave)   { showToast('Date Conflict', `${formatDateDisplay(conflictingLeave)} overlaps with an existing leave application.`, 'error'); return; }
    if (conflictingHalfDay) { showToast('Date Conflict', `${formatDateDisplay(conflictingHalfDay)} already has a half-day certification. Please cancel it first.`, 'error'); return; }

    // Enforce the block ONLY if max_days actually exists and is greater than 0
    if (MAX_DAYS_DATA[ltId] && MAX_DAYS_DATA[ltId].max_days && MAX_DAYS_DATA[ltId].max_days > 0) {
        const mx = MAX_DAYS_DATA[ltId];
        if (mx.remaining <= 0) { showToast('Annual Limit Reached', `You have used all ${mx.max_days} day(s) allowed for ${mx.type_name} this year.`, 'error'); return; }
        if (calSelectedDates.size > mx.remaining) { showToast('Annual Limit Exceeded', `${mx.type_name} allows ${mx.max_days} day(s)/year. You have ${mx.remaining} day(s) left but selected ${calSelectedDates.size}.`, 'error'); return; }
    }

    const btn = document.getElementById('submitLeaveBtn');
    btn.textContent = 'Submitting…'; btn.disabled = true;

    const sorted = [...calSelectedDates].sort();
    const body   = new FormData();
    body.append('leave_type_id', ltId);
    sorted.forEach(ds => body.append('leave_dates[]', ds));
    body.append('start_date',  sorted[0]);
    body.append('end_date',    sorted[sorted.length-1]);
    body.append('no_of_days',  calSelectedDates.size);
    body.append('details_of_leave', (() => {
        const checked = document.querySelector('input[name="details_of_leave"]:checked');
        if (checked) {
            const sw = document.querySelector(`.specify-wrap[data-for-value="${CSS.escape(checked.value)}"]`);
            if (sw && sw.style.display !== 'none') {
                const sv = (sw.querySelector('input')?.value||'').trim();
                if (sv) return `${checked.value}: ${sv}`;
            }
            return checked.value;
        }
        const inp = document.querySelector('#detailsOfLeaveWrapper [name="details_of_leave"]');
        return inp ? inp.value : '';
    })());
    body.append('commutation', document.getElementById('f_commutation').value);
    body.append('reason',      document.getElementById('f_reason').value);
    body.append('_token',      CSRF);

    fetch(LEAVE_URL, { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeLeavePanel();
            viewPdf(data.leave_id);
            showToast('Application Submitted!', 'Your leave application is now pending approval.', 'success');
            const leaveTypeName = document.getElementById('f_leave_type_id').options[document.getElementById('f_leave_type_id').selectedIndex]?.text || '—';
            showSuccessModal('leave', [
                { label:'Leave Type',  value: leaveTypeName },
                { label:'No. of Days', value: calSelectedDates.size + ' day(s)' },
                { label:'Start Date',  value: formatDateDisplay(sorted[0]) },
                { label:'End Date',    value: formatDateDisplay(sorted[sorted.length-1]) },
                { label:'Status',      value: 'Pending for Review' },
            ]);
            setTimeout(() => location.reload(), 4000);
        } else {
            showToast('Error', data.message || 'Something went wrong.', 'error');
        }
    })
    .catch(() => showToast('Network Error', 'Please check your connection.', 'error'))
    .finally(() => { btn.textContent='Confirm Application'; btn.disabled=false; });
}

/* ════════════════════════════════════════════════════════
   SUBMIT MONETIZE
════════════════════════════════════════════════════════ */
function submitMonetize() {
    const ltId   = document.getElementById('fm_leave_type_id').value;
    const days   = document.getElementById('fm_no_of_days').value;
    const reason = document.getElementById('fm_reason').value.trim();
    const errReason = document.getElementById('err_monetize_reason');

    if (errReason) errReason.classList.toggle('hidden', !!reason);
    if (!ltId || !days) { showToast('Missing Fields','Please select a leave type and number of days.','error'); return; }
    if (!reason) { showToast('Missing Fields','Please provide a reason for monetization.','error'); return; }
    const btn = document.getElementById('submitMonetizeBtn'); btn.textContent='Submitting…'; btn.disabled=true;
    const body = new FormData(); body.append('leave_type_id',ltId); body.append('no_of_days',days);
    body.append('reason',document.getElementById('fm_reason').value); body.append('_token',CSRF);
    fetch(MONETIZE_URL,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body})
    .then(r=>r.json()).then(data=>{
        if (data.success) {
            closeMonetizePanel();
            if (data.leave_id) viewPdf(data.leave_id);
            showToast('Request Submitted!','Your monetization request is now pending approval.','success');
            const leaveTypeName = document.getElementById('fm_leave_type_id').options[document.getElementById('fm_leave_type_id').selectedIndex]?.text||'—';
            const estAmt = EMPLOYEE_SALARY * parseInt(days) * 0.0481927;
            showSuccessModal('monetize',[
                {label:'Leave Type',       value:leaveTypeName},
                {label:'Days to Monetize', value:days+' day(s)'},
                {label:'Estimated Amount', value:'₱'+estAmt.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})},
                {label:'Status',           value:'Pending for Approval'},
            ]);
            setTimeout(()=>location.reload(),4000);
        } else showToast('Error',data.message||'Something went wrong.','error');
    }).catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.textContent='Submit Request';btn.disabled=false;});
}

/* ════════════════════════════════════════════════════════
   CANCEL MODAL
════════════════════════════════════════════════════════ */
function cancelApp(id) {
    pendingCancelId = id;
    const m = document.getElementById('cancelModal'), c = document.getElementById('cancelModalCard');
    m.style.display='flex'; c.style.transform='scale(0.93)';
    requestAnimationFrame(()=>requestAnimationFrame(()=>{m.style.opacity='1';c.style.transform='scale(1)';}));
    document.body.style.overflow='hidden';
}
function closeCancelModal() {
    const m = document.getElementById('cancelModal'); m.style.opacity='0'; document.getElementById('cancelModalCard').style.transform='scale(0.93)';
    setTimeout(()=>{m.style.display='none';},200); document.body.style.overflow=''; pendingCancelId=null;
}
function confirmCancelApp() {
    if(!pendingCancelId) return;
    const id=pendingCancelId, btn=document.getElementById('confirmCancelBtn');
    btn.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Cancelling…';
    btn.disabled=true; closeCancelModal(); closeDetailPanel();
    fetch(CANCEL_URL+'/'+id+'/cancel',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:new FormData()})
    .then(r=>r.json()).then(data=>{
        if(data.success){showToast('Application Cancelled','Your leave application has been successfully cancelled.','warning');setTimeout(()=>location.reload(),1800);}
        else showToast('Error',data.message||'Could not cancel application.','error');
    }).catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.innerHTML='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Yes, Cancel It';btn.disabled=false;});
}

/* ════════════════════════════════════════════════════════
   VIEW PDF
════════════════════════════════════════════════════════ */
function viewPdf(id) {
    const win = window.open(PDF_URL+'/'+id+'/pdf','_blank');
    if(!win||win.closed||typeof win.closed==='undefined') showToast('Popup Blocked','Allow popups for this site to view the PDF.','error');
}

/* ════════════════════════════════════════════════════════
   ACTION MENUS
════════════════════════════════════════════════════════ */
function toggleMenu(btn, e) {
    if (e) e.stopPropagation();
    const dd = btn.nextElementSibling;
    const isOpen = dd.classList.contains('open');
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
        dd.classList.add('open');
        const rect = btn.getBoundingClientRect();
        dd.style.top = (rect.bottom + 4) + 'px';
        dd.style.left = (rect.right - 160) + 'px';
        dd.style.transform = window.innerHeight - rect.bottom < 120 ? `translateY(calc(-100% - ${rect.height + 8}px))` : '';
    }
}
document.addEventListener('click',()=>document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')));
window.addEventListener('scroll',()=>document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')),true);

/* ════════════════════════════════════════════════════════
   FILTERS — now just re-render (sort is preserved in state)
════════════════════════════════════════════════════════ */
function filterLeave()    { renderLeaveRows(); }
function filterMonetize() { renderMonetizeRows(); }
function filterHistory()  { renderHistoryRows(); }

/* ════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════ */
function showToast(title, msg, type='success') {
    const map = {
        success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},
        error:  {bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},
        warning:{bg:'#fef9c3',c:'#ca8a04',p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'},
    };
    const s = map[type] || map.success;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    const icon = document.getElementById('toastIcon');
    icon.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M ${s.p}"/></svg>`;
    icon.style.background = s.bg;
    const t = document.getElementById('toast'); t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 3500);
}

/* ════════════════════════════════════════════════════════
   VIEW MONETIZE REQUEST LETTER
════════════════════════════════════════════════════════ */
async function getBase64FromUrl(url) {
    const response = await fetch(url);
    const blob = await response.blob();
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}

async function viewMonetizeRequest(leaveId) {
    const d = MONETIZE_DATA[leaveId];
    if (!d) return;

    // ── Signatories (mirrors pdf.blade.php index logic) ──
    const sigProv = SIGNATORIES[1] ?? null;
    const sigGov  = SIGNATORIES[2] ?? null;

    const provName  = sigProv ? sigProv.full_name.toUpperCase() : '';
    const provTitle = sigProv ? sigProv.title : '';
    const govName   = sigGov  ? sigGov.full_name.toUpperCase()  : '';
    const govTitle  = sigGov  ? sigGov.title                    : '';

    // ── Date (Manila timezone, always today) ──
    const appDate = new Date().toLocaleDateString('en-US', {
        timeZone: 'Asia/Manila',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // ── Leave details ──
    const leaveType = d.leave_type || '—';
    const noDays    = d.no_of_days || '—';
    const dailyRate = {{ $dailyRate }};
    const estAmount = (parseFloat(noDays) * dailyRate).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const creditLabel = leaveType.toLowerCase().includes('sick')
        ? 'Sick Leave Credits'
        : leaveType.toLowerCase().includes('vacation')
            ? 'Vacation Leave Credits'
            : leaveType + ' Credits';
    


    // ── Logo (base64 so it works inside popup) ──
    let logoBase64 = '';
    try {
        logoBase64 = await getBase64FromUrl('/images/kapitolyo.png');
    } catch (e) {
        console.warn('Could not load logo:', e);
    }
    
    const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monetization Request – ${EMPLOYEE_NAME}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 13pt;
    color: #000;
    background: #fff;
    padding: 0;
  }
  .page {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto;
    padding: 25mm 25mm 20mm 30mm;
    background: #fff;
    position: relative;
  }
  .header {
    text-align: center;
    margin-bottom: 16px;
  }
  .seal-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin-bottom: 4px;
  }
  .seal {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
  }
  .seal img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .header-text {
    text-align: center;
  }
  .header-text p { line-height: 1; }
  .republic { font-size: 12pt; }
  .province { font-size: 14pt; font-weight: bold; }
  .daet     { font-size: 12pt; }
  .office   { font-size: 13pt; font-weight: bold; }
  .date-line {
    text-align: right;
    margin-bottom: 24px;
    font-size: 12pt;
  }
  .addressee { margin-bottom: 18px; }
  .addressee p { line-height: 1; font-size: 12pt; }
  .addressee .name { font-weight: bold; font-size: 13pt; text-transform: uppercase; }
  .salutation { margin-bottom: 14px; font-size: 12pt; line-height: 1; }
  .body-section { margin-bottom: 18px; line-height: 1.8; text-align: justify; font-size: 12pt; }
  .body-section p { text-indent: 40px; }
.sig-block {
    text-align: right;
    margin-top: 10px;
  }
  .sig-right {
    display: inline-block;
    text-align: center;
  }
  .noted-block { margin-top: 36px; font-size: 12pt; }
  .noted-label { margin-bottom: 40px; }
  .approved-block { margin-top: 36px; font-size: 12pt; }
  .approved-label { margin-bottom: 4px; font-weight: bold; }
  .sig-name {
    font-weight: bold;
    font-size: 12pt;
    text-transform: uppercase;
    display: block;
    text-align: center;
    white-space: nowrap;
  }
  .sig-title {
    font-size: 11pt;
    text-align: center;    /* ← center the title */
    display: block;
  }
  .noted-block { margin-top: 36px; font-size: 12pt; }
  .noted-label { margin-bottom: 40px; }
  .approved-block { margin-top: 36px; font-size: 12pt; }
  .approved-label { margin-bottom: 4px; font-weight: bold; }
  .print-btn {
    display: block;
    margin: 20px auto 0;
    padding: 10px 32px;
    background: #1a3a1a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13pt;
    cursor: pointer;
    font-family: Arial, sans-serif;
  }
  .print-btn:hover { background: #2d5a1b; }
  @media print {
    body { padding: 0; }
    .page { padding: 20mm 20mm 20mm 25mm; width: 100%; box-shadow: none; }
    .no-print { display: none !important; }
    @page { size: A4; margin: 0; }
  }
</style>
</head>
<body>

<div class="page">

  <div class="header">
    <div class="seal-row">
      <div class="seal">
        ${logoBase64
            ? `<img src="${logoBase64}" alt="Province Seal">`
            : `<div style="width:80px;height:80px;border:2px solid #000;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:7pt;font-weight:bold;text-align:center;padding:6px;line-height:1.2;">PROVINCE<br>OF<br>CAMARINES<br>NORTE<br>★<br>OFFICIAL<br>SEAL</div>`
        }
      </div>
      <div class="header-text">
        <p class="republic">Republic of the Philippines</p>
        <p class="province">PROVINCE OF CAMARINES NORTE</p>
        <p class="daet">Daet</p>
        <p class="office">OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
      </div>
    </div>
  </div>


  <div class="date-line">${appDate}</div>

  <div class="addressee">
    <p class="name">${govName}</p>
    <p>${govTitle}</p>
    <p>Province of Camarines Norte</p>
  </div>

  <div class="salutation">Dear Sir:</div>

  <div class="body-section">
    <p>Greetings!</p>
  </div>

  <div class="body-section">
    <p>Respectfully requesting your good office to kindly allow me to monetize <strong>${noDays} day(s)</strong> of my <strong>${creditLabel}</strong> with an estimated amount of <strong>₱${estAmount}</strong>, to be used for ${d.reason || 'the maintenance of my medicines'}.</p>
  </div>

  <div class="body-section">
    <p>Anticipating with thanks and kind consideration on this request</p>
  </div>

    <div style="margin-top:10px; text-align:right;">
    <p style="font-size:12pt;margin-bottom:48px;">Very truly yours,</p>
  </div>


  <div class="sig-block">
    <div class="sig-right">
      <p class="sig-name">${EMPLOYEE_NAME}</p>
      <p class="sig-title">{{ $employee->position->position_name ?? '' }}</p>
    </div>
  </div>

  <div class="noted-block">
    <p class="noted-label">Noted:</p>
    <div style="display:inline-block; text-align:center;">
      <p class="sig-name">${provName}</p>
      <p class="sig-title">${provTitle}</p>
    </div>
  </div>

  <div class="approved-block" style="text-align:center;">
    <p class="noted-label">APPROVED:</p>
    <br>
    <div style="display:inline-block; text-align:center;">
      <p class="sig-name">${govName}</p>
      <p class="sig-title">${govTitle}</p>
    </div>
  </div>

</div></body>
</html>`;

   // ── Inject modal into current page instead of popup window ──
    const modalId = 'monetizeLetterModal';
    let existing = document.getElementById(modalId);
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.id = modalId;
    modal.style.cssText = `
        position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        padding:20px;
    `;

    modal.innerHTML = `
        <div style="background:#fff; border-radius:16px; width:100%; max-width:860px;
                    max-height:90vh; display:flex; flex-direction:column;
                    box-shadow:0 24px 64px rgba(0,0,0,0.3); overflow:hidden;">
            <div style="background:linear-gradient(135deg,#1a3a1a,#2d5a1b); padding:16px 24px;
                        display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                <div>
                    <p style="color:#fff; font-weight:700; font-size:15px; margin:0;">Monetization Request Letter</p>
                    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:0;">${EMPLOYEE_NAME}</p>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button onclick="document.getElementById('${modalId}').querySelector('iframe').contentWindow.print()"
                        style="padding:7px 16px; background:#fff; border:none; border-radius:8px;
                               font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        🖨️ Print / Save PDF
                    </button>
                    <button onclick="document.getElementById('${modalId}').remove(); document.body.style.overflow='';"
                        style="width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.15);
                               border:none; cursor:pointer; color:#fff; font-size:18px; display:flex;
                               align-items:center; justify-content:center; line-height:1;">×</button>
                </div>
            </div>
            <div style="flex:1; overflow:auto; background:#e5e7eb; padding:16px; display:flex; justify-content:center;">
                <iframe id="monetizeLetterFrame" style="width:100%; max-width:820px; height:100%;
                        min-height:600px; border:none; border-radius:8px;
                        box-shadow:0 4px 20px rgba(0,0,0,0.2); background:#fff;"></iframe>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
            document.body.style.overflow = '';
        }
    });

    const iframe = document.getElementById('monetizeLetterFrame');
    const iDoc = iframe.contentDocument || iframe.contentWindow.document;
    iDoc.open();
    iDoc.write(html);
    iDoc.close();

}
/* ════════════════════════════════════════════════════════
   KEYBOARD & INIT
════════════════════════════════════════════════════════ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeDetailPanel(); closeMonetizePanel(); closeCancelModal(); closeSuccessModal();
        if (calIsOpen) {
            if (calMode === 'range' && (calRangeStart || calSelectedDates.size > 0)) {
                calClearAll();
            } else {
                forceCloseCalendar();
            }
        }
        closeLeavePanel();
    }
});

// Initial render — all three tables sorted by updated_at DESC
document.addEventListener('DOMContentLoaded', () => {
    updateSortHeaders('leave');
    updateSortHeaders('monetize');
    updateSortHeaders('history');
    renderLeaveRows();
    renderMonetizeRows();

    // Auto-switch tab from URL param (e.g. ?tab=monetize from notification click)
    const urlTab = new URLSearchParams(window.location.search).get('tab');
    if (urlTab === 'monetize' || urlTab === 'history') {
        switchTab(urlTab);
    }

    @if(session('success'))
        showToast('Success','{{ session("success") }}','success');
    @endif
});
</script>
@endsection