{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'My Profile')

@section('content')

@php
    $employee   = Auth::user()?->employee;
    $credential = Auth::user();
    $initial    = strtoupper(substr($employee->first_name ?? 'U', 0, 1));
    $position   = $employee?->position?->position_name ?? '—';
    $department = $employee?->department?->department_name ?? '—';
    $empNo      = $employee?->employee_id ?? '—';
@endphp

{{-- ── Flash Messages ── --}}
@if(session('success'))
<div id="flashMsg"
     class="fixed top-5 right-5 z-[2000] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold text-white"
     style="background:#2d5a1b; animation:slideInToast .35s cubic-bezier(.22,1,.36,1);">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
<script>setTimeout(function(){ var e=document.getElementById('flashMsg'); if(e){ e.style.transition='opacity .3s'; e.style.opacity='0'; setTimeout(function(){e.remove();},300); } }, 3500);</script>
@endif

@if($errors->any())
<div id="flashErr"
     class="fixed top-5 right-5 z-[2000] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold text-white"
     style="background:#dc2626; animation:slideInToast .35s cubic-bezier(.22,1,.36,1);">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ $errors->first() }}
</div>
<script>setTimeout(function(){ var e=document.getElementById('flashErr'); if(e){ e.style.transition='opacity .3s'; e.style.opacity='0'; setTimeout(function(){e.remove();},300); } }, 3500);</script>
@endif

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');

* { box-sizing: border-box; }

.profile-root {
    font-family: 'DM Sans', system-ui, sans-serif;
    min-height: 100vh;
    background: #f4f6f3;
    padding: 24px 28px;
}

@keyframes slideInToast {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Layout ── */
.profile-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    max-width: 1600px;
    margin: 0 auto;
    animation: fadeUp .4s ease both;
}

/* ── Cards ── */
.card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e8ece6;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    overflow: hidden;
    transition: box-shadow .2s;
}
.card:hover { box-shadow: 0 4px 20px rgba(45,90,27,.07); }

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f2ef;
    background: #fafbfa;
    gap: 10px;
}
.card-title {
    font-size: 12px;
    font-weight: 800;
    color: #374151;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.card-body { padding: 20px; }

/* ── Hero / Sidebar ── */
.hero-card {
    background: linear-gradient(160deg, #1a3a0f 0%, #2d5a1b 55%, #3d7a24 100%);
    border: none;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.hero-card::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    pointer-events: none;
}
.hero-card::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -30px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.03);
    pointer-events: none;
}
.hero-body {
    padding: 28px 22px 24px;
    text-align: center;
    position: relative;
    z-index: 1;
}
.avatar-ring {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    border: 3px solid rgba(255,255,255,.25);
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
    font-size: 30px;
    font-weight: 900;
    letter-spacing: -1px;
    color: #fff;
    position: relative;
}
.status-dot {
    position: absolute;
    bottom: 3px; right: 3px;
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 2.5px solid rgba(255,255,255,.8);
}
.hero-name {
    font-size: 16px;
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    margin-bottom: 4px;
}
.hero-position {
    font-size: 12.5px;
    font-weight: 600;
    color: rgba(255,255,255,.75);
    margin-bottom: 2px;
}
.hero-dept {
    font-size: 11.5px;
    color: rgba(255,255,255,.5);
    margin-bottom: 14px;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
}
.hero-badge.active   { background: rgba(134,239,172,.18); color: #86efac; }
.hero-badge.inactive { background: rgba(255,255,255,.1);  color: rgba(255,255,255,.5); }

.hero-stats {
    border-top: 1px solid rgba(255,255,255,.1);
    padding: 16px 22px;
    position: relative;
    z-index: 1;
}
.stat-row {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.stat-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    border-radius: 12px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.08);
    gap: 10px;
}
.stat-label {
    font-size: 10.5px;
    font-weight: 600;
    color: rgba(255,255,255,.5);
    letter-spacing: .06em;
    text-transform: uppercase;
    flex-shrink: 0;
}
.stat-value {
    font-size: 12px;
    font-weight: 700;
    color: rgba(255,255,255,.9);
    text-align: right;
    word-break: break-word;
}

/* ── Form inputs ── */
.fl {
    font-size: 10.5px;
    font-weight: 700;
    color: #9ca3af;
    letter-spacing: .09em;
    text-transform: uppercase;
    margin-bottom: 5px;
    display: block;
}
.pi {
    width: 100%;
    padding: 9px 13px;
    border-radius: 10px;
    border: 1.5px solid #e8ece6;
    font-size: 13px;
    color: #374151;
    background: #f8faf7;
    transition: border-color .15s, background .15s, box-shadow .15s;
    outline: none;
    font-family: inherit;
}
.pi:focus        { border-color: #2d5a1b; background: #fff; box-shadow: 0 0 0 3px rgba(45,90,27,.08); }
.pi[readonly]    { background: #f4f6f3; color: #9ca3af; cursor: default; border-color: #eef0ec; }
.pi.editing      { background: #fff !important; border-color: #2d5a1b !important; color: #111827 !important; }

.pi-wrap         { position: relative; }
.pi-wrap .pi     { padding-right: 38px; }
.pi-eye {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #9ca3af;
    display: flex; align-items: center; padding: 0;
    transition: color .15s;
}
.pi-eye:hover { color: #374151; }

/* ── Section subheading ── */
.sub-heading {
    font-size: 10px;
    font-weight: 800;
    color: #b5bfb0;
    letter-spacing: .12em;
    text-transform: uppercase;
    padding-bottom: 12px;
    margin-bottom: 16px;
    border-bottom: 1px solid #f0f2ef;
}

/* ── Buttons ── */
.btn-edit {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700; color: #2d5a1b;
    padding: 5px 13px; border-radius: 8px;
    border: 1.5px solid #2d5a1b; background: transparent;
    cursor: pointer; transition: background .15s, color .15s;
    font-family: inherit; white-space: nowrap; flex-shrink: 0;
}
.btn-edit:hover { background: #2d5a1b; color: #fff; }

.btn-save {
    display: none; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700; color: #fff;
    padding: 5px 13px; border-radius: 8px;
    border: 1.5px solid #2d5a1b; background: #2d5a1b;
    cursor: pointer; transition: opacity .15s;
    font-family: inherit; white-space: nowrap; flex-shrink: 0;
}
.btn-save:hover { opacity: .85; }

.btn-cancel {
    display: none; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700; color: #6b7280;
    padding: 5px 13px; border-radius: 8px;
    border: 1.5px solid #d1d5db; background: #fff;
    cursor: pointer; transition: background .15s;
    font-family: inherit; white-space: nowrap; flex-shrink: 0;
}
.btn-cancel:hover { background: #f3f4f6; }

/* ── Divider ── */
.divider { height: 1px; background: #f0f2ef; margin: 20px 0; }

/* ── Password rule items ── */
.pw-rule {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #ef4444;
    font-weight: 500;
    transition: color .2s;
}
.pw-rule.passed { color: #16a34a; }
.pw-rule-icon {
    font-size: 8px;
    flex-shrink: 0;
    line-height: 1;
    transition: all .2s;
    width: 14px;
    text-align: center;
}
.pw-rule.passed .pw-rule-icon { font-size: 13px; }

/* ── Responsive grids (replaces inline style grids) ── */

/* Name row: 4 cols → 2 cols → 1 col */
.grid-name {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 140px;
    gap: 14px;
    margin-bottom: 14px;
}

/* Contact row: 2 cols → 1 col */
.grid-contact {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

/* Employment details: 3 cols → 2 cols → 1 col */
.grid-employment {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}

/* Government accounts: 2 cols → 1 col */
.grid-govid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

/* Card header action buttons group */
.card-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

/* ── Password modal ── */
.pw-modal-inner {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 24px 64px rgba(0,0,0,.18);
    width: 100%;
    max-width: 420px;
    overflow: hidden;
    font-family: 'DM Sans', system-ui, sans-serif;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}
.pw-modal-body {
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    overflow-y: auto;
    flex: 1;
}
.pw-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 22px 18px;
    border-top: 1px solid #f0f2ef;
    flex-shrink: 0;
}

/* ── RESPONSIVE BREAKPOINTS ── */

/* Tablet: ≤ 900px — stack sidebar below main */
@media (max-width: 900px) {
    .profile-root  { padding: 14px; }
    .profile-grid  { grid-template-columns: 1fr; }
    /* On tablet keep name as 2 cols */
    .grid-name     { grid-template-columns: 1fr 1fr; }
    .grid-employment { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile: ≤ 600px — single column everything */
@media (max-width: 600px) {
    .profile-root  { padding: 10px; }
    .card          { border-radius: 14px; }
    .card-body     { padding: 14px; }
    .card-header   { padding: 12px 14px; }

    .grid-name       { grid-template-columns: 1fr 1fr; }
    .grid-contact    { grid-template-columns: 1fr; }
    .grid-employment { grid-template-columns: 1fr 1fr; }
    .grid-govid      { grid-template-columns: 1fr; }

    .hero-body  { padding: 20px 16px 18px; }
    .hero-stats { padding: 12px 16px; }
    .hero-name  { font-size: 14px; }
    .avatar-ring { width: 70px; height: 70px; font-size: 24px; }

    .stat-item { padding: 8px 12px; }
    .stat-label { font-size: 9.5px; }
    .stat-value { font-size: 11px; }

    .divider    { margin: 14px 0; }

    /* Modal takes full width on mobile */
    .pw-modal-inner { border-radius: 16px; max-height: 95vh; }
    .pw-modal-body  { padding: 16px; gap: 12px; }
    .pw-modal-footer { padding: 12px 16px 14px; flex-wrap: wrap; }
    .pw-modal-footer button { flex: 1; text-align: center; justify-content: center; }

    #flashMsg, #flashErr {
        top: auto !important;
        bottom: 16px !important;
        right: 10px !important;
        left: 10px !important;
        font-size: 12px !important;
    }
}

/* Extra small: ≤ 400px — full single col for name */
@media (max-width: 400px) {
    .grid-name { grid-template-columns: 1fr; }
    .grid-employment { grid-template-columns: 1fr; }
}
</style>

<div class="profile-root">
<div class="profile-grid">

    {{-- ══════════ LEFT SIDEBAR ══════════ --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Hero Card --}}
        <div class="card hero-card">
            <div class="hero-body">
                <div class="avatar-ring">
                    {{ $initial }}
                    <span class="status-dot {{ $employee?->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                </div>
                <div class="hero-name">
                    {{ strtoupper($employee->last_name ?? '') }}{{ $employee?->extension_name ? ' '.strtoupper($employee->extension_name) : '' }},<br>
                    {{ $employee->first_name ?? 'User' }}
                    @if($employee?->middle_name) {{ strtoupper(substr($employee->middle_name,0,1)) }}. @endif
                </div>
                <div class="hero-position">{{ $position }}</div>
                <div class="hero-dept">{{ $department }}</div>
                <span class="hero-badge {{ $employee?->is_active ? 'active' : 'inactive' }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                    {{ $employee?->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="hero-stats">
                <div class="stat-row">
                    <div class="stat-item">
                        <span class="stat-label">Employee ID</span>
                        <span class="stat-value">{{ $empNo }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Hired Date</span>
                        <span class="stat-value">
                            {{ isset($employee->hire_date) ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : '—' }}
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Salary</span>
                        <span class="stat-value">
                            {{ $employee?->salary ? '₱ '.number_format($employee->salary, 2) : '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Account Credentials ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Credentials</span>
                <button onclick="openPwModal()" class="btn-edit">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change
                </button>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label class="fl">Username</label>
                    <input type="text" readonly
                           value="{{ Auth::user()?->username ?? $empNo }}"
                           class="pi">
                </div>
                <div>
                    <label class="fl">Password</label>
                    <div class="pi-wrap">
                        <input type="password" id="credPw" readonly
                               value="**********"
                               class="pi"
                               style="letter-spacing:3px; font-size:14px;">
                        <button type="button" class="pi-eye" id="credPwEye"
                                onclick="toggleCredPw()" tabindex="-1"
                                title="Show / Hide password">
                            <svg id="credPwEyeIcon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="credPwHint" style="display:none;font-size:10px;color:#9ca3af;margin-top:5px;line-height:1.4;">
                        🔒 For security, your actual password cannot be displayed. Use <em>Change Password</em> to set a new one.
                    </p>
                </div>
                <button onclick="openPwModal()"
                        style="font-size:11.5px;font-weight:700;color:#2d5a1b;background:none;border:none;
                               cursor:pointer;text-align:left;padding:0;font-family:inherit;text-decoration:underline;
                               text-underline-offset:2px;">
                    Change Password →
                </button>
            </div>
        </div>

    </div>{{-- end sidebar --}}

    {{-- ══════════ RIGHT MAIN CONTENT ══════════ --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- ── Personal Details ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Personal Details</span>
                <div class="card-header-actions">
                    <button class="btn-cancel" id="cancelPersonal" onclick="cancelSection('personal')">Cancel</button>
                    <button class="btn-save"   id="savePersonal"   onclick="document.getElementById('fPersonal').submit()">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>Save
                    </button>
                    <button class="btn-edit" id="editPersonalBtn" onclick="enableSection('personal')">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>Edit
                    </button>
                </div>
            </div>

            <form id="fPersonal" method="POST" action="{{ route('profile.update') }}" class="card-body">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="personal">

                {{-- Name row --}}
                <div class="grid-name">
                    <div>
                        <label class="fl">First Name</label>
                        <input type="text" name="first_name" readonly
                               value="{{ old('first_name', $employee->first_name ?? '') }}"
                               class="pi personal-field">
                    </div>
                    <div>
                        <label class="fl">Middle Name</label>
                        <input type="text" name="middle_name" readonly
                               value="{{ old('middle_name', $employee->middle_name ?? '') }}"
                               class="pi personal-field">
                    </div>
                    <div>
                        <label class="fl">Last Name</label>
                        <input type="text" name="last_name" readonly
                               value="{{ old('last_name', $employee->last_name ?? '') }}"
                               class="pi personal-field">
                    </div>
                    <div>
                        <label class="fl">Ext. Name</label>
                        <input type="text" name="extension_name" readonly placeholder="Jr., Sr…"
                               value="{{ old('extension_name', $employee->extension_name ?? '') }}"
                               class="pi personal-field">
                    </div>
                </div>

                {{-- Contact row --}}
                <div class="grid-contact">
                    <div>
                        <label class="fl">Phone Number</label>
                        <input type="text" name="contact_number" readonly
                               value="{{ old('contact_number', $employee->contact_number ?? '') }}"
                               class="pi personal-field">
                    </div>
                    <div>
                        <label class="fl">Birthday</label>
                        <input type="date" name="birthday" readonly
                               value="{{ old('birthday', isset($employee->birthday) ? \Carbon\Carbon::parse($employee->birthday)->format('Y-m-d') : '') }}"
                               class="pi personal-field">
                    </div>
                </div>

                {{-- Address --}}
                <div style="margin-bottom:0;">
                    <label class="fl">Address</label>
                    <input type="text" name="address" readonly
                           value="{{ old('address', $employee->address ?? '') }}"
                           class="pi personal-field">
                </div>

                {{-- Employment sub-section --}}
                <div class="divider"></div>
                <div class="sub-heading">Employment Details</div>
                <div class="grid-employment">
                    <div>
                        <label class="fl">Employee ID</label>
                        <input type="text" readonly value="{{ $empNo }}" class="pi">
                    </div>
                    <div>
                        <label class="fl">Department</label>
                        <input type="text" readonly value="{{ $department }}" class="pi">
                    </div>
                    <div>
                        <label class="fl">Position</label>
                        <input type="text" readonly value="{{ $position }}" class="pi">
                    </div>
                    <div>
                        <label class="fl">Hired Date</label>
                        <input type="text" readonly
                               value="{{ isset($employee->hire_date) ? \Carbon\Carbon::parse($employee->hire_date)->format('m/d/Y') : '—' }}"
                               class="pi">
                    </div>
                    <div>
                        <label class="fl">Salary</label>
                        <input type="text" readonly
                               value="{{ $employee?->salary ? '₱ '.number_format($employee->salary, 2) : '—' }}"
                               class="pi">
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Accounts (Pag-ibig / GSIS / PhilHealth / TIN) ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Government Accounts</span>
                <div class="card-header-actions">
                    <button class="btn-cancel" id="cancelAccounts" onclick="cancelSection('accounts')">Cancel</button>
                    <button class="btn-save"   id="saveAccounts"   onclick="document.getElementById('fAccounts').submit()">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>Save
                    </button>
                    <button class="btn-edit" id="editAccountsBtn" onclick="enableSection('accounts')">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>Edit
                    </button>
                </div>
            </div>

            <form id="fAccounts" method="POST" action="{{ route('profile.update') }}" class="card-body">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="accounts">

                <div class="grid-govid">
                    <div>
                        <label class="fl">Pag-ibig ID</label>
                        <input type="text" name="pagibig_id" readonly
                               value="{{ old('pagibig_id', $employee->pagibig_id ?? '') }}"
                               class="pi accounts-field">
                    </div>
                    <div>
                        <label class="fl">GSIS ID</label>
                        <input type="text" name="gsis_id" readonly
                               value="{{ old('gsis_id', $employee->gsis_id ?? '') }}"
                               class="pi accounts-field">
                    </div>
                    <div>
                        <label class="fl">PhilHealth ID</label>
                        <input type="text" name="philhealth_id" readonly
                               value="{{ old('philhealth_id', $employee->philhealth_id ?? '') }}"
                               class="pi accounts-field">
                    </div>
                    <div>
                        <label class="fl">TIN</label>
                        <input type="text" name="tin" readonly
                               value="{{ old('tin', $employee->tin ?? '') }}"
                               class="pi accounts-field">
                    </div>
                </div>
            </form>
        </div>

    </div>{{-- end main content --}}
</div>{{-- end profile-grid --}}
</div>{{-- end profile-root --}}

{{-- ══════════ CHANGE PASSWORD MODAL ══════════ --}}
<div id="pwModal"
     style="display:none; position:fixed; inset:0; z-index:1000;
            background:rgba(0,0,0,.5); backdrop-filter:blur(5px);
            align-items:center; justify-content:center; padding:16px;">

    <div class="pw-modal-inner">

        {{-- Modal header --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:18px 22px 16px; border-bottom:1px solid #f0f2ef; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:11px;">
                <div style="width:36px;height:36px;border-radius:11px;background:#dcfce7;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:17px;height:17px;color:#2d5a1b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#111827;">Change Password</div>
                    <div style="font-size:11px;color:#9ca3af;">Keep your account secure</div>
                </div>
            </div>
            <button onclick="closePwModal()"
                    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;
                           border-radius:9px;border:none;background:none;cursor:pointer;color:#9ca3af;
                           transition:background .15s; flex-shrink:0;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal form --}}
        <form id="fPassword" method="POST" action="{{ route('profile.password') }}"
              class="pw-modal-body">
            @csrf
            @method('PUT')

            {{-- Current Password --}}
            <div>
                <label class="fl">Current Password</label>
                <div class="pi-wrap">
                    <input type="password" name="current_password" id="cpCur"
                           autocomplete="current-password" class="pi">
                    <button type="button" class="pi-eye" onclick="toggleVis('cpCur',this)" tabindex="-1">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- New Password + strength bar + requirements guide --}}
            <div>
                <label class="fl">New Password</label>
                <div class="pi-wrap">
                    <input type="password" name="password" id="cpNew"
                           autocomplete="new-password" class="pi" oninput="updateStrength(this.value)">
                    <button type="button" class="pi-eye" onclick="toggleVis('cpNew',this)" tabindex="-1">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                {{-- Strength bar --}}
                <div id="sWrap" style="display:none; margin-top:8px;">
                    <div style="display:flex;gap:4px;">
                        <div id="sb1" style="height:4px;flex:1;border-radius:999px;background:#e5e7eb;transition:background .25s;"></div>
                        <div id="sb2" style="height:4px;flex:1;border-radius:999px;background:#e5e7eb;transition:background .25s;"></div>
                        <div id="sb3" style="height:4px;flex:1;border-radius:999px;background:#e5e7eb;transition:background .25s;"></div>
                        <div id="sb4" style="height:4px;flex:1;border-radius:999px;background:#e5e7eb;transition:background .25s;"></div>
                    </div>
                    <p id="sLabel" style="font-size:11px;margin-top:5px;font-weight:600;"></p>
                </div>

                {{-- Password requirements guide --}}
                <div id="pwGuide" style="display:none; margin-top:10px; padding:12px 14px;
                                          border-radius:10px; background:#f8faf7;
                                          border:1.5px solid #eef0ec;">
                    <p style="font-size:10px;font-weight:800;color:#9ca3af;letter-spacing:.09em;
                               text-transform:uppercase;margin:0 0 9px;">Password Requirements</p>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <div class="pw-rule" id="rule-len">
                            <span class="pw-rule-icon">●</span>
                            <span class="pw-rule-text">At least 8 characters</span>
                        </div>
                        <div class="pw-rule" id="rule-num">
                            <span class="pw-rule-icon">●</span>
                            <span class="pw-rule-text">At least one numeric digit (0–9)</span>
                        </div>
                        <div class="pw-rule" id="rule-lower">
                            <span class="pw-rule-icon">●</span>
                            <span class="pw-rule-text">At least one lowercase letter</span>
                        </div>
                        <div class="pw-rule" id="rule-upper">
                            <span class="pw-rule-icon">●</span>
                            <span class="pw-rule-text">At least one uppercase letter</span>
                        </div>
                        <div class="pw-rule" id="rule-special">
                            <span class="pw-rule-icon">●</span>
                            <span class="pw-rule-text">At least one special character (@ # $ %)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="fl">Confirm New Password</label>
                <div class="pi-wrap">
                    <input type="password" name="password_confirmation" id="cpConf"
                           autocomplete="new-password" class="pi">
                    <button type="button" class="pi-eye" onclick="toggleVis('cpConf',this)" tabindex="-1">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>

        {{-- Modal footer --}}
        <div class="pw-modal-footer">
            <button onclick="closePwModal()"
                    style="padding:9px 20px;border-radius:10px;font-size:13px;font-weight:700;
                           color:#6b7280;border:1.5px solid #e5e7eb;background:#fff;
                           cursor:pointer;font-family:inherit;transition:background .15s;"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button onclick="document.getElementById('fPassword').submit()"
                    style="padding:9px 20px;border-radius:10px;font-size:13px;font-weight:700;
                           color:#fff;border:none;background:#2d5a1b;
                           cursor:pointer;font-family:inherit;transition:opacity .15s;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                Update Password
            </button>
        </div>
    </div>
</div>

<script>
/* ── Section toggling ── */
var _orig = {};

function enableSection(sec) {
    var fields    = document.querySelectorAll('.' + sec + '-field');
    var editBtn   = document.getElementById('edit'   + cap(sec) + 'Btn');
    var saveBtn   = document.getElementById('save'   + cap(sec));
    var cancelBtn = document.getElementById('cancel' + cap(sec));

    _orig[sec] = {};
    fields.forEach(function(f) {
        _orig[sec][f.name] = f.value;
        f.removeAttribute('readonly');
        f.classList.add('editing');
    });
    if (fields[0]) fields[0].focus();
    editBtn.style.display   = 'none';
    saveBtn.style.display   = 'inline-flex';
    cancelBtn.style.display = 'inline-flex';
}

function cancelSection(sec) {
    var fields    = document.querySelectorAll('.' + sec + '-field');
    var editBtn   = document.getElementById('edit'   + cap(sec) + 'Btn');
    var saveBtn   = document.getElementById('save'   + cap(sec));
    var cancelBtn = document.getElementById('cancel' + cap(sec));

    fields.forEach(function(f) {
        if (_orig[sec] && _orig[sec][f.name] !== undefined) f.value = _orig[sec][f.name];
        f.setAttribute('readonly', '');
        f.classList.remove('editing');
    });
    editBtn.style.display   = '';
    saveBtn.style.display   = 'none';
    cancelBtn.style.display = 'none';
}

function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

/* ── Credentials password toggle ── */
var SVG_EYE_OPEN  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
var SVG_EYE_CLOSE = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

function toggleCredPw() {
    var f    = document.getElementById('credPw');
    var icon = document.getElementById('credPwEyeIcon');
    var hint = document.getElementById('credPwHint');
    if (!f) return;
    if (f.type === 'password') {
        f.type                = 'text';
        f.style.letterSpacing = '2px';
        icon.innerHTML        = SVG_EYE_CLOSE;
        hint.style.display    = 'block';
    } else {
        f.type                = 'password';
        f.style.letterSpacing = '3px';
        icon.innerHTML        = SVG_EYE_OPEN;
        hint.style.display    = 'none';
    }
}

/* ── Visibility toggle (modal) ── */
var SVG_HIDE = '<svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';
var SVG_SHOW = '<svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';

function toggleVis(id, btn) {
    var f = document.getElementById(id);
    if (!f) return;
    if (f.type === 'password') { f.type = 'text';     btn.innerHTML = SVG_HIDE; }
    else                       { f.type = 'password'; btn.innerHTML = SVG_SHOW; }
}

/* ── Modal open / close ── */
function openPwModal() {
    var m = document.getElementById('pwModal');
    m.style.display = 'flex';
    ['cpCur','cpNew','cpConf'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) { el.value = ''; el.type = 'password'; }
    });
    document.getElementById('sWrap').style.display   = 'none';
    document.getElementById('pwGuide').style.display = 'none';
    ['sb1','sb2','sb3','sb4'].forEach(function(id){
        document.getElementById(id).style.background = '#e5e7eb';
    });
    document.getElementById('sLabel').textContent = '';
    ['rule-len','rule-num','rule-lower','rule-upper','rule-special'].forEach(function(id){
        var row  = document.getElementById(id);
        var icon = row.querySelector('.pw-rule-icon');
        row.classList.remove('passed');
        icon.textContent = '●';
    });
}

function closePwModal() {
    document.getElementById('pwModal').style.display = 'none';
}

document.getElementById('pwModal').addEventListener('click', function(e) {
    if (e.target === this) closePwModal();
});

/* ── Password strength + live requirements ── */
function updateStrength(v) {
    var wrap  = document.getElementById('sWrap');
    var guide = document.getElementById('pwGuide');
    var lbl   = document.getElementById('sLabel');
    var bars  = ['sb1','sb2','sb3','sb4'].map(function(i){ return document.getElementById(i); });

    var show = v.length > 0;
    wrap.style.display  = show ? 'block' : 'none';
    guide.style.display = show ? 'block' : 'none';
    if (!show) return;

    var rules = {
        'rule-len':     v.length >= 8,
        'rule-num':     /[0-9]/.test(v),
        'rule-lower':   /[a-z]/.test(v),
        'rule-upper':   /[A-Z]/.test(v),
        'rule-special': /[@#$%^&*!]/.test(v)
    };

    Object.keys(rules).forEach(function(id) {
        var row  = document.getElementById(id);
        var icon = row.querySelector('.pw-rule-icon');
        if (rules[id]) { row.classList.add('passed');    icon.textContent = '✓'; }
        else            { row.classList.remove('passed'); icon.textContent = '●'; }
    });

    var score  = Object.values(rules).filter(Boolean).length;
    var filled = score === 0 ? 0 : score === 5 ? 4 : Math.max(1, Math.floor(score * 4 / 5));

    var colors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e'];
    var labels = ['', 'Weak',    'Fair',    'Good',    'Strong' ];

    bars.forEach(function(b, i) {
        b.style.background = i < filled ? colors[filled] : '#e5e7eb';
    });
    lbl.textContent = labels[filled] || '';
    lbl.style.color = colors[filled] || '#9ca3af';
}
</script>

@endsection