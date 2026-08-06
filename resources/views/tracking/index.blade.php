<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tracklane — Live Tracking</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <!-- ✅ MapTiler CSS - Only loads if key is present -->
    

    <style>
        :root {
            --navy: #0B1220;
            --teal: #0D9488;
            --teal-bright: #14B8A6;
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --border: #E2E8F0;
            --text: #0F172A;
            --muted: #64748B;
            --amber-bg: #FEF3C7;
            --amber-fg: #92400E;
            --emerald-bg: #D1FAE5;
            --emerald-fg: #065F46;
            --slate-bg: #F1F5F9;
            --slate-fg: #475569;
            --red-bg: #FEE2E2;
            --red-fg: #991B1B;
            --pickup-color: #D85A30;
            --dropoff-color: #2563EB;
            --driver-color: #10B981;
        }
        
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
            height: 100vh;
            height: 100dvh;
        }
        
        /* ============================================================
           TOP BAR
           ============================================================ */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--navy);
            color: #fff;
            padding: 10px 18px;
            z-index: 1000;
            position: relative;
            min-height: 49px;
        }
        
        .brand { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-weight: 600; 
            font-size: 14px; 
        }
        
        .brand .dot { 
            width: 8px; 
            height: 8px; 
            border-radius: 50%; 
            background: var(--teal-bright); 
        }
        
        .brand .order-id { 
            font-weight: 400; 
            color: #94A3B8; 
            font-size: 12px; 
            margin-left: 4px;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .role-badge {
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
            background: rgba(255,255,255,0.1);
            color: #94A3B8;
            border: 0.5px solid rgba(255,255,255,0.1);
        }
        
        .role-badge.customer { background: rgba(20,184,166,0.2); color: #14B8A6; border-color: rgba(20,184,166,0.3); }
        .role-badge.driver { background: rgba(59,130,246,0.2); color: #3B82F6; border-color: rgba(59,130,246,0.3); }
        .role-badge.admin { background: rgba(245,158,11,0.2); color: #F59E0B; border-color: rgba(245,158,11,0.3); }
        
        .back-btn {
            color: #94A3B8;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-btn:hover { color: #fff; }
        
        /* ============================================================
           LAYOUT
           ============================================================ */
        .layout { 
            display: grid; 
            grid-template-columns: 1fr 360px; 
            gap: 0; 
            height: calc(100vh - 49px);
            height: calc(100dvh - 49px);
            overflow: hidden;
        }
        
        #map { 
            width: 100%; 
            height: 100%; 
            background: #EEF2F6; 
            min-height: 200px;
        }
        
        .panel { 
            background: var(--surface); 
            border-left: 0.5px solid var(--border); 
            overflow-y: auto; 
            padding: 16px 18px; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
            height: 100%;
        }
        
        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 820px) { 
            .layout { 
                grid-template-columns: 1fr; 
                grid-template-rows: 45vh 55vh;
                height: calc(100dvh - 49px);
            }
            #map { height: 100%; min-height: 200px; width: 100%; }
            .panel { 
                border-left: none; 
                border-top: 0.5px solid var(--border); 
                height: 100%;
                padding: 14px;
            }
        }
        
        @media (max-width: 480px) { 
            .topbar { padding: 8px 12px; }
            .brand { font-size: 12px; }
            .brand .order-id { font-size: 10px; }
            .layout { grid-template-rows: 40vh 60vh; }
            .panel { padding: 10px; gap: 10px; }
        }
        
        /* ============================================================
           CARDS & COMPONENTS
           ============================================================ */
        .card { 
            border: 0.5px solid var(--border); 
            border-radius: 12px; 
            padding: 14px; 
            background: #fff;
        }
        
        .card-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }
        
        .row { display: flex; align-items: center; justify-content: space-between; }
        .label { font-size: 11px; color: var(--muted); margin-bottom: 2px; }
        .value { font-size: 13px; color: var(--text); font-weight: 500; }
        .value-sm { font-size: 12px; color: var(--text); }
        
        .badge { 
            font-size: 11px; 
            font-weight: 500; 
            padding: 3px 10px; 
            border-radius: 12px; 
            display: inline-block; 
        }
        .badge-pending { background: var(--slate-bg); color: var(--slate-fg); }
        .badge-assigned { background: #DBEAFE; color: #1E40AF; }
        .badge-picked_up { background: #E0E7FF; color: #3730A3; }
        .badge-in_transit { background: var(--amber-bg); color: var(--amber-fg); }
        .badge-delivered { background: var(--emerald-bg); color: var(--emerald-fg); }
        .badge-cancelled { background: var(--red-bg); color: var(--red-fg); }
        .badge-price_pending { background: var(--amber-bg); color: var(--amber-fg); }
        .badge-price_confirmed { background: var(--emerald-bg); color: var(--emerald-fg); }
        
        .driver-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: var(--bg);
            border-radius: 10px;
        }
        .driver-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #CCFBF1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            color: var(--teal);
            flex-shrink: 0;
        }
        .driver-info .name { font-weight: 600; font-size: 14px; color: var(--text); }
        .driver-info .detail { font-size: 12px; color: var(--muted); }
        
        .eta-box {
            background: var(--bg);
            border-radius: 10px;
            padding: 12px 16px;
            text-align: center;
        }
        .eta-box .big { font-size: 24px; font-weight: 700; color: var(--teal); }
        .eta-box .label { font-size: 11px; color: var(--muted); }
        
        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: #0F766E; }
        .btn-primary:disabled { background: #CBD5E1; cursor: not-allowed; }
        .btn-outline { background: #fff; border: 0.5px solid var(--border); color: var(--text); }
        .btn-outline:hover { background: var(--bg); }
        .btn-row { display: flex; gap: 8px; }
        .btn-row .btn { flex: 1; }
        
        .btn-small {
            padding: 5px 12px;
            font-size: 11px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-call { background: #10B981; color: #fff; }
        .btn-call:hover { background: #059669; }
        .btn-chat { background: #3B82F6; color: #fff; }
        .btn-chat:hover { background: #2563EB; }
        .btn-map { background: #8B5CF6; color: #fff; }
        .btn-map:hover { background: #7C3AED; }
        
        .timeline { display: flex; flex-direction: column; gap: 0; }
        .tl-step { display: flex; gap: 12px; position: relative; padding-bottom: 16px; }
        .tl-step:last-child { padding-bottom: 0; }
        .tl-dot { 
            width: 10px; 
            height: 10px; 
            border-radius: 50%; 
            background: var(--border); 
            margin-top: 3px; 
            flex-shrink: 0; 
            z-index: 1; 
        }
        .tl-step.done .tl-dot { background: var(--teal); }
        .tl-step:not(:last-child)::before { 
            content: ''; 
            position: absolute; 
            left: 4.5px; 
            top: 14px; 
            bottom: 0; 
            width: 1.5px; 
            background: var(--border); 
        }
        .tl-step.done:not(:last-child)::before { background: var(--teal); }
        .tl-text { font-size: 12px; }
        .tl-title { color: var(--text); font-weight: 500; }
        .tl-step:not(.done) .tl-title { color: var(--muted); font-weight: 400; }
        .tl-time { font-size: 11px; color: var(--muted); }
        
        .gps-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .gps-status.active { background: #D1FAE5; color: #065F46; }
        .gps-status.inactive { background: #FEF3C7; color: #92400E; }
        .gps-status .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .gps-status.active .dot {
            background: #10B981;
            animation: pulse-dot 1.5s infinite;
        }
        .gps-status.inactive .dot { background: #F59E0B; }
        @keyframes pulse-dot {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .conn {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--muted);
            padding: 6px 0;
        }
        .conn .pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.6);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
            70% { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        
        .no-order {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
        .no-order i { font-size: 48px; display: block; margin-bottom: 12px; color: #D7DEE6; }
        .no-order h4 { margin: 0; color: var(--text); }
        .no-order p { font-size: 13px; margin: 4px 0 12px; }
        
        .map-legend {
            position: absolute;
            bottom: 30px;
            left: 10px;
            background: rgba(255,255,255,0.95);
            padding: 10px 14px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 11px;
            border: 0.5px solid #E2E8F0;
        }
        .map-legend .item { display: flex; align-items: center; gap: 8px; }
        .map-legend .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        .map-legend .dot.pickup { background: #D85A30; }
        .map-legend .dot.dropoff { background: #2563EB; }
        .map-legend .dot.driver { 
            background: #10B981;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #10B981;
        }
        .map-legend .label { font-weight: 500; color: var(--text); }
        
        .maptiler-attribution {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255,255,255,0.8);
            padding: 2px 8px;
            font-size: 10px;
            color: #555;
            border-radius: 4px 0 0 0;
            z-index: 1000;
            pointer-events: none;
        }
        
        .toast-message {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            z-index: 99999;
            max-width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            animation: slideUp 0.3s ease;
        }
        .toast-message.success { background: #D1FAE5; color: #065F46; }
        .toast-message.error { background: #FEE2E2; color: #991B1B; }
        @keyframes slideUp {
            from { transform: translateX(-50%) translateY(20px); opacity: 0; }
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* ============================================================
           CUSTOM POPUP
           ============================================================ */
        .custom-popup {
            font-family: 'Inter', system-ui, sans-serif;
            padding: 4px 2px;
        }
        .custom-popup .title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .custom-popup .address {
            font-size: 12px;
            font-weight: 400;
            color: #475569;
            max-width: 200px;
            word-wrap: break-word;
        }
        .custom-popup.pickup .title { color: #D85A30; }
        .custom-popup.dropoff .title { color: #2563EB; }
        .custom-popup.driver .title { color: #10B981; }
        
        /* ============================================================
           ADMIN SPECIFIC
           ============================================================ */
        .delivery-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 8px;
            border-bottom: 0.5px solid var(--border);
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .delivery-list-item:hover { background: var(--bg); }
        .delivery-list-item.selected { background: #F0FDFA; border-color: var(--teal); }
        .delivery-list-item:last-child { border-bottom: none; }
    </style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="brand">
        <span class="dot"></span>
        Tracklane
        @if(isset($order) && $order)
            <span class="order-id">· {{ $order->order_number }}</span>
        @endif
        <a href="{{ route('dashboard') }}" class="back-btn" style="margin-left:12px;">
            <i class="ti ti-arrow-left" style="font-size:16px;"></i> Dashboard
        </a>
    </div>
    <div class="topbar-right">
        <span class="role-badge {{ Auth::user()->role }}">
            @if(Auth::user()->role === 'customer') 👤 Customer
            @elseif(Auth::user()->role === 'driver') 🚚 Driver
            @else 🛡️ Admin
            @endif
        </span>
    </div>
</div>

<!-- LAYOUT -->
<div class="layout">
    <div id="map"></div>
    <div class="panel" id="panel">
        @if(!isset($order) || !$order)
            <div class="no-order">
                <i class="ti ti-truck"></i>
                <h4>No Active Delivery</h4>
                <p>You don't have any active deliveries to track.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top:8px; display:inline-flex;">
                    <i class="ti ti-arrow-left"></i> Go to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    (function() {
        'use strict';

        // ============================================================
        // CONFIGURATION
        // ============================================================
        
        var CONFIG = {
            GPS_UPDATE_INTERVAL: 2000,
            TRACKING_POLL_INTERVAL: 3000,
            MAX_ACCURACY: 2000,
            MAP_PADDING: 50
        };

        // ============================================================
        // DATA FROM LARAVEL
        // ============================================================
        
        var trackingData = @json($trackingData ?? null);
        var allOrdersData = @json($allOrders ?? []);
        var userRole = @json(Auth::user()->role);
        var orderId = @json(isset($order) ? $order->id : null);
        var orderNumber = @json(isset($order) ? $order->order_number : null);
        var driverId = @json(isset($order) && $order->driver ? $order->driver->id : null);
        var dashboardUrl = @json(route('dashboard'));
        var maptilerKey = @json(env('MAPTILER_KEY'));

        // ============================================================
        // STATE
        // ============================================================
        
        var map = null;
        var orderData = trackingData;
        var allOrders = allOrdersData;
        var activeOrder = orderData;
        var pickupMarker = null;
        var dropoffMarker = null;
        var driverMarker = null;
        var driverMarkerMoving = null;
        var routeLine = null;
        var gpsWatchId = null;
        var locationPollInterval = null;
        var isTrackingActive = false;
        var driverCurrentLocation = null;
        var hasRealGpsData = false;
        var isUpdatingStatus = false;

        // ============================================================
        // HELPERS
        // ============================================================
        
        function initials(name) {
            if (!name) return 'N/A';
            var parts = name.split(' ');
            var result = '';
            for (var i = 0; i < parts.length && i < 2; i++) {
                if (parts[i].length > 0) result += parts[i][0].toUpperCase();
            }
            return result || 'N/A';
        }

        function formatTime(date) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        function statusBadge(status) {
            var map = {
                'pending': 'badge-pending',
                'assigned': 'badge-assigned',
                'picked_up': 'badge-picked_up',
                'in_transit': 'badge-in_transit',
                'delivered': 'badge-delivered',
                'cancelled': 'badge-cancelled',
                'price_pending': 'badge-price_pending',
                'price_confirmed': 'badge-price_confirmed'
            };
            var label = status.replace('_', ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            return '<span class="badge ' + (map[status] || 'badge-pending') + '">' + label + '</span>';
        }

        function timelineHtml(steps) {
            var html = '<div class="timeline">';
            for (var i = 0; i < steps.length; i++) {
                var s = steps[i];
                html += '<div class="tl-step ' + (s.done ? 'done' : '') + '">';
                html += '<div class="tl-dot"></div>';
                html += '<div class="tl-text">';
                html += '<div class="tl-title">' + s.title + '</div>';
                if (s.time) html += '<div class="tl-time">' + s.time + '</div>';
                html += '</div></div>';
            }
            html += '</div>';
            return html;
        }

        function nextActionLabel(status) {
            var map = {
                'pending': 'Mark Picked Up',
                'assigned': 'Mark Picked Up',
                'price_confirmed': 'Mark Picked Up',
                'picked_up': 'Start Delivery',
                'in_transit': 'Mark Delivered',
                'delivered': 'Delivered'
            };
            return map[status] || 'Update Status';
        }

        function showToast(message, type) {
            var existing = document.querySelector('.toast-message');
            if (existing) existing.remove();
            var toast = document.createElement('div');
            toast.className = 'toast-message ' + type;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(function() { toast.remove(); }, 300);
            }, 4000);
        }

        // ============================================================
        // DECODE POLYLINE & ROUTE
        // ============================================================
        
        function decodePolyline(str, precision) {
            var index = 0, lat = 0, lng = 0, coordinates = [];
            var shift = 0, result = 0, byte = null;
            var factor = Math.pow(10, precision || 5);
            while (index < str.length) {
                byte = null; shift = 0; result = 0;
                do {
                    byte = str.charCodeAt(index++) - 63;
                    result |= (byte & 0x1f) << shift;
                    shift += 5;
                } while (byte >= 0x20);
                var latChange = ((result & 1) ? ~(result >> 1) : (result >> 1));
                shift = 0; result = 0;
                do {
                    byte = str.charCodeAt(index++) - 63;
                    result |= (byte & 0x1f) << shift;
                    shift += 5;
                } while (byte >= 0x20);
                var lngChange = ((result & 1) ? ~(result >> 1) : (result >> 1));
                lat += latChange;
                lng += lngChange;
                coordinates.push([lat / factor, lng / factor]);
            }
            return coordinates;
        }

        function fetchOSRMRoute(pickupLat, pickupLng, dropoffLat, dropoffLng) {
            var url = 'https://router.project-osrm.org/route/v1/driving/' + 
                pickupLng + ',' + pickupLat + ';' + dropoffLng + ',' + dropoffLat + 
                '?overview=full&geometries=polyline';
            return fetch(url)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        var points = decodePolyline(data.routes[0].geometry);
                        return {
                            points: points,
                            distance: data.routes[0].distance / 1000,
                            duration: data.routes[0].duration / 60
                        };
                    }
                    return null;
                })
                .catch(function() { return null; });
        }

        // ============================================================
        // MAP SETUP - ✅ WITH MAPTILER SUPPORT
        // ============================================================
        
       function initMap() {
    map = L.map('map', {
        center: [4.0511, 9.7679],
        zoom: 13
    });
    
    // ✅ Use MapTiler Streets v2 if key is available, otherwise fallback to OpenStreetMap
    var tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var attribution = '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
    
    if (maptilerKey && maptilerKey !== '') {
        // ✅ CHANGED: streets → streets-v2
        tileUrl = 'https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=' + maptilerKey;
        attribution = '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>';
    }
    
    L.tileLayer(tileUrl, {
        maxZoom: 20,
        attribution: attribution
    }).addTo(map);

    // Add MapTiler attribution if using MapTiler
    if (maptilerKey && maptilerKey !== '') {
        var attributionDiv = document.createElement('div');
        attributionDiv.className = 'maptiler-attribution';
        attributionDiv.innerHTML = '© <a href="https://www.maptiler.com" target="_blank" style="color:#555;">MapTiler</a> | © <a href="https://www.openstreetmap.org/copyright" target="_blank" style="color:#555;">OpenStreetMap</a>';
        document.getElementById('map').appendChild(attributionDiv);
    }

    addMapLegend();
    
         // Handle resize
          var resizeTimeout;
          window.addEventListener('resize', function() {
           clearTimeout(resizeTimeout);
          resizeTimeout = setTimeout(function() { if (map) map.invalidateSize(); }, 300);
          });
          document.addEventListener('visibilitychange', function() {
          if (!document.hidden && map) setTimeout(function() { map.invalidateSize(); }, 400);
             });
        }

        function addMapLegend() {
            var html = '<div class="map-legend">' +
                '<div class="item"><span class="dot pickup"></span><span class="label">📍 Pickup</span></div>' +
                '<div class="item"><span class="dot dropoff"></span><span class="label">🏁 Destination</span></div>' +
                '<div class="item"><span class="dot driver"></span><span class="label">🚚 Driver</span></div>' +
                '</div>';
            setTimeout(function() {
                var container = document.getElementById('map');
                if (container) {
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    container.appendChild(wrapper.firstElementChild);
                }
            }, 500);
        }

        // ============================================================
        // ICONS
        // ============================================================
        
        var pickupIcon = L.divIcon({
            className: '',
            html: '<div style="width:20px;height:20px;border-radius:50%;background:#D85A30;border:2px solid #fff;box-shadow:0 2px 8px rgba(216,90,48,0.4);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:10px;color:#fff;">P</div>',
            iconSize: [20,20],
            iconAnchor: [10,10],
            popupAnchor: [0, -12]
        });

        var dropoffIcon = L.divIcon({
            className: '',
            html: '<div style="width:20px;height:20px;border-radius:50%;background:#2563EB;border:2px solid #fff;box-shadow:0 2px 8px rgba(37,99,235,0.4);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:10px;color:#fff;">D</div>',
            iconSize: [20,20],
            iconAnchor: [10,10],
            popupAnchor: [0, -12]
        });

        var driverIcon = L.divIcon({
            className: '',
            html: '<div style="width:28px;height:28px;border-radius:50%;background:#10B981;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 4px rgba(16,185,129,0.25);border:2px solid #fff;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 17h2l2-5h9l3 4h2v3"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/></svg></div>',
            iconSize: [28,28],
            iconAnchor: [14,14],
            popupAnchor: [0, -14]
        });

        var driverMovingIcon = L.divIcon({
            className: '',
            html: '<div style="width:36px;height:36px;border-radius:50%;background:#10B981;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 6px rgba(16,185,129,0.2),0 0 0 10px rgba(16,185,129,0.08);border:3px solid #fff;animation:pulse-driver 1s infinite;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 17h2l2-5h9l3 4h2v3"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/></svg></div>',
            iconSize: [36,36],
            iconAnchor: [18,18],
            popupAnchor: [0, -18]
        });

        var style = document.createElement('style');
        style.textContent = '@keyframes pulse-driver { 0% { transform: scale(1); } 50% { transform: scale(1.08); } 100% { transform: scale(1); } }';
        document.head.appendChild(style);

        // ============================================================
        // MAP RENDER
        // ============================================================
        
        function clearMapLayers() {
            if (pickupMarker) { map.removeLayer(pickupMarker); pickupMarker = null; }
            if (dropoffMarker) { map.removeLayer(dropoffMarker); dropoffMarker = null; }
            if (driverMarker) { map.removeLayer(driverMarker); driverMarker = null; }
            if (driverMarkerMoving) { map.removeLayer(driverMarkerMoving); driverMarkerMoving = null; }
            if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
        }

        function renderMap(data) {
            if (!data) return;
            clearMapLayers();

            var pickupLat = data.pickup.lat;
            var pickupLng = data.pickup.lng;
            var dropoffLat = data.dropoff.lat;
            var dropoffLng = data.dropoff.lng;
            var bounds = [];

            pickupMarker = L.marker([pickupLat, pickupLng], { icon: pickupIcon })
                .addTo(map)
                .bindPopup('<div class="custom-popup pickup"><div class="title">📍 Pickup</div><div class="address">' + data.pickup.label + '</div></div>');
            bounds.push([pickupLat, pickupLng]);

            dropoffMarker = L.marker([dropoffLat, dropoffLng], { icon: dropoffIcon })
                .addTo(map)
                .bindPopup('<div class="custom-popup dropoff"><div class="title">🏁 Destination</div><div class="address">' + data.dropoff.label + '</div></div>');
            bounds.push([dropoffLat, dropoffLng]);

            fetchOSRMRoute(pickupLat, pickupLng, dropoffLat, dropoffLng)
                .then(function(route) {
                    if (route && route.points.length > 0) {
                        var latlngs = route.points.map(function(p) { return [p[0], p[1]]; });
                        routeLine = L.polyline(latlngs, { color: '#14B8A6', weight: 4, opacity: 0.8 }).addTo(map);
                        if (route.duration) {
                            var eta = document.getElementById('etaText');
                            if (eta) eta.textContent = Math.max(1, Math.round(route.duration)) + ' min';
                        }
                        if (route.distance) {
                            var dist = document.getElementById('distanceText');
                            if (dist) dist.textContent = route.distance.toFixed(1) + ' km';
                        }
                        bounds = L.latLngBounds(latlngs);
                    } else {
                        routeLine = L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], {
                            color: '#14B8A6', weight: 3, dashArray: '6 6', opacity: 0.7
                        }).addTo(map);
                    }
                });

            // Driver marker - only show if we have location data
            if (data.driver && data.status !== 'delivered' && data.status !== 'cancelled') {
                var name = data.driver.name || 'Driver';
                var phone = data.driver.phone || 'N/A';
                var useMoving = hasRealGpsData && driverCurrentLocation && driverCurrentLocation.lat && driverCurrentLocation.lng;
                
                if (driverCurrentLocation && driverCurrentLocation.lat && driverCurrentLocation.lng) {
                    var icon = useMoving ? driverMovingIcon : driverIcon;
                    driverMarkerMoving = L.marker([driverCurrentLocation.lat, driverCurrentLocation.lng], { icon: icon })
                        .addTo(map)
                        .bindPopup('<div class="custom-popup driver"><div class="title">🚚 ' + name + '</div><div class="address">📞 ' + phone + '</div></div>');
                    bounds.push([driverCurrentLocation.lat, driverCurrentLocation.lng]);
                } else {
                    // No location yet - show at pickup with waiting message
                    var pos = data.pickup;
                    driverMarker = L.marker([pos.lat, pos.lng], { icon: driverIcon })
                        .addTo(map)
                        .bindPopup('<div class="custom-popup driver"><div class="title">🚚 ' + name + '</div><div class="address">📞 ' + phone + '<br>⏳ Waiting for GPS...</div></div>');
                    bounds.push([pos.lat, pos.lng]);
                }
            }

            setTimeout(function() {
                if (bounds.length > 0 && map) {
                    map.fitBounds(bounds, { padding: [CONFIG.MAP_PADDING, CONFIG.MAP_PADDING] });
                }
            }, 300);
        }

// ============================================================
// GPS TRACKING (Driver) - HYBRID APPROACH
// ============================================================

var lastSentLocation = null;
var MIN_DISTANCE_CHANGE = 10; // meters
var locationSendInterval = null;

function startGpsTracking() {
    if (!navigator.geolocation) {
        console.log('❌ GPS not supported');
        updateGpsStatus(false, '❌ Not supported');
        return;
    }
    if (isTrackingActive) {
        console.log('📍 GPS already active');
        return;
    }
    console.log('📍 Starting GPS tracking...');
    isTrackingActive = true;
    updateGpsStatus(true, '🔍 Searching...');

    // Clear any existing watch
    if (gpsWatchId) {
        navigator.geolocation.clearWatch(gpsWatchId);
        gpsWatchId = null;
    }

    // ============================================================
    // 1. GPS WATCH - Gets location from hardware
    // ============================================================
    gpsWatchId = navigator.geolocation.watchPosition(
        // ----- SUCCESS -----
        function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            var speed = pos.coords.speed || 0;
            var accuracy = pos.coords.accuracy;

            console.log('📍 GPS: ' + lat + ', ' + lng + ' (accuracy: ' + accuracy + 'm)');

            if (accuracy < CONFIG.MAX_ACCURACY) {
                hasRealGpsData = true;
                driverCurrentLocation = { lat: lat, lng: lng };
                
                // ✅ Update marker immediately
                updateDriverMarker(lat, lng);
                updateLastUpdateTime();
                updateGpsStatus(true, '✅ Live');
                
                // ✅ Check if moved significantly - send immediately if so
                if (lastSentLocation) {
                    var distance = calculateDistance(
                        lastSentLocation.lat, lastSentLocation.lng,
                        lat, lng
                    );
                    if (distance > MIN_DISTANCE_CHANGE) {
                        console.log('📍 Moved ' + Math.round(distance) + 'm — sending immediately');
                        sendLocation(lat, lng, speed);
                        lastSentLocation = { lat: lat, lng: lng };
                    }
                } else {
                    // First location — send immediately
                    lastSentLocation = { lat: lat, lng: lng };
                    sendLocation(lat, lng, speed);
                }
            } else {
                console.log('📍 Low accuracy: ' + accuracy + 'm (need < ' + CONFIG.MAX_ACCURACY + 'm)');
                updateGpsStatus(false, '📡 Low accuracy');
            }
        },
        // ----- ERROR -----
        function(err) {
            console.log('⚠️ GPS Error Code:', err.code);
            console.log('⚠️ GPS Error Message:', err.message);
            
            if (err.code === 3) {
                console.log('⏳ Timeout - retrying in 5 seconds...');
                updateGpsStatus(false, '⏳ Retrying...');
                if (gpsWatchId) {
                    navigator.geolocation.clearWatch(gpsWatchId);
                    gpsWatchId = null;
                }
                isTrackingActive = false;
                setTimeout(function() {
                    startGpsTracking();
                }, 5000);
            } else {
                updateGpsStatus(false, '❌ ' + err.message);
            }
        },
        // ----- OPTIONS -----
        {
            enableHighAccuracy: true,
            timeout: 30000,        // ✅ 30 seconds
            maximumAge: 500        // ✅ 0.5 seconds
        }
    );

    // ============================================================
    // 2. SEND LOCATION EVERY 2 SECONDS (Even if same location)
    // ============================================================
    if (locationSendInterval) clearInterval(locationSendInterval);
    locationSendInterval = setInterval(function() {
        if (hasRealGpsData && driverCurrentLocation) {
            // ✅ Send every 2 seconds — keeps customer updated
            sendLocation(
                driverCurrentLocation.lat,
                driverCurrentLocation.lng,
                0
            );
            console.log('📤 Sent location (2s interval)');
        } else {
            console.log('⏳ Waiting for GPS lock...');
        }
    }, CONFIG.GPS_UPDATE_INTERVAL); // 2000ms
}

// ============================================================
// SEND LOCATION TO SERVER
// ============================================================
function sendLocation(lat, lng, speed) {
    var token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.warn('⚠️ No CSRF token found');
        return;
    }
    
    fetch('/tracking/' + orderId + '/location', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token.getAttribute('content')
        },
        body: JSON.stringify({ 
            latitude: lat, 
            longitude: lng, 
            speed: speed || 0, 
            heading: 0 
        })
    })
    .then(function(r) { 
        if (!r.ok) {
            return r.json().then(function(data) {
                throw new Error(data.message || 'Server error');
            });
        }
        return r.json(); 
    })
    .then(function(data) {
        if (data.success) console.log('✅ Location sent to server');
        else console.log('⚠️ Server rejected location:', data.message);
    })
    .catch(function(e) { 
        console.log('⚠️ Failed to send location:', e.message); 
    });
}

// ============================================================
// UPDATE DRIVER MARKER ON MAP
// ============================================================
function updateDriverMarker(lat, lng) {
    if (driverMarker) { 
        map.removeLayer(driverMarker); 
        driverMarker = null; 
    }
    if (driverMarkerMoving) { 
        map.removeLayer(driverMarkerMoving); 
        driverMarkerMoving = null; 
    }
    
    var name = activeOrder && activeOrder.driver ? activeOrder.driver.name : 'Driver';
    var phone = activeOrder && activeOrder.driver ? activeOrder.driver.phone : 'N/A';
    var icon = hasRealGpsData ? driverMovingIcon : driverIcon;
    
    driverMarkerMoving = L.marker([lat, lng], { icon: icon })
        .addTo(map)
        .bindPopup('<div class="custom-popup driver"><div class="title">🚚 ' + name + '</div><div class="address">📞 ' + phone + '</div></div>');
    
    if (map) {
        map.panTo([lat, lng]);
    }
    
    console.log('🗺️ Marker updated to: ' + lat + ', ' + lng);
}

// ============================================================
// UPDATE GPS STATUS
// ============================================================
function updateGpsStatus(active, message) {
    var el = document.getElementById('gpsStatus');
    if (el) {
        el.className = 'gps-status ' + (active ? 'active' : 'inactive');
        el.innerHTML = '<span class="dot"></span> ' + (message || (active ? 'Live' : 'Offline'));
    }
}

// ============================================================
// UPDATE LAST UPDATE TIME
// ============================================================
function updateLastUpdateTime() {
    var el = document.getElementById('lastUpdateTime');
    if (el) el.textContent = 'Updated: ' + formatTime(new Date());
}

// ============================================================
// CALCULATE DISTANCE (Haversine formula)
// ============================================================
function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000; // Earth radius in meters
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

        // ============================================================
        // CUSTOMER POLLING
        // ============================================================
        
        function startPollingDriverLocation(driverId) {
            if (locationPollInterval) clearInterval(locationPollInterval);
            console.log('📍 Polling driver location...');
            locationPollInterval = setInterval(function() {
                fetchDriverLocation(driverId);
            }, CONFIG.TRACKING_POLL_INTERVAL);
            fetchDriverLocation(driverId);
        }

        function fetchDriverLocation(driverId) {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (!token) return;
            fetch('/tracking/driver-location/' + driverId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { 
                if (!r.ok) {
                    return r.json().then(function(data) {
                        throw new Error(data.message || 'Server error');
                    });
                }
                return r.json(); 
            })
            .then(function(data) {
                if (data.success && data.latitude && data.longitude) {
                    hasRealGpsData = true;
                    driverCurrentLocation = { lat: data.latitude, lng: data.longitude };
                    updateDriverMarker(data.latitude, data.longitude);
                    updateLastUpdateTime();
                    updateGpsStatus(true, 'Live');
                } else {
                    console.log('⚠️ No location data');
                    updateGpsStatus(false, 'Waiting...');
                }
            })
            .catch(function(e) {
                console.log('⚠️ Failed to fetch location:', e.message);
                updateGpsStatus(false, 'Waiting...');
            });
        }

        // ============================================================
        // STATUS UPDATE
        // ============================================================
        
        function updateOrderStatus(orderId, newStatus) {
            if (isUpdatingStatus) return;
            isUpdatingStatus = true;

            var btn = document.getElementById('advanceBtn');
            if (btn) {
                btn.innerHTML = '<span class="spinner"></span> Updating...';
                btn.disabled = true;
            }

            var token = document.querySelector('meta[name="csrf-token"]');
            fetch('/driver/orders/' + orderId + '/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    if (activeOrder) {
                        activeOrder.status = newStatus;
                        var logMap = { 'assigned': 1, 'picked_up': 2, 'in_transit': 3, 'delivered': 4 };
                        if (logMap[newStatus] !== undefined && activeOrder.statusLog) {
                            activeOrder.statusLog[logMap[newStatus]].done = true;
                            activeOrder.statusLog[logMap[newStatus]].time = formatTime(new Date());
                        }
                    }
                    renderPanel();
                    showToast('✅ Status updated to ' + newStatus.replace('_', ' '), 'success');
                } else {
                    showToast('❌ ' + (data.message || 'Update failed'), 'error');
                }
            })
            .catch(function(e) {
                showToast('❌ Error updating status', 'error');
            })
            .finally(function() {
                isUpdatingStatus = false;
                if (btn) {
                    var next = activeOrder ? nextActionLabel(activeOrder.status) : 'Update';
                    btn.innerHTML = next;
                    btn.disabled = false;
                }
            });
        }

        function advanceStatus(data) {
            if (!data || !data.id) return;
            var statuses = ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered'];
            var idx = statuses.indexOf(data.status);
            if (idx < statuses.length - 1) {
                updateOrderStatus(orderId, statuses[idx + 1]);
            } else {
                showToast('✅ Order already delivered!', 'success');
            }
        }

        // ============================================================
        // CALL & CHAT
        // ============================================================
        
        function callCustomer(phone, name) {
            if (phone && phone !== 'N/A') window.location.href = 'tel:' + phone.replace(/[^0-9+]/g, '');
            else alert('📞 ' + name + '\'s phone not available');
        }

        function chatCustomer(phone, name, order) {
            if (phone && phone !== 'N/A') {
                var clean = phone.replace(/[^0-9+]/g, '');
                var msg = 'Hello ' + name + ', I am delivering your order ' + order + '. I will be there soon!';
                window.open('https://wa.me/' + clean + '?text=' + encodeURIComponent(msg), '_blank');
            } else {
                alert('💬 ' + name + '\'s phone not available');
            }
        }

        function callDriver(phone, name) {
            if (phone && phone !== 'N/A') window.location.href = 'tel:' + phone.replace(/[^0-9+]/g, '');
            else alert('📞 ' + name + '\'s phone not available');
        }

        function chatDriver(phone, name, order) {
            if (phone && phone !== 'N/A') {
                var clean = phone.replace(/[^0-9+]/g, '');
                var msg = 'Hello ' + name + ', I am tracking order ' + order + '. Can you provide an update?';
                window.open('https://wa.me/' + clean + '?text=' + encodeURIComponent(msg), '_blank');
            } else {
                alert('💬 ' + name + '\'s phone not available');
            }
        }

        function openMap(address) {
            if (address) window.open('https://www.google.com/maps?q=' + encodeURIComponent(address), '_blank');
        }

        // ============================================================
        // PANEL RENDERERS - ROLE SPECIFIC
        // ============================================================
        
        function renderCustomerPanel(data) {
            var panel = document.getElementById('panel');
            var html = '';

            html += '<div class="card">';
            html += '<div class="row"><span style="font-weight:600;font-size:15px;">Order ' + data.id + '</span>' + statusBadge(data.status) + '</div>';
            html += '</div>';

            if (data.driver) {
                var dName = data.driver.name || 'Driver';
                var dPhone = data.driver.phone || 'N/A';
                html += '<div class="card">';
                html += '<div class="card-title">👨‍✈️ Your Driver</div>';
                html += '<div class="driver-card">';
                html += '<div class="driver-avatar">' + initials(dName) + '</div>';
                html += '<div class="driver-info"><div class="name">' + dName + '</div>';
                html += '<div class="detail">' + (data.driver.vehicle || 'Vehicle') + '</div></div>';
                html += '</div>';
                html += '<div style="display:flex;gap:6px;margin-top:8px;">';
                html += '<button class="btn-small btn-call" onclick="window.callDriver(\'' + dPhone + '\', \'' + dName + '\')"><i class="ti ti-phone"></i> Call</button>';
                html += '<button class="btn-small btn-chat" onclick="window.chatDriver(\'' + dPhone + '\', \'' + dName + '\', \'' + data.id + '\')"><i class="ti ti-message-circle"></i> Chat</button>';
                html += '</div></div>';
            } else {
                html += '<div class="card"><div class="label">Looking for a nearby driver…</div></div>';
            }

            html += '<div class="card">';
            html += '<div class="eta-box">';
            html += '<div class="label">Estimated Arrival</div>';
            html += '<div class="big" id="etaText">' + (data.status === 'delivered' ? '✅ Delivered' : 'Calculating...') + '</div>';
            html += '<div style="font-size:12px;color:var(--muted);margin-top:4px;" id="speedText">Speed: -- km/h</div>';
            html += '</div></div>';

            html += '<div class="card">';
            html += '<div class="label">📍 Pickup</div>';
            html += '<div class="value-sm" style="margin-bottom:8px;">' + data.pickup.label + '</div>';
            html += '<div class="label">🏁 Destination</div>';
            html += '<div class="value-sm">' + data.dropoff.label + '</div></div>';

            html += '<div class="card">';
            html += '<div class="row"><span class="label">Distance</span><span class="value" id="distanceText">Calculating...</span></div></div>';

            html += '<div class="card"><div class="card-title">📋 Progress</div>' + timelineHtml(data.statusLog) + '</div>';

            html += '<div class="conn"><span class="pulse"></span> Live tracking · <span id="lastUpdateTime">updating...</span></div>';
            html += '<div id="gpsStatus" class="gps-status active"><span class="dot"></span> Live</div>';

            panel.innerHTML = html;
        }

        function renderDriverPanel(data) {
            var panel = document.getElementById('panel');
            var html = '';

            html += '<div class="card">';
            html += '<div class="row"><span style="font-weight:600;font-size:15px;">Order ' + data.id + '</span>' + statusBadge(data.status) + '</div>';
            html += '</div>';

            var cName = '{{ isset($order) && $order->customer ? addslashes($order->customer->name) : "Customer" }}';
            var cPhone = '{{ isset($order) && $order->customer ? addslashes($order->customer->phone ?? "N/A") : "N/A" }}';
            html += '<div class="card">';
            html += '<div class="card-title">👤 Customer</div>';
            html += '<div style="display:flex;align-items:center;gap:10px;padding:8px 0;">';
            html += '<div style="width:36px;height:36px;border-radius:50%;background:#DBEAFE;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#1E40AF;">' + initials(cName) + '</div>';
            html += '<div><div style="font-weight:500;font-size:13px;">' + cName + '</div>';
            html += '<div style="font-size:12px;color:var(--muted);">📞 ' + cPhone + '</div></div>';
            html += '</div>';
            html += '<div style="display:flex;gap:6px;margin-top:4px;">';
            html += '<button class="btn-small btn-call" onclick="window.callCustomer(\'' + cPhone + '\', \'' + cName + '\')"><i class="ti ti-phone"></i> Call</button>';
            html += '<button class="btn-small btn-chat" onclick="window.chatCustomer(\'' + cPhone + '\', \'' + cName + '\', \'' + data.id + '\')"><i class="ti ti-message-circle"></i> Chat</button>';
            html += '</div></div>';

            html += '<div class="card">';
            html += '<div class="label">📍 Pickup</div>';
            html += '<div class="value-sm" style="margin-bottom:8px;">' + data.pickup.label + '</div>';
            html += '<div class="label">🏁 Destination</div>';
            html += '<div class="value-sm" style="margin-bottom:12px;">' + data.dropoff.label + '</div>';
            html += '<div class="btn-row">';
            html += '<button class="btn btn-outline" onclick="window.openMap(\'' + data.pickup.label + '\')" style="flex:1;"><i class="ti ti-map"></i> Navigate</button>';
            html += '<button class="btn btn-primary" id="advanceBtn" ' + (data.status === 'delivered' ? 'disabled' : '') + ' style="flex:1;">';
            html += nextActionLabel(data.status) + '</button>';
            html += '</div></div>';

            html += '<div class="card"><div class="card-title">📋 Progress</div>' + timelineHtml(data.statusLog) + '</div>';

            html += '<div class="conn"><span class="pulse"></span> Sharing location · <span id="lastUpdateTime">updating...</span></div>';
            html += '<div id="gpsStatus" class="gps-status active"><span class="dot"></span> GPS Active</div>';

            panel.innerHTML = html;

            var btn = document.getElementById('advanceBtn');
            if (btn) btn.addEventListener('click', function() { advanceStatus(data); });
        }

        function renderAdminPanel(data) {
            var panel = document.getElementById('panel');
            var html = '';

            html += '<div class="card">';
            html += '<div class="row"><span style="font-weight:600;font-size:15px;">Order ' + data.id + '</span>' + statusBadge(data.status) + '</div>';
            html += '</div>';

            html += '<div class="card">';
            html += '<div class="row"><span class="label">Customer</span><span class="value">' + (data.customer ? data.customer.name : 'N/A') + '</span></div>';
            html += '<div class="row" style="margin-top:4px;"><span class="label">Driver</span><span class="value">' + (data.driver ? data.driver.name : '— Unassigned —') + '</span></div>';
            html += '<div class="row" style="margin-top:4px;"><span class="label">Distance</span><span class="value" id="distanceText">Calculating...</span></div>';
            html += '</div>';

            html += '<div class="card">';
            html += '<div class="label">📍 Pickup</div>';
            html += '<div class="value-sm" style="margin-bottom:6px;">' + data.pickup.label + '</div>';
            html += '<div class="label">🏁 Destination</div>';
            html += '<div class="value-sm">' + data.dropoff.label + '</div></div>';

            html += '<div class="card"><div class="card-title">📋 Progress</div>' + timelineHtml(data.statusLog) + '</div>';

            html += '<div class="conn"><span class="pulse"></span> Live · <span id="lastUpdateTime">updating...</span></div>';
            html += '<div id="gpsStatus" class="gps-status active"><span class="dot"></span> Tracking</div>';

            if (!data.driver && data.status !== 'delivered' && data.status !== 'cancelled') {
                html += '<a href="{{ route("admin.orders.assign", isset($order) ? $order->id : 0) }}" class="btn btn-primary" style="width:100%;"><i class="ti ti-user-plus"></i> Assign Driver</a>';
            }

            panel.innerHTML = html;
        }

        // ============================================================
        // MAIN RENDER
        // ============================================================
        
        function renderPanel() {
            if (!activeOrder) {
                var panel = document.getElementById('panel');
                if (panel) {
                    panel.innerHTML = '<div class="no-order">' +
                        '<i class="ti ti-truck"></i>' +
                        '<h4>No Active Delivery</h4>' +
                        '<p>You don\'t have any active deliveries to track.</p>' +
                        '<a href="' + dashboardUrl + '" class="btn btn-primary" style="margin-top:8px;display:inline-flex;"><i class="ti ti-arrow-left"></i> Dashboard</a></div>';
                }
                return;
            }

            if (locationPollInterval) {
                clearInterval(locationPollInterval);
                locationPollInterval = null;
            }

            renderMap(activeOrder);

            if (userRole === 'customer') {
                renderCustomerPanel(activeOrder);
                if (driverId) startPollingDriverLocation(driverId);
            } else if (userRole === 'driver') {
                renderDriverPanel(activeOrder);
                startGpsTracking();
            } else if (userRole === 'admin') {
                renderAdminPanel(activeOrder);
                if (driverId) startPollingDriverLocation(driverId);
            }
        }

        // ============================================================
        // INIT
        // ============================================================
        
        function init() {
            initMap();

            if (orderData) {
                orderData.progress = orderData.progress || 0.4;
                renderPanel();
            } else {
                var panel = document.getElementById('panel');
                if (panel) {
                    panel.innerHTML = '<div class="no-order">' +
                        '<i class="ti ti-truck"></i>' +
                        '<h4>No Active Delivery</h4>' +
                        '<p>You don\'t have any active deliveries to track.</p>' +
                        '<a href="' + dashboardUrl + '" class="btn btn-primary" style="margin-top:8px;display:inline-flex;"><i class="ti ti-arrow-left"></i> Dashboard</a></div>';
                }
            }

            setTimeout(function() { if (map) map.invalidateSize(); }, 500);
            if (window.screen && window.screen.orientation) {
                window.screen.orientation.addEventListener('change', function() {
                    setTimeout(function() { if (map) map.invalidateSize(); }, 500);
                });
            }

            console.log('🚀 Tracklane tracking initialized');
            console.log('👤 Role: ' + userRole);
            console.log('📍 Using web routes for location tracking');
        }

        // ============================================================
        // CLEANUP
        // ============================================================
        
        window.addEventListener('beforeunload', function() {
            if (gpsWatchId) navigator.geolocation.clearWatch(gpsWatchId);
            if (locationPollInterval) clearInterval(locationPollInterval);
        });

        // ============================================================
        // START
        // ============================================================
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        // Expose functions globally
        window.callCustomer = callCustomer;
        window.chatCustomer = chatCustomer;
        window.callDriver = callDriver;
        window.chatDriver = chatDriver;
        window.openMap = openMap;
        window.advanceStatus = advanceStatus;
        window.updateOrderStatus = updateOrderStatus;

    })();
</script>

</body>
</html>