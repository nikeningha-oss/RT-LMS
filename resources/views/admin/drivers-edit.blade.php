@extends('layouts.dashboard')

@section('title', 'Edit Driver - Tracklane')
@section('page-title', 'Edit Driver')

@section('dashboard-content')

<style>
    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 0.5px solid #E2E8F0;
        border-radius: 6px;
        font-size: 13px;
        transition: border-color 0.2s;
        background: #FFFFFF;
    }
    .form-input:focus {
        outline: none;
        border-color: #0D9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #0F172A;
        margin-bottom: 4px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .btn-primary {
        background: #14B8A6;
        color: #FFFFFF;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-primary:hover {
        background: #0D9488;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    .btn-secondary {
        background: #F1F5F9;
        color: #475569;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-secondary:hover {
        background: #E2E8F0;
        color: #0F172A;
    }
    .back-link {
        color: #64748B;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: #0D9488;
    }
    .radio-group {
        display: flex;
        gap: 16px;
        padding-top: 4px;
    }
    .radio-group label {
        font-size: 13px;
        color: #0F172A;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .radio-group input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: #0D9488;
        cursor: pointer;
    }
    .alert-success {
        background: #D1FAE5;
        color: #065F46;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-left: 4px solid #10B981;
    }
    .alert-error {
        background: #FEE2E2;
        color: #991B1B;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-left: 4px solid #E24B4A;
    }
    .card {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 24px;
        max-width: 700px;
    }
    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .driver-avatar-lg {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #CCFBF1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: #0D9488;
        flex-shrink: 0;
    }
</style>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">
    
    <!-- Back Button -->
    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.drivers') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Drivers
        </a>
    </div>

    <!-- Edit Card -->
    <div class="card">
        <div class="card-title">
            <div class="driver-avatar-lg">
                {{ strtoupper(substr($driver->name ?? 'D', 0, 2)) }}
            </div>
            Edit Driver: <span style="color:#0D9488;">{{ $driver->name ?? 'Unknown' }}</span>
        </div>

        {{-- Display Success/Error Messages --}}
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ FIXED: Using route('admin.drivers.update', $driver->id) --}}
        <form method="POST" action="{{ route('admin.drivers.update', $driver->id) }}">
            @csrf
            @method('PUT')

            <!-- Driver Name -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user" style="color:#64748B;"></i> Full Name <span style="color:#E24B4A;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" 
                       class="form-input" required placeholder="Enter full name">
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope" style="color:#64748B;"></i> Email Address <span style="color:#E24B4A;">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $driver->email ?? '') }}" 
                       class="form-input" required placeholder="Enter email address">
                <small style="color:#94A3B8; font-size:11px; display:block; margin-top:4px;">
                    <i class="fas fa-info-circle"></i> This email is used for login
                </small>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-phone" style="color:#64748B;"></i> Phone Number
                </label>
                <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" 
                       class="form-input" placeholder="+237 690000000">
            </div>

            <!-- License Number -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-id-card" style="color:#64748B;"></i> License Number
                </label>
                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}" 
                       class="form-input" placeholder="LIC-2024-001">
                <small style="color:#94A3B8; font-size:11px; display:block; margin-top:4px;">
                    <i class="fas fa-info-circle"></i> Driver's license identification number
                </small>
            </div>

            <!-- Vehicle Assignment -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-car" style="color:#64748B;"></i> Assigned Vehicle
                </label>
                <select name="vehicle_id" class="form-input" style="appearance:auto;">
                    <option value="">-- No vehicle assigned --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ (old('vehicle_id', $driver->vehicle_id) == $vehicle->id) ? 'selected' : '' }}>
                            {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                        </option>
                    @endforeach
                </select>
                <small style="color:#94A3B8; font-size:11px; display:block; margin-top:4px;">
                    <i class="fas fa-info-circle"></i> Select a vehicle to assign to this driver
                </small>
            </div>

            <!-- Availability -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on" style="color:#64748B;"></i> Availability Status
                </label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="is_available" value="1" {{ old('is_available', $driver->is_available) ? 'checked' : '' }}> 
                        <i class="fas fa-circle" style="color:#10B981; font-size:10px;"></i> Available
                    </label>
                    <label>
                        <input type="radio" name="is_available" value="0" {{ !old('is_available', $driver->is_available) ? 'checked' : '' }}> 
                        <i class="fas fa-circle" style="color:#E24B4A; font-size:10px;"></i> Unavailable
                    </label>
                </div>
            </div>

            <!-- Divider -->
            <div style="border-top:1px solid #E2E8F0; margin:20px 0;"></div>

            <!-- Driver Stats -->
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px;">
                <div style="background:#F8FAFC; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:11px; color:#64748B;">Total Earned</div>
                    <div style="font-size:16px; font-weight:700; color:#10B981;">
                        {{ number_format($driver->total_earned ?? 0, 0, ',', ' ') }} F
                    </div>
                </div>
                <div style="background:#F8FAFC; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:11px; color:#64748B;">Available Balance</div>
                    <div style="font-size:16px; font-weight:700; color:#0D9488;">
                        {{ number_format($driver->available_balance ?? 0, 0, ',', ' ') }} F
                    </div>
                </div>
                <div style="background:#F8FAFC; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:11px; color:#64748B;">Total Withdrawn</div>
                    <div style="font-size:16px; font-weight:700; color:#3B82F6;">
                        {{ number_format($driver->total_withdrawn ?? 0, 0, ',', ' ') }} F
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Update Driver
                </button>
                <a href="{{ route('admin.drivers') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection