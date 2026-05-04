{{-- resources/views/components/topbar.blade.php --}}
@php
    $employee   = Auth::user()?->employee;
    $fullName   = $employee ? "{$employee->first_name} {$employee->last_name}" : 'User';
    $position   = $employee?->position?->position_name ?? 'Employee';
    $initial    = strtoupper(substr($employee->first_name ?? 'U', 0, 1));
    $realAccess = session('user_access', 'employee');
    $viewAs     = session('view_as', $realAccess);
    $isAdmin    = ($realAccess === 'admin');
    $isViewingAsEmployee = ($viewAs === 'employee');
@endphp

<header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20 min-h-[72px]">

    {{-- Left: hamburger + page title --}}
    <div class="flex items-center gap-3">
        <button @click="mobileOpen = !mobileOpen"
                class="md:hidden p-1.5 rounded-md text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h1 class="text-xl md:text-2xl font-black text-gray-800 uppercase tracking-widest leading-none">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('D, g:i A, F j, Y') }}</p>
        </div>
    </div>

    {{-- Right --}}
    <div class="flex items-center gap-3">

        {{-- View badge (admin only) --}}
        @if($isAdmin)
        <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
             style="{{ $isViewingAsEmployee
                 ? 'background:#fef9c3; color:#854d0e;'
                 : 'background:#dcfce7; color:#14532d;' }}">
            <span class="w-2 h-2 rounded-full"
                  style="{{ $isViewingAsEmployee ? 'background:#ca8a04;' : 'background:#16a34a;' }}"></span>
            {{ $isViewingAsEmployee ? 'Employee View' : 'Admin View' }}
        </div>
        @endif

        {{-- ── NOTIFICATION BELL ── --}}
        <div id="notifWrapper">
            <button id="notifBellBtn"
                    class="relative p-2 rounded-full text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="notifBadge"
                      class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white
                             text-[9px] font-bold rounded-full items-center justify-center px-0.5"
                      style="display:none;">
                </span>
            </button>
        </div>

        {{-- ── USER DROPDOWN TRIGGER ── --}}
        <div id="userWrapper">
            <button id="userToggleBtn"
                    class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="w-8 h-8 rounded-full flex items-center justify-center
                            text-white text-sm font-bold flex-shrink-0"
                     style="background-color: #2d5a1b;">
                    {{ $initial }}
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-semibold text-gray-800 leading-none">
                        {{ strtoupper($employee->last_name ?? '') }}, {{ $employee->first_name ?? 'User' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $position }}</p>
                </div>
                <svg id="userChevron" class="w-4 h-4 text-gray-400 hidden sm:block transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════════
     DROPDOWNS — rendered OUTSIDE the sticky header at document level
     ══════════════════════════════════════════════════════════════════ --}}

{{-- Transparent click-away overlay --}}
<div id="topbarOverlay"
     style="display:none; position:fixed; inset:0; z-index:998;">
</div>

{{-- ── NOTIFICATION DROPDOWN ── --}}
<div id="notifDropdown"
     style="display:none; position:fixed; z-index:999; width:380px; max-height:500px;
            background:#fff; border-radius:16px;
            box-shadow:0 20px 60px rgba(0,0,0,0.15), 0 4px 20px rgba(0,0,0,0.08);
            border:1px solid #e5e7eb; flex-direction:column; overflow:hidden;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 flex-shrink-0"
         style="border-bottom:1px solid #f3f4f6;">
        <div class="flex items-center gap-2">
            <span class="font-bold text-gray-800 text-sm">Notifications</span>
            <span id="notifHeaderCount"
                  class="bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5"
                  style="display:none;">
            </span>
        </div>
        <button id="markAllBtn"
                class="text-xs font-semibold hover:underline"
                style="display:none; color:#2d5a1b;">
            Mark all as read
        </button>
    </div>

    {{-- Legend row: badge key --}}
    <div id="notifLegend"
         style="display:flex; align-items:center; gap:6px; padding:7px 16px 6px;
                background:#fafafa; border-bottom:1px solid #f3f4f6; flex-wrap:wrap;">
        <span style="font-size:9px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin-right:2px;">Type:</span>
        <span style="font-size:9px;font-weight:700;background:#dbeafe;color:#1e40af;border-radius:4px;padding:2px 6px;">LEAVE</span>
        <span style="font-size:9px;font-weight:700;background:#fef9c3;color:#854d0e;border-radius:4px;padding:2px 6px;">MONETIZATION</span>
        <span style="font-size:9px;font-weight:700;background:#ede9fe;color:#5b21b6;border-radius:4px;padding:2px 6px;">HALF DAY</span>
    </div>

    {{-- List --}}
    <div class="overflow-y-auto flex-1" id="notifList">
        <div id="notifLoading" class="py-10 flex flex-col items-center gap-2">
            <svg class="w-5 h-5 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-xs text-gray-300 mt-1">Loading…</p>
        </div>

        <div id="notifEmpty" style="display:none;"
             class="py-12 flex flex-col items-center gap-3 text-gray-300">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm font-medium">No notifications yet</p>
        </div>

        <div id="notifItems"></div>
    </div>

    {{-- Footer --}}
    <div class="px-4 py-2.5 flex-shrink-0 text-center"
         style="border-top:1px solid #f3f4f6; background:#fafafa;">
        <span class="text-xs text-gray-400">Showing latest 20 notifications</span>
    </div>
</div>

{{-- ── USER DROPDOWN ── --}}
<div id="userDropdown"
     style="display:none; position:fixed; z-index:999; min-width:220px;
            background:#fff; border-radius:14px;
            box-shadow:0 16px 40px rgba(0,0,0,0.13), 0 4px 12px rgba(0,0,0,0.06);
            border:1px solid #e5e7eb; padding:4px 0;">

    <div class="px-4 py-3" style="border-bottom:1px solid #f3f4f6;">
        <p class="text-sm font-bold text-gray-800">
            {{ strtoupper($employee->last_name ?? '') }}, {{ $employee->first_name ?? '' }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $position }}</p>
        @if($isAdmin)
        <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
              style="background:#dcfce7; color:#14532d;">
            ● Admin Account
        </span>
        @endif
    </div>

    <a href="{{ route('profile.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profile
    </a>

    @if($isAdmin)
    <form method="POST" action="{{ route('auth.switch-view') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition hover:bg-gray-50"
                style="{{ $isViewingAsEmployee ? 'color:#2d5a1b;' : 'color:#854d0e;' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            {{ $isViewingAsEmployee ? 'Switch to Admin View' : 'Switch to Employee View' }}
        </button>
    </form>
    @endif

    <hr class="my-1 border-gray-100">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </button>
    </form>
</div>

<script>
(function () {

    const IS_ADMIN  = {{ $isAdmin ? 'true' : 'false' }};
    const VIEW_AS   = "{{ $viewAs }}";

    /* ─── helpers ─── */
    function showEl(el)     { el.style.display = 'flex'; }
    function showBlock(el)  { el.style.display = 'block'; }
    function showInline(el) { el.style.display = 'inline'; }
    function hideEl(el)     { el.style.display = 'none'; }
    function isVisible(el)  { return el.style.display !== 'none'; }

    function positionDropdown(triggerEl, dropdownEl) {
        const rect    = triggerEl.getBoundingClientRect();
        const ddWidth = dropdownEl.offsetWidth || parseInt(dropdownEl.style.width) || 220;
        let   left    = rect.right - ddWidth;
        if (left < 8) left = 8;
        dropdownEl.style.top  = (rect.bottom + 6) + 'px';
        dropdownEl.style.left = left + 'px';
    }

    const overlay    = document.getElementById('topbarOverlay');
    const notifDd    = document.getElementById('notifDropdown');
    const userDd     = document.getElementById('userDropdown');
    const bellBtn    = document.getElementById('notifBellBtn');
    const userBtn    = document.getElementById('userToggleBtn');
    const notifBadge = document.getElementById('notifBadge');
    const headerCnt  = document.getElementById('notifHeaderCount');
    const markAllBtn = document.getElementById('markAllBtn');
    const loadingEl  = document.getElementById('notifLoading');
    const emptyEl    = document.getElementById('notifEmpty');
    const itemsEl    = document.getElementById('notifItems');

    /* ─── Overlay click-away ─── */
    function openOverlay()  { showBlock(overlay); }
    function closeOverlay() { hideEl(overlay); }

    overlay.addEventListener('click', function () { closeAll(); });

    function closeAll() {
        hideEl(notifDd);
        hideEl(userDd);
        closeOverlay();
        const chevron = document.getElementById('userChevron');
        if (chevron) chevron.classList.remove('rotate-180');
    }

    window.addEventListener('scroll', function () {
        if (isVisible(notifDd)) positionDropdown(bellBtn, notifDd);
        if (isVisible(userDd))  positionDropdown(userBtn, userDd);
    }, true);
    window.addEventListener('resize', function () {
        if (isVisible(notifDd)) positionDropdown(bellBtn, notifDd);
        if (isVisible(userDd))  positionDropdown(userBtn, userDd);
    });

    /* ════════════════════════════════════════
       NOTIFICATION BELL
    ════════════════════════════════════════ */
    let notifications = [];
    let unread        = 0;
    let loading       = false;

    /* ── Type → SVG path map ── */
    function typeIcon(type) {
        const icons = {
            /* ── Leave ── */
            leave_approved:              '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            leave_rejected:              '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            leave_pending:               '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            leave_cancelled:             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
            leave_status_changed:        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
            /* ── Half Day ── */
            halfday_submitted:           '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            halfday_approved:            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            halfday_rejected:            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            halfday_cancelled:           '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
            halfday_pending:             '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            /* ── Monetization ── */
            monetization_pending:        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            monetization_approved:       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            monetization_rejected:       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            monetization_cancelled:      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
            monetization_status_changed: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
            monetization_recalled:       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>',
            monetization_submitted:      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        };
        return icons[type] || '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
    }

    /* ── Detect notification category from ref_type or type string ── */
    function getNotifCategory(n) {
        /* ref_type is the most reliable signal — set by the backend */
        if (n.ref_type === 'monetization')      return 'monetization';
        if (n.ref_type === 'half_day')          return 'halfday';
        if (n.ref_type === 'leave_application') return 'leave';

        /* Fallback: inspect the type string prefix */
        const t = (n.type || '').toLowerCase();
        if (t.startsWith('monetization_')) return 'monetization';
        if (t.startsWith('halfday_'))      return 'halfday';
        if (t.startsWith('leave_'))        return 'leave';

        return 'leave'; /* default */
    }

    /* ── Badge HTML per category ── */
    function categoryBadge(category) {
        const map = {
            monetization: 'background:#fef9c3;color:#854d0e;',
            halfday:      'background:#ede9fe;color:#5b21b6;',
            leave:        'background:#dbeafe;color:#1e40af;',
        };
        const label = {
            monetization: 'MONETIZATION',
            halfday:      'HALF DAY',
            leave:        'LEAVE',
        };
        const style = map[category]  || map.leave;
        const text  = label[category] || 'LEAVE';
        return `<span style="font-size:9px;font-weight:700;${style}border-radius:4px;padding:1px 5px;margin-left:5px;vertical-align:middle;white-space:nowrap;">${text}</span>`;
    }

    /* ── Icon background/color per category+type ── */
    function iconStyle(category, type) {
        /* monetization uses amber tones */
        if (category === 'monetization') {
            if (type && type.includes('approved'))  return { bg:'#dcfce7', color:'#16a34a' };
            if (type && type.includes('rejected'))  return { bg:'#fee2e2', color:'#dc2626' };
            if (type && type.includes('cancelled')) return { bg:'#f3f4f6', color:'#6b7280' };
            if (type && type.includes('recalled'))  return { bg:'#ede9fe', color:'#7c3aed' };
            return { bg:'#fef9c3', color:'#854d0e' }; /* pending / default */
        }
        /* halfday uses purple tones */
        if (category === 'halfday') {
            if (type && type.includes('approved'))  return { bg:'#dcfce7', color:'#16a34a' };
            if (type && type.includes('rejected'))  return { bg:'#fee2e2', color:'#dc2626' };
            if (type && type.includes('cancelled')) return { bg:'#f3f4f6', color:'#6b7280' };
            return { bg:'#ede9fe', color:'#7c3aed' };
        }
        /* leave — use what the backend provides, or fall back */
        return {
            bg:    n_iconBg    || '#dbeafe',
            color: n_iconColor || '#1e40af',
        };
    }

    function escHtml(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Render notification list ── */
    function renderNotifs() {
        hideEl(loadingEl);
        itemsEl.innerHTML = '';

        if (notifications.length === 0) {
            showEl(emptyEl);
            return;
        }
        hideEl(emptyEl);

        notifications.forEach(function(n) {
            const category = getNotifCategory(n);
            const badge    = categoryBadge(category);

            /* resolve icon colours */
            let iconBg, iconColor;
            if (category === 'monetization') {
                const t = (n.type || '').toLowerCase();
                if (t.includes('approved'))       { iconBg = '#dcfce7'; iconColor = '#16a34a'; }
                else if (t.includes('rejected'))  { iconBg = '#fee2e2'; iconColor = '#dc2626'; }
                else if (t.includes('cancelled')) { iconBg = '#f3f4f6'; iconColor = '#6b7280'; }
                else if (t.includes('recalled'))  { iconBg = '#ede9fe'; iconColor = '#7c3aed'; }
                else                              { iconBg = '#fef9c3'; iconColor = '#854d0e'; }
            } else if (category === 'halfday') {
                const t = (n.type || '').toLowerCase();
                if (t.includes('approved'))       { iconBg = '#dcfce7'; iconColor = '#16a34a'; }
                else if (t.includes('rejected'))  { iconBg = '#fee2e2'; iconColor = '#dc2626'; }
                else if (t.includes('cancelled')) { iconBg = '#f3f4f6'; iconColor = '#6b7280'; }
                else                              { iconBg = '#ede9fe'; iconColor = '#7c3aed'; }
            } else {
                /* leave — use backend-provided colours or sensible defaults */
                iconBg    = n.icon_bg    || '#dbeafe';
                iconColor = n.icon_color || '#1e40af';
            }

            const div = document.createElement('div');
            div.style.cssText = 'display:flex; align-items:flex-start; gap:12px; padding:12px 16px; cursor:pointer; border-bottom:1px solid #f9fafb; transition:background 0.1s;';
            div.style.background = n.is_read ? '#fff' : '#f0fdf4';
            div.onmouseover = function() { div.style.background = '#f9fafb'; };
            div.onmouseout  = function() { div.style.background = n.is_read ? '#fff' : '#f0fdf4'; };
            div.onclick     = function() { handleNotifClick(n); };

            div.innerHTML =
                /* ── Icon circle ── */
                '<div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;background:' + escHtml(iconBg) + '">' +
                    '<svg style="width:16px;height:16px;color:' + escHtml(iconColor) + '" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        typeIcon(n.type) +
                    '</svg>' +
                '</div>' +
                /* ── Text area ── */
                '<div style="flex:1;min-width:0;">' +
                    '<p style="font-size:13px;font-weight:600;color:#1f2937;line-height:1.35;margin:0;">' +
                        escHtml(n.title || '') +
                        badge +           /* ← MONETIZATION / LEAVE / HALF DAY badge */
                    '</p>' +
                    '<p style="font-size:11px;color:#6b7280;margin:3px 0 0;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' +
                        escHtml(n.message || '') +
                    '</p>' +
                    '<p style="font-size:10px;color:#9ca3af;margin:4px 0 0;">' + escHtml(n.time_ago || '') + '</p>' +
                '</div>' +
                /* ── Unread dot ── */
                (!n.is_read
                    ? '<div style="width:8px;height:8px;border-radius:50%;background:#2d5a1b;flex-shrink:0;margin-top:6px;"></div>'
                    : '<div style="width:8px;flex-shrink:0;"></div>');

            itemsEl.appendChild(div);
        });
    }

    function updateBadge() {
        if (unread > 0) {
            notifBadge.textContent   = unread > 9 ? '9+' : unread;
            notifBadge.style.display = 'flex';
            headerCnt.textContent    = unread;
            showInline(headerCnt);
            showBlock(markAllBtn);
        } else {
            hideEl(notifBadge);
            hideEl(headerCnt);
            hideEl(markAllBtn);
        }
    }

    async function fetchNotifications() {
        if (loading) return;
        loading = true;
        try {
            const res  = await fetch('/notifications', {
                headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
            });
            const data = await res.json();
            notifications = data.notifications || [];
            unread        = data.unread_count  || 0;
            updateBadge();
            if (isVisible(notifDd)) renderNotifs();
        } catch(e) { console.error('Notifications error:', e); }
        loading = false;
    }

    async function markAllRead() {
        try {
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
        } catch(e) {}
        notifications.forEach(function(n) { n.is_read = true; });
        unread = 0;
        updateBadge();
        renderNotifs();
    }

    async function markOneRead(id) {
        try {
            await fetch('/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
        } catch(e) {}
        const n = notifications.find(function(n) { return n.id === id; });
        if (n && !n.is_read) { n.is_read = true; unread = Math.max(0, unread - 1); }
        updateBadge();
    }

    /* ── Click a notification → mark read → navigate ── */
    async function handleNotifClick(n) {
        await markOneRead(n.id);
        closeAll();

        if (!n.ref_id || !n.ref_type) return;

        const isAdminView = (VIEW_AS === 'admin');
        const category    = getNotifCategory(n);

        if (category === 'monetization') {
            /* Navigate to the Monetization tab directly */
            window.location.href = isAdminView
                ? '/admin/leave?tab=monetize&highlight=' + n.ref_id
                : '/application/leave?tab=history&highlight=' + n.ref_id;

        } else if (category === 'halfday') {
            window.location.href = isAdminView
                ? '/admin/halfday?highlight='   + n.ref_id
                : '/application/halfday?highlight=' + n.ref_id;

        } else {
            /* Default: leave application */
            window.location.href = isAdminView
                ? '/admin/leave?highlight='   + n.ref_id
                : '/application/leave?highlight=' + n.ref_id;
        }
    }

    /* ── Open notification dropdown ── */
    function openNotif() {
        hideEl(userDd);
        showEl(notifDd);
        positionDropdown(bellBtn, notifDd);

        showEl(loadingEl);
        hideEl(emptyEl);
        itemsEl.innerHTML = '';

        openOverlay();
        fetchNotifications().then(function() { renderNotifs(); });
    }

    function closeNotif() { hideEl(notifDd); }

    bellBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isVisible(notifDd)) { closeAll(); } else { openNotif(); }
    });

    markAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        markAllRead();
    });

    /* ════════════════════════════════════════
       USER DROPDOWN
    ════════════════════════════════════════ */
    userBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isVisible(userDd)) {
            closeAll();
        } else {
            hideEl(notifDd);
            showBlock(userDd);
            positionDropdown(userBtn, userDd);
            openOverlay();
        }
        document.getElementById('userChevron').classList.toggle(
            'rotate-180', isVisible(userDd)
        );
    });

    window.topbarNotif = { toggleOpen: function() { bellBtn.click(); }, markAllRead: markAllRead, close: closeNotif };
    window.topbarUser  = { toggle: function() { userBtn.click(); }, close: function() { hideEl(userDd); } };

    /* ── Init ── */
    fetchNotifications();
    setInterval(fetchNotifications, 30000);

})();
</script>