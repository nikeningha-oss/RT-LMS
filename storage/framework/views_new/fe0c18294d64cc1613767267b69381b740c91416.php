

<?php $__env->startSection('title', 'Payment - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Complete Payment'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<style>
    .payment-summary {
        background: #F8FAFC;
        border-radius: 12px;
        padding: 16px 18px;
        border: 0.5px solid #E2E8F0;
        margin-bottom: 16px;
    }
    .payment-summary .row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
    }
    .payment-summary .row.total {
        border-top: 1.5px solid #E2E8F0;
        margin-top: 4px;
        padding-top: 8px;
        font-weight: 700;
        font-size: 15px;
    }
    .payment-summary .row .label { color: #64748B; }
    .payment-summary .row .value { color: #0F172A; }
    .payment-summary .row .value.driver { color: #10B981; }
    .payment-summary .row .value.platform { color: #3B82F6; }
    .payment-summary .row .value.total { color: #0D9488; font-size: 18px; }

    .payment-method-card {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .payment-method-card:hover {
        border-color: #14B8A6;
        background: #F8FAFC;
    }
    .payment-method-card.selected {
        border-color: #14B8A6;
        background: #F0FDFA;
        box-shadow: 0 0 0 3px rgba(20,184,166,0.1);
    }
    .payment-method-card .icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .payment-method-card .icon.mobile { background: #D1FAE5; color: #10B981; }
    .payment-method-card .icon.card { background: #DBEAFE; color: #3B82F6; }
    .payment-method-card .icon.cash { background: #FEF3C7; color: #F59E0B; }
    .payment-method-card .info { flex: 1; }
    .payment-method-card .info .name { font-size: 14px; font-weight: 500; color: #0F172A; }
    .payment-method-card .info .desc { font-size: 12px; color: #94A3B8; }
    .payment-method-card .radio { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #E2E8F0; flex-shrink: 0; }
    .payment-method-card.selected .radio { border-color: #14B8A6; background: #14B8A6; position: relative; }
    .payment-method-card.selected .radio::after { content: '✓'; color: #FFFFFF; font-size: 12px; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }

    .mobile-money-fields {
        background: #F8FAFC;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 12px;
        display: none;
    }
    .mobile-money-fields.show {
        display: block;
    }
    .mobile-money-fields .field {
        margin-bottom: 8px;
    }
    .mobile-money-fields .field:last-child { margin-bottom: 0; }
    .mobile-money-fields .field label {
        font-size: 12px;
        font-weight: 500;
        color: #0F172A;
        display: block;
        margin-bottom: 2px;
    }
    .mobile-money-fields .field input,
    .mobile-money-fields .field select {
        width: 100%;
        padding: 8px 12px;
        border: 0.5px solid #E2E8F0;
        border-radius: 6px;
        font-size: 13px;
        background: #FFFFFF;
    }
    .mobile-money-fields .field input:focus,
    .mobile-money-fields .field select:focus {
        outline: none;
        border-color: #14B8A6;
    }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width:700px; margin:0 auto;">

        <!-- Header -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <a href="<?php echo e(route('customer.orders')); ?>" style="color:#64748B; text-decoration:none; font-size:20px;">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <div style="font-size:18px; font-weight:600; color:#0F172A;">💳 Complete Payment</div>
                <div style="font-size:13px; color:#64748B;">Order #<?php echo e($order->order_number); ?></div>
            </div>
        </div>

        <!-- ============================================================
             ORDER SUMMARY
             ============================================================ -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px; margin-bottom:16px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Order</div>
                    <div style="font-size:14px; font-weight:500; color:#0F172A;"><?php echo e($order->order_number); ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Status</div>
                    <div style="font-size:14px; font-weight:500; color:#F59E0B;">Awaiting Payment</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Pickup</div>
                    <div style="font-size:13px; color:#0F172A;"><?php echo e(Str::limit($order->pickup_address, 35)); ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Delivery</div>
                    <div style="font-size:13px; color:#0F172A;"><?php echo e(Str::limit($order->delivery_address, 35)); ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Distance</div>
                    <div style="font-size:13px; color:#0F172A;"><?php echo e($order->distance_km ?? 0); ?> km</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.04em;">Weight</div>
                    <div style="font-size:13px; color:#0F172A;"><?php echo e($order->weight_kg ?? 0); ?> kg</div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             PRICE BREAKDOWN
             ============================================================ -->
        <div class="payment-summary">
            <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:8px;">💰 Price Breakdown</div>
            <div class="row">
                <span class="label">Base Fare</span>
                <span class="value"><?php echo e(number_format($order->base_fare ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div class="row">
                <span class="label">Distance (<?php echo e($order->distance_km ?? 0); ?> km)</span>
                <span class="value"><?php echo e(number_format($order->distance_charge ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div class="row">
                <span class="label">Weight (<?php echo e($order->weight_kg ?? 0); ?> kg)</span>
                <span class="value"><?php echo e(number_format($order->weight_charge ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div class="row">
                <span class="label">Service Fee (5%)</span>
                <span class="value"><?php echo e(number_format($order->service_fee ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div class="row">
                <span class="label">VAT (<?php echo e($order->tax_rate ?? 5); ?>%)</span>
                <span class="value"><?php echo e(number_format($order->tax_amount ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div class="row total">
                <span class="label">💰 Total to Pay</span>
                <span class="value total"><?php echo e(number_format($order->total_price ?? 0, 0, ',', ' ')); ?> F</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px; padding-top:8px; border-top:0.5px solid #E2E8F0;">
                <div class="row">
                    <span class="label">👨‍✈️ Driver Earns (50%)</span>
                    <span class="value driver"><?php echo e(number_format($order->driver_earning ?? 0, 0, ',', ' ')); ?> F</span>
                </div>
                <div class="row">
                    <span class="label">🏢 Platform Fee (50%)</span>
                    <span class="value platform"><?php echo e(number_format($order->platform_fee ?? 0, 0, ',', ' ')); ?> F</span>
                </div>
            </div>
        </div>

        <!-- ============================================================
             PAYMENT METHOD
             ============================================================ -->
        <form method="POST" action="<?php echo e(route('customer.process-payment', $order->id)); ?>" id="paymentForm">
            <?php echo csrf_field(); ?>

            <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px; margin-bottom:16px;">
                <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">💳 Select Payment Method</div>

                <!-- Mobile Money -->
                <div class="payment-method-card" onclick="selectMethod('mobile_money')" id="method_mobile_money">
                    <div class="icon mobile"><i class="ti ti-device-mobile"></i></div>
                    <div class="info">
                        <div class="name">Mobile Money</div>
                        <div class="desc">Pay with Orange Money, MTN Mobile Money, etc.</div>
                    </div>
                    <div class="radio"></div>
                </div>

                <div class="mobile-money-fields" id="mobileMoneyFields">
                    <div class="field">
                        <label>Provider</label>
                        <select name="provider" id="provider">
                            <option value="orange">Orange Money</option>
                            <option value="mtn">MTN Mobile Money</option>
                            <option value="airtel">Airtel Money</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Phone Number</label>
                        <input type="tel" name="phone_number" placeholder="e.g., 690000000" value="<?php echo e(Auth::user()->phone ?? ''); ?>">
                    </div>
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        <i class="ti ti-info-circle"></i> You will receive a payment request on your phone
                    </div>
                </div>

                <!-- Card -->
                <div class="payment-method-card" onclick="selectMethod('card')" id="method_card" style="margin-top:8px;">
                    <div class="icon card"><i class="ti ti-credit-card"></i></div>
                    <div class="info">
                        <div class="name">Card Payment</div>
                        <div class="desc">Visa, Mastercard, etc.</div>
                    </div>
                    <div class="radio"></div>
                </div>

                <!-- Cash -->
                <div class="payment-method-card" onclick="selectMethod('cash')" id="method_cash" style="margin-top:8px;">
                    <div class="icon cash"><i class="ti ti-money"></i></div>
                    <div class="info">
                        <div class="name">Cash on Delivery</div>
                        <div class="desc">Pay cash when the driver arrives</div>
                    </div>
                    <div class="radio"></div>
                </div>
            </div>

            <input type="hidden" name="payment_method" id="payment_method" value="">

            <!-- Submit Button -->
            <button type="submit" 
                    id="payBtn"
                    style="width:100%; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:14px; font-size:16px; font-weight:600; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:8px;">
                <i class="ti ti-credit-card"></i> Pay <?php echo e(number_format($order->total_price ?? 0, 0, ',', ' ')); ?> F
            </button>

            <div style="text-align:center; margin-top:8px;">
                <p style="font-size:11px; color:#94A3B8;">
                    <i class="ti ti-lock me-1"></i> Secured payment. Your information is safe.
                </p>
            </div>

        </form>

    </div>

</div>

<script>
    let selectedMethod = null;

    function selectMethod(method) {
        // Reset all
        document.querySelectorAll('.payment-method-card').forEach(el => el.classList.remove('selected'));
        document.getElementById('mobileMoneyFields').classList.remove('show');
        
        // Select the chosen method
        const el = document.getElementById('method_' + method);
        if (el) {
            el.classList.add('selected');
        }
        
        selectedMethod = method;
        document.getElementById('payment_method').value = method;
        
        // Show mobile money fields if selected
        if (method === 'mobile_money') {
            document.getElementById('mobileMoneyFields').classList.add('show');
        }
    }

    // Auto-select mobile money by default
    document.addEventListener('DOMContentLoaded', function() {
        selectMethod('mobile_money');
    });

    // Validate before submit
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        if (!selectedMethod) {
            e.preventDefault();
            alert('⚠️ Please select a payment method.');
            return false;
        }
        
        if (selectedMethod === 'mobile_money') {
            const phone = document.querySelector('input[name="phone_number"]');
            if (phone && !phone.value.trim()) {
                e.preventDefault();
                alert('⚠️ Please enter your phone number for Mobile Money payment.');
                phone.focus();
                return false;
            }
        }
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/customer/payment.blade.php ENDPATH**/ ?>