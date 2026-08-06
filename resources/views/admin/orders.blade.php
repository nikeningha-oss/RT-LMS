@extends('layouts.dashboard')

@section('title', 'Manage Orders - Tracklane')
@section('page-title', 'Orders Management')

@section('dashboard-content')

<style>
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-assigned { background: #DBEAFE; color: #1E40AF; }
    .status-picked_up { background: #E0E7FF; color: #3730A3; }
    .status-in_transit { background: #FEF3C7; color: #92400E; }
    .status-delivered { background: #D1FAE5; color: #065F46; }
    .status-cancelled { background: #FEE2E2; color: #991B1B; }
    .status-price_pending { background: #FEF3C7; color: #92400E; }
    .status-price_confirmed { background: #D1FAE5; color: #065F46; }

    .earnings-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .earnings-dot.driver { background: #10B981; }
    .earnings-dot.platform { background: #3B82F6; }
    .earnings-dot.tax { background: #F59E0B; }

    .price-breakdown-toggle {
        cursor: pointer;
        color: #14B8A6;
        font-size: 10px;
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
        padding: 10px 14px;
        margin-top: 6px;
        border: 0.5px solid #E2E8F0;
        display: none;
    }
    .price-breakdown-box.show {
        display: block;
    }
    .price-breakdown-box .row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 12px;
        border-bottom: 0.5px solid #F1F5F9;
    }
    .price-breakdown-box .row:last-child {
        border-bottom: none;
        font-weight: 700;
        padding-top: 6px;
        border-top: 1.5px solid #E2E8F0;
    }
    .price-breakdown-box .row .label { color: #64748B; }
    .price-breakdown-box .row .value { color: #0F172A; }
    .price-breakdown-box .row .value.driver { color: #10B981; }
    .price-breakdown-box .row .value.platform { color: #3B82F6; }
    .price-breakdown-box .row .value.total { color: #0D9488; font-weight: 700; }

    .assign-btn {
        background: #3B82F6;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-block;
        border: none;
    }
    .assign-btn:hover {
        background: #2563EB;
    }
    
    .assign-btn.success {
        background: #10B981;
    }
    .assign-btn.success:hover {
        background: #059669;
    }
    
    .btn-view {
        background: #14B8A6;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-view:hover {
        background: #0D9488;
    }

    /* Driver option card */
    .driver-option {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .driver-option:hover {
        border-color: #14B8A6;
        background: #F8FAFC;
    }
    .driver-option.selected {
        background: #F0FDFA;
        border-color: #14B8A6;
        box-shadow: 0 0 0 2px rgba(20,184,166,0.15);
    }
    
    .driver-option .driver-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #CCFBF1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        color: #0D9488;
        flex-shrink: 0;
    }
    
    .driver-option .distance-badge {
        background: #D1FAE5;
        color: #065F46;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .driver-option .distance-badge.far {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .driver-list-container {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Toast notification */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .toast {
        padding: 14px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        min-width: 280px;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        animation: slideIn 0.4s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toast.success {
        background: #D1FAE5;
        color: #065F46;
        border-left: 4px solid #10B981;
    }
    .toast.error {
        background: #FEE2E2;
        color: #991B1B;
        border-left: 4px solid #E24B4A;
    }
    .toast.info {
        background: #DBEAFE;
        color: #1E40AF;
        border-left: 4px solid #3B82F6;
    }
    .toast .toast-icon {
        font-size: 20px;
    }
    .toast .toast-close {
        margin-left: auto;
        cursor: pointer;
        font-size: 18px;
        opacity: 0.6;
        background: none;
        border: none;
        color: inherit;
        padding: 0 4px;
    }
    .toast .toast-close:hover {
        opacity: 1;
    }
    @keyframes slideIn {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100px); opacity: 0; }
    }
    .toast.slide-out {
        animation: slideOut 0.3s ease forwards;
    }
</style>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">📋 Manage Orders</div>
            <div style="font-size:12px; color:#64748B;">View and manage all orders with earnings breakdown</div>
        </div>
        <div style="display:flex; gap:8px;">
            <button onclick="window.location.reload()" 
                    style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 16px; font-size:12px; cursor:pointer;">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div style="display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:8px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Total</div>
            <div style="font-size:16px; font-weight:600; color:#0F172A;">{{ $orders->total() ?? 0 }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Pending</div>
            <div style="font-size:16px; font-weight:600; color:#F59E0B;">
                {{ $orders->where('status', 'pending')->count() ?? 0 }}
            </div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Price Pending</div>
            <div style="font-size:16px; font-weight:600; color:#F59E0B;">
                {{ $orders->where('status', 'price_pending')->count() ?? 0 }}
            </div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">In Transit</div>
            <div style="font-size:16px; font-weight:600; color:#3B82F6;">
                {{ $orders->where('status', 'in_transit')->count() ?? 0 }}
            </div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Delivered</div>
            <div style="font-size:16px; font-weight:600; color:#10B981;">
                {{ $orders->where('status', 'delivered')->count() ?? 0 }}
            </div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Cancelled</div>
            <div style="font-size:16px; font-weight:600; color:#E24B4A;">
                {{ $orders->where('status', 'cancelled')->count() ?? 0 }}
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        
        @if(isset($orders) && $orders->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Order</th>
                        <th style="padding:8px; text-align:left;">Customer</th>
                        <th style="padding:8px; text-align:left;">Driver</th>
                        <th style="padding:8px; text-align:left;">Distance</th>
                        <th style="padding:8px; text-align:left;">Weight</th>
                        <th style="padding:8px; text-align:left;">Status</th>
                        <th style="padding:8px; text-align:right;">Total Price</th>
                        <th style="padding:8px; text-align:right;">Driver Earns</th>
                        <th style="padding:8px; text-align:right;">Platform Fee</th>
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
                                <i class="ti ti-chevron-down" id="toggleIcon{{ $order->id }}"></i> Breakdown
                            </span>
                        </td>
                        <td style="padding:8px; color:#64748B;">
                            {{ $order->customer->name ?? 'N/A' }}
                        </td>
                        <td style="padding:8px; color:#64748B;">
                            {{ $order->driver->name ?? 'Unassigned' }}
                        </td>
                        <td style="padding:8px; color:#64748B;">
                            {{ $order->distance_km ?? 0 }} km
                        </td>
                        <td style="padding:8px; color:#64748B;">
                            {{ $order->weight_kg ?? 0 }} kg
                        </td>
                        <td style="padding:8px;">
                            <span class="status-{{ $order->status }}" style="padding:3px 10px; border-radius:12px; font-size:11px;">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:500; color:#0F172A;">
                            @if($order->total_price > 0)
                                {{ number_format($order->total_price, 0, ',', ' ') }} F
                            @else
                                <span style="color:#94A3B8;">Not set</span>
                            @endif
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:500; color:#10B981;">
                            @if($order->driver_earning > 0)
                                {{ number_format($order->driver_earning, 0, ',', ' ') }} F
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:500; color:#3B82F6;">
                            @if($order->platform_fee > 0)
                                {{ number_format($order->platform_fee, 0, ',', ' ') }} F
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td style="padding:8px; text-align:center;">
                            @if($order->status === 'pending')
                                <button data-order-id="{{ $order->id }}" 
                                        class="set-price-btn"
                                        style="background:#14B8A6; color:#FFFFFF; border:none; border-radius:6px; padding:4px 12px; font-size:11px; cursor:pointer;">
                                    Set Price
                                </button>
                            @elseif($order->status === 'price_pending')
                                <span style="font-size:10px; color:#F59E0B;">
                                    <i class="ti ti-clock"></i> Awaiting Customer
                                </span>
                            @elseif($order->status === 'price_confirmed')
                                <span style="font-size:10px; color:#F59E0B;">
                                    <i class="ti ti-clock"></i> Awaiting Payment
                                </span>
                            @elseif($order->status === 'assigned' && !$order->driver_id)
                                @if($order->isPaid())
                                    <button data-order-id="{{ $order->id }}" 
                                            class="assign-driver-btn assign-btn"
                                            style="background:#3B82F6; color:#FFFFFF; border:none; border-radius:6px; padding:4px 12px; font-size:11px; cursor:pointer;">
                                        <i class="ti ti-user-plus"></i> Assign
                                    </button>
                                @else
                                    <span style="font-size:10px; color:#E24B4A;">
                                        <i class="ti ti-alert-circle"></i> Payment Pending
                                    </span>
                                @endif
                            @elseif($order->status === 'delivered' || $order->status === 'cancelled')
                                <a href="{{ route('tracking', $order->id) }}" 
                                   class="btn-view">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            @else
                                <a href="{{ route('tracking', $order->id) }}" 
                                   class="btn-view">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                    <!-- Price Breakdown Row -->
                    <tr id="breakdownRow{{ $order->id }}" style="display:none;">
                        <td colspan="10" style="padding:4px 8px 12px 8px;">
                            <div class="price-breakdown-box show" id="breakdownBox{{ $order->id }}">
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:8px; font-size:12px;">
                                    <div>
                                        <div class="row"><span class="label">Base Fare</span><span class="value">{{ number_format($order->base_fare ?? 0, 0, ',', ' ') }} F</span></div>
                                        <div class="row"><span class="label">Distance ({{ $order->distance_km ?? 0 }} km)</span><span class="value">{{ number_format($order->distance_charge ?? 0, 0, ',', ' ') }} F</span></div>
                                        <div class="row"><span class="label">Weight ({{ $order->weight_kg ?? 0 }} kg)</span><span class="value">{{ number_format($order->weight_charge ?? 0, 0, ',', ' ') }} F</span></div>
                                    </div>
                                    <div>
                                        <div class="row"><span class="label">Subtotal</span><span class="value" style="font-weight:600;">{{ number_format(($order->base_fare ?? 0) + ($order->distance_charge ?? 0) + ($order->weight_charge ?? 0), 0, ',', ' ') }} F</span></div>
                                        <div class="row"><span class="label">Service Fee (5%)</span><span class="value">{{ number_format($order->service_fee ?? 0, 0, ',', ' ') }} F</span></div>
                                        <div class="row"><span class="label">VAT ({{ $order->tax_rate ?? 5 }}%)</span><span class="value">{{ number_format($order->tax_amount ?? 0, 0, ',', ' ') }} F</span></div>
                                    </div>
                                    <div>
                                        <div class="row" style="font-weight:700; border-top:1px solid #E2E8F0; padding-top:6px;">
                                            <span class="label">💰 Total Price</span>
                                            <span class="value total">{{ number_format($order->total_price ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="row">
                                            <span class="label"><span class="earnings-dot driver"></span> Driver Earns (50%)</span>
                                            <span class="value driver">{{ number_format($order->driver_earning ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                        <div class="row">
                                            <span class="label"><span class="earnings-dot platform"></span> Platform Fee (50%)</span>
                                            <span class="value platform">{{ number_format($order->platform_fee ?? 0, 0, ',', ' ') }} F</span>
                                        </div>
                                    </div>
                                    <div style="background:#F1F5F9; border-radius:8px; padding:8px 10px;">
                                        <div style="font-size:11px; font-weight:600; color:#0F172A; margin-bottom:4px;">📊 Summary</div>
                                        <div style="font-size:11px; color:#64748B;">
                                            <div>Distance: <strong>{{ $order->distance_km ?? 0 }} km</strong></div>
                                            <div>Weight: <strong>{{ $order->weight_kg ?? 0 }} kg</strong></div>
                                            <div>Customer: <strong>{{ $order->customer->name ?? 'N/A' }}</strong></div>
                                            @if($order->status === 'delivered')
                                                <div style="color:#10B981;">✅ Delivered: {{ optional($order->actual_delivery)->format('M d, Y h:i A') ?? 'N/A' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($orders) && method_exists($orders, 'links'))
        <div style="margin-top:16px;">
            {{ $orders->links() }}
        </div>
        @endif
        
        @else
        <div style="text-align:center; padding:40px 20px; color:#94A3B8;">
            <i class="ti ti-inbox" style="font-size:48px; display:block; margin-bottom:12px; color:#D7DEE6;"></i>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">No Orders Yet</div>
            <p style="font-size:13px; margin-top:4px;">Orders will appear here once customers create them.</p>
        </div>
        @endif
    </div>

</div>

<!-- ============================================================
     PRICE MODAL
     ============================================================ -->
<div id="priceModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:500px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="font-size:18px; font-weight:600; color:#0F172A;">💰 Set Order Price</span>
            <button onclick="closePriceModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94A3B8;">✕</button>
        </div>
        <form id="priceForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; font-weight:500; color:#0F172A;">
                    <i class="ti ti-route"></i> Distance (km)
                </label>
                <input type="number" name="distance_km" id="orderDistance" 
                       style="width:100%; padding:10px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; margin-top:4px;" 
                       placeholder="Enter distance in km" step="0.1" min="0">
            </div>
            
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; font-weight:500; color:#0F172A;">
                    <i class="ti ti-weight"></i> Weight (kg)
                </label>
                <input type="number" name="weight_kg" id="orderWeight" 
                       style="width:100%; padding:10px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; margin-top:4px;" 
                       placeholder="Enter weight in kg" step="0.1" min="0">
            </div>
            
            <div style="background:#F8FAFC; padding:12px; border-radius:8px; margin-bottom:12px;">
                <div style="font-size:12px; font-weight:600; color:#0F172A; margin-bottom:6px;">💰 Price Preview</div>
                <div style="display:flex; justify-content:space-between; font-size:12px; padding:2px 0;">
                    <span style="color:#64748B;">Base Fare:</span>
                    <span id="displayBaseFare">500 F</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:12px; padding:2px 0;">
                    <span style="color:#64748B;">Distance Charge:</span>
                    <span id="displayDistanceCharge">0 F</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:12px; padding:2px 0;">
                    <span style="color:#64748B;">Weight Charge:</span>
                    <span id="displayWeightCharge">0 F</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:12px; padding:2px 0; border-top:1px solid #E2E8F0; margin-top:4px; padding-top:6px;">
                    <span style="color:#64748B; font-weight:600;">Total:</span>
                    <span id="displayTotalPrice" style="font-weight:700; color:#0D9488;">0 F</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:11px; padding:2px 0; color:#10B981;">
                    <span>👨‍✈️ Driver Earns (50%):</span>
                    <span id="displayDriverEarning">0 F</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:11px; padding:2px 0; color:#3B82F6;">
                    <span>🏢 Platform Fee (50%):</span>
                    <span id="displayPlatformFee">0 F</span>
                </div>
            </div>
            
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="submit" name="status" value="approved" 
                        style="flex:1; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:10px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-check me-1"></i> Approve
                </button>
                <button type="submit" name="status" value="rejected" 
                        style="flex:1; background:#E24B4A; color:#FFFFFF; border:none; border-radius:8px; padding:10px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-x me-1"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     ASSIGN DRIVER MODAL - Shows ALL online drivers
     ============================================================ -->
<div id="assignModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:95%; max-width:900px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="font-size:18px; font-weight:600; color:#0F172A;">🚚 Assign Driver</span>
            <button onclick="closeAssignModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94A3B8;">✕</button>
        </div>
        
        <div id="assignModalContent">
            <div style="text-align:center; padding:40px;">
                <i class="ti ti-loader" style="font-size:32px; animation:spin 1s linear infinite;"></i>
                <p style="margin-top:8px; color:#64748B;">Loading drivers...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================================
    // GLOBAL VARIABLES
    // ============================================================
    var assignMap = null; // ✅ Declare the map variable globally

    // ============================================================
    // TOAST NOTIFICATIONS
    // ============================================================
    
    function showToast(message, type = 'success') {
        var container = document.getElementById('toastContainer');
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        
        var icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
        
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <span>${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            toast.classList.add('slide-out');
            setTimeout(function() {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }

    // ============================================================
    // SPIN ANIMATION
    // ============================================================
    var styleSpin = document.createElement('style');
    styleSpin.textContent = `@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }`;
    document.head.appendChild(styleSpin);

    // ============================================================
    // EVENT LISTENERS
    // ============================================================
    
    document.addEventListener('DOMContentLoaded', function() {
        // Check for session flash messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        // Set Price buttons
        document.querySelectorAll('.set-price-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var orderId = this.getAttribute('data-order-id');
                openPriceModal(orderId);
            });
        });
        
        // Assign Driver buttons - opens modal with ALL online drivers
        document.querySelectorAll('.assign-driver-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var orderId = this.getAttribute('data-order-id');
                openAssignModal(orderId);
            });
        });
        
        // Price calculator
        var distanceInput = document.getElementById('orderDistance');
        var weightInput = document.getElementById('orderWeight');
        if (distanceInput) distanceInput.addEventListener('input', calculatePricePreview);
        if (weightInput) weightInput.addEventListener('input', calculatePricePreview);
    });

    // ============================================================
    // PRICE CALCULATOR
    // ============================================================
    
    function calculatePricePreview() {
        var distance = parseFloat(document.getElementById('orderDistance').value) || 0;
        var weight = parseFloat(document.getElementById('orderWeight').value) || 0;
        
        var baseFare = 500;
        var distanceCharge = distance * 300;
        var weightCharge = weight * 200;
        var subtotal = baseFare + distanceCharge + weightCharge;
        var serviceFee = subtotal * 0.05;
        var taxAmount = (subtotal + serviceFee) * 0.05;
        var totalPrice = subtotal + serviceFee + taxAmount;
        var driverEarning = subtotal * 0.5;
        var platformFee = subtotal * 0.5;
        
        document.getElementById('displayBaseFare').textContent = baseFare + ' F';
        document.getElementById('displayDistanceCharge').textContent = Math.round(distanceCharge) + ' F';
        document.getElementById('displayWeightCharge').textContent = Math.round(weightCharge) + ' F';
        document.getElementById('displayTotalPrice').textContent = Math.round(totalPrice).toLocaleString() + ' F';
        document.getElementById('displayDriverEarning').textContent = Math.round(driverEarning).toLocaleString() + ' F';
        document.getElementById('displayPlatformFee').textContent = Math.round(platformFee).toLocaleString() + ' F';
    }

    // ============================================================
    // PRICE MODAL
    // ============================================================
    
    function openPriceModal(orderId) {
        document.getElementById('priceModal').style.display = 'block';
        document.getElementById('priceForm').action = '/admin/orders/' + orderId + '/price';
        document.getElementById('orderDistance').focus();
        calculatePricePreview();
    }
    
    function closePriceModal() {
        document.getElementById('priceModal').style.display = 'none';
    }

    // ============================================================
    // ✅ DYNAMIC LEAFLET LOADER (Fallback)
    // ============================================================

    function loadLeaflet(callback) {
        if (typeof L !== 'undefined') {
            if (callback) callback();
            return;
        }
        
        // Load CSS
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
        
        // Load JS
        var script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = function() {
            if (callback) callback();
        };
        script.onerror = function() {
            console.error('Failed to load Leaflet library');
            var mapContainer = document.getElementById('assignMap');
            if (mapContainer) {
                mapContainer.innerHTML = '<div style="padding:20px; text-align:center; color:#E24B4A;"><i class="ti ti-alert-circle"></i> Failed to load map library. Please refresh.</div>';
            }
        };
        document.head.appendChild(script);
    }

    // ============================================================
    // ✅ OPEN ASSIGN MODAL - Using Web Route instead of API Route
    // ============================================================
    
    function openAssignModal(orderId) {
        var modal = document.getElementById('assignModal');
        var content = document.getElementById('assignModalContent');
        modal.style.display = 'block';
        content.innerHTML = '<div style="text-align:center; padding:40px;"><i class="ti ti-loader" style="font-size:32px; animation:spin 1s linear infinite;"></i><p style="margin-top:8px; color:#64748B;">Loading drivers...</p></div>';
        
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // ✅ Using admin web route instead of API route
        fetch('/admin/orders/' + orderId + '/drivers', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(function(response) { 
            if (!response.ok) {
                return response.json().then(function(data) {
                    throw new Error(data.message || 'Server error (HTTP ' + response.status + ')');
                });
            }
            return response.json(); 
        })
        .then(function(data) {
            console.log('✅ Drivers loaded:', data); // Debug log
            if (data.success) {
                renderAssignModalContent(data, orderId);
            } else {
                content.innerHTML = '<div style="text-align:center; padding:40px; color:#E24B4A;"><i class="ti ti-alert-circle" style="font-size:32px;"></i><p style="margin-top:8px;">' + (data.message || 'Error loading drivers') + '</p></div>';
                showToast('Error loading drivers: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(function(error) {
            console.error('❌ Error:', error);
            content.innerHTML = '<div style="text-align:center; padding:40px; color:#E24B4A;"><i class="ti ti-alert-circle" style="font-size:32px;"></i><p style="margin-top:8px;">Error loading drivers: ' + error.message + '</p></div>';
            showToast('Failed to load drivers. Please try again.', 'error');
        });
    }
    
    // ============================================================
    // ✅ RENDER ASSIGN MODAL CONTENT - No form, uses AJAX
    // ============================================================
    
    function renderAssignModalContent(data, orderId) {
        var content = document.getElementById('assignModalContent');
        var html = '';
        
        html += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">';
        
        // Left: Driver list
        html += '<div>';
        html += '<div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">';
        html += '👨‍✈️ Available Drivers <span style="font-size:12px; font-weight:400; color:#64748B;">(' + (data.drivers ? data.drivers.length : 0) + ' online)</span>';
        html += '</div>';
        html += '<div style="font-size:12px; color:#64748B; margin-bottom:8px;">';
        html += '<i class="ti ti-info-circle"></i> All online drivers are shown. Distance is calculated from pickup point.';
        html += '</div>';
        
        if (data.drivers && data.drivers.length > 0) {
            html += '<div class="driver-list-container">';
            data.drivers.forEach(function(driver, index) {
                var isFirst = index === 0;
                var isNearby = driver.distance !== null && driver.distance <= 10;
                var badgeClass = isNearby ? '' : 'far';
                var badgeText = driver.distance !== null ? driver.distance + ' km away' : 'Location unknown';
                
                html += '<div class="driver-option ' + (isFirst ? 'selected' : '') + '" data-driver-id="' + driver.id + '" onclick="selectDriver(this)">';
                html += '<div style="display:flex; justify-content:space-between; align-items:center;">';
                html += '<div style="display:flex; align-items:center; gap:10px;">';
                html += '<div class="driver-avatar">' + (driver.name ? driver.name.substring(0,2).toUpperCase() : 'D') + '</div>';
                html += '<div>';
                html += '<div style="font-weight:500; color:#0F172A; font-size:13px;">' + driver.name + '</div>';
                html += '<div style="font-size:11px; color:#64748B;">📞 ' + driver.phone + ' · ' + (driver.vehicle ? driver.vehicle.model : 'No vehicle') + '</div>';
                html += '</div></div>';
                html += '<div style="text-align:right;">';
                if (driver.distance !== null) {
                    html += '<span class="distance-badge ' + badgeClass + '">' + badgeText + '</span>';
                } else {
                    html += '<span class="distance-badge" style="background:#F1F5F9; color:#64748B;">' + badgeText + '</span>';
                }
                html += '<div style="font-size:10px; color:#94A3B8; margin-top:2px;">' + (isFirst ? '🟢 Recommended' : '') + '</div>';
                html += '</div></div>';
                html += '</div>';
            });
            html += '</div>';
        } else {
            html += '<div style="text-align:center; padding:30px; color:#94A3B8; background:#F8FAFC; border-radius:8px;">';
            html += '<i class="ti ti-users" style="font-size:32px; display:block; margin-bottom:8px;"></i>';
            html += 'No online drivers available at the moment.';
            html += '</div>';
        }
        
        // Use button with onclick instead of form submit
        var defaultDriverId = (data.drivers && data.drivers.length > 0) ? data.drivers[0].id : '';
        var disabled = (data.drivers && data.drivers.length > 0) ? '' : 'disabled';
        var disabledStyle = (data.drivers && data.drivers.length > 0) ? '' : 'disabled; opacity:0.5; cursor:not-allowed;';
        
        html += '<button onclick="submitAssignDriver(' + orderId + ')" class="assign-btn" style="width:100%; padding:10px; font-size:14px; border:none; border-radius:8px; background:#0D9488; color:#FFFFFF; cursor:pointer; ' + disabledStyle + '" ' + disabled + '>';
        html += '<i class="ti ti-user-plus"></i> Assign Selected Driver';
        html += '</button>';
        html += '<input type="hidden" id="selectedDriverId" value="' + defaultDriverId + '">';
        
        html += '</div>';
        
        // Right: Map showing drivers
        html += '<div>';
        html += '<div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">';
        html += '🗺️ Driver Locations';
        html += '</div>';
        html += '<div id="assignMap" style="height:350px; border-radius:8px; background:#EEF2F6;"></div>';
        html += '</div>';
        
        html += '</div>';
        
        content.innerHTML = html;
        
        // Initialize the assignment map with a delay
        setTimeout(function() {
            initAssignMap(data);
        }, 200);
    }
    
    // ============================================================
    // ✅ INIT ASSIGN MAP
    // ============================================================
    
    function initAssignMap(data) {
        var mapContainer = document.getElementById('assignMap');
        if (!mapContainer) return;
        
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.warn('Leaflet library not loaded, attempting to load...');
            mapContainer.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8;"><i class="ti ti-loader" style="animation:spin 1s linear infinite;"></i> Loading map...</div>';
            loadLeaflet(function() {
                initAssignMap(data);
            });
            return;
        }
        
        // Remove existing map if any
        if (assignMap) {
            assignMap.remove();
            assignMap = null;
        }
        
        try {
            assignMap = L.map('assignMap', {
                center: [4.0511, 9.7679],
                zoom: 13
            });
            
            // Using OpenStreetMap (free, no API key needed)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(assignMap);
            
            var bounds = [];
            
            // Pickup marker
            if (data.order && data.order.pickup_lat && data.order.pickup_lng) {
                var pickupIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:18px;height:18px;border-radius:50%;background:#D85A30;border:3px solid #FFFFFF;box-shadow:0 2px 8px rgba(216,90,48,0.4);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:10px;color:#fff;">P</div>',
                    iconSize: [18,18],
                    iconAnchor: [9,9]
                });
                var pickupLat = parseFloat(data.order.pickup_lat);
                var pickupLng = parseFloat(data.order.pickup_lng);
                var marker = L.marker([pickupLat, pickupLng], { icon: pickupIcon })
                    .addTo(assignMap)
                    .bindPopup('<strong>📍 Pickup</strong><br>' + (data.order.pickup_address || ''));
                bounds.push([pickupLat, pickupLng]);
            }
            
            // Driver markers
            if (data.drivers) {
                data.drivers.forEach(function(driver, index) {
                    if (driver.current_lat && driver.current_lng) {
                        var isFirst = index === 0;
                        var isNearby = driver.distance !== null && driver.distance <= 10;
                        var color = isFirst ? '#10B981' : (isNearby ? '#14B8A6' : '#94A3B8');
                        var driverIcon = L.divIcon({
                            className: '',
                            html: '<div style="width:24px;height:24px;border-radius:50%;background:' + color + ';border:3px solid #FFFFFF;box-shadow:0 0 0 4px ' + (isFirst ? 'rgba(16,185,129,0.3)' : 'rgba(20,184,166,0.2)') + ';display:flex;align-items:center;justify-content:center;font-weight:700;font-size:8px;color:#fff;">' + (index + 1) + '</div>',
                            iconSize: [24,24],
                            iconAnchor: [12,12]
                        });
                        var driverLat = parseFloat(driver.current_lat);
                        var driverLng = parseFloat(driver.current_lng);
                        var marker = L.marker([driverLat, driverLng], { icon: driverIcon })
                            .addTo(assignMap)
                            .bindPopup('<strong>🚚 ' + driver.name + '</strong><br>' + (driver.distance !== null ? driver.distance + ' km from pickup' : 'Location unknown'));
                        bounds.push([driverLat, driverLng]);
                    }
                });
            }
            
            if (bounds.length > 0) {
                assignMap.fitBounds(bounds, { padding: [50, 50] });
            } else {
                assignMap.setView([4.0511, 9.7679], 13);
            }
            
            // Force map resize
            setTimeout(function() {
                if (assignMap) {
                    assignMap.invalidateSize();
                }
            }, 300);
            
        } catch (error) {
            console.error('Map initialization error:', error);
            mapContainer.innerHTML = '<div style="padding:20px; text-align:center; color:#E24B4A;"><i class="ti ti-alert-circle"></i> Error loading map: ' + error.message + '</div>';
        }
    }
    
    // ============================================================
    // ✅ SELECT DRIVER
    // ============================================================
    
    function selectDriver(element) {
        document.querySelectorAll('.driver-option').forEach(function(el) {
            el.classList.remove('selected');
        });
        element.classList.add('selected');
        var driverId = element.getAttribute('data-driver-id');
        document.getElementById('selectedDriverId').value = driverId;
        console.log('✅ Selected driver ID:', driverId);
    }
    
    // ============================================================
    // ✅ CLOSE ASSIGN MODAL
    // ============================================================
    
    function closeAssignModal() {
        document.getElementById('assignModal').style.display = 'none';
        if (assignMap) {
            assignMap.remove();
            assignMap = null;
        }
    }

    // ============================================================
    // ✅ SUBMIT ASSIGN DRIVER VIA AJAX WITH TOAST MESSAGES
    // ============================================================
    
    function submitAssignDriver(orderId) {
        var driverId = document.getElementById('selectedDriverId').value;
        
        console.log('📤 Submitting assignment - Order:', orderId, 'Driver:', driverId);
        
        if (!driverId) {
            showToast('Please select a driver first.', 'error');
            return;
        }
        
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading state
        var btn = document.querySelector('#assignModalContent .assign-btn');
        var originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite;"></i> Assigning...';
            btn.disabled = true;
        }
        
        fetch('/admin/orders/' + orderId + '/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                driver_id: driverId,
                vehicle_id: null
            })
        })
        .then(function(response) {
            // Check if response is OK
            if (!response.ok) {
                return response.json().then(function(data) {
                    throw new Error(data.message || 'Server error (HTTP ' + response.status + ')');
                });
            }
            return response.json();
        })
        .then(function(data) {
            console.log('✅ Assignment response:', data);
            if (data.success) {
                showToast('✅ ' + data.message, 'success');
                setTimeout(function() {
                    closeAssignModal();
                    location.reload();
                }, 1000);
            } else {
                showToast('❌ ' + data.message, 'error');
            }
        })
        .catch(function(error) {
            console.error('❌ Assignment error:', error);
            showToast('❌ Error assigning driver: ' + error.message, 'error');
        })
        .finally(function() {
            if (btn) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    }

    // ============================================================
    // TOGGLE BREAKDOWN
    // ============================================================
    
    function toggleBreakdown(orderId) {
        var row = document.getElementById('breakdownRow' + orderId);
        var icon = document.getElementById('toggleIcon' + orderId);
        var box = document.getElementById('breakdownBox' + orderId);
        
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            icon.className = 'ti ti-chevron-up';
        } else {
            row.style.display = 'none';
            icon.className = 'ti ti-chevron-down';
        }
    }

    // ============================================================
    // CLOSE MODALS
    // ============================================================
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePriceModal();
            closeAssignModal();
        }
    });
    
    document.addEventListener('click', function(e) {
        var priceModal = document.getElementById('priceModal');
        var assignModal = document.getElementById('assignModal');
        if (e.target === priceModal) closePriceModal();
        if (e.target === assignModal) closeAssignModal();
    });
</script>

@endsection