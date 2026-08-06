@extends('layouts.dashboard')

@section('title', 'Driver Dashboard - Tracklane')

@section('dashboard-content')

<style>
    /* ─── STATUS TOGGLE ─── */
    .status-toggle-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 7px 12px;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .status-dot.online { background: #10B981; }
    .status-dot.offline { background: #94A3B8; }
    .status-text {
        font-size: 12px;
        color: #0F172A;
    }
    .status-text.offline { color: #94A3B8; }
    .toggle-btn {
        width: 34px;
        height: 18px;
        border-radius: 10px;
        border: none;
        position: relative;
        cursor: pointer;
        padding: 0;
        margin: 0;
        transition: background 0.3s ease;
    }
    .toggle-btn.online { background: #0D9488; }
    .toggle-btn.offline { background: #CBD5E1; }
    .toggle-slider {
        position: absolute;
        top: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #FFFFFF;
        transition: all 0.3s ease;
    }
    .toggle-slider.online { right: 2px; }
    .toggle-slider.offline { left: 2px; }

    /* ─── STAT CARDS ─── */
    .stat-card-driver {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 14px 16px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-driver:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .stat-card-driver .stat-label {
        font-size: 12px;
        color: #64748B;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .stat-card-driver .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #0F172A;
    }
    .stat-card-driver .stat-change {
        font-size: 11px;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .stat-change.positive { color: #10B981; }
    .stat-change.negative { color: #EF4444; }
    .stat-change.neutral { color: #94A3B8; }

    /* ─── EARNINGS BREAKDOWN ─── */
    .earnings-card {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 18px 20px;
        margin-top: 20px;
    }
    .earnings-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .earnings-card .card-header .title {
        font-size: 15px;
        font-weight: 600;
        color: #0F172A;
    }
    .earnings-card .card-header .badge {
        font-size: 11px;
        color: #64748B;
        background: #F1F5F9;
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 500;
    }
    .earnings-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 768px) {
        .earnings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .balance-grid {
            grid-template-columns: 1fr !important;
        }
        .delivery-details-grid {
            grid-template-columns: 1fr !important;
        }
    }
    .earnings-item {
        background: #F8FAFC;
        border-radius: 8px;
        padding: 12px 14px;
        border-left: 3px solid #14B8A6;
    }
    .earnings-item .label {
        font-size: 11px;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
    }
    .earnings-item .value {
        font-size: 18px;
        font-weight: 700;
        color: #0F172A;
        margin-top: 2px;
    }
    .earnings-item .value.green { color: #10B981; }
    .earnings-item .value.blue { color: #3B82F6; }
    .earnings-item .value.amber { color: #F59E0B; }
    .earnings-item .value.purple { color: #8B5CF6; }

    /* ─── SIMPLIFIED: Driver sees only their earnings ─── */
    .commission-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: #F0FDF4;
        border-radius: 8px;
        margin-top: 12px;
        border: 0.5px solid #D1FAE5;
        flex-wrap: wrap;
    }
    .commission-info .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10B981;
        flex-shrink: 0;
    }
    .commission-info .commission-text {
        font-size: 12px;
        color: #0F172A;
    }
    .commission-info .commission-text strong {
        color: #065F46;
    }

    /* ─── BALANCE GRID ─── */
    .balance-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    /* ─── DELIVERY CARD ─── */
    .delivery-card {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 20px;
        margin-top: 20px;
        transition: box-shadow 0.2s ease;
    }
    .delivery-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .delivery-card .delivery-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .delivery-card .delivery-status {
        font-size: 11px;
        background: #FEF3C7;
        color: #92400E;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 500;
    }
    .delivery-card .delivery-status.in-transit { background: #FEF3C7; color: #92400E; }
    .delivery-card .delivery-status.pending { background: #F1F5F9; color: #475569; }
    .delivery-card .delivery-status.delivered { background: #D1FAE5; color: #065F46; }

    .delivery-address-item {
        font-size: 13px;
        color: #0F172A;
        padding: 4px 0;
    }
    .delivery-address-item .label {
        font-size: 10px;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .delivery-details-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
    }

    /* ─── BUTTONS ─── */
    .btn-outline {
        background: transparent;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 500;
        color: #0F172A;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-outline:hover {
        background: #F8FAFC;
        border-color: #14B8A6;
    }
    .btn-primary-driver {
        background: #0D9488;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-primary-driver:hover {
        background: #0F766E;
        transform: translateY(-1px);
    }

    /* ─── TABLE ─── */
    .table-driver {
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }
    .table-driver thead th {
        padding: 10px 8px;
        text-align: left;
        font-weight: 600;
        color: #64748B;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #E2E8F0;
    }
    .table-driver tbody td {
        padding: 10px 8px;
        border-bottom: 0.5px solid #F1F5F9;
        color: #0F172A;
    }
    .table-driver tbody tr:last-child td {
        border-bottom: none;
    }
    .table-driver tbody tr:hover {
        background: #FAFBFC;
    }

    /* ─── MINI MAP ─── */
    .mini-map {
        position: relative;
        height: 170px;
        background: #EEF2F6;
        border-radius: 8px;
        overflow: hidden;
    }
    .mini-map .map-route {
        position: absolute;
        top: 30px;
        left: 40px;
        width: 175px;
        height: 110px;
        border: 2px dashed #14B8A6;
        border-radius: 60% 40% 50% 50%;
        border-top: none;
        border-left: none;
    }
    .mini-map .map-pin {
        position: absolute;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #FFFFFF;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .mini-map .map-pin.pickup { background: #D85A30; top: 60px; left: 26px; }
    .mini-map .map-pin.dropoff { background: #2563EB; top: 130px; left: 194px; }
    .mini-map .map-pin.truck { 
        background: #10B981; 
        top: 88px; 
        left: 110px; 
        width: 22px; 
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.18);
    }
    .mini-map .map-label {
        position: absolute;
        font-size: 10px;
        color: #475569;
        font-weight: 500;
    }
    .mini-map .map-label.pickup-label { top: 42px; left: 10px; color: #D85A30; }
    .mini-map .map-label.dropoff-label { top: 142px; left: 178px; color: #2563EB; }
    .mini-map .map-label.truck-label { top: 72px; left: 100px; color: #10B981; }

    /* ─── GPS STATUS ─── */
    .gps-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .gps-status.active {
        background: #D1FAE5;
        color: #065F46;
    }
    .gps-status.inactive {
        background: #FEF3C7;
        color: #92400E;
    }

    /* ─── EMPTY STATE ─── */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94A3B8;
    }
    .empty-state .empty-icon {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }
    .empty-state .empty-title {
        font-size: 16px;
        font-weight: 500;
        color: #0F172A;
        margin-bottom: 4px;
    }
    .empty-state .empty-subtitle {
        font-size: 13px;
    }

    /* ─── SECTION TITLE ─── */
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #0F172A;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title .section-badge {
        font-size: 11px;
        font-weight: 400;
        color: #64748B;
        background: #F1F5F9;
        padding: 2px 10px;
        border-radius: 12px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 8px;
    }

    /* ─── GPS PULSE ANIMATION ─── */
    @keyframes gps-pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .gps-pulse {
        animation: gps-pulse 2s infinite;
    }

    /* ─── STAT VALUE UPDATE ANIMATION ─── */
    @keyframes stat-update {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); color: #0D9488; }
        100% { transform: scale(1); }
    }
    .stat-update {
        animation: stat-update 0.5s ease;
    }
</style>

<!-- ============================================================
     SECTION 1: DRIVER INFO & STATUS
     ============================================================ -->
@php
    $isOnline = ($driverProfile && $driverProfile->is_available) ? true : false;
    $statusClass = $isOnline ? 'online' : 'offline';
    $statusText = $isOnline ? 'Online' : 'Offline';
    $gpsActive = $isOnline && isset($activeOrder) && $activeOrder;
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <div style="font-size:20px; font-weight:700; color:#0F172A; display:flex; align-items:center; gap:10px;">
            {{ Auth::user()->name }}
            <span style="font-size:13px; font-weight:400; color:#64748B; background:#F1F5F9; padding:2px 12px; border-radius:12px;">
                Driver
            </span>
        </div>
        <div style="font-size:13px; color:#64748B; margin-top:2px;">
            @if($driverProfile && $driverProfile->vehicle)
                {{ $driverProfile->vehicle->model ?? 'Vehicle' }} · {{ $driverProfile->vehicle->plate_number ?? 'N/A' }}
            @else
                No vehicle assigned
            @endif
        </div>
    </div>
    
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        @if($gpsActive)
            <span class="gps-status active gps-pulse">
                <i class="ti ti-map-pin"></i> GPS Active
            </span>
            <span style="font-size:10px; color:#64748B;" id="lastUpdate">
                📍 Updating...
            </span>
        @endif
        
<div class="status-toggle-container">
    <div class="status-dot {{ $statusClass }}"></div>
    <span class="status-text {{ $statusClass }}">{{ $statusText }}</span>
    
  
    <form method="POST" action="{{ route('driver.toggle-status') }}" style="display:inline; margin:0;">
        @csrf
        <button type="submit" class="toggle-btn {{ $statusClass }}">
            <div class="toggle-slider {{ $statusClass }}"></div>
        </button>
    </form>
</div>
    </div>
</div>

<!-- ============================================================
     SECTION 2: STATS CARDS - Driver Only Sees Their Earnings
     ============================================================ -->
<div class="stats-grid">
    <div class="stat-card-driver">
        <div class="stat-label">Today's Earnings</div>
        <div class="stat-value" id="todayEarnings">{{ number_format($todayEarnings ?? 0, 0, ',', ' ') }} F</div>
        <div class="stat-change positive">
            <i class="ti ti-arrow-up"></i> Today's total
        </div>
    </div>
    <div class="stat-card-driver">
        <div class="stat-label">Completed Today</div>
        <div class="stat-value" id="completedToday">{{ $completedToday ?? 0 }}</div>
        <div class="stat-change positive">
            <i class="ti ti-arrow-up"></i> Deliveries today
        </div>
    </div>
    <div class="stat-card-driver">
        <div class="stat-label">Total Deliveries</div>
        <div class="stat-value" id="totalDeliveries">{{ $totalCompleted ?? 0 }}</div>
        <div class="stat-change neutral">
            <i class="ti ti-minus"></i> Lifetime total
        </div>
    </div>
    <div class="stat-card-driver">
        <div class="stat-label">Total Earnings</div>
        <div class="stat-value" id="totalEarnings">{{ number_format($totalEarnings ?? 0, 0, ',', ' ') }} F</div>
        <div class="stat-change neutral">
            <i class="ti ti-minus"></i> All time
        </div>
    </div>
</div>

<!-- ============================================================
     SECTION 3: SIMPLIFIED EARNINGS BREAKDOWN
     ============================================================ -->
@php
    $avgPerDelivery = ($totalCompleted ?? 0) > 0 ? ($totalEarnings ?? 0) / ($totalCompleted ?? 0) : 0;
@endphp

<div class="earnings-card">
    <div class="card-header">
        <span class="title">💰 My Earnings</span>
        <span class="badge">50% Commission</span>
    </div>
    <div class="earnings-grid">
        <div class="earnings-item" style="border-left-color:#10B981;">
            <div class="label">Today's Earnings</div>
            <div class="value green">{{ number_format($todayEarnings ?? 0, 0, ',', ' ') }} F</div>
        </div>
        <div class="earnings-item" style="border-left-color:#3B82F6;">
            <div class="label">Total Earnings</div>
            <div class="value blue">{{ number_format($totalEarnings ?? 0, 0, ',', ' ') }} F</div>
        </div>
        <div class="earnings-item" style="border-left-color:#F59E0B;">
            <div class="label">Total Deliveries</div>
            <div class="value amber">{{ $totalCompleted ?? 0 }}</div>
        </div>
        <div class="earnings-item" style="border-left-color:#8B5CF6;">
            <div class="label">Avg Per Delivery</div>
            <div class="value purple">{{ number_format($avgPerDelivery, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <!-- ✅ SIMPLIFIED: Driver only sees their 50% commission -->
    <div class="commission-info">
        <span class="dot"></span>
        <span class="commission-text">
            You earn <strong>50% of every delivery</strong>. Your earnings are automatically added to your balance when you complete a delivery.
        </span>
    </div>
</div>

<!-- ============================================================
     SECTION 4: WITHDRAWAL & BALANCE
     ============================================================ -->
<div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:18px 20px; margin-top:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
        <div>
            <div style="font-size:15px; font-weight:600; color:#0F172A;">
                💰 My Balance
            </div>
            <div style="font-size:12px; color:#64748B; margin-top:2px;">
                Manage your earnings and request withdrawals
            </div>
        </div>
        <a href="{{ route('driver.withdraw') }}" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 18px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="ti ti-credit-card"></i> Withdraw
        </a>
    </div>

    <div class="balance-grid">
        <div style="background:#F8FAFC; border-radius:8px; padding:12px 14px; text-align:center; border:0.5px solid #E2E8F0;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">Available Balance</div>
            <div style="font-size:22px; font-weight:700; color:#10B981; margin-top:2px;">
                {{ isset($driverProfile) ? number_format($driverProfile->available_balance ?? 0, 0, ',', ' ') : '0' }} F
            </div>
        </div>
        <div style="background:#F8FAFC; border-radius:8px; padding:12px 14px; text-align:center; border:0.5px solid #E2E8F0;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">Total Earned</div>
            <div style="font-size:22px; font-weight:700; color:#0F172A; margin-top:2px;">
                {{ isset($driverProfile) ? number_format($driverProfile->total_earned ?? 0, 0, ',', ' ') : '0' }} F
            </div>
        </div>
        <div style="background:#F8FAFC; border-radius:8px; padding:12px 14px; text-align:center; border:0.5px solid #E2E8F0;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase; letter-spacing:0.04em; font-weight:500;">Total Withdrawn</div>
            <div style="font-size:22px; font-weight:700; color:#F59E0B; margin-top:2px;">
                {{ isset($driverProfile) ? number_format($driverProfile->total_withdrawn ?? 0, 0, ',', ' ') : '0' }} F
            </div>
        </div>
    </div>

    @if(isset($driverProfile) && $driverProfile->available_balance > 0)
    <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('driver.withdraw') }}" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:10px 24px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="ti ti-credit-card"></i> Request Withdrawal
        </a>
        <a href="{{ route('driver.withdrawals') }}" 
           style="background:#FFFFFF; color:#0F172A; border:0.5px solid #E2E8F0; border-radius:8px; padding:10px 24px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="ti ti-history"></i> View History
        </a>
    </div>
    @else
    <div style="margin-top:12px;">
        <a href="{{ route('driver.withdrawals') }}" 
           style="color:#14B8A6; text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
            <i class="ti ti-history"></i> View Withdrawal History
        </a>
    </div>
    @endif
</div>

<!-- ============================================================
     SECTION 5: CURRENT DELIVERY
     ============================================================ -->
@if($activeOrder)
<div class="delivery-card">
    <div class="delivery-header">
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <span style="font-size:16px; font-weight:600; color:#0F172A;">Current Delivery</span>
            <span class="delivery-status in-transit">
                <i class="ti ti-truck" style="font-size:12px;"></i> {{ ucfirst(str_replace('_', ' ', $activeOrder->status)) }}
            </span>
        </div>
        <span style="font-size:12px; color:#64748B; font-weight:500;">#{{ $activeOrder->order_number }}</span>
    </div>

    <div class="delivery-details-grid">
        <!-- Mini Map -->
        <div class="mini-map">
            <div class="map-route"></div>
            <div class="map-pin pickup"></div>
            <div class="map-label pickup-label">📍 Pickup</div>
            <div class="map-pin dropoff"></div>
            <div class="map-label dropoff-label">🏁 Drop-off</div>
            <div class="map-pin truck">
                <i class="ti ti-truck" style="font-size:12px; color:#FFFFFF;"></i>
            </div>
            <div class="map-label truck-label" style="top:72px; left:100px; color:#10B981;">🚚 Driver</div>
        </div>

        <!-- Details -->
        <div style="display:flex; flex-direction:column; gap:8px; justify-content:space-between;">
            <div>
                <div class="delivery-address-item">
                    <span class="label"><i class="ti ti-map-pin"></i> Pickup</span>
                    {{ Str::limit($activeOrder->pickup_address, 35) }}
                </div>
            </div>
            <div>
                <div class="delivery-address-item">
                    <span class="label"><i class="ti ti-location"></i> Drop-off</span>
                    <strong>{{ Str::limit($activeOrder->delivery_address, 35) }}</strong>
                </div>
            </div>
            <div>
                <div class="delivery-address-item">
                    <span class="label"><i class="ti ti-user"></i> Customer</span>
                    {{ $activeOrder->customer->name ?? 'N/A' }}
                    @if($activeOrder->customer && $activeOrder->customer->phone)
                        · {{ $activeOrder->customer->phone }}
                    @endif
                </div>
            </div>
            
            <div style="display:flex; gap:10px; margin-top:6px; flex-wrap:wrap;">
                <a href="{{ route('tracking', $activeOrder->id) }}" 
                   class="btn-outline" style="flex:1; min-width:100px;">
                    <i class="ti ti-map"></i> Track Live
                </a>
                
                @if($activeOrder->status !== 'delivered')
                <form method="POST" action="{{ route('driver.update-status', $activeOrder->id) }}" style="flex:1; min-width:100px;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="btn-primary-driver" style="width:100%;">
                        <i class="ti ti-check"></i> Mark Delivered
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="delivery-card">
    <div class="empty-state">
        <i class="ti ti-truck empty-icon"></i>
        <div class="empty-title">No Active Delivery</div>
        <div class="empty-subtitle">You don't have any active deliveries right now.</div>
        <div style="margin-top:8px; font-size:13px; color:#64748B;">💡 Go online to receive orders</div>
    </div>
</div>
@endif

<!-- ============================================================
     SECTION 6: COMPLETED DELIVERIES - Driver Only Sees Their Earnings
     ============================================================ -->
<div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px; margin-top:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:8px;">
        <div class="section-title">
            📦 Completed Deliveries
            <span class="section-badge">{{ $completedToday ?? 0 }} today</span>
        </div>
    </div>
    
    @if(isset($recentCompleted) && $recentCompleted->count() > 0)
    <div style="overflow-x:auto;">
        <table class="table-driver">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Distance</th>
                    <th>Weight</th>
                    <th>Completed</th>
                    <th style="text-align:right;">My Earnings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentCompleted as $order)
                <tr>
                    <td style="font-weight:500; color:#0F172A;">#{{ $order->order_number }}</td>
                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                    <td style="color:#64748B;">{{ $order->distance_km ?? 0 }} km</td>
                    <td style="color:#64748B;">{{ $order->weight_kg ?? 0 }} kg</td>
                    <td style="color:#64748B;">
                        {{ optional($order->updated_at)->format('d M Y, h:i A') ?? 'N/A' }}
                    </td>
                    <td style="text-align:right; font-weight:600; color:#10B981;">
                        {{ number_format($order->driver_earning ?? 0, 0, ',', ' ') }} F
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state" style="padding:30px 20px;">
        <i class="ti ti-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
        <span style="font-size:13px;">No completed deliveries yet. Start delivering to see your earnings!</span>
    </div>
    @endif
</div>

<!-- ============================================================
     SECTION 7: GPS TRACKING SCRIPT
     ============================================================ -->
@if($isOnline && isset($activeOrder) && $activeOrder)
<script>
    (function() {
        'use strict';
        
        var watchId = null;
        var updateCount = 0;
        var isTracking = false;
        var orderId = '{{ $activeOrder->id }}';

        function startLocationUpdates() {
            if (!navigator.geolocation) {
                console.log('❌ GPS not supported on this device');
                var el = document.getElementById('lastUpdate');
                if (el) {
                    el.textContent = '⚠️ GPS not supported';
                }
                return;
            }

            if (isTracking) {
                console.log('📍 GPS already tracking');
                return;
            }

            console.log('📍 Starting GPS updates every 3 seconds...');
            isTracking = true;

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    var speed = position.coords.speed || 0;
                    var accuracy = position.coords.accuracy;

                    if (accuracy < 100) {
                        sendLocation(lat, lng, speed);
                    } else {
                        console.log('📍 Low accuracy, skipping update: ' + accuracy + 'm');
                    }

                    updateCount++;
                    var lastUpdate = document.getElementById('lastUpdate');
                    if (lastUpdate) {
                        var now = new Date();
                        lastUpdate.textContent = '📍 Updated at ' + now.toLocaleTimeString();
                    }
                },
                function(error) {
                    console.log('⚠️ GPS Error:', error.message);
                    var lastUpdate = document.getElementById('lastUpdate');
                    if (lastUpdate) {
                        lastUpdate.textContent = '⚠️ GPS Error: ' + error.message;
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 5000,
                }
            );
        }

        function sendLocation(lat, lng, speed) {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (!token) return;
            
            var csrfToken = token.getAttribute('content');
            
            fetch('/api/tracking/' + orderId + '/location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng,
                    speed: speed,
                    heading: 0,
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    // Silent success
                } else {
                    console.log('⚠️ Server rejected location:', data.message);
                }
            })
            .catch(function(error) {
                console.log('⚠️ Failed to send location:', error);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startLocationUpdates);
        } else {
            startLocationUpdates();
        }

        window.addEventListener('beforeunload', function() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                console.log('📍 GPS tracking stopped');
                isTracking = false;
            }
        });

    })();
</script>
@endif

@endsection