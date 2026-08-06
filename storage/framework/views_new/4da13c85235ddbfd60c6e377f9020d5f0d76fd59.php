
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->startSection('container-class', ''); ?>

<?php $__env->startSection('content'); ?>
    <!-- Sidebar -->
    <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Main Content -->
    <div class="main-content-tracklane">
        <!-- Top Navigation -->
        <?php echo $__env->make('layouts.top-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Page Content -->
        <?php echo $__env->yieldContent('dashboard-content'); ?>  <!-- ✅ THIS STAYS -->
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>