@extends('layouts.dashboard')

@section('title', 'My Orders - Tracklane')
@section('page-title', 'My Orders')

@section('dashboard-content')

<style>
    .order-detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        padding: 6px 0;
        font-size: 12px;
        border-bottom: 0.5px solid #F1F5F9;
    }
    .order-detail-row .label {
        color: #94A3B8;
        font-weight: 500;
    }
    .order-detail-row .value {
        color: #0F172A;
        font-weight: 500;
    }
    .order-detail-row .value.driver { color: #10B981; }
    .order-detail-row .value.platform { color: #3B82F6; }
    .order-detail-row .value.total { color: #0D9488; font-weight: 700; }

    .price-breakdown-toggle {
        cursor: pointer;
        color: #14B8A6;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .price-breakdown-toggle:hover {
        color: #0D9488;
    }

    .price-breakdown-box {
        background: #F8FAFC;
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 8px;
        border: 0.5px solid #E2E8F0;
        display: none;
    }
    .price-breakdown-box.show {
        display: block;
    }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- ✅ SUCCESS / ERROR MESSAGES -->
    @if(session('success'))
        <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i class="ti ti-check-circle" style="font-size:18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i class="ti ti-alert-circle" style="font-size:18px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('status'))
        <div style="background:#FEF3C7; color:#92400E; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i class="ti ti-info-circle" style="font-size:18px;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">📦 My Orders</div>
            <div style="font-size:12px; color:#64748B;">View all your delivery orders with price breakdown</div>
        </div>
        <a href="{{ route('customer.create-order') }}" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:9px 16px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px; text-decoration:none; cursor:pointer;">
            <i class="ti ti-plus" style="font-size:16px;"></i> New Order
        </a>
    </div>

    <!-- Orders Table -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        
        @if(isset($orders) && $orders->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Order</th>
                        <th style="padding:8px; text-align:left;">Pickup</th>
                        <th style="padding:8px; text-align:left;">Delivery</th>
                        <th style="padding:8px; text-align:left;">Distance</th>
                        <th style="padding:8px; text-align:left;">Weight</th>
                        <th style="padding:8px; text-align:left;">Status</th>
                        <th style="padding:8px; text-align:right;">Price</th>
                        <th style="padding:8px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500; color:#0F172A;">
                            {{ $order->order_number }}
                            <br>
                            <span class="price-breakdown-toggle" onclick="toggleBreakdown({{ $order->id }})">
                                <i class="ti ti-chevron-down" id="toggleIcon{{ $order->id }}"></i> View Price Breakdown
                            </span>
                        </td>
                        <td style="padding:8px; color:#64748B;">{{ Str::limit($order->pickup_address, 15) }}</td>
                        <td style="padding:8px; color:#64748B;">{{ Str::limit($order->delivery_address, 15) }}</td>
                        <td style="padding:8px; color:#64748B;">{{ $order->distance_km ?? 0 }} km</td>
                        <td style="padding:8px; color:#64748B;">{{ $order->weight_kg ?? 0 }} kg</td>
                        <td style="padding:8px;">
                            @php
                                $statusStyles = [
                                    'pending' => 'background:#F1F5F9;color:#475569;',
                                    'assigned' => 'background:#DBEAFE;color:#1E40AF;',
                                    'picked_up' => 'background:#E0E7FF;color:#3730A3;',
                                    'in_transit' => 'background:#FEF3C7;color:#92400E;',
                                    'delivered' => 'background:#D1FAE5;color:#065F46;',
                                    'cancelled' => 'background:#FEE2E2;color:#991B1B;',
                                    'price_pending' => 'background:#FEF3C7;color:#92400E;',
                                    'price_confirmed' => 'background:#D1FAE5;color:#065F46;',
                                ];
                            @endphp
                            <span style="padding:3px 8px; border-radius:12px; font-size:11px; {{ $statusStyles[$order->status] ?? 'background:#F1F5F9;color:#475569;' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:500; color:#0F172A;">
                            @if($order->total_price > 0)
                                {{ number_format($order->total_price, 0, ',', ' ') }} F
                            @else
                                <span style="color:#94A3B8;">Pending</span>
                            @endif
                        </td>
                        <td style="padding:8px; text-align:center;">
                            @if($order->status === 'price_pending')
                                <button onclick="confirmPrice({{ $order->id }})" 
                                        style="background:#0D9488; color:#FFFFFF; border:none; border-radius:4px; padding:2px 10px; font-size:10px; cursor:pointer;">
                                    <i class="ti ti-check"></i> Confirm
                                </button>
                            @elseif($order->status === 'price_confirmed')
                                <button onclick="makePayment({{ $order->id }})" 
                                        style="background:#10B981; color:#FFFFFF; border:none; border-radius:4px; padding:2px 10px; font-size:10px; cursor:pointer;">
                                    <i class="ti ti-credit-card"></i> Pay
                                </button>
                            @else
                                <a href="{{ route('tracking', $order->id) }}" 
                                   style="color:#14B8A6; text-decoration:none; font-size:11px;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                    <!-- Price Breakdown Row -->
                    <tr id="breakdownRow{{ $order->id }}" style="display:none;">
                        <td colspan="8" style="padding:4px 8px 12px 8px;">
                            <div class="price-breakdown-box show" id="breakdownBox{{ $order->id }}">
                                <div style="font-size:13px; font-weight:600; color:#0F172A; margin-bottom:6px;">
                                    💰 Price Breakdown - {{ $order->order_number }}
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                                    <div>
                                        <div class="order-detail-row">
                                            <span class="label">Base Fare</span>
                                            <span class="value">{{ number_format($order->base_fare ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="order-detail-row">
                                            <span class="label">Distance ({{ $order->distance_km ?? 0 }} km)</span>
                                            <span class="value">{{ number_format($order->distance_charge ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="order-detail-row">
                                            <span class="label">Weight ({{ $order->weight_kg ?? 0 }} kg)</span>
                                            <span class="value">{{ number_format($order->weight_charge ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="order-detail-row">
                                            <span class="label">Service Fee (5%)</span>
                                            <span class="value">{{ number_format($order->service_fee ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="order-detail-row">
                                            <span class="label">VAT ({{ $order->tax_rate ?? 5 }}%)</span>
                                            <span class="value">{{ number_format($order->tax_amount ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="order-detail-row" style="border-bottom:1.5px solid #E2E8F0; padding-bottom:8px;">
                                            <span class="label" style="font-weight:600;">Total Price</span>
                                            <span class="value total">{{ number_format($order->total_price ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="order-detail-row">
                                            <span class="label">👨‍✈️ Driver Earns (50%)</span>
                                            <span class="value driver">{{ number_format($order->driver_earning ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="order-detail-row">
                                            <span class="label">🏢 Platform Fee (50%)</span>
                                            <span class="value platform">{{ number_format($order->platform_fee ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div style="font-size:10px; color:#94A3B8; margin-top:4px; padding:4px 0; border-top:0.5px solid #E2E8F0;">
                                            <i class="ti ti-info-circle"></i> Driver commission: 50% of subtotal
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top:6px; padding-top:6px; border-top:1px solid #E2E8F0; font-size:11px; color:#94A3B8;">
                                    <i class="ti ti-route"></i> Distance: {{ $order->distance_km ?? 0 }} km | 
                                    <i class="ti ti-weight"></i> Weight: {{ $order->weight_kg ?? 0 }} kg
                                    @if($order->status === 'delivered')
                                        | <i class="ti ti-check-circle" style="color:#10B981;"></i> Delivered on {{ optional($order->actual_delivery)->format('M d, Y h:i A') ?? 'N/A' }}
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:40px 20px; color:#94A3B8;">
            <i class="ti ti-inbox" style="font-size:48px; display:block; margin-bottom:12px; color:#D7DEE6;"></i>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">No Orders Yet</div>
            <p style="font-size:13px; margin-top:4px;">Start by creating your first delivery order.</p>
            <a href="{{ route('customer.create-order') }}" 
               style="display:inline-block; margin-top:12px; background:#0D9488; color:#FFFFFF; padding:8px 20px; border-radius:8px; text-decoration:none; font-size:13px;">
                <i class="ti ti-plus me-1"></i> Create Order
            </a>
        </div>
        @endif
    </div>

</div>

<script>
    function confirmPrice(orderId) {
        if (confirm('Confirm the price for this order?')) {
            fetch('/customer/orders/' + orderId + '/confirm-price', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error confirming price');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error confirming price');
            });
        }
    }

    function makePayment(orderId) {
        if (confirm('Proceed to payment for this order?')) {
            window.location.href = '/customer/orders/' + orderId + '/pay';
        }
    }

    function toggleBreakdown(orderId) {
        var row = document.getElementById('breakdownRow' + orderId);
        var icon = document.getElementById('toggleIcon' + orderId);
        var box = document.getElementById('breakdownBox' + orderId);
        
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            icon.className = 'ti ti-chevron-up';
            // Trigger animation
            box.style.animation = 'slideDown 0.3s ease';
        } else {
            row.style.display = 'none';
            icon.className = 'ti ti-chevron-down';
        }
    }

    // Add animation keyframes
    var style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
</script>

<style>
    .price-breakdown-box {
        background: #F8FAFC;
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 8px;
        border: 0.5px solid #E2E8F0;
    }
    .price-breakdown-toggle {
        cursor: pointer;
        color: #14B8A6;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .price-breakdown-toggle:hover {
        color: #0D9488;
    }
    .order-detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 4px 0;
        font-size: 12px;
        border-bottom: 0.5px solid #F1F5F9;
    }
    .order-detail-row .label {
        color: #94A3B8;
        font-weight: 500;
    }
    .order-detail-row .value {
        color: #0F172A;
        font-weight: 500;
        text-align: right;
    }
    .order-detail-row .value.driver { color: #10B981; }
    .order-detail-row .value.platform { color: #3B82F6; }
    .order-detail-row .value.total { color: #0D9488; font-weight: 700; }
</style>

@endsection