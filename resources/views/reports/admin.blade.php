@extends('layouts.dashboard')

@section('title', 'Admin Reports - Tracklane')
@section('page-title', 'System Reports & Analytics')

@section('dashboard-content')

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">📊 System Reports</div>
            <div style="font-size:12px; color:#64748B;">Complete overview of your logistics network</div>
        </div>
        <a href="{{ route('admin.reports.export') }}" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:9px 16px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px; text-decoration:none; cursor:pointer;">
            <i class="ti ti-download" style="font-size:16px;"></i> Export Report
        </a>
    </div>

    <!-- Stats Cards -->
    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Orders</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($totalOrders) }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Revenue</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($totalRevenue, 0, ',', ' ') }} F</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Active Drivers</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ $activeDrivers }}/{{ $totalDrivers }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">On-Time Delivery</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ $onTimeRate }}%</div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">Today's Revenue</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($todayRevenue, 0, ',', ' ') }} F</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Week</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($weekOrders) }} orders</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Month</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($monthOrders) }} orders</div>
        </div>
    </div>

    <!-- Daily Orders Chart -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px; margin-bottom:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">📈 Daily Orders (Last 7 Days)</div>
        <div style="display:flex; align-items:flex-end; gap:12px; height:120px;">
            @foreach($dailyOrders as $day)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%;">
                <div style="font-size:10px; color:#64748B; margin-bottom:4px;">{{ $day['count'] }}</div>
                <div style="width:100%; max-width:40px; border-radius:4px 4px 0 0; background:#14B8A6; height:{{ max(4, ($day['count'] / max(1, collect($dailyOrders)->max('count'))) * 100) }}%; min-height:4px;"></div>
                <div style="font-size:10px; color:#64748B; margin-top:6px;">{{ $day['date'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Drivers -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">🏆 Top Performing Drivers</div>
        
        @if($topDrivers->count() > 0)
            @foreach($topDrivers as $driver)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:0.5px solid #E2E8F0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; color:#0D9488;">
                        {{ strtoupper(substr($driver->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size:13px; color:#0F172A;">{{ $driver->name }}</div>
                        <div style="font-size:11px; color:#64748B;">{{ $driver->assigned_orders_count ?? 0 }} deliveries</div>
                    </div>
                </div>
                <span style="font-size:13px; font-weight:600; color:#0D9488;">🏅</span>
            </div>
            @endforeach
        @else
            <div style="text-align:center; color:#94A3B8; padding:20px;">No driver data yet</div>
        @endif
    </div>

</div>

@endsection