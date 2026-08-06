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
    <a href="{{ route('dashboard') }}" class="sidebar-logo" title="Dashboard">
        <i class="ti ti-route"></i>
    </a>
    
    <!-- Navigation Items -->
    <a href="{{ route('dashboard') }}" 
       class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       title="Dashboard">
        <i class="ti ti-layout-dashboard"></i>
    </a>
    
    
    @auth
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.orders') }}" 
               class="sidebar-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}"
               title="Orders">
                <i class="ti ti-package"></i>
            </a>
            
            <a href="{{ route('admin.drivers') }}" 
               class="sidebar-item {{ request()->routeIs('admin.drivers') ? 'active' : '' }}"
               title="Drivers">
                <i class="ti ti-users"></i>
            </a>
            
            <!-- ✅ CUSTOMER MANAGEMENT - ADDED WITH WHITE COLOR -->
            <a href="{{ route('admin.customers') }}" 
               class="sidebar-item {{ request()->routeIs('admin.customers') ? 'active' : '' }}"
               title="Customers">
                <i class="fa-solid fa-users" style="font-size:18px; color:#FFFFFF;"></i>
            </a>
        @endif
        
        <!-- Driver Links (only visible to drivers) -->
        @if(Auth::user()->role === 'driver')
            <a href="{{ route('driver.orders') }}" 
               class="sidebar-item {{ request()->routeIs('driver.orders') ? 'active' : '' }}"
               title="My Orders">
                <i class="ti ti-list"></i>
            </a>
        @endif
        
        <!-- Customer Links (only visible to customers) -->
        @if(Auth::user()->role === 'customer')
            <a href="{{ route('customer.orders') }}" 
               class="sidebar-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}"
               title="My Orders">
                <i class="ti ti-history"></i>
            </a>
            
            <a href="{{ route('customer.create-order') }}" 
               class="sidebar-item {{ request()->routeIs('customer.create-order') ? 'active' : '' }}"
               title="New Order">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
    
    <!-- Common Links (visible to all authenticated users) -->
     
   <!-- Tracking (visible to all) -->
   <a href="{{ route('tracking') }}" 
      class="sidebar-item {{ request()->routeIs('tracking') ? 'active' : '' }}"
      title="Live Tracking">
      <i class="ti ti-map-pin"></i>
    </a>
    
    <!-- Reports Link -->
    <a href="{{ route('reports') }}" 
       class="sidebar-item {{ request()->routeIs('reports') ? 'active' : '' }}"
       title="Reports">
        <i class="ti ti-chart-bar"></i>
    </a>
    
    <!-- Profile at bottom -->
    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" 
           class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
           title="Profile">
            <i class="ti ti-user"></i>
        </a>
    </div>
</div>