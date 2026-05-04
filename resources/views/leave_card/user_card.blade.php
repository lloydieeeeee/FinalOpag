@extends('layouts.app')
@section('title', 'My Leave Card')
@section('page-title', 'My Leave Card')

@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; }

.ulc-page { display:flex; flex-direction:column; gap:16px; }

.ulc-top {
    background:#fff; border-radius:16px;
    border:1px solid #f3f4f6;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    padding:10px 20px;
    display:flex; align-items:center; justify-content:flex-end; gap:12px; flex-wrap:wrap;
}

.ulc-year-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.ulc-year-label { font-size:12px; font-weight:600; color:#374151; }
.ulc-year-select {
    padding:6px 28px 6px 10px; font-size:12px; font-weight:700;
    border:1px solid #d1d5db; border-radius:8px; background:#fff;
    color:#1a3a1a; cursor:pointer; outline:none;
    appearance:none; -webkit-appearance:none; position:relative;
}
.ulc-year-select:focus { border-color:#2d5a1b; }
.ulc-year-wrap { position:relative; display:inline-block; }
.ulc-year-wrap svg { position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }

.ulc-print-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 14px; border-radius:8px; font-size:11px; font-weight:700;
    background:#1a3a1a; color:#fff; border:none; cursor:pointer; transition:background .15s;
    white-space:nowrap;
}
.ulc-print-btn:hover { background:#2d5a1b; }

.ulc-sheet-card {
    background:#fff; border-radius:16px;
    border:1px solid #f3f4f6;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    overflow:hidden;
}
.ulc-sheet-header {
    padding:10px 20px; border-bottom:1px solid #f3f4f6;
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
}

.ulc-legend { display:flex; align-items:center; gap:10px; flex-wrap:wrap; font-size:10px; }
.ulc-legend-item { display:inline-flex; align-items:center; gap:5px; font-weight:600; color:#6b7280; }
.ulc-legend-dot { display:inline-block; width:10px; height:10px; border-radius:3px; flex-shrink:0; }

/* ── Fixed header above the scroll area ── */
.ulc-sheet-title {
    text-align:center; padding:14px 20px 10px; border-bottom:2px solid #1a3a1a;
}
.ulc-sheet-title h3 { font-size:13px; font-weight:800; color:#1a3a1a; margin:0 0 2px; letter-spacing:.05em; }
.ulc-sheet-title p  { font-size:12px; font-weight:700; color:#1a3a1a; margin:0; }

.ulc-opening-bal {
    padding:8px 20px; background:#f0fdf4; font-size:12px;
    color:#166534; font-weight:600; border-bottom:1px solid #d1fae5;
}

/* ── Scroll wrapper — only the table scrolls ── */
.ulc-sheet-wrap {
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

.ulc-sheet {
    width:100%; border-collapse:collapse; font-size:12px;
    font-family:'Courier New', Courier, monospace;
    min-width:860px;
}
.ulc-sheet thead tr { background:#f1f5f9; }
.ulc-sheet th {
    padding:8px 8px; text-align:center; font-size:10px; font-weight:700;
    color:#475569; border:1px solid #e2e8f0; white-space:nowrap;
    background:#f1f5f9; position:sticky; top:0; z-index:2;
}
.ulc-sheet th.left { text-align:left; }
.ulc-sheet td { padding:7px 8px; border:1px solid #e2e8f0; color:#374151; text-align:right; vertical-align:middle; }
.ulc-sheet td.left { text-align:left; }
.ulc-sheet td.center { text-align:center; }

.ulc-sheet tr.row-approved  td { background:#f0fdf4; }
.ulc-sheet tr.row-rejected  td { background:#fff5f5; color:#9ca3af; }
.ulc-sheet tr.row-cancelled td { background:#f9fafb; color:#9ca3af; }
.ulc-sheet tr.row-recalled  td { background:#fffbeb; color:#9ca3af; }
.ulc-sheet tr.row-monetized td { background:#f0fdfa; }
.ulc-sheet tr.row-half-day  td { background:#fdf4ff; }
.ulc-sheet tr.row-manual    td { background:#fff; }

.ulc-sheet tr.row-rejected  td.num,
.ulc-sheet tr.row-cancelled td.num,
.ulc-sheet tr.row-recalled  td.num { text-decoration:line-through; }

.ulc-sheet td.bal { background:#f0fdf4 !important; color:#166534 !important; font-weight:700; }
.ulc-sheet td.bal.neg { background:#fef2f2 !important; color:#991b1b !important; }
.ulc-sheet tr.row-rejected  td.bal,
.ulc-sheet tr.row-cancelled td.bal,
.ulc-sheet tr.row-recalled  td.bal { background:#f9fafb !important; color:#9ca3af !important; }
.ulc-sheet tr.row-monetized td.bal { background:#ccfbf1 !important; color:#0f766e !important; }

.status-badge {
    display:inline-block; padding:2px 8px; border-radius:5px;
    font-size:9px; font-weight:800; letter-spacing:.04em; text-transform:uppercase;
}
.sb-approved  { background:#dcfce7; color:#166534; }
.sb-rejected  { background:#fee2e2; color:#991b1b; }
.sb-cancelled { background:#f3f4f6; color:#6b7280; }
.sb-recalled  { background:#fef9c3; color:#854d0e; }
.sb-monetized { background:#ccfbf1; color:#0f766e; }
.sb-pending   { background:#fef9c3; color:#92400e; }
.sb-default   { background:#f3f4f6; color:#374151; }

.ulc-empty {
    padding:60px 20px; text-align:center;
    display:flex; flex-direction:column; align-items:center; gap:10px;
}
.ulc-empty-icon { width:48px; height:48px; color:#d1d5db; }
.ulc-empty h3 { font-size:14px; font-weight:700; color:#9ca3af; margin:0; }
.ulc-empty p  { font-size:12px; color:#d1d5db; margin:0; }

.ulc-loading {
    padding:60px 20px; text-align:center;
    display:flex; flex-direction:column; align-items:center; gap:12px;
    color:#9ca3af; font-size:12px;
}
.ulc-spinner {
    width:28px; height:28px;
    border:3px solid #f3f4f6; border-top-color:#1a3a1a;
    border-radius:50%; animation:spin .7s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

.ulc-notice {
    margin:20px; padding:14px 18px; border-radius:12px;
    background:#fef9c3; border:1px solid #fde68a;
    font-size:12px; color:#854d0e; display:flex; align-items:center; gap:10px;
}

/* ── PDF banner ── */
.ulc-pdf-banner {
    padding:8px 20px;
    background:#eff6ff;
    border-bottom:1px solid #bfdbfe;
    font-size:12px;
    color:#1e40af;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}
.ulc-pdf-banner a {
    color:#2563eb;
    text-decoration:underline;
    font-weight:700;
}
.ulc-pdf-banner a:hover { color:#1d4ed8; }

/* ── PDF embed viewer ── */
.ulc-pdf-embed {
    border-top:1px solid #bfdbfe;
    background:#f8fafc;
}
.ulc-pdf-embed-header {
    padding:8px 16px;
    background:#dbeafe;
    border-bottom:1px solid #bfdbfe;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
}
.ulc-pdf-iframe {
    width:100%;
    height:780px;
    border:none;
    display:block;
}
.ulc-pdf-newbtn {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11px;
    font-weight:700;
    background:#fff;
    color:#2563eb;
    border:1px solid #bfdbfe;
    cursor:pointer;
    text-decoration:none;
    transition:background .15s;
    white-space:nowrap;
}
.ulc-pdf-newbtn:hover { background:#eff6ff; }

@media (max-width:640px) {
    .ulc-top { padding:10px 14px; }
    .ulc-sheet-header { padding:10px 14px; }
    .ulc-sheet-title { padding:12px 14px 10px; }
    .ulc-sheet-title h3 { font-size:12px; }
    .ulc-opening-bal { padding:8px 14px; font-size:11px; }
    .ulc-pdf-iframe { height:460px; }
}
</style>

<div class="ulc-page" id="ulcPage">

    {{-- ── Top bar ── --}}
    <div class="ulc-top">
        <div class="ulc-year-row">
            <span class="ulc-year-label">Year:</span>
            <div class="ulc-year-wrap">
                <select class="ulc-year-select" id="ulcYear" onchange="ulcLoad()">
                    @for($y = now()->year; $y >= 2025; $y--)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <button class="ulc-print-btn" onclick="ulcPrint()">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- ── Sheet card ── --}}
    <div class="ulc-sheet-card">
        <div class="ulc-sheet-header">
            <div style="flex:1;"></div>
            <div class="ulc-legend">
                <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Legend:</span>
                <span class="ulc-legend-item"><span class="ulc-legend-dot" style="background:#f0fdf4;border:1px solid #bbf7d0;"></span>Approved</span>
                <span class="ulc-legend-item" style="color:#991b1b;"><span class="ulc-legend-dot" style="background:#fff5f5;border:1px solid #fca5a5;"></span>Rejected</span>
                <span class="ulc-legend-item"><span class="ulc-legend-dot" style="background:#f9fafb;border:1px solid #d1d5db;"></span>Cancelled</span>
                <span class="ulc-legend-item" style="color:#854d0e;"><span class="ulc-legend-dot" style="background:#fffbeb;border:1px solid #fde68a;"></span>Recalled</span>
                <span class="ulc-legend-item" style="color:#7c3aed;"><span class="ulc-legend-dot" style="background:#fdf4ff;border:1px solid #e9d5ff;"></span>Half-day</span>
                <span class="ulc-legend-item" style="color:#0f766e;"><span class="ulc-legend-dot" style="background:#f0fdfa;border:1px solid #99f6e4;"></span>Monetized</span>
            </div>
        </div>

        <div id="ulcSheetBody">
            <div class="ulc-loading">
                <div class="ulc-spinner"></div>
                Loading your leave card…
            </div>
        </div>
    </div>

</div>

<script>
const ULC_FETCH_URL    = "{{ route('leave-card.data') }}";
const ULC_PRINT_URL    = "{{ route('leave-card.print') }}";
const ULC_PDF_BASE_URL = "{{ route('leave-card.old-balance-pdf') }}";
const CSRF             = "{{ csrf_token() }}";

const MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];

function fmtRaw(v) {
    if (v === null || v === undefined || v === '') return '—';
    const s = String(v).trim();
    if (s === '' || s === 'null') return '—';
    const parts   = s.split('.');
    const intPart = parts[0];
    const decPart = parts[1] ?? '';
    const dec3    = (decPart + '000').substring(0, 3);
    return `${intPart}.${dec3}`;
}

function statusBadge(status) {
    if (!status) return '';
    const map = {
        'APPROVED' : 'sb-approved',
        'REJECTED' : 'sb-rejected',
        'CANCELLED': 'sb-cancelled',
        'RECALLED' : 'sb-recalled',
        'MONETIZED': 'sb-monetized',
        'PENDING'  : 'sb-pending',
    };
    const cls = map[status.toUpperCase()] || 'sb-default';
    return `<span class="status-badge ${cls}">${escHtml(status)}</span>`;
}

function rowClass(status, isHalfDay) {
    if (isHalfDay) return 'row-half-day';
    const map = {
        'APPROVED' : 'row-approved',
        'REJECTED' : 'row-rejected',
        'CANCELLED': 'row-cancelled',
        'RECALLED' : 'row-recalled',
        'MONETIZED': 'row-monetized',
    };
    return map[(status || '').toUpperCase()] || 'row-manual';
}

function pdfBannerHtml(year, refYear) {
    return `
        <div class="ulc-pdf-banner">
            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Old Balance PDF (${refYear}) is available —
            <a href="${ULC_PDF_BASE_URL}?year=${year}" target="_blank">Open in new tab</a>
        </div>
        <div class="ulc-pdf-embed">
            <div class="ulc-pdf-embed-header">
                <span style="font-size:12px;font-weight:700;color:#1e40af;">
                    Old Balance PDF — ${refYear}
                </span>
                <a href="${ULC_PDF_BASE_URL}?year=${year}" target="_blank" class="ulc-pdf-newbtn">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Open in New Tab
                </a>
            </div>
            <iframe
                src="${ULC_PDF_BASE_URL}?year=${year}"
                class="ulc-pdf-iframe"
                title="Old Balance PDF ${refYear}">
            </iframe>
        </div>`;
}

async function ulcLoad() {
    const year = document.getElementById('ulcYear').value;

    document.getElementById('ulcSheetBody').innerHTML = `
        <div class="ulc-loading">
            <div class="ulc-spinner"></div>
            Loading your leave card…
        </div>`;

    try {
        const res  = await fetch(`${ULC_FETCH_URL}?year=${year}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        const hasPdf = data.old_balance?.found && data.old_balance?.has_pdf;

        if (!data.success) {
            document.getElementById('ulcSheetBody').innerHTML = `
                ${hasPdf ? pdfBannerHtml(year, data.old_balance.reference_year) : ''}
                <div class="ulc-notice">
                    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    No leave card has been set up for <strong>${year}</strong> yet. Please contact HR.
                </div>`;
            return;
        }

        const rawOpenVl = fmtRaw(data.card.opening_vl);
        const rawOpenSl = fmtRaw(data.card.opening_sl);

        renderSheet(data.entries || [], rawOpenVl, rawOpenSl);

        /* ── Inject PDF banner above the table if old_balance exists ── */
        if (hasPdf) {
            const pdfWrap = document.createElement('div');
            pdfWrap.innerHTML = pdfBannerHtml(year, data.old_balance.reference_year);

            const sheetBody = document.getElementById('ulcSheetBody');
            const wrap      = sheetBody.querySelector('.ulc-sheet-wrap');
            if (wrap) sheetBody.insertBefore(pdfWrap, wrap);
        }

    } catch (e) {
        document.getElementById('ulcSheetBody').innerHTML = `
            <div class="ulc-empty">
                <svg class="ulc-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>Could not load leave card</h3>
                <p>Please try refreshing the page.</p>
            </div>`;
    }
}

function renderSheet(entries, rawOpenVl, rawOpenSl) {
    if (!entries.length) {
        document.getElementById('ulcSheetBody').innerHTML = `
            <div class="ulc-empty">
                <svg class="ulc-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3>No entries yet</h3>
                <p>Your leave card for this year is empty.</p>
            </div>`;
        return;
    }

    let rowNum = 0;
    let rows   = '';

    entries.forEach(entry => {
        if (entry.date_particulars === '--- EXCLUDED ---') return;
        if (entry.is_separator) return;

        rowNum++;
        const status    = (entry.status || '').toUpperCase();
        const isHalfDay = !!(entry.is_half_day);
        const rCls      = rowClass(status, isHalfDay);

        const balVlRaw = fmtRaw(entry.balance_vl);
        const balSlRaw = fmtRaw(entry.balance_sl);

        const balVlNum = parseFloat(entry.balance_vl);
        const balSlNum = parseFloat(entry.balance_sl);
        const balVlCls = `bal${(!isNaN(balVlNum) && balVlNum < 0) ? ' neg' : ''}`;
        const balSlCls = `bal${(!isNaN(balSlNum) && balSlNum < 0) ? ' neg' : ''}`;

        const isNonApproved = ['REJECTED','CANCELLED','RECALLED'].includes(status);
        const numCls        = isNonApproved ? 'num' : '';

        rows += `
        <tr class="${rCls}">
            <td class="center" style="color:#9ca3af;font-size:10px;">${rowNum}</td>
            <td class="left"   style="font-size:11px;white-space:nowrap;">${entry.month ? MONTHS[entry.month - 1] : ''}</td>
            <td class="left"   style="min-width:200px;">${escHtml(entry.date_particulars || '')}</td>
            <td class="${numCls}">${fmtRaw(entry.earned_vl)}</td>
            <td class="${numCls}">${fmtRaw(entry.earned_sl)}</td>
            <td class="${numCls}">${fmtRaw(entry.taken_vl)}</td>
            <td class="${numCls}">${fmtRaw(entry.taken_sl)}</td>
            <td class="${numCls}">${fmtRaw(entry.leave_wop)}</td>
            <td class="${numCls}">${fmtRaw(entry.tardy_undertime)}</td>
            <td class="${balVlCls}">${balVlRaw}</td>
            <td class="${balSlCls}">${balSlRaw}</td>
            <td class="left"  style="font-size:11px;">${escHtml(entry.remarks || '')}</td>
            <td class="center">${statusBadge(entry.status)}</td>
        </tr>`;
    });

    document.getElementById('ulcSheetBody').innerHTML = `
        <div class="ulc-sheet-title">
            <h3>PROVINCIAL GOVERNMENT OF CAMARINES NORTE</h3>
            <p>RECORD OF LEAVE OF ABSENCE</p>
        </div>
        <div class="ulc-opening-bal">
            Opening Balance (as carried forward): &nbsp;
            VL: <strong>${rawOpenVl}</strong> &nbsp;&nbsp;
            SL: <strong>${rawOpenSl}</strong>
        </div>
        <div class="ulc-sheet-wrap">
            <table class="ulc-sheet">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th style="width:72px;" class="left">Month</th>
                        <th class="left" style="min-width:200px;">Date / Particulars</th>
                        <th colspan="2" style="background:#e0f2fe;color:#0369a1;">Leave Earned</th>
                        <th colspan="2" style="background:#dcfce7;color:#166534;">Leave Taken (W/Pay)</th>
                        <th style="background:#fef9c3;color:#854d0e;width:64px;">W/O Pay</th>
                        <th style="background:#fee2e2;color:#991b1b;width:70px;">Tardy / UT</th>
                        <th colspan="2" style="background:#f0fdf4;color:#166534;">Balance</th>
                        <th class="left" style="min-width:110px;">Remarks</th>
                        <th style="min-width:80px;">Status</th>
                    </tr>
                    <tr style="background:#f8fafc;">
                        <th></th><th></th><th></th>
                        <th style="background:#e0f2fe;color:#0369a1;font-size:9px;">VL</th>
                        <th style="background:#e0f2fe;color:#0369a1;font-size:9px;">SL</th>
                        <th style="background:#dcfce7;color:#166534;font-size:9px;">VL</th>
                        <th style="background:#dcfce7;color:#166634;font-size:9px;">SL</th>
                        <th></th><th></th>
                        <th style="background:#f0fdf4;color:#166534;font-size:9px;">VL BAL</th>
                        <th style="background:#f0fdf4;color:#166534;font-size:9px;">SL BAL</th>
                        <th></th><th></th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

function ulcPrint() {
    const year = document.getElementById('ulcYear').value;
    window.open(`${ULC_PRINT_URL}?year=${year}`, '_blank');
}

function escHtml(s) {
    return String(s)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', ulcLoad);
</script>
@endsection