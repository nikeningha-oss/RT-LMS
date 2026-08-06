@extends('layouts.dashboard')

@section('title', 'My Performance - Tracklane')
@section('page-title', 'Driver Performance Reports')

@section('dashboard-content')

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="margin-bottom:16px;">
        <div style="font-size:16px; font-weight:500; color:#0F172A;">📊 My Performance</div>
        <div style="font-size:12px; color:#64748B;">Your delivery statistics and performance metrics</div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Orders</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($totalOrders) }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Delivered</div>
            <div style="font-size:22px; font-weight:600; color:#10B981; margin-top:4px;">{{ number_format($deliveredOrders) }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">In Transit</div>
            <div style="font-size:22px; font-weight:600; color:#F59E0B; margin-top:4px;">{{ number_format($inTransitOrders) }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Earnings</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($totalEarnings, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Week</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($weekOrders) }} orders</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Month</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;">{{ number_format($monthOrders) }} orders</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">Pending</div>
            <div style="font-size:18px; font-weight:600; color:#F59E0B; margin-top:4px;">{{ number_format($pendingOrders) }}</div>
        </div>
    </div>

    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">📋 Recent Deliveries</div>
        
        @if($recentOrders->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:0.5px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Order</th>
                        <th style="padding:8px; text-align:left;">Pickup</th>
                        <th style="padding:8px; text-align:left;">Status</th>
                        <th style="padding:8px; text-align:right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500;">{{ $order->order_number }}</td>
                        <td style="padding:8px; color:#64748B;">{{ Str::limit($order->pickup_address, 20) }}</td>
                        <td style="padding:8px;">
                            <span style="padding:2px 12px; border-radius:20px; font-size:11px; 
                                @if($order->status == 'delivered') background:#D1FAE5; color:#065F46;
                                @elseif($order->status == 'pending') background:#FEF3C7; color:#92400E;
                                @elseif($order->status == 'cancelled') background:#FEE2E2; color:#991B1B;
                                @else background:#DBEAFE; color:#1E40AF; @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td style="padding:8px; text-align:right;">{{ number_format($order->total_price, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:20px; color:#94A3B8;">No deliveries yet</div>
        @endif
    </div>

</div>

@endsection