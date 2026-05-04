@extends('layouts.app')
@section('title', 'Half Day Management')
@section('page-title', 'Admin')

@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   HALF DAY MANAGEMENT — Matches leave-applications design
═══════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

/* ── Breadcrumb ── */
.breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:16px; flex-wrap:wrap; }
.breadcrumb a { color:#6b7280; text-decoration:none; }
.breadcrumb a:hover { color:#1a3a1a; }
.breadcrumb .sep { color:#d1d5db; }
.breadcrumb .current { color:#1a3a1a; font-weight:600; }

/* ── Tabs — exactly matching leave-applications style ── */
.tab-btn {
    padding:10px 4px; font-size:14px; font-weight:500;
    color:#6b7280; border:none; background:none;
    border-bottom:2px solid transparent; cursor:pointer;
    transition:all 0.2s; white-space:nowrap;
}
.tab-btn.active { color:#1a3a1a; border-bottom-color:#2d5a1b; font-weight:700; }

/* ── Tab bar — matches leave-applications .app-tab-bar ── */
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

/* ── Tab Panels ── */
.hd-tab-panel { display:none; }
.hd-tab-panel.active { display:flex; flex-direction:column; flex:1; min-height:0; }

/* ── Table ── */
.data-table { width:100%; font-size:13px; border-collapse:collapse; }
.data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
.data-table th { padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
.data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; text-align:center; }
.data-table tbody tr { cursor:pointer; }
.data-table tbody tr:hover { background:#fafafa; }

/* ── Status Badges (read-only / locked) ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
.badge::before { content:'●'; font-size:8px; }
.badge-PENDING   { background:#fef9c3; color:#854d0e; }
.badge-APPROVED  { background:#dcfce7; color:#14532d; }
.badge-REJECTED  { background:#fee2e2; color:#991b1b; }
.badge-CANCELLED { background:#f3f4f6; color:#6b7280; }
.badge-ORPHAN    { background:#fff7ed; color:#9a3412; }

/* ── Status Changer (pill + dropdown) ── */
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
.pill-PENDING  { background:#fef9c3; color:#854d0e; }
.pill-APPROVED { background:#dcfce7; color:#14532d; }
.pill-REJECTED { background:#fee2e2; color:#991b1b; }
.pill-CANCELLED{ background:#f3f4f6; color:#6b7280; }

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
.dot-PENDING  { background:#eab308; }
.dot-APPROVED { background:#22c55e; }
.dot-REJECTED { background:#ef4444; }
.dot-CANCELLED{ background:#9ca3af; }

.status-saving { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; border-radius:20px; background:rgba(255,255,255,0.7); pointer-events:none; opacity:0; transition:opacity 0.15s; }
.status-saving.show { opacity:1; pointer-events:all; }

/* ── Period pill ── */
.period-pill { display:inline-flex; align-items:center; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.period-am { background:#dbeafe; color:#1e40af; }
.period-pm { background:#ede9fe; color:#5b21b6; }

/* ── Action menu ── */
.action-menu { position:relative; display:inline-block; }
.action-menu-btn { background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; color:#9ca3af; font-size:18px; letter-spacing:2px; line-height:1; }
.action-menu-btn:hover { background:#f3f4f6; color:#374151; }
.action-dropdown { position:absolute; right:0; top:100%; margin-top:4px; z-index:50; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:170px; display:none; }
.action-dropdown.open { display:block; }
.action-item { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:13px; color:#374151; cursor:pointer; border:none; background:none; width:100%; text-align:left; }
.action-item:hover { background:#f9fafb; }
.action-item:first-child { border-radius:10px 10px 0 0; }
.action-item:last-child  { border-radius:0 0 10px 10px; }

/* ── Orphan row highlight ── */
.hd-row.orphan-row { background:#fffbf5; }
.hd-row.orphan-row:hover { background:#fff7ed; }
.hd-row.orphan-row td:first-child { border-left:3px solid #fb923c; }

/* ── Overlay ── */
#overlay {
    position:fixed; inset:0; z-index:90;
    background:rgba(0,0,0,0.25);
    backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
    opacity:0; pointer-events:none; transition:opacity 0.3s ease;
}
#overlay.show { opacity:1; pointer-events:all; }

/* ── Detail Slide-in Panel ── */
#detailPanel {
    position:fixed; top:0; right:0; bottom:0; z-index:100;
    width:55vw; min-width:360px; max-width:860px;
    display:flex; flex-direction:column;
    pointer-events:none;
    transform:translateX(100%);
    transition:transform 0.36s cubic-bezier(0.32,0.72,0,1);
}
#detailPanel.open { pointer-events:all; transform:translateX(0); }
.detail-box { background:#fff; width:100%; height:100%; display:flex; flex-direction:column; box-shadow:-12px 0 60px rgba(0,0,0,0.22); overflow:hidden; }
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
.detail-body { flex:1; overflow-y:auto; padding:0; background:#f8f9fa; scrollbar-width:thin; scrollbar-color:#d1d5db transparent; }
.detail-body::-webkit-scrollbar { width:4px; }
.detail-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:99px; }
.detail-footer { flex-shrink:0; padding:14px 20px; border-top:1px solid #f3f4f6; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }

/* Detail panel cards */
.dp-card { background:#fff; border-radius:12px; margin:14px 16px; padding:18px 18px 16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.dp-section-heading { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.dp-section-icon { width:30px; height:30px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
.dp-section-title { font-size:13px; font-weight:700; color:#111827; margin:0; }
.dp-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 20px; }
.dp-field label { display:block; font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:3px; }
.dp-field p { font-size:13px; color:#111827; font-weight:500; margin:0; }
.dp-field.span2 { grid-column:span 2; }

/* ── Confirm Modal ── */
.confirm-modal {
    position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
    display:flex; align-items:center; justify-content:center;
    opacity:0; pointer-events:none; transition:opacity 0.2s; padding:16px;
}
.confirm-modal.show { opacity:1; pointer-events:all; }
.confirm-card {
    background:#fff; border-radius:20px; padding:28px; width:440px; max-width:100%;
    box-shadow:0 24px 64px rgba(0,0,0,0.2);
    transform:scale(0.93); transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
}
.confirm-modal.show .confirm-card { transform:scale(1); }

/* ── Certification Modal ── */
.form-modal {
    position:fixed; inset:0; z-index:400;
    display:flex; align-items:center; justify-content:center;
    opacity:0; pointer-events:none; transition:opacity 0.25s ease;
}
.form-modal.show { opacity:1; pointer-events:all; }
.form-modal-backdrop { position:absolute; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px); }
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
    display:flex; align-items:center; justify-content:space-between; gap:8px; padding:10px 14px;
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
.fm-zoom-bar { display:none; align-items:center; justify-content:space-between; gap:8px; padding:6px 12px; background:#1a3a1a; flex-shrink:0; }
.fm-zoom-bar span { font-size:11px; color:rgba(255,255,255,0.7); }
.fm-zoom-btns { display:flex; gap:4px; }
.fm-zoom-btn { width:28px; height:28px; border-radius:6px; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:16px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.15s; }
.fm-zoom-btn:hover { background:rgba(255,255,255,0.28); }
.fm-zoom-label { font-size:11px; color:#fff; font-weight:700; min-width:36px; text-align:center; padding-top:4px; }
.fm-loading { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; background:#e8f0e8; transition:opacity 0.3s; pointer-events:none; z-index:2; }
.fm-loading.hidden { opacity:0; }
.fm-spinner { width:36px; height:36px; border:3px solid rgba(45,90,27,0.2); border-top-color:#2d5a1b; border-radius:50%; animation:fmSpin 0.75s linear infinite; }
@keyframes fmSpin { to { transform:rotate(360deg); } }
.fm-loading-text { font-size:12px; color:#6b7280; font-weight:500; }

/* ── Toast ── */
#toast { position:fixed; bottom:20px; right:20px; z-index:300; min-width:240px; max-width:calc(100vw - 40px);
    background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 8px 32px rgba(0,0,0,0.15);
    display:flex; align-items:center; gap:12px;
    opacity:0; transform:translateY(16px); transition:all 0.3s; pointer-events:none; }
#toast.show { opacity:1; transform:translateY(0); pointer-events:all; }

.form-field { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; transition:border-color 0.15s; outline:none; }
.form-field:focus { background:#fff; border-color:#2d5a1b; }
.btn-cancel-modal { padding:8px 18px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#6b7280; background:#fff; cursor:pointer; }
.btn-cancel-modal:hover { border-color:#9ca3af; color:#374151; }
.btn-approve { padding:8px 22px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#15803d; cursor:pointer; }
.btn-approve:hover { background:#166534; }
.btn-reject  { padding:8px 22px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; }
.btn-reject:hover  { background:#b91c1c; }
.btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
.btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }

/* ── Filter row — exact match with leave-applications ── */
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

/* ── Stats Grid ── */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:14px; flex-shrink:0; }
.stat-card { background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f3f4f6; display:flex; align-items:center; gap:12px; }
.stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-val { font-size:22px; font-weight:800; color:#111827; line-height:1; }
.stat-lbl { font-size:10px; color:#6b7280; font-weight:700; margin-top:2px; text-transform:uppercase; letter-spacing:0.05em; }

/* Alert bar */
.alert-bar { display:flex; align-items:center; gap:10px; margin-bottom:14px; padding:12px 16px; border-radius:14px; font-size:13px; font-weight:500; background:#fef9c3; border:1px solid #fde047; color:#854d0e; flex-shrink:0; flex-wrap:wrap; }
.alert-bar-orange { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; }

/* ── Toolbar head — matches leave-applications ── */
.toolbar-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.toolbar-head-text p:first-child { font-weight:700; color:#1f2937; font-size:14px; margin:0 0 2px; }
.toolbar-head-text p:last-child  { font-size:11px; color:#9ca3af; margin:0; }

/* ── Pending row highlight ── */
.hd-row[data-status="PENDING"]:not(.orphan-row) td:first-child { border-left:3px solid #eab308; }

/* ════ PAGE LAYOUT ════ */
.app-page { display:flex; flex-direction:column; height:calc(100vh - 120px); overflow:hidden; }
.app-card { flex:1; min-height:0; display:flex; flex-direction:column; background:#fff; border-radius:16px; border:1px solid #f3f4f6; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
.app-panel { flex:1; min-height:0; display:flex; flex-direction:column; }
.app-panel.hidden { display:none; }
.panel-toolbar { flex-shrink:0; }
.tsa { flex:1; min-height:0; overflow-y:auto; overflow-x:auto; scrollbar-width:thin; scrollbar-color:#e5e7eb transparent; -webkit-overflow-scrolling:touch; }
.tsa::-webkit-scrollbar { width:5px; height:5px; }
.tsa::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:99px; }
.tsa .data-table thead { position:sticky; top:0; z-index:2; }

/* ════ MOBILE ════ */
@media (max-width:767px) {
    .app-page { height:auto; overflow:visible; padding-bottom:80px; }
    .app-card { overflow:visible; flex:none; }
    .app-panel { flex:none; height:auto; overflow:visible; }
    .tsa { flex:none; overflow:visible; }
    .stats-grid { grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px; }
    .stat-card { padding:12px 14px; gap:10px; }
    .stat-val { font-size:20px; }
    .stat-icon { width:36px; height:36px; }

    /* filter row mobile */
    .filter-row {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .filter-row .rel.rel-search {
        flex: 1 1 0% !important;
        min-width: 0 !important;
    }
    .filter-row .rel.rel-search input {
        width: 100% !important;
        font-size: 12px !important;
        padding: 8px 8px 8px 30px !important;
    }
    .filter-row .rel:has(select) {
        flex: 0 0 auto !important;
    }
    .filter-row select {
        font-size: 11px !important;
        padding: 8px 22px 8px 8px !important;
        width: auto !important;
        min-width: 80px !important;
        max-width: 108px !important;
    }

    .data-table thead { display:none; }
    .data-table tbody { display:flex; flex-direction:column; gap:0; }
    .data-table tr { display:flex; flex-direction:column; padding:14px 16px; border-bottom:1px solid #f3f4f6; gap:6px; position:relative; }
    .data-table td { padding:0; border-bottom:none; font-size:13px; display:flex; align-items:flex-start; gap:8px; }
    .data-table td[data-label]::before { content:attr(data-label); font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; min-width:80px; flex-shrink:0; padding-top:1px; }
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
    .form-modal { padding:0; }
    .form-modal-shell { width:100vw; height:100vh; max-width:none; border-radius:0; transform:translateY(100%) !important; transition:transform 0.32s cubic-bezier(0.32,0.72,0,1) !important; }
    .form-modal.show .form-modal-shell { transform:translateY(0) !important; }
    .fm-zoom-bar { display:flex; }
    .fm-body { overflow:auto !important; -webkit-overflow-scrolling:touch; }
    #formFrame { width:820px !important; height:1200px !important; transform-origin:top left; transform:scale(var(--fm-scale,0.44)); margin-bottom:calc(1200px * (var(--fm-scale,0.44) - 1)); margin-right:calc(820px * (var(--fm-scale,0.44) - 1)); }
    #toast { left:16px; right:16px; bottom:16px; min-width:0; width:auto; }
    .breadcrumb { font-size:12px; margin-bottom:12px; }
    .confirm-card { padding:20px 18px; }
    .app-tab-bar { gap:16px; padding:0 16px; }
    .tab-btn { font-size:12px; padding:10px 2px; }
}
@media (min-width:768px) and (max-width:1023px) { .app-page { height:calc(100vh - 100px); } #detailPanel { width:80vw; min-width:360px; } }
@media (min-width:1024px) and (max-width:1279px) { #detailPanel { width:60vw; } }
@media (min-width:1280px) { .app-page { height:calc(100vh - 120px); } #detailPanel { width:55vw; } }
</style>

<div class="app-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="flex-shrink:0;">
        <a href="{{ route('dashboard') }}">Admin</a>
        <span class="sep">›</span>
        <span class="current">Half Day Management</span>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" style="flex-shrink:0;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef9c3;">
                <svg style="width:18px;height:18px;color:#854d0e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-val" id="statPending">{{ $halfDays->where('status','PENDING')->count() }}</div>
                <div class="stat-lbl">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;">
                <svg style="width:18px;height:18px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="stat-val" id="statApproved">{{ $halfDays->where('status','APPROVED')->count() }}</div>
                <div class="stat-lbl">Approved</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">
                <svg style="width:18px;height:18px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="stat-val" id="statRejected">{{ $halfDays->where('status','REJECTED')->count() }}</div>
                <div class="stat-lbl">Rejected</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3f4f6;">
                <svg style="width:18px;height:18px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="stat-val" id="statTotal">{{ $halfDays->count() }}</div>
                <div class="stat-lbl">Total</div>
            </div>
        </div>
    </div>

    @php
        $pendingCount  = $halfDays->where('status','PENDING')->filter(fn($h) => !is_null($h->employee_id))->count();
        $orphanCount   = $halfDays->whereNull('employee_id')->count();
        $approvedCount = $halfDays->where('status','APPROVED')->count();
    @endphp

    @if($orphanCount > 0)
    <div class="alert-bar alert-bar-orange" style="flex-shrink:0;">
        <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><strong>{{ $orphanCount }} record{{ $orphanCount > 1 ? 's' : '' }}</strong> with no linked employee — these cannot be approved and are highlighted in orange.</span>
    </div>
    @endif

    @if($pendingCount > 0)
    <div class="alert-bar" style="flex-shrink:0;">
        <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><strong>{{ $pendingCount }} half day application{{ $pendingCount > 1 ? 's' : '' }}</strong> awaiting your action.</span>
    </div>
    @endif

    {{-- ════ MAIN TABLE CARD ════ --}}
    <div class="app-card">

        {{-- Tab Bar — exactly matching leave-applications style --}}
        <div class="app-tab-bar">
            <button class="tab-btn active" id="tabApplications" onclick="switchTab('applications')">Half Day Applications</button>
            <button class="tab-btn"        id="tabHistory"      onclick="switchTab('history')">History</button>
        </div>

        {{-- ── TAB: Applications ── --}}
        <div id="panelApplications" class="app-panel">
            <div class="panel-toolbar" style="padding:14px 16px; border-bottom:1px solid #f9fafb;">
                <div class="toolbar-head" style="margin-bottom:12px;">
                    <div class="toolbar-head-text">
                        <p>Half Day Application List</p>
                        <p>Click a row to view details · Click a status pill to change it</p>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="rel rel-search">
                        <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        <input type="text" placeholder="Search name / ID…" id="searchHd" oninput="filterRows()">
                    </div>
                    <div class="rel">
                        <select id="filterStatus" onchange="filterRows()">
                            <option value="">All Status</option>
                            <option value="PENDING" selected>Pending</option>
                            <option value="REJECTED">Rejected</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                        <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="rel">
                        <select id="filterPeriod" onchange="filterRows()">
                            <option value="">All Periods</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                        <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div class="tsa">
                <table class="data-table" id="hdTable">
                    <thead>
                        <tr>
                            <th>Half Day ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Application Date</th>
                            <th>Date of Absence</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="hdTableBody">
                        @forelse($halfDays->whereNotIn('status', ['APPROVED', 'REJECTED', 'CANCELLED']) as $hd)
                        @php
                            $hdId      = $hd->half_day_id;
                            $isOrphan  = is_null($hd->employee_id) || is_null($hd->employee);
                            $isLocked  = $isOrphan || in_array($hd->status, ['APPROVED','REJECTED','CANCELLED']);
                            $period    = strtoupper($hd->time_period ?? '');
                            $empSearch = strtolower(
                                ($hd->employee->last_name ?? '') . ' ' .
                                ($hd->employee->first_name ?? '') . ' ' .
                                ($hd->employee->formatted_employee_id ?? '')
                            );
                        @endphp
                        <tr class="hd-row{{ $isOrphan ? ' orphan-row' : '' }}"
                            data-id="{{ $hdId }}"
                            data-status="{{ $hd->status }}"
                            data-period="{{ $period }}"
                            data-orphan="{{ $isOrphan ? '1' : '0' }}"
                            data-search="{{ $empSearch }} {{ $hdId }}"
                            onclick="openDetailPanel({{ $hdId }}, event)">

                            <td data-label="HD ID" style="font-family:monospace;font-size:12px;font-weight:600;color:#2d5a1b; text-align:center;">
                                {{ $hdId }}
                            </td>
                            <td data-label="Name" class="mob-title" style="font-weight:600;">
                                @if($isOrphan)
                                    <span style="color:#9ca3af;font-style:italic;">No employee linked</span>
                                @else
                                    {{ $hd->employee->last_name ?? '—' }},
                                    {{ $hd->employee->first_name ?? '' }}
                                    {{ $hd->employee->middle_name ? strtoupper($hd->employee->middle_name[0]).'.' : '' }}
                                @endif
                            </td>
                            <td data-label="Dept." class="mob-hide" style="font-size:12px;color:#6b7280;">
                                {{ $hd->employee?->department?->getAttribute('department_name')
                                ?? $hd->employee?->department?->getAttribute('name')
                                ?? '—' }}
                            </td>
                            <td data-label="Leave Type" style="font-size:12px;color:#6b7280;">
                                {{ optional($hd->leaveType)->type_name ?? optional($hd->leaveType)->name ?? '—' }}
                            </td>
                            <td data-label="Applied" style="color:#374151;">
                                {{ $hd->application_date ? \Carbon\Carbon::parse($hd->application_date)->format('M d, Y') : '—' }}
                            </td>
                            <td data-label="Date of Absence" style="color:#374151;">
                                {{ $hd->date_of_absence ? \Carbon\Carbon::parse($hd->date_of_absence)->format('M d, Y') : '—' }}
                            </td>
                            <td data-label="Period">
                                @if($period)
                                    <span class="period-pill {{ $period === 'AM' ? 'period-am' : 'period-pm' }}">
                                        {{ $period }}
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            <td onclick="event.stopPropagation()" class="mob-status">
                                @if($isOrphan)
                                    <div style="display:inline-flex;align-items:center;gap:6px;">
                                        <span class="badge badge-{{ $hd->status }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                                        <span title="No employee linked — cannot be approved" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#fff7ed;cursor:help;flex-shrink:0;">
                                            <svg style="width:10px;height:10px;color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </span>
                                    </div>
                                @elseif(in_array($hd->status, ['REJECTED','CANCELLED']))
                                    <div style="display:inline-flex;align-items:center;gap:6px;">
                                        <span class="badge badge-{{ $hd->status }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                                        <span title="Status is final" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#f3f4f6;cursor:help;flex-shrink:0;">
                                            <svg style="width:10px;height:10px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2v1H10v-1c0-1.1.9-2 2-2zm-4 9h8a2 2 0 002-2v-5H6v5a2 2 0 002 2z"/></svg>
                                        </span>
                                    </div>
                                @else
                                    <div class="status-changer" id="sc_{{ $hdId }}">
                                        <button class="status-pill pill-{{ $hd->status }}"
                                                onclick="toggleStatusDropdown({{ $hdId }}, event)">
                                            {{ ucfirst(strtolower($hd->status)) }}
                                            <svg class="pill-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="status-saving" id="scspin_{{ $hdId }}">
                                            <svg style="width:16px;height:16px;color:#9ca3af;animation:fmSpin 0.75s linear infinite;" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"/>
                                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:0.75;"/>
                                            </svg>
                                        </div>
                                        <div class="status-dropdown" id="scd_{{ $hdId }}">
                                            @foreach(['PENDING','APPROVED','REJECTED','CANCELLED'] as $s)
                                            <button class="status-option {{ $hd->status === $s ? 'current' : '' }}"
                                                    onclick="changeStatus({{ $hdId }}, '{{ $s }}', event)">
                                                <span class="opt-dot dot-{{ $s }}"></span>
                                                {{ ucfirst(strtolower($s)) }}
                                                @if($hd->status === $s)
                                                    <svg style="width:14px;height:14px;margin-left:auto;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="mob-action" onclick="event.stopPropagation()" style="text-align:center;padding-right:8px;">
                                <div class="action-menu">
                                    <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                    <div class="action-dropdown">
                                        <button class="action-item" onclick="openDetailPanel({{ $hdId }}, event)">
                                            <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">
                                <svg style="width:32px;height:32px;margin:0 auto 8px;display:block;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                No half day applications found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>{{-- end #panelApplications --}}

        {{-- ── TAB: History (APPROVED + REJECTED + CANCELLED records) ── --}}
        <div id="panelHistory" class="app-panel hidden">
            <div class="panel-toolbar" style="padding:14px 16px; border-bottom:1px solid #f9fafb;">
                <div class="toolbar-head" style="margin-bottom:12px;">
                    <div class="toolbar-head-text">
                        <p>Half Day History</p>
                        <p>All approved, rejected, and cancelled half day applications</p>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="rel rel-search">
                        <svg class="search-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        <input type="text" placeholder="Search name / ID…" id="searchHistory" oninput="filterHistory()">
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
                        <select id="filterHistoryPeriod" onchange="filterHistory()">
                            <option value="">All Periods</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                        <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="rel">
                        <select id="filterHistoryMonth" onchange="filterHistory()">
                            <option value="">All Months</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <svg class="chevron" style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div class="tsa">
                <table class="data-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>Half Day ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Application Date</th>
                            <th>Date of Absence</th>
                            <th>Period</th>
                            <th>Updated Date</th>
                            <th>Status</th>
                            <th>Updated By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @php $historyHDs = $halfDays->whereIn('status', ['APPROVED','REJECTED','CANCELLED']); @endphp
                        @forelse($historyHDs as $hd)
                        @php
                            $hdId      = $hd->half_day_id;
                            $isOrphan  = is_null($hd->employee_id) || is_null($hd->employee);
                            $period    = strtoupper($hd->time_period ?? '');
                            $empSearch = strtolower(
                                ($hd->employee->last_name ?? '') . ' ' .
                                ($hd->employee->first_name ?? '') . ' ' .
                                ($hd->employee->formatted_employee_id ?? '')
                            );
                            $historyMonth = ($hd->approved_date ?? $hd->application_date)
                                ? \Carbon\Carbon::parse($hd->approved_date ?? $hd->application_date)->format('m')
                                : '';
                            $approvedByName = $hd->approvedBy
    ? (($hd->approvedBy->last_name ?? '') . ', ' . ($hd->approvedBy->first_name ?? ''))
    : '—';
                        @endphp
                        <tr class="hist-row"
                            data-id="{{ $hdId }}"
                            data-status="{{ $hd->status }}"
                            data-period="{{ $period }}"
                            data-month="{{ $historyMonth }}"
                            data-search="{{ $empSearch }} {{ $hdId }}"
                            onclick="openDetailPanel({{ $hdId }}, event)">

                            <td data-label="HD ID" style="font-family:monospace;font-size:12px;font-weight:600;color:#2d5a1b;text-align:center;">
                                {{ $hdId }}
                            </td>
                            <td data-label="Name" class="mob-title" style="font-weight:600;">
                                @if($isOrphan)
                                    <span style="color:#9ca3af;font-style:italic;">No employee linked</span>
                                @else
                                    {{ $hd->employee->last_name ?? '—' }},
                                    {{ $hd->employee->first_name ?? '' }}
                                    {{ $hd->employee->middle_name ? strtoupper($hd->employee->middle_name[0]).'.' : '' }}
                                @endif
                            </td>
                            <td data-label="Dept." class="mob-hide" style="font-size:12px;color:#6b7280;">
                                {{ $hd->employee?->department?->getAttribute('department_name')
                                ?? $hd->employee?->department?->getAttribute('name')
                                ?? '—' }}
                            </td>
                            <td data-label="Leave Type" style="font-size:12px;color:#6b7280;">
                                {{ optional($hd->leaveType)->type_name ?? optional($hd->leaveType)->name ?? '—' }}
                            </td>
                            <td data-label="Applied" style="color:#374151;">
                                {{ $hd->application_date ? \Carbon\Carbon::parse($hd->application_date)->format('M d, Y') : '—' }}
                            </td>
                            <td data-label="Date of Absence" style="color:#374151;">
                                {{ $hd->date_of_absence ? \Carbon\Carbon::parse($hd->date_of_absence)->format('M d, Y') : '—' }}
                            </td>
                            <td data-label="Period">
                                @if($period)
                                    <span class="period-pill {{ $period === 'AM' ? 'period-am' : 'period-pm' }}">{{ $period }}</span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td data-label="Approved" style="color:#374151;font-size:12px;">
                                {{ $hd->approved_date ? \Carbon\Carbon::parse($hd->approved_date)->format('M d, Y') : '—' }}
                            </td>
                            <td data-label="Status">
                                <span class="badge badge-{{ $hd->status }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                            </td>
                            <td data-label="Approved By" style="font-size:12px;color:#374151;">
                                {{ $approvedByName }}
                            </td>
                            <td class="mob-action" onclick="event.stopPropagation()" style="text-align:center;padding-right:8px;">
                                <div class="action-menu">
                                    <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                                    <div class="action-dropdown">
                                        <button class="action-item" onclick="openDetailPanel({{ $hdId }}, event)">
                                            <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="historyEmptyRow">
                            <td colspan="11" style="padding:64px 24px;text-align:center;color:#9ca3af;font-size:13px;">
                                No completed half day records yet.
                            </td>
                        </tr>
                        @endforelse
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
                <h2 id="dpTitle">Half Day Application Details</h2>
                <p id="dpSubtitle" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Loading…</p>
            </div>
            <button class="detail-close" onclick="closeDetailPanel()">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="detail-body" id="dpBody"></div>
        <div class="detail-footer" style="justify-content:flex-end;">
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
        <div id="rejectReasonWrap" style="display:none;margin-bottom:18px;">
            <label style="display:block;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">
                Reason for Rejection <span style="color:#9ca3af;">(optional)</span>
            </label>
            <textarea id="rejectReason" rows="3" class="form-field" placeholder="Enter reason…"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
            <button onclick="closeConfirmModal()" class="btn-cancel-modal">Cancel</button>
            <button id="confirmOkBtn" onclick="executeConfirmAction()" class="btn-approve">Confirm</button>
        </div>
    </div>
</div>

{{-- ════ CERTIFICATION MODAL ════ --}}
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
                    <div class="fm-topbar-label" id="fmTopbarLabel">Half Day Certification</div>
                    <div class="fm-topbar-sub" id="fmTopbarSub">Office of the Provincial Agriculturist</div>
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
                <button class="fm-zoom-btn" onclick="fmZoom(-0.05)">−</button>
                <span class="fm-zoom-label" id="fmZoomLabel">100%</span>
                <button class="fm-zoom-btn" onclick="fmZoom(+0.05)">+</button>
                <button class="fm-zoom-btn" onclick="fmZoomReset()" style="font-size:13px;margin-left:2px;">⟳</button>
            </div>
        </div>
        <div class="fm-body" id="fmBody">
            <div class="fm-loading" id="fmLoading">
                <div class="fm-spinner"></div>
                <span class="fm-loading-text">Loading certification…</span>
            </div>
            <iframe id="formFrame" src="" title="Half Day Certification"
                    onload="document.getElementById('fmLoading').classList.add('hidden')"></iframe>
        </div>
    </div>
</div>

{{-- ════ TOAST ════ --}}
<div id="toast">
    <div id="toastIcon" style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:justify-content;flex-shrink:0;"></div>
    <div>
        <p style="font-size:13px;font-weight:700;color:#1f2937;margin:0;" id="toastTitle"></p>
        <p style="font-size:11px;color:#6b7280;margin:2px 0 0;" id="toastMsg"></p>
    </div>
</div>

<script>
/* ══════════════════════════════════════════════════════
   DATA — keyed by half_day_id
══════════════════════════════════════════════════════ */
const HD_DATA = {!! json_encode(
    $halfDays->keyBy('half_day_id')->map(fn($h) => [
        'id'               => $h->half_day_id,
        'orphan'           => is_null($h->employee_id) || is_null($h->employee),
        'employee_id_fmt'  => $h->employee->formatted_employee_id ?? '—',
        'first_name'       => $h->employee->first_name  ?? '',
        'last_name'        => $h->employee->last_name   ?? '',
        'middle_name'      => $h->employee->middle_name ?? '',
        'position'   => optional($h->employee?->position)->getAttribute('position_name')
                     ?? optional($h->employee?->position)->getAttribute('name')
                     ?? '—',
        'department' => optional($h->employee?->department)->getAttribute('department_name')
                     ?? optional($h->employee?->department)->getAttribute('name')
                     ?? '—',
        'leave_type'       => optional($h->leaveType)->type_name ?? '—',
        'application_date' => $h->application_date
                                ? \Carbon\Carbon::parse($h->application_date)->format('M d, Y') : '—',
        'date_of_absence'  => $h->date_of_absence
                                ? \Carbon\Carbon::parse($h->date_of_absence)->format('M d, Y') : '—',
        'period'           => strtoupper($h->time_period ?? ''),
        'reason'           => $h->reason ?? '',
        'status'           => $h->status,
        'approved_date'    => $h->approved_date
                                ? \Carbon\Carbon::parse($h->approved_date)->format('M d, Y') : '—',
        'approved_by'      => $h->approvedBy
                                ? (($h->approvedBy->last_name ?? '') . ', ' . ($h->approvedBy->first_name ?? ''))
                                : (isset($h->approved_by) ? $h->approved_by : '—'),
        'updated_at'       => $h->updated_at ? \Carbon\Carbon::parse($h->updated_at)->timestamp : 0,
    ])
) !!};
const CSRF       = "{{ csrf_token() }}";
const STATUS_URL = "{{ url('admin/halfday') }}";
const CERT_URL   = "{{ url('admin/halfday') }}";

let activePanelId        = null;
let pendingConfirmAction = null;
const STATUS_ORDER       = { PENDING:0, APPROVED:1, REJECTED:2, CANCELLED:3 };

/* ══════════════════════════════════════════════════════
   TAB SWITCHING — matches leave-applications switchTab()
══════════════════════════════════════════════════════ */
function switchTab(tab) {
    ['applications','history'].forEach(t => {
        const key = t.charAt(0).toUpperCase() + t.slice(1);
        document.getElementById('tab' + key).classList.toggle('active', t === tab);
        document.getElementById('panel' + key).classList.toggle('hidden', t !== tab);
    });
}

/* ── Sort rows by status priority ── */
function sortTable() {
    const tbody = document.getElementById('hdTableBody');
    if (!tbody) return;
    [...tbody.querySelectorAll('tr.hd-row')]
        .sort((a,b) => (STATUS_ORDER[a.dataset.status]??99) - (STATUS_ORDER[b.dataset.status]??99))
        .forEach(r => tbody.appendChild(r));
}

/* ── Sort Applications tab by updated_at descending ── */
function sortTableByUpdatedAt() {
    const tbody = document.getElementById('hdTableBody');
    if (!tbody) return;
    [...tbody.querySelectorAll('tr.hd-row')]
        .sort((a, b) => (HD_DATA[b.dataset.id]?.updated_at ?? 0) - (HD_DATA[a.dataset.id]?.updated_at ?? 0))
        .forEach(r => tbody.appendChild(r));
}

/* ── Sort History tab by updated_at descending ── */
function sortHistory() {
    const tbody = document.getElementById('historyTableBody');
    if (!tbody) return;
    [...tbody.querySelectorAll('tr.hist-row')]
        .sort((a, b) => (HD_DATA[b.dataset.id]?.updated_at ?? 0) - (HD_DATA[a.dataset.id]?.updated_at ?? 0))
        .forEach(r => tbody.appendChild(r));
}

/* ── Applications Filter ── */
function filterRows() {
    const q  = document.getElementById('searchHd').value.toLowerCase();
    const st = document.getElementById('filterStatus').value;
    const pd = document.getElementById('filterPeriod').value;
    document.querySelectorAll('.hd-row').forEach(row => {
        const ok = (!q  || (row.dataset.search||'').includes(q) || String(row.dataset.id).includes(q))
                && (!st || row.dataset.status === st)
                && (!pd || row.dataset.period === pd);
        row.style.display = ok ? '' : 'none';
    });
}

/* ── History Filter ── */
function filterHistory() {
    const q  = document.getElementById('searchHistory').value.toLowerCase();
    const st = document.getElementById('filterHistoryStatus').value;
    const pd = document.getElementById('filterHistoryPeriod').value;
    const mo = document.getElementById('filterHistoryMonth').value;
    let visible = 0;
    document.querySelectorAll('.hist-row').forEach(row => {
        const ok = (!q  || (row.dataset.search||'').includes(q) || String(row.dataset.id).includes(q))
                && (!st || row.dataset.status === st)
                && (!pd || row.dataset.period === pd)
                && (!mo || row.dataset.month === mo);
        row.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });
    const noResults = document.getElementById('historyNoResults');
    if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
}

/* ── Status pill dropdown ── */
function toggleStatusDropdown(key, e) {
    e.stopPropagation(); e.preventDefault();
    const wrap   = document.getElementById('sc_'+key);
    const dd     = document.getElementById('scd_'+key);
    const isOpen = wrap.classList.contains('open');
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) {
        wrap.classList.add('open');
        const rect = wrap.querySelector('.status-pill').getBoundingClientRect();
        dd.style.left      = rect.left+'px';
        const below        = window.innerHeight - rect.bottom;
        dd.style.top       = below < 180 ? (rect.top-6)+'px' : (rect.bottom+6)+'px';
        dd.style.transform = below < 180 ? 'translateY(-100%)' : '';
    }
}

document.addEventListener('click', () => {
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));
});
window.addEventListener('scroll', () =>
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open')), true);
window.addEventListener('resize', () =>
    document.querySelectorAll('.status-changer.open').forEach(el => el.classList.remove('open')));

/* ── Change status ── */
function changeStatus(hdId, newStatus, e) {
    e.stopPropagation();
    document.getElementById('sc_'+hdId)?.classList.remove('open');
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));

    if (HD_DATA[hdId]?.orphan) {
        showToast('Cannot Update', 'This record has no linked employee.', 'warning');
        return;
    }

    if (newStatus === 'APPROVED' || newStatus === 'REJECTED') {
        pendingConfirmAction = { hdId, newStatus };
        askConfirm(hdId, newStatus);
        return;
    }
    doStatusUpdate(hdId, newStatus, null);
}

/* ── AJAX update ── */
function doStatusUpdate(hdId, newStatus, reason) {
    const spin = document.getElementById('scspin_'+hdId);
    if (spin) spin.classList.add('show');

    fetch(`${STATUS_URL}/${hdId}/status`, {
        method:  'POST',
        headers: {
            'X-CSRF-TOKEN':     CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type':     'application/json',
        },
        body: JSON.stringify({ status: newStatus, reason: reason||null }),
    })
    .then(r => r.json())
    .then(data => {
        if (spin) spin.classList.remove('show');
        if (!data.success) { showToast('Error', data.message||'Could not update.','error'); return; }

        if (HD_DATA[hdId]) {
            HD_DATA[hdId].status     = newStatus;
            HD_DATA[hdId].updated_at = Math.floor(Date.now() / 1000);
            if (data.approved_date) HD_DATA[hdId].approved_date = data.approved_date;
            if (data.approved_by)   HD_DATA[hdId].approved_by   = data.approved_by;
        }

        const row = document.querySelector(`tr.hd-row[data-id="${hdId}"]`);
        if (row) {
            row.dataset.status = newStatus;

            const dateCell = document.getElementById(`approved-date-cell-${hdId}`);
            if (dateCell) dateCell.textContent = data.approved_date || '—';

            const statusTd = row.querySelector('td.mob-status');
            if (statusTd) {
                const locked = ['APPROVED','REJECTED','CANCELLED'].includes(newStatus);
                if (locked) {
                    statusTd.innerHTML = `<div style="display:inline-flex;align-items:center;gap:6px;">
                        <span class="badge badge-${newStatus}">${capitalize(newStatus)}</span>
                        <span title="Status is final" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#f3f4f6;cursor:help;flex-shrink:0;">
                            <svg style="width:10px;height:10px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2v1H10v-1c0-1.1.9-2 2-2zm-4 9h8a2 2 0 002-2v-5H6v5a2 2 0 002 2z"/></svg>
                        </span>
                    </div>`;
                } else {
                    const pill = statusTd.querySelector('.status-pill');
                    if (pill) {
                        [...pill.classList].filter(c=>c.startsWith('pill-')).forEach(c=>pill.classList.remove(c));
                        pill.classList.add('pill-'+newStatus);
                        [...pill.childNodes].filter(n=>n.nodeType===Node.TEXT_NODE)
                            .forEach(n=>n.textContent=capitalize(newStatus)+' ');
                        statusTd.querySelectorAll('.status-option').forEach(opt => {
                            const s = [...opt.querySelector('.opt-dot').classList]
                                        .find(c=>c.startsWith('dot-'))?.replace('dot-','');
                            opt.classList.toggle('current', s===newStatus);
                        });
                    }
                }
            }
        }

        if (['APPROVED','REJECTED','CANCELLED'].includes(newStatus) && row) {
            moveRowToHistory(hdId, row, data.approved_date, data.approved_by);
        }

        updateStats();
        sortTableByUpdatedAt();
        filterRows();
        if (activePanelId===hdId) openDetailPanel(hdId,null);

        showToast('Status Updated', `Changed to ${capitalize(newStatus)}`,
            newStatus==='APPROVED' ? 'success' : newStatus==='REJECTED' ? 'error' : 'info');
    })
    .catch(() => {
        if (spin) spin.classList.remove('show');
        showToast('Network Error','Please check your connection.','error');
    });
}

/* ── Move row to History tab ── */
function moveRowToHistory(hdId, appRow, approvedDate, approvedBy) {
    const d = HD_DATA[hdId];
    if (!d) return;

    appRow.remove();

    const staticEmpty = document.getElementById('historyEmptyRow');
    if (staticEmpty) staticEmpty.remove();

    const noResults = document.getElementById('historyNoResults');
    if (noResults) noResults.remove();

    const period   = d.period || '';
    const mi       = d.middle_name ? d.middle_name.charAt(0).toUpperCase()+'.' : '';
    const approvedDateFmt = approvedDate || d.approved_date || '—';
    const approvedByFmt   = approvedBy   || d.approved_by  || '—';

    let monthVal = '';
    if (approvedDate) {
        const parsed = new Date(approvedDate);
        if (!isNaN(parsed)) monthVal = String(parsed.getMonth()+1).padStart(2,'0');
    }

    const empSearch = (d.last_name+' '+d.first_name+' '+d.employee_id_fmt).toLowerCase();

    const newRow = document.createElement('tr');
    newRow.className = 'hist-row';
    newRow.dataset.id     = hdId;
    newRow.dataset.status = d.status;
    newRow.dataset.period = period;
    newRow.dataset.month  = monthVal;
    newRow.dataset.search = empSearch;
    newRow.onclick = (e) => openDetailPanel(hdId, e);
    newRow.innerHTML = `
        <td data-label="HD ID" style="font-family:monospace;font-size:12px;font-weight:600;color:#2d5a1b;text-align:center;">${hdId}</td>
        <td data-label="Name" class="mob-title" style="font-weight:600;">${d.last_name}, ${d.first_name} ${mi}</td>
        <td data-label="Dept." class="mob-hide" style="font-size:12px;color:#6b7280;">${d.department}</td>
        <td data-label="Leave Type" style="font-size:12px;color:#6b7280;">${d.leave_type}</td>
        <td data-label="Applied" style="color:#374151;">${d.application_date}</td>
        <td data-label="Date of Absence" style="color:#374151;">${d.date_of_absence || '—'}</td>
        <td data-label="Period">
            ${period
                ? `<span class="period-pill ${period==='AM'?'period-am':'period-pm'}">${period}</span>`
                : '<span style="color:#9ca3af;">—</span>'}
        </td>
        <td data-label="Approved" style="color:#374151;font-size:12px;">${d.status === 'APPROVED' ? approvedDateFmt : '—'}</td>
        <td data-label="Status">
            <span class="badge badge-${d.status}">${d.status.charAt(0)+d.status.slice(1).toLowerCase()}</span>
        </td>
        <td data-label="Approved By" style="font-size:12px;color:#374151;">${approvedByFmt}</td>
        <td class="mob-action" onclick="event.stopPropagation()" style="text-align:center;padding-right:8px;">
            <div class="action-menu">
                <button class="action-menu-btn" onclick="toggleMenu(this)">···</button>
                <div class="action-dropdown">
                    <button class="action-item" onclick="openDetailPanel(${hdId}, event)">
                        <svg style="width:14px;height:14px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View Details
                    </button>
                </div>
            </div>
        </td>`;

    document.getElementById('historyTableBody').prepend(newRow);

    /* Re-sort history by updated_at after inserting the moved row */
    sortHistory();
}

/* ── Stats counters ── */
function updateStats() {
    const all = Object.values(HD_DATA);
    const el  = id => document.getElementById(id);
    if(el('statPending'))  el('statPending').textContent  = all.filter(r=>r.status==='PENDING').length;
    if(el('statApproved')) el('statApproved').textContent = all.filter(r=>r.status==='APPROVED').length;
    if(el('statRejected')) el('statRejected').textContent = all.filter(r=>r.status==='REJECTED').length;
    if(el('statTotal'))    el('statTotal').textContent    = all.length;
}

function capitalize(str) {
    return str ? str.charAt(0).toUpperCase()+str.slice(1).toLowerCase() : '';
}

/* ── Confirm modal ── */
function askConfirm(hdId, newStatus) {
    const isApprove = newStatus==='APPROVED';
    document.getElementById('confirmIconWrap').style.background = isApprove ? '#dcfce7' : '#fee2e2';
    document.getElementById('confirmIconWrap').innerHTML =
        `<svg style="width:22px;height:22px;" fill="none" stroke="${isApprove?'#16a34a':'#dc2626'}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="${isApprove?'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z':'M6 18L18 6M6 6l12 12'}"/>
        </svg>`;
    document.getElementById('confirmTitle').textContent = isApprove
        ? 'Approve this Application?' : 'Reject this Application?';
    document.getElementById('confirmDesc').textContent = isApprove
        ? 'This will approve the half day and deduct 0.5 days from leave credits.'
        : 'This will reject the half day. No leave credits will be deducted.';
    document.getElementById('rejectReasonWrap').style.display = isApprove ? 'none' : 'block';
    if (!isApprove) document.getElementById('rejectReason').value = '';
    const ok = document.getElementById('confirmOkBtn');
    ok.textContent = isApprove ? '✓ Yes, Approve' : '✕ Yes, Reject';
    ok.className   = isApprove ? 'btn-approve' : 'btn-reject';
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
    pendingConfirmAction = null;
}

function executeConfirmAction() {
    if (!pendingConfirmAction) return;
    const { hdId, newStatus } = pendingConfirmAction;
    const reason = newStatus==='REJECTED' ? document.getElementById('rejectReason').value.trim() : null;
    closeConfirmModal();
    doStatusUpdate(hdId, newStatus, reason);
}

/* ── Detail Panel ── */
function openDetailPanel(hdId, e) {
    if (e) e.stopPropagation();
    const d = HD_DATA[hdId];
    if (!d) return;
    activePanelId = hdId;

    document.getElementById('dpTitle').textContent    = 'Half Day Application Details';
    document.getElementById('dpSubtitle').textContent = d.orphan
        ? `Record #${hdId} · No Employee Linked · ${d.application_date}`
        : `${d.last_name}, ${d.first_name} · ${d.application_date} · ${d.period}`;


    const isLocked = d.orphan || ['APPROVED','REJECTED','CANCELLED'].includes(d.status);
    const sMap = { PENDING:'#fef9c3|#854d0e',APPROVED:'#dcfce7|#14532d',REJECTED:'#fee2e2|#991b1b',CANCELLED:'#f3f4f6|#6b7280' };
    const [sBg,sC] = (sMap[d.status]||'#f3f4f6|#6b7280').split('|');
    const periodBg = d.period==='AM'?'#dbeafe':'#ede9fe';
    const periodC  = d.period==='AM'?'#1e40af':'#5b21b6';
    const mi = d.middle_name ? d.middle_name.charAt(0).toUpperCase()+'.' : '';

    const orphanBanner = d.orphan ? `
        <div class="dp-card" style="background:#fff7ed;border:1px solid #fed7aa;">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <svg style="width:18px;height:18px;color:#ea580c;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#9a3412;margin:0 0 4px;">No Employee Linked</p>
                    <p style="font-size:12px;color:#c2410c;margin:0;">This record has no associated employee and cannot be approved.</p>
                </div>
            </div>
        </div>` : '';

    let statusNote = '';
    if (d.status==='APPROVED') {
        statusNote = `<div style="display:flex;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-top:12px;">
            <svg style="width:14px;height:14px;color:#16a34a;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p style="font-size:12px;color:#15803d;margin:0;font-weight:600;">Approved on ${d.approved_date} — 0.5 days deducted from ${d.leave_type} credits.</p>
        </div>`;
    } else if (d.status==='REJECTED') {
        statusNote = `<div style="display:flex;gap:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-top:12px;">
            <svg style="width:14px;height:14px;color:#dc2626;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <p style="font-size:12px;color:#991b1b;margin:0;font-weight:600;">Rejected — no leave credit was deducted.</p>
        </div>`;
    } else if (d.status==='CANCELLED') {
        statusNote = `<div style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;margin-top:12px;">
            <p style="font-size:12px;color:#6b7280;margin:0;font-weight:600;">This application has been cancelled.</p>
        </div>`;
    } else if (!d.orphan) {
        statusNote = `<div style="display:flex;gap:8px;background:#fefce8;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-top:12px;">
            <svg style="width:14px;height:14px;color:#ca8a04;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p style="font-size:12px;color:#92400e;margin:0;font-weight:600;">Awaiting action — approving will deduct 0.5 days from ${d.leave_type} credits.</p>
        </div>`;
    }

    document.getElementById('dpBody').innerHTML = `
        ${orphanBanner}
        <div class="dp-card">
            <div class="dp-section-heading">
                <div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                <p class="dp-section-title">Employee Information</p>
            </div>
            <div class="dp-grid">
                <div class="dp-field"><label>Full Name</label><p>${d.orphan ? '<em style="color:#9ca3af;">No employee linked</em>' : d.last_name+', '+d.first_name+' '+mi}</p></div>
                <div class="dp-field"><label>Employee ID</label><p style="font-family:monospace;">${d.employee_id_fmt}</p></div>
                <div class="dp-field"><label>Position</label><p>${d.position}</p></div>
                <div class="dp-field"><label>Department</label><p>${d.department}</p></div>
            </div>
        </div>
        <div class="dp-card">
            <div class="dp-section-heading">
                <div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <p class="dp-section-title">Application Details</p>
            </div>
            <div class="dp-grid">
                <div class="dp-field"><label>Half Day ID</label><p style="font-family:monospace;color:#2d5a1b;font-weight:700;">#${hdId}</p></div>
                <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
                <div class="dp-field"><label>Deduction</label><p style="color:#2d5a1b;font-weight:700;">− 0.5 days</p></div>
                <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>
                <div class="dp-field"><label>Time Period</label>
                    <p><span style="display:inline-flex;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${periodBg};color:${periodC};">${d.period || '—'}</span></p>
                </div>
                <div class="dp-field"><label>Approved Date</label><p>${d.approved_date}</p></div>
                <div class="dp-field"><label>Approved By</label><p>${d.approved_by || '—'}</p></div>
                <div class="dp-field"><label>Current Status</label>
                    <p><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${d.status}</span></p>
                </div>
                ${d.reason ? `<div class="dp-field span2"><label>Reason</label><p>${d.reason}</p></div>` : ''}
            </div>
            ${statusNote}
        </div>
        <div style="height:8px;"></div>`;

    const btns = document.getElementById('dpActionBtns');
    if (d.orphan) {
        btns.innerHTML = `<span style="font-size:11px;color:#ea580c;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            No employee linked — cannot process
        </span>`;
    } else if (isLocked) {
        btns.innerHTML = `<span style="font-size:11px;color:#9ca3af;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2v1H10v-1c0-1.1.9-2 2-2zm-4 9h8a2 2 0 002-2v-5H6v5a2 2 0 002 2z"/></svg>
            Status is final
        </span>`;
    } else {
        btns.innerHTML = `<button class="btn-reject"  onclick="askConfirmFromPanel(${hdId},'REJECTED')">✕ Reject</button>
                          <button class="btn-approve" onclick="askConfirmFromPanel(${hdId},'APPROVED')">✓ Approve</button>`;
    }

    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function askConfirmFromPanel(hdId, newStatus) {
    pendingConfirmAction = { hdId, newStatus };
    askConfirm(hdId, newStatus);
}

function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
    activePanelId = null;
}


function toggleMenu(btn) {
    const dd = btn.nextElementSibling;
    document.querySelectorAll('.action-dropdown.open').forEach(d => { if(d!==dd) d.classList.remove('open'); });
    dd.classList.toggle('open');
}

/* ── Certification modal ── */
let fmCurrentScale=1, fmDefaultScale=1;
function fmApplyScale(s) {
    fmCurrentScale = Math.min(2, Math.max(0.2, s));
    const f = document.getElementById('formFrame');
    if (!f) return;
    f.style.transform    = `scale(${fmCurrentScale})`;
    f.style.marginBottom = `calc(1200px * (${fmCurrentScale} - 1))`;
    f.style.marginRight  = `calc(820px * (${fmCurrentScale} - 1))`;
    const l = document.getElementById('fmZoomLabel');
    if (l) l.textContent = Math.round(fmCurrentScale*100)+'%';
}
function fmZoom(d) { fmApplyScale(fmCurrentScale+d); }
function fmZoomReset() { fmApplyScale(fmDefaultScale); }

function viewCert(id) {
    const d = HD_DATA[id];
    if (!d || d.orphan) return;
    document.getElementById('fmTopbarLabel').textContent = `${d.last_name}, ${d.first_name} — Half Day Certification`;
    document.getElementById('fmTopbarSub').textContent   = `${d.leave_type} · ${d.period} · ${d.application_date} · ${d.status}`;
    document.getElementById('fmLoading').classList.remove('hidden');
    document.getElementById('formFrame').src = `${CERT_URL}/${id}/cert`;
    document.getElementById('formModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    if (window.innerWidth<=767) {
        const scale = parseFloat((window.innerWidth/820).toFixed(4));
        fmDefaultScale = scale; fmApplyScale(scale);
    }
}

function closeFormModal() {
    document.getElementById('formModal').classList.remove('show');
    document.body.style.overflow = '';
    setTimeout(()=>{
        document.getElementById('formFrame').src='';
        document.getElementById('fmLoading').classList.remove('hidden');
    },280);
}

function printFormModal() {
    const f = document.getElementById('formFrame');
    try { f.contentWindow.focus(); f.contentWindow.print(); }
    catch(e) { window.open(f.src,'_blank'); }
}

/* ── Toast ── */
function showToast(title, msg, type='success') {
    const m = {
        success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},
        error:  {bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},
        warning:{bg:'#fef9c3',c:'#ca8a04',p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'},
        info:   {bg:'#dbeafe',c:'#2563eb',p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'},
    };
    const s = m[type]||m.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    document.getElementById('toastIcon').innerHTML =
        `<svg style="width:18px;height:18px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    document.getElementById('toastIcon').style.background = s.bg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),3500);
}

/* ── Keyboard shortcuts ── */
document.addEventListener('keydown', e => {
    if (e.key==='Escape') { closeFormModal(); closeDetailPanel(); closeConfirmModal(); }
});

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    sortTable();
    sortTableByUpdatedAt();
    filterRows();
    sortHistory();

    const histBody = document.getElementById('historyTableBody');
    const noResultsRow = document.createElement('tr');
    noResultsRow.id = 'historyNoResults';
    noResultsRow.style.display = 'none';
    noResultsRow.innerHTML = `<td colspan="11" style="padding:48px 24px;text-align:center;color:#9ca3af;font-size:13px;">
        No records match your filter.</td>`;
    histBody.appendChild(noResultsRow);

    const hl = new URLSearchParams(window.location.search).get('highlight');
    if (hl && HD_DATA[hl]) setTimeout(()=>openDetailPanel(parseInt(hl),null),400);

    const tabParam = new URLSearchParams(window.location.search).get('tab');
    if (tabParam === 'history') switchTab('history');
});
</script>
@endsection