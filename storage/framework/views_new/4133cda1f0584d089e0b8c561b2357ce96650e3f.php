

<?php $__env->startSection('title', 'Create Order - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Create New Delivery'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .map-container-large {
        height: 400px;
        border-radius: 12px;
        border: 0.5px solid #E2E8F0;
        margin-top: 8px;
        margin-bottom: 8px;
        overflow: hidden;
        position: relative;
        width: 100%;
    }
    .map-container-large .map-label {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(255,255,255,0.95);
        padding: 4px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #0F172A;
        z-index: 1000;
        border: 0.5px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .map-container-large .map-label.pickup { 
        border-left: 4px solid #14B8A6; 
        top: 12px;
        left: 12px;
    }
    .map-container-large .map-label.dropoff { 
        border-left: 4px solid #D85A30; 
        top: 12px;
        left: 160px;
    }
    .map-container-large .map-instruction {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: #FFFFFF;
        padding: 6px 20px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 400;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    }
    .map-container-large .map-instruction i {
        margin-right: 6px;
    }
    @media (max-width: 640px) {
        .map-container-large {
            height: 300px;
        }
        .map-container-large .map-label.dropoff {
            left: 12px;
            top: 52px;
        }
        .map-container-large .map-instruction {
            font-size: 10px;
            padding: 4px 12px;
            white-space: normal;
            max-width: 90%;
        }
    }

    .location-input-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 4px;
    }
    .location-input-row input {
        width: 100%;
        padding: 6px 10px;
        border: 0.5px solid #E2E8F0;
        border-radius: 6px;
        font-size: 12px;
        background: #F8FAFC;
    }
    .location-input-row input:focus {
        outline: none;
        border-color: #14B8A6;
        background: #FFFFFF;
    }
    .pickup-coords { border-left: 3px solid #14B8A6; padding-left: 10px; }
    .dropoff-coords { border-left: 3px solid #D85A30; padding-left: 10px; }

    .distance-display-large {
        background: #F8FAFC;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        color: #0F172A;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        border: 0.5px solid #E2E8F0;
        margin: 10px 0;
    }
    .distance-display-large .value {
        font-weight: 700;
        color: #14B8A6;
        font-size: 18px;
    }
    .distance-display-large .label-icon {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748B;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #0F172A;
        margin: 18px 0 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title .badge {
        font-size: 10px;
        font-weight: 500;
        padding: 1px 10px;
        border-radius: 20px;
        background: #F1F5F9;
        color: #64748B;
    }

    .address-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 768px) {
        .address-grid {
            grid-template-columns: 1fr;
        }
    }

    .map-hint {
        font-size: 12px;
        color: #94A3B8;
        margin: 4px 0 8px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ✅ NEW: Price Preview Styles */
    .price-preview-box {
        background: #F8FAFC;
        border-radius: 10px;
        padding: 14px 18px;
        border: 0.5px solid #E2E8F0;
        margin: 10px 0;
    }
    .price-preview-box .row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
    }
    .price-preview-box .row.total {
        border-top: 1.5px solid #E2E8F0;
        margin-top: 4px;
        padding-top: 8px;
        font-weight: 700;
        font-size: 15px;
    }
    .price-preview-box .row.driver {
        color: #10B981;
        font-weight: 600;
    }
    .price-preview-box .row.platform {
        color: #3B82F6;
        font-weight: 600;
    }
    .price-preview-box .label { color: #64748B; }
    .price-preview-box .value { color: #0F172A; }
    .price-preview-box .value.driver { color: #10B981; }
    .price-preview-box .value.platform { color: #3B82F6; }
    .price-preview-box .value.total { color: #0D9488; font-size: 18px; }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width:1100px; margin:0 auto;">

        <!-- ============================================================
             HEADER
             ============================================================ -->
        <div style="margin-bottom:16px;">
            <div style="font-size:20px; font-weight:600; color:#0F172A;">📍 Create New Delivery</div>
            <div style="font-size:13px; color:#64748B;">Click on the map to set pickup and delivery locations</div>
        </div>

        <!-- ============================================================
             ORDER FORM
         ============================================================ -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:24px;">

            <form method="POST" action="<?php echo e(route('customer.store-order')); ?>" id="orderForm">
                <?php echo csrf_field(); ?>

                <!-- ============================================================
                     LARGE MAP PICKER
                     ============================================================ -->
                <div style="margin-bottom:8px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                        <span style="font-size:14px; font-weight:600; color:#0F172A;">🗺️ Select Locations</span>
                        <div style="display:flex; gap:12px; font-size:12px;">
                            <span style="display:flex; align-items:center; gap:4px;">
                                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#14B8A6;"></span>
                                Pickup
                            </span>
                            <span style="display:flex; align-items:center; gap:4px;">
                                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#D85A30;"></span>
                                Drop-off
                            </span>
                        </div>
                    </div>
                    
                    <div class="map-container-large" id="mapContainer">
                        <div id="locationMap" style="height:100%; width:100%;"></div>
                        <div class="map-label pickup" id="pickupLabel" style="display:none;">📍 Pickup</div>
                        <div class="map-label dropoff" id="dropoffLabel" style="display:none;">📍 Drop-off</div>
                        <div class="map-instruction" id="mapInstruction">
                            <i class="ti ti-hand-click"></i> Click to place pickup, then click again for drop-off
                        </div>
                    </div>
                    
                    <div class="map-hint">
                        <i class="ti ti-info-circle"></i> Click on the map to set pickup point, then click again for drop-off. 
                        <span style="color:#14B8A6; font-weight:500;">Markers are draggable</span> for fine-tuning.
                    </div>
                </div>

                <!-- ============================================================
                     COORDINATES (Hidden)
                     ============================================================ -->
                <input type="hidden" name="pickup_lat" id="pickup_lat" value="<?php echo e(old('pickup_lat')); ?>">
                <input type="hidden" name="pickup_lng" id="pickup_lng" value="<?php echo e(old('pickup_lng')); ?>">
                <input type="hidden" name="delivery_lat" id="delivery_lat" value="<?php echo e(old('delivery_lat')); ?>">
                <input type="hidden" name="delivery_lng" id="delivery_lng" value="<?php echo e(old('delivery_lng')); ?>">
                <input type="hidden" name="distance_km" id="distance_km" value="<?php echo e(old('distance_km')); ?>">

                <!-- ============================================================
                     DISTANCE DISPLAY
                     ============================================================ -->
                <div class="distance-display-large" id="distanceDisplay" style="<?php echo e(old('distance_km') ? '' : 'display:none;'); ?>">
                    <span class="label-icon">
                        <i class="ti ti-route" style="font-size:18px; color:#14B8A6;"></i> 
                        Distance between pickup & drop-off
                    </span>
                    <span class="value" id="distanceText"><?php echo e(old('distance_km') ? number_format(old('distance_km'), 1) : '0'); ?> km</span>
                </div>

                <!-- ============================================================
                     ADDRESS FIELDS
                     ============================================================ -->
                <div class="address-grid">

                    <!-- Pickup Address -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                            <i class="ti ti-map-pin me-1" style="color:#14B8A6;"></i> Pickup Address
                        </label>
                        <input type="text" 
                               name="pickup_address" 
                               id="pickup_address"
                               class="form-control <?php $__errorArgs = ['pickup_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter pickup address"
                               value="<?php echo e(old('pickup_address')); ?>"
                               required
                               style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                        <?php $__errorArgs = ['pickup_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="location-input-row pickup-coords">
                            <input type="text" id="pickup_lat_display" placeholder="Latitude" readonly>
                            <input type="text" id="pickup_lng_display" placeholder="Longitude" readonly>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                            <i class="ti ti-map-pin me-1" style="color:#D85A30;"></i> Delivery Address
                        </label>
                        <input type="text" 
                               name="delivery_address" 
                               id="delivery_address"
                               class="form-control <?php $__errorArgs = ['delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter delivery address"
                               value="<?php echo e(old('delivery_address')); ?>"
                               required
                               style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                        <?php $__errorArgs = ['delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="location-input-row dropoff-coords">
                            <input type="text" id="delivery_lat_display" placeholder="Latitude" readonly>
                            <input type="text" id="delivery_lng_display" placeholder="Longitude" readonly>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     DESCRIPTION
                     ============================================================ -->
                <div style="margin:16px 0;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-file-text me-1" style="color:#14B8A6;"></i> Package Description
                    </label>
                    <textarea name="description" 
                              class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              placeholder="Describe what you're sending (e.g., 2 boxes of electronics, fragile items, etc.)"
                              rows="2"
                              style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; font-family:inherit;"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- ============================================================
                     ✅ WEIGHT FIELD (NEW)
                     ============================================================ -->
                <div style="margin:16px 0;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-weight me-1" style="color:#14B8A6;"></i> Package Weight (kg) <span style="color:#E24B4A;">*</span>
                    </label>
                    <input type="number" 
                           name="weight_kg" 
                           id="weight_kg"
                           class="form-control <?php $__errorArgs = ['weight_kg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           placeholder="Enter package weight in kg (e.g., 2.5)"
                           value="<?php echo e(old('weight_kg')); ?>"
                           step="0.1"
                           min="0.1"
                           required
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        💡 Weight affects the delivery price. Heavier items cost more to deliver.
                    </div>
                    <?php $__errorArgs = ['weight_kg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- ============================================================
                     ✅ PRICE PREVIEW (NEW - 50/50 Split)
                     ============================================================ -->
                <div class="price-preview-box" id="pricePreview" style="display:none;">
                    <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:8px;">
                        💰 Price Breakdown
                    </div>
                    <div class="row">
                        <span class="label">Base Fare</span>
                        <span class="value" id="previewBaseFare">500 F</span>
                    </div>
                    <div class="row">
                        <span class="label">Distance Charge</span>
                        <span class="value" id="previewDistanceCharge">0 F</span>
                    </div>
                    <div class="row">
                        <span class="label">Weight Charge</span>
                        <span class="value" id="previewWeightCharge">0 F</span>
                    </div>
                    <div class="row">
                        <span class="label">Service Fee (5%)</span>
                        <span class="value" id="previewServiceFee">0 F</span>
                    </div>
                    <div class="row">
                        <span class="label">VAT (5%)</span>
                        <span class="value" id="previewTax">0 F</span>
                    </div>
                    <div class="row total">
                        <span class="label">💰 Total Price</span>
                        <span class="value total" id="previewTotal">0 F</span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px; padding-top:8px; border-top:0.5px solid #E2E8F0;">
                        <div class="row driver">
                            <span class="label">👨‍✈️ Driver Earns (50%)</span>
                            <span class="value driver" id="previewDriverEarning">0 F</span>
                        </div>
                        <div class="row platform">
                            <span class="label">🏢 Platform Fee (50%)</span>
                            <span class="value platform" id="previewPlatformFee">0 F</span>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     PRICE FIELD (Hidden - Auto-calculated by Admin)
                     ============================================================ -->
                <div style="display:none;">
                    <input type="number" name="total_price" value="0">
                </div>

                <!-- ============================================================
                     SUBMIT BUTTONS
                     ============================================================ -->
                <div style="display:flex; gap:12px; margin-top:8px; flex-wrap:wrap;">
                    <button type="submit" 
                            id="submitBtn"
                            style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:11px 40px; font-size:15px; font-weight:600; cursor:pointer; transition:all 0.2s;">
                        <i class="ti ti-send me-2"></i> Create Order
                    </button>
                    <a href="<?php echo e(route('customer.dashboard')); ?>" 
                       style="background:#FFFFFF; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 28px; font-size:15px; text-decoration:none; transition:all 0.2s;">
                        Cancel
                    </a>
                    <button type="button" 
                            id="resetMapBtn"
                            style="background:#F1F5F9; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 20px; font-size:14px; cursor:pointer; transition:all 0.2s;">
                        <i class="ti ti-refresh me-1"></i> Reset Map
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

<script>
    // =============================================================
    // MAP SETUP
    // =============================================================
    
    var map = L.map('locationMap').setView([4.0511, 9.7679], 13);

    // MapTiler Layer (or OpenStreetMap fallback)
    var maptilerKey = '<?php echo e(env("MAPTILER_KEY")); ?>';
    
    if (maptilerKey && maptilerKey !== '') {
        L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=' + maptilerKey, {
            tileSize: 512,
            zoomOffset: -1,
            minZoom: 1,
            maxZoom: 20,
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a>'
        }).addTo(map);
    } else {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    // =============================================================
    // PRICING CONSTANTS (50/50 Split)
    // =============================================================
    
    var BASE_FARE = 500;
    var PER_KM_RATE = 300;
    var PER_KG_RATE = 200;
    var TAX_RATE = 5;
    var DRIVER_COMMISSION = 50;
    var PLATFORM_COMMISSION = 50;
    var SERVICE_FEE_RATE = 5;

    // =============================================================
    // PRICE PREVIEW FUNCTION
    // =============================================================
    
    function updatePricePreview() {
        var distance = parseFloat(document.getElementById('distance_km').value) || 0;
        var weight = parseFloat(document.getElementById('weight_kg').value) || 0;
        
        if (distance === 0 && weight === 0) {
            document.getElementById('pricePreview').style.display = 'none';
            return;
        }
        
        document.getElementById('pricePreview').style.display = 'block';
        
        // Calculate components
        var baseFare = BASE_FARE;
        var distanceCharge = distance * PER_KM_RATE;
        var weightCharge = weight * PER_KG_RATE;
        var subtotal = baseFare + distanceCharge + weightCharge;
        var serviceFee = subtotal * (SERVICE_FEE_RATE / 100);
        var taxAmount = (subtotal + serviceFee) * (TAX_RATE / 100);
        var totalPrice = subtotal + serviceFee + taxAmount;
        var driverEarning = subtotal * (DRIVER_COMMISSION / 100);
        var platformFee = subtotal * (PLATFORM_COMMISSION / 100);
        
        // Update preview
        document.getElementById('previewBaseFare').textContent = baseFare + ' F';
        document.getElementById('previewDistanceCharge').textContent = Math.round(distanceCharge) + ' F';
        document.getElementById('previewWeightCharge').textContent = Math.round(weightCharge) + ' F';
        document.getElementById('previewServiceFee').textContent = Math.round(serviceFee) + ' F';
        document.getElementById('previewTax').textContent = Math.round(taxAmount) + ' F';
        document.getElementById('previewTotal').textContent = Math.round(totalPrice).toLocaleString() + ' F';
        document.getElementById('previewDriverEarning').textContent = Math.round(driverEarning).toLocaleString() + ' F';
        document.getElementById('previewPlatformFee').textContent = Math.round(platformFee).toLocaleString() + ' F';
    }

    // =============================================================
    // VARIABLES
    // =============================================================
    
    var pickupMarker = null;
    var dropoffMarker = null;
    var routeLine = null;
    var clickCount = 0;

    // =============================================================
    // ICONS
    // =============================================================
    
    var pickupIcon = L.divIcon({
        className: '',
        html: '<div style="width:18px;height:18px;border-radius:50%;background:#14B8A6;border:3px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.25);"></div>',
        iconSize: [18,18],
        iconAnchor: [9,9]
    });

    var dropoffIcon = L.divIcon({
        className: '',
        html: '<div style="width:18px;height:18px;border-radius:50%;background:#D85A30;border:3px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.25);"></div>',
        iconSize: [18,18],
        iconAnchor: [9,9]
    });

    // =============================================================
    // MAP CLICK HANDLER
    // =============================================================
    
    var instructionEl = document.getElementById('mapInstruction');

    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        clickCount++;

        if (clickCount === 1) {
            // Place Pickup
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
            }
            pickupMarker = L.marker([lat, lng], { icon: pickupIcon, draggable: true }).addTo(map);
            pickupMarker.on('dragend', function() { updatePickupCoords(); });
            
            document.getElementById('pickup_lat').value = lat;
            document.getElementById('pickup_lng').value = lng;
            document.getElementById('pickup_lat_display').value = lat.toFixed(6);
            document.getElementById('pickup_lng_display').value = lng.toFixed(6);
            
            document.getElementById('pickupLabel').style.display = 'block';
            document.getElementById('dropoffLabel').style.display = 'none';
            instructionEl.innerHTML = '<i class="ti ti-hand-click"></i> Now click again to set drop-off location';
            
            reverseGeocode(lat, lng, 'pickup_address');
            clearRoute();
            
            if (dropoffMarker) {
                map.removeLayer(dropoffMarker);
                dropoffMarker = null;
                document.getElementById('delivery_lat').value = '';
                document.getElementById('delivery_lng').value = '';
                document.getElementById('delivery_lat_display').value = '';
                document.getElementById('delivery_lng_display').value = '';
                document.getElementById('delivery_address').value = '';
            }
            
            document.getElementById('distanceDisplay').style.display = 'none';
            document.getElementById('suggestedPrice').textContent = '—';
            map.setView([lat, lng], 15);
            
        } else if (clickCount === 2) {
            // Place Dropoff
            if (dropoffMarker) {
                map.removeLayer(dropoffMarker);
            }
            dropoffMarker = L.marker([lat, lng], { icon: dropoffIcon, draggable: true }).addTo(map);
            dropoffMarker.on('dragend', function() { updateDropoffCoords(); });
            
            document.getElementById('delivery_lat').value = lat;
            document.getElementById('delivery_lng').value = lng;
            document.getElementById('delivery_lat_display').value = lat.toFixed(6);
            document.getElementById('delivery_lng_display').value = lng.toFixed(6);
            
            document.getElementById('dropoffLabel').style.display = 'block';
            instructionEl.innerHTML = '<i class="ti ti-check"></i> Locations set! Drag markers to adjust.';
            
            reverseGeocode(lat, lng, 'delivery_address');
            
            if (pickupMarker) {
                var pickupLat = parseFloat(document.getElementById('pickup_lat').value);
                var pickupLng = parseFloat(document.getElementById('pickup_lng').value);
                drawRoute(pickupLat, pickupLng, lat, lng);
                calculateDistance(pickupLat, pickupLng, lat, lng);
            }
            
            clickCount = 0;
            setTimeout(function() {
                instructionEl.innerHTML = '<i class="ti ti-hand-click"></i> Click to set new locations or drag markers';
            }, 3000);
        }
    });

    // =============================================================
    // UPDATE COORDINATES ON DRAG
    // =============================================================
    
    function updatePickupCoords() {
        if (!pickupMarker) return;
        var pos = pickupMarker.getLatLng();
        document.getElementById('pickup_lat').value = pos.lat;
        document.getElementById('pickup_lng').value = pos.lng;
        document.getElementById('pickup_lat_display').value = pos.lat.toFixed(6);
        document.getElementById('pickup_lng_display').value = pos.lng.toFixed(6);
        reverseGeocode(pos.lat, pos.lng, 'pickup_address');
        
        if (dropoffMarker) {
            var dropLat = parseFloat(document.getElementById('delivery_lat').value);
            var dropLng = parseFloat(document.getElementById('delivery_lng').value);
            drawRoute(pos.lat, pos.lng, dropLat, dropLng);
            calculateDistance(pos.lat, pos.lng, dropLat, dropLng);
        }
    }

    function updateDropoffCoords() {
        if (!dropoffMarker) return;
        var pos = dropoffMarker.getLatLng();
        document.getElementById('delivery_lat').value = pos.lat;
        document.getElementById('delivery_lng').value = pos.lng;
        document.getElementById('delivery_lat_display').value = pos.lat.toFixed(6);
        document.getElementById('delivery_lng_display').value = pos.lng.toFixed(6);
        reverseGeocode(pos.lat, pos.lng, 'delivery_address');
        
        if (pickupMarker) {
            var pickupLat = parseFloat(document.getElementById('pickup_lat').value);
            var pickupLng = parseFloat(document.getElementById('pickup_lng').value);
            drawRoute(pickupLat, pickupLng, pos.lat, pos.lng);
            calculateDistance(pickupLat, pickupLng, pos.lat, pos.lng);
        }
    }

    // =============================================================
    // DECODE POLYLINE (for OSRM)
    // =============================================================
    
    function decodePolyline(str, precision) {
        var index = 0,
            lat = 0,
            lng = 0,
            coordinates = [],
            shift = 0,
            result = 0,
            byte = null,
            latitude_change,
            longitude_change,
            factor = Math.pow(10, precision || 5);

        while (index < str.length) {
            byte = null;
            shift = 0;
            result = 0;
            do {
                byte = str.charCodeAt(index++) - 63;
                result |= (byte & 0x1f) << shift;
                shift += 5;
            } while (byte >= 0x20);

            latitude_change = ((result & 1) ? ~(result >> 1) : (result >> 1));
            shift = result = 0;

            do {
                byte = str.charCodeAt(index++) - 63;
                result |= (byte & 0x1f) << shift;
                shift += 5;
            } while (byte >= 0x20);

            longitude_change = ((result & 1) ? ~(result >> 1) : (result >> 1));

            lat += latitude_change;
            lng += longitude_change;

            coordinates.push([lat / factor, lng / factor]);
        }
        return coordinates;
    }

    // =============================================================
    // DRAW ROUTE (OSRM)
    // =============================================================
    
    function drawRoute(pickupLat, pickupLng, dropoffLat, dropoffLng) {
        clearRoute();
        
        var url = 'https://router.project-osrm.org/route/v1/driving/' + 
            pickupLng + ',' + pickupLat + ';' + dropoffLng + ',' + dropoffLat + 
            '?overview=full&geometries=polyline';
        
        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    var geometry = data.routes[0].geometry;
                    var decodedPoints = decodePolyline(geometry);
                    var latlngs = decodedPoints.map(function(p) {
                        return [p[0], p[1]];
                    });
                    routeLine = L.polyline(latlngs, {
                        color: '#14B8A6',
                        weight: 4,
                        opacity: 0.7
                    }).addTo(map);
                    map.fitBounds(L.latLngBounds(latlngs), { padding: [40, 40] });
                } else {
                    routeLine = L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], {
                        color: '#14B8A6',
                        weight: 3,
                        dashArray: '6 6',
                        opacity: 0.6,
                    }).addTo(map);
                    map.fitBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], { padding: [40, 40] });
                }
            })
            .catch(function() {
                routeLine = L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], {
                    color: '#14B8A6',
                    weight: 3,
                    dashArray: '6 6',
                    opacity: 0.6,
                }).addTo(map);
                map.fitBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], { padding: [40, 40] });
            });
    }

    function clearRoute() {
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }
    }

    // =============================================================
    // CALCULATE DISTANCE & UPDATE PRICE PREVIEW
    // =============================================================
    
    function calculateDistance(lat1, lng1, lat2, lng2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var distance = R * c;
        
        document.getElementById('distance_km').value = distance;
        document.getElementById('distanceText').textContent = distance.toFixed(1) + ' km';
        document.getElementById('distanceDisplay').style.display = 'flex';
        
        // ✅ Update price preview with distance
        updatePricePreview();
    }

    // =============================================================
    // REVERSE GEOCODING (Nominatim)
    // =============================================================
    
    function reverseGeocode(lat, lng, fieldId) {
        var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1';
        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data && data.display_name) {
                    document.getElementById(fieldId).value = data.display_name;
                }
            })
            .catch(function() {
                // Silently fail - user can still type address manually
            });
    }

    // =============================================================
    // RESET MAP
    // =============================================================
    
    document.getElementById('resetMapBtn').addEventListener('click', function() {
        if (pickupMarker) { map.removeLayer(pickupMarker); pickupMarker = null; }
        if (dropoffMarker) { map.removeLayer(dropoffMarker); dropoffMarker = null; }
        clearRoute();
        clickCount = 0;
        
        document.getElementById('pickup_lat').value = '';
        document.getElementById('pickup_lng').value = '';
        document.getElementById('delivery_lat').value = '';
        document.getElementById('delivery_lng').value = '';
        document.getElementById('pickup_lat_display').value = '';
        document.getElementById('pickup_lng_display').value = '';
        document.getElementById('delivery_lat_display').value = '';
        document.getElementById('delivery_lng_display').value = '';
        document.getElementById('pickup_address').value = '';
        document.getElementById('delivery_address').value = '';
        document.getElementById('pickupLabel').style.display = 'none';
        document.getElementById('dropoffLabel').style.display = 'none';
        document.getElementById('distanceDisplay').style.display = 'none';
        document.getElementById('suggestedPrice').textContent = '—';
        document.getElementById('pricePreview').style.display = 'none';
        instructionEl.innerHTML = '<i class="ti ti-hand-click"></i> Click to place pickup, then click again for drop-off';
        
        map.setView([4.0511, 9.7679], 13);
    });

    // =============================================================
    // WEIGHT INPUT CHANGE - UPDATE PRICE PREVIEW
    // =============================================================
    
    document.getElementById('weight_kg').addEventListener('input', function() {
        updatePricePreview();
    });

    // =============================================================
    // VALIDATION
    // =============================================================
    
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        var pickupLat = document.getElementById('pickup_lat').value;
        var deliveryLat = document.getElementById('delivery_lat').value;
        var weight = document.getElementById('weight_kg').value;
        
        if (!pickupLat || !deliveryLat) {
            e.preventDefault();
            alert('⚠️ Please select both pickup and delivery locations on the map.');
            return false;
        }
        
        if (!weight || parseFloat(weight) <= 0) {
            e.preventDefault();
            alert('⚠️ Please enter a valid package weight.');
            document.getElementById('weight_kg').focus();
            return false;
        }
    });

    // =============================================================
    // MAP RESIZE FIX
    // =============================================================
    
    setTimeout(function() {
        map.invalidateSize();
    }, 500);
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/customer/create-order.blade.php ENDPATH**/ ?>