@extends('layouts.app')
@section('title', 'Create Payroll')
@section('page-title', 'Payroll')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

.pay-create-page { display: flex; flex-direction: column; height: calc(100vh - 80px); }
.main-card { background: #fff; border-radius: 16px; border: 1px solid #e9ecef; box-shadow: 0 4px 20px rgba(0,0,0,.05); overflow: hidden; flex: 1; display: flex; flex-direction: column; min-height: 0; }

.card-topbar { padding: 18px 26px 15px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; border-bottom: 1px solid #f0f2f0; background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%); flex-shrink: 0; }
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

#step1 { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 52px 26px; overflow-y: auto; }
.step1-inner { width: 100%; max-width: 510px; margin: 0 auto; }
.step1-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700; color: #15803d; margin-bottom: 18px; }
.step1-header h2 { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 8px; letter-spacing: -.5px; }
.step1-header p { font-size: 13px; color: #6b7280; margin: 0 0 32px; line-height: 1.6; }
.step1-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.field-group label { display: block; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
.step1-select, .step1-input { width: 100%; padding: 11px 14px; font-size: 13px; border: 1.5px solid #e9ecef; border-radius: 11px; color: #111827; background: #fafafa; outline: none; transition: border-color .15s, background .15s, box-shadow .15s; }
.step1-select { padding-right: 36px; appearance: none; -webkit-appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
.step1-select:focus, .step1-input:focus { border-color: #2d5a1b; background: #fff; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.step1-select.has-dup { border-color: #f59e0b !important; background-color: #fffbeb; }
.warn-box { border-radius: 12px; padding: 13px 15px; font-size: 12px; margin-bottom: 16px; line-height: 1.65; display: flex; gap: 10px; align-items: flex-start; }
.warn-box.yellow { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.warn-box.red    { background: #fff1f2; border: 1px solid #fecaca; color: #b91c1c; }
.btn-proceed { width: 100%; padding: 14px; font-size: 14px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b); border: none; border-radius: 12px; cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px rgba(26,58,26,.3); display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-proceed:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,58,26,.38); }
.btn-proceed:disabled { opacity: .5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

#step2 { display: none; flex-direction: column; flex: 1; min-height: 0; }
.sub-toolbar { padding: 15px 26px; border-bottom: 1px solid #f0f2f0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: linear-gradient(135deg, #fafffe, #f6faf6); flex-shrink: 0; }
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
.btn-outline { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #374151; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 9px; cursor: pointer; transition: all .15s; }
.btn-outline:hover { border-color: #d1d5db; background: #f9fafb; color: #111827; }
.btn-icon-green { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; font-size: 12px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #16a34a, #15803d); border: none; border-radius: 9px; cursor: pointer; transition: all .15s; box-shadow: 0 2px 6px rgba(22,163,74,.3); }
.btn-icon-green:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(22,163,74,.4); }

#filterPanel { display: none; padding: 12px 26px; gap: 14px; align-items: center; flex-wrap: wrap; border-bottom: 1px solid #f3f4f6; background: #fafffe; flex-shrink: 0; }
#filterPanel.open { display: flex; }
.fp-select { padding: 7px 28px 7px 10px; font-size: 12px; border: 1.5px solid #e9ecef; border-radius: 8px; color: #374151; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 8px center; appearance: none; -webkit-appearance: none; outline: none; }

.tsa { flex: 1; overflow-x: auto; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #d1d5db transparent; min-height: 0; }
.tsa::-webkit-scrollbar { width: 5px; height: 5px; }
.tsa::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.data-table thead { position: sticky; top: 0; z-index: 10; }

.thead-group tr:first-child th { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: 7px 10px; border-right: 1px solid rgba(255,255,255,.15); text-align: center; color: #fff; transition: display 0s; }
.thead-group tr:nth-child(2) th { padding: 7px 8px; font-size: 9px; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; cursor: pointer; user-select: none; border-right: 1px solid #e9ecef; text-align: center; transition: display 0s; }
.thead-group tr:nth-child(2) th.no-sort { cursor: default; }
.thead-group tr:nth-child(2) th .sarr { display: inline-block; margin-left: 3px; opacity: .3; font-size: 9px; }

/* ── Sticky Column & Alternating Colors CSS ── */
.col-sticky-name {
    position: sticky;
    left: 0;
    z-index: 2;
    background: inherit;
    border-right: 2px solid #e5e7eb !important;
    box-shadow: 2px 0 5px rgba(0,0,0,0.02);
}
.data-table thead th.col-sticky-name { z-index: 12; }
.thead-group tr:first-child th.col-sticky-name { background-color: #1a3a1a !important; }
.thead-group tr:nth-child(2) th.col-sticky-name { background-color: #f0fdf4 !important; }

.data-table tbody tr { background-color: #ffffff; transition: background .12s; }
.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
.data-table tbody tr:hover { background-color: #f0fdf4 !important; }

.grp-employee  { background: #1a3a1a; }
.grp-gsis      { background: #1e40af; }
.grp-pagibig   { background: #7c3aed; }
.grp-phic      { background: #0891b2; }
.grp-wtax      { background: #b45309; }
.grp-loans     { background: #92400e; }
.grp-cngwpc    { background: #6b21a8; }
.grp-dynded    { background: #475569; }
.grp-allowance { background: #065f46; }
.grp-net       { background: #991b1b; }
.grp-action    { background: #374151; }

.sub-employee  { background: #f0fdf4 !important; color: #166534 !important; }
.sub-gsis      { background: #dbeafe !important; color: #1e40af !important; }
.sub-pagibig   { background: #ede9fe !important; color: #5b21b6 !important; }
.sub-phic      { background: #cffafe !important; color: #155e75 !important; }
.sub-wtax      { background: #fef3c7 !important; color: #92400e !important; }
.sub-loans     { background: #ffedd5 !important; color: #92400e !important; }
.sub-cngwpc    { background: #f3e8ff !important; color: #6b21a8 !important; }
.sub-dynded    { background: #f8fafc !important; color: #475569 !important; }
.sub-allowance { background: #d1fae5 !important; color: #065f46 !important; }
.sub-net       { background: #fee2e2 !important; color: #991b1b !important; font-weight: 800 !important; }
.sub-action    { background: #f3f4f6 !important; }

.data-table td { padding: 0; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #f3f4f6; color: #374151; vertical-align: middle; white-space: nowrap; transition: display 0s; }
.data-table tbody tr.row-active { background: #dcfce7 !important; box-shadow: inset 3px 0 0 #2d5a1b; }
.data-table tbody tr.row-excluded { opacity: .35; }
.mono { font-family: 'JetBrains Mono', monospace; font-size: 11px; }
.num-cell { text-align: right; font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 500; padding: 8px 10px !important; }
.emp-name { font-weight: 700; color: #111827; font-size: 12.5px; padding: 8px 10px 2px; }
.emp-dept { font-size: 10px; color: #9ca3af; padding: 0 10px 8px; }
input[type="checkbox"] { width: 15px; height: 15px; accent-color: #1a3a1a; cursor: pointer; }

.editable-cell { position: relative; cursor: text; transition: background .15s; min-width: 70px; text-align: right; }
.editable-cell input { width: 100%; background: transparent; border: none; outline: none; font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 500; color: #374151; text-align: right; padding: 8px 10px; cursor: text; min-width: 65px; }
.editable-cell.focused { background: #fff !important; box-shadow: inset 0 0 0 2px #2d5a1b; border-radius: 4px; }
.editable-cell.focused input { color: #111827; }
.editable-cell.agri-allowance { background: transparent; }
.editable-cell.agri-allowance:hover { background: rgba(220, 252, 231, 0.4); }
.editable-cell.agri-locked { background: transparent !important; cursor: not-allowed; }
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

.employer-col { color: #9ca3af !important; font-style: italic; }

.cmodal-bg { position: fixed; inset: 0; z-index: 1500; background: rgba(0,0,0,.45); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .22s; padding: 16px; }
.cmodal-bg.show { opacity: 1; pointer-events: all; }
.cmodal-card { background: #fff; border-radius: 20px; padding: 28px; width: min(98vw, 440px); box-shadow: 0 28px 70px rgba(0,0,0,.22); transform: scale(.92) translateY(14px); transition: transform .28s cubic-bezier(.34,1.56,.64,1); }
.cmodal-bg.show .cmodal-card { transform: scale(1) translateY(0); }
.cstat-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
.cstat-row:last-child { border-bottom: none; }
.cstat-row .lbl { color: #6b7280; }
.cstat-row .val { font-weight: 700; color: #111827; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; }

.modal-input { width: 100%; padding: 10px 14px; font-size: 13px; border: 1.5px solid #e9ecef; border-radius: 10px; background: #fafafa; color: #111827; outline: none; margin-bottom: 14px; transition: border-color .15s, background .15s; font-family: 'Plus Jakarta Sans', sans-serif; }
.modal-input:focus { border-color: #2d5a1b; background: #fff; }
.modal-label { display: block; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }

/* Sub-column accordion styles */
.col-category { margin-bottom: 8px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.cat-header { padding: 12px 16px; background: #f9fafb; font-weight: 700; font-size: 13px; color: #111827; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background .15s; }
.cat-header:hover { background: #f3f4f6; }
.cat-content { display: none; background: #fff; border-top: 1px solid #e5e7eb; }
.cat-content.open { display: block; }
.manage-col-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #f3f4f6; transition: background .1s; }
.manage-col-item:hover { background: #fafafa; }
.manage-col-item:last-child { border-bottom: none; }
.chev { width: 14px; height: 14px; transition: transform .2s ease; color: #9ca3af; }
.chev.rot { transform: rotate(180deg); }

#empOverlay { position: fixed; inset: 0; z-index: 1090; background: rgba(0,0,0,0.35); backdrop-filter: blur(4px); opacity: 0; pointer-events: none; transition: opacity .3s ease; }
#empOverlay.show { opacity: 1; pointer-events: all; }
#empPanel { position: fixed; top: 0; right: 0; bottom: 0; z-index: 1100; width: 55vw; min-width: 380px; max-width: 860px; display: flex; flex-direction: column; pointer-events: none; transform: translateX(100%); transition: transform .36s cubic-bezier(.32,.72,0,1); }
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

#toast { position: fixed; bottom: 22px; right: 22px; z-index: 2000; background: #fff; border-radius: 15px; padding: 13px 17px; box-shadow: 0 10px 36px rgba(0,0,0,.16); display: flex; align-items: center; gap: 11px; min-width: 230px; max-width: calc(100vw - 44px); opacity: 0; transform: translateY(14px); transition: all .32s cubic-bezier(.34,1.56,.64,1); pointer-events: none; }
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

    /* Setup arrays for our grouped modal UI */
    $modalGroups = [
        'GSIS Loans' => [
            'gsis_policy' => 'Policy Loan', 'gsis_emergency' => 'Emergency Loan', 'gsis_real_estate' => 'Real Estate',
            'gsis_mpl' => 'MPL', 'gsis_mpl_lite' => 'MPL Lite', 'gsis_gfal' => 'GFAL',
            'gsis_computer' => 'Computer Loan', 'gsis_conso' => 'Conso Loan'
        ],
        'Pag-IBIG Loans' => [
            'pagibig_mpl' => 'MPL Loan', 'pagibig_calamity' => 'Calamity Loan'
        ],
        'Other Default Loans' => [
            'loan_dbp' => 'DBP Loan', 'loan_lbp' => 'LBP Loan', 'loan_paracle' => 'PARACLE',
            'overpayment' => 'Overpayment', 'other_deduction' => 'Other Deduction'
        ],
        'CNGWPC Cooperative' => $cngCols,
        'Allowances' => [
            'allowance_rata' => 'RA (PA Only)', 'allowance_ta' => 'TA (PA Only)'
        ]
    ];

    /* ─── Load all active deductions from DB ─── */
    $everyDeduction = \App\Models\PayrollDeduction::orderBy('sort_order')->get();
    
    // Manageable Custom Columns for the modal (both deductions and allowances)
    $manageableCols = $everyDeduction->filter(function($ded) {
        if ($ded->parent_id == 9 || strtoupper($ded->name) === 'CNGWPC') return false;
        if (method_exists($ded, 'resolveColumn') && $ded->resolveColumn() !== null) return false;
        return true;
    });

    // Active config for the table layout
    $allDeductions = $everyDeduction->where('is_active', 1);

    /* ─── Filter purely dynamic deductions and allowances ─── */
    $dynamicDeductions = $allDeductions->filter(function($ded) {
        if ($ded->parent_id == 9 || strtoupper($ded->name) === 'CNGWPC') return false;
        if (in_array(strtoupper($ded->name), ['ROOT', 'INTERMEDIATE', 'ALLOWANCE_OTHER'])) return false; // STRICT BLOCK
        if (method_exists($ded, 'isAllowance') && $ded->isAllowance()) return false;
        if (is_null($ded->parent_id)) return false; // Excludes parent headers
        if (in_array($ded->id, [33, 34])) return false; // Exclude PhilHealth children
        $col = method_exists($ded, 'resolveColumn') ? $ded->resolveColumn() : null;
        if ($col !== null) return false;
        return true;
    });

    $dynamicAllowances = $allDeductions->filter(function($ded) {
        if (in_array(strtoupper($ded->name), ['ROOT', 'INTERMEDIATE', 'ALLOWANCE_OTHER'])) return false; // STRICT BLOCK
        if (method_exists($ded, 'isAllowance') && !$ded->isAllowance()) return false;
        if (is_null($ded->parent_id)) return false; // Excludes parent headers
        $col = method_exists($ded, 'resolveColumn') ? $ded->resolveColumn() : null;
        if ($col !== null) return false;
        return true;
    });

    $dbDeductions = $allDeductions->keyBy(fn($d) => strtolower($d->name));

    /* Helper: look up deduction record by exact name */
    $ded = fn(string $name) => $dbDeductions->get(strtolower($name));

    $gsisEeRec    = $ded('Life Retirement Insurance – Personal Share') ?? $ded('gsis employee share');
    $gsisGovtRec  = $ded('Life Retirement Insurance – Government Share') ?? $ded("gsis gov't share");
    $gsisEcRec    = $ded('ECF (Employee Compensation Fund)');
    $pagibigEeRec = $ded('Employee Share') ?? $ded('pagibig employee share');
    $pagibigGovRec= $ded('Employer Share') ?? $ded("pagibig gov't share");
    
    $phicEeRec    = $ded('Personal Share')   ?? $ded('philhealth employee share') ?? $ded('philhealth');
    $phicGovtRec  = $ded('Government Share') ?? $ded("philhealth gov't share") ?? $ded('philhealth');
    
    $peraRec      = $ded('PERA');
    $raRec        = $ded('RA');
    $taRec        = $ded('TA');

    // Ensure Limits are strictly numeric and > 0, otherwise NULL to fix Limit Capping issues
    $getLimit = function($rec) {
        return (isset($rec->limit_amount) && (float)$rec->limit_amount > 0) ? (float)$rec->limit_amount : null;
    };

    $jsConfig = [
        'gsisEeType'      => $gsisEeRec?->rate_type    ?? 'percent',
        'gsisEeValue'     => (float)($gsisEeRec?->rate_value   ?? 0.09),
        'gsisEeLimit'     => $getLimit($gsisEeRec),
        'gsisGovtType'    => $gsisGovtRec?->rate_type  ?? 'percent',
        'gsisGovtValue'   => (float)($gsisGovtRec?->rate_value ?? 0.12),
        'gsisGovtLimit'   => $getLimit($gsisGovtRec),
        'gsisEcType'      => $gsisEcRec?->rate_type    ?? 'flat',
        'gsisEcValue'     => (float)($gsisEcRec?->rate_value   ?? 100),
        'pagibigEeType'   => $pagibigEeRec?->rate_type  ?? 'flat',
        'pagibigEeValue'  => (float)($pagibigEeRec?->rate_value ?? 200),
        'pagibigGovType'  => $pagibigGovRec?->rate_type ?? 'flat',
        'pagibigGovValue' => (float)($pagibigGovRec?->rate_value ?? 200),
        'phicEeType'      => $phicEeRec?->rate_type    ?? 'percent',
        'phicEeValue'     => (float)($phicEeRec?->rate_value   ?? 0.025),
        'phicEeLimit'     => $getLimit($phicEeRec),
        'phicGovtType'    => $phicGovtRec?->rate_type  ?? 'percent',
        'phicGovtValue'   => (float)($phicGovtRec?->rate_value ?? 0.025),
        'phicGovtLimit'   => $getLimit($phicGovtRec),
        'peraType'        => $peraRec?->rate_type      ?? 'flat',
        'peraValue'       => (float)($peraRec?->rate_value     ?? 2000),
        'rataType'        => $raRec?->rate_type        ?? 'flat',
        'rataValue'       => (float)($raRec?->rate_value       ?? 9500),
        'taType'          => $taRec?->rate_type        ?? 'flat',
        'taValue'         => (float)($taRec?->rate_value       ?? 9500),
    ];

    $computeFromConfig = function(string $type, float $value, ?float $limit, float $gross): float {
        $amt = ($type === 'percent') ? round($gross * $value, 2) : round($value, 2);
        return ($limit !== null && $limit > 0) ? min($amt, $limit) : $amt;
    };

    $employees = \App\Models\Employee::with(['position','department'])
        ->where('is_active', 1)->orderBy('last_name')->get();

    /* ── FETCH LATEST RECORD FOR EACH EMPLOYEE TO CARRY FORWARD BALANCES ── */
    $latestRecords = \App\Models\PayrollRecord::whereIn('user_id', $employees->pluck('user_id'))
        ->orderBy('period_id', 'desc')
        ->get()
        ->groupBy('user_id')
        ->map(fn($records) => $records->first());

    $existingPeriods = \App\Models\PayrollPeriod::select('period_id','month','year','period_label','status')
        ->orderBy('year','desc')->orderBy('month','desc')->get();
    $existingPeriodMap = $existingPeriods->mapWithKeys(fn($p) => [
        $p->month . '-' . $p->year => ['label' => $p->period_label, 'status' => $p->status, 'id' => $p->period_id]
    ])->toArray();
@endphp

<div class="pay-create-page">
<div class="main-card">

    {{-- TOP BAR --}}
    <div class="card-topbar">
        <div class="card-topbar-left">
            <a href="{{ route('payroll.index') }}" class="btn-icon" style="margin-right:8px; border:none; background:transparent; box-shadow:none; padding:4px;" title="Exit to Payroll Index">
                <svg style="width:22px;height:22px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
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
                <p>Select the month and year. We will automatically carry forward your employees' loan balances and custom deductions from the previous month.</p>
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
                <div><strong>Loans Auto-Populated:</strong> Active employees' variable loans and custom dynamic deductions have been carried over from their last recorded payroll. Fixed deductions (GSIS, Pag-IBIG, PhilHealth, PERA) are freshly auto-calculated from DB rates.</div>
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
                
                {{-- QUICK ADD COLUMN BUTTON --}}
                <button class="btn-icon-green" onclick="openQuickAddModal()">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Column
                </button>

                {{-- MANAGE COLUMNS BUTTON --}}
                <button class="btn-outline" style="color:#374151;border-color:#e5e7eb;background:#fff;" onclick="openManageColumnsModal()">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Manage Columns
                </button>

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
                    <th class="grp-employee no-sort col-sticky-name" colspan="1" style="text-align:left;padding-left:14px;">Employee</th>
                    <th class="grp-employee no-sort" colspan="3"></th>

                    {{-- GSIS: 3 fixed + 8 editable = 11 cols --}}
                    <th class="grp-gsis no-sort" data-group="gsis" colspan="11" style="text-align:center;">G S I S</th>

                    {{-- PAG-IBIG: 2 fixed + 2 editable = 4 cols --}}
                    <th class="grp-pagibig no-sort" data-group="pagibig" colspan="4" style="text-align:center;">PAG-IBIG</th>

                    {{-- PHILHEALTH: 2 fixed --}}
                    <th class="grp-phic no-sort" data-group="phic" colspan="2" style="text-align:center;">PHILHEALTH</th>

                    {{-- WITHHOLDING TAX: 1 editable --}}
                    <th class="grp-wtax no-sort" data-group="wtax" colspan="1" style="text-align:center;">WithholdingTAX</th>

                    {{-- OTHER LOANS = 5 columns --}}
                    <th class="grp-loans no-sort" data-group="other_loans" colspan="5" style="text-align:center;">OTHER LOANS &amp; DEDUCTIONS</th>

                    {{-- CNGWPC cooperative deductions --}}
                    <th class="grp-cngwpc no-sort" data-group="cngwpc" colspan="{{ count($cngCols) }}" style="text-align:center;">CNGWPC COOPERATIVE</th>

                    {{-- DYNAMIC DEDUCTIONS --}}
                    @if($dynamicDeductions->count() > 0)
                    <th class="grp-dynded no-sort" data-group="dynded" colspan="{{ $dynamicDeductions->count() }}" style="text-align:center;">OTHER DEDUCTIONS</th>
                    @endif

                    {{-- ALLOWANCES: PERA(fixed) + RA(agri-editable) + TA(agri-editable) = 3 + Dynamic Allowances --}}
                    <th class="grp-allowance no-sort" data-group="allowance" colspan="{{ 3 + $dynamicAllowances->count() }}" style="text-align:center;">ALLOWANCES</th>

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
                    <th class="sub-employee col-sticky-name" onclick="sortEmp(2)" style="text-align:left;padding-left:10px;">Name <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(3)">Desig. <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(4)">Dept <span class="sarr">↕</span></th>
                    <th class="sub-employee" onclick="sortEmp(5)" style="text-align:right;">Gross Salary <span class="sarr">↕</span></th>

                    {{-- GSIS --}}
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_ee">Personal Share (9%)</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_govt" style="color:#9ca3af !important;">Government Share(12%)*</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_ec" style="color:#9ca3af !important;">ECF*</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_policy">Policy Loan</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_emergency">Emergency</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_real_estate">Real Estate</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_mpl">MPL</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_mpl_lite">MPL Lite</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_gfal">GFAL</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_computer">Computer</th>
                    <th class="sub-gsis no-sort" data-group="gsis" data-col="gsis_conso">Conso</th>

                    {{-- PAG-IBIG --}}
                    <th class="sub-pagibig no-sort" data-group="pagibig" data-col="pagibig_ee">Personal Share (₱200)</th>
                    <th class="sub-pagibig no-sort" data-group="pagibig" data-col="pagibig_govt" style="color:#9ca3af !important;">Government Share(₱200)*</th>
                    <th class="sub-pagibig no-sort" data-group="pagibig" data-col="pagibig_mpl">MPL</th>
                    <th class="sub-pagibig no-sort" data-group="pagibig" data-col="pagibig_calamity">Calamity</th>

                    {{-- PhilHealth --}}
                    <th class="sub-phic no-sort" data-group="phic" data-col="philhealth_ee">Personal Share (2.5%)</th>
                    <th class="sub-phic no-sort" data-group="phic" data-col="philhealth_govt" style="color:#9ca3af !important;">Government Share (2.5%)*</th>

                    {{-- W/Tax --}}
                    <th class="sub-wtax no-sort" data-group="wtax" data-col="withholding_tax">W/Tax</th>

                    {{-- Other Loans --}}
                    <th class="sub-loans no-sort" data-group="other_loans" data-col="loan_dbp">DBP</th>
                    <th class="sub-loans no-sort" data-group="other_loans" data-col="loan_lbp">LBP</th>
                    <th class="sub-loans no-sort" data-group="other_loans" data-col="loan_paracle">PARACLE</th>
                    <th class="sub-loans no-sort" data-group="other_loans" data-col="overpayment">Overpayment</th>
                    <th class="sub-loans no-sort" data-group="other_loans" data-col="other_deduction">Other Ded.</th>

                    {{-- CNGWPC cooperative columns --}}
                    @foreach($cngCols as $field => $label)
                    <th class="sub-cngwpc no-sort" data-group="cngwpc" data-col="{{ $field }}">{{ $label }}</th>
                    @endforeach

                    {{-- Dynamic Deductions sub-headers --}}
                    @foreach($dynamicDeductions as $d)
                    <th class="sub-dynded no-sort" data-group="dynded" data-col="dyn_{{ $d->id }}" title="{{ $d->name }}">{{ \Illuminate\Support\Str::limit($d->name, 10) }}</th>
                    @endforeach

                    {{-- Allowances --}}
                    <th class="sub-allowance no-sort" data-group="allowance" data-col="allowance_pera">PERA</th>
                    <th class="sub-allowance no-sort" data-group="allowance" data-col="allowance_rata" title="Representation Allowance — Provincial Agriculturist only">RA <span style="font-size:8px;opacity:.6;">(PA)</span></th>
                    <th class="sub-allowance no-sort" data-group="allowance" data-col="allowance_ta" title="Transportation Allowance — Provincial Agriculturist only">TA <span style="font-size:8px;opacity:.6;">(PA)</span></th>
                    
                    {{-- Dynamic Allowances sub-headers --}}
                    @foreach($dynamicAllowances as $a)
                    <th class="sub-allowance no-sort" data-group="allowance" data-col="dyn_{{ $a->id }}" title="{{ $a->name }}">{{ \Illuminate\Support\Str::limit($a->name, 10) }}</th>
                    @endforeach

                    {{-- Net Pay --}}
                    <th class="sub-net no-sort">Net Pay</th>

                    {{-- Action --}}
                    <th class="sub-action no-sort"></th>
                </tr>
            </thead>
            <tbody id="empTbody">
                @forelse($employees as $i => $emp)
                @php
                    /* ── FETCH LATEST RECORD FOR THIS EMPLOYEE ── */
                    $lastRecord = $latestRecords->get($emp->user_id);
                    $lastDynData = [];
                    if ($lastRecord) {
                        $lastDynData = is_string($lastRecord->dynamic_deductions) 
                            ? json_decode($lastRecord->dynamic_deductions, true) 
                            : ($lastRecord->dynamic_deductions ?? []);
                    }
                    $getCarried = fn($field) => $lastRecord ? (float)($lastRecord->{$field} ?? 0) : 0;

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
                    
                    $raDefault  = $isAgri ? $computeFromConfig($jsConfig['rataType'], $jsConfig['rataValue'], null, $gross) : 0.0;
                    $taDefault  = $isAgri ? $computeFromConfig($jsConfig['taType'], $jsConfig['taValue'], null, $gross) : 0.0;

                    /* ── CARRIED FORWARD HARDCODED LOANS ── */
                    $v_gsis_policy      = $getCarried('gsis_policy');
                    $v_gsis_emergency   = $getCarried('gsis_emergency');
                    $v_gsis_real_estate = $getCarried('gsis_real_estate');
                    $v_gsis_mpl         = $getCarried('gsis_mpl');
                    $v_gsis_mpl_lite    = $getCarried('gsis_mpl_lite');
                    $v_gsis_gfal        = $getCarried('gsis_gfal');
                    $v_gsis_computer    = $getCarried('gsis_computer');
                    $v_gsis_conso       = $getCarried('gsis_conso');
                    $v_pagibig_mpl      = $getCarried('pagibig_mpl');
                    $v_pagibig_calamity = $getCarried('pagibig_calamity');
                    $v_withholding_tax  = $getCarried('withholding_tax');
                    $v_loan_dbp         = $getCarried('loan_dbp');
                    $v_loan_lbp         = $getCarried('loan_lbp');
                    $v_loan_paracle     = $getCarried('loan_paracle');
                    $v_overpayment      = $getCarried('overpayment');
                    $v_other_deduction  = $getCarried('other_deduction');

                    $carriedLoansTotal = $v_gsis_policy + $v_gsis_emergency + $v_gsis_real_estate + $v_gsis_mpl + $v_gsis_mpl_lite + $v_gsis_gfal + $v_gsis_computer + $v_gsis_conso 
                                       + $v_pagibig_mpl + $v_pagibig_calamity 
                                       + $v_withholding_tax 
                                       + $v_loan_dbp + $v_loan_lbp + $v_loan_paracle 
                                       + $v_overpayment + $v_other_deduction;

                    foreach($cngCols as $f => $l) {
                        $carriedLoansTotal += $getCarried($f);
                    }

                    /* ── Compute dynamic deduction defaults (Carried Over or Config) ── */
                    $dynValues = [];
                    foreach($dynamicDeductions as $d) {
                        if ($lastRecord && isset($lastDynData[$d->id])) {
                            $val = (float)$lastDynData[$d->id];
                        } else {
                            $val = 0;
                            if (method_exists($d, 'isFixed') && $d->isFixed()) {
                                $val = $d->rate_type === 'percent' ? ($gross * $d->rate_value) : $d->rate_value;
                                if (isset($d->limit_amount) && (float)$d->limit_amount > 0) $val = min($val, (float)$d->limit_amount);
                            }
                        }
                        $dynValues[$d->id] = round($val, 2);
                    }

                    /* ── Compute dynamic allowance defaults (Carried Over or Config) ── */
                    $dynAddValues = [];
                    foreach($dynamicAllowances as $a) {
                        if ($lastRecord && isset($lastDynData[$a->id])) {
                            $val = (float)$lastDynData[$a->id];
                        } else {
                            $val = 0;
                            if (method_exists($a, 'isFixed') && $a->isFixed()) {
                                $val = $a->rate_type === 'percent' ? ($gross * $a->rate_value) : $a->rate_value;
                                if (isset($a->limit_amount) && (float)$a->limit_amount > 0) $val = min($val, (float)$a->limit_amount);
                            }
                        }
                        $dynAddValues[$a->id] = round($val, 2);
                    }

                    $sumDynDed = array_sum($dynValues);
                    $sumDynAdd = array_sum($dynAddValues);

                    // Net = Gross - EE Deductions - Carried Loans + Allowances
                    $netBase = $gross - $gsisEe - $pagibigEe - $phicEe - $sumDynDed - $carriedLoansTotal + $pera + $raDefault + $taDefault + $sumDynAdd;
                @endphp
                <tr data-user-id="{{ $emp->user_id }}"
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
                        <input type="checkbox" class="emp-chk" value="{{ $emp->user_id }}" checked onchange="onChkChange()">
                    </td>
                    <td class="row-num">{{ $i+1 }}</td>

                    {{-- Employee Info --}}
                    <td class="col-sticky-name">
                        <div class="emp-name">{{ $emp->last_name }}, {{ $emp->first_name }}@if($emp->extension_name) {{ $emp->extension_name }}@endif</div>
                        <div class="emp-dept mono">{{ $emp->user_id }}</div>
                    </td>
                    <td style="padding:8px;font-size:11.5px;font-weight:600;color:#374151;">{{ optional($emp->position)->position_code ?? '—' }}</td>
                    <td style="padding:8px;font-size:11px;color:#6b7280;max-width:120px;white-space:normal;line-height:1.3;">{{ optional($emp->department)->department_name ?? '—' }}</td>
                    <td class="num-cell" style="font-weight:800;color:#111827;">{{ number_format($gross,2) }}</td>

                    {{-- ── GSIS (Now Fully Editable) ── --}}
                    <td class="editable-cell" data-col="gsis_ee" data-field="gsis_ee" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($gsisEe, 2) }}" data-default="{{ number_format($gsisEe, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#1e40af;font-weight:700;">
                    </td>
                    <td class="editable-cell" data-col="gsis_govt" data-field="gsis_govt" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($gsisGovt, 2) }}" data-default="{{ number_format($gsisGovt, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#9ca3af;font-style:italic;">
                    </td>
                    <td class="editable-cell" data-col="gsis_ec" data-field="gsis_ec" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($gsisEc, 2) }}" data-default="{{ number_format($gsisEc, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#9ca3af;font-style:italic;">
                    </td>

                    {{-- ── GSIS Editable Loans ── --}}
                    <td class="editable-cell" data-col="gsis_policy"      data-field="gsis_policy"      onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_policy, 2) }}"      data-default="{{ number_format($v_gsis_policy, 2) }}"      class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_emergency"   data-field="gsis_emergency"   onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_emergency, 2) }}"   data-default="{{ number_format($v_gsis_emergency, 2) }}"   class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_real_estate" data-field="gsis_real_estate" onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_real_estate, 2) }}" data-default="{{ number_format($v_gsis_real_estate, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_mpl"         data-field="gsis_mpl"         onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_mpl, 2) }}"         data-default="{{ number_format($v_gsis_mpl, 2) }}"         class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_mpl_lite"    data-field="gsis_mpl_lite"    onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_mpl_lite, 2) }}"    data-default="{{ number_format($v_gsis_mpl_lite, 2) }}"    class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_gfal"        data-field="gsis_gfal"        onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_gfal, 2) }}"        data-default="{{ number_format($v_gsis_gfal, 2) }}"        class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_computer"    data-field="gsis_computer"    onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_computer, 2) }}"    data-default="{{ number_format($v_gsis_computer, 2) }}"    class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="gsis_conso"       data-field="gsis_conso"       onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_gsis_conso, 2) }}"       data-default="{{ number_format($v_gsis_conso, 2) }}"       class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── PAG-IBIG (Now Fully Editable) ── --}}
                    <td class="editable-cell" data-col="pagibig_ee" data-field="pagibig_ee" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($pagibigEe, 2) }}" data-default="{{ number_format($pagibigEe, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#7c3aed;font-weight:700;">
                    </td>
                    <td class="editable-cell" data-col="pagibig_govt" data-field="pagibig_govt" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($pagibigGov, 2) }}" data-default="{{ number_format($pagibigGov, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#9ca3af;font-style:italic;">
                    </td>
                    <td class="editable-cell" data-col="pagibig_mpl"      data-field="pagibig_mpl"      onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_pagibig_mpl, 2) }}"      data-default="{{ number_format($v_pagibig_mpl, 2) }}"      class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="pagibig_calamity" data-field="pagibig_calamity" onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_pagibig_calamity, 2) }}" data-default="{{ number_format($v_pagibig_calamity, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── PhilHealth (Now Fully Editable) ── --}}
                    <td class="editable-cell" data-col="philhealth_ee" data-field="philhealth_ee" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($phicEe, 2) }}" data-default="{{ number_format($phicEe, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#0891b2;font-weight:700;">
                    </td>
                    <td class="editable-cell" data-col="philhealth_govt" data-field="philhealth_govt" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($phicGovt, 2) }}" data-default="{{ number_format($phicGovt, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#9ca3af;font-style:italic;">
                    </td>

                    {{-- ── Withholding Tax ── --}}
                    <td class="editable-cell" data-col="withholding_tax"  data-field="withholding_tax"  onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_withholding_tax, 2) }}"  data-default="{{ number_format($v_withholding_tax, 2) }}"  class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── Other Loans ── --}}
                    <td class="editable-cell" data-col="loan_dbp"         data-field="loan_dbp"         onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_loan_dbp, 2) }}"         data-default="{{ number_format($v_loan_dbp, 2) }}"         class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="loan_lbp"         data-field="loan_lbp"         onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_loan_lbp, 2) }}"         data-default="{{ number_format($v_loan_lbp, 2) }}"         class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="loan_paracle"     data-field="loan_paracle"     onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_loan_paracle, 2) }}"     data-default="{{ number_format($v_loan_paracle, 2) }}"     class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="overpayment"      data-field="overpayment"      onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_overpayment, 2) }}"      data-default="{{ number_format($v_overpayment, 2) }}"      class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>
                    <td class="editable-cell" data-col="other_deduction"  data-field="other_deduction"  onclick="event.stopPropagation()"><input type="text" inputmode="decimal" value="{{ number_format($v_other_deduction, 2) }}"  data-default="{{ number_format($v_other_deduction, 2) }}"  class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)"></td>

                    {{-- ── CNGWPC Cooperative ── --}}
                    @foreach($cngCols as $field => $label)
                    @php $v_cng = $getCarried($field); @endphp
                    <td class="editable-cell" data-col="{{ $field }}" data-field="{{ $field }}" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($v_cng, 2) }}" data-default="{{ number_format($v_cng, 2) }}" class="loan-input dyn-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
                    </td>
                    @endforeach

                    {{-- ── Dynamic Deductions ── --}}
                    @foreach($dynamicDeductions as $d)
                    <td class="editable-cell" data-col="dyn_{{ $d->id }}" data-field="{{ $d->id }}" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($dynValues[$d->id], 2) }}" data-default="{{ number_format($dynValues[$d->id], 2) }}" data-dyn-id="{{ $d->id }}" class="loan-input dyn-ded-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
                    </td>
                    @endforeach

                    {{-- ── PERA: (Now Editable) ── --}}
                    <td class="editable-cell" data-col="allowance_pera" data-field="allowance_pera" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($pera, 2) }}" data-default="{{ number_format($pera, 2) }}" class="loan-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)" style="color:#065f46;font-weight:700;">
                    </td>

                    {{-- ── RA: editable for PA, locked (0) for others ── --}}
                    <td class="editable-cell {{ $isAgri ? 'agri-allowance' : 'agri-locked' }}"
                        data-col="allowance_rata"
                        data-field="allowance_rata"
                        onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal"
                               value="{{ number_format($raDefault, 2) }}"
                               class="loan-input"
                               {{ $isAgri ? '' : 'disabled' }}
                               oninput="recalcRow(this)"
                               onfocus="focusCell(this)"
                               onblur="blurCell(this)">
                    </td>

                    {{-- ── TA: editable for PA, locked (0) for others ── --}}
                    <td class="editable-cell {{ $isAgri ? 'agri-allowance' : 'agri-locked' }}"
                        data-col="allowance_ta"
                        data-field="allowance_ta"
                        onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal"
                               value="{{ number_format($taDefault, 2) }}"
                               class="loan-input"
                               {{ $isAgri ? '' : 'disabled' }}
                               oninput="recalcRow(this)"
                               onfocus="focusCell(this)"
                               onblur="blurCell(this)">
                    </td>

                    {{-- ── Dynamic Allowances ── --}}
                    @foreach($dynamicAllowances as $a)
                    <td class="editable-cell" data-col="dyn_{{ $a->id }}" data-field="{{ $a->id }}" onclick="event.stopPropagation()">
                        <input type="text" inputmode="decimal" value="{{ number_format($dynAddValues[$a->id], 2) }}" data-default="{{ number_format($dynAddValues[$a->id], 2) }}" data-dyn-id="{{ $a->id }}" class="loan-input dyn-add-input" oninput="recalcRow(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
                    </td>
                    @endforeach

                    {{-- ── Net Pay cell ── --}}
                    <td class="net-cell"
                        id="net_{{ $emp->user_id }}"
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
                                <button class="dot-item" onclick="excludeRow({{ $emp->user_id }})">
                                    <svg style="width:13px;height:13px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Exclude from Payroll
                                </button>
                                <button class="dot-item" onclick="includeRow({{ $emp->user_id }})">
                                    <svg style="width:13px;height:13px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Include in Payroll
                                </button>
                                <button class="dot-item danger" onclick="resetRow({{ $emp->user_id }})">
                                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Reset Loans to Default
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

{{-- Quick Add Column Modal --}}
<div id="quickAddModal" class="cmodal-bg" onclick="if(event.target===this)closeQuickAddModal()">
  <div class="cmodal-card">
      <h3 style="font-size:16px;font-weight:800;color:#111827;margin:0 0 4px;">➕ New Column</h3>
      <p style="font-size:12px;color:#6b7280;margin:0 0 20px;">Create a new deduction or allowance. <strong style="color:#b91c1c;">Note: Saving will refresh the page to rebuild the table.</strong></p>
      
      <label class="modal-label">Category Name</label>
      <input type="text" id="qaName" class="modal-input" placeholder="e.g. Palarong Panlalawigan">
      
      <label class="modal-label">Column Type</label>
      <select id="qaType" class="modal-input" style="padding-right:30px;appearance:none;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22none%22 stroke=%22%239ca3af%22 viewBox=%220 0 24 24%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 12px center;">
          <option value="deduction">Deduction (Minus from Net)</option>
          <option value="addition">Allowance (Add to Net)</option>
      </select>
      
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
          <button onclick="closeQuickAddModal()" style="padding:9px 18px;font-size:12px;font-weight:600;border:1.5px solid #e9ecef;border-radius:10px;color:#374151;background:#fff;cursor:pointer;">Cancel</button>
          <button onclick="submitQuickAdd()" id="qaBtn" style="padding:9px 18px;font-size:12px;font-weight:700;border:none;border-radius:10px;color:#fff;background:linear-gradient(135deg, #1a3a1a, #2d5a1b);cursor:pointer;box-shadow:0 3px 10px rgba(26,58,26,.25);">Save & Refresh</button>
      </div>
  </div>
</div>

{{-- Manage Sub Columns Modal (Accordion Style) --}}
<div id="manageColumnsModal" class="cmodal-bg" onclick="if(event.target===this)closeManageColumnsModal()">
  <div class="cmodal-card" style="width: min(98vw, 540px); max-height: 90vh; display: flex; flex-direction: column; padding: 24px;">
      <h3 style="font-size:16px;font-weight:800;color:#111827;margin:0 0 4px;">⚙️ Manage Columns</h3>
      <p style="font-size:12px;color:#6b7280;margin:0 0 16px;">Quickly clear carried-over balances to 0.00 for everyone, or hide columns to save space.</p>
      
      <div style="flex:1; overflow-y:auto; padding-right:6px; margin-bottom: 16px; scrollbar-width: thin;" id="mc-accordion">
          
          {{-- Standard Hardcoded Groups --}}
          @foreach($modalGroups as $groupName => $fields)
          <div class="col-category">
              <div class="cat-header" onclick="this.nextElementSibling.classList.toggle('open'); this.querySelector('.chev').classList.toggle('rot');">
                  {{ $groupName }}
                  <svg class="chev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
              <div class="cat-content">
                  @foreach($fields as $field => $label)
                  <div class="manage-col-item">
                      <div>
                          <strong style="font-size:12.5px;color:#111827;">{{ $label }}</strong>
                      </div>
                      <div style="display:flex;align-items:center;gap:12px;">
                          <button onclick="zeroOutField('{{ $field }}')" style="padding:6px 12px;font-size:11px;font-weight:600;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;cursor:pointer;transition:all .15s;">Zero Out All</button>
                          <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;gap:6px;">
                              <span style="font-size:11px;font-weight:600;color:#374151;">Visible</span>
                              <input type="checkbox" id="chk_hide_{{ $field }}" checked onchange="toggleCol('{{ $field }}', this.checked)" style="width:16px;height:16px;accent-color:#2d5a1b;cursor:pointer;">
                          </label>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
          @endforeach

          {{-- Dynamic/Custom Deductions --}}
          @if($dynamicDeductions->count() > 0)
          <div class="col-category">
              <div class="cat-header" onclick="this.nextElementSibling.classList.toggle('open'); this.querySelector('.chev').classList.toggle('rot');">
                  Other Custom Deductions
                  <svg class="chev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
              <div class="cat-content">
                  @foreach($dynamicDeductions as $col)
                  <div class="manage-col-item">
                      <div>
                          <strong style="font-size:12.5px;color:#111827;">{{ $col->name }}</strong>
                      </div>
                      <div style="display:flex;align-items:center;gap:12px;">
                          <button onclick="zeroOutDynamic({{ $col->id }})" style="padding:6px 12px;font-size:11px;font-weight:600;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;cursor:pointer;transition:all .15s;">Zero Out All</button>
                          <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;gap:6px;">
                              <span style="font-size:11px;font-weight:600;color:#374151;">Visible</span>
                              <input type="checkbox" id="chk_hide_dyn_{{ $col->id }}" checked onchange="toggleDynamicCol({{ $col->id }}, this.checked)" style="width:16px;height:16px;accent-color:#2d5a1b;cursor:pointer;">
                          </label>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
          @endif

          {{-- Dynamic/Custom Allowances --}}
          @if($dynamicAllowances->count() > 0)
          <div class="col-category">
              <div class="cat-header" onclick="this.nextElementSibling.classList.toggle('open'); this.querySelector('.chev').classList.toggle('rot');">
                  Other Custom Allowances
                  <svg class="chev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
              <div class="cat-content">
                  @foreach($dynamicAllowances as $col)
                  <div class="manage-col-item">
                      <div>
                          <strong style="font-size:12.5px;color:#111827;">{{ $col->name }}</strong>
                      </div>
                      <div style="display:flex;align-items:center;gap:12px;">
                          <button onclick="zeroOutDynamic({{ $col->id }})" style="padding:6px 12px;font-size:11px;font-weight:600;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;cursor:pointer;transition:all .15s;">Zero Out All</button>
                          <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;gap:6px;">
                              <span style="font-size:11px;font-weight:600;color:#374151;">Visible</span>
                              <input type="checkbox" id="chk_hide_dyn_{{ $col->id }}" checked onchange="toggleDynamicCol({{ $col->id }}, this.checked)" style="width:16px;height:16px;accent-color:#2d5a1b;cursor:pointer;">
                          </label>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
          @endif

      </div>

      <div style="display:flex;justify-content:flex-end;">
          <button onclick="closeManageColumnsModal()" class="btn-cancel-step">Done</button>
      </div>
  </div>
</div>

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
const CSRF = '{{ csrf_token() }}';

/* ── Utility ── */
function fmt(n)    { return parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtPHP(n) { return '₱'+fmt(n); }

// Helper to strip commas when doing math
function getRawVal(val) {
    if (val === null || val === undefined || val === '') return 0;
    return parseFloat(val.toString().replace(/,/g, '')) || 0;
}

function computeFixed(type, value, limit, gross) {
    let amt = type === 'percent' ? Math.round(gross * value * 100) / 100 : Math.round(value * 100) / 100;
    if (limit !== null && limit !== undefined && limit > 0) amt = Math.min(amt, limit);
    return amt;
}

let selectedMonth = {{ now()->month }};
let selectedMonthName = '{{ \Carbon\Carbon::create()->month(now()->month)->format("F") }}';
let selectedYear  = {{ now()->year }};
let sortCol = -1, sortAsc = true;
let _activeRow = null;
let _isDuplicate = false;

/* ── Draft State Management via LocalStorage ── */
let tableState = JSON.parse(localStorage.getItem('payroll_draft') || '{}');

function saveSessionState() {
    localStorage.setItem('payroll_step', '2');
    localStorage.setItem('payroll_month', document.getElementById('s1Month').value);
    localStorage.setItem('payroll_year', document.getElementById('s1Year').value);
    localStorage.setItem('payroll_label', document.getElementById('s1Label').value);
}

function clearSessionState() {
    localStorage.removeItem('payroll_step');
}

function saveRowState(row) {
    const userId = row.dataset.userId;
    if (!userId) return;
    if (!tableState[userId]) tableState[userId] = {};
    
    row.querySelectorAll('.loan-input:not(:disabled)').forEach(inp => {
        const field = inp.closest('[data-field]')?.dataset.field;
        if (field) {
            tableState[userId][field] = inp.value;
        }
    });
    // Saves aggressively to localStorage so it survives closed tabs
    localStorage.setItem('payroll_draft', JSON.stringify(tableState));
}

function restoreTableState() {
    // 1. Restore input values
    if (tableState && Object.keys(tableState).length > 0) {
        document.querySelectorAll('#empTbody tr[data-user-id]').forEach(row => {
            const userId = row.dataset.userId;
            if (tableState[userId]) {
                Object.keys(tableState[userId]).forEach(field => {
                    const inp = row.querySelector(`[data-field="${field}"] input.loan-input`);
                    if (inp && !inp.disabled) {
                        inp.value = tableState[userId][field];
                    }
                });
                const firstInput = row.querySelector('.loan-input:not(:disabled)');
                if (firstInput) recalcRow(firstInput, true); // true = skip saving while loading
            }
        });
    }

    // 2. Restore unchecked checkboxes (excluded employees)
    const excludedState = JSON.parse(localStorage.getItem('payroll_excluded_draft') || '[]');
    if (excludedState.length > 0) {
        excludedState.forEach(id => {
            const chk = document.querySelector(`.emp-chk[value="${id}"]`);
            if (chk) {
                chk.checked = false;
                updateRowStyle(chk);
            }
        });
    }
    
    // Update visual counts
    const ca = document.getElementById('chkAll');
    if (ca) onChkChange(true); 
}

/* ── Quick Add Column ── */
function openQuickAddModal() {
    document.getElementById('qaName').value = '';
    document.getElementById('qaType').value = 'deduction';
    document.getElementById('quickAddModal').classList.add('show');
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').classList.remove('show');
}

function submitQuickAdd() {
    const name = document.getElementById('qaName').value.trim();
    const kind = document.getElementById('qaType').value;
    if (!name) return showToast('Required', 'Please enter a name.', 'error');

    const btn = document.getElementById('qaBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const payload = {
        name: name,
        type: 'Not Fixed',
        rate_type: 'flat',
        rate_value: 0,
        is_deducted: kind === 'deduction',
        entry_kind: kind,
        is_active: 1
    };

    fetch('/payroll/deductions', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            saveSessionState();
            showToast('Success', 'Column created. Refreshing table...', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Error', 'Failed to create column.', 'error');
            btn.disabled = false;
            btn.textContent = 'Save & Refresh';
        }
    })
    .catch(() => {
        showToast('Error', 'Network error.', 'error');
        btn.disabled = false;
        btn.textContent = 'Save & Refresh';
    });
}

/* ── Manage Sub-Columns (Toggle & Zero Out) ── */
let hiddenCols = JSON.parse(localStorage.getItem('hiddenPayrollCols') || '[]');

function applyHiddenCols() {
    hiddenCols.forEach(colId => {
        const chk = document.getElementById('chk_hide_' + colId);
        if(chk) chk.checked = false;
        document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => el.style.display = 'none');
    });
    updateGroupColspans();
}

function updateGroupColspans() {
    document.querySelectorAll('.thead-group tr:first-child th[data-group]').forEach(grpTh => {
        const grp = grpTh.dataset.group;
        const allChildren = document.querySelectorAll(`.thead-group tr:nth-child(2) th[data-group="${grp}"]`);
        let visibleCount = 0;
        
        allChildren.forEach(child => {
            if(child.style.display !== 'none') visibleCount++;
        });

        if(visibleCount > 0) {
            grpTh.style.display = '';
            grpTh.colSpan = visibleCount;
        } else {
            grpTh.style.display = 'none';
        }
    });
}

function toggleCol(colId, isVisible) {
    document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => el.style.display = isVisible ? '' : 'none');
    
    if (!isVisible && !hiddenCols.includes(colId)) hiddenCols.push(colId);
    if (isVisible) hiddenCols = hiddenCols.filter(id => id !== colId);
    localStorage.setItem('hiddenPayrollCols', JSON.stringify(hiddenCols));
    
    updateGroupColspans();
}

function zeroOutField(field) {
    document.querySelectorAll(`[data-field="${field}"] input`).forEach(inp => {
        if(!inp.disabled) {
            inp.value = "0.00";
            recalcRow(inp);
        }
    });
    showToast('Success', 'Column cleared for all employees.', 'info');
}

function zeroOutDynamic(id) {
    document.querySelectorAll(`.dyn-ded-input[data-dyn-id="${id}"], .dyn-add-input[data-dyn-id="${id}"]`).forEach(inp => {
        inp.value = "0.00";
        recalcRow(inp);
    });
    showToast('Success', 'Column cleared for all employees.', 'info');
}

function toggleDynamicCol(id, isVisible) {
    toggleCol('dyn_' + id, isVisible);
    fetch(`/payroll/deductions/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
}

function openManageColumnsModal() {
    document.getElementById('manageColumnsModal').classList.add('show');
}

function closeManageColumnsModal() {
    document.getElementById('manageColumnsModal').classList.remove('show');
}

const HARD_DEDUCTION_FIELDS = [
    'gsis_ee', 'gsis_policy','gsis_emergency','gsis_real_estate',
    'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
    'pagibig_ee', 'pagibig_mpl','pagibig_calamity',
    'philhealth_ee',
    'withholding_tax',
    'loan_dbp','loan_lbp','loan_paracle','overpayment','other_deduction',
    'cng_capital_share', 'cng_kiddie_savings', 'cng_savings', 'cng_regular_loan',
    'cng_crisis_loan', 'cng_coop_canteen', 'cng_coop_store', 'cng_calamity_loan',
    'cng_abuloy', 'cng_handog', 'cng_b2b_loan', 'cng_petty_cash', 'cng_commodity_loan'
];

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
function goToStep2(isRestoring = false) {
    if (_isDuplicate) { showToast('Duplicate Period','A payroll for this month/year already exists.','error'); return; }
    const label = document.getElementById('s1Label').value.trim();
    if (!label) { showToast('Required','Please enter a period label.','error'); return; }
    
    // Only wipe local drafts if they explicitly select a completely different Month/Year from the drop down
    if (!isRestoring) {
        const newMonth = document.getElementById('s1Month').value;
        const newYear = document.getElementById('s1Year').value;
        const oldMonth = localStorage.getItem('payroll_month');
        const oldYear = localStorage.getItem('payroll_year');
        
        if (oldMonth && oldYear && (oldMonth !== newMonth || oldYear !== newYear)) {
            localStorage.removeItem('payroll_draft');
            localStorage.removeItem('payroll_excluded_draft');
            tableState = {};
            document.querySelectorAll('.emp-chk').forEach(c => c.checked = true);
        }
    }
    
    saveSessionState();
    
    selectedMonth     = parseInt(document.getElementById('s1Month').value);
    selectedMonthName = document.getElementById('s1Month').selectedOptions[0].dataset.name || document.getElementById('s1Month').selectedOptions[0].text;
    selectedYear      = parseInt(document.getElementById('s1Year').value);
    
    document.getElementById('monthPillLabel').textContent = selectedMonthName;
    document.getElementById('yearPillLabel').textContent  = selectedYear;
    syncPillSelections();
    
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'flex';
    document.getElementById('periodPills').style.display = 'flex';
    document.getElementById('bc3sep').style.display = '';
    document.getElementById('bc3').style.display    = '';
    document.getElementById('bc2').style.fontWeight = '400';
    
    // 🚨 Ensures data is re-populated automatically upon reaching Step 2
    restoreTableState(); 
    updateCounts();
}

function goBack() {
    clearSessionState();
    closeEmpPanel();
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'flex';
    document.getElementById('periodPills').style.display = 'none';
    document.getElementById('bc3sep').style.display = 'none';
    document.getElementById('bc3').style.display    = 'none';
}

function updateLabel() {
    const mSel  = document.getElementById('s1Month');
    const mName = mSel.selectedOptions[0].dataset.name || mSel.selectedOptions[0].text;
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
    saveSessionState();
}
function selectYear(y) {
    selectedYear = y;
    document.getElementById('yearPillLabel').textContent = y;
    document.getElementById('yearDrop').classList.remove('open');
    syncPillSelections();
    document.getElementById('s1Year').value = y;
    updateLabel(); checkDuplicate();
    saveSessionState();
}

/* ── Cell focus (UPDATED) ── */
function focusCell(input) {
    const cell = input.closest('.editable-cell') || input.closest('.ep-edit');
    if (cell && !cell.classList.contains('agri-locked')) cell.classList.add('focused');

    // Vanish the zero instantly, but leave commas intact if modifying an existing number
    if (getRawVal(input.value) === 0) {
        input.value = '';
    }
}

function blurCell(input)  { 
    const cell = input.closest('.editable-cell') || input.closest('.ep-edit');
    if (cell) cell.classList.remove('focused'); 

    // If left completely blank, force it back to 0.00 and recalculate
    if (input.value.trim() === '') {
        input.value = '0.00';
        recalcRow(input); 
    } else {
        // Lock in the 2 decimal places when clicking away
        let raw = getRawVal(input.value);
        input.value = raw.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

/* ── Live Formatter (NEW) ── */
function formatLive(input) {
    if (!input || input.tagName !== 'INPUT') return;
    
    let cursor = input.selectionStart;
    let oldLength = input.value.length;
    
    // 1. Strip everything except numbers and a decimal point (Blocks letters)
    let clean = input.value.replace(/[^0-9.]/g, '');
    
    // 2. Prevent typing multiple decimal points
    let parts = clean.split('.');
    if (parts.length > 2) {
        clean = parts[0] + '.' + parts.slice(1).join('');
        parts = clean.split('.');
    }
    
    // 3. Add commas dynamically while typing
    if (parts[0]) {
        if (parts[0].length > 1 && parts[0].startsWith('0')) {
            parts[0] = parseInt(parts[0], 10).toString(); // Prevent leading zeros like '05'
        }
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // 4. Limit to 2 decimal places maximum while typing
    if (parts[1] && parts[1].length > 2) {
        parts[1] = parts[1].substring(0, 2);
    }
    
    let formatted = parts.length > 1 ? parts[0] + '.' + parts[1] : parts[0];
    
    // Update input and preserve cursor position so it doesn't jump to the end of the box
    if (input.value !== formatted) {
        input.value = formatted;
        if (cursor !== null) {
            let newCursor = cursor + (formatted.length - oldLength);
            input.setSelectionRange(newCursor, newCursor);
        }
    }
}

/* ── RECALC (UPDATED) ── */
function recalcRow(input, skipSave = false) {
    // Run the live formatter first to block letters & add commas
    formatLive(input);

    const row = input.closest('tr');
    if (!row) return; // Safely exit if typing inside the slide-out panel
    const netCell = row.querySelector('.net-cell');
    if (!netCell) return;

    const gross     = parseFloat(netCell.dataset.gross)     || 0;
    
    const gsisEeCell = row.querySelector('[data-field="gsis_ee"] input');
    const gsisEe = gsisEeCell ? 0 : (parseFloat(netCell.dataset.gsisEe) || 0);
    
    const pagibigEeCell = row.querySelector('[data-field="pagibig_ee"] input');
    const pagibigEe = pagibigEeCell ? 0 : (parseFloat(netCell.dataset.pagibigEe) || 0);
    
    const phicEeCell = row.querySelector('[data-field="philhealth_ee"] input');
    const phicEe    = phicEeCell ? 0 : (parseFloat(netCell.dataset.phicEe) || 0);

    const peraCell = row.querySelector('[data-field="allowance_pera"] input');
    const pera = peraCell ? getRawVal(peraCell.value) : (parseFloat(netCell.dataset.pera) || 0);

    const raCell = row.querySelector('[data-field="allowance_rata"] input');
    const taCell = row.querySelector('[data-field="allowance_ta"] input');
    
    // Strip commas for internal calculation!
    const ra = raCell ? getRawVal(raCell.value) : 0;
    const ta = taCell ? getRawVal(taCell.value) : 0;

    let totalDeductions = 0;
    let dynAllowances   = 0;

    HARD_DEDUCTION_FIELDS.forEach(field => {
        const cell = row.querySelector(`[data-field="${field}"]`);
        if (cell) {
            totalDeductions += getRawVal(cell.querySelector('input')?.value);
        }
    });

    row.querySelectorAll('.dyn-ded-input').forEach(inp => {
        totalDeductions += getRawVal(inp.value);
    });

    row.querySelectorAll('.dyn-add-input').forEach(inp => {
        dynAllowances += getRawVal(inp.value);
    });

    const net = gross - gsisEe - pagibigEe - phicEe - totalDeductions + pera + ra + ta + dynAllowances;
    netCell.textContent = net.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});

    if (_activeRow === row) {
        document.getElementById('epNet').textContent = fmtPHP(net);
    }
    updateCounts();
    
    // Save draft every single time numbers recalculate
    if (!skipSave) {
        saveRowState(row);
    }
}

/* ── Reset row ── */
function resetRow(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) return;
    const isAgri = row.dataset.isAgri === '1';

    row.querySelectorAll('.loan-input').forEach(inp => {
        const field = inp.closest('[data-field]')?.dataset.field;
        if (field === 'allowance_rata' && isAgri) {
            inp.value = computeFixed(DED_CONFIG.rataType, DED_CONFIG.rataValue, null, parseFloat(row.dataset.salary)||0).toLocaleString('en-US',{minimumFractionDigits:2});
        } else if (field === 'allowance_ta' && isAgri) {
            inp.value = computeFixed(DED_CONFIG.taType, DED_CONFIG.taValue, null, parseFloat(row.dataset.salary)||0).toLocaleString('en-US',{minimumFractionDigits:2});
        } else if (!inp.disabled) {
            inp.value = inp.dataset.default !== undefined ? inp.dataset.default : '0.00';
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
function onChkChange(skipSave = false) {
    const all  = [...document.querySelectorAll('.emp-chk')];
    const vis  = all.filter(c => c.closest('tr').style.display !== 'none');
    const chkd = vis.filter(c => c.checked);
    const ca   = document.getElementById('chkAll');
    ca.checked       = chkd.length > 0 && chkd.length === vis.length;
    ca.indeterminate = chkd.length > 0 && chkd.length < vis.length;
    all.forEach(c => updateRowStyle(c));
    document.getElementById('btnDelete').disabled = chkd.length === 0;
    updateCounts();
    
    // Save draft of excluded checkboxes
    if (!skipSave) {
        const excluded = [];
        all.forEach(c => {
            if(!c.checked) excluded.push(c.value);
        });
        localStorage.setItem('payroll_excluded_draft', JSON.stringify(excluded));
    }
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
    document.querySelectorAll('#empTbody tr[data-user-id]').forEach(row => {
        const mSearch = !search || (row.dataset.name||'').includes(search);
        const mDept   = !dept   || row.dataset.dept === dept;
        const isIncl  = row.querySelector('.emp-chk')?.checked;
        const mStatus = !status || (status === 'included' && isIncl) || (status === 'excluded' && !isIncl);
        row.style.display = (mSearch && mDept && mStatus) ? '' : 'none';
    });
    onChkChange(true); // skip save just updating visual totals
}
function clearFilter() {
    document.getElementById('fDept').value = '';
    document.getElementById('fStatus').value = '';
    document.getElementById('empSearch').value = '';
    filterEmployees();
}

/* ── Include/Exclude ── */
function excludeRow(userId) { const chk = document.querySelector(`.emp-chk[value="${userId}"]`); if (chk) { chk.checked = false; updateRowStyle(chk); } closeDots(); onChkChange(); }
function includeRow(userId) { const chk = document.querySelector(`.emp-chk[value="${userId}"]`); if (chk) { chk.checked = true;  updateRowStyle(chk); } closeDots(); onChkChange(); }
function removeSelected()  { document.querySelectorAll('.emp-chk:checked').forEach(chk => { chk.checked = false; updateRowStyle(chk); }); onChkChange(); }

/* ── Dots ── */
function closeDots() { document.querySelectorAll('.dot-dropdown').forEach(d => d.classList.remove('open')); }
function toggleDot(btn) { const dd = btn.nextElementSibling; closeDots(); dd.classList.toggle('open'); }

/* ── Sort ── */
function sortEmp(col) {
    const tbody = document.getElementById('empTbody');
    const rows  = [...tbody.querySelectorAll('tr[data-user-id]')];
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

    const userId  = row.dataset.userId;
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
    document.getElementById('epSub').textContent  = `${userId}  ·  ${posName}${isAgri ? '  🌾' : ''}`;
    document.getElementById('epNet').textContent  = netCell
        ? fmtPHP(parseFloat(netCell.textContent.replace(/,/g,'')))
        : fmtPHP(0);

    const mkInfo = (label, val, color) =>
        `<div class="ep-field"><label>${label}</label><p${color ? ` style="color:${color}"` : ''}>${val}</p></div>`;
    const mkInfoSpan2 = (label, val) =>
        `<div class="ep-field span2"><label>${label}</label><p>${val}</p></div>`;
    const mkEdit = (label, field) => {
        const inp = row.querySelector(`[data-field="${field}"] input`);
        const val = inp ? (getRawVal(inp.value)).toLocaleString('en-US',{minimumFractionDigits:2, maximumFractionDigits:2}) : '0.00';
        return `<div class="ep-edit"><label>${label}</label>
            <input type="text" inputmode="decimal" value="${val}"
                data-panel-field="${field}" oninput="syncPanelField(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
        </div>`;
    };
    const mkEditAgri = (label, field) => {
        const inp = row.querySelector(`[data-field="${field}"] input`);
        const val = inp ? (getRawVal(inp.value)).toLocaleString('en-US',{minimumFractionDigits:2, maximumFractionDigits:2}) : '0.00';
        if (isAgri) {
            return `<div class="ep-edit"><label>${label} <span style="color:#16a34a;font-size:9px;">(PA Only)</span></label>
                <input type="text" inputmode="decimal" value="${val}"
                    data-panel-field="${field}" oninput="syncPanelField(this)" onfocus="focusCell(this)" onblur="blurCell(this)">
            </div>`;
        }
        return `<div class="ep-edit"><label>${label} <span style="color:#d1d5db;font-size:9px;">(N/A for this position)</span></label>
            <input type="text" inputmode="decimal" value="0.00" disabled
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

    /* Generate HTML specifically for Dynamic Deductions & Allowances */
    const dynDedHtml = [
        @foreach($dynamicDeductions as $d)
            mkEdit('{{ addslashes($d->name) }}', '{{ $d->id }}'),
        @endforeach
    ].join('');

    const dynAddHtml = [
        @foreach($dynamicAllowances as $a)
            mkEdit('{{ addslashes($a->name) }}', '{{ $a->id }}'),
        @endforeach
    ].join('');

    let extraDedCard = dynDedHtml ? mkCard('M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'Other Dynamic Deductions', dynDedHtml) : '';
    let extraAddCard = dynAddHtml ? mkCard('M12 6v6m0 0v6m0-6h6m-6 0H6', 'Other Dynamic Allowances', dynAddHtml) : '';

    document.getElementById('epBody').innerHTML =
        mkCard('M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'Employee Information',
            mkInfo('Gross Salary', fmtPHP(gross), '#111827') +
            mkInfo('Designation', desig) +
            mkInfoSpan2('Department', dept)
        ) +
        mkCard('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'GSIS',
            mkEdit('Personal Share (9%)', 'gsis_ee') +
            mkEdit('Govt Share (12%) *', 'gsis_govt') +
            mkEdit('ECF *', 'gsis_ec') +
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
            mkEdit('Personal Share', 'pagibig_ee') +
            mkEdit('Govt Share *', 'pagibig_govt') +
            mkEdit('MPL Loan',      'pagibig_mpl') +
            mkEdit('Calamity Loan', 'pagibig_calamity')
        ) +
        mkCard('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'PhilHealth',
            mkEdit('Personal Share', 'philhealth_ee') +
            mkEdit('Govt Share *', 'philhealth_govt')
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
        coopCard + extraDedCard +
        mkCard('M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Allowances',
            mkEdit('PERA', 'allowance_pera') +
            mkEditAgri('RA (Representation Allowance)', 'allowance_rata') +
            mkEditAgri('TA (Transportation Allowance)', 'allowance_ta')
        ) + extraAddCard +
        `<p style="font-size:10px;color:#9ca3af;text-align:center;padding:8px 16px 4px;">
            * Employer-paid — not deducted from employee net pay<br>
            🌾 RA &amp; TA apply to Provincial Agriculturist positions only
        </p>`;

    document.getElementById('empPanel').classList.add('open');
    document.getElementById('empOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
    closeDots();
}

/* ── Panel Syncing (UPDATED) ── */
function syncPanelField(input) {
    if (!_activeRow) return;
    const field    = input.dataset.panelField;
    const rowInput = _activeRow.querySelector(`[data-field="${field}"] input`);
    
    // Live format the slide-out panel input
    formatLive(input); 
    
    // Sync the formatted value back to the hidden table row and trigger math
    if (rowInput && !rowInput.disabled) { 
        rowInput.value = input.value; 
        recalcRow(rowInput); 
    }
}

function refreshPanelInputs(row) {
    document.querySelectorAll('#epBody [data-panel-field]').forEach(inp => {
        const rowInput = row.querySelector(`[data-field="${inp.dataset.panelField}"] input`);
        if (rowInput) inp.value = getRawVal(rowInput.value).toLocaleString('en-US',{minimumFractionDigits:2});
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

/* ── Confirm & Submit (UPDATED) ── */
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
        const userId = chk.value;
        const row   = chk.closest('tr');

        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'user_ids[]'; inp.value = userId; 
        hEmp.appendChild(inp);

        // Fetch inputs that are strictly editable loans/customs and STRIP COMMAS for the DB!
        row.querySelectorAll('[data-field] input.loan-input').forEach(fi => {
            const field = fi.closest('[data-field]').dataset.field;
            const oi = document.createElement('input');
            oi.type = 'hidden'; 
            oi.name = `overrides[${userId}][${field}]`; 
            oi.value = getRawVal(fi.value) || '0';
            hOvr.appendChild(oi);
        });

        // Add fixed/employer fields calculated purely on frontend
        const netCell = row.querySelector('.net-cell');
        if (netCell) {
            const fixedFields = {
                'gsis_ee': netCell.dataset.gsisEe,
                'gsis_govt': netCell.dataset.gsisGovt,
                'gsis_ec': netCell.dataset.gsisEc,
                'pagibig_ee': netCell.dataset.pagibigEe,
                'pagibig_govt': netCell.dataset.pagibigGov,
                'philhealth_ee': netCell.dataset.phicEe,
                'philhealth_govt': netCell.dataset.phicGovt,
                'allowance_pera': netCell.dataset.pera,
            };
            for (const key in fixedFields) {
                // Check it doesn't duplicate a field if you later decide to make it editable (like pagibig_ee)
                if (fixedFields[key] !== undefined && !row.querySelector(`[data-field="${key}"] input.loan-input`)) {
                    const oi = document.createElement('input');
                    oi.type = 'hidden'; oi.name = `overrides[${userId}][${key}]`; oi.value = fixedFields[key];
                    hOvr.appendChild(oi);
                }
            }
        }
    });

    // Wipe drafts upon successful submission
    localStorage.removeItem('payroll_draft');
    localStorage.removeItem('payroll_excluded_draft');
    localStorage.removeItem('payroll_step');

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
    
    applyHiddenCols();

    // Now checks ultra-secure localStorage instead of volatile sessionStorage
    if (localStorage.getItem('payroll_step') === '2') {
        const sm = localStorage.getItem('payroll_month');
        const sy = localStorage.getItem('payroll_year');
        const sl = localStorage.getItem('payroll_label');
        
        if(sm) document.getElementById('s1Month').value = sm;
        if(sy) document.getElementById('s1Year').value = sy;
        if(sl) document.getElementById('s1Label').value = sl;
        
        updateLabel();
        checkDuplicate();
        
        if (!_isDuplicate) {
            goToStep2(true); // Automatically goes to Step 2 and restores the table
        } else {
            clearSessionState();
        }
    } else {
        clearSessionState(); // Wipe state on fresh visit
        updateCounts();
        checkDuplicate();
    }
    
    document.querySelectorAll('.emp-chk').forEach(c => c.addEventListener('change', function () { updateRowStyle(this); onChkChange(); }));
});

document.addEventListener('click', e => {
    if (!e.target.closest('.period-pill') && !e.target.closest('.pill-dropdown'))
        document.querySelectorAll('.pill-dropdown').forEach(d => d.classList.remove('open'));
    if (!e.target.closest('.dot-menu')) closeDots();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeEmpPanel(); closeCModal(); closeQuickAddModal(); closeManageColumnsModal(); }
});
</script>
@endsection