@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $periodLabel     = $period?->period_label ?? now()->format('F Y');
    $totalEmployees  = $totalEmployees  ?? 0;
    $totalGross      = (float)($totalGross      ?? 0);
    $totalNet        = (float)($totalNet        ?? 0);
    $totalDeductions = (float)($totalDeductions ?? 0);
    $totalGsis       = (float)($totalGsis       ?? 0);
    $totalWtax       = (float)($totalWtax       ?? 0);
    $totalPhilhealth = (float)($totalPhilhealth ?? 0);
    $totalPagibig    = (float)($totalPagibig    ?? 0);
    $payrollData     = $payrollData ?? [];
    $monthlyTrend    = $monthlyTrend ?? [];
    $pendingLeave    = $pendingLeave ?? 0;
    $pendingHalfDay  = $pendingHalfDay ?? 0;

    // Compute MoM deltas for stat cards
    $trendWithData = array_values(array_filter((array)$monthlyTrend, fn($r) => $r->has_data));
    $deltaGross = $deltaDed = $deltaNet = null;
    if (count($trendWithData) >= 2) {
        $last  = end($trendWithData);
        $prev  = $trendWithData[count($trendWithData) - 2];
        $deltaGross = $prev->total_gross      > 0 ? (($last->total_gross      - $prev->total_gross)      / $prev->total_gross      * 100) : null;
        $deltaDed   = $prev->total_deductions > 0 ? (($last->total_deductions - $prev->total_deductions) / $prev->total_deductions * 100) : null;
        $deltaNet   = $prev->total_net        > 0 ? (($last->total_net        - $prev->total_net)        / $prev->total_net        * 100) : null;
    }
@endphp

{{-- ══════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Card 1: Total Employees --}}
    <div id="card1"
         class="rounded-2xl p-5 flex items-center justify-between shadow-lg opacity-0 translate-y-4 transition-all duration-500 ease-out overflow-hidden relative"
         style="background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 60%,#3d7a2a 100%);">
        <div class="relative z-10">
            <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:rgba(255,255,255,0.55);">Active Employees</p>
            <p class="text-white font-black leading-none mb-2" id="countEmployees" data-target="{{ $totalEmployees }}"
               style="font-size:2.75rem; letter-spacing:-0.03em;">0</p>
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background:rgba(255,255,255,0.15); color:#a3e635;">
                <span style="font-size:8px;">●</span> {{ $periodLabel }}
            </span>
        </div>
        <div class="relative z-10 flex-shrink-0">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.12);">
                <svg class="w-8 h-8" style="color:rgba(255,255,255,0.9);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                             M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                             m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        {{-- decorative ring --}}
        <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full opacity-10" style="background:#a3e635;"></div>
        <div class="absolute -right-3 -bottom-6 w-20 h-20 rounded-full opacity-10" style="background:#fff;"></div>
    </div>

    {{-- Card 2: Total Gross Salary --}}
    <div id="card2"
         class="bg-white rounded-2xl p-5 flex items-center justify-between shadow-lg border opacity-0 translate-y-4 transition-all duration-500 ease-out overflow-hidden relative"
         style="border-color:#f0fdf4;">
        <div class="relative z-10 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Gross Salary</p>
            <p class="font-black text-gray-800 leading-none mb-2" style="font-size:1.85rem; letter-spacing:-0.03em;">
                ₱{{ number_format($totalGross, 0) }}
            </p>
            @if($deltaGross !== null)
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background:{{ $deltaGross >= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $deltaGross >= 0 ? '#16a34a' : '#dc2626' }};">
                @if($deltaGross >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ number_format(abs($deltaGross), 1) }}% from last month
            </span>
            @else
            <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
                  style="background:#f9fafb; color:#9ca3af;">— No prior data</span>
            @endif
        </div>
        <div class="relative z-10 flex-shrink-0">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:#f0fdf4;">
                <svg class="w-8 h-8" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0
                             002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full opacity-5" style="background:#16a34a;"></div>
    </div>

    {{-- Card 3: Total Net Pay --}}
    <div id="card3"
         class="bg-white rounded-2xl p-5 flex items-center justify-between shadow-lg border opacity-0 translate-y-4 transition-all duration-500 ease-out overflow-hidden relative"
         style="border-color:#f0fdf4;">
        <div class="relative z-10 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Net Pay</p>
            <p class="font-black text-gray-800 leading-none mb-2" style="font-size:1.85rem; letter-spacing:-0.03em;">
                ₱{{ number_format($totalNet, 0) }}
            </p>
            @if($deltaNet !== null)
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background:{{ $deltaNet >= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $deltaNet >= 0 ? '#16a34a' : '#dc2626' }};">
                @if($deltaNet >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7M12 3v18"/></svg>
                @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ number_format(abs($deltaNet), 1) }}% after deductions
            </span>
            @else
            <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
                  style="background:#f9fafb; color:#9ca3af;">After all deductions</span>
            @endif
        </div>
        <div class="relative z-10 flex-shrink-0">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:#f0fdf4;">
                <svg class="w-8 h-8" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
        </div>
        <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full opacity-5" style="background:#16a34a;"></div>
    </div>
</div>

{{-- Pending approvals banner --}}
@if($pendingLeave > 0 || $pendingHalfDay > 0)
<div class="rounded-xl px-5 py-3 mb-6 flex items-center gap-3 shadow-sm"
     style="background:#fffbeb; border:1px solid #fde68a;">
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#fef3c7;">
        <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <p class="text-sm font-medium" style="color:#92400e;">
        Pending approvals:
        @if($pendingLeave > 0)
            <strong class="font-bold">{{ $pendingLeave }}</strong> leave application{{ $pendingLeave > 1 ? 's' : '' }}
        @endif
        @if($pendingLeave > 0 && $pendingHalfDay > 0) &nbsp;·&nbsp; @endif
        @if($pendingHalfDay > 0)
            <strong class="font-bold">{{ $pendingHalfDay }}</strong> half-day request{{ $pendingHalfDay > 1 ? 's' : '' }}
        @endif
        awaiting your action.
    </p>
</div>
@endif

{{-- ══════════════════════════════════════════════
     MONTHLY TREND CHART
══════════════════════════════════════════════ --}}
<div id="chartCard"
     class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4 pb-4"
         style="border-bottom:1px solid #f3f4f6;">
        <div>
            <h3 class="text-base font-bold text-gray-800">Payroll Overview</h3>
            <p class="text-xs text-gray-400 mt-0.5">Monthly Gross, Deductions &amp; Net Pay — {{ now()->year }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- MoM delta badges --}}
            @if(count($trendWithData) >= 2)
            @php
                $last2 = end($trendWithData);
                $prev2 = $trendWithData[count($trendWithData) - 2];
            @endphp
            <div class="flex items-center gap-1.5 flex-wrap mr-2">
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full"
                      style="background:{{ $deltaGross >= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $deltaGross >= 0 ? '#16a34a' : '#dc2626' }}; border:1px solid {{ $deltaGross >= 0 ? '#bbf7d0' : '#fecaca' }};">
                    {{ $deltaGross >= 0 ? '↑' : '↓' }} G {{ number_format(abs($deltaGross), 1) }}%
                </span>
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full"
                      style="background:{{ $deltaDed <= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $deltaDed <= 0 ? '#16a34a' : '#dc2626' }}; border:1px solid {{ $deltaDed <= 0 ? '#bbf7d0' : '#fecaca' }};">
                    {{ $deltaDed >= 0 ? '↑' : '↓' }} D {{ number_format(abs($deltaDed), 1) }}%
                </span>
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full"
                      style="background:{{ $deltaNet >= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $deltaNet >= 0 ? '#16a34a' : '#dc2626' }}; border:1px solid {{ $deltaNet >= 0 ? '#bbf7d0' : '#fecaca' }};">
                    {{ $deltaNet >= 0 ? '↑' : '↓' }} N {{ number_format(abs($deltaNet), 1) }}%
                </span>
            </div>
            @endif

            {{-- Dataset toggle pills --}}
            <div class="flex items-center gap-1.5">
                <button data-ds="0" class="ds-toggle text-xs px-3 py-1.5 rounded-full font-semibold transition-all border"
                        style="background:#1a3a1a; color:#fff; border-color:#1a3a1a;">Gross</button>
                <button data-ds="1" class="ds-toggle text-xs px-3 py-1.5 rounded-full font-semibold transition-all border"
                        style="background:#dc3c3c; color:#fff; border-color:#dc3c3c;">Deductions</button>
                <button data-ds="2" class="ds-toggle text-xs px-3 py-1.5 rounded-full font-semibold transition-all border"
                        style="background:#16a34a; color:#fff; border-color:#16a34a;">Net Pay</button>
            </div>
        </div>
    </div>

    <div class="pt-2">
        <canvas id="payrollChart"></canvas>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     BOTTOM ROW
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ── Employee Payroll Summary Table (wider) ── --}}
    <div id="activityCard"
         class="lg:col-span-3 bg-white rounded-2xl shadow-lg border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out overflow-hidden">

        {{-- Table header --}}
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid #f3f4f6;">
            <div>
                <h3 class="text-base font-bold text-gray-800">Employee Payroll Summary</h3>
                <p class="text-xs text-gray-400 mt-0.5">Click any row for full breakdown</p>
            </div>
            <span class="text-xs px-3 py-1.5 rounded-full font-semibold" style="background:#f0fdf4; color:#1a3a1a; border:1px solid #bbf7d0;">
                {{ $periodLabel }}
            </span>
        </div>

        {{-- Column headings --}}
        <div class="px-4" style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left py-2.5 px-2 font-semibold uppercase tracking-wider" style="color:#9ca3af; width:40%;">Employee</th>
                        <th class="text-right py-2.5 px-2 font-semibold uppercase tracking-wider hidden md:table-cell" style="color:#9ca3af;">Gross</th>
                        <th class="text-right py-2.5 px-2 font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:#9ca3af;">Deductions</th>
                        <th class="text-right py-2.5 px-2 font-semibold uppercase tracking-wider" style="color:#9ca3af;">Net Pay</th>
                        <th class="text-center py-2.5 px-2 font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:#9ca3af; width:80px;">Rate</th>
                    </tr>
                </thead>
            </table>
        </div>

        {{-- Scrollable rows --}}
        <div class="overflow-y-auto" style="max-height:360px;">
            <table class="w-full text-sm">
                <tbody>
                    @forelse($payrollData as $i => $emp)
                    @php
                        $empTotDed  = ($emp['gsis'] ?? 0) + ($emp['pagibig'] ?? 0) + ($emp['phic'] ?? 0) + ($emp['wtax'] ?? 0);
                        $empGross   = $emp['gross'] ?? 0;
                        $empNet     = $emp['net'] ?? 0;
                        $dedRate    = $empGross > 0 ? round($empTotDed / $empGross * 100, 1) : 0;
                        $netRate    = $empGross > 0 ? round($empNet / $empGross * 100) : 0;
                        $avatarColors = ['#1a3a1a','#2d5a1b','#3d7a2a','#16a34a','#166534'];
                        $avatarColor  = $avatarColors[$i % count($avatarColors)];
                        $nameParts    = explode(', ', $emp['name'] ?? 'E');
                        $initials     = strtoupper(substr($nameParts[0] ?? 'E', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                    @endphp
                    <tr class="employee-row opacity-0 translate-x-3 transition-all duration-300 cursor-pointer group"
                        style="border-bottom:1px solid #f9fafb;"
                        data-index="{{ $i }}"
                        data-emp="{{ json_encode($emp) }}">

                        {{-- Name + avatar --}}
                        <td class="py-3 px-6" style="width:40%;">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0 group-hover:scale-110 transition-transform"
                                     style="background:{{ $avatarColor }}; font-size:11px;">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-700 text-xs leading-tight truncate group-hover:text-green-700 transition-colors">
                                        {{ $emp['name'] ?? '—' }}
                                    </p>
                                    <p class="text-gray-400 truncate mt-0.5" style="font-size:10px;">{{ $emp['designation'] ?? '' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Gross --}}
                        <td class="py-3 px-2 text-right hidden md:table-cell">
                            <span class="text-xs font-semibold text-gray-700">₱{{ number_format($empGross, 0) }}</span>
                        </td>

                        {{-- Deductions --}}
                        <td class="py-3 px-2 text-right hidden sm:table-cell">
                            <span class="text-xs font-semibold" style="color:#dc2626;">−₱{{ number_format($empTotDed, 0) }}</span>
                        </td>

                        {{-- Net Pay --}}
                        <td class="py-3 px-2 text-right">
                            <span class="text-xs font-bold" style="color:#16a34a;">₱{{ number_format($empNet, 0) }}</span>
                        </td>

                        {{-- Deduction rate mini bar --}}
                        <td class="py-3 px-3 hidden sm:table-cell" style="width:80px;">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs font-semibold" style="color:#6b7280; font-size:10px;">{{ $dedRate }}% ded</span>
                                <div class="w-full rounded-full h-1.5 overflow-hidden" style="background:#f3f4f6;">
                                    <div class="h-1.5 rounded-full" style="background:linear-gradient(90deg,#16a34a,#a3e635); width:{{ $netRate }}%;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:#f9fafb;">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-400">No payroll data for {{ $periodLabel }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Deductions Breakdown Donut ── --}}
    <div id="donutCard"
         class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div class="mb-4" style="border-bottom:1px solid #f3f4f6; padding-bottom:1rem;">
            <h3 class="text-base font-bold text-gray-800">Deductions Breakdown</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $periodLabel }} — All Employees</p>
        </div>

        {{-- Donut --}}
        <div class="flex justify-center mb-5">
            <div class="relative" style="width:180px;height:180px;">
                <canvas id="donutChart" width="180" height="180"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-xs font-medium text-gray-400">Total</p>
                    <p class="font-bold text-gray-800" style="font-size:0.95rem;">₱{{ number_format($totalDeductions, 0) }}</p>
                </div>
            </div>
        </div>

        {{-- Legend rows --}}
        @php
        $donutRows = [
            ['label'=>'GSIS',            'formula'=>'Salary × 9%',             'val'=>$totalGsis,       'color'=>'#1a3a1a'],
            ['label'=>'Withholding Tax', 'formula'=>'BIR 2023 bracket',         'val'=>$totalWtax,       'color'=>'#3d7a2a'],
            ['label'=>'PhilHealth',      'formula'=>'(Salary × 5%) ÷ 2',        'val'=>$totalPhilhealth, 'color'=>'#86efac'],
            ['label'=>'PAG-IBIG',        'formula'=>'min(Salary × 2%, ₱100)',   'val'=>$totalPagibig,    'color'=>'#a3e635'],
        ];
        @endphp

        <div class="space-y-3">
            @foreach($donutRows as $d)
            @php $pct = $totalDeductions > 0 ? round(($d['val'] / $totalDeductions) * 100, 1) : 0; @endphp
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $d['color'] }};"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <span class="text-xs font-semibold text-gray-700">{{ $d['label'] }}</span>
                            <span class="text-gray-400 ml-1.5" style="font-size:10px;">{{ $d['formula'] }}</span>
                        </div>
                        <div class="text-right ml-2 flex-shrink-0">
                            <span class="text-xs font-bold text-gray-700">₱{{ number_format($d['val'], 0) }}</span>
                            <span class="text-gray-400 ml-1" style="font-size:10px;">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="w-full rounded-full h-1.5 overflow-hidden" style="background:#f3f4f6;">
                        <div class="h-1.5 rounded-full donut-bar transition-all duration-700"
                             style="background:{{ $d['color'] }}; width:0%;"
                             data-w="{{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Net formula chip --}}
        <div class="rounded-xl px-4 py-3 mt-4" style="background:#f0fdf4; border:1px solid #dcfce7;">
            <p class="text-xs font-bold mb-0.5" style="color:#1a3a1a;">Net Pay Formula</p>
            <p class="text-xs leading-relaxed" style="color:#3d7a2a;">
                Gross &minus; GSIS &minus; PAG-IBIG &minus; PhilHealth &minus; W.Tax &minus; Loans + Allowances
            </p>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     EMPLOYEE DETAIL DRAWER
══════════════════════════════════════════════ --}}
<div id="empDrawer"
     class="fixed inset-y-0 right-0 z-50 w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
     style="border-left:1px solid #e5e7eb;">
    <div class="flex items-center justify-between px-5 py-4" style="background:linear-gradient(135deg,#1a3a1a,#2d5a1b);">
        <div>
            <h4 class="text-sm font-bold text-white">Payroll Detail</h4>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.55);">{{ $periodLabel }}</p>
        </div>
        <button id="closeDrawer" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                style="background:rgba(255,255,255,0.1);" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto p-5" id="drawerContent"></div>
</div>
<div id="drawerBackdrop"
     class="fixed inset-0 bg-black opacity-0 pointer-events-none z-40 transition-opacity duration-300"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Fade-in cards ──────────────────────────────────────────────
    [
        {id:'card1',delay:100},{id:'card2',delay:200},{id:'card3',delay:300},
        {id:'chartCard',delay:400},{id:'activityCard',delay:500},{id:'donutCard',delay:600},
    ].forEach(({id,delay}) => setTimeout(() => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('opacity-0','translate-y-4'); el.classList.add('opacity-100','translate-y-0'); }
    }, delay));

    // ── Count-up ───────────────────────────────────────────────────
    const countEl = document.getElementById('countEmployees');
    if (countEl) {
        const target = parseInt(countEl.dataset.target) || 0;
        let c = 0;
        const step = Math.max(1, Math.ceil(target / 40));
        const tm = setInterval(() => { c = Math.min(c + step, target); countEl.textContent = c; if (c >= target) clearInterval(tm); }, 40);
    }

    // ── Employee rows animate ──────────────────────────────────────
    document.querySelectorAll('.employee-row').forEach((el, i) => {
        setTimeout(() => { el.classList.remove('opacity-0','translate-x-3'); el.classList.add('opacity-100','translate-x-0'); }, 600 + i * 35);
    });

    // ── Donut bars animate ─────────────────────────────────────────
    setTimeout(() => {
        document.querySelectorAll('.donut-bar').forEach(b => { b.style.width = b.dataset.w; });
    }, 800);

    // ── Row hover highlight ────────────────────────────────────────
    document.querySelectorAll('.employee-row').forEach(row => {
        row.addEventListener('mouseenter', () => { row.style.background = '#f0fdf4'; });
        row.addEventListener('mouseleave', () => { row.style.background = ''; });
    });

    // ── Employee detail drawer ─────────────────────────────────────
    const drawer   = document.getElementById('empDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const content  = document.getElementById('drawerContent');

    function openDrawer(emp) {
        const totDed = (emp.gsis||0)+(emp.pagibig||0)+(emp.phic||0)+(emp.wtax||0);
        const fmt = v => '₱' + parseFloat(v||0).toLocaleString('en-PH',{minimumFractionDigits:2});
        const pct = v => totDed > 0 ? (v/totDed*100).toFixed(1)+'%' : '0%';
        const dedItems = [
            {label:'GSIS',           formula:'× 9%',           val:emp.gsis||0,    color:'#1a3a1a'},
            {label:'PAG-IBIG',       formula:'× 2% max ₱100',  val:emp.pagibig||0, color:'#a3e635'},
            {label:'PhilHealth',     formula:'× 5% ÷ 2',       val:emp.phic||0,    color:'#86efac'},
            {label:'Withholding Tax',formula:'BIR bracket',     val:emp.wtax||0,    color:'#3d7a2a'},
        ];

        const nameParts = (emp.name||'E').split(', ');
        const initials  = ((nameParts[0]||'E')[0] + (nameParts[1]||'')[0]).toUpperCase();

        let html = `
        <div class="flex items-center gap-3 p-4 rounded-xl mb-5" style="background:#f9fafb; border:1px solid #f3f4f6;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                 style="background:#1a3a1a;">${initials}</div>
            <div>
                <p class="font-bold text-gray-800 text-sm leading-tight">${emp.name||'—'}</p>
                <p class="text-xs text-gray-400 mt-0.5">${emp.designation||''}</p>
            </div>
        </div>

        <div class="flex items-center justify-between p-3 rounded-xl mb-4" style="background:#f0fdf4; border:1px solid #dcfce7;">
            <div>
                <p style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">Gross Salary</p>
                <p class="font-black text-gray-800" style="font-size:1.4rem; letter-spacing:-0.02em;">₱${parseFloat(emp.gross||0).toLocaleString('en-PH',{minimumFractionDigits:0})}</p>
            </div>
            <svg class="w-7 h-7" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>

        <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:8px;">Deductions</p>
        <div class="space-y-2 mb-5">`;

        dedItems.forEach(r => {
            html += `
            <div class="flex items-center justify-between p-2.5 rounded-lg" style="background:#f9fafb;">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:${r.color};"></div>
                    <div>
                        <p class="text-xs font-semibold text-gray-700">${r.label}</p>
                        <p style="font-size:10px; color:#9ca3af;">Salary ${r.formula}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold" style="color:#dc2626;">−${fmt(r.val)}</p>
                    <p style="font-size:10px; color:#9ca3af;">${pct(r.val)}</p>
                </div>
            </div>`;
        });

        html += `</div>
        <div style="border-top:2px dashed #f3f4f6; margin-bottom:16px;"></div>
        <div class="flex items-center justify-between p-4 rounded-xl" style="background:linear-gradient(135deg,#1a3a1a,#2d5a1b);">
            <div>
                <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:rgba(255,255,255,0.6);">Net Pay</p>
                <p class="font-black text-white" style="font-size:1.4rem; letter-spacing:-0.02em;">₱${parseFloat(emp.net||0).toLocaleString('en-PH',{minimumFractionDigits:0})}</p>
            </div>
            <div class="text-right">
                <p style="font-size:10px; color:rgba(255,255,255,0.5);">Take-home</p>
                <p class="font-semibold" style="color:#a3e635; font-size:0.85rem;">${totDed > 0 ? (((emp.net||0)/(emp.gross||1))*100).toFixed(1) : 0}% of Gross</p>
            </div>
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
            aspectRatio: isMobile() ? 1.4 : 2.8,
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
            chart.options.aspectRatio = isMobile() ? 1.4 : 2.8;
            chart.update();
        }, 200);
    });

    // ── Deductions Donut ───────────────────────────────────────────
    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['GSIS (9%)', 'W-Tax (BIR)', 'PhilHealth (5%/2)', 'PAG-IBIG (2%/₱100)'],
            datasets: [{
                data: [{{ $totalGsis }}, {{ $totalWtax }}, {{ $totalPhilhealth }}, {{ $totalPagibig }}],
                backgroundColor: ['#1a3a1a', '#3d7a2a', '#86efac', '#a3e635'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: false,
            cutout: '70%',
            animation: { animateRotate: true, duration: 1000, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => ' ₱' + c.raw.toLocaleString('en-PH', { minimumFractionDigits: 2 })
                    }
                }
            }
        }
    });
});
</script>
@endsection