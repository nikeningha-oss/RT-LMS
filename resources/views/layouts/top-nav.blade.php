<!--
    ============================================================
    TRACKLANE TOP NAVIGATION
    ============================================================
    This is the top bar that appears on all dashboard pages.
    It shows:
    - Page title
    - User role badge
    - Search icon with modal
    - Notifications with dropdown
    - User avatar with dropdown
-->

<div class="navbar-tracklane d-flex align-items-center justify-content-between mb-4">
    <!-- Left side: Page Title & Role Badge -->
    <div class="d-flex align-items-center gap-3">
        <h5 class="fw-600 mb-0" style="font-size: 16px; font-family: 'Inter', sans-serif;">
            @yield('page-title', 'Dashboard')
        </h5>
        
        <!-- Role Badge -->
        @auth
            <span class="badge bg-accent-light text-accent px-3 py-1 rounded-pill" 
                  style="font-size: 11px; font-weight: 500; font-family: 'Inter', sans-serif;">
                <i class="ti ti-users me-1"></i> {{ ucfirst(Auth::user()->role) }}
            </span>
        @endauth
    </div>
    
    <!-- Right side: Icons & User -->
    <div class="d-flex align-items-center gap-14" style="position:relative;">
        
        <!-- ============================================================
             SEARCH (with working modal)
             ============================================================ -->
        <i class="ti ti-search" 
           onclick="openSearch()" 
           style="font-size: 18px; color: var(--color-text-secondary); cursor: pointer;" 
           title="Search"></i>
        
        <!-- ============================================================
             NOTIFICATIONS (with working dropdown)
             ============================================================ -->
        <div class="notification-bell" onclick="toggleNotifications()" style="position:relative; cursor:pointer;">
            <i class="ti ti-bell" style="font-size: 18px; color: var(--color-text-secondary);"></i>
            <span id="notificationBadge" style="position:absolute; top:-6px; right:-6px; background:#E24B4A; color:#FFFFFF; font-size:10px; font-weight:600; border-radius:50%; width:18px; height:18px; display:none; align-items:center; justify-content:center;"></span>
        </div>
        
        <!-- Notifications Dropdown -->
        <div id="notificationsDropdown" style="display:none; position:absolute; right:0; top:40px; width:380px; max-height:420px; overflow-y:auto; background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.1); z-index:1050; padding:0;">
            <div style="padding:12px 16px; border-bottom:0.5px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#FFFFFF; border-radius:12px 12px 0 0;">
                <span style="font-weight:600; font-size:14px; color:#0F172A;">🔔 Notifications</span>
                <button onclick="markAllRead()" style="font-size:11px; color:#14B8A6; background:none; border:none; cursor:pointer;">Mark all as read</button>
            </div>
            <div id="notificationsList">
                <div style="padding:30px 20px; text-align:center; color:#94A3B8; font-size:13px;">
                    <i class="ti ti-bell-off" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                    No notifications yet
                </div>
            </div>
        </div>
        
        <!-- ============================================================
             USER DROPDOWN (with working Settings link)
             ============================================================ -->
        @auth
            <div class="user-avatar-tracklane" data-bs-toggle="dropdown" title="Account" style="cursor:pointer;">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end" 
                style="border-radius: var(--radius-md); border: 0.5px solid var(--color-border); box-shadow: var(--shadow-dropdown);">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}" 
                       style="font-family: 'Inter', sans-serif; font-size: 13px;">
                        <i class="ti ti-user me-2"></i> Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') }}" 
                       style="font-family: 'Inter', sans-serif; font-size: 13px;">
                        <i class="ti ti-settings me-2"></i> Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger" 
                                style="font-family: 'Inter', sans-serif; font-size: 13px;">
                            <i class="ti ti-logout me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        @endauth
    </div>
</div>

<!-- ============================================================
     SEARCH MODAL
     ============================================================ -->
<div id="searchModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:600px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="font-size:18px; font-weight:600; color:#0F172A;">🔍 Search</span>
            <button onclick="closeSearch()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94A3B8;">✕</button>
        </div>
        <input type="text" id="searchInput" 
               placeholder="Search for orders, customers, drivers..." 
               onkeyup="searchItems(this.value)"
               style="width:100%; padding:12px 16px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; font-family:'Inter', sans-serif;">
        <div id="searchResults" style="margin-top:12px; max-height:300px; overflow-y:auto;"></div>
    </div>
</div>

<!-- ============================================================
     SCRIPTS
     ============================================================ -->
@push('scripts')
<script>
    // =============================================================
    // SEARCH FUNCTIONS
    // =============================================================

    function openSearch() {
        document.getElementById('searchModal').style.display = 'block';
        document.getElementById('searchInput').focus();
    }

    function closeSearch() {
        document.getElementById('searchModal').style.display = 'none';
        document.getElementById('searchResults').innerHTML = '';
        document.getElementById('searchInput').value = '';
    }

    function searchItems(query) {
        var results = document.getElementById('searchResults');
        if (query.length < 2) {
            results.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8; font-size:13px;">Type at least 2 characters to search</div>';
            return;
        }
        
        results.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8; font-size:13px;">🔍 Searching for "' + query + '"...</div>';
        
        fetch('/api/search?q=' + encodeURIComponent(query))
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.length === 0) {
                    results.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8; font-size:13px;">' +
                        '<i class="ti ti-search" style="font-size:24px; display:block; margin-bottom:8px;"></i>' +
                        'No results found for "' + query + '"</div>';
                    return;
                }
                var html = '';
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];
                    var statusBadge = '';
                    if (item.status) {
                        var statusColors = {
                            'pending': '#F1F5F9; color:#475569;',
                            'assigned': '#DBEAFE; color:#1E40AF;',
                            'picked_up': '#E0E7FF; color:#3730A3;',
                            'in_transit': '#FEF3C7; color:#92400E;',
                            'delivered': '#D1FAE5; color:#065F46;',
                            'cancelled': '#FEE2E2; color:#991B1B;',
                        };
                        var color = statusColors[item.status] || '#F1F5F9; color:#475569;';
                        statusBadge = '<span style="padding:2px 8px; border-radius:12px; font-size:10px; ' + color + '">' + item.status + '</span>';
                    }
                    html += '<a href="' + item.url + '" style="text-decoration:none; color:inherit; display:block;">' +
                        '<div style="padding:10px 12px; border-bottom:0.5px solid #E2E8F0; cursor:pointer; transition:background 0.2s;">' +
                        '<div style="display:flex; align-items:center; justify-content:space-between;">' +
                        '<div style="font-weight:500; color:#0F172A;">' + item.title + '</div>' +
                        statusBadge +
                        '</div>' +
                        '<div style="font-size:12px; color:#64748B; margin-top:2px;">' + item.description + '</div>' +
                        '</div></a>';
                }
                results.innerHTML = html;
            })
            .catch(function(error) {
                console.error('Search error:', error);
                results.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8; font-size:13px;">' +
                    '<i class="ti ti-alert-circle" style="font-size:24px; display:block; margin-bottom:8px;"></i>' +
                    'Something went wrong. Please try again.</div>';
            });
    }

    // Close search on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSearch();
        }
    });

    // Close search when clicking outside
    document.getElementById('searchModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSearch();
        }
    });

    // =============================================================
    // NOTIFICATIONS FUNCTIONS
    // =============================================================

    function toggleNotifications() {
        var dropdown = document.getElementById('notificationsDropdown');
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            loadNotifications();
            dropdown.style.display = 'block';
        }
    }

    // =============================================================
    // ✅ FIXED: loadNotifications() - Added AJAX header
    // =============================================================
    function loadNotifications() {
        fetch('/notifications', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var list = document.getElementById('notificationsList');
                var badge = document.getElementById('notificationBadge');
                
                if (data.unreadCount > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = data.unreadCount;
                } else {
                    badge.style.display = 'none';
                }
                
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div style="padding:30px 20px; text-align:center; color:#94A3B8; font-size:13px;">' +
                        '<i class="ti ti-bell-off" style="font-size:32px; display:block; margin-bottom:10px;"></i>' +
                        'No notifications yet</div>';
                    return;
                }
                
                var html = '';
                for (var i = 0; i < data.notifications.length; i++) {
                    var n = data.notifications[i];
                    var bgColor = n.is_read ? 'transparent' : '#F0FDFA';
                    var iconMap = {
                        'success': 'ti ti-check-circle',
                        'info': 'ti ti-info-circle',
                        'warning': 'ti ti-alert-circle',
                        'danger': 'ti ti-x-circle',
                        'delivered': 'ti ti-package',
                        'assigned': 'ti ti-user-plus',
                        'transit': 'ti ti-truck'
                    };
                    var icon = iconMap[n.type] || 'ti ti-bell';
                    
                    html += '<div style="padding:10px 16px; background:' + bgColor + '; border-bottom:0.5px solid #E2E8F0; cursor:pointer; transition:background 0.2s;"' +
                        ' onclick="markNotificationRead(' + n.id + ')"' +
                        ' onmouseover="this.style.background=\'#F8FAFC\'" onmouseout="this.style.background=\'' + bgColor + '\'">' +
                        '<div style="display:flex; align-items:flex-start; gap:10px;">' +
                        '<i class="' + icon + '" style="color:#14B8A6; font-size:16px; margin-top:2px;"></i>' +
                        '<div style="flex:1;">' +
                        '<div style="font-size:13px; font-weight:500; color:#0F172A;">' + n.title + '</div>' +
                        '<div style="font-size:12px; color:#64748B; margin-top:2px;">' + n.message + '</div>' +
                        '<div style="font-size:10px; color:#94A3B8; margin-top:4px;">' + (n.time_ago || 'Just now') + '</div>' +
                        '</div>' +
                        (n.is_read ? '' : '<div style="width:6px; height:6px; border-radius:50%; background:#14B8A6; flex-shrink:0; margin-top:6px;"></div>') +
                        '</div></div>';
                }
                list.innerHTML = html;
            })
            .catch(function(error) {
                console.error('Error loading notifications:', error);
                var list = document.getElementById('notificationsList');
                list.innerHTML = '<div style="padding:20px; text-align:center; color:#94A3B8; font-size:13px;">' +
                    '<i class="ti ti-alert-circle" style="font-size:24px; display:block; margin-bottom:8px;"></i>' +
                    'Could not load notifications</div>';
            });
    }

    function markNotificationRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(function(error) {
            console.error('Error marking notification as read:', error);
        });
    }

    function markAllRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(function() {
            loadNotifications();
        })
        .catch(function(error) {
            console.error('Error marking all as read:', error);
        });
    }

    // =============================================================
    // ✅ FIXED: updateNotificationBadge() - Added AJAX header
    // =============================================================
    function updateNotificationBadge() {
        fetch('/notifications/unread-count', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var badge = document.getElementById('notificationBadge');
                if (data.count > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = data.count;
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(function(error) {
                console.error('Error updating badge:', error);
            });
    }

    // Check for new notifications every 30 seconds
    setInterval(updateNotificationBadge, 30000);

    // Click outside to close dropdown
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('notificationsDropdown');
        var bell = document.querySelector('.notification-bell');
        if (dropdown && bell && !dropdown.contains(event.target) && !bell.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationBadge();
    });
</script>
@endpush