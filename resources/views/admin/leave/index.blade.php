@extends('layouts.app')
@section('title', 'Leave Applications — Admin')
@section('page-title', 'Application')

@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   RESPONSIVE LEAVE APPLICATION — FULL MOBILE + DESKTOP
═══════════════════════════════════════════════════════ */

*, *::before, *::after { box-sizing: border-box; }

/* ── Breadcrumb ── */
.breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:16px; flex-wrap:wrap; }
.breadcrumb a { color:#6b7280; text-decoration:none; }
.breadcrumb a:hover { color:#1a3a1a; }
.breadcrumb .sep { color:#d1d5db; }
.breadcrumb .current { color:#1a3a1a; font-weight:600; }

/* ── Tabs ── */
.tab-btn {
    padding:10px 4px; font-size:14px; font-weight:500;
    color:#6b7280; border:none; background:none;
    border-bottom:2px solid transparent; cursor:pointer;
    transition:all 0.2s; white-space:nowrap;
}
.tab-btn.active { color:#1a3a1a; border-bottom-color:#2d5a1b; font-weight:700; }

/* ── Table ── */
.data-table { width:100%; font-size:13px; border-collapse:collapse; }
.data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
.data-table th { padding:10px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
.data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; }
.data-table tbody tr:hover { background:#fafafa; }

.data-table tbody .leave-row { cursor:pointer; }
.data-table tbody .leave-row:hover td:nth-child(2) { color:#1a3a1a; }

/* ── Status Badges ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
.badge::before { content:'●'; font-size:8px; }
.badge-PENDING     { background:#fef9c3; color:#854d0e; }
.badge-RECEIVED    { background:#dbeafe; color:#1e40af; }
.badge-ON-PROCESS  { background:#ede9fe; color:#5b21b6; }
.badge-APPROVED    { background:#dcfce7; color:#14532d; }
.badge-REJECTED    { background:#fee2e2; color:#991b1b; }
.badge-CANCELLED   { background:#f3f4f6; color:#6b7280; }

/* ── Status Changer ── */
.status-changer { position:relative; display:inline-block; }
.status-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700;
    cursor:pointer; border:none; white-space:nowrap;
    transition:opacity 0.15s, box-shadow 0.15s; user-select:none;
}
.status-pill::before { content:'●'; font-size:8px; }
.status-pill .pill-arrow { width:12px; height:12px; opacity:0.6; transition:transform 0.2s; flex-shrink:0; }
.status-pill:hover { box-shadow:0 0 0 2px rgba(0,0,0,0.12); }
.status-changer.open .status-pill .pill-arrow { transform:rotate(180deg); }

.pill-PENDING    { background:#fef9c3; color:#854d0e; }
.pill-RECEIVED   { background:#dbeafe; color:#1e40af; }
.pill-ON-PROCESS { background:#ede9fe; color:#5b21b6; }
.pill-APPROVED   { background:#dcfce7; color:#14532d; }
.pill-REJECTED   { background:#fee2e2; color:#991b1b; }
.pill-CANCELLED  { background:#f3f4f6; color:#6b7280; }

.status-dropdown {
    position:fixed; z-index:9999;
    background:#fff; border:1.5px solid #e5e7eb; border-radius:12px;
    box-shadow:0 10px 32px rgba(0,0,0,0.13); padding:6px;
    min-width:175px; display:none; flex-direction:column; gap:2px;
}
.status-changer.open .status-dropdown { display:flex; }
.status-option {
    display:flex; align-items:center; gap:8px; padding:7px 10px;
    border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;
    border:none; background:none; width:100%; text-align:left;
    transition:background 0.1s; color:#374151;
}
.status-option:hover { background:#f3f4f6; }
.status-option.current { opacity:0.4; cursor:default; pointer-events:none; }
.status-option .opt-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.dot-PENDING    { background:#eab308; }
.dot-RECEIVED   { background:#3b82f6; }
.dot-ON-PROCESS { background:#8b5cf6; }
.dot-APPROVED   { background:#22c55e; }
.dot-REJECTED   { background:#ef4444; }
.dot-CANCELLED  { background:#9ca3af; }

.status-saving { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; border-radius:20px; background:rgba(255,255,255,0.7); pointer-events:none; opacity:0; transition:opacity 0.15s; }
.status-saving.show { opacity:1; pointer-events:all; }

/* ── Action menu ── */
.action-menu { position:relative; display:inline-block; }
.action-menu-btn { background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; color:#9ca3af; font-size:18px; letter-spacing:2px; line-height:1; }
.action-menu-btn:hover { background:#f3f4f6; color:#374151; }
.action-dropdown { position:absolute; right:0; top:100%; margin-top:4px; z-index:50; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:160px; display:none; }
.action-dropdown.open { display:block; }
.action-item { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:13px; color:#374151; cursor:pointer; border:none; background:none; width:100%; text-align:left; }
.action-item:hover { background:#f9fafb; }
.action-item:first-child { border-radius:10px 10px 0 0; }
.action-item:last-child  { border-radius:0 0 10px 10px; }

/* ── Overlay ── */
#overlay {
    position:fixed; inset:0; z-index:90;
    background:rgba(0,0,0,0.25);
    backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
    opacity:0; pointer-events:none; transition:opacity 0.3s ease;
}
#overlay.show { opacity:1; pointer-events:all; }

/* ── Full Details Slide-in Panel ── */
#detailPanel {
    position:fixed; top:0; right:0; bottom:0; z-index:100;
    width:55vw; min-width:360px; max-width:860px;
    display:flex; flex-direction:column;
    pointer-events:none;
    transform:translateX(100%);
    transition:transform 0.36s cubic-bezier(0.32,0.72,0,1);
}
#detailPanel.open { pointer-events:all; transform:translateX(0); }
.detail-box {
    background:#fff; width:100%; height:100%;
    display:flex; flex-direction:column;
    box-shadow:-12px 0 60px rgba(0,0,0,0.22); overflow:hidden;
}
.detail-header {
    background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);
    padding:20px 24px 18px;
    display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
}
.detail-header h2 { font-size:16px; font-weight:700; color:#fff; margin:0 0 3px; }
.detail-header p  { font-size:11px; color:rgba(255,255,255,0.6); margin:0; }
.detail-close {
    background:rgba(255,255,255,0.15); border:none; width:32px; height:32px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; color:rgba(255,255,255,0.8); transition:background 0.15s; flex-shrink:0;
}
.detail-close:hover { background:rgba(255,255,255,0.28); color:#fff; }
.detail-body {
    flex:1; overflow-y:auto; padding:0; background:#f8f9fa;
    scrollbar-width:thin; scrollbar-color:#d1d5db transparent;
}
.detail-body::-webkit-scrollbar { width:4px; }
.detail-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:99px; }
.detail-footer {
    flex-shrink:0; padding:14px 20px; border-top:1px solid #f3f4f6;
    background:#fff; display:flex; align-items:center; justify-content:space-between; gap:12px;
    flex-wrap:wrap;
}

.dp-card {
    background:#fff; border-radius:12px;
    margin:14px 16px; padding:18px 18px 16px;
    box-shadow:0 1px 4px rgba(0,0,0,0.06);
}
.dp-section-heading { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.dp-section-icon {
    width:30px; height:30px; border-radius:8px; background:#f0fdf4;
    display:flex; align-items:center; justify-content:center;
    color:#2d5a1b; flex-shrink:0;
}
.dp-section-title { font-size:13px; font-weight:700; color:#111827; margin:0; }
.dp-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 20px; }
.dp-grid.cols-1 { grid-template-columns:1fr; }
.dp-field label { display:block; font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:3px; }
.dp-field p { font-size:13px; color:#111827; font-weight:500; margin:0; }
.dp-field.span2 { grid-column:span 2; }

/* ── Leave Balance bar ── */
.leave-balance-bar { display:flex; flex-direction:column; gap:6px; }
.lbb-row { display:flex; flex-direction:column; gap:3px; }
.lbb-label { display:flex; justify-content:space-between; font-size:11px; font-weight:700; color:#374151; }
.lbb-label span:last-child { color:#6b7280; font-weight:500; }
.lbb-track { height:6px; background:#f3f4f6; border-radius:99px; overflow:hidden; }
.lbb-fill  { height:100%; border-radius:99px; transition:width 0.5s ease; }
.lbb-note  { font-size:10px; color:#f59e0b; font-weight:600; margin-top:2px; display:none; }
.lbb-note.show { display:block; }

/* ── Confirm Modal ── */
.confirm-modal {
    position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
    display:flex; align-items:center; justify-content:center;
    opacity:0; pointer-events:none; transition:opacity 0.2s;
    padding:16px;
}
.confirm-modal.show { opacity:1; pointer-events:all; }
.confirm-card {
    background:#fff; border-radius:20px; padding:28px;
    width:440px; max-width:100%;
    box-shadow:0 24px 64px rgba(0,0,0,0.2);
    transform:scale(0.93); transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
}
.confirm-modal.show .confirm-card { transform:scale(1); }

/* ── Toast ── */
#toast { position:fixed; bottom:20px; right:20px; z-index:300; min-width:240px; max-width:calc(100vw - 40px);
    background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 8px 32px rgba(0,0,0,0.15);
    display:flex; align-items:center; gap:12px;
    opacity:0; transform:translateY(16px); transition:all 0.3s; pointer-events:none; }
#toast.show { opacity:1; transform:translateY(0); pointer-events:all; }

.form-field { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; transition:border-color 0.15s; outline:none; }
.form-field:focus { background:#fff; border-color:#2d5a1b; }

.btn-cancel-modal { padding:8px 18px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#6b7280; background:#fff; cursor:pointer; transition:all 0.15s; }
.btn-cancel-modal:hover { border-color:#9ca3af; color:#374151; }
.btn-approve { padding:8px 22px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#15803d; cursor:pointer; transition:background 0.15s; }
.btn-approve:hover { background:#166534; }
.btn-reject  { padding:8px 22px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; transition:background 0.15s; }
.btn-reject:hover { background:#b91c1c; }
.btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
.btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }

#monetizeToolbar { display:none; }
#monetizeToolbar.show { display:flex; }

/* ════ CS FORM MODAL ════ */
.form-modal {
    position:fixed; inset:0; z-index:400;
    display:flex; align-items:center; justify-content:center;
    opacity:0; pointer-events:none;
    transition:opacity 0.25s ease;
}
.form-modal.show { opacity:1; pointer-events:all; }
.form-modal-backdrop {
    position:absolute; inset:0;
    background:rgba(0,0,0,0.65); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
}
.form-modal-shell {
    position:relative; z-index:1;
    width:min(96vw, 920px); height:90vh;
    background:#e8f0e8; border-radius:18px; overflow:hidden;
    display:flex; flex-direction:column;
    box-shadow:0 40px 100px rgba(0,0,0,0.45), 0 8px 32px rgba(0,0,0,0.25);
    transform:scale(0.94) translateY(12px);
    transition:transform 0.32s cubic-bezier(0.34,1.56,0.64,1);
}
.form-modal.show .form-modal-shell { transform:scale(1) translateY(0); }
.fm-topbar {
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    padding:10px 14px;
    background:linear-gradient(135deg, #081408 0%, #1a3a1a 60%, #2d5a1b 100%);
    flex-shrink:0; border-bottom:1px solid rgba(255,255,255,0.08);
}
.fm-topbar-left { display:flex; align-items:center; gap:8px; min-width:0; flex:1; overflow:hidden; }
.fm-topbar-icon { width:26px; height:26px; border-radius:6px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.fm-topbar-label { font-size:12px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fm-topbar-sub { font-size:10px; color:rgba(255,255,255,0.5); margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fm-topbar-actions { display:flex; gap:6px; flex-shrink:0; }
.fmbtn { padding:6px 12px; border-radius:7px; font-size:11px; font-weight:700; cursor:pointer; border:none; transition:all 0.16s; white-space:nowrap; }
.fmbtn-print { background:#fff; color:#1a3a1a; box-shadow:0 2px 8px rgba(0,0,0,0.18); }
.fmbtn-print:hover { background:#dcfce7; }
.fmbtn-close { background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.8); border:1.5px solid rgba(255,255,255,0.18); }
.fmbtn-close:hover { background:rgba(255,255,255,0.22); color:#fff; }

.fm-body { flex:1; overflow:hidden; position:relative; background:#e8f0e8; }
#formFrame { width:100%; height:100%; border:none; display:block; background:#fff; }

.fm-zoom-bar {
    display:none;
    align-items:center; justify-content:space-between; gap:8px;
    padding:6px 12px; background:#1a3a1a; flex-shrink:0;
}
.fm-zoom-bar span { font-size:11px; color:rgba(255,255,255,0.7); }
.fm-zoom-btns { display:flex; gap:4px; }
.fm-zoom-btn {
    width:28px; height:28px; border-radius:6px;
    background:rgba(255,255,255,0.15); border:none;
    color:#fff; font-size:16px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    transition:background 0.15s;
}
.fm-zoom-btn:hover { background:rgba(255,255,255,0.28); }
.fm-zoom-label { font-size:11px; color:#fff; font-weight:700; min-width:36px; text-align:center; padding-top:4px; }

.fm-loading { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; background:#e8f0e8; transition:opacity 0.3s; pointer-events:none; z-index:2; }
.fm-loading.hidden { opacity:0; }
.fm-spinner { width:36px; height:36px; border:3px solid rgba(45,90,27,0.2); border-top-color:#2d5a1b; border-radius:50%; animation:fmSpin 0.75s linear infinite; }
@keyframes fmSpin { to { transform:rotate(360deg); } }
.fm-loading-text { font-size:12px; color:#6b7280; font-weight:500; }

.leave-row[data-status="PENDING"] td:first-child     { border-left:3px solid #eab308; }
.monetize-row[data-status="PENDING"] td:nth-child(2) { border-left:3px solid #eab308; }

/* ════ PAGE LAYOUT ════ */
.app-page {
    display:flex; flex-direction:column;
    height:calc(100vh - 120px);
    overflow:hidden;
}
.app-card {
    flex:1; min-height:0;
    display:flex; flex-direction:column;
    background:#fff;
    border-radius:16px;
    border:1px solid #f3f4f6;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
    overflow:hidden;
}
.app-tab-bar {
    flex-shrink:0;
    display:flex; align-items:center;
    gap:24px; padding:0 20px;
    border-bottom:1px solid #f3f4f6;
    overflow-x:auto;
    scrollbar-width:none;
    -webkit-overflow-scrolling:touch;
}
.app-tab-bar::-webkit-scrollbar { display:none; }
.app-panel { flex:1; min-height:0; display:flex; flex-direction:column; }
.app-panel.hidden { display:none; }
.panel-toolbar { flex-shrink:0; }
.tsa {
    flex:1; min-height:0;
    overflow-y:auto; overflow-x:auto;
    scrollbar-width:thin; scrollbar-color:#e5e7eb transparent;
    -webkit-overflow-scrolling:touch;
}
.tsa::-webkit-scrollbar { width:5px; height:5px; }
.tsa::-webkit-scrollbar-track { background:transparent; }
.tsa::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:99px; }
.tsa::-webkit-scrollbar-thumb:hover { background:#d1d5db; }
.tsa .data-table thead { position:sticky; top:0; z-index:2; }

/* ── Stat card clickable ── */
.stat-card {
    transition: box-shadow 0.18s, transform 0.18s;
}
.stat-card.clickable {
    cursor: pointer;
}
.stat-card.clickable:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.13);
    transform: translateY(-1px);
}
.stat-card.clickable:active {
    transform: translateY(0);
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

/* ════ Stats Grid — Desktop ════ */
.stats-grid {
    display:grid; grid-template-columns:repeat(5,1fr);
    gap:12px; margin-bottom:14px; flex-shrink:0;
}
.stat-card {
    background:#fff; border-radius:14px; padding:14px 18px;
    box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f3f4f6;
    display:flex; align-items:center; gap:12px;
}
.stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-val  { font-size:22px; font-weight:800; color:#111827; line-height:1; }
.stat-lbl  { font-size:10px; color:#6b7280; font-weight:700; margin-top:2px; text-transform:uppercase; letter-spacing:0.05em; }

/* ════ Alert bar ════ */
.alert-bar {
    display:flex; align-items:center; gap:10px;
    margin-bottom:14px; padding:12px 16px;
    border-radius:14px; font-size:13px; font-weight:500;
    background:#fef9c3; border:1px solid #fde047; color:#854d0e;
    flex-shrink:0; flex-wrap:wrap;
}

/* ════ Filter row ════ */
.filter-row {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}
.filter-row .rel { position:relative; }
.filter-row .rel.rel-search { flex:1 1 160px; min-width:120px; }
.filter-row .rel:has(select) { flex:0 0 auto; }
.filter-row input,
.filter-row select {
    width:100%; appearance:none; -webkit-appearance:none;
    padding:7px 10px; font-size:12px;
    border:1px solid #e5e7eb; border-radius:8px;
    background:#fff; color:#374151; outline:none;
    transition:border-color 0.15s;
}
.filter-row select { padding-right:26px; }
.filter-row input:focus, .filter-row select:focus { border-color:#2d5a1b; }
.filter-row input { padding-left:32px; }
.filter-row .chevron { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }
.filter-row .search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }

.toolbar-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.toolbar-head-text p:first-child { font-weight:700; color:#1f2937; font-size:14px; margin:0 0 2px; }
.toolbar-head-text p:last-child  { font-size:11px; color:#9ca3af; margin:0; }

/* ── Action by chip ── */
.action-by-chip {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10px; color:#6b7280; background:#f3f4f6;
    border-radius:20px; padding:2px 8px; white-space:nowrap;
}
.action-by-chip svg { flex-shrink:0; }

/* ════════════════════════════════════════════════
   MOBILE  ≤ 767px
════════════════════════════════════════════════ */
@media (max-width: 767px) {
    .app-page {
        height: auto !important;
        overflow: visible !important;
        min-height: 0;
        padding-bottom: 80px;
    }
    .app-card {
        overflow: visible !important;
        flex: none;
        height: auto !important;
        min-height: 300px;
    }
    .app-panel {
        flex: none;
        min-height: 0;
        height: auto !important;
        overflow: visible !important;
    }
    .tsa {
        flex: none;
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    /* ── Stats grid: horizontal scroll row ── */
    .stats-grid {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 8px !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 4px !important;
        scrollbar-width: none !important;
        margin-bottom: 12px !important;
        margin-left: -2px;
        margin-right: -2px;
        padding-left: 2px;
        padding-right: 2px;
    }
    .stats-grid::-webkit-scrollbar { display: none; }

    .stat-card {
        flex: 0 0 88px !important;
        width: 88px !important;
        min-width: 88px !important;
        padding: 10px 8px 10px 10px !important;
        gap: 6px !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        border-radius: 12px !important;
        overflow: hidden !important;
    }
    .stat-icon {
        width: 28px !important;
        height: 28px !important;
        border-radius: 8px !important;
        flex-shrink: 0 !important;
    }
    .stat-icon svg {
        width: 13px !important;
        height: 13px !important;
    }
    .stat-val {
        font-size: 20px !important;
        font-weight: 800 !important;
        line-height: 1.1 !important;
        color: #111827 !important;
    }
    .stat-lbl {
        font-size: 9px !important;
        letter-spacing: 0 !important;
        line-height: 1.3 !important;
        white-space: normal !important;
        word-break: break-word !important;
        margin-top: 1px !important;
    }

    /* ══ FILTER ROW FIX — single line, no wrapping, no floating icon ══ */
    .filter-row {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
    }

    /* search wrapper fills remaining space */
    .filter-row .rel.rel-search {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        max-width: none !important;
        position: relative !important;
    }

    /* search input: full width inside its wrapper, icon inside */
    .filter-row .rel.rel-search input {
        width: 100% !important;
        font-size: 12px !important;
        padding: 8px 8px 8px 30px !important;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        background: #fff !important;
        color: #374151 !important;
        box-sizing: border-box !important;
        display: block !important;
    }

    /* search icon: absolutely positioned inside rel-search */
    .filter-row .rel.rel-search .search-icon {
        position: absolute !important;
        left: 9px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
        color: #9ca3af !important;
        z-index: 1 !important;
    }

    /* select wrappers: shrink, fixed compact width */
    .filter-row .rel:has(select) {
        flex: 0 0 auto !important;
        min-width: 0 !important;
        position: relative !important;
    }

    .filter-row select {
        appearance: none !important;
        -webkit-appearance: none !important;
        font-size: 11px !important;
        padding: 8px 22px 8px 8px !important;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        background: #fff !important;
        color: #374151 !important;
        width: auto !important;
        min-width: 80px !important;
        max-width: 108px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    .filter-row .chevron {
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
        color: #9ca3af !important;
    }

    /* ── Table mobile cards ── */
    .data-table thead { display:none; }
    .data-table tbody { display:flex; flex-direction:column; gap:0; }
    .data-table tr { display:flex; flex-direction:column; padding:14px 16px; border-bottom:1px solid #f3f4f6; gap:6px; position:relative; }
    .data-table td { padding:0; border-bottom:none; font-size:13px; display:flex; align-items:flex-start; gap:8px; }
    .data-table td[data-label]::before { content: attr(data-label); font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; min-width:80px; flex-shrink:0; padding-top:1px; }
    .data-table td.mob-hide { display:none; }
    .data-table td.mob-title { font-size:14px; font-weight:700; color:#111827; padding-bottom:6px; border-bottom:1px solid #f3f4f6; margin-bottom:4px; }
    .data-table td.mob-title::before { display:none; }
    .data-table td.mob-action { position:absolute; top:12px; right:12px; display:flex !important; align-items:center; justify-content:flex-end; }
    .data-table td.mob-action::before { display:none; }
    .data-table td.mob-status { margin-top:4px; flex-wrap:wrap; }
    .data-table td.mob-status::before { content:'Status'; font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; min-width:80px; flex-shrink:0; padding-top:3px; }

    #detailPanel { width:100%; min-width:0; top:0; right:0; left:0; bottom:0; transform:translateY(100%); }
    #detailPanel.open { transform:translateY(0); }
    .dp-grid { grid-template-columns:1fr; }
    .dp-field.span2 { grid-column:span 1; }
    .detail-footer { flex-direction:column; gap:10px; }
    .detail-footer > button, .detail-footer > div { width:100%; }
    #dpActionBtns { display:flex; gap:8px; width:100%; }
    #dpActionBtns .btn-reject, #dpActionBtns .btn-approve { flex:1; text-align:center; }

    .panel-toolbar .flex { flex-direction:column; align-items:stretch !important; gap:10px !important; }
    .panel-toolbar .flex > div.flex { flex-direction:row; flex-wrap:wrap; }
    .panel-toolbar input[type="text"], .panel-toolbar select { width:100% !important; }
    .panel-toolbar .relative { width:100%; }
    .tab-btn { font-size:12px; padding:10px 2px; }
    .breadcrumb { font-size:12px; margin-bottom:12px; }
    .alert-bar { font-size:12px; }
    .confirm-card { padding:20px 18px; }
    .form-modal { padding:0; }
    .form-modal-shell { width:100vw; height:100vh; max-width:none; border-radius:0; transform:translateY(100%) !important; transition:transform 0.32s cubic-bezier(0.32,0.72,0,1) !important; }
    .form-modal.show .form-modal-shell { transform:translateY(0) !important; }
    .fm-zoom-bar { display:flex; }
    .fm-body { overflow:auto !important; -webkit-overflow-scrolling:touch; }
    #formFrame { width:820px !important; height:1200px !important; transform-origin:top left; transform:scale(var(--fm-scale, 0.44)); margin-bottom:calc(1200px * (var(--fm-scale, 0.44) - 1)); margin-right:calc(820px * (var(--fm-scale, 0.44) - 1)); }
    #toast { left:16px; right:16px; bottom:16px; min-width:0; width:auto; }
    #monetizeToolbar.show { flex-wrap:wrap; gap:10px; }
    .data-table td.mob-check { position:absolute; top:14px; left:14px; display:flex !important; }
    .data-table td.mob-check::before { display:none; }
    .monetize-row { padding-left:44px !important; }
}

/* ════ TABLET ════ */
@media (min-width:768px) and (max-width:1023px) {
    .app-page { height:calc(100vh - 100px); }
    #detailPanel { width:80vw; min-width:360px; }
    .panel-toolbar .flex.sm\:flex-row { flex-wrap:wrap; gap:10px; }
    .app-tab-bar { gap:16px; padding:0 16px; }
    .dp-grid { grid-template-columns:1fr 1fr; }
    .stats-grid { grid-template-columns:repeat(3,1fr); gap:10px; }
}

/* ════ SMALL DESKTOP ════ */
@media (min-width:1024px) and (max-width:1279px) {
    #detailPanel { width:60vw; }
}

/* ════ LARGE DESKTOP ════ */
@media (min-width:1280px) {
    .app-page { height:calc(100vh - 120px); }
    #detailPanel { width:55vw; }
}
</style>

{{-- ════ PAGE WRAPPER ════ --}}
<div class="app-page">

{{-- Breadcrumb --}}
<div class="breadcrumb" style="flex-shrink:0;">
    <a href="{{ route('dashboard') }}">Application</a>
    <span class="sep">›</span>
    <span class="current">Leave Application</span>
</div>

{{-- ════ STATS GRID — shared across all tabs ════ --}}
<div class="stats-grid" style="flex-shrink:0;">
    <div class="stat-card clickable" onclick="goToStat('leave','PENDING')">
        <div class="stat-icon" style="background:#fef9c3;">
            <svg style="width:18px;height:18px;color:#854d0e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-val" id="leaveStat-PENDING">{{ $leaveApps->whereNotIn('status',['APPROVED','REJECTED','CANCELLED'])->where('status','PENDING')->count() }}</div>
            <div class="stat-lbl">Pending</div>
        </div>
    </div>
    <div class="stat-card clickable" onclick="goToStat('leave','RECEIVED')">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg style="width:18px;height:18px;color:#1e40af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-val" id="leaveStat-RECEIVED">{{ $leaveApps->where('status','RECEIVED')->count() }}</div>
            <div class="stat-lbl">Received</div>
        </div>
    </div>
    <div class="stat-card clickable" onclick="goToStat('history','APPROVED')">
        <div class="stat-icon" style="background:#dcfce7;">
            <svg style="width:18px;height:18px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div class="stat-val" id="leaveStat-APPROVED">{{ $leaveApps->where('status','APPROVED')->count() }}</div>
            <div class="stat-lbl">Approved</div>
        </div>
    </div>
    <div class="stat-card clickable" onclick="goToStat('history','REJECTED')">
        <div class="stat-icon" style="background:#fee2e2;">
            <svg style="width:18px;height:18px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div>
            <div class="stat-val" id="leaveStat-REJECTED">{{ $leaveApps->where('status','REJECTED')->count() }}</div>
            <div class="stat-lbl">Rejected</div>
        </div>
    </div>
    <div class="stat-card clickable" onclick="goToStat('monetize','PENDING')">
        <div class="stat-icon" style="background:#ede9fe;">
            <svg style="width:18px;height:18px;color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-val" id="monetizeStat-PENDING">{{ $monetizationApps->where('status','PENDING')->count() }}</div>
            <div class="stat-lbl">Monetize Pending</div>
        </div>
    </div>
</div>

{{-- Pending alert bar --}}
@if($pendingCount > 0)
<div class="alert-bar" style="flex-shrink:0;">
    <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
        <strong>{{ $pendingCount }} leave application{{ $pendingCount > 1 ? 's' : '' }}</strong> awaiting your action.
        @if($monetizePending > 0)
            &nbsp;·&nbsp; <strong>{{ $monetizePending }} monetization request{{ $monetizePending > 1 ? 's' : '' }}</strong> pending.
        @endif
    </span>
</div>
@endif

{{-- ── MAIN CARD ── --}}
<div class="app-card">

    {{-- Tab Bar --}}
    <div class="app-tab-bar">
        <button class="tab-btn active" id="tabLeave"    onclick="switchTab('leave')">Leave Applications</button>
        <button class="tab-btn"        id="tabMonetize" onclick="switchTab('monetize')">Monetization</button>
        <button class="tab-btn"        id="tabHistory"  onclick="switchTab('history')">History</button>
    </div>

    {{-- ═══ LEAVE APPLICATIONS PANEL ═══ --}}
    <div id="panelLeave" class="app-panel">

        <div class="panel-toolbar" style="padding:14px 16px; border-bottom:1px solid #f9fafb;">
            <div class="toolbar-head" style="margin-bottom:12px;">
                <div class="toolbar-head-text">
                    <p>Leave Application List</p>
                    <p>Click a row to view details · Click a status pill to change it</p>
                </div>
            </div>
            <div class="filter-row">
                <div class="rel rel-search">
                    <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search name / ID…" id="searchLeave" oninput="filterTable('leave')">
                </div>
                <div class="rel">
                    <select id="filterLeaveStatus" onchange="filterTable('leave')">
                        <option value="">All Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="RECEIVED">Received</option>
                        <option value="ON-PROCESS">On-Process</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="rel">
                    <select id="filterLeaveMonth" onchange="filterTable('leave')">
                        <option value="">All Months</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ now()->month == $m ? 'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="tsa">
            <table class="data-table" id="leaveTable">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Application Date</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Leave Type</th>
                        <th>Status</th>
                        <th class="text-right" style="padding-right:18px;">Action</th>
                    </tr>
                </thead>
                <tbody id="leaveTbody">
                    @php $activeApps = $leaveApps->whereNotIn('status', ['APPROVED','REJECTED','CANCELLED']); @endphp
                    @forelse($activeApps as $app)
                    <tr class="leave-row"
                        data-status="{{ $app->status }}"
                        data-month="{{ $app->application_date ? $app->application_date->format('m') : '' }}"
                        data-appdate="{{ $app->application_date ? $app->application_date->toDateString() : '' }}"
                        data-search="{{ strtolower($app->employee->last_name ?? '') }} {{ strtolower($app->employee->first_name ?? '') }} {{ $app->employee_id }}"
                        data-leave-id="{{ $app->leave_id }}"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)">

                        <td data-label="Emp. ID" class="font-bold font-mono text-sm">{{ $app->employee->formatted_employee_id ?? $app->employee_id }}</td>
                        <td data-label="Name" class="font-semibold mob-title">
                            {{ $app->employee->last_name ?? '—' }}, {{ $app->employee->first_name ?? '' }}
                        </td>
                        <td data-label="App. Date" class="text-gray-500">{{ $app->application_date ? $app->application_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Duration" class="text-gray-500">{{ $app->no_of_days }} day(s)</td>
                        <td data-label="Start" class="text-gray-500">{{ $app->start_date ? $app->start_date->format('M d, Y') : '—' }}</td>
                        <td data-label="End" class="text-gray-500">{{ $app->end_date ? $app->end_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Type" class="text-xs text-gray-500">{{ $app->leaveType->type_name ?? '—' }}</td>

                        <td onclick="event.stopPropagation()" class="mob-status">
                            <div class="status-changer" id="sc_{{ $app->leave_id }}">
                                <button class="status-pill pill-{{ $app->status }}"
                                        onclick="toggleStatusDropdown({{ $app->leave_id }}, event)">
                                    {{ ucfirst(strtolower(str_replace('-',' ',$app->status))) }}
                                    <svg class="pill-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="status-saving" id="scspin_{{ $app->leave_id }}">
                                    <svg class="animate-spin" style="width:16px;height:16px;color:#9ca3af;" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </div>
                                <div class="status-dropdown" id="scd_{{ $app->leave_id }}">
                                    @foreach(['PENDING','RECEIVED','ON-PROCESS','APPROVED','REJECTED'] as $s)
                                    <button class="status-option {{ $app->status === $s ? 'current' : '' }}"
                                            onclick="changeStatus({{ $app->leave_id }}, '{{ $s }}', event)">
                                        <span class="opt-dot dot-{{ $s }}"></span>
                                        {{ ucfirst(strtolower(str_replace('-', ' ', $s))) }}
                                        @if($app->status === $s)
                                            <svg class="w-3.5 h-3.5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </td>

                        <td class="mob-action" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="openDetailPanel({{ $app->leave_id }}, event)">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    <button class="action-item" onclick="viewPdf({{ $app->leave_id }})">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">No active leave applications.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- end #panelLeave --}}

    {{-- ═══ MONETIZATION PANEL ═══ --}}
    <div id="panelMonetize" class="app-panel hidden">

        <div class="panel-toolbar">
            <div id="monetizeToolbar" class="items-center gap-3 flex-wrap" style="padding:10px 16px; border-bottom:1px solid #fef9c3; background:#fffbeb;">
                <span style="font-size:12px;font-weight:600;color:#92400e;" id="checkedCountLabel">0 selected</span>
                <button onclick="generateLetter()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:12px;font-weight:700;color:#fff;background:#1a3a1a;border:none;border-radius:8px;cursor:pointer;transition:background 0.15s;"
                        onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generate Letter / PDF
                </button>
            </div>

            <div style="padding:14px 16px; border-bottom:1px solid #f9fafb;">
                <div class="toolbar-head" style="margin-bottom:12px;">
                    <div class="toolbar-head-text">
                        <p>Monetization Request List</p>
                        <p>Check rows to generate a consolidated letter · Click a row or ··· to view details</p>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="rel rel-search">
                        <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        <input type="text" placeholder="Search…" id="searchMonetize" oninput="filterTable('monetize')">
                    </div>
                    <div class="rel">
                        <select id="filterMonetizeStatus" onchange="filterTable('monetize')">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="RECEIVED">Received</option>
                            <option value="ON-PROCESS">On-Process</option>
                        </select>
                        <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="tsa">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:36px;"><input type="checkbox" id="chkAllMonetize" onchange="toggleAllMonetize(this)" style="border-radius:4px;"></th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Application Date</th>
                        <th>Leave Type</th>
                        <th>Days</th>
                        <th>Est. Amount</th>
                        <th>Status</th>
                        <th style="text-align:right;padding-right:18px;">Action</th>
                    </tr>
                </thead>
                <tbody id="monetizeTbody">
                    @php $activeMonetize = $monetizationApps->whereNotIn('status', ['APPROVED','REJECTED','CANCELLED']); @endphp
                    @forelse($activeMonetize as $app)
                    @php
                        $mAmount = ($app->employee->salary ?? 0) * $app->no_of_days * 0.0481927;
                    @endphp
                    <tr class="monetize-row"
                        data-status="{{ $app->status }}"
                        data-search="{{ strtolower($app->employee->last_name ?? '') }} {{ strtolower($app->employee->first_name ?? '') }} {{ strtolower($app->employee->formatted_employee_id ?? $app->employee_id) }} {{ strtolower($app->leaveType->type_name ?? '') }} {{ $app->application_date ? strtolower($app->application_date->format('M d Y')) : '' }} {{ $app->no_of_days }}"
                        data-leave-id="{{ $app->leave_id }}"
                        data-name="{{ $app->employee->last_name ?? '' }}, {{ $app->employee->first_name ?? '' }}"
                        data-emp-id="{{ $app->employee->formatted_employee_id ?? $app->employee_id }}"
                        data-days="{{ $app->no_of_days }}"
                        data-amount="{{ number_format($mAmount,2) }}"
                        data-leave-type="{{ $app->leaveType->type_name ?? '' }}"
                        data-appdate="{{ $app->application_date ? $app->application_date->toDateString() : '' }}"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)"
                        style="cursor:pointer;">

                        <td class="mob-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="monetize-chk" value="{{ $app->leave_id }}" onchange="onMonetizeCheck()" style="border-radius:4px;">
                        </td>
                        <td data-label="Emp. ID" class="font-bold font-mono text-sm">{{ $app->employee->formatted_employee_id ?? $app->employee_id }}</td>
                        <td data-label="Name" class="font-semibold mob-title">
                            {{ $app->employee->last_name ?? '—' }}, {{ $app->employee->first_name ?? '' }}
                        </td>
                        <td data-label="App. Date" class="text-gray-500">{{ $app->application_date ? $app->application_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Leave Type" class="text-xs text-gray-500">{{ $app->leaveType->type_name ?? '—' }}</td>
                        <td data-label="Days" class="text-gray-500">{{ $app->no_of_days }}</td>
                        <td data-label="Est. Amount" class="font-medium" style="color:#374151;">₱{{ number_format($mAmount, 2) }}</td>
                        <td onclick="event.stopPropagation()" class="mob-status">
                            <div class="status-changer" id="sc_m_{{ $app->leave_id }}">
                                <button class="status-pill pill-{{ $app->status }}"
                                        onclick="toggleStatusDropdown('m_{{ $app->leave_id }}', event)">
                                    {{ ucfirst(strtolower(str_replace('-', ' ', $app->status))) }}
                                    <svg class="pill-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="status-saving" id="scspin_m_{{ $app->leave_id }}"></div>
                                <div class="status-dropdown" id="scd_m_{{ $app->leave_id }}">
                                    @foreach(['PENDING','RECEIVED','ON-PROCESS','APPROVED','REJECTED'] as $s)
                                    <button class="status-option {{ $app->status === $s ? 'current' : '' }}"
                                            onclick="changeStatus({{ $app->leave_id }}, '{{ $s }}', event, 'm_{{ $app->leave_id }}')">
                                        <span class="opt-dot dot-{{ $s }}"></span>
                                        {{ ucfirst(strtolower(str_replace('-', ' ', $s))) }}
                                        @if($app->status === $s)
                                            <svg style="width:14px;height:14px;margin-left:auto;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td class="mob-action" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="openDetailPanel({{ $app->leave_id }}, event)">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    <button class="action-item" onclick="viewPdf({{ $app->leave_id }})">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">No active monetization requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- end #panelMonetize --}}

    {{-- ═══ HISTORY PANEL ═══ --}}
    <div id="panelHistory" class="app-panel hidden">

        <div class="panel-toolbar" style="padding:14px 16px;border-bottom:1px solid #f9fafb;">
            <div class="toolbar-head" style="margin-bottom:10px;">
                <div class="toolbar-head-text">
                    <p>Application History</p>
                    <p>All processed applications (approved, rejected &amp; cancelled)</p>
                </div>
            </div>
            <div class="filter-row">
                <div class="rel rel-search">
                    <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search name, ID, type, date…" id="searchHistory" oninput="filterTable('history')">
                </div>
                <div class="rel">
                    <select id="filterHistoryStatus" onchange="filterTable('history')">
                        <option value="">All Status</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="rel">
                    <select id="filterHistoryType" onchange="filterTable('history')">
                        <option value="">All Types</option>
                        <option value="leave">Leave</option>
                        <option value="monetization">Monetization</option>
                    </select>
                    <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="tsa">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Leave Type</th>
                        <th>Application Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Action By</th>
                        <th style="text-align:right;padding-right:18px;">Action</th>
                    </tr>
                </thead>
                <tbody id="historyTbody">
                    @php $processed = $leaveApps->whereIn('status', ['APPROVED','REJECTED','CANCELLED'])->values(); @endphp
                    @forelse($processed as $app)
                    <tr class="history-row"
                        data-status="{{ $app->status }}"
                        data-record-type="leave"
                        data-search="{{ strtolower(implode(' ', array_filter([
                            $app->employee->last_name ?? '',
                            $app->employee->first_name ?? '',
                            $app->employee->middle_name ?? '',
                            $app->employee->formatted_employee_id ?? (string)($app->employee_id ?? ''),
                            $app->leaveType->type_name ?? '',
                            strtolower($app->status ?? ''),
                            $app->application_date ? $app->application_date->format('M d Y') : '',
                            $app->start_date ? $app->start_date->format('M d Y') : '',
                            $app->end_date ? $app->end_date->format('M d Y') : '',
                            (string)($app->no_of_days ?? ''),
                            $app->actioned_by_name ?? '',
                            'leave',
                        ]))) }}"
                        style="cursor:pointer;"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)">
                        <td data-label="Emp. ID" class="font-bold font-mono text-sm">{{ $app->employee->formatted_employee_id ?? $app->employee_id }}</td>
                        <td data-label="Name" class="font-semibold mob-title">{{ $app->employee->last_name ?? '—' }}, {{ $app->employee->first_name ?? '' }}</td>
                        <td data-label="Type">
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#f0fdf4;color:#166534;">
                                Leave
                            </span>
                        </td>
                        <td data-label="Leave Type" class="text-xs text-gray-500">{{ $app->leaveType->type_name ?? '—' }}</td>
                        <td data-label="App. Date" class="text-gray-500">{{ $app->application_date ? $app->application_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Start" class="text-gray-500">{{ $app->start_date ? $app->start_date->format('M d, Y') : '—' }}</td>
                        <td data-label="End" class="text-gray-500">{{ $app->end_date ? $app->end_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Days" class="text-gray-500">{{ $app->no_of_days }}</td>
                        <td data-label="Status"><span class="badge badge-{{ $app->status }}">{{ ucfirst(strtolower($app->status)) }}</span></td>
                        <td data-label="Action By" onclick="event.stopPropagation()">
                            @if($app->actioned_by_name)
                            <span class="action-by-chip">
                                <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $app->actioned_by_name }}
                            </span>
                            @else
                            <span style="color:#d1d5db;font-size:11px;">—</span>
                            @endif
                        </td>
                        <td class="mob-action" onclick="event.stopPropagation()" style="text-align:right;padding-right:14px;">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="openDetailPanel({{ $app->leave_id }}, event)">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    <button class="action-item" onclick="viewPdf({{ $app->leave_id }})">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse

                    @php $processedMonetize = $monetizationApps->whereIn('status', ['APPROVED','REJECTED','CANCELLED'])->values(); @endphp
                    @forelse($processedMonetize as $app)
                    @php $mAmount = ($app->employee->salary ?? 0) * $app->no_of_days * 0.0481927; @endphp
                    <tr class="history-row"
                        data-status="{{ $app->status }}"
                        data-record-type="monetization"
                        data-search="{{ strtolower(implode(' ', array_filter([
                            $app->employee->last_name ?? '',
                            $app->employee->first_name ?? '',
                            $app->employee->middle_name ?? '',
                            $app->employee->formatted_employee_id ?? (string)($app->employee_id ?? ''),
                            $app->leaveType->type_name ?? '',
                            strtolower($app->status ?? ''),
                            $app->application_date ? $app->application_date->format('M d Y') : '',
                            $app->start_date ? $app->start_date->format('M d Y') : '',
                            $app->end_date ? $app->end_date->format('M d Y') : '',
                            (string)($app->no_of_days ?? ''),
                            number_format($mAmount, 2),
                            $app->actioned_by_name ?? '',
                            'monetization',
                        ]))) }}"
                        style="cursor:pointer;"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)">
                        <td data-label="Emp. ID" class="font-bold font-mono text-sm">{{ $app->employee->formatted_employee_id ?? $app->employee_id }}</td>
                        <td data-label="Name" class="font-semibold mob-title">{{ $app->employee->last_name ?? '—' }}, {{ $app->employee->first_name ?? '' }}</td>
                        <td data-label="Type">
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#ede9fe;color:#5b21b6;">
                                Monetization
                            </span>
                        </td>
                        <td data-label="Leave Type" class="text-xs text-gray-500">{{ $app->leaveType->type_name ?? '—' }}</td>
                        <td data-label="App. Date" class="text-gray-500">{{ $app->application_date ? $app->application_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Start" class="text-gray-500">{{ $app->start_date ? $app->start_date->format('M d, Y') : '—' }}</td>
                        <td data-label="End" class="text-gray-500">{{ $app->end_date ? $app->end_date->format('M d, Y') : '—' }}</td>
                        <td data-label="Days" class="text-gray-500">{{ $app->no_of_days }}</td>
                        <td data-label="Status"><span class="badge badge-{{ $app->status }}">{{ ucfirst(strtolower($app->status)) }}</span></td>
                        <td data-label="Action By" onclick="event.stopPropagation()">
                            @if($app->actioned_by_name)
                            <span class="action-by-chip">
                                <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $app->actioned_by_name }}
                            </span>
                            @else
                            <span style="color:#d1d5db;font-size:11px;">—</span>
                            @endif
                        </td>
                        <td class="mob-action" onclick="event.stopPropagation()" style="text-align:right;padding-right:14px;">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="openDetailPanel({{ $app->leave_id }}, event)">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    <button class="action-item" onclick="viewPdf({{ $app->leave_id }})">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse

                    @if($processed->isEmpty() && $processedMonetize->isEmpty())
                    <tr><td colspan="11" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">No processed applications yet.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>{{-- end #panelHistory --}}

</div>{{-- end .app-card --}}
</div>{{-- end .app-page --}}

{{-- ════ OVERLAY ════ --}}
<div id="overlay" onclick="closeDetailPanel()"></div>

{{-- ════ DETAIL PANEL ════ --}}
<div id="detailPanel">
    <div class="detail-box">
        <div class="detail-header">
            <div style="min-width:0;">
                <h2 id="dpTitle">Leave Application Details</h2>
                <p id="dpSubtitle" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Loading…</p>
            </div>
            <button class="detail-close" onclick="closeDetailPanel()">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="detail-body" id="dpBody"></div>
        <div class="detail-footer" id="dpFooter">
            <button class="btn-pdf" onclick="viewPdfFromPanel()">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                View PDF
            </button>
            <div style="display:flex;gap:8px;" id="dpActionBtns"></div>
        </div>
    </div>
</div>

{{-- ════ CONFIRM MODAL ════ --}}
<div id="confirmModal" class="confirm-modal">
    <div class="confirm-card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
            <div id="confirmIconWrap" style="width:48px;height:48px;border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
            <div>
                <h3 id="confirmTitle" style="font-weight:700;color:#1f2937;font-size:15px;margin:0 0 2px;"></h3>
                <p id="confirmDesc" style="font-size:12px;color:#6b7280;margin:0;"></p>
            </div>
        </div>
        <div id="rejectReasonWrap" class="hidden" style="margin-bottom:18px;">
            <label style="display:block;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">
                Reason for Rejection <span style="color:#9ca3af;">(optional)</span>
            </label>
            <textarea id="rejectReason" rows="3" class="form-field" placeholder="Enter reason for disapproval…"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
            <button onclick="closeConfirmModal()" class="btn-cancel-modal">Cancel</button>
            <button id="confirmOkBtn" onclick="executeConfirmAction()" class="btn-approve">Confirm</button>
        </div>
    </div>
</div>

{{-- ════ TOAST ════ --}}
<div id="toast">
    <div id="toastIcon" style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
    <div>
        <p style="font-size:13px;font-weight:700;color:#1f2937;margin:0;" id="toastTitle"></p>
        <p style="font-size:11px;color:#6b7280;margin:2px 0 0;" id="toastMsg"></p>
    </div>
</div>

{{-- ════ CS FORM MODAL ════ --}}
<div id="formModal" class="form-modal" role="dialog" aria-modal="true">
    <div class="form-modal-backdrop" onclick="closeFormModal()"></div>
    <div class="form-modal-shell">
        <div class="fm-topbar">
            <div class="fm-topbar-left">
                <div class="fm-topbar-icon">
                    <svg style="width:15px;height:15px;color:rgba(255,255,255,0.85);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="fm-topbar-label" id="fmTopbarLabel">Application for Leave — CS Form No. 6</div>
                    <div class="fm-topbar-sub" id="fmTopbarSub">Civil Service Form · Revised 2020</div>
                </div>
            </div>
            <div class="fm-topbar-actions">
                <button class="fmbtn fmbtn-print" onclick="printFormModal()">🖨 Print</button>
                <button class="fmbtn fmbtn-close" onclick="closeFormModal()">✕</button>
            </div>
        </div>
        <div class="fm-zoom-bar" id="fmZoomBar">
            <span>Pinch or use buttons to zoom</span>
            <div class="fm-zoom-btns">
                <button class="fm-zoom-btn" onclick="fmZoom(-0.05)" title="Zoom out">−</button>
                <span class="fm-zoom-label" id="fmZoomLabel">44%</span>
                <button class="fm-zoom-btn" onclick="fmZoom(+0.05)" title="Zoom in">+</button>
                <button class="fm-zoom-btn" onclick="fmZoomReset()" title="Reset zoom" style="font-size:13px;margin-left:2px;">⟳</button>
            </div>
        </div>
        <div class="fm-body" id="fmBody">
            <div class="fm-loading" id="fmLoading">
                <div class="fm-spinner"></div>
                <span class="fm-loading-text">Loading form…</span>
            </div>
            <iframe id="formFrame" src="" title="CS Form No. 6"
                    onload="document.getElementById('fmLoading').classList.add('hidden')"></iframe>
        </div>
    </div>
</div>

{{-- ════ JS DATA ════ --}}
<script>
const LEAVE_DATA = {!! json_encode(
    $leaveApps->merge($monetizationApps)->keyBy('leave_id')->map(function($a) {
        $emp = $a->employee; // may be null
        return [
            'leave_id'         => $a->leave_id,
            'employee_id'      => $emp ? ($emp->formatted_employee_id ?? $a->employee_id) : $a->employee_id,
            'first_name'       => $emp->first_name ?? '',
            'last_name'        => $emp->last_name ?? '',
            'middle_name'      => $emp->middle_name ?? '',
            'extension_name'   => $emp->extension_name ?? '',
            'position'         => $emp->position->position_name ?? '—',
            'department'       => $emp->department->department_name ?? '—',
            'salary'           => $emp->salary ?? 0,
            'hire_date'        => ($emp && $emp->hire_date) ? \Carbon\Carbon::parse($emp->hire_date)->format('M d, Y') : '—',
            'contact_number'   => $emp->contact_number ?? '—',
            'address'          => $emp->address ?? '—',
            'leave_type'       => $a->leaveType->type_name ?? '—',
            'application_date' => $a->application_date ? $a->application_date->format('M d, Y') : '—',
            'start_date'       => $a->start_date ? $a->start_date->format('M d, Y') : '—',
            'end_date'         => $a->end_date ? $a->end_date->format('M d, Y') : '—',
            'no_of_days'       => $a->no_of_days,
            'details_of_leave' => $a->details_of_leave ?? '—',
            'commutation'      => $a->commutation ?? '—',
            'reason'           => $a->reason ?? '',
            'status'           => $a->status,
            'actioned_by_name' => $a->actioned_by_name ?? null,
            'vacation_balance' => $emp->vacation_leave_balance ?? 15,
            'sick_balance'     => $emp->sick_leave_balance ?? 15,
            'vacation_max'     => $emp->vacation_leave_max ?? 15,
            'sick_max'         => $emp->sick_leave_max ?? 15,
        ];
    })
) !!};

const CSRF        = "{{ csrf_token() }}";
const APPROVE_URL = "{{ url('admin/leave') }}";
const PDF_URL     = "{{ url('admin/leave') }}";
</script>

<script>
let activePanelLeaveId   = null;
let pendingConfirmAction = null;

/* ══ STAT CARD NAVIGATION ══ */
function goToStat(tab, status) {
    switchTab(tab);
    if (tab === 'leave') {
        document.getElementById('filterLeaveStatus').value = status;
        filterTable('leave');
    } else if (tab === 'monetize') {
        document.getElementById('filterMonetizeStatus').value = status;
        filterTable('monetize');
    } else if (tab === 'history') {
        document.getElementById('filterHistoryStatus').value = status;
        filterTable('history');
    }
    const card = document.querySelector('.app-card');
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ══ TABS ══ */
function switchTab(tab) {
    ['leave','monetize','history'].forEach(t => {
        const key = t.charAt(0).toUpperCase() + t.slice(1);
        document.getElementById('tab'   + key).classList.toggle('active', t === tab);
        document.getElementById('panel' + key).classList.toggle('hidden', t !== tab);
    });
    if (tab === 'history') filterTable('history');
}

/* ══ SORT ══ */
const STATUS_ORDER = { PENDING:0, RECEIVED:1, 'ON-PROCESS':2, APPROVED:3, REJECTED:4, CANCELLED:5 };
function _sortTbody(tbodyId, rowClass) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = [...tbody.querySelectorAll('tr.' + rowClass)];
    if (!rows.length) return;
    rows.sort((a, b) => {
        const sa = STATUS_ORDER[a.dataset.status] ?? 99;
        const sb = STATUS_ORDER[b.dataset.status] ?? 99;
        if (sa !== sb) return sa - sb;
        const da = a.dataset.appdate ? new Date(a.dataset.appdate) : new Date(0);
        const db = b.dataset.appdate ? new Date(b.dataset.appdate) : new Date(0);
        return db - da;
    });
    rows.forEach(r => tbody.appendChild(r));
}
function sortLeaveTable()    { _sortTbody('leaveTbody',    'leave-row');    }
function sortMonetizeTable() { _sortTbody('monetizeTbody', 'monetize-row'); }

/* ══ LEAVE BALANCE CROSS-DEDUCTION LOGIC ══ */
function computeBalanceAfterLeave(vacBal, sickBal, vacMax, sickMax, days, leaveType) {
    let vac  = parseFloat(vacBal)  || 0;
    let sick = parseFloat(sickBal) || 0;
    const d  = parseFloat(days)    || 0;
    let crossNote = '';
    const isVacation = /vacation/i.test(leaveType);
    const isSick     = /sick/i.test(leaveType);
    if (isVacation) {
        if (vac >= d) { vac -= d; }
        else { const ov = d - vac; crossNote = `${ov} day(s) charged to Sick Leave (Vacation balance exhausted)`; vac = 0; sick = Math.max(0, sick - ov); }
    } else if (isSick) {
        if (sick >= d) { sick -= d; }
        else { const ov = d - sick; crossNote = `${ov} day(s) charged to Vacation Leave (Sick balance exhausted)`; sick = 0; vac = Math.max(0, vac - ov); }
    }
    return { vacBal: vac, sickBal: sick, crossNote };
}

/* ══ CS FORM MODAL ══ */
const FM_FORM_WIDTH = 820;
let fmCurrentScale  = 1;
let fmDefaultScale  = 1;

function fmApplyScale(s) {
    fmCurrentScale = Math.min(2, Math.max(0.2, s));
    const frame = document.getElementById('formFrame');
    const shell = document.querySelector('.form-modal-shell');
    if (!frame || !shell) return;
    shell.style.setProperty('--fm-scale', fmCurrentScale);
    frame.style.transform    = `scale(${fmCurrentScale})`;
    frame.style.marginBottom = `calc(1200px * (${fmCurrentScale} - 1))`;
    frame.style.marginRight  = `calc(${FM_FORM_WIDTH}px * (${fmCurrentScale} - 1))`;
    const lbl = document.getElementById('fmZoomLabel');
    if (lbl) lbl.textContent = Math.round(fmCurrentScale * 100) + '%';
}
function fmZoom(delta) { fmApplyScale(fmCurrentScale + delta); }
function fmZoomReset() { fmApplyScale(fmDefaultScale); }
function isMobile() { return window.innerWidth <= 767; }

function viewPdf(id) {
    const d = LEAVE_DATA[id];
    if (d) {
        document.getElementById('fmTopbarLabel').textContent = `${d.last_name}, ${d.first_name}  ·  Leave #${id}`;
        document.getElementById('fmTopbarSub').textContent   = `${d.leave_type}  ·  ${d.no_of_days} day(s)  ·  ${d.status}`;
    }
    document.getElementById('fmLoading').classList.remove('hidden');
    document.getElementById('formFrame').src = `${PDF_URL}/${id}/pdf`;
    document.getElementById('formModal').classList.add('show');
    document.body.style.overflow = 'hidden';

    if (isMobile()) {
        const scale = window.innerWidth / FM_FORM_WIDTH;
        fmDefaultScale = parseFloat(scale.toFixed(4));
        fmCurrentScale = fmDefaultScale;
        const shell = document.querySelector('.form-modal-shell');
        if (shell) shell.style.setProperty('--fm-scale', fmDefaultScale);
        const frame = document.getElementById('formFrame');
        if (frame) {
            frame.style.transform    = `scale(${fmDefaultScale})`;
            frame.style.marginBottom = `calc(1200px * (${fmDefaultScale} - 1))`;
            frame.style.marginRight  = `calc(${FM_FORM_WIDTH}px * (${fmDefaultScale} - 1))`;
        }
        const lbl = document.getElementById('fmZoomLabel');
        if (lbl) lbl.textContent = Math.round(fmDefaultScale * 100) + '%';
    } else {
        const frame = document.getElementById('formFrame');
        if (frame) { frame.style.transform = ''; frame.style.marginBottom = ''; frame.style.marginRight = ''; frame.style.width = ''; frame.style.height = ''; }
    }
}
function closeFormModal() {
    document.getElementById('formModal').classList.remove('show');
    document.body.style.overflow = '';
    setTimeout(() => { document.getElementById('formFrame').src = ''; document.getElementById('fmLoading').classList.remove('hidden'); }, 280);
}
function printFormModal() {
    const f = document.getElementById('formFrame');
    try { f.contentWindow.focus(); f.contentWindow.print(); }
    catch (e) { window.open(f.src, '_blank'); }
}

/* ══ DETAIL PANEL ══ */
function openDetailPanel(leaveId, e) {
    if (e) e.stopPropagation();
    const d = LEAVE_DATA[leaveId];
    if (!d) return;
    activePanelLeaveId = leaveId;

    document.getElementById('dpTitle').textContent    = 'Leave Application Details';
    document.getElementById('dpSubtitle').textContent = `${d.last_name}, ${d.first_name} · ${d.application_date}`;

    const isLocked = ['APPROVED','REJECTED','CANCELLED'].includes(d.status);
    const statusColors = {
        PENDING:'#fef9c3|#854d0e', RECEIVED:'#dbeafe|#1e40af',
        'ON-PROCESS':'#ede9fe|#5b21b6', APPROVED:'#dcfce7|#14532d',
        REJECTED:'#fee2e2|#991b1b', CANCELLED:'#f3f4f6|#6b7280'
    };
    const [sBg, sC] = (statusColors[d.status] || '#f3f4f6|#6b7280').split('|');
    const fullName  = [d.last_name, d.first_name, d.middle_name ? d.middle_name[0]+'.' : '', d.extension_name].filter(Boolean).join(' ');
    const salaryFmt = '₱' + parseFloat(d.salary || 0).toLocaleString('en-PH', {minimumFractionDigits:2});

    const vacMax  = parseFloat(d.vacation_max)  || 15;
    const sickMax = parseFloat(d.sick_max)       || 15;
    const vacRaw  = parseFloat(d.vacation_balance) ?? vacMax;
    const sickRaw = parseFloat(d.sick_balance)     ?? sickMax;

    const { vacBal, sickBal, crossNote } = computeBalanceAfterLeave(
        vacRaw, sickRaw, vacMax, sickMax, d.no_of_days, d.leave_type
    );

    const vacPct  = Math.min(100, Math.max(0, (vacBal  / vacMax)  * 100));
    const sickPct = Math.min(100, Math.max(0, (sickBal / sickMax) * 100));
    const vacFill  = vacPct  < 20 ? '#ef4444' : vacPct  < 50 ? '#f59e0b' : '#22c55e';
    const sickFill = sickPct < 20 ? '#ef4444' : sickPct < 50 ? '#f59e0b' : '#22c55e';

    const balanceHtml = `
        <div class="leave-balance-bar">
            <div class="lbb-row">
                <div class="lbb-label"><span>Vacation Leave</span><span>${vacBal.toFixed(1)} / ${vacMax} days</span></div>
                <div class="lbb-track"><div class="lbb-fill" style="width:${vacPct}%;background:${vacFill};"></div></div>
            </div>
            <div class="lbb-row">
                <div class="lbb-label"><span>Sick Leave</span><span>${sickBal.toFixed(1)} / ${sickMax} days</span></div>
                <div class="lbb-track"><div class="lbb-fill" style="width:${sickPct}%;background:${sickFill};"></div></div>
            </div>
            ${crossNote ? `<div class="lbb-note show">⚠ Cross-deduction: ${crossNote}</div>` : ''}
        </div>`;

    const actionByHtml = (isLocked && d.actioned_by_name)
        ? `<div class="dp-field span2">
                <label>Action Taken By</label>
                <p style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#f0fdf4;flex-shrink:0;">
                        <svg style="width:12px;height:12px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    ${d.actioned_by_name}
                </p>
           </div>`
        : '';

    document.getElementById('dpBody').innerHTML = `
        <div class="dp-card">
            <div class="dp-section-heading">
                <div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                <p class="dp-section-title">Employee Information</p>
            </div>
            <div class="dp-grid">
                <div class="dp-field"><label>Full Name</label><p>${fullName}</p></div>
                <div class="dp-field"><label>Employee ID</label><p style="font-family:monospace;">${d.employee_id}</p></div>
                <div class="dp-field"><label>Position</label><p>${d.position}</p></div>
                <div class="dp-field"><label>Department</label><p>${d.department}</p></div>
                <div class="dp-field"><label>Salary</label><p>${salaryFmt}</p></div>
                <div class="dp-field"><label>Hire Date</label><p>${d.hire_date}</p></div>
                <div class="dp-field"><label>Contact</label><p>${d.contact_number}</p></div>
                <div class="dp-field span2"><label>Address</label><p>${d.address}</p></div>
            </div>
        </div>
        <div class="dp-card">
            <div class="dp-section-heading">
                <div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <p class="dp-section-title">Application Details</p>
            </div>
            <div class="dp-grid">
                <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
                <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>
                <div class="dp-field"><label>Start Date</label><p>${d.start_date}</p></div>
                <div class="dp-field"><label>End Date</label><p>${d.end_date}</p></div>
                <div class="dp-field"><label>No. of Days</label><p>${d.no_of_days} working day(s)</p></div>
                <div class="dp-field"><label>Commutation</label><p>${d.commutation}</p></div>
                <div class="dp-field span2"><label>Details of Leave</label><p>${d.details_of_leave}</p></div>
                <div class="dp-field span2"><label>Current Status</label>
                    <p><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${d.status}</span></p>
                </div>
                ${d.reason ? `<div class="dp-field span2"><label>Reason / Remarks</label><p>${d.reason}</p></div>` : ''}
                ${actionByHtml}
            </div>
        </div>
        <div class="dp-card">
            <div class="dp-section-heading">
                <div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <p class="dp-section-title">Leave Balance After Application</p>
            </div>
            ${balanceHtml}
        </div>
        <div style="height:8px;"></div>`;

    const btns = document.getElementById('dpActionBtns');
    if (isLocked) {
        btns.innerHTML = `<span style="font-size:11px;color:#9ca3af;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2v1H10v-1c0-1.1.9-2 2-2zm-4 9h8a2 2 0 002-2v-5H6v5a2 2 0 002 2z"/></svg>
            Status is final
        </span>`;
    } else {
        btns.innerHTML = `
            <button class="btn-reject"  onclick="askConfirm(${leaveId},'REJECTED')">✕ Reject</button>
            <button class="btn-approve" onclick="askConfirm(${leaveId},'APPROVED')">✓ Approve</button>`;
    }

    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
    activePanelLeaveId = null;
}
function viewPdfFromPanel() { if (activePanelLeaveId) viewPdf(activePanelLeaveId); }

/* ══ CONFIRM MODAL ══ */
function askConfirm(leaveId, newStatus) {
    pendingConfirmAction = { leaveId, newStatus, key: String(leaveId) };
    const isApprove = newStatus === 'APPROVED';
    const iconBg    = isApprove ? '#dcfce7' : '#fee2e2';
    const iconColor = isApprove ? '#16a34a' : '#dc2626';
    const iconPath  = isApprove ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M6 18L18 6M6 6l12 12';

    document.getElementById('confirmIconWrap').innerHTML =
        `<svg style="width:22px;height:22px;" fill="none" stroke="${iconColor}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"/></svg>`;
    document.getElementById('confirmIconWrap').style.background = iconBg;
    document.getElementById('confirmTitle').textContent = isApprove ? 'Approve this Application?' : 'Reject this Application?';
    document.getElementById('confirmDesc').textContent  = isApprove
        ? 'This will mark the application as Approved and move it to History. This action cannot be reversed.'
        : 'This will mark the application as Rejected and move it to History. This action cannot be reversed.';

    const reasonWrap = document.getElementById('rejectReasonWrap');
    reasonWrap.classList.toggle('hidden', isApprove);
    if (!isApprove) document.getElementById('rejectReason').value = '';

    const okBtn = document.getElementById('confirmOkBtn');
    okBtn.textContent = isApprove ? '✓ Yes, Approve' : '✕ Yes, Reject';
    okBtn.className   = isApprove ? 'btn-approve' : 'btn-reject';
    document.getElementById('confirmModal').classList.add('show');
}
function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
    pendingConfirmAction = null;
}
function executeConfirmAction() {
    if (!pendingConfirmAction) return;
    const { leaveId, newStatus, key } = pendingConfirmAction;
    const reason = newStatus === 'REJECTED' ? document.getElementById('rejectReason').value : null;
    closeConfirmModal();
    doStatusUpdate(leaveId, newStatus, key, reason);
}

/* ══ STATUS DROPDOWN ══ */
function toggleStatusDropdown(key, e) {
    e.stopPropagation(); e.preventDefault();
    const wrap   = document.getElementById('sc_' + key);
    const dd     = document.getElementById('scd_' + key);
    const isOpen = wrap.classList.contains('open');
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) {
        wrap.classList.add('open');
        const pill = wrap.querySelector('.status-pill');
        const rect = pill.getBoundingClientRect();
        dd.style.left = rect.left + 'px';
        const spaceBelow = window.innerHeight - rect.bottom;
        if (spaceBelow < 220) {
            dd.style.top       = (rect.top - 6) + 'px';
            dd.style.transform = 'translateY(-100%)';
        } else {
            dd.style.top       = (rect.bottom + 6) + 'px';
            dd.style.transform = '';
        }
    }
}
document.addEventListener('click', () => {
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));
});
window.addEventListener('scroll',  () => document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open')), true);
window.addEventListener('resize',  () => document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open')));

/* ══ CHANGE STATUS ══ */
function changeStatus(leaveId, newStatus, e, scKey) {
    e.stopPropagation();
    const key  = scKey || String(leaveId);
    const wrap = document.getElementById('sc_' + key);
    if (wrap) wrap.classList.remove('open');
    if (newStatus === 'APPROVED' || newStatus === 'REJECTED') {
        pendingConfirmAction = { leaveId, newStatus, key };
        askConfirm(leaveId, newStatus);
        return;
    }
    doStatusUpdate(leaveId, newStatus, key, null);
}

function doStatusUpdate(leaveId, newStatus, key, reason) {
    const spin = document.getElementById('scspin_' + key);
    if (spin) spin.classList.add('show');

    fetch(`${APPROVE_URL}/${leaveId}/status`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus, reason: reason || null }),
    })
    .then(r => r.json())
    .then(data => {
        if (spin) spin.classList.remove('show');
        if (data.success) {
            const isTerminal = ['APPROVED','REJECTED','CANCELLED'].includes(newStatus);

            if (LEAVE_DATA[leaveId]) {
                LEAVE_DATA[leaveId].status = newStatus;
                if (data.actioned_by_name) LEAVE_DATA[leaveId].actioned_by_name = data.actioned_by_name;
            }

            if (isTerminal) {
                const activeRow = document.querySelector(`tr[data-leave-id="${leaveId}"]`);
                const rowData   = activeRow ? {
                    leaveId   : leaveId,
                    empId     : activeRow.querySelector('[data-label="Emp. ID"]')?.textContent?.trim() || '',
                    name      : activeRow.querySelector('.mob-title')?.textContent?.trim() || '',
                    leaveType : activeRow.querySelector('[data-label="Type"], [data-label="Leave Type"]')?.textContent?.trim() || '',
                    appDate   : activeRow.querySelector('[data-label="App. Date"]')?.textContent?.trim() || '',
                    startDate : activeRow.querySelector('[data-label="Start"]')?.textContent?.trim() || '',
                    endDate   : activeRow.querySelector('[data-label="End"]')?.textContent?.trim() || '',
                    days      : activeRow.querySelector('[data-label="Duration"], [data-label="Days"]')?.textContent?.trim() || '',
                    status    : activeRow.dataset.status,
                    recordType: activeRow.classList.contains('monetize-row') ? 'monetization' : 'leave',
                } : null;

                if (activeRow) {
                    activeRow.style.transition = 'opacity 0.35s, transform 0.35s';
                    activeRow.style.opacity    = '0';
                    activeRow.style.transform  = 'translateX(20px)';
                    setTimeout(() => { activeRow.remove(); }, 360);
                }

                const d = LEAVE_DATA[leaveId];
                if (d && rowData) {
                    const actionedBy = data.actioned_by_name || '';
                    const badgeCls   = `badge-${newStatus}`;
                    const typeBadge  = rowData.recordType === 'monetization'
                        ? `<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#ede9fe;color:#5b21b6;">Monetization</span>`
                        : `<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#f0fdf4;color:#166534;">Leave</span>`;
                    const actionByHtml = actionedBy
                        ? `<span class="action-by-chip"><svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>${actionedBy}</span>`
                        : `<span style="color:#d1d5db;font-size:11px;">—</span>`;

                    const searchStr = [
                        d.last_name, d.first_name, d.middle_name || '',
                        d.employee_id, d.leave_type,
                        newStatus.toLowerCase(),
                        d.application_date, d.start_date, d.end_date,
                        String(d.no_of_days), actionedBy,
                        rowData.recordType,
                    ].join(' ').toLowerCase();

                    const tr = document.createElement('tr');
                    tr.className          = 'history-row';
                    tr.dataset.status     = newStatus;
                    tr.dataset.recordType = rowData.recordType;
                    tr.dataset.search     = searchStr;
                    tr.style.cursor       = 'pointer';
                    tr.style.opacity      = '0';
                    tr.innerHTML = `
                        <td data-label="Emp. ID" class="font-bold font-mono text-sm">${d.employee_id}</td>
                        <td data-label="Name" class="font-semibold mob-title">${d.last_name}, ${d.first_name}</td>
                        <td data-label="Type">${typeBadge}</td>
                        <td data-label="Leave Type" class="text-xs text-gray-500">${d.leave_type}</td>
                        <td data-label="App. Date" class="text-gray-500">${d.application_date}</td>
                        <td data-label="Start" class="text-gray-500">${d.start_date}</td>
                        <td data-label="End" class="text-gray-500">${d.end_date}</td>
                        <td data-label="Days" class="text-gray-500">${d.no_of_days}</td>
                        <td data-label="Status"><span class="badge ${badgeCls}">${capitalize(newStatus)}</span></td>
                        <td data-label="Action By" onclick="event.stopPropagation()">${actionByHtml}</td>
                        <td class="mob-action" onclick="event.stopPropagation()" style="text-align:right;padding-right:14px;">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="openDetailPanel(${leaveId}, event)">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                    <button class="action-item" onclick="viewPdf(${leaveId})">
                                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                </div>
                            </div>
                        </td>`;
                    tr.onclick = (ev) => openDetailPanel(leaveId, ev);

                    const hTbody = document.getElementById('historyTbody');
                    const emptyRow = hTbody.querySelector('td[colspan="11"]');
                    if (emptyRow) emptyRow.closest('tr').remove();

                    hTbody.prepend(tr);
                    requestAnimationFrame(() => {
                        tr.style.transition = 'opacity 0.4s';
                        tr.style.opacity    = '1';
                    });
                }

                closeDetailPanel();

            } else {
                const wrap = document.getElementById('sc_' + key);
                if (wrap) {
                    const pill = wrap.querySelector('.status-pill');
                    [...pill.classList].filter(c => c.startsWith('pill-')).forEach(c => pill.classList.remove(c));
                    pill.classList.add('pill-' + newStatus);
                    const txt = [...pill.childNodes].filter(n => n.nodeType === Node.TEXT_NODE);
                    if (txt.length) txt[0].textContent = capitalize(newStatus.replace('-', ' ')) + ' ';
                    wrap.querySelectorAll('.status-option').forEach(opt => {
                        const s = opt.querySelector('.opt-dot').className.replace('opt-dot dot-', '');
                        opt.classList.toggle('current', s === newStatus);
                    });
                    const row = wrap.closest('tr');
                    if (row) row.dataset.status = newStatus;
                }
                if (activePanelLeaveId === leaveId) {
                    const statusColors = { PENDING:'#fef9c3|#854d0e', RECEIVED:'#dbeafe|#1e40af', 'ON-PROCESS':'#ede9fe|#5b21b6', APPROVED:'#dcfce7|#14532d', REJECTED:'#fee2e2|#991b1b', CANCELLED:'#f3f4f6|#6b7280' };
                    const [sBg, sC] = (statusColors[newStatus] || '#f3f4f6|#6b7280').split('|');
                    document.querySelectorAll('#dpBody .dp-field').forEach(f => {
                        if (f.querySelector('label')?.textContent === 'Current Status') {
                            f.querySelector('p').innerHTML = `<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${newStatus}</span>`;
                        }
                    });
                }
            }

            /* ── Recalculate stat counters ── */
            const counts = { PENDING:0, RECEIVED:0, APPROVED:0, REJECTED:0 };
            document.querySelectorAll('.leave-row').forEach(r => {
                if (counts[r.dataset.status] !== undefined) counts[r.dataset.status]++;
            });
            document.querySelectorAll('.monetize-row').forEach(r => {
                if (counts[r.dataset.status] !== undefined) counts[r.dataset.status]++;
            });
            Object.keys(counts).forEach(s => {
                const el = document.getElementById('leaveStat-' + s);
                if (el) el.textContent = counts[s] || 0;
            });

            let mPend = 0;
            document.querySelectorAll('.monetize-row').forEach(r => { if (r.dataset.status === 'PENDING') mPend++; });
            const mPendEl = document.getElementById('monetizeStat-PENDING');
            if (mPendEl) mPendEl.textContent = mPend;

            if (key.startsWith('m_')) { sortMonetizeTable(); filterTable('monetize'); }
            else { sortLeaveTable(); filterTable('leave'); }

            const labelMap = { PENDING:'Pending', RECEIVED:'Received', 'ON-PROCESS':'On-Process', APPROVED:'Approved', REJECTED:'Rejected' };
            showToast('Status Updated', `Changed to ${labelMap[newStatus] || newStatus}`, newStatus === 'APPROVED' ? 'success' : newStatus === 'REJECTED' ? 'error' : 'info');
        } else {
            showToast('Error', data.message || 'Could not update status.', 'error');
        }
    })
    .catch(() => showToast('Network Error', 'Please check your connection.', 'error'));
}

function capitalize(str) {
    return str.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
}

/* ══ MONETIZE CHECKBOXES ══ */
function onMonetizeCheck() {
    const checked = document.querySelectorAll('.monetize-chk:checked');
    const toolbar = document.getElementById('monetizeToolbar');
    document.getElementById('checkedCountLabel').textContent = `${checked.length} selected`;
    toolbar.classList.toggle('show', checked.length > 0);
    document.getElementById('chkAllMonetize').indeterminate =
        checked.length > 0 && checked.length < document.querySelectorAll('.monetize-chk').length;
}
function toggleAllMonetize(cb) {
    document.querySelectorAll('.monetize-chk').forEach(c => c.checked = cb.checked);
    onMonetizeCheck();
}

function generateLetter() {
    const checked = [...document.querySelectorAll('.monetize-chk:checked')];
    if (!checked.length) return;
    const rows = checked.map(cb => {
        const tr = cb.closest('tr');
        return { empId: tr.dataset.empId, name: tr.dataset.name, leaveType: tr.dataset.leaveType, days: tr.dataset.days, amount: tr.dataset.amount, appDate: tr.dataset.appdate };
    });
    const today     = new Date().toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'});
    const tableRows = rows.map((r,i) => `<tr><td>${i+1}</td><td>${r.empId}</td><td>${r.name}</td><td>${r.leaveType}</td><td>${r.days}</td><td>₱${r.amount}</td></tr>`).join('');
    const totalAmt  = rows.reduce((s,r) => s + parseFloat(r.amount.replace(/,/g,'')), 0);
    const html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Monetization Letter</title>
<style>body{font-family:Arial,sans-serif;font-size:12pt;margin:60px;color:#111;}h2{text-align:center;font-size:14pt;}.sub{text-align:center;font-size:10pt;color:#555;margin-bottom:32px;}p{line-height:1.7;margin-bottom:12px;}table{width:100%;border-collapse:collapse;margin:20px 0;}th,td{border:1px solid #ccc;padding:8px 10px;text-align:left;font-size:11pt;}th{background:#f3f4f6;font-weight:700;}.total td{font-weight:700;background:#f9fafb;}.footer{margin-top:48px;}.sig-line{border-top:1px solid #111;width:220px;margin-top:48px;padding-top:4px;font-size:10pt;}</style></head><body>
<h2>PROVINCIAL GOVERNMENT OF CAMARINES NORTE</h2>
<div class="sub">Office of the Provincial Agriculturist · Daet, Camarines Norte</div>
<p>Date: <strong>${today}</strong></p>
<p>To: <strong>The Concerned Authority</strong></p>
<p>Subject: <strong>Leave Monetization Request</strong></p>
<p>The following employees have filed and been approved for Leave Monetization:</p>
<table><thead><tr><th>#</th><th>Emp. ID</th><th>Name</th><th>Leave Type</th><th>Days</th><th>Est. Amount</th></tr></thead>
<tbody>${tableRows}</tbody>
<tfoot><tr class="total"><td colspan="5" style="text-align:right;">Total:</td><td>₱${totalAmt.toLocaleString('en-PH',{minimumFractionDigits:2})}</td></tr></tfoot></table>
<p>This letter serves as an official request for the monetization of leave credits as authorized under CSC guidelines.</p>
<div class="footer"><p>Prepared by:</p><div class="sig-line">PHRM Officer</div><br><br><p>Approved by:</p><div class="sig-line">Provincial Agriculturist</div></div>
<script>window.onload=function(){window.print();}<\/script></body></html>`;
    const win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(html); win.document.close();
}

/* ══ ACTION MENUS ══ */
function toggleMenu(btn) {
    const dd = btn.nextElementSibling;
    document.querySelectorAll('.action-dropdown.open').forEach(d => { if (d !== dd) d.classList.remove('open'); });
    dd.classList.toggle('open');
}

/* ══ FILTERS ══ */
function filterTable(type) {
    if (type === 'leave') {
        const search = document.getElementById('searchLeave').value.toLowerCase();
        const status = document.getElementById('filterLeaveStatus').value;
        const month  = document.getElementById('filterLeaveMonth').value;
        document.querySelectorAll('.leave-row').forEach(row => {
            const matchSearch = !search || (row.dataset.search || '').includes(search);
            const matchStatus = !status || row.dataset.status === status;
            const matchMonth  = !month  || row.dataset.month === month;
            row.style.display = (matchSearch && matchStatus && matchMonth) ? '' : 'none';
        });
    } else if (type === 'monetize') {
        const search = document.getElementById('searchMonetize').value.toLowerCase();
        const status = document.getElementById('filterMonetizeStatus').value;
        document.querySelectorAll('.monetize-row').forEach(row => {
            const matchSearch = !search || (row.dataset.search || '').includes(search);
            const matchStatus = !status || row.dataset.status === status;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    } else if (type === 'history') {
        const rawSearch  = document.getElementById('searchHistory').value.toLowerCase().trim();
        const status     = document.getElementById('filterHistoryStatus').value;
        const recType    = document.getElementById('filterHistoryType').value;
        const words      = rawSearch.split(/\s+/).filter(Boolean);

        document.querySelectorAll('.history-row').forEach(row => {
            const cellText   = [...row.querySelectorAll('td')].map(td => td.textContent.trim()).join(' ').toLowerCase();
            const attrSearch = (row.dataset.search || '').toLowerCase();
            const haystack   = cellText + ' ' + attrSearch;

            const matchSearch  = !words.length || words.every(w => haystack.includes(w));
            const matchStatus  = !status   || row.dataset.status === status;
            const matchType    = !recType  || row.dataset.recordType === recType;
            row.style.display  = (matchSearch && matchStatus && matchType) ? '' : 'none';
        });
    }
}

/* ══ HIGHLIGHT ROW ══ */
(function () {
    const hl = new URLSearchParams(window.location.search).get('highlight');
    if (!hl) return;
    const row = document.querySelector(`tr[data-leave-id="${hl}"]`);
    if (row) {
        row.scrollIntoView({ behavior:'smooth', block:'center' });
        row.style.transition = 'background 0.5s';
        row.style.background = '#fef9c3';
        setTimeout(() => row.style.background = '', 2500);
    }
})();

/* ══ TOAST ══ */
function showToast(title, msg, type = 'success') {
    const map = {
        success : { bg:'#dcfce7', c:'#16a34a', p:'M5 13l4 4L19 7' },
        error   : { bg:'#fee2e2', c:'#dc2626', p:'M6 18L18 6M6 6l12 12' },
        warning : { bg:'#fef9c3', c:'#ca8a04', p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        info    : { bg:'#dbeafe', c:'#2563eb', p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const s = map[type] || map.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    document.getElementById('toastIcon').innerHTML    = `<svg style="width:18px;height:18px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    document.getElementById('toastIcon').style.background = s.bg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

/* ══ KEYBOARD ══ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeFormModal(); closeDetailPanel(); closeConfirmModal(); }
});

/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    sortLeaveTable();
    filterTable('leave');
    sortMonetizeTable();
    filterTable('monetize');
});
</script>
@endsection