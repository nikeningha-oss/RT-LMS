@extends('layouts.dashboard')

@section('title', 'My Reports - Tracklane')
@section('page-title', 'My Delivery Reports')

@section('dashboard-content')

<style>
    .report-card {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 14px 16px;
        transition: all 0.2s ease;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .report-card .label {
        font-size: 11px;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .report-card .value {
        font-size: 22px;
        font-weight: 600;
        color: #0F172A;
        margin-top: 4px;
    }
    .report-card .value.green { color: #10B981; }
    .report-card .value.orange { color: #F59E0B; }
    .report-card .value.blue { color: #3B82F6; }
    .chart-bar {
        width: 100%;
        max-width: 40px;
        border-radius: 4px 4px 0 0;
        background: #14B8A6;
        min-height: 4px;
        transition: height 0.3s ease;
    }
    .status-badge {
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        display: inline-block;
    }
    .status-badge.delivered { background: #D1FAE5; color: #065F46; }
    .status-badge.pending { background: #FEF3C7; color: #92400E; }
    .status-badge.cancelled { background: #FEE2E2; color: #991B1B; }
    .status-badge.assigned { background: #DBEAFE; color: #1E40AF; }
    .status-badge.picked_up { background: #E0E7FF; color: #3730A3; }
    .status-badge.in_transit { background: #FEF3C7; color: #92400E; }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- ============================================================
         HEADER
         ============================================================ -->
    <div style="margin-bottom:16px;">
        <div style="font-size:16px; font-weight:500; color:#0F172A;">📊 My Delivery Reports</div>
        <div style="font-size:12px; color:#64748B;">Your personal delivery statistics and history</div>
    </div>

    <!-- ============================================================
         STATS CARDS
         ============================================================ -->
    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        
        <!-- Total Orders -->
        <div class="report-card">
            <div class="label">Total Orders</div>
            <div class="value">{{ number_format($totalOrders ?? 0) }}</div>
        </div>
        
        <!-- Delivered -->
        <div class="report-card">
            <div class="label">Delivered</div>
            <div class="value green">{{ number_format($deliveredOrders ?? 0) }}</div>
        </div>
        
        <!-- In Transit -->
        <div class="report-card">
            <div class="label">In Transit</div>
            <div class="value orange">{{ number_format($inTransitOrders ?? 0) }}</div>
        </div>
        
        <!-- Total Spent -->
        <div class="report-card">
            <div class="label">Total Spent</div>
            <div class="value">{{ number_format($totalSpent ?? 0, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <!-- ============================================================
         MONTHLY ORDERS CHART
         ============================================================ -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px; margin-bottom:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">📈 Monthly Orders (Last 6 Months)</div>
        
        @if(isset($monthlyOrders) && count($monthlyOrders) > 0)
            @php
                $maxCount = max(array_column($monthlyOrders, 'count'));
                $maxCount = $maxCount > 0 ? $maxCount : 1;
            @endphp
            <div style="display:flex; align-items:flex-end; gap:12px; height:120px; padding-top:10px;">
                @foreach($monthlyOrders as $month)
                    @php
                        $height = max(4, ($month['count'] / $maxCount) * 100);
                    @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%;">
                        <div style="font-size:10px; color:#64748B; margin-bottom:4px;">{{ $month['count'] }}</div>
                        <div class="chart-bar" style="height:{{ $height }}%;"></div>
                        <div style="font-size:10px; color:#64748B; margin-top:6px;">{{ $month['month'] }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:20px; color:#94A3B8; font-size:13px;">
                No monthly data available
            </div>
        @endif
    </div>

    <!-- ============================================================
         RECENT ORDERS TABLE
         ============================================================ -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">📋 Recent Orders</div>
        
        @if(isset($recentOrders) && $recentOrders->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:0.5px solid #E2E8F0;">
                        <th style="padding:8px 8px 8px 0; text-align:left;">Order</th>
                        <th style="padding:8px; text-align:left;">Pickup</th>
                        <th style="padding:8px; text-align:left;">Status</th>
                        <th style="padding:8px; text-align:right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px 8px 8px 0; font-weight:500; color:#0F172A;">{{ $order->order_number }}</td>
                        <td style="padding:8px; color:#64748B;">{{ Str::limit($order->pickup_address, 20) }}</td>
                        <td style="padding:8px;">
                            @php
                                $statusClass = match($order->status) {
                                    'delivered' => 'delivered',
                                    'pending' => 'pending',
                                    'cancelled' => 'cancelled',
                                    'assigned' => 'assigned',
                                    'picked_up' => 'picked_up',
                                    'in_transit' => 'in_transit',
                                    default => 'pending'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:500; color:#0F172A;">
                            {{ number_format($order->total_price, 0, ',', ' ') }} F
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:20px; color:#94A3B8; font-size:13px;">
            <i class="ti ti-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            No orders yet
        </div>
        @endif
    </div>

</div>

@endsection