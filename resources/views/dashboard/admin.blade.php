@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Tracklane')

@section('content')
    <!-- Sidebar -->
    @include('layouts.sidebar')
    
    <div class="main-content-tracklane">
        <!-- Top Navigation -->
        @include('layouts.top-nav')

        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <style>
            :root {
                --primary: #0D9488;
                --primary-light: #14B8A6;
                --primary-bg: #CCFBF1;
                --success: #10B981;
                --success-bg: #D1FAE5;
                --warning: #F59E0B;
                --warning-bg: #FEF3C7;
                --danger: #E24B4A;
                --danger-bg: #FEE2E2;
                --gray-50: #F8FAFC;
                --gray-100: #F1F5F9;
                --gray-200: #E2E8F0;
                --gray-400: #94A3B8;
                --gray-500: #64748B;
                --gray-600: #475569;
                --gray-700: #334155;
                --gray-800: #1E293B;
                --gray-900: #0F172A;
            }

            .stat-card-admin {
                background: #FFFFFF;
                border: 0.5px solid var(--gray-200);
                border-radius: 10px;
                padding: 14px 16px;
                transition: all 0.2s ease;
                position: relative;
                overflow: hidden;
            }
            .stat-card-admin:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            }
            .stat-card-admin .stat-icon {
                position: absolute;
                right: 12px;
                top: 12px;
                font-size: 28px;
                opacity: 0.15;
            }
            .stat-card-admin .label {
                font-size: 12px;
                color: var(--gray-500);
                margin-bottom: 4px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }
            .stat-card-admin .value {
                font-size: 24px;
                font-weight: 700;
                color: var(--gray-900);
            }
            .stat-card-admin .change {
                font-size: 11px;
                margin-top: 4px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .stat-card-admin .change.positive { color: var(--success); }
            .stat-card-admin .change.negative { color: var(--danger); }
            .stat-card-admin .change.neutral { color: var(--gray-400); }

            .quick-action-btn {
                background: var(--gray-50);
                padding: 16px 12px;
                border-radius: 10px;
                text-align: center;
                text-decoration: none;
                color: var(--gray-900);
                font-size: 12px;
                font-weight: 500;
                border: 0.5px solid var(--gray-200);
                transition: all 0.2s ease;
                cursor: pointer;
                display: block;
            }
            .quick-action-btn:hover {
                background: var(--primary-bg);
                border-color: var(--primary-light);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }
            .quick-action-btn i,
            .quick-action-btn .fa-solid,
            .quick-action-btn .fa-regular {
                font-size: 22px;
                display: block;
                margin-bottom: 6px;
            }

            .earnings-card {
                background: #FFFFFF;
                border: 0.5px solid var(--gray-200);
                border-radius: 10px;
                padding: 14px 16px;
                border-left: 4px solid var(--primary-light);
                transition: all 0.2s ease;
            }
            .earnings-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }
            .earnings-card .label {
                font-size: 11px;
                color: var(--gray-500);
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-weight: 600;
            }
            .earnings-card .value {
                font-size: 20px;
                font-weight: 700;
                color: var(--gray-900);
                margin-top: 2px;
            }
            .earnings-card .value.driver { color: var(--success); }
            .earnings-card .value.platform { color: #3B82F6; }
            .earnings-card .value.total { color: var(--primary); }
            .earnings-card .sub-text {
                font-size: 11px;
                color: var(--gray-400);
                margin-top: 2px;
            }

            .driver-earnings-table {
                width: 100%;
                font-size: 12px;
                border-collapse: collapse;
            }
            .driver-earnings-table thead th {
                padding: 10px 8px;
                text-align: left;
                font-weight: 600;
                color: var(--gray-500);
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                border-bottom: 1px solid var(--gray-200);
            }
            .driver-earnings-table tbody td {
                padding: 10px 8px;
                border-bottom: 0.5px solid var(--gray-100);
                color: var(--gray-900);
                vertical-align: middle;
            }
            .driver-earnings-table tbody tr:hover {
                background: var(--gray-50);
            }
            .driver-earnings-table tbody tr:last-child td {
                border-bottom: none;
            }
            .driver-earnings-table .amount {
                font-weight: 600;
                color: var(--success);
            }
            .driver-earnings-table .badge-pending {
                background: var(--warning-bg);
                color: #92400E;
                padding: 3px 12px;
                border-radius: 20px;
                font-size: 10px;
                font-weight: 500;
            }
            .driver-earnings-table .badge-paid {
                background: var(--success-bg);
                color: #065F46;
                padding: 3px 12px;
                border-radius: 20px;
                font-size: 10px;
                font-weight: 500;
            }

            /* ─── WITHDRAWAL SECTION ─── */
            .withdrawal-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                margin-bottom: 14px;
            }
            .withdrawal-table-mini {
                width: 100%;
                font-size: 12px;
                border-collapse: collapse;
            }
            .withdrawal-table-mini thead th {
                padding: 8px;
                text-align: left;
                font-weight: 600;
                color: #64748B;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                border-bottom: 1px solid #E2E8F0;
            }
            .withdrawal-table-mini tbody td {
                padding: 8px;
                border-bottom: 0.5px solid #F1F5F9;
                color: #0F172A;
                vertical-align: middle;
            }
            .withdrawal-table-mini tbody tr:hover {
                background: #FAFBFC;
            }
            .withdrawal-table-mini tbody tr:last-child td {
                border-bottom: none;
            }
            .withdrawal-action-btn {
                display: inline-flex;
                align-items: center;
                gap: 3px;
                padding: 3px 10px;
                border: none;
                border-radius: 4px;
                font-size: 10px;
                font-weight: 500;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.2s;
            }
            .withdrawal-action-btn.approve {
                background: #10B981;
                color: #FFFFFF;
            }
            .withdrawal-action-btn.approve:hover {
                background: #059669;
            }
            .withdrawal-action-btn.reject {
                background: #E24B4A;
                color: #FFFFFF;
            }
            .withdrawal-action-btn.reject:hover {
                background: #DC2626;
            }
            .withdrawal-action-btn.view {
                background: #F1F5F9;
                color: #0F172A;
            }
            .withdrawal-action-btn.view:hover {
                background: #E2E8F0;
            }

            .dispatch-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 18px;
            }
            @media (max-width: 1024px) {
                .dispatch-grid { grid-template-columns: 1fr; }
            }

            .card-dispatch {
                background: #FFFFFF;
                border-radius: 12px;
                padding: 16px 18px;
                border: 0.5px solid var(--gray-200);
                transition: all 0.2s ease;
            }
            .card-dispatch:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }
            .card-dispatch .card-title {
                font-size: 14px;
                font-weight: 600;
                color: var(--gray-900);
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 4px;
            }
            .card-dispatch .badge-count {
                background: var(--gray-100);
                color: var(--gray-900);
                font-size: 11px;
                font-weight: 600;
                padding: 2px 12px;
                border-radius: 30px;
            }
            .card-dispatch .badge-count.pending { background: var(--warning-bg); color: #92400E; }
            .card-dispatch .badge-count.active { background: var(--success-bg); color: #065F46; }

            .order-item {
                background: var(--gray-50);
                border-radius: 8px;
                padding: 10px 12px;
                margin-bottom: 8px;
                border-left: 3px solid var(--gray-200);
                transition: all 0.15s ease;
            }
            .order-item:hover { background: var(--gray-100); }
            .order-item .order-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 4px;
                margin-bottom: 4px;
            }
            .order-item .order-id {
                font-weight: 600;
                font-size: 13px;
                color: var(--gray-900);
            }
            .order-item .order-meta {
                font-size: 12px;
                color: var(--gray-500);
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .order-item .order-meta i { font-size: 14px; }
            .order-item .order-details {
                font-size: 12px;
                color: var(--gray-600);
                display: flex;
                flex-wrap: wrap;
                gap: 4px 12px;
                margin-top: 2px;
            }
            .order-item .order-details .highlight { font-weight: 500; color: var(--gray-900); }
            .order-item .assign-btn {
                background: var(--primary-light);
                color: #FFFFFF;
                border: none;
                border-radius: 6px;
                padding: 3px 14px;
                font-size: 11px;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.2s;
                text-decoration: none;
                display: inline-block;
            }
            .order-item .assign-btn:hover { background: var(--primary); }

            .status-dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                margin-right: 4px;
            }
            .status-dot.available { background: var(--success); }
            .status-dot.on-route { background: var(--warning); }
            .status-dot.awaiting { background: var(--gray-400); }
            .status-dot.offline { background: var(--gray-200); }

            .driver-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--primary-bg);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: 600;
                color: var(--primary);
                flex-shrink: 0;
            }

            .btn-pay-driver {
                background: var(--primary);
                color: #FFFFFF;
                border: none;
                border-radius: 6px;
                padding: 4px 14px;
                font-size: 11px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .btn-pay-driver:hover {
                background: #0F766E;
                transform: translateY(-1px);
            }
            .btn-pay-driver:disabled {
                background: var(--gray-200);
                cursor: not-allowed;
                transform: none;
            }
            .btn-pay-driver.paid {
                background: var(--gray-400);
                cursor: default;
            }
            .btn-pay-driver.paid:hover {
                background: var(--gray-400);
                transform: none;
            }

            /* ─── SUMMARY STATS GRID ─── */
            .summary-stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                margin-bottom: 16px;
            }
            @media (max-width: 768px) {
                .summary-stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .summary-item {
                background: #FFFFFF;
                border: 0.5px solid var(--gray-200);
                border-radius: 10px;
                padding: 12px 14px;
                text-align: center;
                transition: all 0.2s ease;
            }
            .summary-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }
            .summary-item .summary-label {
                font-size: 10px;
                color: var(--gray-500);
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-weight: 600;
            }
            .summary-item .summary-value {
                font-size: 18px;
                font-weight: 700;
                color: var(--gray-900);
                margin-top: 2px;
            }
            .summary-item .summary-value.green { color: var(--success); }
            .summary-item .summary-value.blue { color: #3B82F6; }
            .summary-item .summary-value.primary { color: var(--primary); }
            .summary-item .summary-value.orange { color: var(--warning); }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                margin-bottom: 16px;
            }
            .earnings-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                margin-bottom: 16px;
            }

            /* ─── PAYMENT STATUS BADGE ─── */
            .payment-badge {
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 10px;
                font-weight: 500;
            }
            .payment-badge.paid {
                background: #D1FAE5;
                color: #065F46;
            }
            .payment-badge.pending {
                background: #FEF3C7;
                color: #92400E;
            }
        </style>

        <div style="background:var(--gray-50); padding:20px 24px; min-height:560px;">

            <!-- HEADER -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="font-size:18px; font-weight:600; color:var(--gray-900);">📊 Dashboard</div>
                    <div style="font-size:13px; color:var(--gray-500);">Welcome back, {{ Auth::user()->name }}!</div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <span style="background:var(--primary-bg); color:var(--primary); padding:6px 16px; border-radius:20px; font-size:12px; font-weight:500;">
                        <i class="ti ti-calendar"></i> {{ date('l, M d, Y') }}
                    </span>
                    <span style="background:var(--gray-100); color:var(--gray-500); padding:6px 12px; border-radius:20px; font-size:12px;">
                        <i class="ti ti-clock"></i> {{ date('h:i A') }}
                    </span>
                </div>
            </div>

            <!-- ============================================================
                 SECTION: PLATFORM SUMMARY
                 ============================================================ -->
            <div class="summary-stats-grid">
                <div class="summary-item">
                    <div class="summary-label">👨‍✈️ Total Drivers</div>
                    <div class="summary-value blue">{{ number_format($totalDrivers ?? 0) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">👤 Total Customers</div>
                    <div class="summary-value primary">{{ number_format($totalCustomers ?? 0) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">📦 Total Orders</div>
                    <div class="summary-value orange">{{ number_format($totalOrdersCount ?? 0) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">✅ Delivered Orders</div>
                    <div class="summary-value green">{{ number_format($deliveredOrders ?? 0) }}</div>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card-admin">
                    <div class="stat-icon"><i class="ti ti-truck"></i></div>
                    <div class="label">Active Deliveries</div>
                    <div class="value">{{ number_format($activeDeliveries ?? 0) }}</div>
                    <div class="change positive"><i class="ti ti-arrow-up"></i> +{{ number_format($todayOrders ?? 0) }} today</div>
                </div>
                <div class="stat-card-admin">
                    <div class="stat-icon"><i class="ti ti-user-check"></i></div>
                    <div class="label">Online Drivers</div>
                    <div class="value">{{ number_format($onlineDrivers ?? 0) }}</div>
                    <div class="change neutral">{{ number_format($idleDrivers ?? 0) }} idle</div>
                </div>
                <div class="stat-card-admin">
                    <div class="stat-icon"><i class="ti ti-clock"></i></div>
                    <div class="label">Avg Delivery Time</div>
                    <div class="value">{{ isset($avgDeliveryTime) ? round($avgDeliveryTime) : 0 }} min</div>
                    <div class="change positive"><i class="ti ti-arrow-down"></i> 2 min vs avg</div>
                </div>
                <div class="stat-card-admin">
                    <div class="stat-icon"><i class="ti ti-package"></i></div>
                    <div class="label">Total Deliveries</div>
                    <div class="value">{{ number_format($deliveredOrders ?? 0) }}</div>
                    <div class="change neutral">All time</div>
                </div>
            </div>

            <!-- EARNINGS SUMMARY -->
            <div class="earnings-grid">
                <div class="earnings-card" style="border-left-color:var(--primary);">
                    <div class="label">💰 Today's Revenue</div>
                    <div class="value total">{{ number_format($todayRevenue ?? 0, 0, ',', ' ') }} F</div>
                    <div class="sub-text">Total customer payments</div>
                </div>
                <div class="earnings-card" style="border-left-color:var(--success);">
                    <div class="label">👨‍✈️ Today's Driver Earnings</div>
                    <div class="value driver">{{ number_format($todayDriverEarnings ?? 0, 0, ',', ' ') }} F</div>
                    <div class="sub-text">To be paid to drivers</div>
                </div>
                <div class="earnings-card" style="border-left-color:#3B82F6;">
                    <div class="label">🏢 Today's Platform Earnings</div>
                    <div class="value platform">{{ number_format($todayPlatformEarnings ?? 0, 0, ',', ' ') }} F</div>
                    <div class="sub-text">Platform commission (50%)</div>
                </div>
            </div>

            <!-- MONTHLY SUMMARY -->
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">
                <div class="summary-item">
                    <div class="summary-label">📊 Monthly Revenue</div>
                    <div class="summary-value primary">{{ number_format($monthlyRevenue ?? 0, 0, ',', ' ') }} F</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">👨‍✈️ Monthly Driver Earnings</div>
                    <div class="summary-value green">{{ number_format($monthlyDriverEarnings ?? 0, 0, ',', ' ') }} F</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">🏢 Monthly Platform Earnings</div>
                    <div class="summary-value blue">{{ number_format($monthlyPlatformEarnings ?? 0, 0, ',', ' ') }} F</div>
                </div>
            </div>

            <!-- ============================================================
                 DRIVER EARNINGS TABLE - FIXED
                 ============================================================ -->
            <div style="background:#FFFFFF; border:0.5px solid var(--gray-200); border-radius:12px; padding:18px 20px; margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
                    <div>
                        <div style="font-size:15px; font-weight:600; color:var(--gray-900);">
                            👨‍✈️ Driver Earnings
                            <span style="font-size:12px; font-weight:400; color:var(--gray-500); margin-left:8px;">
                                This Month · {{ now()->format('M Y') }}
                            </span>
                        </div>
                        <div style="font-size:12px; color:var(--gray-400); margin-top:2px;">
                            Total: {{ number_format($totalDriverEarningsThisMonth ?? 0, 0, ',', ' ') }} F
                        </div>
                    </div>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <span style="font-size:11px; color:var(--gray-500); background:var(--gray-100); padding:4px 12px; border-radius:20px;">
                            <i class="ti ti-calendar"></i> {{ now()->format('M Y') }}
                        </span>
                        <span style="font-size:11px; color:var(--success); background:var(--success-bg); padding:4px 12px; border-radius:20px;">
                            <i class="ti ti-check"></i> {{ $driverEarningsThisMonth->where('status', 'paid')->count() ?? 0 }} paid
                        </span>
                        <span style="font-size:11px; color:#92400E; background:var(--warning-bg); padding:4px 12px; border-radius:20px;">
                            <i class="ti ti-clock"></i> {{ $driverEarningsThisMonth->where('status', 'pending')->count() ?? 0 }} pending
                        </span>
                    </div>
                </div>

                @if(isset($driverEarningsThisMonth) && $driverEarningsThisMonth->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="driver-earnings-table">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Deliveries</th>
                                <th>Distance</th>
                                <th>Weight</th>
                                <th style="text-align:right;">Total Earned</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($driverEarningsThisMonth as $earning)
                            @php
                                // ✅ FIXED: Check if earnings are paid
                                $isPaid = $earning->status === 'paid';
                                // ✅ Also check if driver was paid this month via withdrawal
                                $hasBeenPaidThisMonth = \App\Models\WithdrawalRequest::where('driver_id', $earning->driver_id)
                                    ->where('status', 'completed')
                                    ->whereMonth('processed_at', now()->month)
                                    ->whereYear('processed_at', now()->year)
                                    ->exists();
                                $isPaid = $isPaid || $hasBeenPaidThisMonth;
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div class="driver-avatar" style="width:32px; height:32px; font-size:11px;">
                                            {{ strtoupper(substr($earning->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; color:var(--gray-900); font-size:13px;">{{ $earning->driver->name ?? 'Unknown Driver' }}</div>
                                            <div style="font-size:11px; color:var(--gray-500);">ID: #{{ $earning->driver_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight:500;">{{ $earning->deliveries_count ?? 0 }}</td>
                                <td style="color:var(--gray-500);">{{ number_format($earning->total_distance ?? 0, 1) }} km</td>
                                <td style="color:var(--gray-500);">{{ number_format($earning->total_weight ?? 0, 1) }} kg</td>
                                <td class="amount" style="text-align:right; font-size:14px;">
                                    {{ number_format($earning->total_earned ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge-{{ $isPaid ? 'paid' : 'pending' }}">
                                        {{ $isPaid ? '✅ Paid' : '⏳ Pending' }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <button class="btn-pay-driver" 
                                            data-driver="{{ $earning->driver_id }}" 
                                            data-amount="{{ $earning->total_earned }}"
                                            data-name="{{ $earning->driver->name ?? 'Driver' }}"
                                            data-deliveries="{{ $earning->deliveries_count ?? 0 }}"
                                            data-month="{{ now()->format('F Y') }}"
                                            {{ $isPaid ? 'disabled' : '' }}>
                                        <i class="ti ti-credit-card"></i> 
                                        {{ $isPaid ? 'Paid' : 'Pay Now' }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align:center; padding:30px; color:var(--gray-400);">
                    <i class="ti ti-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                    No driver earnings this month
                </div>
                @endif
            </div>

            <!-- ============================================================
                 WITHDRAWAL MANAGEMENT
                 ============================================================ -->
            @php
                $pendingWithdrawals = \App\Models\WithdrawalRequest::with(['driver', 'driver.user'])
                    ->where('status', 'pending')
                    ->orderBy('requested_at', 'asc')
                    ->limit(5)
                    ->get();
                $pendingCount = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                $pendingAmount = \App\Models\WithdrawalRequest::where('status', 'pending')->sum('amount');
                // ✅ FIXED: Use processed_at for "Completed This Month"
                $processedMonth = \App\Models\WithdrawalRequest::where('status', 'completed')
                    ->whereMonth('processed_at', now()->month)
                    ->whereYear('processed_at', now()->year)
                    ->sum('net_amount');
            @endphp

            <div style="background:#FFFFFF; border:0.5px solid var(--gray-200); border-radius:12px; padding:18px 20px; margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
                    <div>
                        <div style="font-size:15px; font-weight:600; color:var(--gray-900);">
                            💸 Withdrawal Requests
                        </div>
                        <div style="font-size:12px; color:var(--gray-500); margin-top:2px;">
                            Review and process driver withdrawal requests
                        </div>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="{{ route('admin.withdrawals') }}" 
                           style="background:var(--primary); color:#FFFFFF; border:none; border-radius:8px; padding:8px 18px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                            <i class="ti ti-arrow-right"></i> View All
                        </a>
                    </div>
                </div>

                <!-- Withdrawal Stats -->
                <div class="withdrawal-stats">
                    <div style="background:var(--gray-50); border-radius:8px; padding:10px 14px; text-align:center; border:0.5px solid var(--gray-200);">
                        <div style="font-size:10px; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">
                            <i class="ti ti-clock"></i> Pending Requests
                        </div>
                        <div style="font-size:20px; font-weight:700; color:var(--warning); margin-top:2px;">
                            {{ $pendingCount ?? 0 }}
                        </div>
                    </div>
                    <div style="background:var(--gray-50); border-radius:8px; padding:10px 14px; text-align:center; border:0.5px solid var(--gray-200);">
                        <div style="font-size:10px; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">
                            <i class="ti ti-credit-card"></i> Total Pending Amount
                        </div>
                        <div style="font-size:20px; font-weight:700; color:var(--warning); margin-top:2px;">
                            {{ number_format($pendingAmount ?? 0, 0, ',', ' ') }} F
                        </div>
                    </div>
                    <div style="background:var(--gray-50); border-radius:8px; padding:10px 14px; text-align:center; border:0.5px solid var(--gray-200);">
                        <div style="font-size:10px; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">
                            <i class="ti ti-check"></i> Processed This Month
                        </div>
                        <div style="font-size:20px; font-weight:700; color:var(--success); margin-top:2px;">
                            {{ number_format($processedMonth ?? 0, 0, ',', ' ') }} F
                        </div>
                    </div>
                </div>

                <!-- Pending Requests Table -->
                @if(isset($pendingWithdrawals) && $pendingWithdrawals->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="withdrawal-table-mini">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th style="text-align:right;">Amount</th>
                                <th style="text-align:center;">Method</th>
                                <th>Requested</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingWithdrawals as $withdrawal)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:var(--primary-bg); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:var(--primary);">
                                            {{ strtoupper(substr($withdrawal->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; color:var(--gray-900); font-size:13px;">{{ $withdrawal->driver->name ?? 'Unknown' }}</div>
                                            <div style="font-size:10px; color:var(--gray-500);">{{ $withdrawal->driver->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:right; font-weight:600; color:var(--gray-900);">
                                    {{ $withdrawal->formatted_amount }}
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-size:10px; padding:2px 8px; border-radius:4px; background:var(--gray-100); font-weight:500;">
                                        {{ $withdrawal->payment_method_label }}
                                    </span>
                                </td>
                                <td style="font-size:11px; color:var(--gray-500);">
                                    {{ $withdrawal->requested_at ? $withdrawal->requested_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; gap:4px; justify-content:center;">
                                        <button class="withdrawal-action-btn approve" 
                                                onclick="approveWithdrawal({{ $withdrawal->id }}, '{{ $withdrawal->driver->name }}', '{{ $withdrawal->formatted_amount }}')">
                                            <i class="ti ti-check"></i> Approve
                                        </button>
                                        <button class="withdrawal-action-btn reject" 
                                                onclick="rejectWithdrawal({{ $withdrawal->id }}, '{{ $withdrawal->driver->name }}', '{{ $withdrawal->formatted_amount }}')">
                                            <i class="ti ti-x"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align:center; padding:20px; color:var(--gray-400);">
                    <i class="ti ti-check-circle" style="font-size:28px; display:block; margin-bottom:6px; color:var(--success);"></i>
                    <span style="font-size:13px;">No pending withdrawal requests</span>
                </div>
                @endif
            </div>

            <!-- ============================================================
                 QUICK ACTIONS
                 ============================================================ -->
            <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                <a href="{{ route('admin.drivers') }}" class="quick-action-btn">
                    <i class="ti ti-truck" style="color:var(--primary-light);"></i>
                    Drivers
                </a>
                <a href="{{ route('admin.customers') }}" class="quick-action-btn">
                    <i class="fa-solid fa-users" style="color:#3B82F6;"></i>
                    Customers
                </a>
                <a href="{{ route('admin.orders') }}" class="quick-action-btn">
                    <i class="ti ti-package" style="color:var(--warning);"></i>
                    Orders
                </a>
                <a href="{{ route('admin.users') }}" class="quick-action-btn">
                    <i class="ti ti-user-check" style="color:#8B5CF6;"></i>
                    Approvals
                </a>
            </div>

            <!-- ============================================================
                 DISPATCH GRID
                 ============================================================ -->
            <div class="dispatch-grid">

                <!-- LEFT COLUMN: ORDER QUEUE -->
                <div>

                    <!-- PENDING ORDERS -->
                    <div class="card-dispatch" style="margin-bottom:18px;">
                        <div class="card-title">
                            <span>📦 Order Queue <span class="badge-count pending">{{ $pendingOrdersCount ?? 0 }} Pending</span></span>
                            <span style="font-size:12px; color:var(--gray-400);">
                                <i class="ti ti-search"></i> {{ $pendingOrdersCount ?? 0 }} waiting
                            </span>
                        </div>

                        @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                            @foreach($pendingOrders as $order)
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-id">{{ $order->order_number }}</span>
                                    <span class="order-meta">
                                        <i class="ti ti-user"></i> {{ $order->customer->name ?? 'Customer' }}
                                    </span>
                                </div>
                                <div class="order-details">
                                    <span><span class="highlight">Pickup:</span> {{ Str::limit($order->pickup_address, 25) }}</span>
                                    <span><span class="highlight">Drop:</span> {{ Str::limit($order->delivery_address, 25) }}</span>
                                    @if($order->distance_km)
                                        <span><i class="ti ti-route"></i> {{ number_format($order->distance_km, 1) }} km</span>
                                    @endif
                                </div>
                                <div style="display:flex; justify-content:flex-end; margin-top:6px;">
                                    <a href="{{ route('admin.orders.assign-driver', $order->id) }}" class="assign-btn">
                                        <i class="ti ti-user-plus"></i> Assign
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align:center; padding:20px; color:var(--gray-400); font-size:13px;">
                                <i class="ti ti-inbox" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                                No pending orders
                            </div>
                        @endif
                    </div>

                    <!-- ACTIVE ORDERS -->
                    <div class="card-dispatch">
                        <div class="card-title">
                            <span>🔄 Active Orders <span class="badge-count active">{{ $activeOrdersCount ?? 0 }}</span></span>
                        </div>

                        @if(isset($activeOrders) && $activeOrders->count() > 0)
                            @foreach($activeOrders as $order)
                            <div class="order-item" style="border-left-color:var(--primary-light);">
                                <div class="order-header">
                                    <span class="order-id">{{ $order->order_number }}</span>
                                    <span class="order-meta">
                                        <i class="ti ti-user"></i> {{ $order->customer->name ?? 'Customer' }}
                                    </span>
                                    <span class="order-meta" style="color:var(--primary-light);">
                                        <i class="ti ti-clock"></i> 
                                        @if($order->estimated_delivery)
                                            {{ $order->estimated_delivery->diffForHumans() }}
                                        @else
                                            In progress
                                        @endif
                                    </span>
                                </div>
                                <div class="order-details">
                                    <span><span class="highlight">Driver:</span> {{ optional($order->driver)->name ?? 'Unassigned' }}</span>
                                    <span><span class="highlight">Status:</span> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align:center; padding:20px; color:var(--gray-400); font-size:13px;">
                                <i class="ti ti-truck" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                                No active orders
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: DRIVERS STATUS -->
                <div>

                    <!-- DRIVERS STATUS -->
                    <div class="card-dispatch">
                        <div class="card-title">
                            <span>👨‍✈️ Drivers <span class="badge-count">{{ $onlineDrivers ?? 0 }} available</span></span>
                            <span style="font-size:12px; color:var(--gray-400);">
                                {{ $idleDrivers ?? 0 }} on route · {{ $pendingDrivers ?? 0 }} awaiting
                            </span>
                        </div>

                        @if(isset($availableDrivers) && $availableDrivers->count() > 0)
                            @foreach($availableDrivers as $driver)
                            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:0.5px solid var(--gray-100);">
                                <div class="driver-avatar">{{ strtoupper(substr($driver->name ?? 'D', 0, 2)) }}</div>
                                <div style="flex:1;">
                                    <div style="font-size:13px; font-weight:500; color:var(--gray-900);">{{ $driver->name ?? 'Driver' }}</div>
                                    <div style="font-size:11px; color:var(--gray-500);">
                                        <span class="status-dot available"></span> Available
                                        @if($driver->vehicle)
                                            · {{ $driver->vehicle->plate_number }}
                                        @endif
                                    </div>
                                </div>
                                <span style="font-size:11px; color:var(--gray-400); background:var(--gray-100); padding:2px 10px; border-radius:12px;">
                                    @if($driver->vehicle)
                                        {{ $driver->vehicle->model ?? 'Vehicle' }}
                                    @else
                                        No vehicle
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align:center; padding:20px; color:var(--gray-400); font-size:13px;">
                                <i class="ti ti-users" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                                No drivers available
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        <!-- PAY DRIVER MODAL -->
        <div id="payDriverModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:480px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <span style="font-size:18px; font-weight:600; color:var(--gray-900);">💰 Pay Driver</span>
                    <button onclick="closePayModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:var(--gray-400);">✕</button>
                </div>
                <div id="payDriverContent">
                    <!-- Dynamic content loaded via JavaScript -->
                </div>
                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button onclick="processPayment()" 
                            style="flex:1; background:var(--success); color:#FFFFFF; border:none; border-radius:8px; padding:12px; font-size:13px; font-weight:600; cursor:pointer;">
                        <i class="ti ti-check"></i> Confirm Payment
                    </button>
                    <button onclick="closePayModal()" 
                            style="flex:1; background:var(--gray-100); color:var(--gray-500); border:none; border-radius:8px; padding:12px; font-size:13px; font-weight:500; cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- WITHDRAWAL APPROVAL MODAL -->
        <div id="withdrawalModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:440px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <span style="font-size:18px; font-weight:600; color:var(--gray-900);" id="withdrawalModalTitle">Process Withdrawal</span>
                    <button onclick="closeWithdrawalModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:var(--gray-400);">✕</button>
                </div>
                <div id="withdrawalModalContent">
                    <!-- Dynamic content -->
                </div>
                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button id="withdrawalConfirmBtn" 
                            style="flex:1; background:var(--success); color:#FFFFFF; border:none; border-radius:8px; padding:12px; font-size:13px; font-weight:600; cursor:pointer;">
                        <i class="ti ti-check"></i> Confirm
                    </button>
                    <button onclick="closeWithdrawalModal()" 
                            style="flex:1; background:var(--gray-100); color:var(--gray-500); border:none; border-radius:8px; padding:12px; font-size:13px; font-weight:500; cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <script>
            // ============================================================
            // PAY DRIVER - FULLY OPERATIONAL
            // ============================================================
            
            let selectedPayment = {
                driverId: null,
                amount: 0,
                driverName: '',
                deliveries: 0,
                month: ''
            };

            document.querySelectorAll('.btn-pay-driver:not(:disabled)').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var driverId = this.getAttribute('data-driver');
                    var amount = this.getAttribute('data-amount');
                    var driverName = this.getAttribute('data-name');
                    var deliveries = this.getAttribute('data-deliveries');
                    var month = this.getAttribute('data-month');
                    
                    selectedPayment.driverId = driverId;
                    selectedPayment.amount = amount;
                    selectedPayment.driverName = driverName;
                    selectedPayment.deliveries = deliveries;
                    selectedPayment.month = month;
                    
                    document.getElementById('payDriverContent').innerHTML = `
                        <div style="padding:8px 0;">
                            <div style="margin-bottom:16px; display:flex; align-items:center; gap:14px;">
                                <div style="width:56px; height:56px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; color:#0D9488; flex-shrink:0;">
                                    ${driverName ? driverName.substring(0, 2).toUpperCase() : 'D'}
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:600; color:var(--gray-900);">${driverName}</div>
                                    <div style="font-size:13px; color:var(--gray-500);">Driver ID: #${driverId}</div>
                                    <div style="font-size:12px; color:var(--gray-400);">📦 ${deliveries} deliveries this month</div>
                                </div>
                            </div>
                            <div style="background:var(--gray-50); border-radius:10px; padding:14px 16px; margin-bottom:10px;">
                                <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                    <span style="color:var(--gray-500);">Month:</span>
                                    <span style="font-weight:500; color:var(--gray-900);">${month}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                    <span style="color:var(--gray-500);">Deliveries:</span>
                                    <span style="font-weight:500; color:var(--gray-900);">${deliveries}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; padding:4px 0; border-top:1px solid var(--gray-200); margin-top:4px; padding-top:8px;">
                                    <span style="color:var(--gray-700); font-weight:600;">Total Amount:</span>
                                    <span style="font-weight:700; color:var(--success); font-size:20px;">${Number(amount).toLocaleString()} F</span>
                                </div>
                            </div>
                            <div style="background:var(--warning-bg); padding:10px 14px; border-radius:8px;">
                                <div style="font-size:12px; color:#92400E; display:flex; align-items:flex-start; gap:6px;">
                                    <i class="ti ti-info-circle" style="margin-top:1px;"></i>
                                    <span>This will mark the driver's earnings as <strong>PAID</strong> for this month.</span>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('payDriverModal').style.display = 'block';
                });
            });

            function processPayment() {
                if (!selectedPayment.driverId) return;
                
                if (confirm('Confirm payment of ' + Number(selectedPayment.amount).toLocaleString() + ' F to ' + selectedPayment.driverName + '?')) {
                    var token = document.querySelector('meta[name="csrf-token"]');
                    var csrfToken = token ? token.getAttribute('content') : '';
                    
                    fetch('/admin/drivers/' + selectedPayment.driverId + '/pay', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            amount: selectedPayment.amount,
                            month: selectedPayment.month,
                            driver_name: selectedPayment.driverName
                        })
                    })
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.success) {
                            alert('✅ Payment of ' + Number(selectedPayment.amount).toLocaleString() + ' F to ' + selectedPayment.driverName + ' recorded successfully!');
                            closePayModal();
                            location.reload();
                        } else {
                            alert('❌ Payment failed: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('❌ Payment failed. Please try again.');
                    });
                }
            }

            function closePayModal() {
                document.getElementById('payDriverModal').style.display = 'none';
            }

            // ============================================================
            // WITHDRAWAL APPROVAL - FULLY OPERATIONAL
            // ============================================================
            
            let selectedWithdrawal = {
                id: null,
                action: null,
                driverName: '',
                amount: ''
            };

            function approveWithdrawal(id, driverName, amount) {
                selectedWithdrawal.id = id;
                selectedWithdrawal.action = 'approve';
                selectedWithdrawal.driverName = driverName;
                selectedWithdrawal.amount = amount;
                
                document.getElementById('withdrawalModalTitle').textContent = '✅ Approve Withdrawal';
                document.getElementById('withdrawalModalContent').innerHTML = `
                    <div style="padding:8px 0;">
                        <div style="background:var(--gray-50); border-radius:10px; padding:14px 16px;">
                            <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                <span style="color:var(--gray-500);">Driver:</span>
                                <span style="font-weight:500; color:var(--gray-900);">${driverName}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                <span style="color:var(--gray-500);">Amount:</span>
                                <span style="font-weight:700; color:var(--success); font-size:18px;">${amount}</span>
                            </div>
                        </div>
                        <div style="background:var(--success-bg); padding:10px 14px; border-radius:8px; margin-top:12px;">
                            <div style="font-size:12px; color:#065F46; display:flex; align-items:flex-start; gap:6px;">
                                <i class="ti ti-check-circle" style="margin-top:1px;"></i>
                                <span>This will approve the withdrawal request and the driver will receive the funds.</span>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('withdrawalConfirmBtn').onclick = function() {
                    processWithdrawal('approve');
                };
                
                document.getElementById('withdrawalModal').style.display = 'block';
            }

            function rejectWithdrawal(id, driverName, amount) {
                selectedWithdrawal.id = id;
                selectedWithdrawal.action = 'reject';
                selectedWithdrawal.driverName = driverName;
                selectedWithdrawal.amount = amount;
                
                document.getElementById('withdrawalModalTitle').textContent = '❌ Reject Withdrawal';
                document.getElementById('withdrawalModalContent').innerHTML = `
                    <div style="padding:8px 0;">
                        <div style="background:var(--gray-50); border-radius:10px; padding:14px 16px;">
                            <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                <span style="color:var(--gray-500);">Driver:</span>
                                <span style="font-weight:500; color:var(--gray-900);">${driverName}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; padding:4px 0;">
                                <span style="color:var(--gray-500);">Amount:</span>
                                <span style="font-weight:700; color:var(--danger); font-size:18px;">${amount}</span>
                            </div>
                        </div>
                        <div style="background:var(--danger-bg); padding:10px 14px; border-radius:8px; margin-top:12px;">
                            <div style="font-size:12px; color:#991B1B; display:flex; align-items:flex-start; gap:6px;">
                                <i class="ti ti-alert-circle" style="margin-top:1px;"></i>
                                <span>This will reject the withdrawal request. The driver will be notified.</span>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('withdrawalConfirmBtn').onclick = function() {
                    processWithdrawal('reject');
                };
                
                document.getElementById('withdrawalModal').style.display = 'block';
            }

            function processWithdrawal(action) {
                if (!selectedWithdrawal.id) return;
                
                var token = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = token ? token.getAttribute('content') : '';
                
                var actionText = action === 'approve' ? 'approved' : 'rejected';
                var confirmMsg = action === 'approve' 
                    ? 'Approve withdrawal of ' + selectedWithdrawal.amount + ' for ' + selectedWithdrawal.driverName + '?'
                    : 'Reject withdrawal of ' + selectedWithdrawal.amount + ' for ' + selectedWithdrawal.driverName + '?';
                
                if (confirm(confirmMsg)) {
                    fetch('/admin/withdrawals/' + selectedWithdrawal.id + '/' + action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.success) {
                            alert('✅ Withdrawal request ' + actionText + ' successfully!');
                            closeWithdrawalModal();
                            location.reload();
                        } else {
                            alert('❌ Failed to ' + action + ' withdrawal: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        alert('❌ Operation failed. Please try again.');
                    });
                }
            }

            function closeWithdrawalModal() {
                document.getElementById('withdrawalModal').style.display = 'none';
            }

            // ============================================================
            // KEYBOARD SHORTCUTS
            // ============================================================
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePayModal();
                    closeWithdrawalModal();
                }
            });

            document.addEventListener('click', function(e) {
                var payModal = document.getElementById('payDriverModal');
                if (e.target === payModal) {
                    closePayModal();
                }
                var withdrawalModal = document.getElementById('withdrawalModal');
                if (e.target === withdrawalModal) {
                    closeWithdrawalModal();
                }
            });
        </script>
    </div>
@endsection