<!--
    TRACKLANE MAIN LAYOUT
    This is the base UI for all pages (login, register, dashboards)
    Includes Bootstrap + Google Fonts + Icons
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Tracklane - Logistics System')</title>

    <!-- ===============================
         BOOTSTRAP 5
    ================================ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ===============================
         GOOGLE FONTS - INTER
         Clean modern UI font
    ================================ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ===============================
         TABLER ICONS
         Modern icon set used in the design
    ================================ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

    <!-- ===============================
         BOOTSTRAP ICONS (fallback)
    ================================ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ===============================
         CUSTOM THEME STYLES (TRACKLANE)
    ================================ -->
    <style>
        /* ===============================
           ROOT VARIABLES
        ================================ */
        :root {
            --color-primary: #14B8A6;
            --color-primary-dark: #0F766E;
            --color-primary-light: #CCFBF1;
            --color-bg-dark: #0B1220;
            --color-bg-light: #1A2332;
            --color-text-primary: #0F172A;
            --color-text-secondary: #64748B;
            --color-text-muted: #94A3B8;
            --color-border: #E2E8F0;
            --color-bg-body: #F8FAFC;
            --color-success: #10B981;
            --color-warning: #F59E0B;
            --color-danger: #E24B4A;
            --color-delayed: #D85A30;
            --radius-lg: 16px;
            --radius-md: 8px;
            --radius-sm: 6px;
            --shadow-card: 0 10px 25px rgba(0,0,0,0.05);
            --shadow-dropdown: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* ===============================
           GLOBAL RESET & BASE
        ================================ */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-bg-body);
            color: var(--color-text-primary);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: var(--color-primary);
            transition: all 0.2s ease;
        }

        a:hover {
            color: var(--color-primary-dark);
        }

        /* ===============================
           AUTH PAGES (Login / Register)
        ================================ */
        .auth-wrapper {
            max-width: 420px;
            margin: 60px auto;
            padding: 0 15px;
        }

        @media (min-width: 768px) {
            .auth-wrapper {
                margin: 80px auto;
            }
        }

        .auth-card {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            background: #FFFFFF;
            box-shadow: var(--shadow-card);
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 24px 18px;
            }
        }

        .app-title {
            font-weight: 700;
            font-size: 22px;
            color: var(--color-text-primary);
            text-align: center;
            letter-spacing: -0.02em;
        }

        .app-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            margin-bottom: 16px;
        }

        .app-logo i {
            font-size: 28px;
            color: var(--color-bg-dark);
        }

        /* ===============================
           FORM ELEMENTS
        ================================ */
        .form-control {
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 14px;
            border: 0.5px solid var(--color-border);
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: #FFFFFF;
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
        }

        .form-control.is-invalid:focus {
            border-color: var(--color-danger);
            box-shadow: 0 0 0 3px rgba(226, 75, 74, 0.1);
        }

        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: var(--color-text-primary);
            margin-bottom: 6px;
            font-family: 'Inter', sans-serif;
        }

        .form-label i {
            font-size: 16px;
            margin-right: 6px;
        }

        .form-check-input:checked {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
            border-color: var(--color-primary);
        }

        .form-check-label {
            font-size: 13px;
            color: var(--color-text-secondary);
            font-family: 'Inter', sans-serif;
        }

        /* ===============================
           BUTTONS
        ================================ */
        .btn-primary {
            background: var(--color-primary);
            border: none;
            color: var(--color-bg-dark);
            font-weight: 600;
            border-radius: var(--radius-md);
            padding: 10px 20px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-outline-primary {
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
            background: transparent;
            border-radius: var(--radius-md);
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: var(--color-primary);
            color: var(--color-bg-dark);
            border-color: var(--color-primary);
        }

        /* ===============================
           DASHBOARD COMPONENTS
        ================================ */
        /* Sidebar */
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
            transition: all 0.2s ease;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-logo i {
            font-size: 18px;
            color: var(--color-bg-dark);
        }

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
            position: relative;
            border: none;
            background: transparent;
        }

        .sidebar-item:hover {
            background: rgba(20, 184, 166, 0.1);
            color: var(--color-primary);
        }

        .sidebar-item.active {
            background: rgba(20, 184, 166, 0.18);
            color: #2DD4BF;
        }

        .sidebar-item i {
            font-size: 18px;
        }

        .sidebar-item .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--color-danger);
        }

        .sidebar-divider {
            width: 32px;
            height: 1px;
            background: rgba(255, 255, 255, 0.06);
            margin: 8px 0;
        }

        .sidebar-footer {
            margin-top: auto;
        }

        /* Main Content */
        .main-content-tracklane {
            margin-left: 64px;
            padding: 18px 22px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .sidebar-tracklane {
                width: 56px;
                padding: 12px 0;
            }
            
            .main-content-tracklane {
                margin-left: 56px;
                padding: 12px;
            }
            
            .sidebar-item {
                width: 36px;
                height: 36px;
            }
            
            .sidebar-item i {
                font-size: 16px;
            }
        }

        /* Cards */
        .card-tracklane {
            background: #FFFFFF;
            border: 0.5px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 12px;
            transition: all 0.2s ease;
        }

        .card-tracklane:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        /* Status Badges */
        .badge-status {
            padding: 3px 8px;
            border-radius: var(--radius-md);
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            font-family: 'Inter', sans-serif;
        }

        .badge-status.in-transit {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-status.delivered {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-status.pending {
            background: #F1F5F9;
            color: #475569;
        }

        .badge-status.delayed {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-status.assigned {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-status.picked-up {
            background: #E0E7FF;
            color: #3730A3;
        }

        .badge-status.cancelled {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* Metric Cards */
        .metric-card-tracklane {
            background: #FFFFFF;
            border: 0.5px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 11px 12px;
            transition: all 0.2s ease;
        }

        .metric-card-tracklane:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .metric-label {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin-bottom: 4px;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
        }

        .metric-value {
            font-size: 20px;
            font-weight: 600;
            color: var(--color-text-primary);
            letter-spacing: -0.02em;
            font-family: 'Inter', sans-serif;
        }

        .metric-change {
            font-size: 11px;
            margin-top: 2px;
            font-family: 'Inter', sans-serif;
        }

        .metric-change.positive {
            color: var(--color-primary);
        }

        .metric-change.negative {
            color: var(--color-danger);
        }

        /* Driver Status Dot */
        .driver-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .driver-dot.online {
            background: var(--color-success);
        }

        .driver-dot.busy {
            background: var(--color-warning);
        }

        .driver-dot.offline {
            background: var(--color-text-muted);
        }

        /* Tables */
        .table-tracklane {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
            font-family: 'Inter', sans-serif;
        }

        .table-tracklane th {
            color: var(--color-text-secondary);
            font-weight: 500;
            padding: 6px 4px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table-tracklane td {
            padding: 7px 4px;
            color: var(--color-text-primary);
            border-top: 0.5px solid var(--color-border);
            font-size: 12px;
        }

        .table-tracklane tr:first-child td {
            border-top: none;
        }

        /* Top Navigation */
        .navbar-tracklane {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .navbar-tracklane .navbar-brand {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-text-primary);
            font-family: 'Inter', sans-serif;
        }

        .navbar-tracklane .nav-link {
            color: var(--color-text-secondary);
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .navbar-tracklane .nav-link:hover {
            color: var(--color-text-primary);
            background: rgba(0, 0, 0, 0.04);
        }

        .navbar-tracklane .nav-link.active {
            color: var(--color-primary);
            background: rgba(20, 184, 166, 0.08);
        }

        /* User Avatar */
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
            font-family: 'Inter', sans-serif;
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell .badge-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--color-danger);
        }

        /* ===============================
           UTILITY CLASSES
        ================================ */
        .text-accent {
            color: var(--color-primary);
        }

        .bg-accent-light {
            background: var(--color-primary-light);
        }

        .fw-500 {
            font-weight: 500;
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
        }

        .gap-8 {
            gap: 8px;
        }

        .gap-10 {
            gap: 10px;
        }

        .gap-12 {
            gap: 12px;
        }

        .gap-14 {
            gap: 14px;
        }

        /* ===============================
           SCROLLBAR
        ================================ */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-muted);
        }

        /* ===============================
           RESPONSIVE
        ================================ */
        @media (max-width: 576px) {
            .auth-wrapper {
                margin: 30px auto;
            }
            
            .metric-value {
                font-size: 17px;
            }
            
            .table-tracklane {
                font-size: 11px;
            }
            
            .table-tracklane td,
            .table-tracklane th {
                padding: 4px 3px;
            }
        }

        /* ===============================
           ANIMATIONS
        ================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .pulse-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }
    </style>
</head>

<body>

    <!-- PAGE CONTENT -->
    <div class="@yield('container-class', 'auth-wrapper')">
        @yield('content')
    </div>

    <!-- ===============================
         SCRIPTS
    ================================ -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Stack for page-specific scripts -->
    @stack('scripts')

</body>
</html>