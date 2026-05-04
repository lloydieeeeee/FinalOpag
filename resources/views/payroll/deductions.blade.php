@extends('layouts.app')
@section('title', 'Payroll Components')
@section('page-title', 'Payroll')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════════════════
   PAYROLL COMPONENTS — Group Tabs Layout
═══════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --page-bg:       #f4f6f9;
    --card-bg:       #ffffff;
    --border:        #e8eaed;
    --border-light:  #f1f3f5;

    --txt-primary:   #1c2128;
    --txt-secondary: #5a6472;
    --txt-muted:     #9aa5b4;

    --brand:         #1a3a1a;
    --brand-mid:     #2d5a1b;
    --brand-light:   #edf7ee;
    --brand-border:  #c3e6c3;

    --ded-bg:        #fff8f5;
    --ded-border:    #ffd4c2;
    --ded-accent:    #c0411a;
    --ded-soft:      #fee8de;
    --ded-text:      #7c2100;

    --add-bg:        #f3fbfa;
    --add-border:    #b6e8e2;
    --add-accent:    #0d7b6b;
    --add-soft:      #d1f5f0;
    --add-text:      #065047;

    --govt-bg:       #f5f3ff;
    --govt-border:   #d4c9ff;
    --govt-accent:   #5b3fc8;
    --govt-soft:     #e8e2ff;
    --govt-text:     #341f82;

    --radius-sm:  6px;
    --radius-md:  10px;
    --radius-lg:  14px;
    --radius-xl:  18px;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:  0 4px 16px rgba(0,0,0,.08);
    --shadow-lg:  0 12px 40px rgba(0,0,0,.14);

    --font: 'DM Sans', sans-serif;
    --mono: 'DM Mono', monospace;
}

body, .ded-page { font-family: var(--font); }

/* ─── PAGE SHELL ─────────────────────────────── */
.ded-page {
    display: flex;
    flex-direction: column;
    gap: 0;
}

/* ─── BREADCRUMB ─────────────────────────────── */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--txt-muted);
    margin-bottom: 12px;
}
.breadcrumb a { color: var(--txt-muted); text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: var(--brand); }
.breadcrumb .sep { color: var(--border); }
.breadcrumb .current { color: var(--txt-primary); font-weight: 600; }

/* ─── STAT CARDS ─────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 16px;
}
.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}
.stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.stat-card.s-total::after  { background: linear-gradient(90deg, #6b7280, #9ca3af); }
.stat-card.s-ded::after    { background: linear-gradient(90deg, var(--ded-accent), #e05b2b); }
.stat-card.s-add::after    { background: linear-gradient(90deg, var(--add-accent), #13a896); }
.stat-card.s-active::after { background: linear-gradient(90deg, var(--brand), var(--brand-mid)); }
.stat-icon {
    width: 38px; height: 38px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 17px;
}
.s-total  .stat-icon { background: #f3f4f6; }
.s-ded    .stat-icon { background: var(--ded-soft); }
.s-add    .stat-icon { background: var(--add-soft); }
.s-active .stat-icon { background: var(--brand-light); }
.stat-val { font-size: 22px; font-weight: 700; color: var(--txt-primary); line-height: 1; font-variant-numeric: tabular-nums; }
.stat-lbl { font-size: 11px; color: var(--txt-muted); margin-top: 3px; font-weight: 500; }

/* ─── MAIN CARD ──────────────────────────────── */
.app-card {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

/* ─── TOOLBAR ────────────────────────────────── */
.toolbar {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    background: #fafbfc;
}
.toolbar-left h2 { font-size: 14px; font-weight: 700; color: var(--txt-primary); margin-bottom: 1px; }
.toolbar-left p  { font-size: 11px; color: var(--txt-muted); }
.toolbar-right   { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.search-wrap { position: relative; }
.search-wrap input {
    padding: 8px 12px 8px 34px;
    font-size: 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    background: #fafbfc;
    color: var(--txt-primary);
    outline: none;
    width: 210px;
    transition: all .15s;
    font-family: var(--font);
}
.search-wrap input:focus { border-color: var(--brand-mid); background: #fff; box-shadow: 0 0 0 3px var(--brand-light); }
.search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--txt-muted); pointer-events: none; }

.btn-add-ded {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 15px; font-size: 12px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #c0411a, #e05b2b);
    border: none; border-radius: var(--radius-md); cursor: pointer;
    transition: all .15s; font-family: var(--font);
    box-shadow: 0 2px 6px rgba(192,65,26,.35);
}
.btn-add-ded:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(192,65,26,.4); }

.btn-add-add {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 15px; font-size: 12px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, var(--add-accent), #13a896);
    border: none; border-radius: var(--radius-md); cursor: pointer;
    transition: all .15s; font-family: var(--font);
    box-shadow: 0 2px 6px rgba(13,123,107,.3);
}
.btn-add-add:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,123,107,.38); }

/* ─── GROUP TABS CONTAINER ───────────────────── */
.group-tabs-wrapper {
    display: flex;
    min-height: 0;
}

/* ─── LEFT: GROUP TAB LIST ───────────────────── */
.group-tab-list {
    width: 240px;
    min-width: 200px;
    flex-shrink: 0;
    border-right: 1px solid var(--border-light);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
    background: #fafbfc;
    max-height: calc(100vh - 280px);
}
.group-tab-list::-webkit-scrollbar { width: 4px; }
.group-tab-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

/* Filter pills inside left panel */
.gtl-filter {
    padding: 10px 12px 8px;
    display: flex;
    gap: 5px;
    border-bottom: 1px solid var(--border-light);
    background: #fff;
    flex-wrap: wrap;
}
.filter-pill {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 700;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--txt-muted);
    cursor: pointer;
    transition: all .15s;
    font-family: var(--font);
}
.filter-pill.active-all  { background: var(--txt-primary); border-color: var(--txt-primary); color: #fff; }
.filter-pill.active-ded  { background: var(--ded-soft); border-color: var(--ded-accent); color: var(--ded-accent); }
.filter-pill.active-add  { background: var(--add-soft); border-color: var(--add-accent); color: var(--add-accent); }

/* Individual group tab button */
.group-tab-btn {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 11px 14px;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
    gap: 10px;
    transition: background .12s;
    border-bottom: 1px solid var(--border-light);
    position: relative;
    font-family: var(--font);
}
.group-tab-btn:hover { background: #f3f4f6; }
.group-tab-btn.active { background: #fff; }
.group-tab-btn.active::after {
    content: '';
    position: absolute;
    right: 0; top: 0; bottom: 0;
    width: 3px;
    border-radius: 3px 0 0 3px;
}
.group-tab-btn.active.kind-ded::after { background: var(--ded-accent); }
.group-tab-btn.active.kind-add::after { background: var(--add-accent); }

.gtb-index {
    font-size: 10px;
    color: var(--txt-muted);
    font-variant-numeric: tabular-nums;
    width: 16px;
    flex-shrink: 0;
    text-align: right;
}
.gtb-icon {
    width: 30px; height: 30px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.kind-ded .gtb-icon { background: var(--ded-soft); }
.kind-add .gtb-icon { background: var(--add-soft); }

.gtb-info { flex: 1; min-width: 0; }
.gtb-name {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--txt-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.group-tab-btn.active.kind-ded .gtb-name { color: var(--ded-text); }
.group-tab-btn.active.kind-add .gtb-name { color: var(--add-text); }

.gtb-meta {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 2px;
}
.gtb-pill {
    font-size: 9px;
    font-weight: 800;
    border-radius: 4px;
    padding: 1px 5px;
    text-transform: uppercase;
    letter-spacing: .06em;
}
.kind-ded .gtb-pill { background: var(--ded-soft); color: var(--ded-accent); }
.kind-add .gtb-pill { background: var(--add-soft); color: var(--add-accent); }
.gtb-count {
    font-size: 10px;
    color: var(--txt-muted);
    font-weight: 500;
}

/* ─── RIGHT: PANEL ───────────────────────────── */
.group-panel-area {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* Panel header */
.panel-header {
    padding: 16px 20px 12px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.panel-title-wrap { display: flex; align-items: center; gap: 10px; }
.panel-kind-badge {
    font-size: 10px;
    font-weight: 800;
    border-radius: 6px;
    padding: 3px 9px;
    text-transform: uppercase;
    letter-spacing: .07em;
}
.panel-kind-ded { background: var(--ded-soft); color: var(--ded-accent); }
.panel-kind-add { background: var(--add-soft); color: var(--add-accent); }
.panel-name { font-size: 16px; font-weight: 800; color: var(--txt-primary); }
.panel-sub  { font-size: 11px; color: var(--txt-muted); margin-top: 1px; }
.panel-actions { display: flex; align-items: center; gap: 7px; }

/* Panel body — scrollable */
.panel-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
    max-height: calc(100vh - 340px);
}
.panel-body::-webkit-scrollbar { width: 5px; }
.panel-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

/* ─── CHILD TABLE ────────────────────────────── */
.child-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    min-width: 560px;
}
.child-table thead tr {
    background: #fafbfc;
    border-bottom: 1px solid var(--border);
}
.child-table th {
    padding: 9px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--txt-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    white-space: nowrap;
}
.child-table td {
    padding: 11px 16px;
    border-bottom: 1px solid var(--border-light);
    color: var(--txt-secondary);
    vertical-align: middle;
}
.child-table tbody tr:hover td { background: #fafbfc; }
.child-table tbody tr.inactive { opacity: .45; }

/* Empty panel */
.panel-empty {
    padding: 60px 24px;
    text-align: center;
    color: var(--txt-muted);
}
.panel-empty-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: var(--border-light);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; font-size: 22px;
}
.panel-empty p     { font-size: 13px; font-weight: 600; color: var(--txt-secondary); margin-bottom: 4px; }
.panel-empty small { font-size: 11px; }

/* No group selected splash */
.panel-splash {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 24px;
    text-align: center;
    color: var(--txt-muted);
}
.panel-splash-icon {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: var(--border-light);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 28px;
}
.panel-splash h3 { font-size: 14px; font-weight: 700; color: var(--txt-secondary); margin-bottom: 5px; }
.panel-splash p  { font-size: 12px; }

/* ─── BADGES ─────────────────────────────────── */
.badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10.5px; font-weight: 700; white-space: nowrap;
}
.badge-fixed    { background: #dbeafe; color: #1e40af; }
.badge-typable  { background: #fef9c3; color: #854d0e; }
.badge-inactive { background: var(--border-light); color: var(--txt-muted); }
.badge-active   { background: var(--brand-light); color: var(--brand); }
.badge-tiered   { background: #ede9fe; color: #5b21b6; }

.impact {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10.5px; font-weight: 700; white-space: nowrap;
}
.impact-minus { background: var(--ded-soft); color: var(--ded-accent); }
.impact-govt  { background: var(--govt-soft); color: var(--govt-accent); }
.impact-plus  { background: var(--add-soft); color: var(--add-accent); }

.rate-val { font-family: var(--mono); font-size: 12px; font-weight: 500; color: var(--txt-primary); }

/* ─── ACTION BUTTONS ─────────────────────────── */
.action-cell { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }
.icon-btn {
    display: flex; align-items: center; justify-content: center;
    width: 28px; height: 28px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: #fff; cursor: pointer; color: var(--txt-muted);
    transition: all .15s;
}
.icon-btn:hover           { border-color: #9ca3af; color: var(--txt-secondary); background: #f9fafb; }
.icon-btn.edit:hover      { border-color: var(--brand-mid); color: var(--brand); background: var(--brand-light); }
.icon-btn.del:hover       { border-color: #dc2626; color: #dc2626; background: #fff1f2; }

/* ─── BTN SMALL ──────────────────────────────── */
.btn-sm {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px; font-size: 11.5px; font-weight: 700;
    border: none; border-radius: var(--radius-md); cursor: pointer;
    transition: all .15s; font-family: var(--font); color: #fff;
}
.btn-sm-ded { background: linear-gradient(135deg, #c0411a, #e05b2b); box-shadow: 0 2px 6px rgba(192,65,26,.3); }
.btn-sm-ded:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(192,65,26,.4); }
.btn-sm-add { background: linear-gradient(135deg, var(--add-accent), #13a896); box-shadow: 0 2px 6px rgba(13,123,107,.25); }
.btn-sm-add:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(13,123,107,.35); }

/* Edit group btn */
.btn-edit-group {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 12px; font-size: 11.5px; font-weight: 600;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
    background: #fff; color: var(--txt-secondary); cursor: pointer;
    transition: all .15s; font-family: var(--font);
}
.btn-edit-group:hover { border-color: var(--brand-mid); color: var(--brand); background: var(--brand-light); }

/* Delete group btn */
.btn-del-group {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 12px; font-size: 11.5px; font-weight: 600;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
    background: #fff; color: var(--txt-secondary); cursor: pointer;
    transition: all .15s; font-family: var(--font);
}
.btn-del-group:hover { border-color: #dc2626; color: #dc2626; background: #fff1f2; }

/* ─── NO GROUPS EMPTY STATE ──────────────────── */
.no-groups-state {
    padding: 80px 24px;
    text-align: center;
    color: var(--txt-muted);
}
.no-groups-icon {
    width: 60px; height: 60px;
    border-radius: 16px;
    background: var(--border-light);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px; font-size: 26px;
}
.no-groups-state p     { font-size: 13px; font-weight: 600; color: var(--txt-secondary); margin-bottom: 4px; }
.no-groups-state small { font-size: 11px; }

/* ─── MODAL BACKDROP ─────────────────────────── */
.modal-bg {
    position: fixed; inset: 0; z-index: 300;
    background: rgba(10,15,20,.55);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity .2s; padding: 16px;
}
.modal-bg.show { opacity: 1; pointer-events: all; }

.modal-card {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    width: min(98vw, 540px);
    max-height: 92vh;
    overflow: hidden;
    display: flex; flex-direction: column;
    box-shadow: 0 32px 80px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.06);
    transform: scale(.93) translateY(14px);
    transition: transform .3s cubic-bezier(.34,1.56,.64,1);
}
.modal-bg.show .modal-card { transform: scale(1) translateY(0); }

.modal-head {
    padding: 20px 22px 18px;
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-shrink: 0; border-bottom: 1px solid var(--border-light);
}
.modal-head.kind-deduction { background: linear-gradient(135deg, #3d1205, #7c2100); }
.modal-head.kind-addition  { background: linear-gradient(135deg, #013b33, #065047); }
.modal-head.kind-default   { background: linear-gradient(135deg, var(--brand), var(--brand-mid)); }
.modal-head h3 { font-size: 15px; font-weight: 700; color: #fff; }
.modal-head p  { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 2px; }
.modal-close {
    background: rgba(255,255,255,.15); border: none;
    width: 30px; height: 30px; border-radius: 50%; color: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 15px; transition: background .15s; flex-shrink: 0;
}
.modal-close:hover { background: rgba(255,255,255,.28); }

.modal-body {
    padding: 20px 22px; overflow-y: auto; flex: 1;
    display: flex; flex-direction: column; gap: 14px;
}
.modal-foot {
    padding: 14px 22px; border-top: 1px solid var(--border-light);
    display: flex; gap: 10px; justify-content: flex-end;
    flex-shrink: 0; background: #fafbfc;
}

/* ─── FORM ELEMENTS ──────────────────────────── */
.f-label {
    display: block; font-size: 10px; font-weight: 700;
    color: var(--txt-muted); text-transform: uppercase;
    letter-spacing: .07em; margin-bottom: 5px;
}
.f-input {
    width: 100%; padding: 9px 12px; font-size: 13px;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
    background: #fafbfc; color: var(--txt-primary);
    outline: none; transition: all .15s; font-family: var(--font);
}
.f-input:focus { border-color: var(--brand-mid); background: #fff; box-shadow: 0 0 0 3px var(--brand-light); }
.f-select {
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239aa5b4' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
    padding-right: 30px; cursor: pointer;
}
.f-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.f-hint { font-size: 10px; color: var(--txt-muted); margin-top: 3px; }
.f-err  { font-size: 10px; color: #dc2626; margin-top: 3px; display: none; }

.kind-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.kind-option {
    border: 2px solid var(--border); border-radius: var(--radius-md);
    padding: 12px 14px; cursor: pointer; transition: all .15s;
    display: flex; align-items: center; gap: 10px; background: #fafbfc;
}
.kind-option input[type=radio] { display: none; }
.kind-option.selected-ded { border-color: var(--ded-accent); background: var(--ded-bg); box-shadow: 0 0 0 3px var(--ded-soft); }
.kind-option.selected-add { border-color: var(--add-accent); background: var(--add-bg); box-shadow: 0 0 0 3px var(--add-soft); }
.kind-icon { width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.kind-ded .kind-icon { background: var(--ded-soft); }
.kind-add .kind-icon { background: var(--add-soft); }
.kind-title { font-size: 12px; font-weight: 700; color: var(--txt-primary); }
.kind-desc  { font-size: 10px; color: var(--txt-muted); margin-top: 1px; }

.toggle-wrap {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; background: #fafbfc;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
}
.toggle-wrap label { font-size: 12px; font-weight: 600; color: var(--txt-secondary); flex: 1; cursor: pointer; }
.toggle-wrap .toggle-desc { font-size: 10px; color: var(--txt-muted); display: block; margin-top: 1px; }
.switch { position: relative; width: 40px; height: 22px; flex-shrink: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.switch .slider { position: absolute; inset: 0; background: var(--border); border-radius: 22px; transition: background .2s; cursor: pointer; }
.switch .slider::before { content:''; position: absolute; left: 3px; bottom: 3px; width: 16px; height: 16px; background: #fff; border-radius: 50%; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.switch input:checked + .slider { background: #16a34a; }
.switch input:checked + .slider::before { transform: translateX(18px); }

.parent-banner {
    background: var(--brand-light); border: 1px solid var(--brand-border);
    border-radius: var(--radius-md); padding: 10px 14px;
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--brand); font-weight: 600;
}

.pagibig-info {
    background: #ede9fe; border: 1px solid #c4b5fd;
    border-radius: var(--radius-md); padding: 10px 14px;
    font-size: 11px; color: #5b21b6; line-height: 1.5;
}
.pagibig-info strong { font-weight: 700; }

.preview-box { border-radius: var(--radius-md); padding: 14px 16px; font-size: 12px; }
.preview-box.kind-ded { background: var(--ded-bg); border: 1px solid var(--ded-border); }
.preview-box.kind-add { background: var(--add-bg); border: 1px solid var(--add-border); }
.preview-box.kind-neutral { background: var(--brand-light); border: 1px solid var(--brand-border); }
.preview-head { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 10px; }
.kind-ded .preview-head { color: var(--ded-accent); }
.kind-add .preview-head { color: var(--add-accent); }
.preview-box.kind-neutral .preview-head { color: var(--brand); }
.p-row { display: flex; justify-content: space-between; padding: 3px 0; }
.p-lbl { color: var(--txt-muted); }
.p-val { font-weight: 700; color: var(--txt-primary); }

.btn-cancel {
    padding: 9px 18px; font-size: 12px; font-weight: 600;
    border: 1.5px solid var(--border); border-radius: var(--radius-md);
    color: var(--txt-secondary); background: #fff; cursor: pointer;
    transition: all .15s; font-family: var(--font);
}
.btn-cancel:hover { border-color: #9ca3af; }
.btn-save {
    padding: 9px 22px; font-size: 12px; font-weight: 700;
    border: none; border-radius: var(--radius-md); color: #fff;
    background: var(--brand); cursor: pointer;
    transition: all .15s; font-family: var(--font);
}
.btn-save:hover { background: var(--brand-mid); }
.btn-save:disabled { background: var(--txt-muted); cursor: not-allowed; }

/* ─── CONFIRM MODAL ──────────────────────────── */
.confirm-modal {
    position: fixed; inset: 0; z-index: 400;
    background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity .2s; padding: 16px;
}
.confirm-modal.show { opacity: 1; pointer-events: all; }
.confirm-card {
    background: var(--card-bg); border-radius: var(--radius-xl);
    padding: 26px; width: min(98vw, 420px); box-shadow: var(--shadow-lg);
    transform: scale(.93);
    transition: transform .26s cubic-bezier(.34,1.56,.64,1);
}
.confirm-modal.show .confirm-card { transform: scale(1); }

/* ─── TOAST ──────────────────────────────────── */
#toast {
    position: fixed; bottom: 22px; right: 22px; z-index: 999;
    background: var(--card-bg); border-radius: var(--radius-lg);
    padding: 12px 16px; box-shadow: var(--shadow-lg), 0 0 0 1px var(--border);
    display: flex; align-items: center; gap: 12px;
    min-width: 220px; max-width: calc(100vw - 44px);
    opacity: 0; transform: translateY(14px);
    transition: all .3s; pointer-events: none;
}
#toast.show { opacity: 1; transform: translateY(0); }

/* ─── RESPONSIVE ─────────────────────────────── */
@media (max-width: 900px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .group-tabs-wrapper { flex-direction: column; }
    .group-tab-list { width: 100%; max-height: none; border-right: none; border-bottom: 1px solid var(--border-light); display: flex; flex-direction: row; overflow-x: auto; overflow-y: hidden; }
    .group-tab-btn { border-bottom: none; border-right: 1px solid var(--border-light); min-width: 140px; }
    .group-tab-btn.active::after { top: auto; bottom: 0; left: 0; right: 0; width: auto; height: 3px; }
    .panel-body { max-height: 400px; }
}
@media (max-width: 640px) {
    .toolbar { flex-direction: column; align-items: stretch; }
    .toolbar-right { flex-direction: column; }
    .search-wrap input { width: 100%; }
    .f-row2, .kind-selector { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: 1fr 1fr; }
}
</style>

@php
    $statTotal      = $stats['total']      ?? $stats['total_groups']      ?? 0;
    $statDeductions = $stats['deductions'] ?? $stats['deduction_groups']  ?? 0;
    $statAdditions  = $stats['additions']  ?? $stats['addition_groups']   ?? 0;
    $statActive     = $stats['active']     ?? $stats['active_components'] ?? 0;
    $search         = $search              ?? request('search', '');

    $pagibigEeId = 22;
    $pagibigErId = 23;
@endphp

<div class="ded-page">

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">Payroll Components</span>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-card s-total">
        <div class="stat-icon">📋</div>
        <div>
            <div class="stat-val">{{ $statTotal }}</div>
            <div class="stat-lbl">Total Groups</div>
        </div>
    </div>
    <div class="stat-card s-ded">
        <div class="stat-icon">➖</div>
        <div>
            <div class="stat-val">{{ $statDeductions }}</div>
            <div class="stat-lbl">Deduction Groups</div>
        </div>
    </div>
    <div class="stat-card s-add">
        <div class="stat-icon">➕</div>
        <div>
            <div class="stat-val">{{ $statAdditions }}</div>
            <div class="stat-lbl">Addition Groups</div>
        </div>
    </div>
    <div class="stat-card s-active">
        <div class="stat-icon">✅</div>
        <div>
            <div class="stat-val">{{ $statActive }}</div>
            <div class="stat-lbl">Active Components</div>
        </div>
    </div>
</div>

<div class="app-card">

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <h2>Payroll Components</h2>
            <p>Select a group on the left to view and manage its sub-items</p>
        </div>
        <div class="toolbar-right">
            <div class="search-wrap">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Search groups…" oninput="filterGroupTabs(this.value)">
            </div>
            <button class="btn-add-ded" onclick="openAddGroupModal('deduction')">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Deduction
            </button>
            <button class="btn-add-add" onclick="openAddGroupModal('addition')">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Addition
            </button>
        </div>
    </div>

    {{-- Group Tabs Layout --}}
    <div class="group-tabs-wrapper">

        {{-- LEFT: Group List --}}
        <div class="group-tab-list" id="groupTabList">

            {{-- Filter pills --}}
            <div class="gtl-filter">
                <button class="filter-pill active-all" id="pill-all" onclick="setKindFilter('all')">All</button>
                <button class="filter-pill" id="pill-ded" onclick="setKindFilter('deduction')">➖ Deductions</button>
                <button class="filter-pill" id="pill-add" onclick="setKindFilter('addition')">➕ Additions</button>
            </div>

            @forelse($groups as $gi => $group)
            @php $isAdd = $group->entry_kind === 'addition'; @endphp
            <button
                class="group-tab-btn {{ $isAdd ? 'kind-add' : 'kind-ded' }}"
                id="gtab-{{ $group->id }}"
                data-group-id="{{ $group->id }}"
                data-kind="{{ $group->entry_kind }}"
                data-name="{{ strtolower($group->name) }}"
                onclick="selectGroupTab({{ $group->id }})"
            >
                <span class="gtb-index">{{ $gi + 1 }}</span>
                <div class="gtb-icon">{{ $isAdd ? '➕' : '➖' }}</div>
                <div class="gtb-info">
                    <div class="gtb-name">{{ $group->name }}</div>
                    <div class="gtb-meta">
                        <span class="gtb-pill">{{ $isAdd ? 'Addition' : 'Deduction' }}</span>
                        <span class="gtb-count">{{ $group->children->count() }} items</span>
                    </div>
                </div>
            </button>
            @empty
            <div style="padding:24px;text-align:center;color:var(--txt-muted);font-size:12px;">No groups yet</div>
            @endforelse
        </div>

        {{-- RIGHT: Panel --}}
        <div class="group-panel-area" id="groupPanelArea">

            {{-- Splash (no group selected) --}}
            <div class="panel-splash" id="panelSplash">
                <div class="panel-splash-icon">📋</div>
                <h3>Select a Group</h3>
                <p>Click any group on the left to view its components</p>
            </div>

            {{-- Dynamic panels per group --}}
            @foreach($groups as $group)
            @php $isAdd = $group->entry_kind === 'addition'; @endphp
            <div id="panel-{{ $group->id }}" style="display:none;flex-direction:column;flex:1;">

                {{-- Panel Header --}}
                <div class="panel-header">
                    <div>
                        <div class="panel-title-wrap">
                            <span class="panel-kind-badge {{ $isAdd ? 'panel-kind-add' : 'panel-kind-ded' }}">
                                {{ $isAdd ? 'Addition' : 'Deduction' }}
                            </span>
                            <span class="panel-name">{{ $group->name }}</span>
                        </div>
                        <div class="panel-sub">{{ $group->children->count() }} sub-item(s) · {{ $group->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <div class="panel-actions">
                        <button class="btn-sm {{ $isAdd ? 'btn-sm-add' : 'btn-sm-ded' }}"
                            onclick="openAddSubModal({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ $group->entry_kind }}')">
                            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Sub-Item
                        </button>
                        <button class="btn-edit-group" onclick="openEditModal({{ $group->toJson() }}, null)">
                            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Group
                        </button>
                        <button class="btn-del-group" onclick="askDelete({{ $group->id }}, '{{ addslashes($group->name) }}')">
                            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Children Table --}}
                <div class="panel-body">
                    @if($group->children->count() > 0)
                    <table class="child-table">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Rate</th>
                                <th>Cap</th>
                                <th>Impact</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($group->children as $ci => $child)
                        @php $isPagibig = in_array($child->id, [$pagibigEeId, $pagibigErId]); @endphp
                        <tr class="{{ !$child->is_active ? 'inactive' : '' }}">
                            <td style="color:var(--txt-muted);font-size:11px;">{{ $ci + 1 }}</td>
                            <td style="font-weight:600;color:var(--txt-primary);">{{ $child->name }}</td>
                            <td>
                                @if($isPagibig)
                                    <span class="badge badge-tiered">Tiered %</span>
                                @elseif($child->type === 'Fixed')
                                    <span class="badge badge-fixed">Fixed</span>
                                @else
                                    <span class="badge badge-typable">Typable</span>
                                @endif
                            </td>
                            <td>
                                @if($child->rate)
                                    <span class="rate-val">{{ $child->rate }}</span>
                                @else
                                    <span style="color:var(--txt-muted);font-size:12px;">—</span>
                                @endif
                            </td>
                            <td style="color:var(--txt-muted);font-size:12px;">
                                @if($child->limit_amount !== null)
                                    <span class="rate-val">₱{{ number_format($child->limit_amount, 2) }}</span>
                                @else
                                    <span style="color:var(--border);">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($child->entry_kind === 'addition')
                                    <span class="impact impact-plus">
                                        <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        Addition
                                    </span>
                                @elseif($child->is_deducted)
                                    <span class="impact impact-minus">
                                        <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                        Employee
                                    </span>
                                @else
                                    <span class="impact impact-govt">
                                        <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5"/></svg>
                                        Gov't Share
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($child->is_active)
                                    <span style="font-size:11px;color:var(--txt-secondary);">Active</span>
                                @else
                                    <span class="badge badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-cell">
                                    <button class="icon-btn edit" title="Edit"
                                        onclick="openEditModal({{ $child->toJson() }}, '{{ addslashes($group->name) }}')">
                                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button class="icon-btn del" title="Delete"
                                        onclick="askDelete({{ $child->id }}, '{{ addslashes($child->name) }}')">
                                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="panel-empty">
                        <div class="panel-empty-icon">📭</div>
                        <p>No sub-items yet</p>
                        <small>Click "Add Sub-Item" above to create one under {{ $group->name }}.</small>
                    </div>
                    @endif
                </div>

            </div>{{-- /panel --}}
            @endforeach

        </div>{{-- /group-panel-area --}}
    </div>{{-- /group-tabs-wrapper --}}

</div>{{-- /app-card --}}
</div>{{-- /ded-page --}}

{{-- ═══════════════════════════════════════
     ADD / EDIT MODAL
═══════════════════════════════════════ --}}
<div id="formModal" class="modal-bg" onclick="if(event.target===this)closeModal()">
    <div class="modal-card">
        <div class="modal-head kind-default" id="modalHead">
            <div>
                <h3 id="modalTitle">Add Component</h3>
                <p id="modalSub">Fill in the details below</p>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body">
            <div id="parentBanner" class="parent-banner" style="display:none;">
                <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                Sub-item under: <strong id="parentBannerName" style="margin-left:3px;"></strong>
            </div>
            <div id="pagibigBanner" class="pagibig-info" style="display:none;">
                🏦 <strong>Pag-IBIG 2025–2026 Tiered Rates (HDMF)</strong><br>
                This component uses automatic tiered computation — rate fields below are for reference only.<br>
                <strong>EE:</strong> 1% if gross &lt; ₱1,500 &nbsp;|&nbsp; 2% if gross ≥ ₱1,500 &nbsp;(max base: ₱10,000)<br>
                <strong>ER:</strong> Always 2% &nbsp;(max base: ₱10,000)
            </div>
            <input type="hidden" id="f_id">
            <input type="hidden" id="f_parent_id">
            <div id="kindSelectorWrap">
                <label class="f-label">Component Kind <span style="color:#dc2626;">*</span></label>
                <div class="kind-selector">
                    <label class="kind-option kind-ded" id="kindOptDed" onclick="selectKind('deduction')">
                        <input type="radio" name="entry_kind" value="deduction" id="f_kind_ded" checked>
                        <div class="kind-icon">➖</div>
                        <div>
                            <div class="kind-title" style="color:var(--ded-accent);">Deduction</div>
                            <div class="kind-desc">Subtracted from gross salary<br>(GSIS, PhilHealth, taxes, loans…)</div>
                        </div>
                    </label>
                    <label class="kind-option kind-add" id="kindOptAdd" onclick="selectKind('addition')">
                        <input type="radio" name="entry_kind" value="addition" id="f_kind_add">
                        <div class="kind-icon">➕</div>
                        <div>
                            <div class="kind-title" style="color:var(--add-accent);">Addition</div>
                            <div class="kind-desc">Added on top of basic salary<br>(PERA, RATA, TA, allowances…)</div>
                        </div>
                    </label>
                </div>
            </div>
            <div>
                <label class="f-label">Name <span style="color:#dc2626;">*</span></label>
                <input id="f_name" class="f-input" type="text" placeholder="e.g. GSIS Employee Share" oninput="updatePreview()">
                <p class="f-err" id="err_name">Name is required.</p>
            </div>
            <div class="f-row2">
                <div>
                    <label class="f-label">Type <span style="color:#dc2626;">*</span></label>
                    <select id="f_type" class="f-input f-select">
                        <option value="Fixed">Fixed (auto-calculated)</option>
                        <option value="Not Fixed">Typable (manual entry)</option>
                    </select>
                </div>
                <div>
                    <label class="f-label">Rate Type</label>
                    <select id="f_rate_type" class="f-input f-select" onchange="updateRateLabel();updatePreview();">
                        <option value="percent">Percentage (%)</option>
                        <option value="flat">Flat Amount (₱)</option>
                    </select>
                </div>
            </div>
            <div class="f-row2">
                <div>
                    <label class="f-label" id="rateLabel">Rate Value (%)</label>
                    <input id="f_rate_value" class="f-input" type="number" step="0.0001" min="0" placeholder="e.g. 9 for 9%" oninput="updatePreview()">
                    <p class="f-hint" id="rateHint">Leave blank for manual entry</p>
                </div>
                <div>
                    <label class="f-label">Limit / Cap (₱) <span style="font-weight:400;color:var(--txt-muted);">optional</span></label>
                    <input id="f_limit" class="f-input" type="number" step="0.01" min="0" placeholder="e.g. 2500" oninput="updatePreview()">
                </div>
            </div>
            <div id="isDeductedWrap" class="toggle-wrap">
                <label for="f_is_deducted">
                    Deduct from employee take-home?
                    <span class="toggle-desc">Turn OFF for government/employer shares that are NOT subtracted from the employee's net pay.</span>
                </label>
                <label class="switch">
                    <input type="checkbox" id="f_is_deducted" checked>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="f-row2">
                <div>
                    <label class="f-label">Status Label <span style="font-weight:400;color:var(--txt-muted);">optional</span></label>
                    <input id="f_status" class="f-input" type="text" placeholder="Auto-filled from name">
                </div>
                <div>
                    <label class="f-label">Sort Order</label>
                    <input id="f_sort" class="f-input" type="number" min="0" placeholder="0">
                </div>
            </div>
            <div id="previewBox" class="preview-box kind-neutral" style="display:none;">
                <div class="preview-head">Preview</div>
                <div class="p-row"><span class="p-lbl">Name</span><span class="p-val" id="prev_name">—</span></div>
                <div class="p-row"><span class="p-lbl">Kind</span><span class="p-val" id="prev_kind">—</span></div>
                <div class="p-row"><span class="p-lbl">Rate</span><span class="p-val" id="prev_rate">—</span></div>
                <div class="p-row"><span class="p-lbl">Cap</span><span class="p-val" id="prev_limit">—</span></div>
                <div class="p-row"><span class="p-lbl">Impact</span><span class="p-val" id="prev_impact">—</span></div>
                <div id="prev_pagibig_rows" style="display:none;margin-top:8px;border-top:1px solid rgba(0,0,0,.07);padding-top:8px;">
                    <div class="p-row"><span class="p-lbl" style="font-style:italic;">Sample: ₱10,000 salary</span><span class="p-val" id="prev_pagibig_10k">—</span></div>
                    <div class="p-row"><span class="p-lbl" style="font-style:italic;">Sample: ₱20,000 salary</span><span class="p-val" id="prev_pagibig_20k">—</span></div>
                    <div class="p-row"><span class="p-lbl" style="font-style:italic;">Sample: ₱1,200 salary</span><span class="p-val" id="prev_pagibig_1200">—</span></div>
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-save" id="saveBtn" onclick="saveDeduction()">💾 Save</button>
        </div>
    </div>
</div>

{{-- DELETE CONFIRM --}}
<div id="confirmModal" class="confirm-modal" onclick="if(event.target===this)closeConfirm()">
    <div class="confirm-card">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px;">🗑</div>
            <div>
                <h3 style="font-size:15px;font-weight:700;color:var(--txt-primary);margin-bottom:3px;">Delete Component?</h3>
                <p style="font-size:12px;color:var(--txt-secondary);" id="confirmName">This cannot be undone.</p>
            </div>
        </div>
        <p style="font-size:11px;color:var(--txt-muted);margin-bottom:18px;">If this is a group, all its sub-items will be unlinked (not deleted).</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeConfirm()" class="btn-cancel">Cancel</button>
            <button onclick="executeDelete()" style="padding:9px 20px;font-size:12px;font-weight:700;color:#fff;background:#dc2626;border:none;border-radius:8px;cursor:pointer;font-family:var(--font);" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                Delete
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast">
    <div id="toastIcon" style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;"></div>
    <div>
        <p id="toastTitle" style="font-size:13px;font-weight:700;color:var(--txt-primary);margin:0;"></p>
        <p id="toastMsg"   style="font-size:11px;color:var(--txt-muted);margin:2px 0 0;"></p>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const PAGIBIG_EE_ID   = {{ $pagibigEeId }};
const PAGIBIG_ER_ID   = {{ $pagibigErId }};
const PAGIBIG_CEILING = 10000;

let editingId  = null;
let deletingId = null;
let activeGroupId = null;
let kindFilter = 'all';

/* ── PAG-IBIG TIERED ── */
function computePagibig(gross, isEmployer = false) {
    const base   = Math.min(gross, PAGIBIG_CEILING);
    const eeRate = gross < 1500 ? 0.01 : 0.02;
    const erRate = 0.02;
    return parseFloat((base * (isEmployer ? erRate : eeRate)).toFixed(2));
}

function isPagibigId(id) {
    return id === PAGIBIG_EE_ID || id === PAGIBIG_ER_ID;
}

/* ── GROUP TAB SELECTION ── */
function selectGroupTab(gid) {
    // Deactivate all tabs
    document.querySelectorAll('.group-tab-btn').forEach(b => b.classList.remove('active'));
    // Hide all panels
    document.querySelectorAll('[id^="panel-"]').forEach(p => p.style.display = 'none');
    // Hide splash
    document.getElementById('panelSplash').style.display = 'none';

    // Activate selected tab
    const tab = document.getElementById('gtab-' + gid);
    if (tab) tab.classList.add('active');

    // Show selected panel
    const panel = document.getElementById('panel-' + gid);
    if (panel) panel.style.display = 'flex';

    activeGroupId = gid;

    // Persist in URL hash
    history.replaceState(null, '', '#group-' + gid);
}

/* ── AUTO-SELECT FROM HASH ── */
(function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#group-')) {
        const gid = parseInt(hash.replace('#group-', ''));
        if (!isNaN(gid)) { selectGroupTab(gid); return; }
    }
    // Auto-select first visible tab
    const firstBtn = document.querySelector('.group-tab-btn');
    if (firstBtn) selectGroupTab(parseInt(firstBtn.dataset.groupId));
})();

/* ── KIND FILTER ── */
function setKindFilter(kind) {
    kindFilter = kind;
    document.getElementById('pill-all').className = 'filter-pill' + (kind === 'all' ? ' active-all' : '');
    document.getElementById('pill-ded').className = 'filter-pill' + (kind === 'deduction' ? ' active-ded' : '');
    document.getElementById('pill-add').className = 'filter-pill' + (kind === 'addition' ? ' active-add' : '');
    applyGroupFilter(document.getElementById('searchInput').value);
}

/* ── SEARCH FILTER ── */
function filterGroupTabs(val) {
    applyGroupFilter(val);
}

function applyGroupFilter(val) {
    const q = (val || '').toLowerCase();
    let firstVisible = null;
    document.querySelectorAll('.group-tab-btn').forEach(btn => {
        const nameMatch = !q || (btn.dataset.name || '').includes(q);
        const kindMatch = kindFilter === 'all' || btn.dataset.kind === kindFilter;
        const visible   = nameMatch && kindMatch;
        btn.style.display = visible ? '' : 'none';
        if (visible && !firstVisible) firstVisible = btn;
    });
    // If current active tab is now hidden, switch to first visible
    if (activeGroupId) {
        const activeBtn = document.getElementById('gtab-' + activeGroupId);
        if (activeBtn && activeBtn.style.display === 'none' && firstVisible) {
            selectGroupTab(parseInt(firstVisible.dataset.groupId));
        }
    }
}

/* ── KIND SELECTOR (modal) ── */
function selectKind(kind) {
    document.getElementById('f_kind_ded').checked = (kind === 'deduction');
    document.getElementById('f_kind_add').checked = (kind === 'addition');
    document.getElementById('kindOptDed').classList.toggle('selected-ded', kind === 'deduction');
    document.getElementById('kindOptAdd').classList.toggle('selected-add', kind === 'addition');
    document.getElementById('isDeductedWrap').style.display = kind === 'addition' ? 'none' : '';
    const head = document.getElementById('modalHead');
    head.className = 'modal-head ' + (kind === 'deduction' ? 'kind-deduction' : 'kind-addition');
    updatePreview();
}

/* ── MODAL OPEN ── */
function openAddGroupModal(kind) {
    editingId = null;
    const isAdd = kind === 'addition';
    document.getElementById('modalTitle').textContent = isAdd ? 'Add Addition Group' : 'Add Deduction Group';
    document.getElementById('modalSub').textContent   = isAdd
        ? 'Creates a new allowance/addition group (e.g. PERA, RATA, TA)'
        : 'Creates a new deduction group (e.g. GSIS, PhilHealth)';
    document.getElementById('parentBanner').style.display  = 'none';
    document.getElementById('pagibigBanner').style.display = 'none';
    document.getElementById('kindSelectorWrap').style.display = '';
    document.getElementById('f_id').value        = '';
    document.getElementById('f_parent_id').value = '';
    resetForm();
    selectKind(kind);
    document.getElementById('formModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('f_name').focus(), 200);
}

function openAddSubModal(parentId, parentName, kind) {
    editingId = null;
    document.getElementById('modalTitle').textContent   = 'Add Sub-Item';
    document.getElementById('modalSub').textContent     = 'Under: ' + parentName;
    document.getElementById('parentBanner').style.display   = 'flex';
    document.getElementById('pagibigBanner').style.display  = 'none';
    document.getElementById('parentBannerName').textContent = parentName;
    document.getElementById('kindSelectorWrap').style.display = '';
    document.getElementById('f_id').value        = '';
    document.getElementById('f_parent_id').value = parentId;
    resetForm();
    selectKind(kind || 'deduction');
    document.getElementById('formModal').classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('f_name').focus(), 200);
}

function openEditModal(d, parentName) {
    editingId = d.id;
    document.getElementById('f_id').value = d.id;
    document.getElementById('modalTitle').textContent = 'Edit Component';
    document.getElementById('modalSub').textContent   = '#' + d.id + ' · ' + d.name;
    document.getElementById('kindSelectorWrap').style.display = '';
    document.getElementById('parentBanner').style.display  = (d.parent_id && parentName) ? 'flex' : 'none';
    if (d.parent_id && parentName) document.getElementById('parentBannerName').textContent = parentName;
    document.getElementById('pagibigBanner').style.display = isPagibigId(d.id) ? '' : 'none';
    document.getElementById('f_parent_id').value     = d.parent_id || '';
    document.getElementById('f_name').value          = d.name || '';
    document.getElementById('f_type').value          = d.type || 'Not Fixed';
    document.getElementById('f_rate_type').value     = d.rate_type || 'flat';
    document.getElementById('f_status').value        = d.status || '';
    document.getElementById('f_sort').value          = d.sort_order || 0;
    document.getElementById('f_limit').value         = d.limit_amount || '';
    document.getElementById('f_is_deducted').checked = (d.is_deducted === true || d.is_deducted === 1);
    const rVal = parseFloat(d.rate_value || 0);
    document.getElementById('f_rate_value').value = d.rate_type === 'percent' ? (rVal * 100).toString() : rVal.toString();
    selectKind(d.entry_kind || 'deduction');
    updateRateLabel();
    updatePreview();
    document.getElementById('formModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('formModal').classList.remove('show');
    document.body.style.overflow = '';
    editingId = null;
}

function resetForm() {
    ['f_name','f_rate_value','f_limit','f_status','f_sort'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    document.getElementById('f_type').value      = 'Not Fixed';
    document.getElementById('f_rate_type').value = 'flat';
    document.getElementById('f_is_deducted').checked = true;
    document.getElementById('previewBox').style.display = 'none';
    document.querySelectorAll('.f-err').forEach(e => e.style.display = 'none');
    updateRateLabel();
}

function updateRateLabel() {
    const isPercent = document.getElementById('f_rate_type').value === 'percent';
    document.getElementById('rateLabel').textContent = 'Rate Value (' + (isPercent ? '%' : '₱') + ')';
    document.getElementById('rateHint').textContent  = isPercent ? 'e.g. 9 for 9%' : 'Leave blank for manual entry';
    document.getElementById('f_rate_value').placeholder = isPercent ? 'e.g. 9' : 'e.g. 2000';
}

function currentKind() {
    return document.getElementById('f_kind_add').checked ? 'addition' : 'deduction';
}

function updatePreview() {
    const name      = document.getElementById('f_name').value.trim();
    const rType     = document.getElementById('f_rate_type').value;
    const rVal      = parseFloat(document.getElementById('f_rate_value').value) || 0;
    const limit     = parseFloat(document.getElementById('f_limit').value);
    const kind      = currentKind();
    const ded       = document.getElementById('f_is_deducted').checked;
    const currentId = parseInt(document.getElementById('f_id').value) || null;
    const isPagibig = isPagibigId(currentId);
    const isEr      = currentId === PAGIBIG_ER_ID;

    if (!name && !rVal && !isPagibig) { document.getElementById('previewBox').style.display = 'none'; return; }

    const box = document.getElementById('previewBox');
    box.className = 'preview-box ' + (kind === 'addition' ? 'kind-add' : 'kind-ded');
    box.style.display = '';

    let rateDisplay = isPagibig
        ? (isEr ? '2% (employer, flat)' : '1% or 2% (tiered by salary)')
        : rVal > 0
            ? (rType === 'percent' ? rVal + '%' : '₱' + rVal.toLocaleString('en-PH', { minimumFractionDigits: 2 }))
            : '—';

    let capDisplay = isPagibig
        ? '₱10,000.00 (max base)'
        : (!isNaN(limit) && limit > 0 ? '₱' + limit.toLocaleString('en-PH', { minimumFractionDigits: 2 }) : 'N/A');

    let impactText = kind === 'addition' ? '➕ Added to salary'
        : ded ? '➖ Deducted from employee' : '🏛 Government/Employer share';

    document.getElementById('prev_name').textContent   = name || '—';
    document.getElementById('prev_kind').textContent   = kind === 'addition' ? '➕ Addition' : '➖ Deduction';
    document.getElementById('prev_rate').textContent   = rateDisplay;
    document.getElementById('prev_limit').textContent  = capDisplay;
    document.getElementById('prev_impact').textContent = impactText;

    const pagibigRows = document.getElementById('prev_pagibig_rows');
    if (isPagibig) {
        pagibigRows.style.display = '';
        const fmt = v => '₱' + v.toFixed(2);
        document.getElementById('prev_pagibig_10k').textContent  = fmt(computePagibig(10000, isEr));
        document.getElementById('prev_pagibig_20k').textContent  = fmt(computePagibig(20000, isEr));
        document.getElementById('prev_pagibig_1200').textContent = fmt(computePagibig(1200,  isEr));
    } else {
        pagibigRows.style.display = 'none';
    }
}

document.getElementById('f_is_deducted').addEventListener('change', updatePreview);

/* ── SAVE ── */
function saveDeduction() {
    const name    = document.getElementById('f_name').value.trim();
    const errName = document.getElementById('err_name');
    if (!name) { errName.style.display = 'block'; return; }
    errName.style.display = 'none';

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled    = true;
    saveBtn.textContent = 'Saving…';

    const rType           = document.getElementById('f_rate_type').value;
    const rValRaw         = parseFloat(document.getElementById('f_rate_value').value);
    const storedRateValue = rType === 'percent' ? (rValRaw / 100) : rValRaw;
    const limit           = document.getElementById('f_limit').value;
    const parentId        = document.getElementById('f_parent_id').value;
    const kind            = currentKind();

    const payload = {
        name,
        type:         document.getElementById('f_type').value,
        rate_type:    rType,
        rate_value:   isNaN(storedRateValue) ? 0 : storedRateValue,
        limit_amount: limit !== '' ? parseFloat(limit) : null,
        status:       document.getElementById('f_status').value.trim() || name,
        sort_order:   parseInt(document.getElementById('f_sort').value) || 0,
        parent_id:    parentId !== '' ? parseInt(parentId) : null,
        is_deducted:  kind === 'addition' ? false : document.getElementById('f_is_deducted').checked,
        entry_kind:   kind,
    };

    const url    = editingId ? `/payroll/deductions/${editingId}` : '/payroll/deductions';
    const method = editingId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        saveBtn.disabled    = false;
        saveBtn.textContent = '💾 Save';
        if (res.success) {
            closeModal();
            showToast('Saved!', (editingId ? 'Updated' : 'Created') + ': ' + name, 'success');
            setTimeout(() => location.reload(), 700);
        } else {
            showToast('Error', 'Could not save. Please try again.', 'error');
        }
    })
    .catch(() => {
        saveBtn.disabled    = false;
        saveBtn.textContent = '💾 Save';
        showToast('Network Error', 'Please check your connection.', 'error');
    });
}

/* ── DELETE ── */
function askDelete(id, name) {
    deletingId = id;
    document.getElementById('confirmName').textContent = `"${name}" will be permanently deleted.`;
    document.getElementById('confirmModal').classList.add('show');
}
function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('show');
    deletingId = null;
}
function executeDelete() {
    if (!deletingId) return;
    fetch(`/payroll/deductions/${deletingId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => r.json())
    .then(res => {
        closeConfirm();
        if (res.success) {
            showToast('Deleted', 'Component removed successfully.', 'success');
            setTimeout(() => location.reload(), 400);
        } else {
            showToast('Error', 'Could not delete.', 'error');
        }
    })
    .catch(() => showToast('Network Error', 'Please try again.', 'error'));
}

/* ── TOAST ── */
function showToast(title, msg, type) {
    const icons = { success: '✅', error: '❌', info: 'ℹ️' };
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    document.getElementById('toastIcon').textContent  = icons[type] || '💬';
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3200);
}

/* ── KEYBOARD ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModal(); closeConfirm(); }
    if (e.key === 'Enter' && document.getElementById('formModal').classList.contains('show')) {
        e.preventDefault(); saveDeduction();
    }
});
</script>
@endsection