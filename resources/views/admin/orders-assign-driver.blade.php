@extends('layouts.dashboard')

@section('title', 'Assign Driver - Tracklane')
@section('page-title', 'Assign Driver to Order')

@section('dashboard-content')

<style>
    .order-summary-card {
        background: #F8FAFC;
        border-radius: 12px;
        padding: 16px 18px;
        border: 0.5px solid #E2E8F0;
        margin-bottom: 16px;
    }
    .order-summary-card .label {
        font-size: 11px;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
    }
    .order-summary-card .value {
        font-size: 14px;
        color: #0F172A;
        font-weight: 500;
    }
    .driver-card-select {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 10px;
        padding: 12px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .driver-card-select:hover {
        border-color: #14B8A6;
        background: #F0FDFA;
    }
    .driver-card-select.selected {
        border-color: #14B8A6;
        background: #F0FDFA;
        box-shadow: 0 0 0 2px rgba(20,184,166,0.15);
    }
    .driver-card-select .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #CCFBF1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: #0D9488;
        flex-shrink: 0;
    }
    .driver-card-select .info {
        flex: 1;
    }
    .driver-card-select .info .name {
        font-size: 14px;
        font-weight: 500;
        color: #0F172A;
    }
    .driver-card-select .info .details {
        font-size: 12px;
        color: #64748B;
    }
    .driver-card-select .status-badge {
        font-size: 10px;
        font-weight: 500;
        padding: 2px 10px;
        border-radius: 20px;
    }
    .driver-card-select .status-badge.available { background: #D1FAE5; color: #065F46; }
    .driver-card-select .status-badge.busy { background: #FEF3C7; color: #92400E; }
    .driver-card-select .status-badge.offline { background: #F1F5F9; color: #475569; }

    .vehicle-tag {
        display: inline-block;
        background: #F1F5F9;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        color: #475569;
        margin-top: 2px;
    }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width:900px; margin:0 auto;">

        <!-- HEADER -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <a href="{{ route('admin.orders') }}" style="color:#64748B; text-decoration:none; font-size:20px;">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <div style="font-size:18px; font-weight:600; color:#0F172A;">🚚 Assign Driver</div>
                <div style="font-size:13px; color:#64748B;">Order #{{ $order->order_number }}</div>
            </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="order-summary-card">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <div class="label"><i class="ti ti-user"></i> Customer</div>
                    <div class="value">{{ $order->customer->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label"><i class="ti ti-phone"></i> Phone</div>
                    <div class="value">{{ $order->customer->phone ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label"><i class="ti ti-map-pin"></i> Pickup</div>
                    <div class="value">{{ Str::limit($order->pickup_address, 35) }}</div>
                </div>
                <div>
                    <div class="label"><i class="ti ti-location"></i> Delivery</div>
                    <div class="value">{{ Str::limit($order->delivery_address, 35) }}</div>
                </div>
                <div>
                    <div class="label"><i class="ti ti-currency-franc"></i> Price</div>
                    <div class="value">{{ number_format($order->total_price, 0, ',', ' ') }} F</div>
                </div>
                <div>
                    <div class="label"><i class="ti ti-status"></i> Status</div>
                    <div class="value">
                        <span style="padding:2px 12px; border-radius:12px; font-size:12px; background:#FEF3C7; color:#92400E;">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ASSIGN FORM -->
        <form method="POST" action="{{ route('admin.orders.assign', $order->id) }}">
            @csrf

            <!-- AVAILABLE DRIVERS -->
            <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px; margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:6px;">
                    <span style="font-size:14px; font-weight:600; color:#0F172A;">
                        👨‍✈️ Select Driver
                    </span>
                    <span style="font-size:12px; color:#64748B;">
                        {{ isset($availableDrivers) ? $availableDrivers->count() : 0 }} drivers available
                    </span>
                </div>

                @if(isset($availableDrivers) && $availableDrivers->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($availableDrivers as $driver)
                        <label class="driver-card-select">
                            <input type="radio" name="driver_id" value="{{ $driver->id }}" style="display:none;" 
                                   onchange="selectDriver(this)">
                            <div style="display:flex; align-items:center; gap:12px; width:100%;">
                                <div class="avatar">{{ strtoupper(substr($driver->name, 0, 2)) }}</div>
                                <div class="info">
                                    <div class="name">{{ $driver->name }}</div>
                                    <div class="details">
                                        {{ $driver->email }}
                                        @if($driver->vehicle)
                                            <span class="vehicle-tag">
                                                <i class="ti ti-truck"></i> {{ $driver->vehicle->plate_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if($driver->is_available)
                                        <span class="status-badge available">🟢 Available</span>
                                    @else
                                        <span class="status-badge busy">🟡 Busy</span>
                                    @endif
                                </div>
                                <div style="font-size:12px; color:#64748B; text-align:right;">
                                    <i class="ti ti-route"></i> 
                                    {{ $driver->current_lat ? 'Online' : 'Offline' }}
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:30px; color:#94A3B8;">
                        <i class="ti ti-users" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                        <p style="font-size:14px;">No available drivers at the moment.</p>
                        <p style="font-size:12px;">Try again later or check driver availability.</p>
                    </div>
                @endif
            </div>

            <!-- SELECT VEHICLE (Optional) -->
            @if(isset($availableVehicles) && $availableVehicles->count() > 0)
            <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px; margin-bottom:16px;">
                <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">
                    🚗 Select Vehicle <span style="font-size:12px; font-weight:400; color:#94A3B8;">(Optional)</span>
                </div>
                <select name="vehicle_id" style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; background:#FFFFFF;">
                    <option value="">— No vehicle —</option>
                    @foreach($availableVehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->plate_number }} · {{ $vehicle->model }} ({{ $vehicle->type }})
                        </option>
                    @endforeach
                </select>
                <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                    <i class="ti ti-info-circle"></i> Assigning a vehicle is optional
                </div>
            </div>
            @endif

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-top:8px; flex-wrap:wrap;">
                <button type="submit" 
                        id="assignBtn"
                        style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:11px 40px; font-size:15px; font-weight:600; cursor:pointer; transition:all 0.2s;">
                    <i class="ti ti-user-plus me-2"></i> Assign Driver
                </button>
                <a href="{{ route('admin.orders') }}" 
                   style="background:#FFFFFF; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 28px; font-size:15px; text-decoration:none; transition:all 0.2s;">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</div>

<script>
    function selectDriver(radio) {
        document.querySelectorAll('.driver-card-select').forEach(function(el) {
            el.classList.remove('selected');
        });
        if (radio.checked) {
            radio.closest('.driver-card-select').classList.add('selected');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var drivers = document.querySelectorAll('input[name="driver_id"]');
        if (drivers.length === 1) {
            drivers[0].checked = true;
            drivers[0].closest('.driver-card-select').classList.add('selected');
        }
    });
</script>

@endsection