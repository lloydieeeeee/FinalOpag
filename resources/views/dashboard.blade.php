@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $periodLabel      = $period?->period_label ?? now()->format('F Y');
    $totalEmployees   = $totalEmployees  ?? 0;
    $totalGross       = (float)($totalGross      ?? 0);
    $totalNet         = (float)($totalNet        ?? 0);
    $totalDeductions  = (float)($totalDeductions ?? 0);
    $totalGsis        = (float)($totalGsis       ?? 0);
    $totalWtax        = (float)($totalWtax       ?? 0);
    $totalPhilhealth  = (float)($totalPhilhealth ?? 0);
    $totalPagibig     = (float)($totalPagibig    ?? 0);
    $payrollData      = $payrollData ?? [];
    $monthlyTrend     = $monthlyTrend ?? [];
    $pendingLeave     = $pendingLeave ?? 0;
    $pendingHalfDay   = $pendingHalfDay ?? 0;
    $onLeave          = $onLeave ?? 0;

    // Payroll chart MoM deltas (percentages)
    $deltaGross = $deltaGross ?? null;
    $deltaDed   = $deltaDed   ?? null;
    $deltaNet   = $deltaNet   ?? null;

    // Stat card MoM deltas (absolute counts)
    $deltaEmployees      = $deltaEmployees      ?? null;
    $deltaOnLeave        = $deltaOnLeave        ?? null;
    $deltaPendingLeave   = $deltaPendingLeave   ?? null;
    $deltaPendingHalfDay = $deltaPendingHalfDay ?? null;

    // Helper: render a delta badge for count-based stats
    // Returns an array: ['class' => ..., 'icon' => 'up|down|neutral', 'text' => ...]
    $makeDeltaBadge = function(?int $delta, bool $darkStyle = false) {
        if ($delta === null) {
            return ['dir' => 'neutral', 'text' => 'No prior data'];
        }
        if ($delta > 0) {
            return ['dir' => 'up', 'text' => '+' . $delta . ' from last month'];
        }
        if ($delta < 0) {
            return ['dir' => 'down', 'text' => $delta . ' from last month'];
        }
        return ['dir' => 'neutral', 'text' => '—'];
    };

    $badgeEmployees      = $makeDeltaBadge($deltaEmployees,      true);
    $badgeOnLeave        = $makeDeltaBadge($deltaOnLeave);
    $badgePendingLeave   = $makeDeltaBadge($deltaPendingLeave);
    $badgePendingHalfDay = $makeDeltaBadge($deltaPendingHalfDay);
@endphp

<style>
    .dash-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        padding: 1.1rem 1.25rem 1rem;
        transition: box-shadow .18s, transform .18s;
    }
    .dash-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.10); transform: translateY(-1px); }
    .dash-card-dark {
        background: linear-gradient(135deg,#16301c 0%,#1e4d24 60%,#275e2b 100%);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.14);
        padding: 1.1rem 1.25rem 1rem;
        transition: box-shadow .18s, transform .18s;
    }
    .dash-card-dark:hover { box-shadow: 0 4px 18px rgba(22,163,74,0.18); transform: translateY(-1px); }
    .stat-label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 0.3rem;
    }
    .stat-value {
        font-size: 1.7rem;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: #111827;
        line-height: 1;
    }
    .delta-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        margin-top: 0.45rem;
    }
    .delta-up      { background:#f0fdf4; color:#16a34a; }
    .delta-down    { background:#fef2f2; color:#dc2626; }
    .delta-neutral { background:#f9fafb; color:#9ca3af; }
    .icon-box {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .section-title {
        font-size: 0.8rem;
        font-weight: 800;
        color: #111827;
        letter-spacing: 0.01em;
    }
    .section-sub {
        font-size: 0.68rem;
        color: #9ca3af;
        margin-top: 1px;
    }
</style>

{{-- ══════════════════════════════════════════════
     TOP STAT CARDS ROW
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    {{-- Card 1: Total Employees --}}
    <div class="dash-card flex items-center justify-between overflow-hidden relative">
        <div>
            <p class="stat-label">Total Employees</p>
            <p class="stat-value" id="countEmployees" data-target="{{ $totalEmployees }}">0</p>
            @php $b = $badgeEmployees; @endphp
            <span class="delta-badge delta-{{ $b['dir'] }}">
                @if($b['dir'] === 'up')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @elseif($b['dir'] === 'down')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                @endif
                {{ $b['text'] }}
            </span>
        </div>
        <div class="icon-box" style="background:#f0fdf4;">
            <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 00-9.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    </div>

    {{-- Card 2: Employees on Leave --}}
    <div class="dash-card flex items-center justify-between overflow-hidden relative">
        <div>
            <p class="stat-label">Employees on Leave</p>
            <p class="stat-value" id="countOnLeave" data-target="{{ $onLeave }}">0</p>
            @php $b = $badgeOnLeave; @endphp
            <span class="delta-badge delta-{{ $b['dir'] }}">
                @if($b['dir'] === 'up')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @elseif($b['dir'] === 'down')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                @endif
                {{ $b['text'] }}
            </span>
        </div>
        <div class="icon-box" style="background:#f0fdf4;">
            <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    </div>

    {{-- Card 3: Leave Pending --}}
    <div class="dash-card flex items-center justify-between overflow-hidden relative">
        <div>
            <p class="stat-label">Leave Pending</p>
            <p class="stat-value" id="countLeavePending" data-target="{{ $pendingLeave }}">0</p>
            @php $b = $badgePendingLeave; @endphp
            <span class="delta-badge delta-{{ $b['dir'] }}">
                @if($b['dir'] === 'up')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @elseif($b['dir'] === 'down')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                @endif
                {{ $b['text'] }}
            </span>
        </div>
        <div class="icon-box" style="background:#f0fdf4;">
            <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
    </div>

    {{-- Card 4: Half-Day Pending --}}
    <div class="dash-card flex items-center justify-between overflow-hidden relative">
        <div>
            <p class="stat-label">Half-Day Pending</p>
            <p class="stat-value" id="countHalfDay" data-target="{{ $pendingHalfDay }}">0</p>
            @php $b = $badgePendingHalfDay; @endphp
            <span class="delta-badge delta-{{ $b['dir'] }}">
                @if($b['dir'] === 'up')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @elseif($b['dir'] === 'down')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                @endif
                {{ $b['text'] }}
            </span>
        </div>
        <div class="icon-box" style="background:#f0fdf4;">
            <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MAIN CONTENT ROW: Chart + Right Panels
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ── LEFT: Payroll Overview Chart ── --}}
    <div class="lg:col-span-2 dash-card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="section-title">Payroll Overview</p>
                <p class="section-sub">Gross, Deductions &amp; Net Pay — {{ now()->year }}</p>
            </div>
            {{-- Year selector chip --}}
            <div class="flex items-center gap-2">
                @php
                    $trendWithData = array_values(array_filter((array)$monthlyTrend, fn($r) => $r->has_data));
                @endphp
                @if(count($trendWithData) >= 2)
                <div class="flex gap-1.5 flex-wrap">
                    <span class="delta-badge {{ ($deltaGross ?? 0) >= 0 ? 'delta-up' : 'delta-down' }}">
                        {{ ($deltaGross ?? 0) >= 0 ? '↑' : '↓' }} G {{ number_format(abs($deltaGross ?? 0), 1) }}%
                    </span>
                    <span class="delta-badge {{ ($deltaNet ?? 0) >= 0 ? 'delta-up' : 'delta-down' }}">
                        {{ ($deltaNet ?? 0) >= 0 ? '↑' : '↓' }} N {{ number_format(abs($deltaNet ?? 0), 1) }}%
                    </span>
                </div>
                @endif
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                     style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                    Year <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        {{-- Dataset toggle pills --}}
        <div class="flex items-center gap-2 mb-3 flex-wrap">
            <button data-ds="0" class="ds-toggle text-xs px-3 py-1 rounded-full font-semibold transition-all border"
                    style="background:#16301c; color:#fff; border-color:#16301c;">Gross</button>
            <button data-ds="1" class="ds-toggle text-xs px-3 py-1 rounded-full font-semibold transition-all border"
                    style="background:#dc3c3c; color:#fff; border-color:#dc3c3c;">Deductions</button>
            <button data-ds="2" class="ds-toggle text-xs px-3 py-1 rounded-full font-semibold transition-all border"
                    style="background:#16a34a; color:#fff; border-color:#16a34a;">Net Pay</button>
        </div>

        <canvas id="payrollChart"></canvas>
    </div>

    {{-- ── RIGHT: Gross + Net cards + Upcoming Leaves ── --}}
    <div class="flex flex-col gap-4">

        {{-- Total Gross Salary --}}
        <div class="dash-card flex items-center justify-between">
            <div>
                <p class="stat-label">Total Gross Salary</p>
                <p class="stat-value" style="font-size:1.5rem;">₱{{ number_format($totalGross, 0) }}</p>
                @if($deltaGross !== null)
                <span class="delta-badge {{ $deltaGross >= 0 ? 'delta-up' : 'delta-down' }}">
                    @if($deltaGross >= 0)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                    @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    @endif
                    {{ number_format(abs($deltaGross), 1) }}% · {{ $periodLabel }}
                </span>
                @else
                <span class="delta-badge delta-neutral">{{ $periodLabel }}</span>
                @endif
            </div>
            <div class="icon-box" style="background:#f0fdf4;">
                <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total Net Pay --}}
        <div class="dash-card flex items-center justify-between">
            <div>
                <p class="stat-label">Total Net Pay</p>
                <p class="stat-value" style="font-size:1.5rem;">₱{{ number_format($totalNet, 0) }}</p>
                @if($deltaNet !== null)
                <span class="delta-badge {{ $deltaNet >= 0 ? 'delta-up' : 'delta-down' }}">
                    @if($deltaNet >= 0)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                    @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    @endif
                    After all deductions
                </span>
                @else
                <span class="delta-badge delta-neutral">After all deductions</span>
                @endif
            </div>
            <div class="icon-box" style="background:#f0fdf4;">
                <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
        </div>

        {{-- Upcoming Approved Leaves --}}
        <div class="dash-card flex-1 overflow-hidden" style="min-height:0;">
            @php
                $activeLeaves = $activeLeaves ?? collect();
                $totalLeaves  = $activeLeaves->count();
            @endphp

            <div class="flex items-center justify-between mb-3" style="padding-bottom:0.65rem; border-bottom:1px solid #f3f4f6;">
                <div>
                    <p class="section-title">Upcoming Approved Leaves</p>
                    <p class="section-sub">Current &amp; future approved leaves</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-bold"
                      style="background:{{ $totalLeaves > 0 ? '#fef2f2' : '#f0fdf4' }}; color:{{ $totalLeaves > 0 ? '#dc2626' : '#16a34a' }}; border:1px solid {{ $totalLeaves > 0 ? '#fecaca' : '#bbf7d0' }};">
                    {{ $totalLeaves }} on leave
                </span>
            </div>

            <div class="overflow-y-auto space-y-2" style="max-height:320px;">

                @forelse($activeLeaves as $i => $la)
                @php
                    $lemp      = $la->employee;
                    $lName     = optional($lemp)->last_name . ', ' . optional($lemp)->first_name;
                    $lInitials = strtoupper(substr(optional($lemp)->last_name ?? 'E', 0, 1) . substr(optional($lemp)->first_name ?? '', 0, 1));
                    $lColors   = ['#16301c','#1e4d24','#275e2b','#15803d','#166534'];
                    $lBg       = $lColors[$i % count($lColors)];
                    $lStart    = \Carbon\Carbon::parse($la->start_date);
                    $lEnd      = \Carbon\Carbon::parse($la->end_date);
                    $lDays     = $lStart->diffInDays($lEnd) + 1;
                    $lType     = optional($la->leaveType)->type_name ?? 'Leave';

                    $today     = now()->startOfDay();
                    $isOngoing = $lStart->lte($today) && $lEnd->gte($today);
                    $isUpcoming= $lStart->gt($today);
                    $daysUntil = $today->diffInDays($lStart->startOfDay(), false);
                    $daysLeft  = (int) $today->diffInDays($lEnd->startOfDay(), false) + 1;
                @endphp
                <div class="flex items-start gap-2.5 p-2.5 rounded-xl"
                     style="background:#f9fafb; border:1px solid #f3f4f6;"
                     onmouseenter="this.style.background='#f0fdf4';this.style.borderColor='#bbf7d0';"
                     onmouseleave="this.style.background='#f9fafb';this.style.borderColor='#f3f4f6';">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0"
                         style="background:{{ $lBg }}; font-size:10px;">{{ $lInitials }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-1">
                            <p class="text-xs font-semibold text-gray-700 leading-tight truncate">{{ $lName }}</p>
                            @if($isOngoing)
                                <span class="font-bold flex-shrink-0 px-1.5 py-0.5 rounded-md"
                                      style="background:#fef2f2; color:#dc2626; font-size:9px; white-space:nowrap;">On Leave</span>
                            @else
                                <span class="font-bold flex-shrink-0 px-1.5 py-0.5 rounded-md"
                                      style="background:#fffbeb; color:#d97706; font-size:9px; white-space:nowrap;">Upcoming</span>
                            @endif
                        </div>
                        <p class="text-gray-400 mt-0.5" style="font-size:10px;">{{ $lType }}</p>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="flex items-center gap-1 font-semibold text-gray-500" style="font-size:10px;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $lStart->format('M d') }} – {{ $lEnd->format('M d, Y') }}
                            </span>
                            <span style="font-size:10px; color:#6b7280;">
                                {{ $lDays }} day{{ $lDays > 1 ? 's' : '' }}
                                @if($isOngoing)
                                    · <span style="color:#f59e0b;">{{ $daysLeft }}d left</span>
                                @else
                                    · <span style="color:#6b7280;">starts in {{ $daysUntil }}d</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 gap-2">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                        <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-400">No upcoming approved leaves</p>
                </div>
                @endforelse

            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     EMPLOYEE PAYROLL TABLE (full width below)
══════════════════════════════════════════════ --}}
<div class="dash-card mt-4 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="section-title">Employee Payroll Summary</p>
            <p class="section-sub">Click any row for full breakdown</p>
        </div>
        <span class="text-xs px-3 py-1.5 rounded-full font-semibold"
              style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
            {{ $periodLabel }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                    <th class="text-left py-2.5 px-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Employee</th>
                    <th class="text-right py-2.5 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 hidden md:table-cell">Gross</th>
                    <th class="text-right py-2.5 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 hidden sm:table-cell">Deductions</th>
                    <th class="text-right py-2.5 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Net Pay</th>
                    <th class="text-center py-2.5 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 hidden sm:table-cell" style="width:90px;">Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollData as $i => $emp)
                @php
                    $empTotDed  = ($emp['gsis'] ?? 0) + ($emp['pagibig'] ?? 0) + ($emp['phic'] ?? 0) + ($emp['wtax'] ?? 0);
                    $empGross   = $emp['gross'] ?? 0;
                    $empNet     = $emp['net']   ?? 0;
                    $dedRate    = $empGross > 0 ? round($empTotDed / $empGross * 100, 1) : 0;
                    $netRate    = $empGross > 0 ? round($empNet / $empGross * 100) : 0;
                    $avatarColors = ['#16301c','#1e4d24','#275e2b','#16a34a','#15803d'];
                    $avatarColor  = $avatarColors[$i % count($avatarColors)];
                    $nameParts    = explode(', ', $emp['name'] ?? 'E');
                    $initials     = strtoupper(substr($nameParts[0] ?? 'E', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                @endphp
                <tr class="employee-row cursor-pointer group transition-colors"
                    style="border-bottom:1px solid #f9fafb;"
                    data-emp="{{ json_encode($emp) }}"
                    onmouseenter="this.style.background='#f0fdf4'"
                    onmouseleave="this.style.background=''">
                    <td class="py-3 px-4" style="width:35%;">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0 group-hover:scale-105 transition-transform"
                                 style="background:{{ $avatarColor }}; font-size:10px;">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-700 text-xs leading-tight truncate group-hover:text-green-700 transition-colors">
                                    {{ $emp['name'] ?? '—' }}
                                </p>
                                <p class="text-gray-400 truncate" style="font-size:10px;">{{ $emp['designation'] ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-3 text-right hidden md:table-cell">
                        <span class="text-xs font-semibold text-gray-700">₱{{ number_format($empGross, 0) }}</span>
                    </td>
                    <td class="py-3 px-3 text-right hidden sm:table-cell">
                        <span class="text-xs font-semibold" style="color:#dc2626;">−₱{{ number_format($empTotDed, 0) }}</span>
                    </td>
                    <td class="py-3 px-3 text-right">
                        <span class="text-xs font-bold" style="color:#16a34a;">₱{{ number_format($empNet, 0) }}</span>
                    </td>
                    <td class="py-3 px-3 hidden sm:table-cell" style="width:90px;">
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-gray-400" style="font-size:10px; font-weight:600;">{{ $dedRate }}% ded</span>
                            <div class="w-full rounded-full h-1.5 overflow-hidden" style="background:#f3f4f6;">
                                <div class="h-1.5 rounded-full" style="background:linear-gradient(90deg,#16a34a,#a3e635); width:{{ $netRate }}%;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:#f9fafb;">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400">No payroll data for {{ $periodLabel }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     EMPLOYEE DETAIL DRAWER
══════════════════════════════════════════════ --}}
<div id="empDrawer"
     class="fixed inset-y-0 right-0 z-50 w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
     style="border-left:1px solid #e5e7eb;">
    <div class="flex items-center justify-between px-5 py-4"
         style="background:linear-gradient(135deg,#16301c,#1e4d24);">
        <div>
            <h4 class="text-sm font-bold text-white">Payroll Detail</h4>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.55);">{{ $periodLabel }}</p>
        </div>
        <button id="closeDrawer" class="w-8 h-8 rounded-lg flex items-center justify-center"
                style="background:rgba(255,255,255,0.1);"
                onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                onmouseout="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto p-5" id="drawerContent"></div>
</div>
<div id="drawerBackdrop" class="fixed inset-0 bg-black opacity-0 pointer-events-none z-40 transition-opacity duration-300"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Count-up animations ────────────────────────────────────────
    ['countEmployees','countOnLeave','countLeavePending','countHalfDay'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const target = parseInt(el.dataset.target) || 0;
        let c = 0;
        const step = Math.max(1, Math.ceil(target / 40));
        const tm = setInterval(() => { c = Math.min(c + step, target); el.textContent = c.toLocaleString(); if (c >= target) clearInterval(tm); }, 35);
    });

    // ── Employee detail drawer ─────────────────────────────────────
    const drawer   = document.getElementById('empDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const content  = document.getElementById('drawerContent');

    function openDrawer(emp) {
        const totDed = (emp.gsis||0)+(emp.pagibig||0)+(emp.phic||0)+(emp.wtax||0);
        const fmt = v => '₱' + parseFloat(v||0).toLocaleString('en-PH',{minimumFractionDigits:2});
        const dedItems = [
            {label:'GSIS',           val:emp.gsis||0,    color:'#16301c'},
            {label:'PAG-IBIG',       val:emp.pagibig||0, color:'#a3e635'},
            {label:'PhilHealth',     val:emp.phic||0,    color:'#86efac'},
            {label:'Withholding Tax',val:emp.wtax||0,    color:'#3d7a2a'},
        ];
        const nameParts = (emp.name||'E').split(', ');
        const initials  = ((nameParts[0]||'E')[0]+(nameParts[1]||'')[0]).toUpperCase();

        let html = `
        <div class="flex items-center gap-3 p-3 rounded-xl mb-4" style="background:#f9fafb; border:1px solid #f3f4f6;">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                 style="background:#16301c;">${initials}</div>
            <div>
                <p class="font-bold text-gray-800 text-sm">${emp.name||'—'}</p>
                <p class="text-xs text-gray-400">${emp.designation||''}</p>
            </div>
        </div>
        <div class="flex items-center justify-between p-3 rounded-xl mb-4" style="background:#f0fdf4; border:1px solid #dcfce7;">
            <div>
                <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280;">Gross Salary</p>
                <p class="font-black text-gray-800" style="font-size:1.3rem;">₱${parseFloat(emp.gross||0).toLocaleString('en-PH',{minimumFractionDigits:0})}</p>
            </div>
            <svg class="w-6 h-6" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:8px;">Deductions</p>
        <div class="space-y-2 mb-4">`;

        dedItems.forEach(r => {
            html += `
            <div class="flex items-center justify-between p-2.5 rounded-lg" style="background:#f9fafb;">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full" style="background:${r.color};"></div>
                    <span class="text-xs font-semibold text-gray-700">${r.label}</span>
                </div>
                <span class="text-xs font-bold" style="color:#dc2626;">−${fmt(r.val)}</span>
            </div>`;
        });

        html += `</div>
        <div style="border-top:2px dashed #f3f4f6; margin-bottom:12px;"></div>
        <div class="flex items-center justify-between p-4 rounded-xl" style="background:linear-gradient(135deg,#16301c,#1e4d24);">
            <div>
                <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:rgba(255,255,255,0.55);">Net Pay</p>
                <p class="font-black text-white" style="font-size:1.3rem;">₱${parseFloat(emp.net||0).toLocaleString('en-PH',{minimumFractionDigits:0})}</p>
            </div>
            <p class="font-semibold" style="color:#a3e635;">${emp.gross > 0 ? ((emp.net/emp.gross)*100).toFixed(1) : 0}% of Gross</p>
        </div>`;

        content.innerHTML = html;
        drawer.classList.remove('translate-x-full');
        backdrop.classList.remove('opacity-0','pointer-events-none');
        backdrop.classList.add('opacity-30','pointer-events-auto');
    }

    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        backdrop.classList.remove('opacity-30','pointer-events-auto');
        backdrop.classList.add('opacity-0','pointer-events-none');
    }

    document.querySelectorAll('.employee-row').forEach(row => {
        row.addEventListener('click', () => {
            try { openDrawer(JSON.parse(row.dataset.emp)); } catch(e) {}
        });
    });
    document.getElementById('closeDrawer').addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);

    // ══════════════════════════════════════════════
    //  MONTHLY TREND CHART — Jan–Dec current year
    // ══════════════════════════════════════════════
    const monthlyTrend = @json($monthlyTrend);

    const tLabels = monthlyTrend.map(r => r.period_label);
    const tGross  = monthlyTrend.map(r => r.has_data ? (parseFloat(r.total_gross)      || 0) : null);
    const tDed    = monthlyTrend.map(r => r.has_data ? (parseFloat(r.total_deductions) || 0) : null);
    const tNet    = monthlyTrend.map(r => r.has_data ? (parseFloat(r.total_net)        || 0) : null);

    const ctx = document.getElementById('payrollChart').getContext('2d');

    const makeGrad = (r, g, b, opacity) => {
        const grd = ctx.createLinearGradient(0, 0, 0, 300);
        grd.addColorStop(0, `rgba(${r},${g},${b},${opacity})`);
        grd.addColorStop(1, `rgba(${r},${g},${b},0)`);
        return grd;
    };

    const isMobile = () => window.innerWidth < 640;

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: tLabels,
            datasets: [
                {
                    label: 'Gross Salary',
                    data: tGross,
                    spanGaps: false,
                    fill: true,
                    backgroundColor: makeGrad(26,58,26,0.7),
                    borderColor: '#1a3a1a',
                    borderWidth: 2.5,
                    pointRadius: monthlyTrend.map(r => r.has_data ? 5 : 0),
                    pointHoverRadius: monthlyTrend.map(r => r.has_data ? 7 : 0),
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1a3a1a',
                    pointBorderWidth: 2,
                    tension: 0.4,
                },
                {
                    label: 'Total Deductions',
                    data: tDed,
                    spanGaps: false,
                    fill: true,
                    backgroundColor: makeGrad(220,60,60,0.5),
                    borderColor: '#dc3c3c',
                    borderWidth: 2.5,
                    pointRadius: monthlyTrend.map(r => r.has_data ? 5 : 0),
                    pointHoverRadius: monthlyTrend.map(r => r.has_data ? 7 : 0),
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#dc3c3c',
                    pointBorderWidth: 2,
                    tension: 0.4,
                },
                {
                    label: 'Net Pay',
                    data: tNet,
                    spanGaps: false,
                    fill: true,
                    backgroundColor: makeGrad(22,163,74,0.5),
                    borderColor: '#16a34a',
                    borderWidth: 2.5,
                    pointRadius: monthlyTrend.map(r => r.has_data ? 5 : 0),
                    pointHoverRadius: monthlyTrend.map(r => r.has_data ? 7 : 0),
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#16a34a',
                    pointBorderWidth: 2,
                    tension: 0.4,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: isMobile() ? 1.0 : 1.6,
            interaction: { mode: 'index', intersect: false },
            animation: { duration: 900, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    titleColor: '#111827',
                    titleFont: { weight: '700', size: 12 },
                    bodyColor: '#6b7280',
                    bodyFont: { size: 12 },
                    padding: 14,
                    cornerRadius: 10,
                    filter: item => item.raw !== null,
                    callbacks: {
                        title: items => items[0].label,
                        label: c => {
                            if (c.raw === null) return null;
                            const icons = ['💰', '📉', '✅'];
                            return `  ${icons[c.datasetIndex]} ${c.dataset.label}: ₱${c.raw.toLocaleString('en-PH',{minimumFractionDigits:0})}`;
                        },
                        afterBody: items => {
                            const validItems = items.filter(i => i.raw !== null);
                            const gross = validItems.find(i => i.datasetIndex === 0)?.raw || 0;
                            const ded   = validItems.find(i => i.datasetIndex === 1)?.raw || 0;
                            if (gross > 0 && ded > 0) {
                                return ['  ─────────────────', `  Deduction Rate: ${(ded/gross*100).toFixed(1)}%`];
                            }
                            return [];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        maxRotation: 0,
                        autoSkip: false,
                        maxTicksLimit: 12,
                    }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.035)', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: v => '₱' + (v >= 1000000
                            ? (v / 1000000).toFixed(1) + 'M'
                            : (v / 1000).toFixed(0) + 'k'),
                        maxTicksLimit: 6,
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // ── Dataset toggle pills ───────────────────────────────────────
    const pillActive = {
        0: { bg: '#1a3a1a', text: '#fff', border: '#1a3a1a' },
        1: { bg: '#dc3c3c', text: '#fff', border: '#dc3c3c' },
        2: { bg: '#16a34a', text: '#fff', border: '#16a34a' },
    };

    document.querySelectorAll('.ds-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const dsIndex = parseInt(this.dataset.ds);
            const ds = chart.data.datasets[dsIndex];
            ds.hidden = !ds.hidden;
            chart.update();

            if (ds.hidden) {
                this.style.background   = '#f9fafb';
                this.style.color        = '#9ca3af';
                this.style.borderColor  = '#e5e7eb';
            } else {
                const s = pillActive[dsIndex];
                this.style.background  = s.bg;
                this.style.color       = s.text;
                this.style.borderColor = s.border;
            }
        });
    });

    // ── Resize ─────────────────────────────────────────────────────
    let rt;
    window.addEventListener('resize', () => {
        clearTimeout(rt);
        rt = setTimeout(() => {
            chart.options.aspectRatio = isMobile() ? 1.0 : 1.6;
            chart.update();
        }, 200);
    });

});
</script>
@endsection