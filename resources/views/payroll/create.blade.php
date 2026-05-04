@extends('layouts.app')
@section('title', 'Create Payroll')
@section('page-title', 'Payroll')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

.pay-create-page { display: flex; flex-direction: column; min-height: calc(100vh - 120px); }
.breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #9ca3af; margin-bottom: 20px; flex-wrap: wrap; }
.breadcrumb a { color: #9ca3af; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #1a3a1a; }
.breadcrumb .sep { color: #e5e7eb; }
.breadcrumb .current { color: #1a3a1a; font-weight: 700; }
.main-card { background: #fff; border-radius: 20px; border: 1px solid #e9ecef; box-shadow: 0 2px 20px rgba(0,0,0,.07); overflow: hidden; flex: 1; }
.card-topbar { padding: 18px 26px 15px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; border-bottom: 1px solid #f0f2f0; background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%); }
.card-topbar-left { display: flex; align-items: center; gap: 12px; }
.card-topbar-icon { width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0; background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(26,58,26,.3); }
.card-topbar-icon svg { width: 20px; height: 20px; color: #fff; }
.card-topbar-title { font-size: 16px; font-weight: 800; color: #111827; margin: 0; letter-spacing: -.3px; }
.card-topbar-sub { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }
.period-pills { display: flex; align-items: center; gap: 8px; }
.period-pill { display: inline-flex; align-items: center; gap: 6px; padding: 8px 15px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b); border: none; cursor: pointer; transition: all .2s; box-shadow: 0 2px 8px rgba(26,58,26,.28); position: relative; }
.period-pill:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(26,58,26,.35); }
.period-pill svg { width: 12px; height: 12px; }
.pill-dropdown { position: absolute; top: calc(100% + 8px); left: 0; z-index: 50; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 12px 36px rgba(0,0,0,.14); min-width: 155px; display: none; flex-direction: column; padding: 6px; }
.pill-dropdown.open { display: flex; }
.pill-option { padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; color: #374151; cursor: pointer; border: none; background: none; text-align: left; transition: background .1s; }
.pill-option:hover { background: #f3f4f6; }
.pill-option.selected { background: #f0fdf4; color: #1a3a1a; font-weight: 700; }
.pill-option.has-period { color: #b45309; }
.pill-option.has-period::after { content: ' ✓'; font-size: 10px; opacity: .7; }

#step1 { padding: 52px 26px; }
.step1-inner { max-width: 510px; margin: 0 auto; }
.step1-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700; color: #15803d; margin-bottom: 18px; }
.step1-header h2 { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 8px; letter-spacing: -.5px; }
.step1-header p { font-size: 13px; color: #6b7280; margin: 0 0 32px; line-height: 1.6; }
.step1-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.field-group label { display: block; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
.step1-select, .step1-input { width: 100%; padding: 11px 14px; font-size: 13px; border: 1.5px solid #e9ecef; border-radius: 11px; color: #111827; background: #fafafa; outline: none; transition: border-color .15s, background .15s, box-shadow .15s; }
.step1-select { padding-right: 36px; appearance: none; -webkit-appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-color: #fafafa; }
.step1-select:focus, .step1-input:focus { border-color: #2d5a1b; background: #fff; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.step1-select.has-dup { border-color: #f59e0b !important; background-color: #fffbeb; }
.warn-box { border-radius: 12px; padding: 13px 15px; font-size: 12px; margin-bottom: 16px; line-height: 1.65; display: flex; gap: 10px; align-items: flex-start; }
.warn-box.yellow { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.warn-box.red    { background: #fff1f2; border: 1px solid #fecaca; color: #b91c1c; }
.btn-proceed { width: 100%; padding: 14px; font-size: 14px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b); border: none; border-radius: 12px; cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px rgba(26,58,26,.3); display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-proceed:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,58,26,.38); }
.btn-proceed:disabled { opacity: .5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

#step2 { display: none; flex-direction: column; }
.sub-toolbar { padding: 15px 26px; border-bottom: 1px solid #f0f2f0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: linear-gradient(135deg, #fafffe, #f6faf6); }
.sub-toolbar-left h3 { font-size: 14px; font-weight: 800; color: #1f2937; margin: 0 0 2px; }
.sub-toolbar-left p { font-size: 11px; color: #9ca3af; margin: 0; }
.sub-toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.search-wrap { position: relative; }
.search-wrap input { padding: 8px 12px 8px 34px; font-size: 12px; font-weight: 500; border: 1.5px solid #e9ecef; border-radius: 9px; background: #fff; color: #374151; outline: none; width: 210px; transition: border-color .15s, box-shadow .15s; }
.search-wrap input:focus { border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
.btn-delete { display: inline-flex; align-items: center; gap: 5px; padding: 8px 13px; font-size: 12px; font-weight: 600; color: #dc2626; background: #fff; border: 1.5px solid #fecaca; border-radius: 9px; cursor: pointer; transition: all .15s; }
.btn-delete:hover { background: #fff1f2; border-color: #dc2626; }
.btn-delete:disabled { opacity: .4; cursor: not-allowed; }
.btn-icon { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #374151; background: #fff; border: 1.5px solid #e9ecef; border-radius: 9px; cursor: pointer; transition: all .15s; }
.btn-icon:hover { border-color: #9ca3af; background: #fafafa; }

#filterPanel { display: none; padding: 12px 26px; gap: 14px; align-items: center; flex-wrap: wrap; border-bottom: 1px solid #f3f4f6; background: #fafffe; }
#filterPanel.open { display: flex; }
.fp-select { padding: 7px 28px 7px 10px; font-size: 12px; border: 1.5px solid #e9ecef; border-radius: 8px; color: #374151; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 8px center; appearance: none; -webkit-appearance: none; outline: none; }

.tsa { overflow-x: auto; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #d1d5db transparent; max-height: calc(100vh - 380px); }
.tsa::-webkit-scrollbar { width: 5px; height: 5px; }
.tsa::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.data-table thead { position: sticky; top: 0; z-index: 10; }

/* Group header row */
.thead-group tr:first-child th {
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    padding: 7px 10px; border-right: 1px solid rgba(255,255,255,.15);
    text-align: center; color: #fff;
}
/* Sub-header row */
.thead-group tr:nth-child(2) th {
    padding: 7px 8px; font-size: 9px; font-weight: 700; color: #4b5563;
    text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
    cursor: pointer; user-select: none; border-right: 1px solid #e9ecef; text-align: center;
}
.thead-group tr:nth-child(2) th.no-sort { cursor: default; }
.thead-group tr:nth-child(2) th .sarr { display: inline-block; margin-left: 3px; opacity: .3; font-size: 9px; }

/* Group colors */
.grp-employee  { background: #1a3a1a; }
.grp-gsis      { background: #1e40af; }
.grp-pagibig   { background: #7c3aed; }
.grp-phic      { background: #0891b2; }
.grp-wtax      { background: #b45309; }
.grp-loans     { background: #92400e; }
.grp-cngwpc    { background: #6b21a8; }
.grp-allowance { background: #065f46; }
.grp-net       { background: #991b1b; }
.grp-action    { background: #374151; }

/* Sub-header tints */
.sub-employee  { background: #f0fdf4 !important; color: #166534 !important; }
.sub-gsis      { background: #dbeafe !important; color: #1e40af !important; }
.sub-pagibig   { background: #ede9fe !important; color: #5b21b6 !important; }
.sub-phic      { background: #cffafe !important; color: #155e75 !important; }
.sub-wtax      { background: #fef3c7 !important; color: #92400e !important; }
.sub-loans     { background: #ffedd5 !important; color: #92400e !important; }
.sub-cngwpc    { background: #f3e8ff !important; color: #6b21a8 !important; }
.sub-allowance { background: #d1fae5 !important; color: #065f46 !important; }
.sub-net       { background: #fee2e2 !important; color: #991b1b !important; font-weight: 800 !important; }
.sub-action    { background: #f3f4f6 !important; }

.data-table td { padding: 0; border-bottom: 1px solid #f0f2f0; border-right: 1px solid #f5f5f5; color: #374151; vertical-align: middle; white-space: nowrap; }
.data-table tbody tr { cursor: pointer; transition: background .12s; }
.data-table tbody tr:hover { background: #f0fdf4; }
.data-table tbody tr.row-active { background: #f0fdf4; box-shadow: inset 3px 0 0 #2d5a1b; }
.data-table tbody tr.row-excluded { opacity: .35; }
.mono { font-family: 'JetBrains Mono', monospace; font-size: 11px; }
.num-cell { text-align: right; font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 500; padding: 8px 10px !important; }
.emp-name { font-weight: 700; color: #111827; font-size: 12.5px; padding: 8px 10px 2px; }
.emp-dept { font-size: 10px; color: #9ca3af; padding: 0 10px 8px; }
input[type="checkbox"] { width: 15px; height: 15px; accent-color: #1a3a1a; cursor: pointer; }

.editable-cell { position: relative; cursor: text; transition: background .15s; min-width: 70px; text-align: right; }
.editable-cell:hover { background: #f0fdf4; }
.editable-cell input {
    width: 100%; background: transparent; border: none; outline: none;
    font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 500;
    color: #374151; text-align: right; padding: 8px 10px; cursor: text; min-width: 65px;
}
.editable-cell.focused { background: #fff !important; box-shadow: inset 0 0 0 2px #2d5a1b; border-radius: 4px; }
.editable-cell.focused input { color: #111827; }

/* Agriculturist-only allowance cells — styled with a subtle tint so they're visually distinct */
.editable-cell.agri-allowance { background: #f0fdf4; }
.editable-cell.agri-allowance:hover { background: #dcfce7; }
/* Non-agriculturist allowance cells are greyed-out/locked */
.editable-cell.agri-locked { background: #f9fafb !important; cursor: not-allowed; }
.editable-cell.agri-locked input { color: #d1d5db !important; cursor: not-allowed; pointer-events: none; }

.net-cell { font-weight: 800; color: #dc2626; text-align: right; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; padding: 8px 10px !important; }

.chk-cell { padding: 8px 10px !important; text-align: center; }
.row-num  { color: #9ca3af; font-size: 11px; font-weight: 600; text-align: center; padding: 8px 6px !important; }

.dot-menu { position: relative; display: inline-block; }
.dot-btn { background: none; border: none; cursor: pointer; padding: 4px 7px; border-radius: 7px; color: #9ca3af; font-size: 16px; letter-spacing: 2px; line-height: 1; transition: background .12s; }
.dot-btn:hover { background: #f3f4f6; color: #374151; }
.dot-dropdown { position: absolute; right: 0; top: 100%; margin-top: 4px; z-index: 50; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 10px 32px rgba(0,0,0,.13); min-width: 165px; display: none; overflow: hidden; }
.dot-dropdown.open { display: block; }
.dot-item { display: flex; align-items: center; gap: 8px; padding: 9px 14px; font-size: 12px; font-weight: 500; color: #374151; cursor: pointer; border: none; background: none; width: 100%; text-align: left; transition: background .1s; }
.dot-item:hover { background: #f9fafb; }
.dot-item.danger { color: #dc2626; }
.dot-item.danger:hover { background: #fff1f2; }

.bottom-bar { padding: 15px 26px; border-top: 2px solid #f0f2f0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: linear-gradient(135deg, #fafffe, #f6faf6); flex-shrink: 0; }
.bottom-info { font-size: 12px; color: #6b7280; }
.bottom-info strong { color: #111827; font-weight: 700; }
.bottom-actions { display: flex; gap: 10px; }
.btn-cancel-step { padding: 10px 22px; font-size: 13px; font-weight: 600; color: #374151; background: #fff; border: 1.5px solid #e9ecef; border-radius: 10px; cursor: pointer; transition: all .15s; }
.btn-cancel-step:hover { border-color: #9ca3af; color: #111827; }
.btn-final { padding: 10px 28px; font-size: 13px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b); border: none; border-radius: 10px; cursor: pointer; transition: all .2s; box-shadow: 0 3px 10px rgba(26,58,26,.28); }
.btn-final:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(26,58,26,.38); }
.btn-final:disabled { background: linear-gradient(135deg, #9ca3af, #9ca3af); box-shadow: none; cursor: not-allowed; transform: none; }

/* Employer-paid column styling */
.employer-col { color: #9ca3af !important; font-style: italic; }

/* Modal */
.cmodal-bg { position: fixed; inset: 0; z-index: 300; background: rgba(0,0,0,.45); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .22s; padding: 16px; }
.cmodal-bg.show { opacity: 1; pointer-events: all; }
.cmodal-card { background: #fff; border-radius: 20px; padding: 28px; width: min(98vw, 440px); box-shadow: 0 28px 70px rgba(0,0,0,.22); transform: scale(.92) translateY(14px); transition: transform .28s cubic-bezier(.34,1.56,.64,1); }
.cmodal-bg.show .cmodal-card { transform: scale(1) translateY(0); }
.cstat-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
.cstat-row:last-child { border-bottom: none; }
.cstat-row .lbl { color: #6b7280; }
.cstat-row .val { font-weight: 700; color: #111827; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; }

/* Slide panel */
#empOverlay { position: fixed; inset: 0; z-index: 90; background: rgba(0,0,0,0.35); backdrop-filter: blur(4px); opacity: 0; pointer-events: none; transition: opacity .3s ease; }
#empOverlay.show { opacity: 1; pointer-events: all; }
#empPanel { position: fixed; top: 0; right: 0; bottom: 0; z-index: 100; width: 55vw; min-width: 380px; max-width: 860px; display: flex; flex-direction: column; pointer-events: none; transform: translateX(100%); transition: transform .36s cubic-bezier(.32,.72,0,1); }
#empPanel.open { pointer-events: all; transform: translateX(0); }
.ep-box { background: #fff; width: 100%; height: 100%; display: flex; flex-direction: column; box-shadow: -12px 0 60px rgba(0,0,0,.22); overflow: hidden; }
.ep-head { background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%); padding: 20px 24px 18px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.ep-head-info { min-width: 0; }
.ep-head h2 { font-size: 16px; font-weight: 700; color: #fff; margin: 0 0 3px; }
.ep-head p  { font-size: 11px; color: rgba(255,255,255,.6); margin: 0; }
.ep-close { background: rgba(255,255,255,.15); border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: rgba(255,255,255,.8); transition: background .15s; flex-shrink: 0; }
.ep-close:hover { background: rgba(255,255,255,.28); color: #fff; }
.ep-close svg { width: 14px; height: 14px; }
.ep-body { flex: 1; overflow-y: auto; background: #f8f9fa; scrollbar-width: thin; scrollbar-color: #d1d5db transparent; padding-bottom: 6px; }
.ep-body::-webkit-scrollbar { width: 4px; }
.ep-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.ep-card { background: #fff; border-radius: 12px; margin: 14px 16px; padding: 18px 18px 16px; box-shadow: 0 1px 4px rgba(0,0,0,.06); border: 1px solid #f0f2f0; }
.ep-card-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.ep-card-icon { width: 30px; height: 30px; border-radius: 8px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #2d5a1b; flex-shrink: 0; }
.ep-card-title { font-size: 13px; font-weight: 700; color: #111827; margin: 0; }
.ep-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
.ep-field label { display: block; font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
.ep-field p { font-size: 13px; font-weight: 500; color: #111827; margin: 0; }
.ep-field.span2 { grid-column: span 2; }
.ep-edit label { display: block; font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
.ep-edit input { width: 100%; padding: 7px 9px; border: 1.5px solid #e9ecef; border-radius: 8px; font-size: 12px; font-weight: 500; font-family: 'JetBrains Mono', monospace; background: #fafafa; color: #111827; outline: none; transition: border-color .15s; text-align: right; }
.ep-edit input:focus { border-color: #2d5a1b; background: #fff; box-shadow: 0 0 0 3px rgba(45,90,27,.07); }
.ep-edit input:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; border-color: #e9ecef; }
.ep-net-bar { padding: 12px 20px; border-top: 2px solid #f0f2f0; background: #fff5f5; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.ep-net-bar span:first-child { font-size: 12px; font-weight: 800; color: #991b1b; }
#epNet { font-size: 22px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #dc2626; }
.ep-foot { padding: 14px 20px; border-top: 1px solid #f0f2f0; display: flex; gap: 9px; flex-shrink: 0; background: #fff; align-items: center; justify-content: space-between; }
.ep-foot-right { display: flex; gap: 9px; }
.ep-btn-cancel { padding: 10px 20px; font-size: 13px; font-weight: 600; color: #374151; background: #fff; border: 1.5px solid #e9ecef; border-radius: 10px; cursor: pointer; }
.ep-btn-save { padding: 10px 24px; font-size: 13px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b); border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 7px; box-shadow: 0 3px 10px rgba(26,58,26,.25); }

#toast { position: fixed; bottom: 22px; right: 22px; z-index: 999; background: #fff; border-radius: 15px; padding: 13px 17px; box-shadow: 0 10px 36px rgba(0,0,0,.16); display: flex; align-items: center; gap: 11px; min-width: 230px; max-width: calc(100vw - 44px); opacity: 0; transform: translateY(14px); transition: all .32s cubic-bezier(.34,1.56,.64,1); pointer-events: none; }
#toast.show { opacity: 1; transform: translateY(0); }

@media (max-width: 767px) {
    .step1-row { grid-template-columns: 1fr; }
    .card-topbar, .sub-toolbar { flex-direction: column; align-items: flex-start; }
    .search-wrap input { width: 100%; }
    .bottom-bar { flex-direction: column; align-items: stretch; }
    .bottom-actions { flex-direction: column; }
    .btn-cancel-step, .btn-final { width: 100%; text-align: center; }
    #empPanel { width: 100%; min-width: 0; top: 0; right: 0; left: 0; bottom: 0; transform: translateY(100%); }
    #empPanel.open { transform: translateY(0); }
    .ep-grid { grid-template-columns: 1fr; }
    .ep-field.span2 { grid-column: span 1; }
    .ep-foot { flex-direction: column; }
    .ep-foot-right { width: 100%; }
    .ep-btn-cancel, .ep-btn-save { flex: 1; justify-content: center; }
}
</style>

@php
    $cngCols = [
        'cng_capital_share'  => 'Capital Share',
        'cng_kiddie_savings' => 'Kiddie Sav.',
        'cng_savings'        => 'Savings',
        'cng_regular_loan'   => 'Reg. Loan',
        'cng_crisis_loan'    => 'Crisis Loan',
        'cng_coop_canteen'   => 'Coop Canteen',
        'cng_coop_store'     => 'Coop Store',
        'cng_calamity_loan'  => 'Calamity Loan',
        'cng_abuloy'         => 'Abuloy',
        'cng_handog'         => 'Handog/Ituro',
        'cng_b2b_loan'       => 'B2B/Special',
        'cng_petty_cash'     => 'Petty Cash',
        'cng_commodity_loan' => 'Commodity Loan'
    ];

    /* ─── Load all active deductions from DB ─── */
    $allDeductions = \App\Models\PayrollDeduction::where('is_active', 1)
        ->orderBy('sort_order')
        ->get();

    $dbDeductions = $allDeductions->keyBy(fn($d) => strtolower($d->name));

    /* Helper: look up deduction record by exact name */
    $ded = fn(string $name) => $dbDeductions->get(strtolower($name));

    $gsisEeRec    = $ded('Life Retirement Insurance – Personal Share') ?? $ded('gsis employee share');
    $gsisGovtRec  = $ded('Life Retirement Insurance – Government Share') ?? $ded("gsis gov't share");
    $gsisEcRec    = $ded('ECF (Employee Compensation Fund)');
    $pagibigEeRec = $ded('Employee Share') ?? $ded('pagibig employee share');
    $pagibigGovRec= $ded('Employer Share') ?? $ded("pagibig gov't share");
    $phicEeRec    = $ded('Personal Share')   ?? $ded('philhealth employee share');
    $phicGovtRec  = $ded('Government Share') ?? $ded("philhealth gov't share");
    $peraRec      = $ded('PERA');
    $raRec        = $ded('RA');
    $taRec        = $ded('TA');

    $jsConfig = [
        'gsisEeType'      => $gsisEeRec?->rate_type    ?? 'percent',
        'gsisEeValue'     => (float)($gsisEeRec?->rate_value   ?? 0.09),
        'gsisEeLimit'     => $gsisEeRec?->limit_amount !== null ? (float)$gsisEeRec->limit_amount : null,
        'gsisGovtType'    => $gsisGovtRec?->rate_type  ?? 'percent',
        'gsisGovtValue'   => (float)($gsisGovtRec?->rate_value ?? 0.12),
        'gsisGovtLimit'   => $gsisGovtRec?->limit_amount !== null ? (float)$gsisGovtRec->limit_amount : null,
        'gsisEcType'      => $gsisEcRec?->rate_type    ?? 'flat',
        'gsisEcValue'     => (float)($gsisEcRec?->rate_value   ?? 100),
        'pagibigEeType'   => $pagibigEeRec?->rate_type  ?? 'flat',
        'pagibigEeValue'  => (float)($pagibigEeRec?->rate_value ?? 200),
        'pagibigGovType'  => $pagibigGovRec?->rate_type ?? 'flat',
        'pagibigGovValue' => (float)($pagibigGovRec?->rate_value ?? 200),
        'phicEeType'      => $phicEeRec?->rate_type    ?? 'percent',
        'phicEeValue'     => (float)($phicEeRec?->rate_value   ?? 0.025),
        'phicEeLimit'     => $phicEeRec?->limit_amount !== null ? (float)$phicEeRec->limit_amount : null,
        'phicGovtType'    => $phicGovtRec?->rate_type  ?? 'percent',
        'phicGovtValue'   => (float)($phicGovtRec?->rate_value ?? 0.025),
        'phicGovtLimit'   => $phicGovtRec?->limit_amount !== null ? (float)$phicGovtRec->limit_amount : null,
        'peraType'        => $peraRec?->rate_type      ?? 'flat',
        'peraValue'       => (float)($peraRec?->rate_value     ?? 2000),
        'raType'          => $raRec?->rate_type        ?? 'flat',
        'raValue'         => (float)($raRec?->rate_value       ?? 9500),
        'taType'          => $taRec?->rate_type        ?? 'flat',
        'taValue'         => (float)($taRec?->rate_value       ?? 9500),
    ];

    $computeFromConfig = function(string $type, float $value, ?float $limit, float $gross): float {
        if ($type === 'percent') {
            $amt = round($gross * $value, 2);
            return $limit !== null ? min($amt, $limit) : $amt;
        }
        return round($value, 2);
    };

    $employees = \App\Models\Employee::with(['position','department'])
        ->where('is_active', 1)->orderBy('last_name')->get();

    $existingPeriods = \App\Models\PayrollPeriod::select('period_id','month','year','period_label','status')
        ->orderBy('year','desc')->orderBy('month','desc')->get();
    $existingPeriodMap = $existingPeriods->mapWithKeys(fn($p) => [
        $p->month . '-' . $p->year => ['label' => $p->period_label, 'status' => $p->status, 'id' => $p->period_id]
    ])->toArray();
@endphp

<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <a href="#" id="bc2" onclick="goBack(); return false;">Create Payroll</a>
    <span class="sep" id="bc3sep" style="display:none;">›</span>
    <span class="current" id="bc3" style="display:none;">Review Employees</span>
</div>

<div class="pay-create-page">
<div class="main-card">

    {{-- TOP BAR --}}
    <div class="card-topbar">
        <div class="card-topbar-left">
            <div class="card-topbar-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="card-topbar-title">Create Payroll</p>
                <p class="card-topbar-sub">Provincial Payroll System</p>
            </div>
        </div>
        <div class="period-pills" id="periodPills" style="display:none;">
            <div style="position:relative;">
                <button class="period-pill" id="monthPill" onclick="togglePill('monthDrop')">
                    <span id="monthPillLabel">{{ \Carbon\Carbon::create()->month(now()->month)->format('F') }}</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="pill-dropdown" id="monthDrop">
                    @foreach(range(1,12) as $m)
                    <button class="pill-option" data-val="{{ $m }}" onclick="selectMonth({{ $m }}, '{{ \Carbon\Carbon::create()->month($m)->format('F') }}')">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</button>
                    @endforeach
                </div>
            </div>
            <div style="position:relative;">
                <button class="period-pill" id="yearPill" onclick="togglePill('yearDrop')">
                    <span id="yearPillLabel">{{ now()->year }}</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="pill-dropdown" id="yearDrop">
                    @foreach(range(now()->year, now()->year - 4, -1) as $y)
                    <button class="pill-option" data-val="{{ $y }}" onclick="selectYear({{ $y }})">{{ $y }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ STEP 1 ══════════════════ --}}
    <div id="step1">
        <div class="step1-inner">
            <div class="step1-badge">
                <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                New Payroll Period
            </div>
            <div class="step1-header">
                <h2>Configure Payroll Period</h2>
                <p>Select the month and year, then review which active employees to include. All deduction values are loaded from Deduction Management.</p>
            </div>
            <div class="step1-row">
                <div class="field-group">
                    <label>Month</label>
                    <select id="s1Month" class="step1-select" onchange="updateLabel(); checkDuplicate()">
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }} data-name="{{ \Carbon\Carbon::create()->month($m)->format('F') }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label>Year</label>
                    <select id="s1Year" class="step1-select" onchange="updateLabel(); checkDuplicate()">
                        @foreach(range(now()->year, now()->year - 3, -1) as $y)
                        <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <div class="field-group">
                    <label>Period Label</label>
                    <input type="text" id="s1Label" class="step1-input" value="{{ \Carbon\Carbon::create()->month(now()->month)->format('F') . ' ' . now()->year }}" placeholder="e.g. March 2026">
                </div>
            </div>
            <div class="warn-box red" id="dupWarn" style="display:none;">
                <span>🚫</span>
                <div>
                    A payroll period for <strong id="dupWarnLabel"></strong> already exists
                    (<span id="dupWarnStatus"></span>).
                    <br><a id="dupWarnLink" href="#" style="color:#b91c1c;font-weight:700;">View existing period →</a>
                </div>
            </div>
            <div class="warn-box yellow" style="margin-bottom:24px;">
                <span>⚠️</span>
                <div>Only <strong>active employees</strong> are shown. Fixed deductions (GSIS, Pag-IBIG, PhilHealth, PERA) are auto-calculated from DB rates. <strong>RA &amp; TA allowances apply only to the Provincial Agriculturist (PA)</strong> and are editable. Click any row to edit variable deductions.</div>
            </div>
            <button class="btn-proceed" id="btnProceed1" onclick="goToStep2()">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                Next — Review Active Employees
            </button>
        </div>
    </div>

    {{-- ══════════════════ STEP 2 ══════════════════ --}}
    <div id="step2">
        <div class="sub-toolbar">
            <div class="sub-toolbar-left">
                <h3>Review Active Employees — Full Deduction Breakdown</h3>
                <p>Total: <strong id="totalCount">0</strong> employees — <strong>click any row</strong> to view &amp; edit variable deductions</p>
            </div>
            <div class="sub-toolbar-right">
                <div class="search-wrap">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" id="empSearch" placeholder="Search employee…" oninput="filterEmployees()">
                </div>
                <button class="btn-delete" id="btnDelete" disabled onclick="removeSelected()">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                    Exclude
                </button>
                <button class="btn-icon" onclick="toggleFilter()">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filters
                </button>
            </div>
        </div>

        <div id="filterPanel">
            <div>
                <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px;">Department</label>
                <select id="fDept" class="fp-select" onchange="filterEmployees()">
                    <option value="">All Departments</option>
                    @foreach(\App\Models\Department::where('is_active',1)->orderBy('department_name')->get() as $dept)
                    <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px;">Status</label>
                <select id="fStatus" class="fp-select" onchange="filterEmployees()">
                    <option value="">All</option>
                    <option value="included">Included</option>
                    <option value="excluded">Excluded</option>
                </select>
            </div>
            <button onclick="clearFilter()" style="align-self:flex-end;padding:8px 12px;font-size:11px;font-weight:600;border:1.5px solid #e9ecef;border-radius:7px;background:#fff;color:#6b7280;cursor:pointer;">Clear</button>
        </div>

        <div class="tsa">
        <table class="data-table">
            <thead class="thead-group">
                {{-- ── GROUP HEADER ROW ── --}}
                <tr>
                    <th class="grp-employee no-sort" colspan="2"></th>
                    <th class="grp-employee no-sort" colspan="4" style="text-align:left;padding-left:14px;">Employee</th>

                    {{-- GSIS: 3 fixed + 8 editable = 11 cols --}}
                    <th class="grp-gsis no-sort" colspan="11" style="text-align:center;">G S I S</th>

                    {{-- PAG-IBIG: 2 fixed + 2 editable = 4 cols --}}
                    <th class="grp-pagibig no-sort" colspan="4" style="text-align:center;">PAG-IBIG</th>

                    {{-- PHILHEALTH: 2 fixed --}}
                    <th class="grp-phic no-sort" colspan="2" style="text-align:center;">PHILHEALTH</th>

                    {{-- WITHHOLDING TAX: 1 editable --}}
                    <th class="grp-wtax no-sort" colspan="1" style="text-align:center;">W/TAX</th>

                    {{-- OTHER LOANS = 5 columns --}}
                    <th class="grp-loans no-sort" colspan="5" style="text-align:center;">OTHER LOANS &amp; DEDUCTIONS</th>

                    {{-- CNGWPC cooperative deductions --}}
                    <th class="grp-cngwpc no-sort" colspan="{{ count($cngCols) }}" style="text-align:center;">CNGWPC COOPERATIVE</th>

                    {{-- ALLOWANCES: PERA(fixed) + RA(agri-editable) + TA(agri-editable) = 3 --}}
                    <th class="grp-allowance no-sort" colspan="3" style="text-align:center;">ALLOWANCES</th>

                    {{-- NET PAY --}}
                    <th class="grp-net no-sort" colspan="1" style="text-align:center;">NET PAY</th>

                    {{-- ACTION --}}
                    <th class="grp-action no-sort" colspan="1"></th>
                </tr>

                {{-- ── SUB-HEADER ROW ── --}}
                <tr>
                    {{-- Checkbox + # --}}
                    <th class="chk-cell sub-employee no-sort" style="width:38px;"><input type="checkbox" id="chkAll" onchange="toggleAll(this)"></th>
                    <th class="row-num sub-employee no-sort">#</th>

                    {{-- Employee info --}}
                    <th class="sub-employee" onclick="sortEmp(2)" style="text-align:left;padding-left:10px;">Name <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(3)">Desig. <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(4)">Dept <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(5)" style="text-align:right;">Gross Salary <span class="sarr">↕</span></th>

                    {{-- GSIS --}}
                    <th class="sub-gsis">Personal Share (9%)</th>
                    <th class="sub-gsis" style="color:#9ca3af !important;">Govt (12%)*</th>
                    <th class="sub-gsis" style="color:#9ca3af !important;">ECF*</th>
                    <th class="sub-gsis no-sort">Policy Loan</th>
                    <th class="sub-gsis no-sort">Emergency</th>
                    <th class="sub-gsis no-sort">Real Estate</th>
                    <th class="sub-gsis no-sort">MPL</th>
                    <th class="sub-gsis no-sort">MPL Lite</th>
                    <th class="sub-gsis no-sort">GFAL</th>
                    <th class="sub-gsis no-sort">Computer</th>
                    <th class="sub-gsis no-sort">Conso</th>

                    {{-- PAG-IBIG --}}
                    <th class="sub-pagibig">Personal Share (₱200)</th>
                    <th class="sub-pagibig" style="color:#9ca3af !important;">Govt (₱200)*</th>
                    <th class="sub-pagibig no-sort">MPL</th>
                    <th class="sub-pagibig no-sort">Calamity</th>

                    {{-- PhilHealth --}}
                    <th class="sub-phic">Personal Share (2.5%)</th>
                    <th class="sub-phic" style="color:#9ca3af !important;">Govt (2.5%)*</th>

                    {{-- W/Tax --}}
                    <th class="sub-wtax no-sort">W/Tax</th>

                    {{-- Other Loans --}}
                    <th class="sub-loans no-sort">DBP</th>
                    <th class="sub-loans no-sort">LBP</th>
                    <th class="sub-loans no-sort">PARACLE</th>
                    <th class="sub-loans no-sort">Overpayment</th>
                    <th class="sub-loans no-sort">Other Ded.</th>

                    {{-- CNGWPC cooperative columns --}}
                    @foreach($cngCols as $field => $label)
                    <th class="sub-cngwpc no-sort">{{ $label }}</th>
                    @endforeach

                    {{-- Allowances --}}
                    <th class="sub-allowance">PERA</th>
                    {{-- RA & TA: editable for PA, locked for others --}}
                    <th class="sub-allowance no-sort" title="Representation Allowance — Provincial Agriculturist only">RA <span style="font-size:8px;opacity:.6;">(PA)</span></th>
                    <th class="sub-allowance no-sort" title="Transportation Allowance — Provincial Agriculturist only">TA <span style="font-size:8px;opacity:.6;">(PA)</span></th>

                    {{-- Net Pay --}}
                    <th class="sub-net no-sort">Net Pay</th>

                    {{-- Action --}}
                    <th class="sub-action no-sort"></th>
                </tr>
            </thead>
            <tbody id="empTbody">
                @forelse($employees as $i => $emp)
                @php
                    $gross      = (float)$emp->salary;
                    $gsisEe     = $computeFromConfig($jsConfig['gsisEeType'],    $jsConfig['gsisEeValue'],    $jsConfig['gsisEeLimit'],    $gross);
                    $gsisGovt   = $computeFromConfig($jsConfig['gsisGovtType'],  $jsConfig['gsisGovtValue'],  $jsConfig['gsisGovtLimit'],  $gross);
                    $gsisEc     = $computeFromConfig($jsConfig['gsisEcType'],    $jsConfig['gsisEcValue'],    null,                        $gross);
                    $pagibigEe  = $computeFromConfig($jsConfig['pagibigEeType'], $jsConfig['pagibigEeValue'], null,                        $gross);
                    $pagibigGov = $computeFromConfig($jsConfig['pagibigGovType'],$jsConfig['pagibigGovValue'],null,                        $gross);
                    $phicEe     = $computeFromConfig($jsConfig['phicEeType'],    $jsConfig['phicEeValue'],    $jsConfig['phicEeLimit'],    $gross);
                    $phicGovt   = $computeFromConfig($jsConfig['phicGovtType'],  $jsConfig['phicGovtValue'],  $jsConfig['phicGovtLimit'],  $gross);
                    $pera       = $computeFromConfig($jsConfig['peraType'],       $jsConfig['peraValue'],      null,                        $gross);

                    /* RA & TA: STRICTLY ONLY FOR PROVINCIAL AGRICULTURIST ("PA") */
                    $positionCode = strtoupper(trim(optional($emp->position)->position_code ?? ''));
                    $isAgri = ($positionCode === 'PA');
                    
                    $raDefault  = $isAgri ? $computeFromConfig($jsConfig['raType'], $jsConfig['raValue'], null, $gross) : 0.0;
                    $taDefault  = $isAgri ? $computeFromConfig($jsConfig['taType'], $jsConfig['taValue'], null, $gross) : 0.0;

                    // Net = Gross - GSIS_EE - Pagibig_EE - PhilHealth_EE + PERA + RA + TA
                    $netBase    = $gross - $gsisEe - $pagibigEe - $phicEe + $pera + $raDefault + $taDefault;
                @endphp
                <tr data-emp-id="{{ $emp->employee_id }}"
                    data-dept="{{ $emp->department_id }}"
                    data-name="{{ strtolower($emp->last_name . ' ' . $emp->first_name) }}"
                    data-salary="{{ $gross }}"
                    data-designation="{{ optional($emp->position)->position_code ?? '' }}"
                    data-position-name="{{ optional($emp->position)->position_name ?? '' }}"
                    data-department-name="{{ optional($emp->department)->department_name ?? '' }}"
                    data-full-name="{{ $emp->last_name }}, {{ $emp->first_name }}{{ $emp->extension_name ? ' '.$emp->extension_name : '' }}"
                    data-is-agri="{{ $isAgri ? '1' : '0' }}"
                    onclick="openEmpPanel(this)">

                    {{-- Checkbox --}}
                    <td class="chk-cell" onclick="event.stopPropagation()">
                        <input type="checkbox" class="emp-chk" value="{{ $emp->employee_id }}" checked onchange="onChkChange()">
                    </td>
                    <td class="row-num">{{ $i+1 }}</td>

                    {{-- Employee Info --}}
                    <td>
                        <div class="emp-name">{{ $emp->last_name }}, {{ $emp->first_name }}@if($emp->extension_name) {{ $emp->extension_name }}@endif</div>
                        <div class="emp-dept mono">{{ $emp->employee_id }}</div>
                    </td>
                    <td style="padding:8px;font-size:11.5px;font-weight:600;color:#374151;">{{ optional($emp->position)->position_code ?? '—' }}</td>
                    <td style="padding:8px;font-size:11px;color:#6b7280;max-width:120px;white-space:normal;line-height:1.3;">{{ optional($emp->department)->department_name ?? '—' }}</td>
                    <td class="num-cell" style="font-weight:800;color:#111827;">{{ number_format($gross,2) }}</td>

                    {{-- ── GSIS Fixed ── --}}
                    <td class="num-cell" style="color:#1e40af;font-weight:700;">{{ number_format($gsisEe,2) }}</td>
                    <td class="num-cell employer-col">{{ number_format($gsisGovt,2) }}</td>
                    <td class="num-cell employer-col">{{ number_format($gsisEc,2) }}</td>

                    {{-- ── GSIS Editable Loans ── --}}
                    <td class="editable-cell" data-field="gsis_policy"      onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_emergency"   onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_real_estate" onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_mpl"         onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_mpl_lite"    onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_gfal"        onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_computer"    onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="gsis_conso"       onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── PAG-IBIG Fixed + Editable ── --}}
                    <td class="num-cell" style="color:#7c3aed;font-weight:700;">{{ number_format($pagibigEe,2) }}</td>
                    <td class="num-cell employer-col">{{ number_format($pagibigGov,2) }}</td>
                    <td class="editable-cell" data-field="pagibig_mpl"      onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="pagibig_calamity" onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── PhilHealth Fixed ── --}}
                    <td class="num-cell" style="color:#0891b2;font-weight:700;">{{ number_format($phicEe,2) }}</td>
                    <td class="num-cell employer-col">{{ number_format($phicGovt,2) }}</td>

                    {{-- ── Withholding Tax ── --}}
                    <td class="editable-cell" data-field="withholding_tax"  onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── Other Loans ── --}}
                    <td class="editable-cell" data-field="loan_dbp"         onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="loan_lbp"         onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="loan_paracle"     onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="overpayment"      onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-field="other_deduction"  onclick="event.stopPropagation()"><input type="number" step="0.01" min="0" value="0" class="loan-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── CNGWPC Cooperative ── --}}
                    @foreach($cngCols as $field => $label)
                    <td class="editable-cell" data-field="{{ $field }}" onclick="event.stopPropagation()">
                        <input type="number" step="0.01" min="0" value="0" class="loan-input dyn-input" onchange="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
                    </td>
                    @endforeach

                    {{-- ── PERA: always fixed ── --}}
                    <td class="num-cell" style="color:#065f46;font-weight:700;">{{ number_format($pera,2) }}</td>

                    {{-- ── RA: editable for PA, locked (0) for others ── --}}
                    <td class="editable-cell {{ $isAgri ? 'agri-allowance' : 'agri-locked' }}"
                        data-field="allowance_ra"
                        onclick="event.stopPropagation()">
                        <input type="number" step="0.01" min="0"
                               value="{{ number_format($raDefault, 2, '.', '') }}"
                               class="loan-input"
                               {{ $isAgri ? '' : 'disabled' }}
                               onchange="recalcRow(this)"
                               onfocus="focusCell(this)"
                               onblur="blurCell(this)">
                    </td>

                    {{-- ── TA: editable for PA, locked (0) for others ── --}}
                    <td class="editable-cell {{ $isAgri ? 'agri-allowance' : 'agri-locked' }}"
                        data-field="allowance_ta"
                        onclick="event.stopPropagation()">
                        <input type="number" step="0.01" min="0"
                               value="{{ number_format($taDefault, 2, '.', '') }}"
                               class="loan-input"
                               {{ $isAgri ? '' : 'disabled' }}
                               onchange="recalcRow(this)"
                               onfocus="focusCell(this)"
                               onblur="blurCell(this)">
                    </td>

                    {{-- ── Net Pay cell ── --}}
                    <td class="net-cell"
                        id="net_{{ $emp->employee_id }}"
                        data-gross="{{ $gross }}"
                        data-gsis-ee="{{ $gsisEe }}"
                        data-gsis-govt="{{ $gsisGovt }}"
                        data-gsis-ec="{{ $gsisEc }}"
                        data-pagibig-ee="{{ $pagibigEe }}"
                        data-pagibig-gov="{{ $pagibigGov }}"
                        data-phic-ee="{{ $phicEe }}"
                        data-phic-govt="{{ $phicGovt }}"
                        data-pera="{{ $pera }}"
                        data-is-agri="{{ $isAgri ? '1' : '0' }}">
                        {{ number_format($netBase, 2) }}
                    </td>

                    {{-- Action --}}
                    <td style="text-align:right;padding-right:10px;" onclick="event.stopPropagation()">
                        <div class="dot-menu">
                            <button class="dot-btn" onclick="toggleDot(this)">···</button>
                            <div class="dot-dropdown">
                                <button class="dot-item" onclick="openEmpPanel(this.closest('tr'))">
                                    <svg style="width:13px;height:13px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View / Edit
                                </button>
                                <button class="dot-item" onclick="excludeRow({{ $emp->employee_id }})">
                                    <svg style="width:13px;height:13px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Exclude from Payroll
                                </button>
                                <button class="dot-item" onclick="includeRow({{ $emp->employee_id }})">
                                    <svg style="width:13px;height:13px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Include in Payroll
                                </button>
                                <button class="dot-item danger" onclick="resetRow({{ $emp->employee_id }})">
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Reset Loans to 0
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="60" style="padding:56px;text-align:center;color:#9ca3af;">No active employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="bottom-bar">
            <div class="bottom-info">
                <strong id="selectedCount">0</strong> of <strong id="totalCount2">{{ $employees->count() }}</strong> employees included
                &nbsp;·&nbsp; Est. Gross: <strong id="grossTotal">₱0.00</strong>
                &nbsp;·&nbsp; Est. Net: <strong id="netTotal">₱0.00</strong>
            </div>
            <div class="bottom-actions">
                <button class="btn-cancel-step" onclick="goBack()">← Back</button>
                <button class="btn-final" id="btnProceed2" onclick="confirmProceed()">Generate Payroll →</button>
            </div>
        </div>
    </div>{{-- /step2 --}}

</div>{{-- /main-card --}}
</div>{{-- /pay-create-page --}}

{{-- Slide panel overlay --}}
<div id="empOverlay" onclick="closeEmpPanel()"></div>
<div id="empPanel">
    <div class="ep-box">
        <div class="ep-head">
            <div class="ep-head-info">
                <h2 id="epName">—</h2>
                <p id="epSub">—</p>
            </div>
            <button class="ep-close" onclick="closeEmpPanel()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="ep-body" id="epBody"></div>
        <div class="ep-net-bar">
            <span>Net Pay</span>
            <span id="epNet">₱0.00</span>
        </div>
        <div class="ep-foot">
            <button class="ep-btn-cancel" onclick="closeEmpPanel()">Cancel</button>
            <div class="ep-foot-right">
                <button class="ep-btn-save" onclick="saveEmpPanel()">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm modal --}}
<div id="cModal" class="cmodal-bg" onclick="if(event.target===this)closeCModal()">
    <div class="cmodal-card">
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:20px;">
            <div style="width:46px;height:46px;border-radius:13px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:22px;height:22px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 style="font-size:16px;font-weight:800;color:#1f2937;margin:0 0 4px;">Generate Payroll?</h3>
                <p id="cModalDesc" style="font-size:12px;color:#6b7280;margin:0;line-height:1.55;">This will create payroll records for the selected employees.</p>
            </div>
        </div>
        <div style="background:#f9fafb;border-radius:12px;padding:14px 16px;margin-bottom:18px;">
            <div class="cstat-row"><span class="lbl">Period</span><strong class="val" id="cPeriod">—</strong></div>
            <div class="cstat-row"><span class="lbl">Employees</span><strong class="val" id="cCount">—</strong></div>
            <div class="cstat-row"><span class="lbl">Est. Gross Total</span><strong class="val" id="cGross">—</strong></div>
            <div class="cstat-row"><span class="lbl">Est. Net Total</span><strong class="val" id="cNet" style="color:#dc2626;">—</strong></div>
        </div>
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 13px;margin-bottom:18px;font-size:11.5px;color:#92400e;">
            ⚠️ Payroll records will be created with the values shown. Further edits can be made from Payroll Management.
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeCModal()" class="btn-cancel-step">Cancel</button>
            <button onclick="submitPayroll()" class="btn-final" id="cProceedBtn">✅ Confirm &amp; Generate</button>
        </div>
    </div>
</div>

{{-- Hidden form --}}
<form id="submitForm" method="POST" action="{{ route('payroll.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="month"        id="hMonth">
    <input type="hidden" name="year"         id="hYear">
    <input type="hidden" name="period_label" id="hLabel">
    <div id="hEmployees"></div>
    <div id="hOverrides"></div>
</form>

<div id="toast">
    <div id="toastIcon" style="width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
    <div>
        <p id="toastTitle" style="font-size:13px;font-weight:700;color:#1f2937;margin:0;"></p>
        <p id="toastMsg"   style="font-size:11px;color:#6b7280;margin:2px 0 0;"></p>
    </div>
</div>

<script>
const DED_CONFIG       = @json($jsConfig);
const EXISTING_PERIODS = @json($existingPeriodMap);
const PAYROLL_INDEX_URL= '{{ route('payroll.index') }}';

const HARD_DEDUCTION_FIELDS = [
    'gsis_policy','gsis_emergency','gsis_real_estate',
    'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
    'pagibig_mpl','pagibig_calamity',
    'withholding_tax',
    'loan_dbp','loan_lbp','loan_paracle','overpayment','other_deduction',
    'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
    'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
    'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan'
];

/* ── Utility ── */
function fmt(n)    { return parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtPHP(n) { return '₱'+fmt(n); }

function computeFixed(type, value, limit, gross) {
    let amt = type === 'percent' ? Math.round(gross * value * 100) / 100 : Math.round(value * 100) / 100;
    if (limit !== null && limit !== undefined) amt = Math.min(amt, limit);
    return amt;
}

let selectedMonth = {{ now()->month }};
let selectedMonthName = '{{ \Carbon\Carbon::create()->month(now()->month)->format("F") }}';
let selectedYear  = {{ now()->year }};
let sortCol = -1, sortAsc = true;
let _activeRow = null;
let _isDuplicate = false;

/* ── Duplicate check ── */
function checkDuplicate() {
    const m   = parseInt(document.getElementById('s1Month').value);
    const y   = parseInt(document.getElementById('s1Year').value);
    const key = m + '-' + y;
    const dup = EXISTING_PERIODS[key];
    const warn= document.getElementById('dupWarn');
    const btn = document.getElementById('btnProceed1');
    const mSel= document.getElementById('s1Month');
    const ySel= document.getElementById('s1Year');
    if (dup) {
        _isDuplicate = true;
        document.getElementById('dupWarnLabel').textContent  = dup.label;
        document.getElementById('dupWarnStatus').textContent = dup.status;
        document.getElementById('dupWarnLink').href          = PAYROLL_INDEX_URL + '?period_id=' + dup.id;
        warn.style.display = 'flex'; btn.disabled = true;
        mSel.classList.add('has-dup'); ySel.classList.add('has-dup');
    } else {
        _isDuplicate = false;
        warn.style.display = 'none'; btn.disabled = false;
        mSel.classList.remove('has-dup'); ySel.classList.remove('has-dup');
    }
}

/* ── Step navigation ── */
function goToStep2() {
    if (_isDuplicate) { showToast('Duplicate Period','A payroll for this month/year already exists.','error'); return; }
    const label = document.getElementById('s1Label').value.trim();
    if (!label) { showToast('Required','Please enter a period label.','error'); return; }
    selectedMonth     = parseInt(document.getElementById('s1Month').value);
    selectedMonthName = document.getElementById('s1Month').selectedOptions[0].dataset.name;
    selectedYear      = parseInt(document.getElementById('s1Year').value);
    document.getElementById('monthPillLabel').textContent = selectedMonthName;
    document.getElementById('yearPillLabel').textContent  = selectedYear;
    syncPillSelections();
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'flex';
    document.getElementById('step2').style.flexDirection = 'column';
    document.getElementById('periodPills').style.display = 'flex';
    document.getElementById('bc3sep').style.display = '';
    document.getElementById('bc3').style.display    = '';
    document.getElementById('bc2').style.fontWeight = '400';
    updateCounts();
}
function goBack() {
    closeEmpPanel();
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = '';
    document.getElementById('periodPills').style.display = 'none';
    document.getElementById('bc3sep').style.display = 'none';
    document.getElementById('bc3').style.display    = 'none';
}
function updateLabel() {
    const mSel  = document.getElementById('s1Month');
    const mName = mSel.selectedOptions[0].dataset.name;
    document.getElementById('s1Label').value = mName + ' ' + document.getElementById('s1Year').value;
}

/* ── Period pills ── */
function togglePill(id) {
    document.querySelectorAll('.pill-dropdown').forEach(d => { if (d.id !== id) d.classList.remove('open'); });
    document.getElementById(id).classList.toggle('open');
}
function syncPillSelections() {
    document.querySelectorAll('#monthDrop .pill-option').forEach(o => o.classList.toggle('selected', parseInt(o.dataset.val) === selectedMonth));
    document.querySelectorAll('#yearDrop .pill-option').forEach(o => o.classList.toggle('selected', parseInt(o.dataset.val) === selectedYear));
}
function selectMonth(m, name) {
    selectedMonth = m; selectedMonthName = name;
    document.getElementById('monthPillLabel').textContent = name;
    document.getElementById('monthDrop').classList.remove('open');
    syncPillSelections();
    document.getElementById('s1Month').value = m;
    updateLabel(); checkDuplicate();
}
function selectYear(y) {
    selectedYear = y;
    document.getElementById('yearPillLabel').textContent = y;
    document.getElementById('yearDrop').classList.remove('open');
    syncPillSelections();
    document.getElementById('s1Year').value = y;
    updateLabel(); checkDuplicate();
}

/* ── Cell focus ── */
function focusCell(input) {
    const cell = input.closest('.editable-cell');
    if (!cell.classList.contains('agri-locked')) cell.classList.add('focused');
}
function blurCell(input)  { input.closest('.editable-cell').classList.remove('focused'); }

/* ── RECALC ── */
function recalcRow(input) {
    const row     = input.closest('tr');
    const netCell = row.querySelector('.net-cell');
    if (!netCell) return;

    const gross     = parseFloat(netCell.dataset.gross)     || 0;
    const gsisEe    = parseFloat(netCell.dataset.gsisEe)    || 0;
    const pagibigEe = parseFloat(netCell.dataset.pagibigEe) || 0;
    const phicEe    = parseFloat(netCell.dataset.phicEe)    || 0;
    const pera      = parseFloat(netCell.dataset.pera)      || 0;

    const raCell = row.querySelector('[data-field="allowance_ra"] input');
    const taCell = row.querySelector('[data-field="allowance_ta"] input');
    const ra = raCell ? (parseFloat(raCell.value) || 0) : 0;
    const ta = taCell ? (parseFloat(taCell.value) || 0) : 0;

    let totalDeductions = 0;

    HARD_DEDUCTION_FIELDS.forEach(field => {
        const cell = row.querySelector(`[data-field="${field}"]`);
        if (cell) totalDeductions += parseFloat(cell.querySelector('input')?.value) || 0;
    });

    const net = gross - gsisEe - pagibigEe - phicEe - totalDeductions + pera + ra + ta;
    netCell.textContent = net.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});

    if (_activeRow === row) {
        document.getElementById('epNet').textContent = fmtPHP(net);
    }
    updateCounts();
}

/* ── Reset row (keeps agri RA/TA at their default, zeroes loans) ── */
function resetRow(empId) {
    const row = document.querySelector(`tr[data-emp-id="${empId}"]`);
    if (!row) return;
    const isAgri = row.dataset.isAgri === '1';

    row.querySelectorAll('.loan-input').forEach(inp => {
        const field = inp.closest('[data-field]')?.dataset.field;
        if (field === 'allowance_ra' && isAgri) {
            inp.value = computeFixed(DED_CONFIG.raType, DED_CONFIG.raValue, null, parseFloat(row.dataset.salary)||0).toFixed(2);
        } else if (field === 'allowance_ta' && isAgri) {
            inp.value = computeFixed(DED_CONFIG.taType, DED_CONFIG.taValue, null, parseFloat(row.dataset.salary)||0).toFixed(2);
        } else if (!inp.disabled) {
            inp.value = 0;
        }
    });
    recalcRow(row.querySelector('.loan-input:not(:disabled)') || row.querySelector('.loan-input'));
    closeDots();
    if (_activeRow === row) refreshPanelInputs(row);
    showToast('Reset','Loans/deductions reset to default values.','info');
}

/* ── Checkbox logic ── */
function toggleAll(cb) {
    document.querySelectorAll('.emp-chk').forEach(chk => {
        const row = chk.closest('tr');
        if (row.style.display !== 'none') { chk.checked = cb.checked; updateRowStyle(chk); }
    });
    onChkChange();
}
function onChkChange() {
    const all  = [...document.querySelectorAll('.emp-chk')];
    const vis  = all.filter(c => c.closest('tr').style.display !== 'none');
    const chkd = vis.filter(c => c.checked);
    const ca   = document.getElementById('chkAll');
    ca.checked       = chkd.length > 0 && chkd.length === vis.length;
    ca.indeterminate = chkd.length > 0 && chkd.length < vis.length;
    all.forEach(c => updateRowStyle(c));
    document.getElementById('btnDelete').disabled = chkd.length === 0;
    updateCounts();
}
function updateRowStyle(chk) { chk.closest('tr').classList.toggle('row-excluded', !chk.checked); }
function updateCounts() {
    const included = [...document.querySelectorAll('.emp-chk:checked')];
    const total    = [...document.querySelectorAll('.emp-chk')];
    document.getElementById('selectedCount').textContent = included.length;
    document.getElementById('totalCount').textContent    = total.length;
    document.getElementById('totalCount2').textContent   = total.length;
    let gross = 0, net = 0;
    included.forEach(chk => {
        const row = chk.closest('tr');
        gross += parseFloat(row.dataset.salary) || 0;
        const nc = row.querySelector('.net-cell');
        if (nc) net += parseFloat(nc.textContent.replace(/,/g,'')) || 0;
    });
    document.getElementById('grossTotal').textContent = '₱' + gross.toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('netTotal').textContent   = '₱' + net.toLocaleString('en-PH',{minimumFractionDigits:2});
    const btn = document.getElementById('btnProceed2');
    if (btn) btn.disabled = included.length === 0;
}

/* ── Filter ── */
function toggleFilter() { document.getElementById('filterPanel').classList.toggle('open'); }
function filterEmployees() {
    const search = document.getElementById('empSearch').value.toLowerCase();
    const dept   = document.getElementById('fDept')?.value   || '';
    const status = document.getElementById('fStatus')?.value || '';
    document.querySelectorAll('#empTbody tr[data-emp-id]').forEach(row => {
        const mSearch = !search || (row.dataset.name||'').includes(search);
        const mDept   = !dept   || row.dataset.dept === dept;
        const isIncl  = row.querySelector('.emp-chk')?.checked;
        const mStatus = !status || (status === 'included' && isIncl) || (status === 'excluded' && !isIncl);
        row.style.display = (mSearch && mDept && mStatus) ? '' : 'none';
    });
    onChkChange();
}
function clearFilter() {
    document.getElementById('fDept').value = '';
    document.getElementById('fStatus').value = '';
    document.getElementById('empSearch').value = '';
    filterEmployees();
}

/* ── Include/Exclude ── */
function excludeRow(empId) { const chk = document.querySelector(`.emp-chk[value="${empId}"]`); if (chk) { chk.checked = false; updateRowStyle(chk); } closeDots(); onChkChange(); }
function includeRow(empId) { const chk = document.querySelector(`.emp-chk[value="${empId}"]`); if (chk) { chk.checked = true;  updateRowStyle(chk); } closeDots(); onChkChange(); }
function removeSelected()  { document.querySelectorAll('.emp-chk:checked').forEach(chk => { chk.checked = false; updateRowStyle(chk); }); onChkChange(); }

/* ── Dots ── */
function closeDots() { document.querySelectorAll('.dot-dropdown').forEach(d => d.classList.remove('open')); }
function toggleDot(btn) { const dd = btn.nextElementSibling; closeDots(); dd.classList.toggle('open'); }

/* ── Sort ── */
function sortEmp(col) {
    const tbody = document.getElementById('empTbody');
    const rows  = [...tbody.querySelectorAll('tr[data-emp-id]')];
    if (sortCol === col) sortAsc = !sortAsc; else { sortCol = col; sortAsc = true; }
    rows.sort((a,b) => {
        let va = a.cells[col]?.textContent.trim() || '';
        let vb = b.cells[col]?.textContent.trim() || '';
        if (col === 5) { va = parseFloat(a.dataset.salary)||0; vb = parseFloat(b.dataset.salary)||0; return sortAsc ? va-vb : vb-va; }
        return sortAsc ? va.localeCompare(vb) : vb.localeCompare(va);
    });
    rows.forEach(r => tbody.appendChild(r));
}

/* ── Employee panel ── */
function openEmpPanel(row) {
    if (row && row.tagName !== 'TR') row = row.closest('tr');
    if (!row) return;
    _activeRow = row;
    document.querySelectorAll('#empTbody tr').forEach(r => r.classList.remove('row-active'));
    row.classList.add('row-active');

    const empId   = row.dataset.empId;
    const name    = row.dataset.fullName  || '—';
    const desig   = row.dataset.designation || '—';
    const posName = row.dataset.positionName || desig;
    const dept    = row.dataset.departmentName || '—';
    const gross   = parseFloat(row.dataset.salary) || 0;
    const isAgri  = row.dataset.isAgri === '1';
    const netCell = row.querySelector('.net-cell');

    const gsisEe     = parseFloat(netCell?.dataset.gsisEe)    || 0;
    const gsisGovt   = parseFloat(netCell?.dataset.gsisGovt)  || 0;
    const gsisEc     = parseFloat(netCell?.dataset.gsisEc)    || 0;
    const pagibigEe  = parseFloat(netCell?.dataset.pagibigEe) || 0;
    const pagibigGov = parseFloat(netCell?.dataset.pagibigGov)|| 0;
    const phicEe     = parseFloat(netCell?.dataset.phicEe)    || 0;
    const phicGovt   = parseFloat(netCell?.dataset.phicGovt)  || 0;
    const pera       = parseFloat(netCell?.dataset.pera)      || 0;

    document.getElementById('epName').textContent = name;
    document.getElementById('epSub').textContent  = `${empId}  ·  ${posName}${isAgri ? '  🌾' : ''}`;
    document.getElementById('epNet').textContent  = netCell
        ? fmtPHP(parseFloat(netCell.textContent.replace(/,/g,'')))
        : fmtPHP(0);

    const mkInfo = (label, val, color) =>
        `<div class="ep-field"><label>${label}</label><p${color ? ` style="color:${color}"` : ''}>${val}</p></div>`;
    const mkInfoSpan2 = (label, val) =>
        `<div class="ep-field span2"><label>${label}</label><p>${val}</p></div>`;
    const mkEdit = (label, field) => {
        const inp = row.querySelector(`[data-field="${field}"] input`);
        const val = inp ? parseFloat(inp.value||0).toFixed(2) : '0.00';
        return `<div class="ep-edit"><label>${label}</label>
            <input type="number" step="0.01" min="0" value="${val}"
                data-panel-field="${field}" oninput="syncPanelField(this)">
        </div>`;
    };
    const mkEditAgri = (label, field) => {
        const inp = row.querySelector(`[data-field="${field}"] input`);
        const val = inp ? parseFloat(inp.value||0).toFixed(2) : '0.00';
        if (isAgri) {
            return `<div class="ep-edit"><label>${label} <span style="color:#16a34a;font-size:9px;">(PA Only)</span></label>
                <input type="number" step="0.01" min="0" value="${val}"
                    data-panel-field="${field}" oninput="syncPanelField(this)">
            </div>`;
        }
        return `<div class="ep-edit"><label>${label} <span style="color:#d1d5db;font-size:9px;">(N/A for this position)</span></label>
            <input type="number" step="0.01" min="0" value="0.00" disabled
                style="background:#f3f4f6;color:#9ca3af;cursor:not-allowed;">
        </div>`;
    };

    const mkCard = (icon, title, inner) =>
        `<div class="ep-card">
            <div class="ep-card-heading">
                <div class="ep-card-icon">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"/>
                    </svg>
                </div>
                <p class="ep-card-title">${title}</p>
            </div>
            <div class="ep-grid">${inner}</div>
        </div>`;

    /* Build cooperative section from hard columns */
    const cngKeys = {!! json_encode($cngCols) !!};
    const coopRows = Object.keys(cngKeys).map(field => {
        return mkEdit(cngKeys[field], field);
    }).join('');
    
    let coopCard = mkCard(
        'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'CNGWPC Cooperative',
        coopRows
    );

    document.getElementById('epBody').innerHTML =
        mkCard('M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'Employee Information',
            mkInfo('Gross Salary', fmtPHP(gross), '#111827') +
            mkInfo('Designation', desig) +
            mkInfoSpan2('Department', dept)
        ) +
        mkCard('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'GSIS',
            mkInfo('Personal Share (9%)', fmtPHP(gsisEe), '#1e40af') +
            mkInfo("Govt Share (12%) *", fmtPHP(gsisGovt), '#9ca3af') +
            mkInfo('ECF *', fmtPHP(gsisEc), '#9ca3af') +
            mkEdit('Policy Loan',    'gsis_policy') +
            mkEdit('Emergency Loan', 'gsis_emergency') +
            mkEdit('Real Estate',    'gsis_real_estate') +
            mkEdit('MPL',            'gsis_mpl') +
            mkEdit('MPL Lite',       'gsis_mpl_lite') +
            mkEdit('GFAL',           'gsis_gfal') +
            mkEdit('Computer Loan',  'gsis_computer') +
            mkEdit('Conso Loan',     'gsis_conso')
        ) +
        mkCard('M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Pag-IBIG',
            mkInfo('Personal Share (₱200)', fmtPHP(pagibigEe), '#7c3aed') +
            mkInfo("Govt Share (₱200) *", fmtPHP(pagibigGov), '#9ca3af') +
            mkEdit('MPL Loan',      'pagibig_mpl') +
            mkEdit('Calamity Loan', 'pagibig_calamity')
        ) +
        mkCard('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'PhilHealth',
            mkInfo('Personal Share (2.5%)', fmtPHP(phicEe), '#0891b2') +
            mkInfo("Govt Share (2.5%) *", fmtPHP(phicGovt), '#9ca3af')
        ) +
        mkCard('M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            'Withholding Tax &amp; Other Loans',
            mkEdit('Withholding Tax', 'withholding_tax') +
            mkEdit('DBP Loan',        'loan_dbp') +
            mkEdit('LBP Loan',        'loan_lbp') +
            mkEdit('PARACLE',         'loan_paracle') +
            mkEdit('Overpayment',     'overpayment') +
            mkEdit('Other Deduction', 'other_deduction')
        ) +
        coopCard +
        mkCard('M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Allowances',
            mkInfo('PERA', fmtPHP(pera), '#065f46') +
            mkEditAgri('RA (Representation Allowance)', 'allowance_ra') +
            mkEditAgri('TA (Transportation Allowance)', 'allowance_ta')
        ) +
        `<p style="font-size:10px;color:#9ca3af;text-align:center;padding:8px 16px 4px;">
            * Employer-paid — not deducted from employee net pay<br>
            🌾 RA &amp; TA apply to Provincial Agriculturist positions only
        </p>`;

    document.getElementById('empPanel').classList.add('open');
    document.getElementById('empOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
    closeDots();
}

function syncPanelField(input) {
    if (!_activeRow) return;
    const field    = input.dataset.panelField;
    const rowInput = _activeRow.querySelector(`[data-field="${field}"] input`);
    if (rowInput && !rowInput.disabled) { rowInput.value = input.value; recalcRow(rowInput); }
}

function refreshPanelInputs(row) {
    document.querySelectorAll('#epBody [data-panel-field]').forEach(inp => {
        const rowInput = row.querySelector(`[data-field="${inp.dataset.panelField}"] input`);
        if (rowInput) inp.value = parseFloat(rowInput.value||0).toFixed(2);
    });
    const netCell = row.querySelector('.net-cell');
    if (netCell) document.getElementById('epNet').textContent = fmtPHP(parseFloat(netCell.textContent.replace(/,/g,'')));
}
function saveEmpPanel() {
    const name = document.getElementById('epName').textContent;
    closeEmpPanel();
    showToast('Saved', `Deductions updated for ${name}.`, 'success');
    updateCounts();
}
function closeEmpPanel() {
    document.getElementById('empPanel').classList.remove('open');
    document.getElementById('empOverlay').classList.remove('show');
    document.body.style.overflow = '';
    document.querySelectorAll('#empTbody tr').forEach(r => r.classList.remove('row-active'));
    _activeRow = null;
}

/* ── Confirm & Submit ── */
function confirmProceed() {
    if (_isDuplicate) { showToast('Duplicate Period','A payroll for this month/year already exists.','error'); return; }
    const included = [...document.querySelectorAll('.emp-chk:checked')];
    if (included.length === 0) { showToast('No employees','Please include at least one employee.','error'); return; }
    const label = document.getElementById('s1Label').value.trim();
    let gross = 0, net = 0;
    included.forEach(chk => {
        const row = chk.closest('tr');
        gross += parseFloat(row.dataset.salary) || 0;
        const nc = row.querySelector('.net-cell');
        if (nc) net += parseFloat(nc.textContent.replace(/,/g,'')) || 0;
    });
    document.getElementById('cPeriod').textContent = label || (selectedMonthName + ' ' + selectedYear);
    document.getElementById('cCount').textContent  = included.length + ' employees';
    document.getElementById('cGross').textContent  = '₱' + gross.toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('cNet').textContent    = '₱' + net.toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('cModalDesc').textContent = `Generating payroll for ${included.length} employee(s) — ${label}.`;
    document.getElementById('cModal').classList.add('show');
}
function closeCModal() { document.getElementById('cModal').classList.remove('show'); }

function submitPayroll() {
    if (_isDuplicate) { closeCModal(); showToast('Duplicate Period','Cannot create duplicate payroll.','error'); return; }
    const btn = document.getElementById('cProceedBtn');
    btn.disabled = true; btn.textContent = 'Generating…';

    document.getElementById('hMonth').value = selectedMonth;
    document.getElementById('hYear').value  = selectedYear;
    document.getElementById('hLabel').value = document.getElementById('s1Label').value.trim() || (selectedMonthName + ' ' + selectedYear);

    const hEmp = document.getElementById('hEmployees'); hEmp.innerHTML = '';
    const hOvr = document.getElementById('hOverrides'); hOvr.innerHTML = '';

    document.querySelectorAll('.emp-chk:checked').forEach(chk => {
        const empId = chk.value;
        const row   = chk.closest('tr');

        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'employee_ids[]'; inp.value = empId;
        hEmp.appendChild(inp);

        /* Send all input values. Backend computes loan_cngwmpc automatically. */
        row.querySelectorAll('[data-field] input.loan-input').forEach(fi => {
            const field = fi.closest('[data-field]').dataset.field;
            const oi = document.createElement('input');
            oi.type = 'hidden'; oi.name = `overrides[${empId}][${field}]`; oi.value = fi.value || '0';
            hOvr.appendChild(oi);
        });
    });

    document.getElementById('submitForm').submit();
}

/* ── Toast ── */
function showToast(title, msg, type) {
    const map = {
        success: {bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},
        error:   {bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},
        info:    {bg:'#dbeafe',c:'#2563eb',p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}
    };
    const s = map[type] || map.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;
    document.getElementById('toastIcon').innerHTML    = `<svg style="width:16px;height:16px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    document.getElementById('toastIcon').style.background = s.bg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3200);
}

/* ── Boot ── */
document.addEventListener('DOMContentLoaded', () => {
    updateCounts();
    checkDuplicate();
    document.querySelectorAll('.emp-chk').forEach(c => c.addEventListener('change', function () { updateRowStyle(this); onChkChange(); }));
});
document.addEventListener('click', e => {
    if (!e.target.closest('.period-pill') && !e.target.closest('.pill-dropdown'))
        document.querySelectorAll('.pill-dropdown').forEach(d => d.classList.remove('open'));
    if (!e.target.closest('.dot-menu')) closeDots();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeEmpPanel(); closeCModal(); }
});
</script>
@endsection