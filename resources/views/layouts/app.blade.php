<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tracklane - Logistics System')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #14B8A6;
            --color-primary-dark: #0F766E;
            --color-primary-light: #CCFBF1;
            --color-bg-dark: #0B1220;
            --color-text-primary: #0F172A;
            --color-text-secondary: #64748B;
            --color-text-muted: #94A3B8;
            --color-border: #E2E8F0;
            --color-bg-body: #F8FAFC;
            --radius-lg: 16px;
            --radius-md: 8px;
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--color-bg-body); margin: 0; padding: 0; }

        .sidebar-tracklane {
            width: 64px;
            background: var(--color-bg-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px 0;
            gap: 8px;
            flex-shrink: 0;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1030;
        }

        .sidebar-logo {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .sidebar-logo i { font-size: 18px; color: var(--color-bg-dark); }

        .sidebar-item {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-muted);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            border: none;
            background: transparent;
        }

        .sidebar-item:hover { background: rgba(20, 184, 166, 0.1); color: var(--color-primary); }
        .sidebar-item.active { background: rgba(20, 184, 166, 0.18); color: #2DD4BF; }
        .sidebar-item i { font-size: 18px; }

        .sidebar-footer { margin-top: auto; }

        .main-content-tracklane {
            margin-left: 64px;
            padding: 18px 22px;
            min-height: 100vh;
        }

        .navbar-tracklane { background: transparent; padding: 0; margin-bottom: 20px; }

        .user-avatar-tracklane {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: var(--color-bg-dark);
            cursor: pointer;
        }

        .notification-bell { position: relative; cursor: pointer; }
        .notification-bell .badge-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #E24B4A;
        }

        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }
        .text-accent { color: var(--color-primary); }
        .bg-accent-light { background: var(--color-primary-light); }
        .gap-14 { gap: 14px; }

        @media (max-width: 768px) {
            .sidebar-tracklane { width: 56px; padding: 12px 0; }
            .main-content-tracklane { margin-left: 56px; padding: 12px; }
            .sidebar-item { width: 36px; height: 36px; }
            .sidebar-item i { font-size: 16px; }
        }
    </style>
</head>
<body>

    <div class="@yield('container-class', 'auth-wrapper')">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>