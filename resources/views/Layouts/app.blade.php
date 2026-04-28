<!DOCTYPE html>
<html lang="fr">
<head>
    
    <meta charset="UTF-8">
    <meta name="api-token" content="{{ session('api_token') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>@yield('title', 'DKSoft')</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue:         #1a7bbf;
            --blue-light:   #2fa8e8;
            --orange:       #e8541a;
            --orange-light: #ff7a3d;
            --dark:         #0d1b2e;
            --card-bg:      #ffffff;
            --text:         #0d1b2e;
            --text-muted:   #5a7a99;
            --border:       rgba(26,123,191,0.12);
            --bg:           #f0f6fc;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* ========== ANIMATION SPOTS BLEUS & ORANGES ========== */
        .spots-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .spot {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26,123,191,0.2) 0%, rgba(26,123,191,0) 70%);
            animation: floatSpot 25s infinite ease-in-out;
            will-change: transform, opacity;
        }
        .spot.orange {
            background: radial-gradient(circle, rgba(232,84,26,0.15) 0%, rgba(232,84,26,0) 70%);
        }
        @keyframes floatSpot {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.4;
            }
            50% {
                transform: translate(30px, -20px) scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: translate(-20px, 40px) scale(0.9);
                opacity: 0.4;
            }
        }

        /* Ajustement du contenu principal pour qu'il soit au‑dessus des spots */
        header, main, footer {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(26,123,191,0.15);
            backdrop-filter: blur(10px);
        }

        /* ══════════════════════════════
           HEADER
        ══════════════════════════════ */
        #app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 48px;
            border-bottom: 1px solid rgba(26,123,191,0.1);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 12px rgba(26,123,191,0.08);
            animation: headerSlideDown .5s ease both;
        }
        @keyframes headerSlideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .app-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .app-logo svg {
            width: 48px;
            height: 48px;
            filter: drop-shadow(0 2px 6px rgba(26,123,191,.2));
            flex-shrink: 0;
        }
        .app-brand-name {
            font-family: 'Rajdhani', sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            line-height: 1;
        }
        .app-brand-name .dk  { color: var(--blue); }
        .app-brand-name .sft { color: var(--orange); }
        .app-brand-tagline {
            font-size: 9px;
            color: var(--text-muted);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Nav slot */
        #app-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Classes réutilisables ── */
        .header-badge {
            font-size: 11px;
            color: var(--text-muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1px solid rgba(26,123,191,0.18);
            padding: 6px 14px;
            border-radius: 20px;
            background: rgba(26,123,191,0.05);
        }
        .nav-link {
            font-size: 13px; font-weight: 600; letter-spacing: 0.5px;
            text-transform: none; color: var(--text-muted);
            text-decoration: none; padding: 7px 16px;
            border-radius: 8px; border: 1px solid transparent;
            transition: color .2s, background .2s;
        }
        .nav-link:hover  { color: var(--dark); background: rgba(26,123,191,0.06); }
        .nav-link.active { color: var(--blue); background: rgba(26,123,191,0.08); }

        .btn-header {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 20px; border-radius: 10px; border: none;
            font-family: 'Montserrat', sans-serif; font-size: 12px;
            font-weight: 700; letter-spacing: 0.5px; text-transform: none;
            text-decoration: none; cursor: pointer; transition: all .2s;
        }
        .btn-header:hover { transform: translateY(-1px); filter: brightness(1.08); box-shadow: 0 4px 16px rgba(26,123,191,0.25); }
        .btn-blue   { background: linear-gradient(135deg, var(--blue), var(--blue-light)); color: #fff; box-shadow: 0 2px 10px rgba(26,123,191,0.2); }
        .btn-orange { background: linear-gradient(135deg, var(--orange), var(--orange-light)); color: #fff; }
        .btn-ghost  { background: transparent; color: var(--text-muted); border: 1px solid rgba(26,123,191,0.2); }

        @media (max-width: 768px) {
            #app-header { padding: 12px 20px; }
            #app-nav    { gap: 4px; }
            .nav-link   { padding: 6px 10px; font-size: 11px; }
        }
    </style>

    @stack('styles')
    @livewireStyles
</head>

<body class="flex flex-col min-h-screen">

    {{-- ANIMATION SPOTS (bleus et oranges) --}}
    <div class="spots-bg" id="spots-container"></div>

    <header id="app-header">
        @php
    $isAdmin = session('admin_id') ? true : false;
@endphp

@if($isAdmin)
    {{-- Admin : déconnexion + redirection vers login --}}
    <a class="app-logo" href="#"
       onclick="event.preventDefault();
                fetch('{{ route('admin.logout') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => window.location.href = '{{ route('admin.login') }}');"
       style="cursor: pointer;">
@else
    {{-- Utilisateur normal : lien classique --}}
    <a class="app-logo" href="{{ url('/') }}">
@endif
    {{-- Contenu SVG et texte (inchangé) --}}
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect width="100" height="100" rx="16" fill="white" stroke="#e8eef6" stroke-width="2"/>
        <text x="8"  y="72" font-family="Arial Black" font-size="62" font-weight="900" fill="#1a7bbf">D</text>
        <text x="48" y="72" font-family="Arial Black" font-size="62" font-weight="900" fill="#e8541a">K</text>
        <text x="8"  y="22" font-family="Arial"       font-size="16" font-weight="700" fill="#1a7bbf">Soft</text>
        <line x1="8" y1="80" x2="92" y2="80" stroke="#e8541a" stroke-width="3"/>
        <circle cx="8"  cy="90" r="2" fill="#1a7bbf"/>
        <circle cx="14" cy="90" r="2" fill="#1a7bbf"/>
    </svg>
    <div>
        <div class="app-brand-name"><span class="dk">DK</span><span class="sft">Soft</span></div>
        <div class="app-brand-tagline">Your Digital Partner</div>
    </div>
</a>
    <nav id="app-nav">
    {{-- Liens communs (si besoin) --}}
    @hasSection('nav')
        @yield('nav')
    @else
        {{-- Par défaut pour les pages sans section 'nav' --}}
        @if(!$isAdmin)
            {{-- Lien "Système de Gestion Intégré" pour l'utilisateur normal --}}
            <a href="" class="btn-header btn-blue">
                <i class="fas fa-chalkboard-user"></i> Système Gestion Intégré
            </a>
        @endif
    @endif

    {{-- Déconnexion selon le type d'utilisateur --}}
    @if($isAdmin)
        {{-- Admin : formulaire de déconnexion spécifique --}}
        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-header btn-orange">
                🔓 Déconnexion Admin
            </button>
        </form>
    @elseif(auth()->check())
        {{-- Utilisateur normal connecté --}}
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-header btn-ghost">
                🔓 Déconnexion
            </button>
        </form>
    @endif
</nav>
    </header>

    <main class="flex-1 flex items-center justify-center @yield('content-class') px-4 py-10">
        @yield('content')
    </main>

    @stack('scripts')
    @livewireScripts

    {{-- Script pour générer dynamiquement les spots (meilleure performance) --}}
    <script>
        (function() {
            const container = document.getElementById('spots-container');
            if (!container) return;

            const colors = ['blue', 'orange'];
            const spotCount = 24;

            for (let i = 0; i < spotCount; i++) {
                const spot = document.createElement('div');
                const colorClass = colors[Math.floor(Math.random() * colors.length)];
                spot.classList.add('spot');
                if (colorClass === 'orange') spot.classList.add('orange');

                const size = Math.random() * 300 + 80; // entre 80 et 380px
                const left = Math.random() * 100; // pourcentage
                const top = Math.random() * 100;

                spot.style.width = `${size}px`;
                spot.style.height = `${size}px`;
                spot.style.left = `${left}%`;
                spot.style.top = `${top}%`;
                spot.style.animationDuration = `${Math.random() * 20 + 15}s`;
                spot.style.animationDelay = `${Math.random() * 10}s`;

                container.appendChild(spot);
            }
        })();
    </script>
</body>
</html>