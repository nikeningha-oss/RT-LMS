<!--
    ============================================================
    TRACKLANE GUEST LAYOUT
    ============================================================
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Tracklane - Logistics System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

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
            --radius-lg: 16px;
            --radius-md: 8px;
            --shadow-card: 0 10px 25px rgba(0,0,0,0.05);
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 4800px;
            padding: 20px;
            margin: 0 auto;
        }

        .auth-card {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            background: #FFFFFF;
            box-shadow: var(--shadow-card);
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

        .form-control {
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 14px;
            border: 0.5px solid var(--color-border);
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: #FFFFFF;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
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
            width: 100%;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .alert {
            border-radius: var(--radius-md);
            font-size: 13px;
            padding: 10px 14px;
        }
        
        .alert-success {
            background: #CCFBF1;
            color: #0F766E;
            border: none;
        }
        
        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: none;
        }
        
        .alert-warning {
            background: #FEF3C7;
            color: #92400E;
            border: none;
        }

        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }
        .text-accent { color: var(--color-primary); }
        .bg-accent-light { background: var(--color-primary-light); }

        @media (max-width: 576px) {
            .auth-wrapper {
                padding: 10px;
            }
            .auth-card {
                padding: 24px 18px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <div class="auth-wrapper">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>