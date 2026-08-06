<!--
    ============================================================
    TRACKLANE SIDEBAR
    ============================================================
    This is the fixed sidebar that appears on all dashboard pages.
    It contains navigation links to different sections.
    
    Links are highlighted based on the current page using
    the request()->routeIs() method.
-->

<div class="sidebar-tracklane">
    <!-- Logo -->
    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-logo" title="Dashboard">
        <i class="ti ti-route"></i>
    </a>
    
    <!-- Navigation Items -->
    <a href="<?php echo e(route('dashboard')); ?>" 
       class="sidebar-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
       title="Dashboard">
        <i class="ti ti-layout-dashboard"></i>
    </a>
    
    
    <?php if(auth()->guard()->check()): ?>
        <?php if(Auth::user()->role === 'admin'): ?>
            <a href="<?php echo e(route('admin.orders')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('admin.orders') ? 'active' : ''); ?>"
               title="Orders">
                <i class="ti ti-package"></i>
            </a>
            
            <a href="<?php echo e(route('admin.drivers')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('admin.drivers') ? 'active' : ''); ?>"
               title="Drivers">
                <i class="ti ti-users"></i>
            </a>
            
            <!-- ✅ CUSTOMER MANAGEMENT - ADDED WITH WHITE COLOR -->
            <a href="<?php echo e(route('admin.customers')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('admin.customers') ? 'active' : ''); ?>"
               title="Customers">
                <i class="fa-solid fa-users" style="font-size:18px; color:#FFFFFF;"></i>
            </a>
        <?php endif; ?>
        
        <!-- Driver Links (only visible to drivers) -->
        <?php if(Auth::user()->role === 'driver'): ?>
            <a href="<?php echo e(route('driver.orders')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('driver.orders') ? 'active' : ''); ?>"
               title="My Orders">
                <i class="ti ti-list"></i>
            </a>
        <?php endif; ?>
        
        <!-- Customer Links (only visible to customers) -->
        <?php if(Auth::user()->role === 'customer'): ?>
            <a href="<?php echo e(route('customer.orders')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('customer.orders') ? 'active' : ''); ?>"
               title="My Orders">
                <i class="ti ti-history"></i>
            </a>
            
            <a href="<?php echo e(route('customer.create-order')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('customer.create-order') ? 'active' : ''); ?>"
               title="New Order">
                <i class="ti ti-plus"></i>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Common Links (visible to all authenticated users) -->
     
   <!-- Tracking (visible to all) -->
   <a href="<?php echo e(route('tracking')); ?>" 
      class="sidebar-item <?php echo e(request()->routeIs('tracking') ? 'active' : ''); ?>"
      title="Live Tracking">
      <i class="ti ti-map-pin"></i>
    </a>
    
    <!-- Reports Link -->
    <a href="<?php echo e(route('reports')); ?>" 
       class="sidebar-item <?php echo e(request()->routeIs('reports') ? 'active' : ''); ?>"
       title="Reports">
        <i class="ti ti-chart-bar"></i>
    </a>
    
    <!-- Profile at bottom -->
    <div class="sidebar-footer">
        <a href="<?php echo e(route('profile.edit')); ?>" 
           class="sidebar-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
           title="Profile">
            <i class="ti ti-user"></i>
        </a>
    </div>
</div><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>