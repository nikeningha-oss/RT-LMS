<!--
    ============================================================
    TRACKLANE - HOME PAGE
    ============================================================
    This is the landing page for the Tracklane logistics system.
    It's designed to:
    - Welcome visitors
    - Explain what Tracklane does
    - Show key features
    - Encourage users to sign up or log in
    
    WHO SEES THIS PAGE:
    - Anyone who visits the website root URL
    - Both logged-in and non-logged-in users
    - But we show different content based on login status
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Tracklane - Smart Logistics Management</title>

    <!-- ============================================================
         BOOTSTRAP 5 CSS
         ============================================================ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ============================================================
         GOOGLE FONTS - INTER
         ============================================================ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ============================================================
         TABLER ICONS
         ============================================================ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

    <!-- ============================================================
         CUSTOM STYLES
         ============================================================ -->
    <style>
        /* ============================================================
           ROOT VARIABLES
           ============================================================ */
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
            --radius-lg: 16px;
            --radius-md: 8px;
        }

        /* ============================================================
           GLOBAL RESET
           ============================================================ */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #FFFFFF;
            color: var(--color-text-primary);
            margin: 0;
            padding: 0;
        }

        /* ============================================================
           NAVBAR
           ============================================================ */
        .navbar-tracklane {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 0.5px solid var(--color-border);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            transition: all 0.3s ease;
        }

        .navbar-tracklane.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .navbar-brand-custom {
            font-weight: 700;
            font-size: 22px;
            color: var(--color-text-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand-custom .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            color: var(--color-bg-dark);
        }

        .navbar-brand-custom .logo-icon i {
            font-size: 18px;
        }

        .navbar-brand-custom span:last-child {
            color: var(--color-primary);
        }

        /* ============================================================
           HERO SECTION
           ============================================================ */
        .hero-section {
            padding: 140px 0 80px;
            background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-weight: 800;
            font-size: 48px;
            line-height: 1.1;
            color: var(--color-text-primary);
            margin-bottom: 20px;
        }

        .hero-title .highlight {
            color: var(--color-primary);
            position: relative;
        }

        .hero-title .highlight::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--color-primary-light);
            border-radius: 4px;
            z-index: -1;
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--color-text-secondary);
            max-width: 500px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--color-primary);
            color: var(--color-bg-dark);
            font-weight: 600;
            padding: 12px 32px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-primary:hover {
            background: var(--color-primary-dark);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(20, 184, 166, 0.3);
        }

        .btn-hero-secondary {
            background: transparent;
            color: var(--color-text-primary);
            font-weight: 600;
            padding: 12px 32px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-secondary:hover {
            background: var(--color-bg-body);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Hero Image / Illustration */
        .hero-image {
            position: relative;
        }

        .hero-image .placeholder-illustration {
            background: linear-gradient(135deg, #CCFBF1 0%, #14B8A6 100%);
            border-radius: var(--radius-lg);
            padding: 40px;
            text-align: center;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-image .placeholder-illustration i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 20px;
        }

        .hero-image .placeholder-illustration h4 {
            color: #FFFFFF;
            font-weight: 600;
        }

        .hero-image .placeholder-illustration p {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Floating badges */
        .floating-badge {
            position: absolute;
            background: #FFFFFF;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: float 3s ease-in-out infinite;
        }

        .floating-badge:nth-child(2) {
            top: 10%;
            right: -10%;
            animation-delay: 1s;
        }

        .floating-badge:nth-child(3) {
            bottom: 10%;
            left: -10%;
            animation-delay: 2s;
        }

        .floating-badge .badge-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-badge .badge-icon.green {
            background: #D1FAE5;
            color: #065F46;
        }

        .floating-badge .badge-icon.blue {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .floating-badge .badge-icon.orange {
            background: #FEF3C7;
            color: #92400E;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* ============================================================
           FEATURES SECTION
           ============================================================ */
        .features-section {
            padding: 80px 0;
            background: #FFFFFF;
        }

        .section-badge {
            display: inline-block;
            background: var(--color-primary-light);
            color: var(--color-primary-dark);
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .section-title {
            font-weight: 700;
            font-size: 36px;
            color: var(--color-text-primary);
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--color-text-secondary);
            max-width: 600px;
            margin: 0 auto 48px;
        }

        .feature-card {
            padding: 32px 24px;
            border-radius: var(--radius-lg);
            border: 0.5px solid var(--color-border);
            background: #FFFFFF;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border-color: var(--color-primary);
        }

        .feature-card .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .feature-card .feature-icon.teal {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .feature-card .feature-icon.blue {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .feature-card .feature-icon.orange {
            background: #FEF3C7;
            color: #92400E;
        }

        .feature-card .feature-icon.purple {
            background: #EDE9FE;
            color: #6D28D9;
        }

        .feature-card .feature-icon.green {
            background: #D1FAE5;
            color: #065F46;
        }

        .feature-card .feature-icon.pink {
            background: #FCE7F3;
            color: #9D174D;
        }

        .feature-card h5 {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .feature-card p {
            color: var(--color-text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* ============================================================
           STATS SECTION
           ============================================================ */
        .stats-section {
            padding: 80px 0;
            background: var(--color-bg-dark);
        }

        .stats-section .section-title {
            color: #FFFFFF;
        }

        .stats-section .section-subtitle {
            color: rgba(255, 255, 255, 0.6);
        }

        .stat-item {
            text-align: center;
            padding: 24px;
        }

        .stat-number {
            font-weight: 800;
            font-size: 48px;
            color: var(--color-primary);
            display: block;
            margin-bottom: 4px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        /* ============================================================
           HOW IT WORKS SECTION
           ============================================================ */
        .how-it-works-section {
            padding: 80px 0;
            background: var(--color-bg-body);
        }

        .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--color-primary);
            color: var(--color-bg-dark);
            font-weight: 700;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .step-card {
            text-align: center;
            padding: 32px 24px;
        }

        .step-card h5 {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .step-card p {
            color: var(--color-text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ============================================================
           CTA SECTION
           ============================================================ */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #14B8A6 0%, #0F766E 100%);
        }

        .cta-section h2 {
            color: #FFFFFF;
            font-weight: 700;
            font-size: 36px;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
        }

        .btn-cta {
            background: #FFFFFF;
            color: var(--color-primary);
            font-weight: 600;
            padding: 14px 40px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            color: var(--color-primary-dark);
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .footer-tracklane {
            padding: 60px 0 30px;
            background: var(--color-bg-dark);
            border-top: 0.5px solid rgba(255, 255, 255, 0.06);
        }

        .footer-tracklane h6 {
            color: #FFFFFF;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .footer-tracklane a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .footer-tracklane a:hover {
            color: var(--color-primary);
        }

        .footer-tracklane .footer-brand {
            font-weight: 700;
            font-size: 20px;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-tracklane .footer-brand .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            color: var(--color-bg-dark);
        }

        .footer-tracklane .footer-brand .logo-icon i {
            font-size: 18px;
        }

        .footer-tracklane p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            margin-top: 8px;
        }

        .footer-divider {
            border-top: 0.5px solid rgba(255, 255, 255, 0.06);
            margin: 30px 0 20px;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
            margin: 0;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 36px;
            }

            .hero-section {
                padding: 120px 0 60px;
            }

            .floating-badge {
                display: none;
            }

            .section-title {
                font-size: 28px;
            }

            .stat-number {
                font-size: 36px;
            }

            .cta-section h2 {
                font-size: 28px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 28px;
            }

            .hero-subtitle {
                font-size: 16px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-buttons .btn-hero-primary,
            .hero-buttons .btn-hero-secondary {
                text-align: center;
                justify-content: center;
            }

            .section-title {
                font-size: 24px;
            }

            .stat-number {
                font-size: 28px;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- ============================================================
         NAVBAR
         ============================================================ -->
    <nav class="navbar-tracklane" id="navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">
                
                <!-- Logo -->
                <a href="/" class="navbar-brand-custom">
                    <span class="logo-icon">
                        <i class="ti ti-route"></i>
                    </span>
                    Track<span>lane</span>
                </a>

                <!-- Navigation Links -->
                <div class="d-none d-md-flex align-items-center gap-4">
                    <a href="#features" class="text-decoration-none" style="color: var(--color-text-secondary); font-weight: 500; font-size: 14px;">Features</a>
                    <a href="#how-it-works" class="text-decoration-none" style="color: var(--color-text-secondary); font-weight: 500; font-size: 14px;">How It Works</a>
                    <a href="#stats" class="text-decoration-none" style="color: var(--color-text-secondary); font-weight: 500; font-size: 14px;">Stats</a>
                </div>

                <!-- Auth Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::check())
                        <!-- If logged in, show dashboard link -->
                        <a href="{{ route('dashboard') }}" class="btn-hero-primary" style="padding: 8px 20px; font-size: 14px;">
                            <i class="ti ti-layout-dashboard"></i> Dashboard
                        </a>
                    @else
                        <!-- If not logged in, show login and register -->
                        <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--color-text-secondary); font-weight: 500; font-size: 14px; padding: 8px 16px;">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-primary" style="padding: 8px 20px; font-size: 14px;">
                            <i class="ti ti-user-plus"></i> Get Started
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================
         HERO SECTION
         ============================================================ -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left: Text Content -->
                <div class="col-lg-6">
                    <div class="mb-4">
                        <span class="section-badge">
                            <i class="ti ti-rocket me-1"></i> Next-Gen Logistics
                        </span>
                    </div>
                    
                    <h1 class="hero-title">
                        Smart Logistics <br>
                        <span class="highlight">Real-Time Tracking</span>
                    </h1>
                    
                    <p class="hero-subtitle">
                        Track your deliveries in real-time, assign drivers instantly, 
                        and manage your entire logistics network from one powerful dashboard.
                    </p>
                    
                    <div class="hero-buttons">
                        @if(Auth::check())
                            <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                                <i class="ti ti-layout-dashboard"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-hero-primary">
                                <i class="ti ti-user-plus"></i> Get Started Free
                            </a>
                            <a href="#features" class="btn-hero-secondary">
                                Learn More <i class="ti ti-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right: Hero Image -->
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-image">
                        <div class="placeholder-illustration">
                            <i class="ti ti-truck-delivery"></i>
                            <h4>Track Every Delivery</h4>
                            <p>Real-time GPS tracking for all your shipments</p>
                            
                            <div style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; justify-content: center;">
                                <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 8px;">
                                    <i class="ti ti-package" style="color: #FFFFFF;"></i>
                                    <span style="color: #FFFFFF; font-weight: 500; margin-left: 8px;">1,234 Orders</span>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 8px;">
                                    <i class="ti ti-users" style="color: #FFFFFF;"></i>
                                    <span style="color: #FFFFFF; font-weight: 500; margin-left: 8px;">56 Drivers</span>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 8px;">
                                    <i class="ti ti-clock" style="color: #FFFFFF;"></i>
                                    <span style="color: #FFFFFF; font-weight: 500; margin-left: 8px;">18 min avg</span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badges -->
                        <div class="floating-badge" style="top: 5%; right: -5%;">
                            <div class="badge-icon green">
                                <i class="ti ti-check"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">98.5%</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">On-time delivery</div>
                            </div>
                        </div>

                        <div class="floating-badge" style="bottom: 15%; left: -5%;">
                            <div class="badge-icon blue">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">24/7</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">Support available</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         FEATURES SECTION
         ============================================================ -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center">
                <span class="section-badge">
                    <i class="ti ti-star me-1"></i> Features
                </span>
                <h2 class="section-title">Everything You Need</h2>
                <p class="section-subtitle">
                    Tracklane provides all the tools you need to manage your logistics efficiently
                </p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon teal">
                            <i class="ti ti-map-pin" style="font-size: 24px;"></i>
                        </div>
                        <h5>Real-Time Tracking</h5>
                        <p>Track your drivers and packages in real-time with live GPS updates.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="ti ti-users" style="font-size: 24px;"></i>
                        </div>
                        <h5>Driver Management</h5>
                        <p>Manage your entire fleet of drivers, their availability, and assignments.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="ti ti-package" style="font-size: 24px;"></i>
                        </div>
                        <h5>Order Management</h5>
                        <p>Create, assign, and track orders from pickup to delivery completion.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon purple">
                            <i class="ti ti-chart-bar" style="font-size: 24px;"></i>
                        </div>
                        <h5>Analytics & Reports</h5>
                        <p>Get insights into your operations with detailed analytics and reports.</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="ti ti-bell" style="font-size: 24px;"></i>
                        </div>
                        <h5>Instant Notifications</h5>
                        <p>Real-time alerts for order assignments, status changes, and updates.</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon pink">
                            <i class="ti ti-device-mobile" style="font-size: 24px;"></i>
                        </div>
                        <h5>Mobile Friendly</h5>
                        <p>Access your logistics system from any device, anywhere in the world.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         STATS SECTION
         ============================================================ -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="text-center">
                <span class="section-badge" style="background: rgba(255,255,255,0.1); color: var(--color-primary);">
                    <i class="ti ti-chart-line me-1"></i> Our Impact
                </span>
                <h2 class="section-title">Trusted by Businesses</h2>
                <p class="section-subtitle">
                    Join thousands of businesses that rely on Tracklane for their logistics
                </p>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Orders Delivered</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Active Drivers</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                        <span class="stat-label">On-Time Delivery</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number">4.9⭐</span>
                        <span class="stat-label">User Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         HOW IT WORKS SECTION
         ============================================================ -->
    <section class="how-it-works-section" id="how-it-works">
        <div class="container">
            <div class="text-center">
                <span class="section-badge">
                    <i class="ti ti-settings me-1"></i> How It Works
                </span>
                <h2 class="section-title">Simple Three-Step Process</h2>
                <p class="section-subtitle">
                    Get started with Tracklane in just three simple steps
                </p>
            </div>

            <div class="row g-4 mt-3">
                <!-- Step 1 -->
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number mx-auto">1</div>
                        <h5>Create an Account</h5>
                        <p>Sign up as a customer or driver and set up your profile.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number mx-auto">2</div>
                        <h5>Create or Accept Orders</h5>
                        <p>Customers create orders, drivers accept and deliver them.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number mx-auto">3</div>
                        <h5>Track in Real-Time</h5>
                        <p>Track every delivery live with real-time GPS updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         CTA SECTION
         ============================================================ -->
    <section class="cta-section">
        <div class="container text-center">
            <h2>Ready to Get Started?</h2>
            <p class="mb-4">
                Join Tracklane today and transform your logistics operations.
            </p>
            @if(Auth::check())
                <a href="{{ route('dashboard') }}" class="btn-cta">
                    <i class="ti ti-layout-dashboard"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-cta">
                    <i class="ti ti-user-plus"></i> Create Free Account
                </a>
            @endif
        </div>
    </section>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="footer-tracklane">
        <div class="container">
            <div class="row g-4">
                <!-- Brand -->
                <div class="col-md-4">
                    <div class="footer-brand">
                        <span class="logo-icon">
                            <i class="ti ti-route"></i>
                        </span>
                        Track<span style="color: var(--color-primary);">lane</span>
                    </div>
                    <p style="color: rgba(255,255,255,0.4); max-width: 300px; margin-top: 12px;">
                        Smart logistics management platform for modern businesses.
                    </p>
                </div>
                 <!-- In the footer or bottom of the page -->
                   <div style="text-align:center; padding:20px; margin-top:20px;">
                      <a href="{{ route('admin.login') }}" 
                              style="font-size:12px; color:#94A3B8; text-decoration:none; transition:color 0.2s;"
                              onmouseover="this.style.color='#14B8A6'" 
                               onmouseout="this.style.color='#94A3B8'">
                             <i class="ti ti-shield"></i> Admin Access
                        </a>
                   </div>
                <!-- Quick Links -->
                <div class="col-md-2">
                    <h6>Product</h6>
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#stats">Stats</a>
                </div>

                <div class="col-md-2">
                    <h6>Support</h6>
                    <a href="#">Help Center</a>
                    <a href="#">Contact Us</a>
                    <a href="#">FAQ</a>
                </div>

                <div class="col-md-2">
                    <h6>Legal</h6>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Cookie Policy</a>
                </div>

                <div class="col-md-2">
                    <h6>Connect</h6>
                    <a href="#"><i class="ti ti-brand-twitter me-2"></i> Twitter</a>
                    <a href="#"><i class="ti ti-brand-linkedin me-2"></i> LinkedIn</a>
                    <a href="#"><i class="ti ti-brand-github me-2"></i> GitHub</a>
                </div>
            </div>

            <div class="footer-divider"></div>
            
            <div class="footer-bottom">
                <p>
                    <i class="ti ti-copyright me-1"></i> 2024 Tracklane. All rights reserved.
                </p>
                <p>
                    Built with <i class="ti ti-heart" style="color: var(--color-primary);"></i> for logistics professionals
                </p>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         BOOTSTRAP 5 JAVASCRIPT
         ============================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================================================
         CUSTOM JAVASCRIPT
         ============================================================ -->
    <script>
        // ============================================================
        // NAVBAR SCROLL EFFECT
        // ============================================================
        // Adds a shadow to the navbar when you scroll down
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ============================================================
        // SMOOTH SCROLL FOR NAVIGATION LINKS
        // ============================================================
        // Makes links scroll smoothly to sections instead of jumping
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>


</div>
</body>
</html>