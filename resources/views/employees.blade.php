@extends('layouts.app')
@section('title', 'Employees')
@section('page-title', 'Employees')

@section('content')

<style>
    /* ─── TABLE CARD ─── */
    .table-card { display:flex;flex-direction:column;max-height:calc(100vh - 140px);min-height:0; }
    .table-card-header { flex-shrink:0; }
    .table-card-footer { flex-shrink:0; }
    .table-scroll { flex:1;overflow-y:auto;overflow-x:auto;scrollbar-width:thin;scrollbar-color:#d1d5db transparent; }
    .table-scroll::-webkit-scrollbar { width:5px;height:5px; }
    .table-scroll::-webkit-scrollbar-track { background:transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background:#d1d5db;border-radius:99px; }
    #employeeTable thead tr { position:sticky;top:0;z-index:2;background:#fafafa;box-shadow:0 1px 0 #f3f4f6; }
    #employeeTable { font-size:11px; }
    #employeeTable thead th { font-size:11px !important; }
    #employeeTable tbody td { font-size:11px !important; }
    .badge { display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;border:none;outline:none;white-space:nowrap; }
    .badge-active   { background:#dcfce7;color:#15803d; }
    .badge-inactive { background:#fee2e2;color:#b91c1c; }
    tbody tr { transition:background 0.12s; }
    tbody tr:hover { background:#f9fafb; }
    .sort-btn { background:none;border:none;cursor:pointer;padding:0 3px;color:#cbd5e1;transition:color 0.15s;display:inline-flex;align-items:center; }
    .sort-btn:hover { color:#2d5a1b; }
    .sort-btn svg { transition:transform 0.2s; }
    .sort-btn.asc { color:#2d5a1b; }
    .sort-btn.asc svg { transform:rotate(180deg); }
    .sort-btn.desc { color:#2d5a1b; }
    .action-btn { background:none;border:1.5px solid #e5e7eb;border-radius:7px;padding:4px 7px;cursor:pointer;color:#6b7280;transition:all 0.15s;display:inline-flex;align-items:center; }
    .action-btn:hover { background:#f0fdf4;border-color:#2d5a1b;color:#2d5a1b; }

    /* ─── OVERLAY ─── */
    #overlay { position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:40;opacity:0;pointer-events:none;transition:opacity 0.3s ease; }
    #overlay.show { opacity:1;pointer-events:all; }

    /* ─── PANELS — shared base ─── */
    #empPanel,
    #viewPanel {
        position:fixed;top:0;right:0;bottom:0;left:0;
        z-index:9999;
        width:100%;min-width:0;max-width:100%;
        display:flex;flex-direction:column;
        pointer-events:none;
        transform:translateX(100%);
        transition:transform 0.36s cubic-bezier(0.32,0.72,0,1);
        isolation:isolate;
        filter:none !important;
        backdrop-filter:none !important;
        -webkit-backdrop-filter:none !important;
    }
    @media (min-width:641px) {
        #empPanel  { left:auto;width:55vw;min-width:480px;max-width:900px; }
        #viewPanel { left:auto;width:62vw;min-width:560px;max-width:1000px; }
    }
    #empPanel.open,
    #viewPanel.open { pointer-events:all;transform:translateX(0); }

    /* Force panel children to also be unblurred */
    #empPanel *,
    #viewPanel * {
        filter:none !important;
        backdrop-filter:none !important;
        -webkit-backdrop-filter:none !important;
    }

    /* ─── MODAL BOX ─── */
    .modal-box { background:#fff;width:100%;height:100%;display:flex;flex-direction:column;box-shadow:-12px 0 60px rgba(0,0,0,0.22);overflow:hidden; }
    .modal-header { background:#2d5a1b;padding:20px 24px 18px;display:flex;align-items:flex-start;justify-content:space-between;flex-shrink:0; }
    .modal-header h2 { font-size:18px;font-weight:700;color:#fff;margin:0 0 3px;letter-spacing:-0.01em; }
    .modal-header p { font-size:12px;color:rgba(255,255,255,0.65);margin:0; }
    .modal-close { background:rgba(255,255,255,0.15);border:none;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:rgba(255,255,255,0.8);transition:background 0.15s;flex-shrink:0;margin-top:2px; }
    .modal-close:hover { background:rgba(255,255,255,0.25);color:#fff; }
    .modal-body { flex:1;overflow-y:auto;padding:0;background:#f8f9fa;scrollbar-width:thin;scrollbar-color:#d1d5db transparent; }
    .modal-body::-webkit-scrollbar { width:4px; }
    .modal-body::-webkit-scrollbar-thumb { background:#d1d5db;border-radius:99px; }
    .form-section-card { background:#fff;border-radius:12px;margin:16px 20px;padding:20px 22px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .section-heading { display:flex;align-items:center;gap:10px;margin-bottom:18px; }
    .section-icon { width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#2d5a1b;flex-shrink:0; }
    .section-title { font-size:14px;font-weight:700;color:#111827;margin:0; }
    .field-label { display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:5px; }
    .field-label .req { color:#ef4444; }
    .form-field { width:100%;background:#f3f4f6;border:1.5px solid transparent;border-radius:8px;padding:9px 12px;font-size:13px;color:#111827;outline:none;transition:border-color 0.15s,background 0.15s; }
    .form-field:focus { background:#fff;border-color:#2d5a1b;box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .form-field::placeholder { color:#9ca3af; }
    .form-field.field-error { border-color:#ef4444 !important;background:#fff5f5 !important; }
    .form-field.field-readonly { background:#f3f4f6 !important;color:#6b7280 !important;cursor:not-allowed; }
    select.form-field { cursor:pointer; }
    .phone-wrap { display:flex; }
    .flag-select { display:flex;align-items:center;gap:6px;background:#f3f4f6;border:1.5px solid transparent;border-radius:8px 0 0 8px;padding:9px 12px;font-size:13px;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap; }
    .phone-input { flex:1;background:#f3f4f6;border:1.5px solid transparent;border-radius:0 8px 8px 0;padding:9px 12px;font-size:13px;color:#111827;outline:none;transition:border-color 0.15s,background 0.15s; }
    .phone-input:focus { background:#fff;border-color:#2d5a1b;box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .salary-wrap { position:relative; }
    .salary-prefix { position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6b7280;pointer-events:none; }
    .salary-input { padding-left:24px !important; }
    .pw-wrap { position:relative; }
    .pw-toggle { position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;display:flex;align-items:center;transition:color 0.15s; }
    .pw-toggle:hover { color:#374151; }
    .error-msg { font-size:11px;color:#ef4444;margin-top:4px;display:none; }
    .error-msg.show { display:block; }
    .id-hint { font-size:11px;color:#9ca3af;margin-top:4px;font-family:monospace; }
    .sd-wrap { position:relative; }
    .sd-trigger { width:100%;background:#f3f4f6;border:1.5px solid transparent;border-radius:8px;padding:9px 34px 9px 12px;font-size:13px;color:#111827;outline:none;cursor:pointer;text-align:left;display:flex;align-items:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:border-color 0.15s,background 0.15s; }
    .sd-trigger:focus { background:#fff;border-color:#2d5a1b; }
    .sd-trigger.placeholder-shown { color:#9ca3af; }
    .sd-trigger.field-error { border-color:#ef4444 !important;background:#fff5f5 !important; }
    .sd-arrow { position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;transition:transform 0.2s; }
    .sd-wrap.open .sd-arrow { transform:translateY(-50%) rotate(180deg); }
    .sd-dropdown { position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.12);z-index:200;display:none;flex-direction:column;overflow:hidden; }
    .sd-wrap.open .sd-dropdown { display:flex; }
    .sd-search-box { padding:8px 10px 6px;border-bottom:1px solid #f3f4f6;flex-shrink:0;position:relative; }
    .sd-search { width:100%;background:#f3f4f6;border:1.5px solid transparent;border-radius:6px;padding:6px 10px 6px 28px;font-size:12px;color:#111827;outline:none;transition:border-color 0.15s; }
    .sd-search:focus { border-color:#2d5a1b;background:#fff; }
    .sd-search-icon { position:absolute;left:18px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
    .sd-list { overflow-y:auto;max-height:160px; }
    .sd-item { padding:8px 12px;font-size:13px;color:#374151;cursor:pointer;transition:background 0.1s;display:flex;align-items:center;justify-content:space-between; }
    .sd-item:hover { background:#f0fdf4;color:#1a3a1a; }
    .sd-item.selected { background:#dcfce7;color:#15803d;font-weight:600; }
    .sd-item.sd-hidden { display:none; }
    .sd-empty { padding:10px 12px;font-size:12px;color:#9ca3af;text-align:center;display:none; }
    .mode-add  .edit-only { display:none !important; }
    .mode-edit .add-only  { display:none !important; }
    .gov-id-input::-webkit-outer-spin-button,.gov-id-input::-webkit-inner-spin-button { -webkit-appearance:none;margin:0; }
    .gov-id-input { -moz-appearance:textfield;font-family:monospace;letter-spacing:0.04em; }
    .modal-footer { flex-shrink:0;padding:14px 24px;background:#fff;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between; }
    .btn-deact { padding:8px 16px;font-size:12px;font-weight:600;border:1.5px solid #fca5a5;border-radius:8px;color:#b91c1c;background:#fff;cursor:pointer;transition:all 0.15s; }
    .btn-deact:hover { background:#fee2e2; }
    .btn-activ { padding:8px 16px;font-size:12px;font-weight:600;border:1.5px solid #86efac;border-radius:8px;color:#15803d;background:#fff;cursor:pointer;transition:all 0.15s; }
    .btn-activ:hover { background:#dcfce7; }
    .btn-cancel { padding:8px 18px;font-size:12px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:8px;color:#6b7280;background:#fff;cursor:pointer;transition:all 0.15s; }
    .btn-cancel:hover { border-color:#9ca3af;color:#374151; }
    .btn-submit { padding:8px 22px;font-size:12px;font-weight:700;border:none;border-radius:8px;color:#fff;background:#1a3a1a;cursor:pointer;transition:background 0.15s; }
    .btn-submit:hover { background:#2d5a1b; }
    #confirmModal { position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);opacity:0;pointer-events:none;transition:opacity 0.2s ease; }
    #confirmModal.show { opacity:1;pointer-events:all; }
    .confirm-box { background:#fff;border-radius:16px;padding:36px 30px;max-width:400px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1); }
    #confirmModal.show .confirm-box { transform:scale(1); }
    #toast { position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;align-items:center;gap:12px;background:#fff;border:1.5px solid #bbf7d0;border-radius:14px;padding:14px 18px;box-shadow:0 8px 32px rgba(0,0,0,0.13);opacity:0;transform:translateY(8px);transition:all 0.3s ease;pointer-events:none;min-width:280px; }
    #toast.show { opacity:1;transform:translateY(0);pointer-events:all; }
    #toastIcon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    #noResultsRow { display:none; }
    body.panel-open { overflow:hidden; }

    /* ── EMPLOYEE TYPE TOGGLE ── */
    .emp-type-toggle { display:flex;gap:0;border-radius:8px;overflow:hidden;border:1.5px solid #e5e7eb; }
    .emp-type-btn { flex:1;padding:9px 12px;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;background:#f3f4f6;color:#6b7280; }
    .emp-type-btn:first-child { border-right:1px solid #e5e7eb; }
    .emp-type-btn.active-new { background:#dcfce7;color:#15803d; }
    .emp-type-btn.active-old { background:#dbeafe;color:#1d4ed8; }
    .emp-type-btn:not(.active-new):not(.active-old):hover { background:#f9fafb;color:#374151; }

    /* ── LEAVE PREVIEW CARD ── */
    #leavePreviewCard { display:none;margin-top:14px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:10px;padding:14px 16px;animation:fadeSlideIn 0.25s ease; }
    @keyframes fadeSlideIn { from { opacity:0;transform:translateY(-6px); } to { opacity:1;transform:translateY(0); } }
    .lp-header { display:flex;align-items:center;gap:8px;margin-bottom:12px; }
    .lp-header-icon { width:28px;height:28px;border-radius:7px;background:#dcfce7;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0; }
    .lp-title { font-size:12px;font-weight:700;color:#15803d; }
    .lp-subtitle { font-size:11px;color:#6b7280;margin-top:1px; }
    .lp-grid { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
    .lp-item { background:#fff;border-radius:8px;padding:10px 12px;border:1px solid #d1fae5; }
    .lp-type { font-size:10px;font-weight:800;color:#2d5a1b;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px; }
    .lp-name { font-size:10px;color:#6b7280;margin-bottom:6px; }
    .lp-balance { font-size:22px;font-weight:800;color:#15803d;line-height:1; }
    .lp-unit { font-size:10px;color:#9ca3af;margin-top:2px; }
    .lp-formula { font-size:10px;color:#6b7280;margin-top:8px;padding-top:8px;border-top:1px solid #d1fae5;font-family:monospace; }
    .lp-note { font-size:10px;color:#9ca3af;margin-top:4px; }
    .lp-nodate { background:#fff;border-radius:8px;padding:10px 12px;text-align:center;font-size:12px;color:#9ca3af;border:1px dashed #d1fae5; }

    /* ── OLD EMPLOYEE BALANCE CARD ── */
    #oldEmpBalanceCard { display:none;margin-top:14px;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:14px 16px;animation:fadeSlideIn 0.25s ease; }
    .ob-header { display:flex;align-items:center;gap:8px;margin-bottom:14px; }
    .ob-header-icon { width:28px;height:28px;border-radius:7px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#1d4ed8;flex-shrink:0; }
    .ob-title { font-size:12px;font-weight:700;color:#1d4ed8; }
    .ob-subtitle { font-size:11px;color:#6b7280;margin-top:1px; }
    .ob-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
    .ob-field { background:#fff;border-radius:8px;padding:12px 14px;border:1px solid #bfdbfe; }
    .ob-field-top { display:flex;align-items:center;gap:8px;margin-bottom:8px; }
    .ob-type-badge { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;font-size:11px;font-weight:800;letter-spacing:0.04em; }
    .ob-type-badge.vl { background:#dcfce7;color:#15803d; }
    .ob-type-badge.sl { background:#fee2e2;color:#b91c1c; }
    .ob-type-name { font-size:11px;font-weight:700;color:#374151; }
    .ob-type-sub  { font-size:10px;color:#9ca3af; }
    .ob-input-wrap { position:relative; }
    .ob-input { width:100%;background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:7px;padding:8px 40px 8px 10px;font-size:14px;font-weight:700;color:#111827;outline:none;transition:border-color 0.15s,background 0.15s;font-family:monospace; }
    .ob-input:focus { background:#fff;border-color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,0.08); }
    .ob-input.field-error { border-color:#ef4444 !important;background:#fff5f5 !important; }
    .ob-unit { position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:10px;color:#9ca3af;pointer-events:none;font-family:monospace; }
    .ob-hint { font-size:10px;color:#6b7280;margin-top:5px; }
    .ob-note { font-size:10px;color:#6b7280;margin-top:10px;padding-top:10px;border-top:1px solid #bfdbfe;display:flex;align-items:flex-start;gap:6px; }
    .ob-note svg { flex-shrink:0;margin-top:1px; }

    /* ══ VIEW PANEL SPECIFIC ══ */
    #viewPanel .modal-box { background:#fff;width:100%;height:100%;display:flex;flex-direction:column;box-shadow:-12px 0 60px rgba(0,0,0,0.22);overflow:hidden; }
    .vp-header { background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);flex-shrink:0; }
    .vp-header-top { display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px 16px; }
    .vp-avatar { width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0;margin-right:14px;letter-spacing:-0.5px; }
    .vp-header-info { flex:1;min-width:0; }
    .vp-header-info h2 { font-size:18px;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-0.02em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .vp-header-info p { font-size:12px;color:rgba(255,255,255,0.65);margin:0; }
    .vp-badge-active   { display:inline-flex;align-items:center;gap:4px;background:rgba(74,222,128,0.2);color:#bbf7d0;border:1px solid rgba(74,222,128,0.3);border-radius:999px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px; }
    .vp-badge-inactive { display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.3);border-radius:999px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px; }
    .vp-tabs { display:flex;padding:0 24px;gap:2px;border-top:1px solid rgba(255,255,255,0.1);overflow-x:auto;scrollbar-width:none; }
    .vp-tabs::-webkit-scrollbar { display:none; }
    .vp-tab { padding:10px 16px;font-size:12px;font-weight:600;color:rgba(255,255,255,0.55);background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;transition:color 0.15s,border-color 0.15s;display:flex;align-items:center;gap:6px; }
    .vp-tab:hover { color:rgba(255,255,255,0.85); }
    .vp-tab.active { color:#fff;border-bottom-color:#86efac; }
    .vp-body { flex:1;overflow-y:auto;background:#f4f6f3;scrollbar-width:thin;scrollbar-color:#d1d5db transparent; }
    .vp-body::-webkit-scrollbar { width:4px; }
    .vp-body::-webkit-scrollbar-thumb { background:#d1d5db;border-radius:99px; }
    .vp-pane { display:none;padding:20px; }
    .vp-pane.active { display:block; }
    .vp-card { background:#fff;border-radius:12px;padding:18px 20px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,0.05); }
    .vp-card-title { font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 14px;display:flex;align-items:center;gap:7px; }
    .vp-card-title-icon { width:26px;height:26px;border-radius:7px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#2d5a1b;flex-shrink:0; }
    .vp-info-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px 20px; }
    .vp-info-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    .vp-info-grid.cols-1 { grid-template-columns:1fr; }
    .vp-field-label { font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px; }
    .vp-field-value { font-size:13px;color:#111827;font-weight:500;word-break:break-word; }
    .vp-field-value.mono { font-family:monospace;letter-spacing:0.04em; }
    .lb-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px;margin-bottom:14px; }
    .lb-card { background:#fff;border-radius:12px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,0.05);border-left:3px solid #2d5a1b;position:relative;overflow:hidden; }
    .lb-type-code { font-size:10px;font-weight:800;color:#2d5a1b;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px; }
    .lb-type-name { font-size:11px;color:#6b7280;margin-bottom:10px;line-height:1.3; }
    .lb-balance { font-size:26px;font-weight:800;color:#111827;line-height:1;margin-bottom:2px; }
    .lb-balance-label { font-size:10px;color:#9ca3af; }
    .lb-stats { display:flex;gap:12px;margin-top:10px;padding-top:10px;border-top:1px solid #f3f4f6; }
    .lb-stat { flex:1; }
    .lb-stat-val { font-size:12px;font-weight:700;color:#374151; }
    .lb-stat-lbl { font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em; }
    .vp-table-wrap { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.05);margin-bottom:14px; }
    .vp-table { width:100%;border-collapse:collapse;font-size:11.5px; }
    .vp-table thead tr { background:#fafafa;border-bottom:1px solid #f3f4f6; }
    .vp-table thead th { padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
    .vp-table tbody tr { border-bottom:1px solid #f9fafb;transition:background 0.1s; }
    .vp-table tbody tr:last-child { border-bottom:none; }
    .vp-table tbody tr:hover { background:#f9fafb; }
    .vp-table tbody td { padding:10px 14px;color:#374151;vertical-align:middle; }
    .st { display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700; }
    .st-pending   { background:#fef9c3;color:#a16207; }
    .st-approved  { background:#dcfce7;color:#15803d; }
    .st-rejected  { background:#fee2e2;color:#b91c1c; }
    .st-cancelled { background:#f3f4f6;color:#6b7280; }
    .st-received  { background:#dbeafe;color:#1d4ed8; }
    .st-onprocess { background:#ede9fe;color:#6d28d9; }
    .vp-empty { text-align:center;padding:36px 20px;color:#9ca3af;font-size:13px; }
    .vp-empty svg { margin:0 auto 10px;display:block;opacity:0.35; }
    .pay-summary { display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px; }
    .pay-summary-card { background:#fff;border-radius:12px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,0.05);text-align:center; }
    .pay-summary-label { font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px; }
    .pay-summary-val   { font-size:18px;font-weight:800;color:#111827; }
    .pay-summary-val.green { color:#16a34a; }
    .pay-summary-val.red   { color:#dc2626; }
    .vp-loader { display:flex;align-items:center;justify-content:center;padding:60px 20px;flex-direction:column;gap:12px;color:#9ca3af;font-size:13px; }
    .spinner { width:32px;height:32px;border:3px solid #f3f4f6;border-top-color:#2d5a1b;border-radius:50%;animation:spin 0.7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .vp-footer { flex-shrink:0;padding:14px 24px;background:#fff;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:flex-end;gap:10px; }

    /* ── RESPONSIVE ── */
    @media (max-width:640px) {
        .vp-header-top { padding:14px 14px 12px; }
        .vp-avatar { width:38px;height:38px;font-size:14px;margin-right:10px; }
        .vp-header-info h2 { font-size:14px; }
        .vp-tabs { padding:0 8px; }
        .vp-tab { padding:8px 8px;font-size:10px;gap:3px; }
        .vp-tab svg { width:11px;height:11px; }
        .vp-pane { padding:12px 10px; }
        .vp-card { padding:12px 12px; }
        .vp-footer { padding:10px 14px; }
        .vp-info-grid, .vp-info-grid.cols-3 { grid-template-columns:1fr 1fr !important; }
        .pay-summary { grid-template-columns:1fr !important; }
        .modal-header { padding:14px 16px; }
        .modal-footer { padding:10px 14px;flex-wrap:wrap;gap:8px; }
        .modal-footer > div:last-child { width:100%;justify-content:flex-end; }
        .form-section-card { margin:10px 10px;padding:14px 12px; }
        #empPanel .grid.grid-cols-2 { grid-template-columns:1fr !important; }
        #empPanel .col-span-2 { grid-column:1 !important; }
        .ob-grid { grid-template-columns:1fr !important; }
        .lp-grid { grid-template-columns:1fr !important; }
        #toast { right:10px;left:10px;min-width:0; }
        .table-card { max-height:none; }

        /* ── Mobile: Status badge fix ── */
        .badge { padding:2px 6px;font-size:10px;gap:2px; }
        #employeeTable td:nth-child(7),
        #employeeTable th:nth-child(7) { width:1%;padding-left:6px;padding-right:6px; }
    }
</style>

{{-- ── TABLE CARD ── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden table-card">
    <div class="table-card-header px-6 py-4" style="border-bottom:1px solid #f3f4f6;">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="font-bold text-gray-800 text-base">Employee</p>
            <p class="text-xs text-gray-400 mt-0.5">List of All Employees &mdash; <span id="resultCount"></span></p>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="relative flex-1">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input id="searchInput" type="text" placeholder="Search employees..." class="w-full pl-9 pr-9 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700" oninput="filterTable()" autocomplete="off">
            <button id="clearSearch" onclick="clearSearchInput()" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" style="background:none;border:none;cursor:pointer;padding:2px;display:none;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <button onclick="openAddPanel()" class="flex items-center justify-center gap-2 px-4 py-2 text-sm text-white font-semibold rounded-lg transition" style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add new Employee
        </button>
    </div>
</div>

    <div class="table-scroll">
        <table class="w-full text-sm" id="employeeTable">
            <thead>
                <tr style="border-bottom:1px solid #f3f4f6;background:#fafafa;">
                    <th class="text-left px-6 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Employee ID<button class="sort-btn" onclick="sortTable(0)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Name<button class="sort-btn" onclick="sortTable(1)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Hired Date<button class="sort-btn" onclick="sortTable(2)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Department<button class="sort-btn" onclick="sortTable(3)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Position<button class="sort-btn" onclick="sortTable(4)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500"><div class="flex items-center gap-1">Salary<button class="sort-btn" onclick="sortTable(5)"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button></div></th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500">Status</th>
                    <th class="text-right px-6 py-2.5 text-xs font-semibold text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody id="empTbody">
                @forelse($employees as $emp)
                <tr style="border-bottom:1px solid #f9fafb;cursor:pointer;" class="emp-row" data-userid="{{ $emp->user_id }}" onclick="openViewPanel({{ $emp->user_id }})">
                    <td class="px-6 py-2.5 font-bold text-gray-800 font-mono">{{ $emp->formatted_employee_id }}</td>
                    <td class="px-4 py-2.5 text-gray-600">
                        {{ $emp->last_name }}, {{ $emp->first_name }}
                        {{ $emp->middle_name ? strtoupper(substr($emp->middle_name,0,1)).'.' : '' }}
                        {{ $emp->extension_name ?? '' }}
                    </td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('M d, Y') : '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $emp->department->department_name ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $emp->position->position_name ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500">₱{{ number_format($emp->salary,2) }}</td>
                    <td class="px-4 py-2.5">
                        @if($emp->is_active)
                            <span class="badge badge-active">● Active</span>
                        @else
                            <span class="badge badge-inactive">● Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-2.5 text-right" style="white-space:nowrap;">
                        <button class="action-btn" onclick="event.stopPropagation(); openViewPanel({{ $emp->user_id }})" title="View Details" style="margin-right:4px;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button class="action-btn" onclick="event.stopPropagation(); openEditPanel({{ $emp->user_id }})" title="Edit Employee">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No employees found.</td></tr>
                @endforelse
                <tr id="noResultsRow" style="display:none;">
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No employees match your search.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="table-card-footer px-6 py-4 flex items-center justify-between border-t border-gray-50">
        <p class="text-xs text-gray-400" id="footerCount"></p>
    </div>
</div>

{{-- ── OVERLAY ── --}}
<div id="overlay" onclick="closePanel(); closeViewPanel();"></div>

{{-- ════ EDIT PANEL ════ --}}
<div id="empPanel">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h2 id="panelTitle">Add New Employee</h2>
                <p id="panelSubtitle">Fill in all required fields</p>
            </div>
            <button class="modal-close" onclick="closePanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="empForm" autocomplete="off">
                @csrf

                {{-- ── Personal Information ── --}}
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <p class="section-title">Personal Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <label class="field-label">First Name <span class="req">*</span></label>
                            <input type="text" id="f_first_name" name="first_name" class="form-field auto-caps" placeholder="First Name" autocomplete="off" data-lpignore="true" data-form-type="other">
                            <p class="error-msg" id="err_first_name">First name is required.</p>
                        </div>
                        <div>
                            <label class="field-label">Middle Name</label>
                            <input type="text" id="f_middle_name" name="middle_name" class="form-field auto-caps" placeholder="Middle Name" autocomplete="off" data-lpignore="true" data-form-type="other">
                        </div>
                        <div>
                            <label class="field-label">Last Name <span class="req">*</span></label>
                            <input type="text" id="f_last_name" name="last_name" class="form-field auto-caps" placeholder="Last Name" autocomplete="off" data-lpignore="true" data-form-type="other" oninput="autoFillPassword()">
                            <p class="error-msg" id="err_last_name">Last name is required.</p>
                        </div>
                        <div>
                            <label class="field-label">Extension Name</label>
                            <select id="f_extension_name" name="extension_name" class="form-field">
                                <option value="">None</option>
                                <option value="Jr.">Jr.</option><option value="Sr.">Sr.</option>
                                <option value="II">II</option><option value="III">III</option>
                                <option value="IV">IV</option><option value="V">V</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Birthday</label>
                            <input type="date" id="f_birthday" name="birthday" class="form-field" autocomplete="off">
                        </div>
                        <div>
                            <label class="field-label">Address</label>
                            <input type="text" id="f_address" name="address" class="form-field auto-caps" placeholder="Permanent Address" autocomplete="off" data-lpignore="true" data-form-type="other">
                        </div>
                        <div class="col-span-2">
                            <label class="field-label">Phone Number</label>
                            <div class="phone-wrap">
                                <div class="flag-select">🇵🇭 &nbsp;+63</div>
                                <input type="text" id="f_contact_number" name="contact_number" class="phone-input" placeholder="9XX XXX XXXX" autocomplete="off" inputmode="numeric">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Employment Information ── --}}
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                        <p class="section-title">Employment Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">

                        {{-- Employee Type Toggle (Add mode only) --}}
                        <div class="col-span-2 add-only">
                            <label class="field-label">Employee Type <span class="req">*</span></label>
                            <div class="emp-type-toggle">
                                <button type="button" id="btnEmpTypeNew" class="emp-type-btn" onclick="setEmpType('new')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                    New Employee
                                </button>
                                <button type="button" id="btnEmpTypeOld" class="emp-type-btn" onclick="setEmpType('old')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Old / Existing Employee
                                </button>
                            </div>
                            <input type="hidden" id="f_is_new_employee" name="is_new_employee" value="">
                            <p class="error-msg" id="err_emp_type">Please select an employee type.</p>
                            <p class="id-hint" style="margin-top:4px;">
                                <span id="empTypeHint" style="color:#6b7280;">Select whether this is a newly hired employee or an existing/transferred employee.</span>
                            </p>
                        </div>

                        {{-- Employee ID --}}
                        <div>
                            <label class="field-label">Employee ID <span class="req">*</span></label>
                            <input
                                type="text"
                                id="f_employee_id_display"
                                class="form-field gov-id-input"
                                placeholder="000-0000"
                                maxlength="8"
                                oninput="formatEmployeeId(this)"
                                autocomplete="off"
                                data-lpignore="true">
                            <input type="hidden" id="f_employee_id_raw" name="employee_id">
                            <p class="error-msg" id="err_employee_id_display">Valid 7-digit ID required (e.g. 001-0001).</p>
                            <p class="id-hint">Numbers only · auto-formats as 000-0000</p>
                        </div>

                        <div>
                            <label class="field-label">Hired Date <span class="req">*</span></label>
                            <input type="date" id="f_hire_date" name="hire_date" class="form-field" autocomplete="off" oninput="onHireDateChange()">
                            <p class="error-msg" id="err_hire_date">Hire date is required.</p>
                        </div>
                        <div>
                            <label class="field-label">Department <span class="req">*</span></label>
                            <input type="hidden" id="f_department_id" name="department_id">
                            <div class="sd-wrap" id="sdw_dept">
                                <button type="button" id="sdt_dept" class="sd-trigger placeholder-shown" onclick="sdToggle('dept')">Select Department</button>
                                <svg class="sd-arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <div class="sd-dropdown" id="sdd_dept">
                                    <div class="sd-search-box">
                                        <svg class="sd-search-icon w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                                        <input type="text" id="sds_dept" class="sd-search" placeholder="Search…" autocomplete="off" oninput="sdFilter('dept', this.value)">
                                    </div>
                                    <div class="sd-list" id="sdl_dept">
                                        @foreach($departments as $dept)
                                        <div class="sd-item" data-val="{{ $dept->department_id }}" data-lbl="{{ $dept->department_name }}" onclick="sdSelect('dept', '{{ $dept->department_id }}', '{{ addslashes($dept->department_name) }}')">
                                            <span>{{ $dept->department_name }}</span>
                                            <svg class="sd-chk w-3.5 h-3.5 text-green-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="sd-empty" id="sde_dept">No departments found.</div>
                                </div>
                            </div>
                            <p class="error-msg" id="err_department_id">Please select a department.</p>
                        </div>
                        <div>
                            <label class="field-label">Position <span class="req">*</span></label>
                            <input type="hidden" id="f_position_id" name="position_id">
                            <div class="sd-wrap" id="sdw_pos">
                                <button type="button" id="sdt_pos" class="sd-trigger placeholder-shown" onclick="sdToggle('pos')">Select Position</button>
                                <svg class="sd-arrow w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <div class="sd-dropdown" id="sdd_pos">
                                    <div class="sd-search-box">
                                        <svg class="sd-search-icon w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                                        <input type="text" id="sds_pos" class="sd-search" placeholder="Search…" autocomplete="off" oninput="sdFilter('pos', this.value)">
                                    </div>
                                    <div class="sd-list" id="sdl_pos">
                                        @foreach($positions as $pos)
                                        <div class="sd-item" data-val="{{ $pos->position_id }}" data-lbl="{{ $pos->position_name }}" onclick="sdSelect('pos', '{{ $pos->position_id }}', '{{ addslashes($pos->position_name) }}')">
                                            <span>{{ $pos->position_name }}</span>
                                            <svg class="sd-chk w-3.5 h-3.5 text-green-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="sd-empty" id="sde_pos">No positions found.</div>
                                </div>
                            </div>
                            <p class="error-msg" id="err_position_id">Please select a position.</p>
                        </div>
                        <div>
                            <label class="field-label">Salary <span class="req">*</span></label>
                            <input type="hidden" id="f_salary" name="salary">
                            <div class="salary-wrap">
                                <span class="salary-prefix">₱</span>
                                <input type="text" id="f_salary_display" class="form-field salary-input" placeholder="0.00" oninput="formatSalary(this)" onblur="finalizeSalary(this)" autocomplete="off" data-lpignore="true">
                            </div>
                            <p class="error-msg" id="err_salary">Salary is required.</p>
                        </div>
                        <div>
                            <label class="field-label">User Access</label>
                            <select id="f_user_access" name="user_access" class="form-field">
                                @foreach($roleOptions as $role)
                                <option value="{{ $role->label }}">{{ ucfirst($role->label) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ── NEW EMPLOYEE: Leave Balance Auto-Preview ── --}}
                        <div class="col-span-2 add-only" id="leavePreviewWrap">
                            <div id="leavePreviewCard">
                                <div class="lp-header">
                                    <div class="lp-header-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="lp-title">Initial Leave Balance (Auto-Computed)</p>
                                        <p class="lp-subtitle" id="lpSubtitle">Based on hire date · Accrual rate: 1.25 days/month</p>
                                    </div>
                                </div>
                                <div id="lpContent">
                                    <div class="lp-nodate">Enter a hire date above to compute initial leave balances.</div>
                                </div>
                                <input type="hidden" id="f_vl_balance" name="vl_balance" value="0">
                                <input type="hidden" id="f_sl_balance" name="sl_balance" value="0">
                            </div>
                        </div>

                        {{-- ── OLD EMPLOYEE: Manual Leave Balance Entry ── --}}
                        <div class="col-span-2 add-only" id="oldEmpBalanceWrap">
                            <div id="oldEmpBalanceCard">
                                <div class="ob-header">
                                    <div class="ob-header-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    </div>
                                    <div>
                                        <p class="ob-title">Current Leave Balances</p>
                                        <p class="ob-subtitle">Enter the employee's existing VL &amp; SL balances — saved directly to <code style="font-size:10px;background:#dbeafe;padding:1px 4px;border-radius:3px;">leave_credit_balance</code>.</p>
                                    </div>
                                </div>
                                <div class="ob-grid">
                                    <div class="ob-field">
                                        <div class="ob-field-top">
                                            <span class="ob-type-badge vl">VL</span>
                                            <div>
                                                <p class="ob-type-name">Vacation Leave</p>
                                                <p class="ob-type-sub">leave_credit_balance · leave_type_id = 1</p>
                                            </div>
                                        </div>
                                        <div class="ob-input-wrap">
                                            <input type="text"
                                                   id="f_vl_current_display"
                                                   class="ob-input"
                                                   placeholder="0.00"
                                                   inputmode="decimal"
                                                   autocomplete="off"
                                                   oninput="sanitizeBalanceInput(this)"
                                                   onblur="finalizeBalanceInput(this, 'f_vl_balance')">
                                            <span class="ob-unit">days</span>
                                        </div>
                                        <p class="ob-hint">Saved as <code style="font-size:10px;background:#eff6ff;padding:1px 4px;border-radius:3px;">remaining_balance</code> &amp; <code style="font-size:10px;background:#eff6ff;padding:1px 4px;border-radius:3px;">total_accrued</code></p>
                                    </div>
                                    <div class="ob-field">
                                        <div class="ob-field-top">
                                            <span class="ob-type-badge sl">SL</span>
                                            <div>
                                                <p class="ob-type-name">Sick Leave</p>
                                                <p class="ob-type-sub">leave_credit_balance · leave_type_id = 2</p>
                                            </div>
                                        </div>
                                        <div class="ob-input-wrap">
                                            <input type="text"
                                                   id="f_sl_current_display"
                                                   class="ob-input"
                                                   placeholder="0.00"
                                                   inputmode="decimal"
                                                   autocomplete="off"
                                                   oninput="sanitizeBalanceInput(this)"
                                                   onblur="finalizeBalanceInput(this, 'f_sl_balance')">
                                            <span class="ob-unit">days</span>
                                        </div>
                                        <p class="ob-hint">Saved as <code style="font-size:10px;background:#eff6ff;padding:1px 4px;border-radius:3px;">remaining_balance</code> &amp; <code style="font-size:10px;background:#eff6ff;padding:1px 4px;border-radius:3px;">total_accrued</code></p>
                                    </div>
                                </div>
                                <div class="ob-note">
                                    <svg class="w-3.5 h-3.5" style="color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Both VL and SL are inserted into <strong>leave_credit_balance</strong> for the current year with <code style="font-size:10px;background:#dbeafe;padding:1px 4px;border-radius:3px;">total_used = 0</code>. Leave blank or enter <strong>0</strong> if not applicable.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── Government IDs ── --}}
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg></div>
                        <p class="section-title">Government IDs</p>
                    </div>
                    <div class="grid grid-cols-1 gap-y-4">
                        <div>
                            <label class="field-label">Pag-IBIG ID</label>
                            <input type="text" id="f_pagibig_id" name="pagibig_id" class="form-field gov-id-input" placeholder="Enter Pag-IBIG ID number" inputmode="numeric" autocomplete="off" data-lpignore="true" oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>
                        <div>
                            <label class="field-label">GSIS ID</label>
                            <input type="text" id="f_gsis_id" name="gsis_id" class="form-field gov-id-input" placeholder="Enter GSIS ID number" inputmode="numeric" autocomplete="off" data-lpignore="true" oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>
                        <div>
                            <label class="field-label">PhilHealth ID</label>
                            <input type="text" id="f_philhealth_id" name="philhealth_id" class="form-field gov-id-input" placeholder="Enter PhilHealth ID number" inputmode="numeric" autocomplete="off" data-lpignore="true" oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>
                        <div>
                            <label class="field-label">TIN</label>
                            <input type="text" id="f_tin" name="tin" class="form-field gov-id-input" placeholder="Enter TIN (e.g. 123-456-789-000)" inputmode="numeric" autocomplete="off" data-lpignore="true" oninput="formatTin(this)">
                        </div>
                    </div>
                </div>

                {{-- ── Account Credentials ── --}}
                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>
                        <p class="section-title">Account Credentials</p>
                    </div>
                    <div class="grid grid-cols-1 gap-y-4">
                        <div>
                            <label class="field-label">Username / ID <span class="req add-only">*</span></label>
                            <input type="text" id="f_username" name="username" class="form-field" placeholder="Auto-filled from Employee ID" autocomplete="off" data-lpignore="true" data-form-type="other"
                                   oninput="this.dataset.manuallyEdited = this.value ? '1' : ''">
                            <p class="error-msg" id="err_username">Username is required.</p>
                            <p class="id-hint add-only" style="margin-top:4px;">Auto-filled as Employee ID · type to override</p>
                            <p class="id-hint edit-only" style="margin-top:4px;">Leave blank to keep current username.</p>
                        </div>
                        <div>
                            <label class="field-label">Password <span class="req add-only">*</span></label>
                            <div class="pw-wrap">
                                <input type="password" id="f_password" name="password" class="form-field" style="padding-right:40px;" placeholder="Enter password" autocomplete="new-password"
                                       oninput="this.dataset.manuallyEdited = this.value ? '1' : ''">
                                <button type="button" class="pw-toggle" onclick="togglePw()">
                                    <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <p class="error-msg" id="err_password">Password is required (min 6 characters).</p>
                            <p class="id-hint add-only" style="margin-top:4px;">Auto-filled as: <span id="pwHintText" class="font-mono text-green-700">—</span></p>
                            <p class="id-hint edit-only" style="margin-top:4px;">Leave blank to keep current password.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="edit-only">
                <button id="toggleStatusBtn" onclick="confirmToggleStatus()" class="btn-deact">Deactivate</button>
            </div>
            <div class="add-only"></div>
            <div style="display:flex;align-items:center;gap:10px;">
                <button onclick="closePanel()" class="btn-cancel">Cancel</button>
                <button id="submitBtn" onclick="submitForm()" class="btn-submit">Save Employee</button>
            </div>
        </div>
    </div>
</div>

{{-- ════ VIEW PANEL ════ --}}
<div id="viewPanel">
    <div class="modal-box">
        <div class="vp-header">
            <div class="vp-header-top">
                <div class="vp-avatar" id="vpAvatar">?</div>
                <div class="vp-header-info">
                    <h2 id="vpName">Loading…</h2>
                    <p id="vpMeta"></p>
                    <span id="vpStatusBadge"></span>
                </div>
                <button class="modal-close" onclick="closeViewPanel()" style="margin-left:12px;flex-shrink:0;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="vp-tabs">
                <button class="vp-tab active" onclick="switchVpTab('personal')" id="vptab-personal">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Personal Info
                </button>
                <button class="vp-tab" onclick="switchVpTab('leave')" id="vptab-leave">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Leave Balances
                </button>
                <button class="vp-tab" onclick="switchVpTab('history')" id="vptab-history">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Leave History
                </button>
                <button class="vp-tab" onclick="switchVpTab('payroll')" id="vptab-payroll">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Payroll
                </button>
                <button class="vp-tab" onclick="switchVpTab('govids')" id="vptab-govids">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                    Gov't IDs
                </button>
            </div>
        </div>

        <div class="vp-body" id="vpBody">
            <div class="vp-loader" id="vpLoader">
                <div class="spinner"></div>
                Loading employee details…
            </div>

            {{-- Personal Tab --}}
            <div class="vp-pane" id="vpPane-personal">
                <div class="vp-card">
                    <p class="vp-card-title"><span class="vp-card-title-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>Employment Information</p>
                    <div class="vp-info-grid cols-3">
                        <div><p class="vp-field-label">Employee ID</p><p class="vp-field-value mono" id="vd_id">—</p></div>
                        <div><p class="vp-field-label">Department</p><p class="vp-field-value" id="vd_dept">—</p></div>
                        <div><p class="vp-field-label">Position</p><p class="vp-field-value" id="vd_pos">—</p></div>
                        <div><p class="vp-field-label">Hire Date</p><p class="vp-field-value" id="vd_hire">—</p></div>
                        <div><p class="vp-field-label">Monthly Salary</p><p class="vp-field-value" id="vd_salary">—</p></div>
                        <div><p class="vp-field-label">User Access</p><p class="vp-field-value" id="vd_access">—</p></div>
                        <div><p class="vp-field-label">Username</p><p class="vp-field-value mono" id="vd_username">—</p></div>
                    </div>
                </div>
                <div class="vp-card">
                    <p class="vp-card-title"><span class="vp-card-title-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>Personal Information</p>
                    <div class="vp-info-grid">
                        <div><p class="vp-field-label">First Name</p><p class="vp-field-value" id="vd_fname">—</p></div>
                        <div><p class="vp-field-label">Middle Name</p><p class="vp-field-value" id="vd_mname">—</p></div>
                        <div><p class="vp-field-label">Last Name</p><p class="vp-field-value" id="vd_lname">—</p></div>
                        <div><p class="vp-field-label">Extension</p><p class="vp-field-value" id="vd_ext">—</p></div>
                        <div><p class="vp-field-label">Birthday</p><p class="vp-field-value" id="vd_bday">—</p></div>
                        <div><p class="vp-field-label">Contact Number</p><p class="vp-field-value" id="vd_contact">—</p></div>
                        <div style="grid-column:1/-1"><p class="vp-field-label">Address</p><p class="vp-field-value" id="vd_address">—</p></div>
                    </div>
                </div>
            </div>

            {{-- Leave Balances Tab --}}
            <div class="vp-pane" id="vpPane-leave">
                <div id="vpLeaveBalances">
                    <div class="vp-empty"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>No leave balance records found.</div>
                </div>
            </div>

            {{-- Leave History Tab --}}
            <div class="vp-pane" id="vpPane-history">
                <div class="vp-table-wrap">
                    <table class="vp-table">
                        <thead><tr><th>Type</th><th>Applied</th><th>Period</th><th>Days</th><th>Status</th><th>Note</th></tr></thead>
                        <tbody id="vpLeaveHistoryBody"><tr><td colspan="6" class="vp-empty">Loading…</td></tr></tbody>
                    </table>
                </div>
                <div class="vp-card" style="margin-top:0;">
                    <p class="vp-card-title"><span class="vp-card-title-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>Half-Day Applications</p>
                    <div class="vp-table-wrap" style="margin:0;box-shadow:none;border-radius:8px;border:1px solid #f3f4f6;">
                        <table class="vp-table">
                            <thead><tr><th>Type</th><th>Date</th><th>Period</th><th>Status</th></tr></thead>
                            <tbody id="vpHalfDayBody"><tr><td colspan="4" class="vp-empty">Loading…</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Payroll Tab --}}
            <div class="vp-pane" id="vpPane-payroll">
                <div class="pay-summary" id="vpPaySummary" style="display:none;">
                    <div class="pay-summary-card"><p class="pay-summary-label">Gross Salary</p><p class="pay-summary-val" id="vpPayGross">—</p></div>
                    <div class="pay-summary-card"><p class="pay-summary-label">Total Deductions</p><p class="pay-summary-val red" id="vpPayDed">—</p></div>
                    <div class="pay-summary-card"><p class="pay-summary-label">Net Pay</p><p class="pay-summary-val green" id="vpPayNet">—</p></div>
                </div>
                <div class="vp-table-wrap">
                    <table class="vp-table">
                        <thead><tr><th>Period</th><th>Gross</th><th>GSIS EE</th><th>Pag-IBIG EE</th><th>PhilHealth EE</th><th>Tax</th><th>Deductions</th><th>Net Pay</th></tr></thead>
                        <tbody id="vpPayrollBody"><tr><td colspan="8" class="vp-empty">Loading…</td></tr></tbody>
                    </table>
                </div>
                <div class="vp-card" id="vpPayBreakdown" style="display:none;">
                    <p class="vp-card-title"><span class="vp-card-title-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></span>Latest Period — Full Breakdown</p>
                    <div class="vp-info-grid cols-3" id="vpPayBreakdownGrid"></div>
                </div>
            </div>

            {{-- Gov IDs Tab --}}
            <div class="vp-pane" id="vpPane-govids">
                <div class="vp-card">
                    <p class="vp-card-title"><span class="vp-card-title-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg></span>Government ID Numbers</p>
                    <div class="vp-info-grid cols-1" style="gap:16px;">
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f9fafb;border-radius:10px;border:1px solid #f3f4f6;">
                            <div style="width:40px;height:40px;border-radius:10px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg class="w-5 h-5" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <div><p class="vp-field-label">Pag-IBIG ID</p><p class="vp-field-value mono" id="vd_pagibig">—</p></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f9fafb;border-radius:10px;border:1px solid #f3f4f6;">
                            <div style="width:40px;height:40px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg class="w-5 h-5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                            <div><p class="vp-field-label">GSIS ID</p><p class="vp-field-value mono" id="vd_gsis">—</p></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f9fafb;border-radius:10px;border:1px solid #f3f4f6;">
                            <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            <div><p class="vp-field-label">PhilHealth ID</p><p class="vp-field-value mono" id="vd_philhealth">—</p></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f9fafb;border-radius:10px;border:1px solid #f3f4f6;">
                        <div style="width:40px;height:40px;border-radius:10px;background:#faf5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                        <div><p class="vp-field-label">TIN</p><p class="vp-field-value mono" id="vd_tin">—</p></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="vp-footer">
            <button onclick="closeViewPanel()" class="btn-cancel">Close</button>
            <button onclick="openEditFromView()" class="btn-submit" style="background:#1a3a1a;">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Employee
            </button>
        </div>
    </div>
</div>

{{-- ════ CONFIRM MODAL ════ --}}
<div id="confirmModal">
    <div class="confirm-box">
        <div id="confirmIconWrap" class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"></div>
        <h3 id="confirmTitle" class="text-lg font-bold text-gray-800 mb-2"></h3>
        <p id="confirmMsg" class="text-sm text-gray-500 mb-6 leading-relaxed"></p>
        <div class="flex gap-3">
            <button onclick="closeConfirm()" class="btn-cancel flex-1 py-3">Cancel</button>
            <button id="confirmOkBtn" class="btn-submit flex-1 py-3"></button>
        </div>
    </div>
</div>

{{-- ── TOAST ── --}}
<div id="toast">
    <div id="toastIcon"></div>
    <div>
        <p class="text-sm font-bold text-gray-800" id="toastTitle">Done!</p>
        <p class="text-xs text-gray-500 mt-0.5" id="toastMsg"></p>
    </div>
</div>

<script>
const EMPLOYEES_DATA  = @json($employeesJson);
const STORE_URL       = "{{ route('employees.store') }}";
const UPDATE_URL      = "{{ url('employees') }}";
const SHOW_URL        = "{{ url('employees') }}";
const CSRF            = "{{ csrf_token() }}";
const TOTAL_EMPLOYEES = {{ $employees instanceof \Illuminate\Support\Collection ? $employees->count() : $employees->total() }};
const ROLE_OPTIONS    = @json($roleOptions->pluck('label'));
</script>

<script>
/* ════════════════════════════════════════════════
   EMPLOYEE TYPE TOGGLE
   Sets is_new_employee (1=new, 0=old) and shows/hides
   the appropriate leave balance UI section.
════════════════════════════════════════════════ */
let currentEmpType = ''; // 'new' | 'old' | ''

function setEmpType(type) {
    currentEmpType = type;
    document.getElementById('f_is_new_employee').value = type === 'new' ? '1' : '0';

    const btnNew = document.getElementById('btnEmpTypeNew');
    const btnOld = document.getElementById('btnEmpTypeOld');
    const hint   = document.getElementById('empTypeHint');
    const errEl  = document.getElementById('err_emp_type');

    btnNew.classList.remove('active-new', 'active-old');
    btnOld.classList.remove('active-new', 'active-old');
    errEl.classList.remove('show');

    if (type === 'new') {
        btnNew.classList.add('active-new');
        hint.textContent = '✓ New hire — initial VL & SL will be auto-computed from the hire date.';
        hint.style.color = '#15803d';
        // Show auto-preview card, hide manual entry card
        document.getElementById('leavePreviewCard').style.display  = 'block';
        document.getElementById('oldEmpBalanceCard').style.display = 'none';
        // Reset old-employee display inputs
        const vlDisp = document.getElementById('f_vl_current_display');
        const slDisp = document.getElementById('f_sl_current_display');
        if (vlDisp) vlDisp.value = '';
        if (slDisp) slDisp.value = '';
        // Recompute from hire date
        computeLeaveBalances();
    } else {
        btnOld.classList.add('active-old');
        hint.textContent = '✓ Existing/transferred employee — enter the current leave balances below.';
        hint.style.color = '#1d4ed8';
        // Show manual entry card, hide auto-preview
        document.getElementById('leavePreviewCard').style.display  = 'none';
        document.getElementById('oldEmpBalanceCard').style.display = 'block';
        // Reset the hidden balance fields; user will fill manually
        document.getElementById('f_vl_balance').value = '0';
        document.getElementById('f_sl_balance').value = '0';
    }
}

/* ════════════════════════════════════════════════
   OLD EMPLOYEE – BALANCE INPUT HELPERS
   sanitizeBalanceInput: live cleanup while typing
   finalizeBalanceInput: on blur, writes the
     formatted display value AND syncs the hidden
     field (f_vl_balance / f_sl_balance) that gets
     submitted to the backend.
════════════════════════════════════════════════ */
function sanitizeBalanceInput(input) {
    let v = input.value.replace(/[^0-9.]/g, '');
    const parts = v.split('.');
    if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
    if (parts[1] !== undefined && parts[1].length > 2) v = parts[0] + '.' + parts[1].substring(0, 2);
    input.value = v;
}

/**
 * On blur: format the visible input to 2dp and sync
 * the value into the hidden field identified by hiddenId.
 * hiddenId is either 'f_vl_balance' or 'f_sl_balance'.
 */
function finalizeBalanceInput(input, hiddenId) {
    const raw = parseFloat(input.value);
    const val = isNaN(raw) ? 0 : Math.max(0, raw);
    input.value = val > 0 ? val.toFixed(2) : '';
    // Write 4-decimal value to the hidden field that the form submits
    document.getElementById(hiddenId).value = val.toFixed(4);
}

/* ════════════════════════════════════════════════
   NEW EMPLOYEE – LEAVE BALANCE AUTO-COMPUTATION
   Formula: (remaining days in hire month / 30) × 1.25
   Result is applied equally to both VL and SL.
   Writes to:
     f_vl_balance (leave_type_id=1 in leave_credit_balance)
     f_sl_balance (leave_type_id=2 in leave_credit_balance)
════════════════════════════════════════════════ */
function computeLeaveBalances() {
    if (currentEmpType !== 'new') return;

    const hireDateVal = document.getElementById('f_hire_date').value;
    const content     = document.getElementById('lpContent');
    const subtitle    = document.getElementById('lpSubtitle');

    if (!hireDateVal) {
        content.innerHTML = '<div class="lp-nodate">Enter a hire date above to compute initial leave balances.</div>';
        document.getElementById('f_vl_balance').value = '0';
        document.getElementById('f_sl_balance').value = '0';
        return;
    }

    const parts       = hireDateVal.split('-');
    const hireYear    = parseInt(parts[0], 10);
    const hireMon     = parseInt(parts[1], 10);
    const hireDay     = parseInt(parts[2], 10);
    const daysInMonth = new Date(hireYear, hireMon, 0).getDate();
    const remainingDays = daysInMonth - hireDay + 1;
    // Round to 4 decimal places to match decimal(7,4) precision
    const balance        = Math.round((remainingDays / 30) * 1.25 * 10000) / 10000;
    const balanceDisplay = balance.toFixed(4);
    const monthNames     = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const monthName      = monthNames[hireMon - 1];

    subtitle.textContent = 'Hire date: ' + monthName + ' ' + hireDay + ', ' + hireYear + ' · Accrual rate: 1.25 days/month';
    content.innerHTML = `
        <div class="lp-grid">
            <div class="lp-item">
                <p class="lp-type">VL</p>
                <p class="lp-name">Vacation Leave (leave_type_id = 1)</p>
                <p class="lp-balance">${balanceDisplay}</p>
                <p class="lp-unit">days initial balance</p>
            </div>
            <div class="lp-item">
                <p class="lp-type">SL</p>
                <p class="lp-name">Sick Leave (leave_type_id = 2)</p>
                <p class="lp-balance">${balanceDisplay}</p>
                <p class="lp-unit">days initial balance</p>
            </div>
        </div>
        <p class="lp-formula">Formula: (${remainingDays} remaining days ÷ 30) × 1.25 = <strong>${balanceDisplay} days</strong></p>
        <p class="lp-note">Remaining days in ${monthName}: from day ${hireDay} to day ${daysInMonth} = ${remainingDays} days</p>
    `;
    // Sync both hidden fields — these are what the backend reads
    document.getElementById('f_vl_balance').value = balance.toFixed(4);
    document.getElementById('f_sl_balance').value = balance.toFixed(4);
}

function onHireDateChange() {
    if (panelMode === 'add' && currentEmpType === 'new') {
        computeLeaveBalances();
    }
}

/* ════════════════════════════════════════════════
   AUTO-FILL: Username & Password from Employee ID
════════════════════════════════════════════════ */
function formatEmployeeId(input) {
    let d = input.value.replace(/\D/g, '').substring(0, 7);
    input.value = d.length > 3 ? d.substring(0, 3) + '-' + d.substring(3) : d;
    document.getElementById('f_employee_id_raw').value = d;

    if (panelMode === 'add') {
        const uEl = document.getElementById('f_username');
        if (!uEl.dataset.manuallyEdited) {
            uEl.value = input.value;
        }
        autoFillPassword();
    }
}

function formatTin(input) {
    // Strip non-digits, limit to 12 digits, format as 123-456-789-000
    let d = input.value.replace(/\D/g, '').substring(0, 12);
    let parts = [];
    if (d.length > 0) parts.push(d.substring(0, 3));
    if (d.length > 3) parts.push(d.substring(3, 6));
    if (d.length > 6) parts.push(d.substring(6, 9));
    if (d.length > 9) parts.push(d.substring(9, 12));
    input.value = parts.join('-');
}

function autoFillPassword() {
    if (panelMode !== 'add') return;
    const pwEl = document.getElementById('f_password');
    if (pwEl.dataset.manuallyEdited) return;
    const ln   = document.getElementById('f_last_name').value.trim().toLowerCase().replace(/\s+/g, '');
    const raw  = document.getElementById('f_employee_id_raw').value ?? '';
    const newPw = ln + (raw.slice(-4) || '');
    pwEl.value = newPw;
    document.getElementById('pwHintText').textContent = newPw || '—';
}

/* ════════════════════════════════════════════════
   EDIT PANEL
════════════════════════════════════════════════ */
let panelMode = 'add', currentUserId = null, currentIsActive = true;
let confirmCallback = null, savedFormState = null;

const PERSIST_FIELDS = [
    'f_first_name','f_middle_name','f_last_name','f_extension_name',
    'f_birthday','f_address','f_contact_number',
    'f_employee_id_display','f_employee_id_raw',
    'f_hire_date','f_department_id','f_position_id',
    'f_salary','f_salary_display','f_user_access',
    'f_username','f_password','f_pagibig_id','f_gsis_id','f_philhealth_id','f_tin',
    'f_is_new_employee','f_vl_balance','f_sl_balance',
    'f_vl_current_display','f_sl_current_display',
];

function saveFormState() {
    const fields = {};
    PERSIST_FIELDS.forEach(id => { const el = document.getElementById(id); if (el) fields[id] = el.value; });
    fields['_pw_manually_edited']       = document.getElementById('f_password').dataset.manuallyEdited || '';
    fields['_username_manually_edited'] = document.getElementById('f_username').dataset.manuallyEdited || '';
    fields['_emp_type'] = currentEmpType;
    savedFormState = { mode: panelMode, userId: currentUserId, isActive: currentIsActive, fields };
}

function restoreFormState(state) {
    if (!state) return false;
    PERSIST_FIELDS.forEach(id => { const el = document.getElementById(id); if (el && state.fields[id] !== undefined) el.value = state.fields[id]; });
    const salRaw = document.getElementById('f_salary').value;
    if (salRaw && !isNaN(parseFloat(salRaw))) document.getElementById('f_salary_display').value = parseFloat(salRaw).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    if (state.fields['f_department_id']) sdSet('dept', state.fields['f_department_id']);
    if (state.fields['f_position_id'])   sdSet('pos',  state.fields['f_position_id']);
    document.getElementById('f_password').dataset.manuallyEdited       = state.fields['_pw_manually_edited'] || '';
    document.getElementById('f_username').dataset.manuallyEdited       = state.fields['_username_manually_edited'] || '';
    if (state.fields['_emp_type']) setEmpType(state.fields['_emp_type']);
    if (panelMode === 'add') { const pw = document.getElementById('f_password').value; document.getElementById('pwHintText').textContent = pw || '—'; }
    return true;
}

const SD = {
    dept: { wrap:'sdw_dept',trigger:'sdt_dept',hidden:'f_department_id',list:'sdl_dept',empty:'sde_dept',search:'sds_dept',placeholder:'Select Department' },
    pos:  { wrap:'sdw_pos', trigger:'sdt_pos', hidden:'f_position_id',  list:'sdl_pos', empty:'sde_pos', search:'sds_pos', placeholder:'Select Position'   },
};
function sdToggle(key) {
    const cfg=SD[key],wrap=document.getElementById(cfg.wrap),isOpen=wrap.classList.contains('open');
    Object.keys(SD).forEach(k=>document.getElementById(SD[k].wrap).classList.remove('open'));
    if(!isOpen){wrap.classList.add('open');const s=document.getElementById(cfg.search);s.value='';sdFilter(key,'');setTimeout(()=>s.focus(),60);}
}
function sdFilter(key,q) {
    const cfg=SD[key],items=document.querySelectorAll('#'+cfg.list+' .sd-item');
    q=q.trim().toLowerCase();let n=0;
    items.forEach(i=>{const m=!q||(i.dataset.lbl||'').toLowerCase().includes(q);i.classList.toggle('sd-hidden',!m);if(m)n++;});
    document.getElementById(cfg.empty).style.display=n===0?'block':'none';
}
function sdSelect(key,val,lbl) {
    const cfg=SD[key];
    document.getElementById(cfg.hidden).value=val;
    const t=document.getElementById(cfg.trigger);
    t.textContent=lbl;t.classList.remove('placeholder-shown','field-error');
    document.querySelectorAll('#'+cfg.list+' .sd-item').forEach(i=>{const s=i.dataset.val==val;i.classList.toggle('selected',s);const c=i.querySelector('.sd-chk');if(c)c.classList.toggle('hidden',!s);});
    document.getElementById(cfg.wrap).classList.remove('open');
    document.getElementById(key==='dept'?'err_department_id':'err_position_id').classList.remove('show');
}
function sdSet(key,val){if(!val){sdReset(key);return;}const i=document.querySelector('#'+SD[key].list+' .sd-item[data-val="'+val+'"]');if(i)sdSelect(key,val,i.dataset.lbl);}
function sdReset(key){
    const cfg=SD[key];document.getElementById(cfg.hidden).value='';
    const t=document.getElementById(cfg.trigger);t.textContent=cfg.placeholder;t.classList.add('placeholder-shown');t.classList.remove('field-error');
    document.querySelectorAll('#'+cfg.list+' .sd-item').forEach(i=>{i.classList.remove('selected','sd-hidden');const c=i.querySelector('.sd-chk');if(c)c.classList.add('hidden');});
    document.getElementById(cfg.empty).style.display='none';
    document.getElementById(cfg.wrap).classList.remove('open');
    document.getElementById(cfg.search).value='';
}
document.addEventListener('click',e=>{Object.keys(SD).forEach(k=>{const w=document.getElementById(SD[k].wrap);if(!w.contains(e.target))w.classList.remove('open');});});

function applyAutoCaps(el){const pos=el.selectionStart;el.value=el.value.split(' ').map(w=>w?w[0].toUpperCase()+w.slice(1):w).join(' ');try{el.setSelectionRange(pos,pos);}catch(e){}}
document.addEventListener('DOMContentLoaded',()=>{
    document.querySelectorAll('.auto-caps').forEach(el=>el.addEventListener('input',()=>applyAutoCaps(el)));
    document.querySelectorAll('#empPanel input,#empPanel select').forEach(el=>{el.setAttribute('autocomplete',el.type==='password'?'new-password':'off');el.setAttribute('data-lpignore','true');el.setAttribute('data-form-type','other');});
    updateFooterCount(TOTAL_EMPLOYEES,TOTAL_EMPLOYEES);
});

function formatSalary(input){let raw=input.value.replace(/[^0-9.]/g,'');const pts=raw.split('.');if(pts.length>2)raw=pts[0]+'.'+pts.slice(1).join('');if(pts[1]!==undefined&&pts[1].length>2)raw=pts[0]+'.'+pts[1].substring(0,2);const intFmt=raw.split('.')[0].replace(/\B(?=(\d{3})+(?!\d))/g,',');const decFmt=raw.includes('.')?'.'+(raw.split('.')[1]??''):'';const oldLen=input.value.length;input.value=intFmt+decFmt;const pos=(input.selectionStart??0)+(input.value.length-oldLen);try{input.setSelectionRange(pos,pos);}catch(e){}document.getElementById('f_salary').value=raw;}
function finalizeSalary(input){const raw=document.getElementById('f_salary').value;if(!raw)return;const num=parseFloat(raw);if(isNaN(num))return;input.value=num.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});document.getElementById('f_salary').value=num.toFixed(2);}

function resetEmpTypeUI() {
    currentEmpType = '';
    document.getElementById('f_is_new_employee').value = '';
    document.getElementById('btnEmpTypeNew').classList.remove('active-new', 'active-old');
    document.getElementById('btnEmpTypeOld').classList.remove('active-new', 'active-old');
    document.getElementById('empTypeHint').textContent = 'Select whether this is a newly hired employee or an existing/transferred employee.';
    document.getElementById('empTypeHint').style.color = '#6b7280';
    document.getElementById('leavePreviewCard').style.display  = 'none';
    document.getElementById('oldEmpBalanceCard').style.display = 'none';
    document.getElementById('lpContent').innerHTML = '<div class="lp-nodate">Enter a hire date above to compute initial leave balances.</div>';
    // Reset all leave balance fields
    document.getElementById('f_vl_balance').value = '0';
    document.getElementById('f_sl_balance').value = '0';
    const vlDisp = document.getElementById('f_vl_current_display');
    const slDisp = document.getElementById('f_sl_current_display');
    if (vlDisp) vlDisp.value = '';
    if (slDisp) slDisp.value = '';
    document.getElementById('err_emp_type').classList.remove('show');
}

function openAddPanel(){
    panelMode='add';currentUserId=null;
    if(savedFormState&&savedFormState.mode==='add'){
        _applyMeta('add',null,null);
        restoreFormState(savedFormState);
    } else {
        resetForm();
        savedFormState=null;
        document.getElementById('panelTitle').textContent='Add New Employee';
        document.getElementById('panelSubtitle').textContent='Fill in all required fields';
        document.getElementById('submitBtn').textContent='Save Employee';
    }
    document.getElementById('empPanel').className='mode-add open';
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow='hidden';
}

function openEditPanel(userId){
    panelMode='edit';currentUserId=userId;
    if(savedFormState&&savedFormState.mode==='edit'&&savedFormState.userId===userId){
        currentIsActive=savedFormState.isActive;
        _applyMeta('edit',userId,currentIsActive);
        restoreFormState(savedFormState);
        document.getElementById('empPanel').className='mode-edit open';
        document.getElementById('overlay').classList.add('show');
        document.body.style.overflow='hidden';
        return;
    }
    resetForm();savedFormState=null;
    const emp=EMPLOYEES_DATA[userId];if(!emp){showToast('Error','Employee data not found.','error');return;}
    currentIsActive=emp.is_active==1;_applyMeta('edit',userId,currentIsActive);
    
    // UPDATED: employee_id is now fully editable.
    const idf=document.getElementById('f_employee_id_display');
    idf.value=emp.formatted_id??emp.employee_id;
    // Strip hyphens and ensure it is padded to 7 digits
    document.getElementById('f_employee_id_raw').value = idf.value.replace(/\D/g, '').padStart(7, '0');
    
    document.getElementById('f_first_name').value=emp.first_name??'';
    document.getElementById('f_middle_name').value=emp.middle_name??'';
    document.getElementById('f_last_name').value=emp.last_name??'';
    document.getElementById('f_extension_name').value=emp.extension_name??'';
    document.getElementById('f_birthday').value=emp.birthday??'';
    document.getElementById('f_contact_number').value=emp.contact_number??'';
    document.getElementById('f_address').value=emp.address??'';
    document.getElementById('f_hire_date').value=emp.hire_date??'';
    document.getElementById('f_pagibig_id').value=emp.pagibig_id??'';
    document.getElementById('f_gsis_id').value=emp.gsis_id??'';
    document.getElementById('f_philhealth_id').value=emp.philhealth_id??'';
    document.getElementById('f_tin').value = emp.tin ?? '';
    sdSet('dept',emp.department_id??'');sdSet('pos',emp.position_id??'');
    if(emp.salary){const n=parseFloat(emp.salary);document.getElementById('f_salary_display').value=n.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});document.getElementById('f_salary').value=n.toFixed(2);}
    if(emp.access){const sel=document.getElementById('f_user_access');const val=emp.access.user_access??'employee';const opt=[...sel.options].find(o=>o.value===val);if(opt)sel.value=val;else sel.selectedIndex=0;}
    
    // UPDATED: Correctly pull username
    document.getElementById('f_username').value=emp.username ?? '';

    document.getElementById('f_password').value='';
    document.getElementById('f_password').placeholder='Leave blank to keep current';
    document.getElementById('empPanel').className='mode-edit open';
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow='hidden';
}

function _applyMeta(mode,userId,isActive){
    if(mode==='edit'){
        const emp=EMPLOYEES_DATA[userId]||{};
        document.getElementById('panelTitle').textContent='Edit Employee';
        document.getElementById('panelSubtitle').textContent='Editing: '+(emp.last_name||'')+', '+(emp.first_name||'');
        document.getElementById('submitBtn').textContent='Update Employee';
        const btn=document.getElementById('toggleStatusBtn');
        if(isActive){btn.textContent='Deactivate';btn.className='btn-deact';}
        else{btn.textContent='Activate';btn.className='btn-activ';}
    } else {
        document.getElementById('panelTitle').textContent='Add New Employee';
        document.getElementById('panelSubtitle').textContent='Fill in all required fields';
        document.getElementById('submitBtn').textContent='Save Employee';
    }
}

function closePanel(){
    saveFormState();
    document.getElementById('empPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow='';
    Object.keys(SD).forEach(k=>document.getElementById(SD[k].wrap).classList.remove('open'));
}

function resetForm(){
    document.getElementById('empForm').reset();
    ['f_employee_id_display','f_employee_id_raw','f_salary_display','f_salary',
     'f_pagibig_id','f_gsis_id','f_philhealth_id','f_tin',
     'f_vl_balance','f_sl_balance',
     'f_vl_current_display','f_sl_current_display'].forEach(id=>{
        const el=document.getElementById(id);if(el)el.value='';
    });
    document.getElementById('f_password').dataset.manuallyEdited='';
    document.getElementById('f_username').dataset.manuallyEdited='';
    sdReset('dept');sdReset('pos');
    document.querySelectorAll('.error-msg').forEach(e=>e.classList.remove('show'));
    document.querySelectorAll('.form-field,.phone-input,.sd-trigger,.ob-input').forEach(f=>f.classList.remove('field-error'));
    
    // Ensure ID is not locked
    const idEl = document.getElementById('f_employee_id_display');
    idEl.readOnly = false;
    idEl.classList.remove('field-readonly');
    resetEmpTypeUI();
}

function validate(){
    let ok=true;
    [{id:'f_first_name',err:'err_first_name',msg:'First name is required.'},
     {id:'f_last_name', err:'err_last_name', msg:'Last name is required.' },
     {id:'f_hire_date', err:'err_hire_date', msg:'Hire date is required.' }
    ].forEach(({id,err,msg})=>{
        const f=document.getElementById(id),e=document.getElementById(err);
        if(!f?.value.trim()){f?.classList.add('field-error');if(e){e.textContent=msg;e.classList.add('show');}ok=false;}
        else{f?.classList.remove('field-error');e?.classList.remove('show');}
    });
    if(!document.getElementById('f_department_id').value){document.getElementById('sdt_dept').classList.add('field-error');document.getElementById('err_department_id').classList.add('show');ok=false;}
    else{document.getElementById('sdt_dept').classList.remove('field-error');document.getElementById('err_department_id').classList.remove('show');}
    if(!document.getElementById('f_position_id').value){document.getElementById('sdt_pos').classList.add('field-error');document.getElementById('err_position_id').classList.add('show');ok=false;}
    else{document.getElementById('sdt_pos').classList.remove('field-error');document.getElementById('err_position_id').classList.remove('show');}
    const sal=document.getElementById('f_salary').value;
    if(!sal||isNaN(parseFloat(sal))){document.getElementById('f_salary_display').classList.add('field-error');document.getElementById('err_salary').classList.add('show');ok=false;}
    else{document.getElementById('f_salary_display').classList.remove('field-error');document.getElementById('err_salary').classList.remove('show');}

    // UPDATED: ID is now validated for both Add and Edit
    const rawId=document.getElementById('f_employee_id_raw').value;
    const idEl=document.getElementById('f_employee_id_display'),idErr=document.getElementById('err_employee_id_display');
    if(!rawId||rawId.length!==7){
        idEl.classList.add('field-error');
        idErr.textContent='Employee ID must be exactly 7 digits (format: 000-0000).';
        idErr.classList.add('show');
        ok=false;
    } else {
        idEl.classList.remove('field-error');
        idErr.classList.remove('show');
    }

    if(panelMode==='add'){
        if(!currentEmpType){document.getElementById('err_emp_type').classList.add('show');ok=false;}
        else{document.getElementById('err_emp_type').classList.remove('show');}

        const uEl=document.getElementById('f_username'),uErr=document.getElementById('err_username');
        if(!uEl.value.trim()){uEl.classList.add('field-error');uErr.textContent='Username is required.';uErr.classList.add('show');ok=false;}
        else{uEl.classList.remove('field-error');uErr.classList.remove('show');}

        const pEl=document.getElementById('f_password'),pErr=document.getElementById('err_password');
        if(!pEl.value.trim()){pEl.classList.add('field-error');pErr.textContent='Password is required (min 6 characters).';pErr.classList.add('show');ok=false;}
        else{pEl.classList.remove('field-error');pErr.classList.remove('show');}

        if (currentEmpType === 'old') {
            const vlDisp = document.getElementById('f_vl_current_display');
            const slDisp = document.getElementById('f_sl_current_display');
            if (vlDisp) finalizeBalanceInput(vlDisp, 'f_vl_balance');
            if (slDisp) finalizeBalanceInput(slDisp, 'f_sl_balance');
        }
    }
    return ok;
}

function submitForm(){
    if(!validate())return;
    const btn=document.getElementById('submitBtn'),isEdit=panelMode==='edit';
    const url=isEdit?UPDATE_URL+'/'+currentUserId:STORE_URL;
    btn.textContent='Saving…';btn.disabled=true;
    const body=new FormData(document.getElementById('empForm'));
    if(isEdit)body.append('_method','PUT');
    body.set('employee_id',document.getElementById('f_employee_id_raw').value);
    
    fetch(url,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body})
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            savedFormState=null;
            closePanel();
            showToast(isEdit?'Employee Updated!':'Employee Added!',isEdit?'Changes saved successfully.':'New employee account created.','success');
            setTimeout(()=>location.reload(),1800);
        } else {
            if(data.errors){
                Object.entries(data.errors).forEach(([f,m])=>{
                    const el=document.getElementById('f_'+f),er=document.getElementById('err_'+f);
                    if(el)el.classList.add('field-error');
                    if(er){er.textContent=m[0];er.classList.add('show');}
                });
            } else {
                showToast('Error',data.message||'Something went wrong.','error');
            }
        }
    })
    .catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.textContent=isEdit?'Update Employee':'Save Employee';btn.disabled=false;});
}

function confirmToggleStatus(){
    if(!currentUserId)return;
    const d=currentIsActive,color=d?'#ef4444':'#16a34a',bg=d?'#fee2e2':'#dcfce7';
    const path=d?'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636':'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
    document.getElementById('confirmIconWrap').innerHTML='<svg class="w-8 h-8" fill="none" stroke="'+color+'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'+path+'"/></svg>';
    document.getElementById('confirmIconWrap').style.background=bg;
    document.getElementById('confirmTitle').textContent=(d?'Deactivate':'Activate')+' Employee?';
    document.getElementById('confirmMsg').innerHTML=d?'This employee will be marked <strong>Inactive</strong> and cannot log in until reactivated.':'This employee will be <strong>Activated</strong> and can log in again.';
    const ok=document.getElementById('confirmOkBtn');ok.textContent=d?'Yes, Deactivate':'Yes, Activate';ok.style.background=d?'#ef4444':'#16a34a';
    confirmCallback=doToggleStatus;document.getElementById('confirmModal').classList.add('show');
}
function doToggleStatus(){
    fetch(UPDATE_URL+'/'+currentUserId+'/toggle-status',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:new FormData()})
    .then(r=>r.json())
    .then(data=>{if(data.success){savedFormState=null;closePanel();closeConfirm();showToast(currentIsActive?'Employee Deactivated':'Employee Activated',data.message,currentIsActive?'warning':'success');setTimeout(()=>location.reload(),1800);}else{showToast('Error',data.message||'Could not update status.','error');}})
    .catch(()=>showToast('Network Error','Please check your connection.','error'));
}
function closeConfirm(){document.getElementById('confirmModal').classList.remove('show');confirmCallback=null;}
document.getElementById('confirmOkBtn').addEventListener('click',()=>{if(confirmCallback)confirmCallback();});

/* ════════════════════════════════════════════════
   VIEW PANEL
════════════════════════════════════════════════ */
let vpCurrentUserId = null, vpActiveTab = 'personal', vpDataCache = {};

function openViewPanel(userId) {
    vpCurrentUserId = userId;
    vpActiveTab    = 'personal';
    setVpLoader(true);
    switchVpTab('personal', false);
    document.getElementById('vpName').textContent      = 'Loading…';
    document.getElementById('vpMeta').textContent      = '';
    document.getElementById('vpAvatar').textContent    = '?';
    document.getElementById('vpStatusBadge').innerHTML = '';
    document.getElementById('viewPanel').classList.add('open');
    document.getElementById('overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
    if (vpDataCache[userId]) { renderViewPanel(vpDataCache[userId]); return; }
    fetch(SHOW_URL + '/' + userId + '/show', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => { if (data.success) { vpDataCache[userId] = data; renderViewPanel(data); } else { setVpLoader(false); showToast('Error', 'Could not load employee details.', 'error'); } })
    .catch(() => { setVpLoader(false); showToast('Network Error', 'Please check your connection.', 'error'); });
}
function closeViewPanel() {
    document.getElementById('viewPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
}
function openEditFromView() { closeViewPanel(); setTimeout(() => openEditPanel(vpCurrentUserId), 120); }

function setVpLoader(show) {
    document.getElementById('vpLoader').style.display = show ? 'flex' : 'none';
    document.querySelectorAll('.vp-pane').forEach(p => { p.classList.remove('active'); p.style.display = show ? 'none' : ''; });
    if (!show) switchVpTab(vpActiveTab, false);
}
function switchVpTab(tab, animate) {
    vpActiveTab = tab;
    document.querySelectorAll('.vp-tab').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('vptab-' + tab); if (btn) btn.classList.add('active');
    document.querySelectorAll('.vp-pane').forEach(p => { p.classList.remove('active'); p.style.display = 'none'; });
    const pane = document.getElementById('vpPane-' + tab); if (pane) { pane.classList.add('active'); pane.style.display = 'block'; }
}

function renderViewPanel(data) {
    const e = data.employee;
    const fmtDate = v => v ? new Date(v+'T00:00:00').toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—';
    const fmtPHP  = v => v != null ? '₱'+parseFloat(v).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}) : '—';
    const initials = ((e.first_name||'')[0]||'') + ((e.last_name||'')[0]||'');
    document.getElementById('vpAvatar').textContent    = initials.toUpperCase();
    document.getElementById('vpName').textContent      = (e.last_name||'') + ', ' + (e.first_name||'') + (e.middle_name ? ' '+e.middle_name[0]+'.' : '') + (e.extension_name ? ' '+e.extension_name : '');
    document.getElementById('vpMeta').textContent      = (e.position_name||'') + ' · ' + (e.department_name||'');
    document.getElementById('vpStatusBadge').innerHTML = e.is_active ? '<span class="vp-badge-active">● Active</span>' : '<span class="vp-badge-inactive">● Inactive</span>';
    document.getElementById('vd_id').textContent       = e.formatted_id || e.employee_id;
    document.getElementById('vd_dept').textContent     = e.department_name || '—';
    document.getElementById('vd_pos').textContent      = e.position_name || '—';
    document.getElementById('vd_hire').textContent     = fmtDate(e.hire_date);
    document.getElementById('vd_salary').textContent   = fmtPHP(e.salary);
    document.getElementById('vd_access').innerHTML     = '<span class="st '+(e.user_access==='admin'?'st-approved':'st-received')+'" style="font-size:10px;">'+(e.user_access||'—').toUpperCase()+'</span>';
    document.getElementById('vd_username').textContent = e.username || '—';
    document.getElementById('vd_fname').textContent    = e.first_name || '—';
    document.getElementById('vd_mname').textContent    = e.middle_name || '—';
    document.getElementById('vd_lname').textContent    = e.last_name || '—';
    document.getElementById('vd_ext').textContent      = e.extension_name || '—';
    document.getElementById('vd_bday').textContent     = fmtDate(e.birthday);
    document.getElementById('vd_contact').textContent  = e.contact_number ? '+63 ' + e.contact_number : '—';
    document.getElementById('vd_address').textContent  = e.address || '—';
    document.getElementById('vd_pagibig').textContent    = e.pagibig_id    || '—';
    document.getElementById('vd_gsis').textContent       = e.gsis_id       || '—';
    document.getElementById('vd_philhealth').textContent = e.philhealth_id || '—';
    document.getElementById('vd_tin').textContent = e.tin || '—';
    renderLeaveBalances(data.leaveBalances);
    renderLeaveHistory(data.leaveApplications, data.halfDayApplications);
    renderPayroll(data.payrollRecords);
    setVpLoader(false);
}

function renderLeaveBalances(balances) {
    const wrap = document.getElementById('vpLeaveBalances');
    if (!balances || balances.length === 0) {
        wrap.innerHTML = '<div class="vp-empty"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>No leave balance records found.</div>';
        return;
    }
    const byYear = {};
    balances.forEach(b => { (byYear[b.year] = byYear[b.year]||[]).push(b); });
    let html = '';
    Object.keys(byYear).sort((a,b)=>b-a).forEach(year => {
        html += '<div style="margin-bottom:6px;padding:2px 0 8px;"><p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px;">Year '+year+'</p><div class="lb-grid">';
        byYear[year].forEach(b => {
           const bal=b.remaining_balance,used=b.total_used,acc=b.total_accrued;
const balNum=parseFloat(bal);
const color=balNum<=0?'#ef4444':balNum<=2?'#f59e0b':'#2d5a1b';
html+='<div class="lb-card" style="border-left-color:'+color+';"><p class="lb-type-code">'+b.type_code+'</p><p class="lb-type-name">'+b.leave_type+'</p><p class="lb-balance" style="color:'+color+';">'+bal+'</p><p class="lb-balance-label">days remaining</p><div class="lb-stats"><div class="lb-stat"><p class="lb-stat-val">'+acc+'</p><p class="lb-stat-lbl">Accrued</p></div><div class="lb-stat"><p class="lb-stat-val">'+used+'</p><p class="lb-stat-lbl">Used</p></div></div></div>';
        });
        html += '</div></div>';
    });
    wrap.innerHTML = html;
}

function renderLeaveHistory(applications, halfDays) {
    const sc = s => ({'PENDING':'st-pending','APPROVED':'st-approved','REJECTED':'st-rejected','CANCELLED':'st-cancelled','RECEIVED':'st-received','ON-PROCESS':'st-onprocess'}[s]||'st-pending');
    const fd  = v => v ? new Date(v+'T00:00:00').toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '—';
    const tbody = document.getElementById('vpLeaveHistoryBody');
    if (!applications || applications.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="vp-empty">No leave applications found.</td></tr>'; }
    else { tbody.innerHTML = applications.map(la => { const note=la.status==='REJECTED'&&la.reject_reason?'<span style="color:#ef4444;font-size:10px;">⚠ '+la.reject_reason.substring(0,25)+(la.reject_reason.length>25?'…':'')+'</span>':(la.is_monetization?'<span style="color:#7c3aed;font-size:10px;">💰 Monetized</span>':'—'); return '<tr><td><span class="st st-received" style="font-size:10px;">'+la.type_code+'</span> '+la.leave_type+'</td><td>'+fd(la.application_date)+'</td><td style="white-space:nowrap;">'+fd(la.start_date)+(la.start_date!==la.end_date?' → '+fd(la.end_date):'')+'</td><td style="font-weight:700;">'+parseFloat(la.no_of_days).toFixed(1)+'</td><td><span class="st '+sc(la.status)+'">'+la.status+'</span></td><td style="font-size:11px;">'+note+'</td></tr>';}).join(''); }
    const hbody = document.getElementById('vpHalfDayBody');
    if (!halfDays || halfDays.length === 0) { hbody.innerHTML = '<tr><td colspan="4" class="vp-empty">No half-day applications found.</td></tr>'; }
    else { hbody.innerHTML = halfDays.map(hd => '<tr><td>'+hd.leave_type+'</td><td>'+fd(hd.date_of_absence)+'</td><td><span class="st" style="background:'+(hd.time_period==='AM'?'#dbeafe':'#fce7f3')+';color:'+(hd.time_period==='AM'?'#1d4ed8':'#9d174d')+';">'+hd.time_period+'</span></td><td><span class="st '+sc(hd.status)+'">'+hd.status+'</span></td></tr>').join(''); }
}

function renderPayroll(records) {
    const fmtPHP = v => v!=null?'₱'+parseFloat(v).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}):'—';
    const tbody  = document.getElementById('vpPayrollBody');
    if (!records || records.length === 0) { tbody.innerHTML = '<tr><td colspan="8" class="vp-empty">No payroll records found.</td></tr>'; return; }
    const latest = records[0];
    document.getElementById('vpPayGross').textContent = fmtPHP(latest.gross_salary);
    document.getElementById('vpPayDed').textContent   = fmtPHP(latest.total_deductions);
    document.getElementById('vpPayNet').textContent   = fmtPHP(latest.net_pay);
    document.getElementById('vpPaySummary').style.display = 'grid';
    tbody.innerHTML = records.map(pr=>'<tr><td style="white-space:nowrap;font-weight:600;">'+pr.period_label+'</td><td>'+fmtPHP(pr.gross_salary)+'</td><td>'+fmtPHP(pr.gsis_ee)+'</td><td>'+fmtPHP(pr.pagibig_ee)+'</td><td>'+fmtPHP(pr.philhealth_ee)+'</td><td>'+fmtPHP(pr.withholding_tax)+'</td><td style="color:#dc2626;font-weight:600;">'+fmtPHP(pr.total_deductions)+'</td><td style="color:#16a34a;font-weight:700;">'+fmtPHP(pr.net_pay)+'</td></tr>').join('');
    const breakdown=[{label:'Gross Salary',val:latest.gross_salary},{label:'GSIS (EE)',val:latest.gsis_ee},{label:'GSIS (Govt)',val:latest.gsis_govt},{label:'Pag-IBIG (EE)',val:latest.pagibig_ee},{label:'Pag-IBIG (Govt)',val:latest.pagibig_govt},{label:'PhilHealth (EE)',val:latest.philhealth_ee},{label:'PhilHealth (Govt)',val:latest.philhealth_govt},{label:'Withholding Tax',val:latest.withholding_tax},{label:'Loan – DBP',val:latest.loan_dbp},{label:'Loan – LBP',val:latest.loan_lbp},{label:'Loan – CNGWMPC',val:latest.loan_cngwmpc},{label:'Loan – PARACLE',val:latest.loan_paracle},{label:'Allowance – PERA',val:latest.allowance_pera},{label:'Allowance – RATA',val:latest.allowance_rata},{label:'Other Allowances',val:latest.allowance_other},{label:'Total Allowances',val:latest.total_allowances},{label:'Total Deductions',val:latest.total_deductions},{label:'Net Pay',val:latest.net_pay}];
    document.getElementById('vpPayBreakdownGrid').innerHTML = breakdown.filter(b=>parseFloat(b.val)!==0).map(b=>'<div><p class="vp-field-label">'+b.label+'</p><p class="vp-field-value">'+fmtPHP(b.val)+'</p></div>').join('');
    document.getElementById('vpPayBreakdown').style.display = 'block';
}

/* ════ SHARED UTILITIES ════ */
function showToast(title,msg,type){
    const m={success:{bg:'#dcfce7',border:'#bbf7d0',c:'#16a34a',p:'M5 13l4 4L19 7'},error:{bg:'#fee2e2',border:'#fca5a5',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},warning:{bg:'#fef9c3',border:'#fde047',c:'#ca8a04',p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}};
    const s=m[type]||m.success;
    document.getElementById('toastTitle').textContent=title;document.getElementById('toastMsg').textContent=msg;
    document.getElementById('toastIcon').innerHTML='<svg class="w-5 h-5" fill="none" stroke="'+s.c+'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'+s.p+'"/></svg>';
    document.getElementById('toastIcon').style.background=s.bg;
    const t=document.getElementById('toast');t.style.borderColor=s.border;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3500);
}
function togglePw(){const pw=document.getElementById('f_password'),eo=document.getElementById('eyeOpen'),ec=document.getElementById('eyeClosed');if(pw.type==='password'){pw.type='text';eo.classList.add('hidden');ec.classList.remove('hidden');}else{pw.type='password';eo.classList.remove('hidden');ec.classList.add('hidden');}}
function updateFooterCount(visible,total){const rc=document.getElementById('resultCount'),fc=document.getElementById('footerCount');if(rc)rc.textContent=visible===total?total+' employees':visible+' of '+total+' shown';if(fc)fc.textContent=visible===total?'Showing all '+total+' employee'+(total!==1?'s':''):'Showing '+visible+' of '+total+' employee'+(total!==1?'s':'');}
function filterTable(){const raw=document.getElementById('searchInput').value,q=raw.trim().toLowerCase(),rows=document.querySelectorAll('#empTbody .emp-row'),noRes=document.getElementById('noResultsRow'),clear=document.getElementById('clearSearch');if(clear)clear.style.display=q?'block':'none';let visible=0;rows.forEach(row=>{let match=false;if(!q){match=true;}else{if(row.textContent.toLowerCase().includes(q)){match=true;}if(!match){const userId=row.dataset.userid,emp=userId?EMPLOYEES_DATA[userId]:null;if(emp){const searchable=[emp.first_name,emp.middle_name,emp.last_name,emp.extension_name,emp.birthday,emp.address,emp.contact_number,emp.pagibig_id,emp.gsis_id,emp.philhealth_id,emp.formatted_id,emp.credential?.username,emp.access?.user_access,emp.is_active?'active':'inactive'].filter(Boolean).join(' ').toLowerCase();if(searchable.includes(q))match=true;}}}row.style.display=match?'':'none';if(match)visible++;});if(noRes)noRes.style.display=(q&&visible===0)?'':'none';updateFooterCount(visible,rows.length);}
function clearSearchInput(){document.getElementById('searchInput').value='';filterTable();document.getElementById('searchInput').focus();}
const sortState={};
function sortTable(col){const tbody=document.getElementById('empTbody'),rows=Array.from(tbody.querySelectorAll('.emp-row')),prev=sortState[col]||null,dir=prev==='asc'?'desc':'asc';document.querySelectorAll('.sort-btn').forEach(b=>b.classList.remove('asc','desc'));document.querySelectorAll('th:nth-child('+(col+1)+') .sort-btn').forEach(b=>b.classList.add(dir));Object.keys(sortState).forEach(k=>delete sortState[k]);sortState[col]=dir;rows.sort((a,b)=>{const av=a.cells[col]?.textContent.trim()??'',bv=b.cells[col]?.textContent.trim()??'',cmp=av.localeCompare(bv,undefined,{numeric:true,sensitivity:'base'});return dir==='asc'?cmp:-cmp;});rows.forEach(r=>tbody.appendChild(r));}
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeConfirm();closePanel();closeViewPanel();}});
@if(session('success'))
    document.addEventListener('DOMContentLoaded',()=>showToast('Success',@json(session('success')),'success'));
@endif
</script>

@endsection