@extends('layouts.dashboard')

@section('title', 'My Orders - Tracklane')
@section('page-title', 'My Orders')

@section('dashboard-content')

<style>
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-assigned { background: #DBEAFE; color: #1E40AF; }
    .status-picked_up { background: #E0E7FF; color: #3730A3; }
    .status-in_transit { background: #FEF3C7; color: #92400E; }
    .status-delivered { background: #D1FAE5; color: #065F46; }
    .status-cancelled { background: #FEE2E2; color: #991B1B; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    .stat-box {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 10px 12px;
        text-align: center;
    }
    .stat-box .label {
        font-size: 10px;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .stat-box .value {
        font-size: 16px;
        font-weight: 600;
        color: #0F172A;
    }
    .stat-box .value.green { color: #10B981; }
    .stat-box .value.blue { color: #3B82F6; }

    .earnings-badge {
        font-size: 11px;
        font-weight: 500;
        padding: 2px 10px;
        border-radius: 12px;
        background: #D1FAE5;
        color: #065F46;
    }

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
        border-bottom: 1.5px solid #E2E8F0;
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

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        display: inline-block;
    }
    .status-badge.pending { background: #F1F5F9; color: #475569; }
    .status-badge.assigned { background: #DBEAFE; color: #1E40AF; }
    .status-badge.picked_up { background: #E0E7FF; color: #3730A3; }
    .status-badge.in_transit { background: #FEF3C7; color: #92400E; }
    .status-badge.delivered { background: #D1FAE5; color: #065F46; }
    .status-badge.cancelled { background: #FEE2E2; color: #991B1B; }

    .view-details-btn {
        color: #14B8A6;
        text-decoration: none;
        font-size: 11px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .view-details-btn:hover {
        color: #0D9488;
    }

    .no-orders {
        text-align: center;
        padding: 50px 20px;
        color: #94A3B8;
    }
    .no-orders i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        color: #D7DEE6;
    }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- ============================================================
         HEADER
         ============================================================ -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">📋 My Orders</div>
            <div style="font-size:12px; color:#64748B;">View all orders assigned to you</div>
        </div>
        <a href="{{ route('driver.dashboard') }}" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px; text-decoration:none; cursor:pointer;">
            <i class="ti ti-arrow-left" style="font-size:16px;"></i> Back to Dashboard
        </a>
    </div>

    <!-- ============================================================
         STATS SUMMARY
         ============================================================ -->
    @php
        $totalEarned = $orders->where('status', 'delivered')->sum('driver_earning');
        $totalDeliveries = $orders->where('status', 'delivered')->count();
        $avgPerDelivery = $totalDeliveries > 0 ? $totalEarned / $totalDeliveries : 0;
    @endphp

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Orders</div>
            <div class="value">{{ $orders->count() ?? 0 }}</div>
        </div>
        <div class="stat-box">
            <div class="label">In Transit</div>
            <div class="value blue">{{ $orders->where('status', 'in_transit')->count() ?? 0 }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Delivered</div>
            <div class="value green">{{ $totalDeliveries }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Total Earned</div>
            <div class="value green">{{ number_format($totalEarned, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <!-- ============================================================
         ORDERS TABLE
         ============================================================ -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        
        @if(isset($orders) && $orders->count() > 0)
        <div style="overflow-x:auto;">
            <table class="table-driver">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Pickup</th>
                        <th>Delivery</th>
                        <th>Distance</th>
                        <th>Weight</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Your Earnings</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td style="font-weight:500; color:#0F172A;">
                            {{ $order->order_number }}
                        </td>
                        <td style="color:#64748B;">
                            {{ $order->customer->name ?? 'N/A' }}
                        </td>
                        <td style="color:#64748B; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ Str::limit($order->pickup_address, 15) }}
                        </td>
                        <td style="color:#64748B; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ Str::limit($order->delivery_address, 15) }}
                        </td>
                        <td style="color:#64748B;">
                            {{ $order->distance_km ?? 0 }} km
                        </td>
                        <td style="color:#64748B;">
                            {{ $order->weight_kg ?? 0 }} kg
                        </td>
                        <td style="color:#64748B;">
                            {{ optional($order->created_at)->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="status-badge {{ $order->status }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td style="text-align:right; font-weight:500; color:#10B981;">
                            @if($order->driver_earning > 0)
                                {{ number_format($order->driver_earning, 0, ',', ' ') }} F
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($order->status === 'assigned' || $order->status === 'picked_up' || $order->status === 'in_transit')
                                <button onclick="updateStatus({{ $order->id }}, 'delivered')" 
                                        style="background:#10B981; color:#FFFFFF; border:none; border-radius:4px; padding:4px 10px; font-size:10px; cursor:pointer;">
                                    <i class="ti ti-check"></i> Complete
                                </button>
                            @else
                                <a href="{{ route('tracking', $order->id) }}" class="view-details-btn">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="no-orders">
            <i class="ti ti-package"></i>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">No Orders Assigned</div>
            <p style="font-size:13px; margin-top:4px;">You haven't been assigned any orders yet.</p>
            <div style="margin-top:8px; font-size:13px; color:#64748B;">💡 Go online to receive orders</div>
        </div>
        @endif
    </div>

</div>

<script>
    function updateStatus(orderId, status) {
        if (confirm('Mark this order as ' + status + '?')) {
            fetch('/driver/orders/' + orderId + '/status', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }
    }
</script>

@endsection