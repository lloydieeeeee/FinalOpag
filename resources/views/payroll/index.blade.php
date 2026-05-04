@extends('layouts.app')
@section('title', 'Payroll — Admin')
@section('page-title', 'Payroll')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

:root {
  --forest:   #2d5a2d;
  --forest-2: #3a6e3a;
  --forest-3: #3d7a2a;
  --leaf:     #5aaa5a;
  --mist:     #f4f6f3;
  --bone:     #fafaf8;
  --ink:      #141a14;
  --slate:    #4a5568;
  --dust:     #9aa3a0;
  --line:     #e8ede8;
  --red:      #c0392b;
  --red-l:    #fce8e6;
  --green:    #1a6b1a;
  --green-l:  #e6f4e6;
  --amber:    #b45309;
  --amber-l:  #fef3c7;
  --blue:     #1e3a6e;
  --mono:     'JetBrains Mono', monospace;
}

html, body { height: 100%; }

.content-wrapper,
[data-page-content],
#page-content,
.page-content,
main {
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  min-height: 0;
  height: 100%;
}

.pay-page {
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  height: 100%;
  min-height: 0;
  overflow: hidden;
  gap: 0;
  padding: 0;
}

/* ══════════════════════════════
  STATS STRIP
══════════════════════════════ */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  margin-bottom: 14px;
  flex-shrink: 0;
}

.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 14px 18px;
  box-shadow: 0 1px 4px rgba(0,0,0,.06);
  border: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  transition: box-shadow .18s, transform .18s;
}
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.13); transform: translateY(-1px); }
.stat-card:active { transform: translateY(0); box-shadow: 0 1px 4px rgba(0,0,0,.06); }

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.stat-val { font-size: 22px; font-weight: 800; color: #111827; line-height: 1; }
.stat-lbl { font-size: 10px; color: #6b7280; font-weight: 700; margin-top: 2px; text-transform: uppercase; letter-spacing: .05em; }

/* ══════════════════════════════
  BANNERS
══════════════════════════════ */
.success-banner {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--green-l);
  border: 1px solid #a7d8a7;
  border-radius: 14px;
  font-size: 13px;
  color: var(--green);
  margin-bottom: 14px;
  font-weight: 500;
}

.error-banner {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--red-l);
  border: 1px solid #f5c6c6;
  border-radius: 14px;
  font-size: 13px;
  color: var(--red);
  margin-bottom: 14px;
  font-weight: 500;
}

/* ══════════════════════════════
  MAIN CARD
══════════════════════════════ */
.app-card {
  flex: 1 1 0;
  min-height: 0;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
  overflow: hidden;
}

/* ══════════════════════════════
  TOP BAR
══════════════════════════════ */
.card-topbar {
  flex-shrink: 0;
  background: linear-gradient(135deg, #3a6e3a 0%, #4a8c38 100%);
  padding: 14px 20px;
  border-bottom: none;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.topbar-left p:first-child {
  font-weight: 700;
  color: #fff;
  font-size: 15px;
  margin: 0 0 2px;
}
.topbar-left p:last-child {
  font-size: 11px;
  color: rgba(255,255,255,.6);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.period-select {
  appearance: none;
  -webkit-appearance: none;
  padding: 7px 28px 7px 10px;
  font-size: 12px;
  font-weight: 600;
  border: 1.5px solid rgba(255,255,255,.25);
  border-radius: 8px;
  background: rgba(255,255,255,.12) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' stroke='%23ffffff' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 9px center;
  color: #fff;
  outline: none;
  cursor: pointer;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: border-color .15s, background .15s;
}
.period-select:focus { border-color: rgba(255,255,255,.6); background-color: rgba(255,255,255,.2); }
.period-select option { background: #2d5a2d; color: #fff; }

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  color: #2d5a2d;
  background: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  transition: background .15s, color .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-primary:hover { background: #dcfce7; color: #2d5a2d; }

.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.85);
  background: rgba(255,255,255,.1);
  border: 1.5px solid rgba(255,255,255,.2);
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-outline:hover { border-color: rgba(255,255,255,.5); background: rgba(255,255,255,.2); color: #fff; }

.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  background: var(--red);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-danger:hover { background: #a93226; }

.btn-divider { width: 1px; height: 22px; background: rgba(255,255,255,.2); flex-shrink: 0; }

/* ══════════════════════════════
  BADGES
══════════════════════════════ */
.badge-DRAFT {
  background: var(--amber-l);
  color: var(--amber);
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .04em;
}
.badge-FINALIZED {
  background: var(--green-l);
  color: var(--green);
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .04em;
}

/* ══════════════════════════════
  PANEL TOOLBAR
══════════════════════════════ */
.panel-toolbar {
  flex-shrink: 0;
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%);
}

.filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-row .rel { position: relative; }
.filter-row .rel.rel-search { flex: 1 1 200px; min-width: 140px; }
.filter-row .rel:has(select) { flex: 0 0 auto; }
.filter-row input,
.filter-row select {
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
  padding: 7px 10px;
  font-size: 12px;
  border: 1.5px solid #e9ecef;
  border-radius: 8px;
  background: #fff;
  color: #374151;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.filter-row select { padding-right: 26px; }
.filter-row input  { padding-left: 30px; }
.filter-row input:focus, .filter-row select:focus { border-color: var(--forest); box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.filter-row .chevron     { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #9ca3af; }
.filter-row .search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }

/* ══════════════════════════════
  SPREADSHEET TABLE
══════════════════════════════ */
.sheet-wrap {
  flex: 1 1 0;
  min-height: 0;
  overflow: auto;
  scrollbar-width: thin;
  scrollbar-color: #d1d5db transparent;
  position: relative;
}
.sheet-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
.sheet-wrap::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.sheet-wrap::-webkit-scrollbar-track { background: transparent; }

.sheet-table {
  border-collapse: collapse;
  font-size: 11px;
  font-family: var(--mono);
  min-width: max-content;
  width: 100%;
}

/* ══════════════════════════════
  FROZEN COLUMNS
══════════════════════════════ */
.col-frozen-0 {
  position: sticky;
  left: 0;
  z-index: 8;
}
.col-frozen-1 {
  position: sticky;
  left: 40px;
  z-index: 8;
}
.col-frozen-2 {
  position: sticky;
  left: 246px;
  z-index: 8;
}
.col-frozen-net {
  position: sticky;
  left: 342px;
  z-index: 8;
  box-shadow: 4px 0 10px -2px rgba(0,0,0,.13);
}

thead tr:first-child  .col-frozen-0,
thead tr:first-child  .col-frozen-1,
thead tr:first-child  .col-frozen-2,
thead tr:first-child  .col-frozen-net,
thead tr:nth-child(2) .col-frozen-0,
thead tr:nth-child(2) .col-frozen-1,
thead tr:nth-child(2) .col-frozen-2,
thead tr:nth-child(2) .col-frozen-net { z-index: 30 !important; }

tfoot .col-frozen-0,
tfoot .col-frozen-1,
tfoot .col-frozen-2,
tfoot .col-frozen-net { z-index: 20; }

.sheet-table thead tr:first-child th {
  position: sticky;
  top: 0;
  z-index: 10;
  font-size: 8.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .06em;
  padding: 6px 8px;
  height: 28px;
  border-right: 1px solid rgba(0,0,0,.06);
  text-align: center;
  white-space: nowrap;
  user-select: none;
}

.sheet-table thead tr:nth-child(2) th {
  position: sticky;
  top: 28px;
  z-index: 10;
  padding: 5px 8px;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
  user-select: none;
  border-right: 1px solid #e9ecef;
  text-align: right;
}

/* ══════════════════════════════
  GROUP HEADER COLOURS
══════════════════════════════ */
.grp-employee { background: #deeede !important; color: #1e4d1e !important; }
.grp-gsis     { background: #dce8fa !important; color: #1a3585 !important; }
.grp-pagibig  { background: #ece8fb !important; color: #4530a0 !important; }
.grp-ph       { background: #d4f2fa !important; color: #0b6a88 !important; }
.grp-loans    { background: #fdefd8 !important; color: #8c4d08 !important; }
.grp-allow    { background: #d4f7ea !important; color: #0c5a3c !important; }
.grp-net      { background: #fde4e4 !important; color: #991a1a !important; }
.grp-action   { background: #eef0f2 !important; color: #374151 !important; }

/* ══════════════════════════════
  SUB-HEADER COLOURS
══════════════════════════════ */
.sub-employee { background: #f4fbf4 !important; color: #1a5c1a !important; }
.sub-gsis     { background: #eff6ff !important; color: #1e3fb0 !important; }
.sub-pagibig  { background: #f4f2ff !important; color: #4a30a8 !important; }
.sub-ph       { background: #eafbff !important; color: #0a6c8a !important; }
.sub-loans    { background: #fdf8ee !important; color: #854a08 !important; }
.sub-allow    { background: #ecfdf6 !important; color: #0b5e40 !important; }
.sub-net      { background: #fff2f2 !important; color: #b01c1c !important; font-weight: 800 !important; }
.sub-action   { background: #f9fafb !important; color: #374151 !important; }

.sheet-table tbody tr { transition: background .08s; cursor: pointer; }
.sheet-table tbody tr:nth-child(even) td { background: #fafaf8; }
.sheet-table tbody tr:nth-child(odd)  td { background: #fff; }
.sheet-table tbody tr:hover td           { background: #f0fdf4 !important; }
.sheet-table tbody tr.row-dirty td       { background: #fffbeb !important; }
.sheet-table tbody tr.row-saving td      { opacity: .55; }
.sheet-table tbody tr.row-saved td       { background: #e6f4e6 !important; }

.sheet-table td {
  padding: 0;
  border-right: 1px solid #f0f2f0;
  border-bottom: 1px solid #f0f2f0;
  vertical-align: middle;
  white-space: nowrap;
}
.sheet-table td:last-child { border-right: none; }

/* ══════════════════════════════
  FROZEN BODY CELL BACKGROUNDS
══════════════════════════════ */
tbody tr:nth-child(even) .col-frozen-0,
tbody tr:nth-child(even) .col-frozen-1,
tbody tr:nth-child(even) .col-frozen-2,
tbody tr:nth-child(even) .col-frozen-net { background: #fafaf8 !important; }

tbody tr:nth-child(odd) .col-frozen-0,
tbody tr:nth-child(odd) .col-frozen-1,
tbody tr:nth-child(odd) .col-frozen-2,
tbody tr:nth-child(odd) .col-frozen-net  { background: #ffffff !important; }

tbody tr:hover .col-frozen-0,
tbody tr:hover .col-frozen-1,
tbody tr:hover .col-frozen-2,
tbody tr:hover .col-frozen-net           { background: #f0fdf4 !important; }

tbody tr.row-dirty .col-frozen-0,
tbody tr.row-dirty .col-frozen-1,
tbody tr.row-dirty .col-frozen-2,
tbody tr.row-dirty .col-frozen-net       { background: #fffbeb !important; }

tbody tr.row-saved .col-frozen-0,
tbody tr.row-saved .col-frozen-1,
tbody tr.row-saved .col-frozen-2,
tbody tr.row-saved .col-frozen-net       { background: #e6f4e6 !important; }

.cell-static { padding: 5px 8px; color: var(--slate); font-size: 11px; font-family: 'Plus Jakarta Sans', sans-serif; }
.cell-num { padding: 5px 8px; text-align: right; font-size: 10.5px; color: var(--slate); font-family: var(--mono); }
.cell-num.neg { color: var(--red); }
.cell-num.pos { color: var(--green); font-weight: 700; }

.net-pay-cell {
  padding: 6px 10px;
  text-align: right;
  font-size: 12px;
  font-family: var(--mono);
  font-weight: 800;
  color: #dc2626;
  min-width: 110px;
  display: block;
}
.net-pay-cell.positive { color: #16a34a; }

.cell-input-wrap {
  position: relative;
  cursor: text;
  min-width: 76px;
  text-align: right;
  transition: background .15s;
}
.cell-input-wrap:hover { background: #f0fdf4; }
.cell-input-wrap.focused { background: #fff !important; box-shadow: inset 0 0 0 2px #3d7a2a; border-radius: 4px; }

.cell-input {
  display: block;
  width: 100%;
  padding: 5px 8px;
  font-size: 10.5px;
  font-family: var(--mono);
  font-weight: 500;
  text-align: right;
  border: none;
  background: transparent;
  color: #374151;
  outline: none;
  cursor: text;
  min-width: 76px;
  transition: color .12s;
}
.cell-input:focus { color: #111827; }
.cell-input.changed { color: var(--forest); font-weight: 700; }
.cell-input[readonly] { color: #b8c0cc; cursor: not-allowed; }

.col-num   { width: 40px;  min-width: 40px; text-align: center; }
.col-name  { width: 206px; min-width: 206px; }
.col-desig { width: 96px;  min-width: 96px; }
.col-net   { width: 110px; min-width: 110px; }

/* ══════════════════════════════
  TOTALS FOOTER
══════════════════════════════ */
.sheet-table tfoot td {
  background: #deeede !important;
  color: #1a4a1a;
  font-weight: 700;
  font-size: 10.5px;
  font-family: var(--mono);
  padding: 7px 8px;
  white-space: nowrap;
  border-right: 1px solid #c4d8c4;
  border-top: 2px solid #b8d0b8;
  position: sticky;
  bottom: 0;
  z-index: 8;
}

tfoot .col-frozen-0,
tfoot .col-frozen-1,
tfoot .col-frozen-2,
tfoot .col-frozen-net {
  z-index: 25;
  background: #deeede !important;
}

tfoot .net-footer {
  color: #991a1a !important;
  font-weight: 800 !important;
  font-size: 11px !important;
  text-align: right;
  padding: 7px 10px;
  background: #fde4e4 !important;
  border-right: 2px solid #f5c0c0 !important;
}

/* ══════════════════════════════
  SAVE BAR
══════════════════════════════ */
.save-bar {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
  background: #f0fdf4;
  border-top: 1px solid #b7ddb7;
  gap: 12px;
  transition: opacity .25s, transform .25s;
}
.save-bar.hidden { opacity: 0; pointer-events: none; transform: translateY(4px); }
.save-bar-info {
  font-size: 12px;
  color: var(--green);
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}
.save-bar-info::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--leaf);
  animation: pulse 1.4s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: .5; transform: scale(.85); }
}

.btn-save-all {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 20px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #2d5a2d, #3d7a2a);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all .2s;
  font-family: 'Plus Jakarta Sans', sans-serif;
  box-shadow: 0 3px 10px rgba(45,90,45,.28);
}
.btn-save-all:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(45,90,45,.38); }

.btn-discard-all {
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-discard-all:hover { border-color: #9ca3af; color: #374151; }

/* ══════════════════════════════
  TOAST
══════════════════════════════ */
#toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
  min-width: 240px;
  background: #fff;
  border-radius: 15px;
  padding: 13px 17px;
  box-shadow: 0 10px 36px rgba(0,0,0,.16);
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  transform: translateY(14px);
  transition: all .32s cubic-bezier(.34,1.56,.64,1);
  pointer-events: none;
  border: 1px solid #f3f4f6;
}
#toast.show { opacity: 1; transform: translateY(0); }

/* ══════════════════════════════
  RESPONSIVE
══════════════════════════════ */
@media (max-width: 767px) {
  .pay-page { height: auto; overflow: visible; padding-bottom: 60px; flex: none; }
  .app-card { overflow: visible; flex: none; height: auto; }
  .sheet-wrap { overflow-x: auto; height: auto; max-height: none; flex: none; }
  .stats-grid {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    gap: 8px !important;
    padding-bottom: 4px;
    scrollbar-width: none;
  }
  .stats-grid::-webkit-scrollbar { display: none; }
  .stat-card {
    flex: 0 0 100px !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    padding: 10px 12px !important;
  }
  .stat-val { font-size: 18px !important; }
  .card-topbar { flex-direction: column; align-items: flex-start; }
  .filter-row { flex-wrap: nowrap !important; }
  .filter-row .rel.rel-search { flex: 1 1 0% !important; min-width: 0 !important; }
  .filter-row select { min-width: 90px !important; max-width: 110px !important; font-size: 11px !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
  .stats-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>

@php
$dbDeductions = \App\Models\PayrollDeduction::where('is_active', 1)->get()->keyBy('name');

$jsConfig = [
    'gsisEeType'      => $dbDeductions->get('GSIS Employee Share')?->rate_type    ?? 'percent',
    'gsisEeValue'     => (float)($dbDeductions->get('GSIS Employee Share')?->rate_value   ?? 0.09),
    'gsisEeLimit'     => $dbDeductions->get('GSIS Employee Share')?->limit_amount !== null
                            ? (float)$dbDeductions->get('GSIS Employee Share')->limit_amount : null,
    'gsisGovtType'    => $dbDeductions->get("GSIS Gov't Share")?->rate_type  ?? 'percent',
    'gsisGovtValue'   => (float)($dbDeductions->get("GSIS Gov't Share")?->rate_value ?? 0.12),
    'gsisGovtLimit'   => $dbDeductions->get("GSIS Gov't Share")?->limit_amount !== null
                            ? (float)$dbDeductions->get("GSIS Gov't Share")->limit_amount : null,
    'gsisEcType'      => $dbDeductions->get('ECF (Employee Compensation Fund)')?->rate_type  ?? 'flat',
    'gsisEcValue'     => (float)($dbDeductions->get('ECF (Employee Compensation Fund)')?->rate_value ?? 100),
    'gsisEcLimit'     => null,
    'pagibigEeType'   => $dbDeductions->get("PAGIBIG Gov't Share")?->rate_type  ?? 'flat',
    'pagibigEeValue'  => (float)($dbDeductions->get("PAGIBIG Gov't Share")?->rate_value ?? 200),
    'pagibigEeLimit'  => null,
    'phicEeType'      => $dbDeductions->get('PhilHealth Employee Share')?->rate_type  ?? 'percent',
    'phicEeValue'     => (float)($dbDeductions->get('PhilHealth Employee Share')?->rate_value ?? 0.025),
    'phicEeLimit'     => $dbDeductions->get('PhilHealth Employee Share')?->limit_amount !== null
                            ? (float)$dbDeductions->get('PhilHealth Employee Share')->limit_amount : null,
    'phicGovtType'    => $dbDeductions->get("PhilHealth Gov't Share")?->rate_type  ?? 'percent',
    'phicGovtValue'   => (float)($dbDeductions->get("PhilHealth Gov't Share")?->rate_value ?? 0.025),
    'phicGovtLimit'   => $dbDeductions->get("PhilHealth Gov't Share")?->limit_amount !== null
                            ? (float)$dbDeductions->get("PhilHealth Gov't Share")->limit_amount : null,
    'peraType'        => $dbDeductions->get('PERA')?->rate_type   ?? 'flat',
    'peraValue'       => (float)($dbDeductions->get('PERA')?->rate_value ?? 2000),
    'peraLimit'       => null,
];
@endphp

<div class="pay-page">

  {{-- Stats --}}
  <div class="stats-grid" style="flex-shrink:0;">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e6f4e6;">
        <svg style="width:18px;height:18px;color:#1a6b1a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val">{{ $summary->employees }}</div>
        <div class="stat-lbl">Employees</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#dbeafe;">
        <svg style="width:18px;height:18px;color:#1e3a8a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->gross, 0) }}</div>
        <div class="stat-lbl">Gross Salary</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fce8e6;">
        <svg style="width:18px;height:18px;color:#c0392b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->deductions, 0) }}</div>
        <div class="stat-lbl">Total Deductions</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#dcfce7;">
        <svg style="width:18px;height:18px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->net, 0) }}</div>
        <div class="stat-lbl">Net Pay</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#ede9fe;">
        <svg style="width:18px;height:18px;color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->wtax, 0) }}</div>
        <div class="stat-lbl">W/Tax</div>
      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="success-banner">
    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
  </div>
  @endif

  @if(session('error'))
  <div class="error-banner">
    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    {{ session('error') }}
  </div>
  @endif

  {{-- Main card --}}
  <div class="app-card">

    {{-- Top bar --}}
    <div class="card-topbar">
      <div class="topbar-left">
        <p>Payroll Records</p>
        <p>
          @if($selectedPeriod)
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ $selectedPeriod->period_label }}
            <span style="color:rgba(255,255,255,.3);">·</span>
            <span class="badge-{{ $selectedPeriod->status }}">{{ $selectedPeriod->status }}</span>
          @else
            No period selected
          @endif
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <form method="GET" action="{{ route('payroll.index') }}" style="display:flex;align-items:center;">
          <select name="period_id" class="period-select" onchange="this.form.submit()">
            @foreach($periods as $p)
              <option value="{{ $p->period_id }}" {{ $p->period_id == optional($selectedPeriod)->period_id ? 'selected' : '' }}>
                {{ $p->period_label }}
              </option>
            @endforeach
          </select>
        </form>

        <div class="btn-divider"></div>

        {{-- ★ FINALIZE: now opens the polished modal instead of a plain confirm() ★ --}}
        @if($selectedPeriod && $selectedPeriod->status === 'DRAFT')
          <button type="button" class="btn-danger" onclick="openFinalizeModal()">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Finalize
          </button>
        @endif

        @if($selectedPeriod)
          <a href="{{ route('payroll.pdf', $selectedPeriod->period_id) }}" class="btn-outline" target="_blank">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Print Payroll
          </a>
          <a href="{{ route('payroll.payslip-all-pdf', $selectedPeriod->period_id) }}" class="btn-outline" target="_blank">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print All Payslips
          </a>
          <a href="{{ route('payroll.remittances', ['period_id' => $selectedPeriod->period_id]) }}" class="btn-outline">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Remittances
          </a>
        @endif

        <div class="btn-divider"></div>

        <a href="{{ route('payroll.create') }}" class="btn-primary">
          <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          New Period
        </a>
      </div>
    </div>

    {{-- Toolbar / filter row --}}
    <div class="panel-toolbar">
      <div class="filter-row">
        <div class="rel rel-search">
          <svg class="search-icon" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
          <input type="text" placeholder="Search employee name / ID…" id="payrollSearch" oninput="filterPayroll(this.value)">
        </div>
        <div class="rel">
          <select id="filterDept" onchange="filterPayroll(document.getElementById('payrollSearch').value)">
            <option value="">All Departments</option>
            @foreach($records->pluck('employee.department.department_name')->filter()->unique()->sort() as $dept)
              <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
          </select>
          <svg class="chevron" style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>
    </div>

    {{-- Spreadsheet table --}}
    <div class="sheet-wrap" id="sheetWrap">
      <table class="sheet-table" id="payrollSheet">
        <thead>
          {{-- ══ GROUP HEADER ROW ══ --}}
          <tr>
            <th class="col-frozen-0 col-num grp-employee" rowspan="2"></th>
            <th class="col-frozen-1 col-name grp-employee" rowspan="2" style="text-align:left;padding-left:14px;">Employee</th>
            <th class="col-frozen-2 col-desig grp-employee" rowspan="2" style="text-align:left;padding-left:10px;">Position</th>
            <th class="col-frozen-net col-net grp-net" rowspan="2" style="text-align:right;padding-right:10px;border-right:2px solid rgba(220,38,38,.25);">Net Pay</th>
            <th class="grp-employee" rowspan="2" style="text-align:right;min-width:110px;padding:0 8px;">Gross Salary</th>
            <th class="grp-gsis"    colspan="11" style="text-align:center;">G &nbsp; S &nbsp; I &nbsp; S</th>
            <th class="grp-pagibig" colspan="4"  style="text-align:center;">PAG-IBIG</th>
            <th class="grp-ph"      colspan="2"  style="text-align:center;">PHILHEALTH</th>
            <th class="grp-loans"   colspan="7"  style="text-align:center;">LOANS &amp; DEDUCTIONS</th>
            <th class="grp-allow"   colspan="3"  style="text-align:center;">ALLOWANCES</th>
            <th class="grp-net"     colspan="1"  style="text-align:center;">TOTAL DED.</th>
            <th class="grp-action" rowspan="2" style="text-align:center;min-width:40px;">St.</th>
          </tr>
          {{-- ══ SUB-HEADER ROW ══ --}}
          <tr>
            <th class="sub-gsis"    style="min-width:82px;">EE (9%)</th>
            <th class="sub-gsis"    style="min-width:82px;">Gov't (12%)</th>
            <th class="sub-gsis"    style="min-width:68px;">EC</th>
            <th class="sub-gsis"    style="min-width:82px;">Policy Loan</th>
            <th class="sub-gsis"    style="min-width:82px;">Emergency</th>
            <th class="sub-gsis"    style="min-width:82px;">Real Estate</th>
            <th class="sub-gsis"    style="min-width:68px;">MPL</th>
            <th class="sub-gsis"    style="min-width:72px;">MPL Lite</th>
            <th class="sub-gsis"    style="min-width:68px;">GFAL</th>
            <th class="sub-gsis"    style="min-width:74px;">Computer</th>
            <th class="sub-gsis"    style="min-width:68px;">Conso</th>
            <th class="sub-pagibig" style="min-width:68px;">EE</th>
            <th class="sub-pagibig" style="min-width:68px;">Gov't</th>
            <th class="sub-pagibig" style="min-width:68px;">MPL</th>
            <th class="sub-pagibig" style="min-width:82px;">Calamity</th>
            <th class="sub-ph"      style="min-width:68px;">EE</th>
            <th class="sub-ph"      style="min-width:68px;">Gov't</th>
            <th class="sub-loans"   style="min-width:68px;">W/Tax</th>
            <th class="sub-loans"   style="min-width:68px;">DBP</th>
            <th class="sub-loans"   style="min-width:68px;">LBP</th>
            <th class="sub-loans"   style="min-width:82px;">CNGWMPC</th>
            <th class="sub-loans"   style="min-width:74px;">PARACLE</th>
            <th class="sub-loans"   style="min-width:82px;">Overpayment</th>
            <th class="sub-loans"   style="min-width:160px;background:#78350f;color:#fef3c7;">Other Deduction</th>
            <th class="sub-allow"   style="min-width:68px;">PERA</th>
            <th class="sub-allow"   style="min-width:68px;">RA</th>
            <th class="sub-allow"   style="min-width:72px;">TA/Other</th>
            <th class="sub-net"     style="min-width:96px;">Total Ded.</th>
          </tr>
        </thead>

        <tbody id="payrollTbody">
          @forelse($records as $i => $r)
          <?php
            $pid    = $r->payroll_id;
            $locked = optional($selectedPeriod)->status === 'FINALIZED';
            $ro     = $locked ? 'readonly' : '';
            $onch   = $locked ? '' : "markDirty({$pid}, this)";
            $dept   = strtolower($r->employee->department->department_name ?? '');
            $name   = strtolower(($r->employee->last_name ?? '') . ' ' . ($r->employee->first_name ?? ''));
            $empid  = strtolower($r->employee->formatted_employee_id ?? $r->employee_id ?? '');
          ?>
          <tr id="row_{{ $pid }}"
              data-id="{{ $pid }}"
              data-orig="{{ $r->toJson() }}"
              data-search="{{ $name }} {{ $empid }}"
              data-dept="{{ $dept }}"
              data-gross="{{ $r->gross_salary }}">

            <td class="col-frozen-0 col-num">
              <div class="cell-static" style="text-align:center;color:#9ca3af;font-size:10px;padding:5px 4px;">{{ $i+1 }}</div>
            </td>

            <td class="col-frozen-1 col-name" style="border-right:1px solid #f0f2f0;">
              <div style="padding:5px 12px;">
                <div style="font-weight:700;color:#111827;font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;line-height:1.3;">
                  {{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}
                </div>
                <div style="font-size:10px;color:#9ca3af;font-family:'JetBrains Mono',monospace;margin-top:1px;">
                  {{ $r->employee->department->department_name ?? '' }}
                </div>
              </div>
            </td>

            <td class="col-frozen-2 col-desig">
              <div class="cell-static" style="font-size:11px;color:#374151;font-weight:600;">
                {{ $r->designation ?? optional($r->employee->position)->position_code }}
              </div>
            </td>

            <td class="col-frozen-net col-net" style="border-right:2px solid rgba(220,38,38,.18);">
              <span class="net-pay-cell {{ $r->net_pay > 0 ? 'positive' : '' }}" id="net_{{ $pid }}">
                {{ number_format($r->net_pay, 2) }}
              </span>
            </td>

            <td>
              <div class="cell-num" style="font-weight:700;color:#111827;min-width:110px;">{{ number_format($r->gross_salary, 2) }}</div>
            </td>

            {{-- GSIS (11 cols) --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_ee, 2, '.', '') }}"
                  data-field="gsis_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->gsis_govt ?? 0, 2, '.', '') }}">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_ec ?? 0, 2, '.', '') }}"
                  data-field="gsis_ec" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_policy ?? 0, 2, '.', '') }}"
                  data-field="gsis_policy" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_emergency ?? 0, 2, '.', '') }}"
                  data-field="gsis_emergency" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_real_estate ?? 0, 2, '.', '') }}"
                  data-field="gsis_real_estate" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_mpl ?? 0, 2, '.', '') }}"
                  data-field="gsis_mpl" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_mpl_lite ?? 0, 2, '.', '') }}"
                  data-field="gsis_mpl_lite" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_gfal ?? 0, 2, '.', '') }}"
                  data-field="gsis_gfal" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_computer ?? 0, 2, '.', '') }}"
                  data-field="gsis_computer" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_conso ?? 0, 2, '.', '') }}"
                  data-field="gsis_conso" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- PAG-IBIG (4 cols) --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_ee ?? 0, 2, '.', '') }}"
                  data-field="pagibig_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->pagibig_govt ?? 0, 2, '.', '') }}">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_mpl ?? 0, 2, '.', '') }}"
                  data-field="pagibig_mpl" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_calamity ?? 0, 2, '.', '') }}"
                  data-field="pagibig_calamity" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- PHILHEALTH (2 cols) --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->philhealth_ee ?? 0, 2, '.', '') }}"
                  data-field="philhealth_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->philhealth_govt ?? 0, 2, '.', '') }}">
              </div>
            </td>

            {{-- LOANS & DEDUCTIONS (6 cols) --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->withholding_tax ?? 0, 2, '.', '') }}"
                  data-field="withholding_tax" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_dbp ?? 0, 2, '.', '') }}"
                  data-field="loan_dbp" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_lbp ?? 0, 2, '.', '') }}"
                  data-field="loan_lbp" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_cngwmpc ?? 0, 2, '.', '') }}"
                  data-field="loan_cngwmpc" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_paracle ?? 0, 2, '.', '') }}"
                  data-field="loan_paracle" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->overpayment ?? 0, 2, '.', '') }}"
                  data-field="overpayment" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- OTHER DEDUCTION --}}
            <td style="background:{{ ($r->other_deduction ?? 0) > 0 ? '#fffbeb' : '' }};">
              <div style="display:flex;gap:3px;align-items:center;padding:2px 3px;min-width:155px;">
                <input
                  type="text"
                  {{ $ro }}
                  value="{{ $r->other_deduction_label ?? '' }}"
                  data-field="other_deduction_label"
                  placeholder="Label…"
                  oninput="{{ $locked ? '' : "markDirty({$pid}, this)" }}"
                  style="
                    flex:1;min-width:60px;max-width:80px;
                    font-size:10px;font-family:'Plus Jakarta Sans',sans-serif;
                    color:#92400e;font-weight:600;
                    border:1.5px solid #fde68a;border-radius:5px;
                    padding:4px 5px;background:#fffbeb;outline:none;
                    {{ $locked ? 'cursor:not-allowed;opacity:.7;' : '' }}
                  "
                  onfocus="this.style.borderColor='#f59e0b'"
                  onblur="this.style.borderColor='#fde68a'">
                <input
                  class="cell-input"
                  type="number" step="0.01" min="0"
                  {{ $ro }}
                  value="{{ number_format($r->other_deduction ?? 0, 2, '.', '') }}"
                  data-field="other_deduction"
                  oninput="{{ $onch }}"
                  placeholder="0.00"
                  style="
                    min-width:68px;
                    {{ ($r->other_deduction ?? 0) > 0 ? 'background:#fffbeb;border-color:#f59e0b;color:#92400e;font-weight:700;' : '' }}
                  "
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- ALLOWANCES (3 cols) --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_pera ?? 0, 2, '.', '') }}"
                  data-field="allowance_pera" oninput="{{ $onch }}"
                  style="color:#0b5e40;font-weight:600;"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_rata ?? 0, 2, '.', '') }}"
                  data-field="allowance_rata" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_other ?? 0, 2, '.', '') }}"
                  data-field="allowance_other" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- TOTAL DEDUCTIONS --}}
            <td>
              <span class="cell-computed" id="ded_{{ $pid }}"
                style="color:#c0392b;font-weight:700;padding:5px 8px;display:block;text-align:right;font-size:11px;font-family:'JetBrains Mono',monospace;">
                {{ number_format($r->total_deductions, 2) }}
              </span>
            </td>

            {{-- STATUS --}}
            <td style="text-align:center;padding:0 6px;">
              <span id="status_{{ $pid }}" style="font-size:11px;color:#9ca3af;font-family:'Plus Jakarta Sans',sans-serif;">—</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="35" style="padding:64px;text-align:center;color:#9ca3af;font-family:'Plus Jakarta Sans',sans-serif;">
              <svg style="width:40px;height:40px;color:#d1d5db;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              No payroll records found.
              <a href="{{ route('payroll.create') }}" style="color:var(--forest);font-weight:600;text-decoration:none;border-bottom:1px solid var(--leaf);">Create a payroll period →</a>
            </td>
          </tr>
          @endforelse
        </tbody>

        @if($records->count())
        <tfoot>
          <tr>
            <td class="col-frozen-0 col-num" style="text-align:center;font-size:12px;opacity:.7;">Σ</td>
            <td class="col-frozen-1 col-name" style="font-family:'Plus Jakarta Sans',sans-serif;font-size:11px;opacity:.85;">{{ $records->count() }} employees</td>
            <td class="col-frozen-2 col-desig"></td>
            <td class="col-frozen-net net-footer" id="footerNet">{{ number_format($summary->net, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->gross, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->gsis_ee ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->gsis_govt ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->gsis_ec ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->pagibig ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->philhealth ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->wtax ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;font-size:10px;color:#92400e;">—</td>
            <td style="text-align:right;padding:7px 8px;">{{ number_format($summary->pera_total ?? 0, 2) }}</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;opacity:.4;">—</td>
            <td style="text-align:right;padding:7px 8px;color:#991a1a;font-weight:800;">{{ number_format($summary->deductions, 2) }}</td>
            <td></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    {{-- Save bar --}}
    <div class="save-bar hidden" id="saveBar">
      <div class="save-bar-info">
        <span id="dirtyCount">0</span> unsaved row(s)
      </div>
      <div style="display:flex;gap:8px;">
        <button class="btn-discard-all" onclick="discardAll()">↩ Discard</button>
        <button class="btn-save-all" onclick="saveAll()">
          <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          Save All Changes
        </button>
      </div>
    </div>

  </div>{{-- end .app-card --}}
</div>{{-- end .pay-page --}}

{{-- Toast --}}
<div id="toast">
  <div id="toastIcon" style="width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
  <div>
    <p style="font-size:13px;font-weight:700;color:#111827;margin:0;" id="toastTitle"></p>
    <p style="font-size:11px;color:#6b7280;margin:3px 0 0;" id="toastMsg"></p>
  </div>
</div>

{{-- ════════════════════════════════════════
     FINALIZE MODAL
════════════════════════════════════════ --}}
@if($selectedPeriod && $selectedPeriod->status === 'DRAFT')
<div id="finalizeOverlay" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;margin:16px;box-shadow:0 24px 80px rgba(0,0,0,.28);overflow:hidden;animation:modalIn .22s cubic-bezier(.34,1.56,.64,1);">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#1f4d1f,#2d6e25);padding:24px 28px 20px;">
      <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.12);border:2px solid rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
        <svg style="width:24px;height:24px;" fill="none" stroke="rgba(255,255,255,.9)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </div>
      <h2 style="font-size:20px;font-weight:800;color:#fff;letter-spacing:-.02em;margin:0 0 4px;">Finalize Payroll</h2>
      <p style="font-size:12px;color:rgba(255,255,255,.6);font-weight:500;margin:0 0 10px;">This action is permanent and cannot be undone</p>
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 10px;font-size:11px;font-weight:700;color:rgba(255,255,255,.9);">
        <span style="width:6px;height:6px;border-radius:50%;background:#fbbf24;flex-shrink:0;display:inline-block;"></span>
        {{ $selectedPeriod->period_label }} · DRAFT
      </span>
    </div>

    {{-- Warning strip --}}
    <div style="display:flex;align-items:flex-start;gap:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin:20px 20px 0;">
      <svg style="width:15px;height:15px;flex-shrink:0;margin-top:1px;" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
      <p style="font-size:12px;color:#92400e;font-weight:600;line-height:1.5;margin:0;">Once finalized, all payroll records will be locked. No further edits will be allowed for this period.</p>
    </div>

    {{-- Summary checklist --}}
    <div style="padding:16px 20px 4px;">
      @foreach([
        ['#e6f4e6','#1a6b1a','M5 13l4 4L19 7', $summary->employees.' employees included', 'All active employees have payroll records'],
        ['#dbeafe','#1e3a8a','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'Net Pay: ₱'.number_format($summary->net,0), 'Gross ₱'.number_format($summary->gross,0).' · Deductions ₱'.number_format($summary->deductions,0)],
        ['#ede9fe','#5b21b6','M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z', 'W/Tax: ₱'.number_format($summary->wtax,0), 'Tax records computed for this period']
      ] as [$bg,$stroke,$path,$title,$sub])
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f3f4f6;">
        <div style="width:28px;height:28px;border-radius:8px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg style="width:14px;height:14px;" fill="none" stroke="{{ $stroke }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
        </div>
        <div>
          <div style="font-size:12px;font-weight:700;color:#111827;">{{ $title }}</div>
          <div style="font-size:11px;color:#6b7280;font-weight:500;">{{ $sub }}</div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Confirm checkbox --}}
    <div style="padding:16px 20px 4px;">
      <label id="fzLabel" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:11px 14px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;transition:all .15s;user-select:none;">
        <input type="checkbox" id="fzCheck" style="width:16px;height:16px;accent-color:#2d5a2d;cursor:pointer;flex-shrink:0;" onchange="fzToggle(this)">
        <span style="font-size:12px;font-weight:600;color:#374151;">I confirm the figures above are correct and want to finalize</span>
      </label>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:10px;padding:16px 20px 20px;">
      <button onclick="closeFinalizeModal()" style="flex:1;padding:11px;font-size:13px;font-weight:700;color:#6b7280;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .15s;" onmouseover="this.style.borderColor='#9ca3af'" onmouseout="this.style.borderColor='#e5e7eb'">Cancel</button>
      <form method="POST" action="{{ route('payroll.finalize', $selectedPeriod->period_id) }}" id="fzForm" style="flex:2;display:contents;">
        @csrf
        <button type="submit" id="fzBtn" disabled style="flex:2;padding:11px 20px;font-size:13px;font-weight:800;color:#fff;background:#b91c1c;border:none;border-radius:10px;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;opacity:.4;pointer-events:none;transition:all .2s;">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Finalize Payroll
        </button>
      </form>
    </div>

  </div>
</div>
@endif

<style>
@keyframes modalIn { from { opacity:0; transform:scale(.94) translateY(8px); } to { opacity:1; transform:none; } }
</style>

<script>
const CSRF         = '{{ csrf_token() }}';
const IS_FINALIZED = {{ optional($selectedPeriod)->status === 'FINALIZED' ? 'true' : 'false' }};
const DED_CONFIG   = @json($jsConfig);

/* ── Dirty tracking ── */
const dirtyRows = new Set();

function markDirty(pid, inputEl) {
  const row = document.getElementById('row_' + pid);
  if (!row) return;
  row.classList.add('row-dirty');
  if (inputEl) inputEl.classList.add('changed');
  dirtyRows.add(pid);
  updateSaveBar();
  recomputeRow(pid);
}

function updateSaveBar() {
  document.getElementById('dirtyCount').textContent = dirtyRows.size;
  document.getElementById('saveBar').classList.toggle('hidden', dirtyRows.size === 0);
}

const EDITABLE_DEDUCTION_FIELDS = [
  'gsis_ee','gsis_ec','gsis_policy','gsis_emergency',
  'gsis_real_estate','gsis_mpl','gsis_mpl_lite','gsis_gfal',
  'gsis_computer','gsis_conso',
  'pagibig_ee','pagibig_mpl','pagibig_calamity',
  'philhealth_ee','withholding_tax',
  'loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle','overpayment','other_deduction'
];
const ALLOWANCE_FIELDS = ['allowance_pera','allowance_rata','allowance_other'];

function getRowVal(row, field) {
  const inp = row.querySelector('[data-field="' + field + '"]');
  return parseFloat(inp?.value) || 0;
}

function recomputeRow(pid) {
  const row = document.getElementById('row_' + pid);
  if (!row) return;
  const gross = parseFloat(row.dataset.gross) || 0;
  const ded   = EDITABLE_DEDUCTION_FIELDS.reduce((s, f) => s + getRowVal(row, f), 0);
  const alw   = ALLOWANCE_FIELDS.reduce((s, f) => s + getRowVal(row, f), 0);
  const net   = gross - ded + alw;
  const fmt   = n => n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  const dedEl = document.getElementById('ded_' + pid);
  const netEl = document.getElementById('net_' + pid);

  if (dedEl) dedEl.textContent = fmt(ded);
  if (netEl) {
    netEl.textContent = fmt(net);
    netEl.classList.toggle('positive', net > 0);
  }

  updateFooterTotals();
}

function updateFooterTotals() {
  const footerNet = document.getElementById('footerNet');
  if (!footerNet) return;
  let totalNet = 0;
  document.querySelectorAll('#payrollTbody tr[data-id]').forEach(row => {
    const pid = row.dataset.id;
    const netEl = document.getElementById('net_' + pid);
    if (netEl && row.style.display !== 'none') {
      totalNet += parseFloat(netEl.textContent.replace(/,/g, '')) || 0;
    }
  });
  footerNet.textContent = totalNet.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ── Collect row data ── */
function collectRow(pid) {
  const row = document.getElementById('row_' + pid);
  if (!row) return null;
  const get = f => parseFloat(row.querySelector('[data-field="' + f + '"]')?.value) || 0;
  return {
    gsis_ee: get('gsis_ee'), gsis_ec: get('gsis_ec'),
    gsis_policy: get('gsis_policy'), gsis_emergency: get('gsis_emergency'),
    gsis_real_estate: get('gsis_real_estate'), gsis_mpl: get('gsis_mpl'),
    gsis_mpl_lite: get('gsis_mpl_lite'), gsis_gfal: get('gsis_gfal'),
    gsis_computer: get('gsis_computer'), gsis_conso: get('gsis_conso'),
    pagibig_ee: get('pagibig_ee'), pagibig_mpl: get('pagibig_mpl'),
    pagibig_calamity: get('pagibig_calamity'), philhealth_ee: get('philhealth_ee'),
    withholding_tax: get('withholding_tax'), loan_dbp: get('loan_dbp'),
    loan_lbp: get('loan_lbp'), loan_cngwmpc: get('loan_cngwmpc'),
    loan_paracle: get('loan_paracle'), overpayment: get('overpayment'),
    other_deduction: get('other_deduction'),
    other_deduction_label: (row.querySelector('[data-field="other_deduction_label"]')?.value ?? ''),
    allowance_pera: get('allowance_pera'), allowance_rata: get('allowance_rata'),
    allowance_other: get('allowance_other'),
  };
}

/* ── Save all dirty rows ── */
async function saveAll() {
  if (dirtyRows.size === 0) return;
  const btn = document.querySelector('.btn-save-all');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  const promises = [...dirtyRows].map(pid => {
    const data = collectRow(pid);
    if (!data) return Promise.resolve();
    const statusEl = document.getElementById('status_' + pid);
    if (statusEl) statusEl.textContent = '…';
    return fetch('/payroll/record/' + pid, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
      const row      = document.getElementById('row_' + pid);
      const statusEl = document.getElementById('status_' + pid);
      if (res.success) {
        row?.classList.remove('row-dirty');
        row?.classList.add('row-saved');
        row?.querySelectorAll('.changed').forEach(el => el.classList.remove('changed'));
        if (statusEl) statusEl.innerHTML = '<span style="color:#1a6b1a;font-size:13px;">✓</span>';
        setTimeout(() => row?.classList.remove('row-saved'), 1800);
        dirtyRows.delete(pid);
      } else {
        if (statusEl) statusEl.innerHTML = '<span style="color:#c0392b;font-size:13px;">✗</span>';
      }
    })
    .catch(() => {
      const statusEl = document.getElementById('status_' + pid);
      if (statusEl) statusEl.innerHTML = '<span style="color:#c0392b;font-size:13px;">✗</span>';
    });
  });

  await Promise.all(promises);

  btn.disabled = false;
  btn.innerHTML = '<svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save All Changes';
  updateSaveBar();

  showToast(
    dirtyRows.size === 0 ? 'Saved!' : 'Partial save',
    dirtyRows.size === 0 ? 'All changes saved successfully.' : dirtyRows.size + ' row(s) failed.',
    dirtyRows.size === 0 ? 'success' : 'error'
  );
}

/* ── Discard all ── */
function discardAll() {
  dirtyRows.forEach(pid => {
    const row = document.getElementById('row_' + pid);
    if (!row) return;
    const orig = JSON.parse(row.dataset.orig || '{}');
    const fields = [
      'gsis_ee','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
      'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
      'pagibig_ee','pagibig_mpl','pagibig_calamity','philhealth_ee',
      'withholding_tax','loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle','overpayment',
      'other_deduction','other_deduction_label',
      'allowance_pera','allowance_rata','allowance_other',
    ];
    fields.forEach(field => {
      const inp = row.querySelector('[data-field="' + field + '"]');
      if (inp) { inp.value = parseFloat(orig[field] || 0).toFixed(2); inp.classList.remove('changed'); }
    });
    row.classList.remove('row-dirty');
    recomputeRow(pid);
    const statusEl = document.getElementById('status_' + pid);
    if (statusEl) statusEl.textContent = '—';
  });
  dirtyRows.clear();
  updateSaveBar();
  showToast('Discarded', 'All changes reverted.', 'info');
}

/* ── Filter ── */
function filterPayroll(query) {
  const q    = (query || '').toLowerCase().trim();
  const dept = (document.getElementById('filterDept')?.value || '').toLowerCase();
  document.querySelectorAll('#payrollTbody tr[data-id]').forEach(row => {
    const matchName = !q    || (row.dataset.search || '').includes(q);
    const matchDept = !dept || (row.dataset.dept   || '').includes(dept);
    row.style.display = (matchName && matchDept) ? '' : 'none';
  });
  updateFooterTotals();
}

/* ── Toast ── */
function showToast(title, msg, type) {
  const map = {
    success: { bg: '#dcfce7', c: '#16a34a', p: 'M5 13l4 4L19 7' },
    error:   { bg: '#fee2e2', c: '#dc2626', p: 'M6 18L18 6M6 6l12 12' },
    info:    { bg: '#dbeafe', c: '#2563eb', p: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  };
  const s = map[type] || map.info;
  document.getElementById('toastTitle').textContent = title;
  document.getElementById('toastMsg').textContent   = msg;
  const icon = document.getElementById('toastIcon');
  icon.innerHTML  = '<svg style="width:16px;height:16px;" fill="none" stroke="' + s.c + '" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + s.p + '"/></svg>';
  icon.style.background = s.bg;
  const t = document.getElementById('toast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

/* ── Ctrl+S / Cmd+S ── */
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    if (dirtyRows.size > 0) saveAll();
  }
});

/* ── Finalize Modal ── */
function openFinalizeModal() {
  const overlay = document.getElementById('finalizeOverlay');
  if (overlay) overlay.style.display = 'flex';
}
function closeFinalizeModal() {
  const overlay = document.getElementById('finalizeOverlay');
  if (overlay) overlay.style.display = 'none';
  const cb = document.getElementById('fzCheck');
  if (cb) { cb.checked = false; fzToggle({ checked: false }); }
}
function fzToggle(cb) {
  const btn = document.getElementById('fzBtn');
  const lbl = document.getElementById('fzLabel');
  if (!btn || !lbl) return;
  btn.disabled        = !cb.checked;
  btn.style.opacity   = cb.checked ? '1' : '.4';
  btn.style.pointerEvents = cb.checked ? 'auto' : 'none';
  btn.style.boxShadow = cb.checked ? '0 4px 14px rgba(153,27,27,.35)' : 'none';
  lbl.style.borderColor = cb.checked ? '#3d7a2a' : '#e5e7eb';
  lbl.style.background  = cb.checked ? '#f0fdf4' : '#f9fafb';
}
// Close on backdrop click
const fzOverlay = document.getElementById('finalizeOverlay');
if (fzOverlay) {
  fzOverlay.addEventListener('click', function(e) {
    if (e.target === this) closeFinalizeModal();
  });
}

/* ══════════════════════════════
  HEIGHT FIX
══════════════════════════════ */
(function initHeightFill() {
  const page = document.querySelector('.pay-page');
  if (!page) return;
  function applyHeight() {
    const top = page.getBoundingClientRect().top + window.scrollY;
    page.style.height = 'calc(100vh - ' + Math.ceil(top) + 'px)';
  }
  applyHeight();
  if (window.ResizeObserver) {
    new ResizeObserver(applyHeight).observe(document.documentElement);
  }
  window.addEventListener('resize', applyHeight);
})();
</script>
@endsection