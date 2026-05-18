{{-- resources/views/components/sidebar.blade.php --}}
@php
    $employee   = Auth::user()?->employee;
    $realAccess = session('user_access', 'employee');
    $viewAs     = session('view_as',     $realAccess);

    $adminNav = [
        ['label' => 'Dashboard',           'route' => 'dashboard',          'icon' => 'grid'],
        ['label' => 'Employees',           'route' => 'employees.index',    'icon' => 'users'],
        ['label' => 'Application',         'icon'  => 'folder',             'children' => [
            ['label' => 'Application for Leave',      'route' => 'admin.leave.index'],
            ['label' => 'Certification for Half Day', 'route' => 'admin.halfday.index'],
        ]],
        ['label' => 'Leave Card',          'route' => 'leave-card.index',   'icon' => 'card'],
        ['label' => 'Payroll', 'icon' => 'dollar', 'children' => [
        ['label' => 'Create Payroll',       'route' => 'payroll.index'],
        ['label' => 'Remittances',          'route' => 'payroll.remittances'],
        ['label' => 'Payslip Management',   'route' => 'payroll.manage'], // <--- FIXED ROUTE HERE
        ['label' => 'Deduction Management', 'route' => 'payroll.deductions.index'],
    ]],
        ['label' => 'Management Settings', 'route' => 'settings.leaveType', 'icon' => 'settings'],
        //['label' => 'User Logs',           'route' => 'logs.index',         'icon' => 'file'],
    ];

    $employeeNav = [
        ['label' => 'Dashboard',   'route' => 'dashboard',     'icon' => 'grid'],
        ['label' => 'Application', 'icon'  => 'folder',        'children' => [
            ['label' => 'Application for Leave',      'route' => 'application.leave'],
            ['label' => 'Certification for Half Day', 'route' => 'application.halfday'],
        ]],
        ['label' => 'Leave Card',  'route' => 'leave.card',    'icon' => 'card'],
        ['label' => 'Payroll',     'icon'  => 'dollar',        'children' => [
            ['label' => 'Pay Slip', 'route' => 'payroll.payslip'],
        ]],
    ];
    $nav = $viewAs === 'admin' ? $adminNav : $employeeNav;

    // Pre-compute which group should be open on load based on current route
    $activeGroup = null;
    foreach ($nav as $item) {
        if (isset($item['children'])) {
            foreach ($item['children'] as $child) {
                if (request()->routeIs($child['route'])) {
                    $activeGroup = $item['label'];
                    break 2;
                }
            }
        }
    }
@endphp

<style>
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.15s, color 0.15s;
    overflow: hidden;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
}
.nav-link:hover {
    background-color: rgba(255,255,255,0.08);
    color: white;
}
.nav-link.nav-active,
.nav-link.nav-active:hover {
    background-color: #3d7a2a !important;
    color: white !important;
}

.nav-group-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.15s, color 0.15s;
    color: rgba(255,255,255,0.75);
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: left;
}
.nav-group-btn:hover {
    background-color: rgba(255,255,255,0.08);
    color: white;
}
.nav-group-btn.nav-group-active,
.nav-group-btn.nav-group-active:hover {
    background-color: #3d7a2a !important;
    color: white !important;
}

.nav-child-link {
    display: block;
    padding: 0.5rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: background-color 0.15s, color 0.15s;
    color: rgba(255,255,255,0.65);
    text-decoration: none;
}
.nav-child-link:hover {
    background-color: rgba(255,255,255,0.08);
    color: white;
}
.nav-child-link.nav-active,
.nav-child-link.nav-active:hover {
    color: white !important;
    font-weight: 500;
}
</style>

{{-- ─── DESKTOP SIDEBAR ─── --}}
<aside class="hidden md:flex flex-col text-white transition-all duration-200 z-30 fixed top-0 left-0 h-screen"
       style="background-color: #1a3a1a;"
       :class="sidebarOpen ? 'w-56' : 'w-14'"
       x-data="{ openGroup: {{ $activeGroup ? '\'' . $activeGroup . '\'' : 'null' }} }">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-3 py-4 min-h-[72px] overflow-hidden"
         style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="w-10 h-10 flex-shrink-0 overflow-hidden flex items-center justify-center">
            <img src="{{ asset('images/opag.png') }}"
                 alt="OPAG Logo"
                 class="w-10 h-10 object-contain rounded-full"
                 onerror="this.outerHTML='<div style=\'width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;\'><svg style=\'color:rgba(255,255,255,0.5);width:20px;height:20px\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'12\' r=\'10\' stroke-width=\'2\'/></svg></div>'">
        </div>
        <div x-show="sidebarOpen" x-transition.opacity class="leading-tight overflow-hidden">
            <p class="text-white/70 uppercase tracking-widest font-medium" style="font-size:9px;">Office of the</p>
            <p class="text-white/70 uppercase tracking-widest font-medium" style="font-size:9px;">Provincial</p>
            <p class="text-white uppercase tracking-widest font-bold" style="font-size:10px;">Agriculturist</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2" style="display:flex; flex-direction:column; gap:2px;">
        @foreach($nav as $item)

            @if(isset($item['children']))
            @php
                $groupHasActive = collect($item['children'])->contains(fn($c) => request()->routeIs($c['route']));
            @endphp
            <div>
                <button
                    @click.stop="openGroup = openGroup === '{{ $item['label'] }}' ? null : '{{ $item['label'] }}'"
                    class="nav-group-btn {{ $groupHasActive ? 'nav-group-active' : '' }}"
                >
                    @include('components.icons.' . $item['icon'])
                    <span x-show="sidebarOpen" x-transition.opacity class="flex-1 text-left whitespace-nowrap">
                        {{ $item['label'] }}
                    </span>
                    <svg x-show="sidebarOpen" x-transition.opacity
                         class="w-3 h-3 flex-shrink-0 transition-transform duration-200"
                         :class="openGroup === '{{ $item['label'] }}' ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openGroup === '{{ $item['label'] }}' && sidebarOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 ml-4 pl-3"
                     style="border-left: 1px solid rgba(255,255,255,0.2);">
                    @foreach($item['children'] as $child)
                        <a href="{{ route($child['route']) }}"
                           class="nav-child-link {{ request()->routeIs($child['route']) ? 'nav-active' : '' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            @else
            @php
                $isSettingsNav = isset($item['route']) && str_starts_with($item['route'], 'settings.');
                $isLeaveCard   = isset($item['route']) && $item['route'] === 'leave-card.index';
                $isActive      = $isSettingsNav
                    ? request()->routeIs('settings.*')
                    : ($isLeaveCard
                        ? request()->routeIs('leave-card.*')
                        : (isset($item['route']) && request()->routeIs($item['route'])));
            @endphp

            <a href="{{ route($item['route']) }}"
               class="nav-link {{ $isActive ? 'nav-active' : '' }}">
                @include('components.icons.' . $item['icon'])
                <span x-show="sidebarOpen" x-transition.opacity class="whitespace-nowrap">
                    {{ $item['label'] }}
                </span>
            </a>
            @endif

        @endforeach
    </nav>

    {{-- Collapse Toggle --}}
    <div class="p-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
        <button @click="sidebarOpen = !sidebarOpen"
                class="nav-group-btn justify-center" style="color:rgba(255,255,255,0.5);">
            <svg class="w-4 h-4 transition-transform" :class="sidebarOpen ? '' : 'rotate-180'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>
</aside>

{{-- ─── MOBILE SIDEBAR ─── --}}
<div x-show="mobileOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 w-56 h-screen text-white flex flex-col md:hidden z-30"
     style="background-color: #1a3a1a;"
     x-data="{ openGroup: {{ $activeGroup ? '\'' . $activeGroup . '\'' : 'null' }} }">

    <div class="flex items-center gap-3 px-3 py-4 min-h-[72px]"
         style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="w-10 h-10 flex-shrink-0 rounded-full"
             style="background:rgba(255,255,255,0.15);"></div>
        <div class="leading-tight">
            <p class="text-white/70 uppercase tracking-widest font-medium" style="font-size:9px;">Office of the Provincial</p>
            <p class="text-white uppercase tracking-widest font-bold" style="font-size:10px;">Agriculturist</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 px-2" style="display:flex; flex-direction:column; gap:2px;">
        @foreach($nav as $item)
            @if(isset($item['children']))
            @php
                $groupHasActive = collect($item['children'])->contains(fn($c) => request()->routeIs($c['route']));
            @endphp
            <div>
                <button @click.stop="openGroup = openGroup === '{{ $item['label'] }}' ? null : '{{ $item['label'] }}'"
                        class="nav-group-btn {{ $groupHasActive ? 'nav-group-active' : '' }}">
                    @include('components.icons.' . $item['icon'])
                    <span class="flex-1 text-left">{{ $item['label'] }}</span>
                    <svg class="w-3 h-3 transition-transform duration-200"
                         :class="openGroup === '{{ $item['label'] }}' ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openGroup === '{{ $item['label'] }}'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-1 ml-4 pl-3"
                     style="border-left: 1px solid rgba(255,255,255,0.2);">
                    @foreach($item['children'] as $child)
                        <a href="{{ route($child['route']) }}"
                           @click="mobileOpen = false"
                           class="nav-child-link {{ request()->routeIs($child['route']) ? 'nav-active' : '' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            @else
            @php
                $isSettingsNav = isset($item['route']) && str_starts_with($item['route'], 'settings.');
                $isLeaveCard   = isset($item['route']) && $item['route'] === 'leave-card.index';
                $isActive      = $isSettingsNav
                    ? request()->routeIs('settings.*')
                    : ($isLeaveCard
                        ? request()->routeIs('leave-card.*')
                        : (isset($item['route']) && request()->routeIs($item['route'])));
            @endphp

            <a href="{{ route($item['route']) }}"
               @click="mobileOpen = false"
               class="nav-link {{ $isActive ? 'nav-active' : '' }}">
                @include('components.icons.' . $item['icon'])
                <span>{{ $item['label'] }}</span>
            </a>
            @endif
        @endforeach
    </nav>
</div>

{{-- Spacer --}}
<div class="hidden md:block flex-shrink-0 transition-all duration-200"
     :class="sidebarOpen ? 'w-56' : 'w-14'">
</div>