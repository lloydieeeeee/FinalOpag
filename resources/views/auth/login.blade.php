<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OPAG - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
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

    <style>
        /* Floating Input Wrapper */
        .input-group {
            position: relative;
            margin-bottom: 2rem;
        }

        /* Input Field */
        .input {
            width: 100%;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            background: transparent;
            padding: 1rem;
            font-size: 0.95rem;
            color: #1a3a1a;
            transition: all 150ms cubic-bezier(0.4,0,0.2,1);
        }

        .input:focus {
            outline: none;
            border: 1.5px solid #2d5a1b;
        }

        /* Floating Label */
        .user-label {
            position: absolute;
            left: 15px;
            top: 0;
            color: #6b7280;
            pointer-events: none;
            transform: translateY(1rem);
            transition: 150ms cubic-bezier(0.4,0,0.2,1);
            background-color: white;
            font-size: 0.95rem;
        }

        .input:focus ~ .user-label,
        .input:not(:placeholder-shown) ~ .user-label {
            transform: translateY(-50%) scale(0.8);
            padding: 0 .3em;
            color: #2d5a1b;
        }

        /* Alert animations */
        .alert-box {
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .alert-box.show {
            opacity: 1;
            transform: translateY(0);
        }

        .alert-box.hide {
            opacity: 0;
            transform: translateY(-8px);
        }
    </style>
</head>

<body class="min-h-screen bg-green-800 font-sans">

<div class="min-h-screen flex flex-col md:flex-row">

    <!-- ── LEFT PANEL ── -->
    <div class="relative w-full md:w-3/5 flex items-center justify-center overflow-hidden min-h-[300px] md:min-h-screen">

        <img src="{{ asset('images/kapitolback.png') }}"
             class="absolute inset-0 w-full h-full object-cover opacity-30"
             alt="Background">

        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 text-center text-white px-10 py-16">

            <p class="text-2xl md:text-3xl font-semibold mb-6">
                Provincial Government of Camarines Norte
            </p>

            <h1 class="text-3xl md:text-5xl font-bold leading-snug mb-10">
                Office of the Provincial Agriculturist <br>
                Agriwork System
            </h1>

            <div class="flex justify-center gap-10">
                <img src="{{ asset('images/kapitolyo.png') }}" class="w-24 md:w-32 object-contain" alt="Province Seal">
                <img src="{{ asset('images/opag.png') }}"      class="w-24 md:w-32 object-contain" alt="OPAG Seal">
            </div>

        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="w-full md:w-2/5 flex items-center justify-center bg-white px-8 py-14">
        <div class="w-full max-w-md text-center">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logoniabel.png') }}"
                     class="w-40 h-40 object-contain"
                     alt="OPAG Logo">
            </div>

            <!-- Heading -->
            <h2 class="text-4xl font-bold text-opag-green mb-2">
                Login to your Account
            </h2>
            <p class="text-sm text-gray-500 mb-6">
                See what is going on with your business
            </p>

            <!-- ── SUCCESS ALERT ── -->
            @if(session('success'))
                <div id="alertBox"
                     class="alert-box flex gap-3 rounded-xl border border-green-200 bg-green-50 p-4 mb-6 text-left">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-700">Success</p>
                        <p class="text-sm text-green-600 mt-0.5">{{ session('success') }}</p>
                    </div>
                    <!-- Close button -->
                    <button onclick="dismissAlert()"
                            class="ml-auto text-green-400 hover:text-green-600 transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{--
  resources/views/auth/login.blade.php
  Replace the ERROR ALERT section with this — shows specific login errors
--}}

<!-- ── ERROR ALERT ── -->
@if($errors->any())
    <div id="alertBox" class="alert-box flex gap-3 rounded-xl border p-4 mb-6 text-left
        @if(str_contains($errors->first(), 'deactivated'))
            border-orange-200 bg-orange-50
        @else
            border-red-200 bg-red-50
        @endif">

        {{-- Icon: deactivated = lock, wrong password = warning, not found = question --}}
        @if(str_contains($errors->first(), 'deactivated'))
        <svg class="h-5 w-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0
                     002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        @elseif(str_contains($errors->first(), 'password'))
        <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1
                     1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        @else
        <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        @endif

        <div class="flex-1">
            <p class="text-sm font-semibold
                @if(str_contains($errors->first(), 'deactivated')) text-orange-700
                @else text-red-700 @endif">
                @if(str_contains($errors->first(), 'deactivated'))
                    Account Deactivated
                @elseif(str_contains($errors->first(), 'password'))
                    Wrong Password
                @else
                    Account Not Found
                @endif
            </p>
            <p class="text-sm mt-0.5
                @if(str_contains($errors->first(), 'deactivated')) text-orange-600
                @else text-red-600 @endif">
                {{ $errors->first('login') }}
            </p>
        </div>

        <button onclick="dismissAlert()" class="ml-auto flex-shrink-0
            @if(str_contains($errors->first(), 'deactivated')) text-orange-400 hover:text-orange-600
            @else text-red-400 hover:text-red-600 @endif transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif

            <!-- ── LOGIN FORM ── -->
            <form method="POST" action="{{ route('login.post') }}" class="text-left">
                @csrf

                <!-- Employee ID -->
                <div class="input-group">
                    <input required
                           type="text"
                           name="employee_id"
                           value="{{ old('employee_id') }}"
                           autocomplete="off"
                           id="employee_id"
                           class="input"
                           placeholder=""
                           data-placeholder="e.g. 1, 2, 3..."
                           onfocus="showPlaceholder(this)"
                           oninput="hidePlaceholderOnType(this)"
                           onblur="removePlaceholderIfEmpty(this)">
                    <label class="user-label" for="employee_id">Employee ID</label>
                </div>

                <!-- Password -->
                <div class="input-group relative">
                    <input required
                           type="password"
                           name="password"
                           id="password"
                           autocomplete="off"
                           class="input pr-12"
                           placeholder=""
                           data-placeholder="Enter your password"
                           onfocus="showPlaceholder(this)"
                           oninput="hidePlaceholderOnType(this)"
                           onblur="removePlaceholderIfEmpty(this)">
                    <label class="user-label" for="password">Password</label>

                    <!-- Toggle visibility -->
                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-opag-green transition">
                        <!-- Eye Open -->
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5
                                     c4.477 0 8.268 2.943 9.542 7
                                     -1.274 4.057-5.065 7-9.542 7
                                     -4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <!-- Eye Closed -->
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3l18 18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.584 10.587A3 3 0 0012 15a3 3 0 002.413-1.584
                                     M9.363 5.365A9.057 9.057 0 0112 5c4.477 0 8.268 2.943
                                     9.542 7a9.042 9.042 0 01-1.064 2.11M6.343 6.343
                                     A9.042 9.042 0 002.458 12c1.274 4.057 5.065 7 9.542 7
                                     a9.057 9.057 0 004.635-1.265"/>
                        </svg>
                    </button>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-opag-green hover:bg-opag-dark text-white font-semibold py-3 rounded-xl transition-all duration-200 mt-2 shadow-sm hover:shadow-md">
                    Login
                </button>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const alertBox = document.getElementById('alertBox');

    if (alertBox) {
        // ── Fade in ──
        setTimeout(() => {
            alertBox.classList.add('show');
        }, 100);

        // ── Auto dismiss after 5 seconds ──
        setTimeout(() => {
            fadeOutAlert();
        }, 5000);
    }
});

// ── Manual dismiss via X button ──
function dismissAlert() {
    fadeOutAlert();
}

function fadeOutAlert() {
    const alertBox = document.getElementById('alertBox');
    if (!alertBox) return;
    alertBox.classList.remove('show');
    alertBox.classList.add('hide');
    // Remove from DOM after transition ends
    setTimeout(() => {
        alertBox.remove();
    }, 400);
}

// ── Floating label placeholder helpers ──
function showPlaceholder(input) {
    if (!input.value) {
        input.placeholder = input.dataset.placeholder;
    }
}

function hidePlaceholderOnType(input) {
    if (input.value.length > 0) {
        input.placeholder = '';
    }
}

function removePlaceholderIfEmpty(input) {
    if (!input.value) {
        input.placeholder = '';
    }
}

// ── Toggle password visibility ──
function togglePassword() {
    const password  = document.getElementById('password');
    const eyeOpen   = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    if (password.type === 'password') {
        password.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        password.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}
</script>

</body>
</html>