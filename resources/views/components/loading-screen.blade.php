{{-- resources/views/components/loading-screen.blade.php --}}

<div id="page-loader"
     style="
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.35s ease, visibility 0.35s ease;
     ">

    {{-- ── Full-screen soft background glow ── --}}
    <div style="
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(61,122,42,0.08) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- ── Spinner + Logo wrapper ── --}}
    <div style="position:relative; width:180px; height:180px; margin-bottom:32px; display:flex; align-items:center; justify-content:center;">

        {{-- Comet-tail ring spinner --}}
        <div class="ls-ring"></div>

        {{-- Logo — centered on top of spinner --}}
        <img
            src="{{ asset('images/opag.png') }}"
            alt="OPAG"
            id="ls-logo-img"
            style="
                position: absolute;
                width: 120px;
                height: 120px;
                object-fit: contain;
                z-index: 2;
            "
            onerror="this.style.display='none'; document.getElementById('ls-fallback-icon').style.display='flex';"
        />

        {{-- Fallback icon if image fails --}}
        <div id="ls-fallback-icon"
             style="
                display: none;
                position: absolute;
                width: 110px; height: 110px;
                border-radius: 50%;
                background: #f0fdf4;
                align-items: center; justify-content: center;
                z-index: 2;
             ">
            <svg width="48" height="48" fill="none" stroke="#1a3a1a"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                 viewBox="0 0 24 24">
                <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                         002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2
                         2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
    </div>

    {{-- Office name --}}
    <p style="
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #1a3a1a;
        opacity: 0.5;
        margin-bottom: 6px;
    ">Office of the Provincial Agriculturist</p>

    {{-- Loading label --}}
    <p style="
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: #1a3a1a;
        margin-bottom: 20px;
        animation: ls-pulse 1.4s ease-in-out infinite;
    ">Loading…</p>

    {{-- Progress bar --}}
    <div style="width:200px; height:3px; background:#e5e7eb; border-radius:99px; overflow:hidden;">
        <div id="ls-bar"
             style="
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #1a3a1a, #3d7a2a, #86efac);
                border-radius: 99px;
                transition: width 0.25s ease;
             "></div>
    </div>
</div>

<style>
/* ── Comet-tail ring (your new snippet, recoloured to OPAG green) ── */
.ls-ring {
    position: absolute;
    inset: 0;
    width: 180px;
    height: 180px;
    box-sizing: border-box;
    mask:
        radial-gradient(#0000 47%, #000 48% 71%, #0000 72%) exclude,
        conic-gradient(#000 0 0) no-clip;
    animation: ls-ring-spin 1.5s linear infinite;
}
.ls-ring::before {
    content: "";
    position: absolute;
    /* comet head position — top-centre of the ring */
    inset: 0 35% 70%;
    border-radius: 50%;
    background: #1a3a1a;
    filter: blur(15px);
}
/* Coloured arc behind the comet head */
.ls-ring::after {
    content: "";
    position: absolute;
    inset: 0;
    background: conic-gradient(
        #0000 0deg,
        #0000 200deg,
        #1a3a1a 220deg,
        #3d7a2a 270deg,
        #86efac 310deg,
        #0000 340deg,
        #0000 360deg
    );
    border-radius: 50%;
}

@keyframes ls-ring-spin {
    to { rotate: 1turn; }
}
@keyframes ls-pulse {
    0%, 100% { opacity: 1;    }
    50%       { opacity: 0.4; }
}

body.ls-loading { overflow: hidden; }
</style>

<script>
(function () {
    'use strict';

    const loader = document.getElementById('page-loader');
    const bar    = document.getElementById('ls-bar');
    if (!loader) return;

    document.body.style.visibility = 'visible';
    document.body.classList.add('ls-loading');

    let progress = 0;
    let rafId;

    function tickProgress() {
        progress += (80 - progress) * 0.06;
        if (bar) bar.style.width = progress.toFixed(1) + '%';
        if (progress < 79.5) rafId = requestAnimationFrame(tickProgress);
    }
    rafId = requestAnimationFrame(tickProgress);

    function hideLoader() {
        cancelAnimationFrame(rafId);
        if (bar) {
            bar.style.transition = 'width 0.2s ease';
            bar.style.width = '100%';
        }
        setTimeout(function () {
            loader.style.opacity    = '0';
            loader.style.visibility = 'hidden';
            document.body.classList.remove('ls-loading');
        }, 200);
    }

    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader, { once: true });
    }

    setTimeout(hideLoader, 5000);

    function showLoader() {
        progress = 0;
        if (bar) { bar.style.transition = 'none'; bar.style.width = '0%'; }
        loader.style.visibility = 'visible';
        loader.style.opacity    = '1';
        document.body.classList.add('ls-loading');
        rafId = requestAnimationFrame(tickProgress);
    }

    document.addEventListener('click', function (e) {
        const anchor = e.target.closest('a[href]');
        if (!anchor) return;
        const href = anchor.getAttribute('href');
        if (
            !href ||
            href.startsWith('#') ||
            href.startsWith('http') ||
            href.startsWith('mailto') ||
            href.startsWith('javascript') ||
            anchor.getAttribute('target') === '_blank'
        ) return;
        showLoader();
    });

    document.addEventListener('submit', function (e) {
        if (e.target.getAttribute('target') === '_blank') return;
        showLoader();
    });

    window.addEventListener('pageshow', function (e) {
        if (e.persisted) hideLoader();
    });
})();
</script>