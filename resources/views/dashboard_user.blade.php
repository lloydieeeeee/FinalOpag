@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $employee = Auth::user()->employee;

    // Guard: if no employee record linked to this user, show a friendly message
    if (!$employee) {
        $noEmployee = true;
    } else {
        $noEmployee = false;

        $fullName = trim(
            $employee->first_name . ' ' .
            ($employee->middle_name ? $employee->middle_name . ' ' : '') .
            $employee->last_name
        );
        $position    = $employee->position->position_name ?? 'N/A';
        $periodLabel = $period?->period_label ?? now()->format('F Y');

        // ── All values from payroll_record via DashboardController ──
        $gross   = (float)($gross   ?? $employee->salary ?? 0);
        $net     = (float)($net     ?? 0);
        $gsis    = (float)($gsis    ?? round($gross * 0.09, 2));
        $pagibig = (float)($pagibig ?? min(round($gross * 0.02, 2), 100));
        $phic    = (float)($phic    ?? min(max(round(($gross * 0.05) / 2, 2), 500), 5000));
        $wtax    = (float)($wtax    ?? 0);
        $totDed  = (float)($totDed  ?? ($gsis + $pagibig + $phic + $wtax));

        if ($net === 0.0 && $gross > 0) { $net = $gross - $totDed; }

        $pendingLeave   = $pendingLeave   ?? 0;
        $pendingHalfDay = $pendingHalfDay ?? 0;
    }
@endphp

{{-- ══════════════════════════════════════════════
     NO EMPLOYEE RECORD FALLBACK
══════════════════════════════════════════════ --}}
@if($noEmployee)
<div class="rounded-xl p-8 flex flex-col items-center justify-center shadow text-center" style="background:#1a3a1a; min-height:200px;">
    <svg class="w-12 h-12 mb-3 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <h2 class="text-xl font-bold text-white mb-1">No Employee Record Found</h2>
    <p class="text-sm" style="color:rgba(255,255,255,0.55);">
        Your account is not linked to an employee profile yet.<br>
        Please contact your administrator to set up your employee record.
    </p>
</div>
@else

{{-- ══════════════════════════════════════════════
     WELCOME BANNER
══════════════════════════════════════════════ --}}
<div id="banner"
     class="rounded-xl p-5 mb-6 flex items-center justify-between shadow opacity-0 translate-y-4 transition-all duration-500 ease-out"
     style="background:#1a3a1a;">
    <div>
        <p class="text-sm mb-0.5" style="color:rgba(255,255,255,0.6);">Welcome back,</p>
        <h2 class="text-xl font-bold text-white">{{ $fullName }}</h2>
        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.5);">{{ $position }}</p>
        @if($pendingLeave > 0 || $pendingHalfDay > 0)
        <div class="flex flex-wrap gap-2 mt-2">
            @if($pendingLeave > 0)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                  style="background:rgba(254,243,199,0.2);color:#fef3c7;border:1px solid rgba(254,243,199,0.3);">
                {{ $pendingLeave }} pending leave{{ $pendingLeave > 1 ? 's' : '' }}
            </span>
            @endif
            @if($pendingHalfDay > 0)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                  style="background:rgba(254,243,199,0.2);color:#fef3c7;border:1px solid rgba(254,243,199,0.3);">
                {{ $pendingHalfDay }} half-day pending
            </span>
            @endif
        </div>
        @endif
    </div>
    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
         style="background:rgba(255,255,255,0.15);">
        {{ strtoupper(substr($employee->first_name, 0, 1)) }}
    </div>
</div>

{{-- ══════════════════════════════════════════════
     4 STAT CARDS
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Gross Salary --}}
    <div id="card1" class="bg-white rounded-xl p-5 shadow border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Gross Salary</p>
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#dcfce7;">
                <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3
                             2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11
                             0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($gross, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $periodLabel }}</p>
    </div>

    {{-- Net Pay --}}
    <div id="card2" class="rounded-xl p-5 shadow opacity-0 translate-y-4 transition-all duration-500 ease-out" style="background:#1a3a1a;">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color:rgba(255,255,255,0.55);">Net Pay</p>
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.15);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                             2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">₱{{ number_format($net, 2) }}</p>
        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.45);">After all deductions</p>
    </div>

    {{-- Total Deductions --}}
    <div id="card3" class="bg-white rounded-xl p-5 shadow border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Deductions</p>
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#fee2e2;">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-red-500">₱{{ number_format($totDed, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">GSIS · PAG-IBIG · PHIC · Tax</p>
    </div>

    {{-- Position --}}
    <div id="card4" class="bg-white rounded-xl p-5 shadow border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Position</p>
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#eff6ff;">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2
                             0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2
                             2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-lg font-bold text-gray-800 leading-tight">{{ $position }}</p>
        <p class="text-xs text-gray-400 mt-1">Current designation</p>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     BOTTOM ROW
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Deductions Breakdown (Donut + Bars + Formulas) ── --}}
    <div id="deductCard"
         class="bg-white rounded-xl shadow border border-gray-100 p-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <h3 class="text-base font-bold text-gray-800 mb-1">Deductions Breakdown</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $periodLabel }} — Government Deductions</p>

        <div class="flex items-start gap-5">
            {{-- Donut --}}
            <div class="relative flex-shrink-0" style="width:150px;height:150px;">
                <canvas id="deductDonut" width="150" height="150"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-xs font-bold text-gray-700">₱{{ number_format($totDed, 0) }}</p>
                </div>
            </div>

            {{-- Bars with formulas --}}
            <div class="flex-1 space-y-3">
                @php
                $items = [
                    [
                        'label'   => 'GSIS',
                        'formula' => 'Salary × 9%',
                        'val'     => $gsis,
                        'color'   => '#1a3a1a',
                    ],
                    [
                        'label'   => 'Withholding Tax',
                        'formula' => 'BIR 2023 bracket',
                        'val'     => $wtax,
                        'color'   => '#3d7a2a',
                    ],
                    [
                        'label'   => 'PhilHealth',
                        'formula' => '(Salary × 5%) ÷ 2',
                        'val'     => $phic,
                        'color'   => '#86efac',
                    ],
                    [
                        'label'   => 'PAG-IBIG',
                        'formula' => 'min(Salary × 2%, ₱100)',
                        'val'     => $pagibig,
                        'color'   => '#a3e635',
                    ],
                ];
                @endphp

                @foreach($items as $d)
                <div>
                    <div class="flex items-start justify-between mb-1 gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $d['color'] }};"></div>
                                <span class="text-xs font-semibold text-gray-700">{{ $d['label'] }}</span>
                            </div>
                            <p class="text-gray-400 ml-3.5 leading-tight" style="font-size:10px;">{{ $d['formula'] }}</p>
                        </div>
                        <span class="text-xs font-bold text-gray-700 flex-shrink-0">₱{{ number_format($d['val'], 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 ml-0">
                        <div class="h-1.5 rounded-full transition-all duration-1000 ease-out bar-animate"
                             style="background:{{ $d['color'] }}; width:0%;"
                             data-w="{{ $totDed > 0 ? round(($d['val'] / $totDed) * 100, 1) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Pay Slip Summary ── --}}
    <div id="summaryCard"
         class="bg-white rounded-xl shadow border border-gray-100 p-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <h3 class="text-base font-bold text-gray-800 mb-1">Pay Slip Summary</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $periodLabel }}</p>

        <div class="space-y-0">
            {{-- Gross --}}
            <div class="flex justify-between items-center py-3" style="border-bottom:2px solid #f3f4f6;">
                <div>
                    <p class="text-sm font-bold text-gray-800">Gross Salary</p>
                    <p class="text-xs text-gray-400">Basic monthly pay</p>
                </div>
                <p class="text-sm font-bold text-gray-800">₱{{ number_format($gross, 2) }}</p>
            </div>

            {{-- Deduction rows --}}
            @php
            $slipRows = [
                ['label'=>'GSIS',           'formula'=>'Salary × 9%',             'val'=>$gsis,    'col'=>'gsis col I'],
                ['label'=>'PAG-IBIG',       'formula'=>'min(Salary × 2%, ₱100)',  'val'=>$pagibig, 'col'=>'PAG-IBIG col H'],
                ['label'=>'PhilHealth',     'formula'=>'(Salary × 5%) ÷ 2',       'val'=>$phic,    'col'=>'PHIC col W'],
                ['label'=>'Withholding Tax','formula'=>'BIR 2023 bracket',         'val'=>$wtax,    'col'=>'WTAX col H'],
            ];
            @endphp

            @foreach($slipRows as $row)
            <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid #f9fafb;">
                <div>
                    <p class="text-sm text-gray-600">(−) {{ $row['label'] }}</p>
                    <p class="text-gray-400" style="font-size:10px;">{{ $row['formula'] }} &nbsp;·&nbsp; {{ $row['col'] }}</p>
                </div>
                <p class="text-sm font-medium text-red-400">−₱{{ number_format($row['val'], 2) }}</p>
            </div>
            @endforeach

            {{-- Total deductions sub-total --}}
            <div class="flex justify-between items-center py-2.5 px-3 rounded-lg mt-1" style="background:#fff1f2;">
                <p class="text-xs font-semibold text-red-500">Total Deductions</p>
                <p class="text-xs font-bold text-red-500">−₱{{ number_format($totDed, 2) }}</p>
            </div>

            {{-- Leave balances (if available) --}}
            @if(isset($vlBalance) || isset($slBalance))
            <div class="pt-3 mt-1" style="border-top:1px solid #f3f4f6;">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Leave Balances</p>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-lg px-3 py-2 text-center" style="background:#eff6ff;">
                        <p class="text-xs font-semibold text-blue-500">Vacation Leave</p>
                        <p class="text-xl font-bold text-blue-700">{{ number_format($vlBalance?->remaining_balance ?? 0, 1) }}</p>
                        <p class="text-xs text-blue-400">days remaining</p>
                    </div>
                    <div class="rounded-lg px-3 py-2 text-center" style="background:#fff1f2;">
                        <p class="text-xs font-semibold text-red-400">Sick Leave</p>
                        <p class="text-xl font-bold text-red-500">{{ number_format($slBalance?->remaining_balance ?? 0, 1) }}</p>
                        <p class="text-xs text-red-300">days remaining</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Net Pay --}}
            <div class="flex justify-between items-center py-3 px-4 mt-2 rounded-xl" style="background:#1a3a1a;">
                <div>
                    <p class="text-sm font-bold text-white">NET PAY</p>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.5);"></p>
                </div>
                <p class="text-base font-bold" style="color:#86efac;">₱{{ number_format($net, 2) }}</p>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Fade-in ────────────────────────────────────────────────────
    [
        {id:'banner',delay:100},{id:'card1',delay:200},{id:'card2',delay:300},
        {id:'card3',delay:400},{id:'card4',delay:500},
        {id:'deductCard',delay:600},{id:'summaryCard',delay:700},
    ].forEach(({id,delay}) => setTimeout(() => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('opacity-0','translate-y-4'); el.classList.add('opacity-100','translate-y-0'); }
    }, delay));

    // ── Progress bars ──────────────────────────────────────────────
    setTimeout(() => {
        document.querySelectorAll('.bar-animate').forEach(b => { b.style.width = b.dataset.w + '%'; });
    }, 900);

    // ── Deductions Donut ───────────────────────────────────────────
    new Chart(document.getElementById('deductDonut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['GSIS (9%)','W-Tax (BIR)','PhilHealth (5%/2)','PAG-IBIG (2%)'],
            datasets:[{
                data: [{{ $gsis }}, {{ $wtax }}, {{ $phic }}, {{ $pagibig }}],
                backgroundColor: ['#1a3a1a','#3d7a2a','#86efac','#a3e635'],
                borderWidth: 0, hoverOffset: 6,
            }]
        },
        options: {
            responsive:false, cutout:'65%',
            animation:{animateRotate:true,duration:1000,easing:'easeInOutQuart'},
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:c=>' ₱'+c.raw.toLocaleString('en-PH',{minimumFractionDigits:2})}}
            }
        }
    });
});
</script>

@endif {{-- end @if($noEmployee) --}}
@endsection