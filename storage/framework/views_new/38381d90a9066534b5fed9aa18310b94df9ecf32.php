

<?php $__env->startSection('title', 'Customer Dashboard - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Customer Dashboard'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<style>
    .price-notification {
        background: #FEF3C7;
        border: 1px solid #F59E0B;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .price-notification .price-amount {
        font-size: 20px;
        font-weight: 700;
        color: #0F172A;
    }
    .price-notification .order-ref {
        font-size: 13px;
        color: #64748B;
    }
    .btn-confirm {
        background: #0D9488;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-confirm:hover {
        background: #0F766E;
        transform: translateY(-1px);
    }
    .btn-pay {
        background: #10B981;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-pay:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    .price-badge {
        background: #FEF3C7;
        color: #92400E;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .price-badge.confirmed { background: #D1FAE5; color: #065F46; }
    .price-badge.pending { background: #FEF3C7; color: #92400E; }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">Hi <?php echo e(Auth::user()->name); ?> 👋</div>
            <div style="font-size:12px; color:#64748B;">Where do you want to send a package today?</div>
        </div>
        <a href="<?php echo e(route('customer.create-order')); ?>" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:9px 16px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px; text-decoration:none; cursor:pointer;">
            <i class="ti ti-plus" style="font-size:16px;"></i> New delivery
        </a>
    </div>

    <!-- ============================================================
         PRICE CONFIRMATION NOTIFICATION
         ============================================================ -->
    <?php if(isset($pendingPriceOrders) && $pendingPriceOrders->count() > 0): ?>
        <?php $__currentLoopData = $pendingPriceOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="price-notification">
            <div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span style="font-size:14px; font-weight:600; color:#0F172A;">💰 Price Set!</span>
                    <span class="price-badge pending">Awaiting Confirmation</span>
                </div>
                <div style="margin-top:4px;">
                    <span class="order-ref">Order #<?php echo e($order->order_number); ?></span>
                    <span style="margin:0 8px; color:#94A3B8;">|</span>
                    <span style="font-size:13px; color:#0F172A;">
                        Delivery: <strong><?php echo e(Str::limit($order->delivery_address, 30)); ?></strong>
                    </span>
                </div>
                <div style="margin-top:4px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <span style="font-size:14px; color:#64748B;">Total Amount:</span>
                    <span class="price-amount"><?php echo e(number_format($order->total_price, 0, ',', ' ')); ?> F</span>
                    <span style="font-size:12px; color:#94A3B8;">(Set by admin)</span>
                </div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button onclick="confirmPrice(<?php echo e($order->id); ?>)" class="btn-confirm">
                    <i class="ti ti-check"></i> Confirm Price
                </button>
                <button onclick="window.location.href='<?php echo e(route('customer.orders')); ?>'" 
                        style="background:#F1F5F9; color:#64748B; border:none; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <i class="ti ti-eye"></i> View Details
                </button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- ============================================================
         CONFIRMED ORDERS AWAITING PAYMENT
         ============================================================ -->
    <?php if(isset($confirmedOrders) && $confirmedOrders->count() > 0): ?>
        <?php $__currentLoopData = $confirmedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:#D1FAE5; border:1px solid #10B981; border-radius:12px; padding:16px 18px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span style="font-size:14px; font-weight:600; color:#0F172A;">✅ Ready for Payment</span>
                    <span class="price-badge confirmed">Confirmed</span>
                </div>
                <div style="margin-top:4px;">
                    <span class="order-ref">Order #<?php echo e($order->order_number); ?></span>
                    <span style="margin:0 8px; color:#94A3B8;">|</span>
                    <span style="font-size:13px; color:#0F172A;">
                        Total: <strong><?php echo e(number_format($order->total_price, 0, ',', ' ')); ?> F</strong>
                    </span>
                </div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button onclick="makePayment(<?php echo e($order->id); ?>)" class="btn-pay">
                    <i class="ti ti-credit-card"></i> Pay Now
                </button>
                <button onclick="window.location.href='<?php echo e(route('tracking', $order->id)); ?>'" 
                        style="background:#FFFFFF; color:#0F172A; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <i class="ti ti-map"></i> Track
                </button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Active Delivery Card -->
    <?php if(isset($activeOrder) && $activeOrder): ?>
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:14px; margin-bottom:16px;">
        <!-- ... existing active delivery card ... -->
    </div>
    <?php else: ?>
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:30px; text-align:center; margin-bottom:16px;">
        <i class="ti ti-truck" style="font-size:48px; color:#94A3B8; display:block; margin-bottom:12px;"></i>
        <div style="font-size:16px; font-weight:500; color:#0F172A;">No Active Delivery</div>
        <div style="font-size:13px; color:#64748B; margin-top:4px;">You don't have any active deliveries right now.</div>
        <a href="<?php echo e(route('customer.create-order')); ?>" 
           style="display:inline-block; margin-top:12px; background:#0D9488; color:#FFFFFF; padding:8px 20px; border-radius:8px; text-decoration:none; font-size:13px;">
            <i class="ti ti-plus me-1"></i> Create New Order
        </a>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 12px;">
            <div style="font-size:12px; color:#64748B; margin-bottom:6px;">Total deliveries</div>
            <div style="font-size:18px; font-weight:500; color:#0F172A;"><?php echo e($totalOrders ?? 0); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 12px;">
            <div style="font-size:12px; color:#64748B; margin-bottom:6px;">This month</div>
            <div style="font-size:18px; font-weight:500; color:#0F172A;"><?php echo e($monthlyOrders ?? 0); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:11px 12px;">
            <div style="font-size:12px; color:#64748B; margin-bottom:6px;">Pending payment</div>
            <div style="font-size:18px; font-weight:500; color:#F59E0B;">
                <?php echo e(isset($pendingPaymentCount) ? $pendingPaymentCount : 0); ?>

            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:12px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:10px;">Recent orders</div>
        
        <?php if(isset($recentOrders) && $recentOrders->count() > 0): ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B;">
                        <th style="padding:6px 4px; text-align:left;">Order</th>
                        <th style="padding:6px 4px; text-align:left;">To</th>
                        <th style="padding:6px 4px; text-align:left;">Date</th>
                        <th style="padding:6px 4px; text-align:left;">Status</th>
                        <th style="padding:6px 4px; text-align:right;">Price</th>
                        <th style="padding:6px 4px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-top:0.5px solid #E2E8F0;">
                        <td style="padding:7px 4px; color:#0F172A; font-weight:500;"><?php echo e($order->order_number); ?></td>
                        <td style="padding:7px 4px; color:#0F172A;"><?php echo e(Str::limit($order->delivery_address, 20)); ?></td>
                        <td style="padding:7px 4px; color:#64748B;">
                            <?php echo e(optional($order->created_at)->format('M d') ?? 'N/A'); ?>

                        </td>
                        <td style="padding:7px 4px;">
                            <?php
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
                            ?>
                            <span style="padding:3px 8px; border-radius:6px; <?php echo e($statusStyles[$order->status] ?? 'background:#F1F5F9;color:#475569;'); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $order->status))); ?>

                            </span>
                        </td>
                        <td style="padding:7px 4px; text-align:right; color:#0F172A;">
                            <?php if($order->total_price > 0): ?>
                                <?php echo e(number_format($order->total_price, 0, ',', ' ')); ?> F
                            <?php else: ?>
                                <span style="color:#94A3B8;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:7px 4px; text-align:center;">
                            <?php if($order->status === 'price_pending'): ?>
                                <button onclick="confirmPrice(<?php echo e($order->id); ?>)" 
                                        style="background:#0D9488; color:#FFFFFF; border:none; border-radius:4px; padding:2px 10px; font-size:10px; cursor:pointer;">
                                    Confirm
                                </button>
                            <?php elseif($order->status === 'price_confirmed'): ?>
                                <button onclick="makePayment(<?php echo e($order->id); ?>)" 
                                        style="background:#10B981; color:#FFFFFF; border:none; border-radius:4px; padding:2px 10px; font-size:10px; cursor:pointer;">
                                    Pay
                                </button>
                            <?php else: ?>
                                <a href="<?php echo e(route('tracking', $order->id)); ?>" 
                                   style="color:#14B8A6; text-decoration:none; font-size:11px;">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:20px; color:#94A3B8;">
            <i class="ti ti-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            <span style="font-size:13px;">No orders yet</span>
        </div>
        <?php endif; ?>
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
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/dashboard/customer.blade.php ENDPATH**/ ?>