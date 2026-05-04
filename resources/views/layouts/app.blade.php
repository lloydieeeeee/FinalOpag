{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- CSRF token used by Alpine.js fetch calls --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OPAG - @yield('title', 'Dashboard')</title>

    {{-- ── Prevent flash of unstyled content ──────────────────────
         Hide the body instantly via inline style, then the loading
         screen JS will unhide it once assets are ready.
    ─────────────────────────────────────────────────────────────── --}}
    <style>
        body { visibility: hidden; }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'opag-dark':  '#1a3a1a',
                        'opag-green': '#2d5a1b',
                        'opag-mid':   '#3d7a2a',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #1a3a1a; }
        ::-webkit-scrollbar-thumb { background: #3d7a2a; border-radius: 2px; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @yield('head')
</head>
<body class="font-sans"
      style="background-color: #f3f4f6;"
      x-data="{ sidebarOpen: true, mobileOpen: false }">

    {{-- ✅ Loading screen — must be FIRST inside <body> --}}
    <x-loading-screen />

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Page wrapper --}}
        <div class="flex-1 flex flex-col min-w-0">
            @include('components.topbar')
            <main class="flex-1 p-4 md:p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile overlay --}}
    <div x-show="mobileOpen"
         x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 z-20 md:hidden"
         style="background-color: rgba(0,0,0,0.5);">
    </div>

    @yield('scripts')
</body>
</html>