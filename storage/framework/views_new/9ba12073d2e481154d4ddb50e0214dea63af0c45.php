

<?php $__env->startSection('title', 'Admin Reports - Tracklane'); ?>
<?php $__env->startSection('page-title', 'System Reports & Analytics'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">📊 System Reports</div>
            <div style="font-size:12px; color:#64748B;">Complete overview of your logistics network</div>
        </div>
        <a href="<?php echo e(route('admin.reports.export')); ?>" 
           style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:9px 16px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px; text-decoration:none; cursor:pointer;">
            <i class="ti ti-download" style="font-size:16px;"></i> Export Report
        </a>
    </div>

    <!-- Stats Cards -->
    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Orders</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e(number_format($totalOrders)); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Total Revenue</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e(number_format($totalRevenue, 0, ',', ' ')); ?> F</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">Active Drivers</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e($activeDrivers); ?>/<?php echo e($totalDrivers); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B; text-transform:uppercase;">On-Time Delivery</div>
            <div style="font-size:22px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e($onTimeRate); ?>%</div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">Today's Revenue</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e(number_format($todayRevenue, 0, ',', ' ')); ?> F</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Week</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e(number_format($weekOrders)); ?> orders</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
            <div style="font-size:11px; color:#64748B;">This Month</div>
            <div style="font-size:18px; font-weight:600; color:#0F172A; margin-top:4px;"><?php echo e(number_format($monthOrders)); ?> orders</div>
        </div>
    </div>

    <!-- Daily Orders Chart -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px; margin-bottom:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">📈 Daily Orders (Last 7 Days)</div>
        <div style="display:flex; align-items:flex-end; gap:12px; height:120px;">
            <?php $__currentLoopData = $dailyOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%;">
                <div style="font-size:10px; color:#64748B; margin-bottom:4px;"><?php echo e($day['count']); ?></div>
                <div style="width:100%; max-width:40px; border-radius:4px 4px 0 0; background:#14B8A6; height:<?php echo e(max(4, ($day['count'] / max(1, collect($dailyOrders)->max('count'))) * 100)); ?>%; min-height:4px;"></div>
                <div style="font-size:10px; color:#64748B; margin-top:6px;"><?php echo e($day['date']); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Top Drivers -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">🏆 Top Performing Drivers</div>
        
        <?php if($topDrivers->count() > 0): ?>
            <?php $__currentLoopData = $topDrivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:0.5px solid #E2E8F0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; color:#0D9488;">
                        <?php echo e(strtoupper(substr($driver->name, 0, 2))); ?>

                    </div>
                    <div>
                        <div style="font-size:13px; color:#0F172A;"><?php echo e($driver->name); ?></div>
                        <div style="font-size:11px; color:#64748B;"><?php echo e($driver->assigned_orders_count ?? 0); ?> deliveries</div>
                    </div>
                </div>
                <span style="font-size:13px; font-weight:600; color:#0D9488;">🏅</span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div style="text-align:center; color:#94A3B8; padding:20px;">No driver data yet</div>
        <?php endif; ?>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/reports/admin.blade.php ENDPATH**/ ?>