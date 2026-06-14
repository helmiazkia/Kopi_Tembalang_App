<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Lodo Kopi Tembalang' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --lime:   #D4E971;
            --dark:   #0F1117;
            --dark-2: #181C26;
            --dark-3: #1F2433;
            --slate:  #94A3B8;
            --white:  #FFFFFF;
            --off-white: #F8FAFC;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Grain overlay (dark panel only) ── */
        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.06'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* ── Glow blobs ── */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 {
            width: 500px; height: 500px;
            background: var(--lime);
            opacity: 0.07;
            top: -150px; left: -150px;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .blob-2 {
            width: 350px; height: 350px;
            background: #6366F1;
            opacity: 0.06;
            bottom: -80px; right: 0;
            animation: drift 18s ease-in-out infinite alternate-reverse;
        }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.1); }
        }

        /* ── Screen wrapper ── */
        .screen {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ════════════════════════════════
           LEFT PANEL — dark branding
        ════════════════════════════════ */
        .panel-left {
            display: none;
            width: 50%;
            background: var(--dark);
            position: relative;
            overflow: hidden;
            padding: 56px 64px;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 1024px) {
            .panel-left { display: flex; }
        }

        /* Subtle grid */
        .panel-left .grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(212,233,113,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,233,113,0.03) 1px, transparent 1px);
            background-size: 56px 56px;
            z-index: 0;
        }

        .panel-brand {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 68px; height: 68px;
            border-radius: 18px;
            object-fit: cover;
            border: 2px solid rgba(212,233,113,0.2);
            margin-bottom: 44px;
            box-shadow: 0 0 48px rgba(212,233,113,0.12);
        }

        .brand-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(60px, 7vw, 88px);
            line-height: 0.88;
            color: #fff;
            margin-bottom: 24px;
        }

        .brand-title span { color: var(--lime); display: block; }

        .brand-desc {
            font-size: 11px;
            font-weight: 300;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--slate);
            line-height: 2;
        }

        /* Stats */
        .panel-stats {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 28px;
        }

        .stat-item {
            border-top: 1px solid rgba(255,255,255,0.07);
            padding-top: 16px;
        }

        .stat-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            color: var(--lime);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--slate);
            font-weight: 500;
        }

        /* ════════════════════════════════
           RIGHT PANEL — white form
        ════════════════════════════════ */
        .panel-right {
            flex: 1;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
        }

        /* Subtle dot pattern on white panel */
        .panel-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, #e2e8f0 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Top accent line */
        .panel-right::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--lime), transparent);
            display: block;
        }

        @media (min-width: 1024px) {
            .panel-right::after { display: none; }
        }

        .form-card {
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Mobile logo */
        .mobile-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }

        .mobile-logo img {
            width: 40px; height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .mobile-logo-text {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            letter-spacing: 0.05em;
            color: var(--dark);
            line-height: 1;
        }

        .mobile-logo-text span { color: #65a30d; }

        @media (min-width: 1024px) { .mobile-logo { display: none; } }

        /* Heading */
        .form-heading {
            margin-bottom: 32px;
        }

        .form-heading h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            letter-spacing: 0.02em;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 6px;
        }

        .form-heading h2 span { color: #65a30d; }

        .form-heading p {
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--slate);
            font-weight: 500;
        }

        /* Inputs */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .form-input {
            width: 100%;
            padding: 14px 18px;
            background: var(--off-white);
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            color: var(--dark);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder { color: #CBD5E1; }

        .form-input:focus {
            border-color: #a3c92a;
            box-shadow: 0 0 0 3px rgba(163,201,42,0.12);
            background: #fff;
        }

        .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #CBD5E1;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            transition: color 0.2s;
        }

        .input-icon:hover { color: #65a30d; }

        /* Form meta */
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #65a30d;
        }

        .remember-label span {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748B;
        }

        .forgot-link {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94A3B8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: #65a30d; }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--dark);
            color: var(--lime);
            border: none;
            border-radius: 12px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 15px;
            letter-spacing: 0.25em;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 24px rgba(15,17,23,0.15);
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            background: #1e293b;
            box-shadow: 0 8px 32px rgba(15,17,23,0.25);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(15,17,23,0.15);
        }

        /* Error */
        .form-error {
            margin-top: 5px;
            font-size: 11px;
            color: #EF4444;
            font-weight: 500;
        }

        /* Footer */
        .form-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #F1F5F9;
            text-align: center;
        }

        .form-footer p {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #CBD5E1;
        }

        /* Corner accent on form card */
        .card-accent {
            position: absolute;
            top: -1px; left: 0;
            width: 48px; height: 3px;
            background: var(--lime);
            border-radius: 0 0 4px 0;
            display: none;
        }

        @media (min-width: 1024px) { .card-accent { display: block; } }
    </style>
</head>
<body class="antialiased">
    <div class="screen">
        {{ $slot }}
    </div>
</body>
</html>