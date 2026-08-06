

<?php $__env->startSection('title', 'Manage Drivers - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Drivers Management'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<style>
    .status-active { background: #D1FAE5; color: #065F46; }
    .status-inactive { background: #FEE2E2; color: #991B1B; }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-unknown { background: #F1F5F9; color: #64748B; }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 24px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #94A3B8;
        padding: 0;
    }
    
    .modal-close:hover { color: #0F172A; }
    
    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 0.5px solid #E2E8F0;
        border-radius: 8px;
        font-size: 13px;
        margin-top: 4px;
        transition: border-color 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #0D9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }
    
    .form-label {
        font-size: 12px;
        font-weight: 500;
        color: #0F172A;
        display: block;
        margin-bottom: 2px;
    }
    
    /* ✅ UNIFORM BUTTON STYLES - Same as Customers Page */
    .btn-approve {
        background: #10B981;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .btn-approve:hover { background: #059669; }
    
    .btn-reject {
        background: #E24B4A;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .btn-reject:hover { background: #C0392B; }
    
    .btn-edit {
        background: #14B8A6;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .btn-edit:hover { background: #0D9488; }
    
    .btn-toggle {
        background: #3B82F6;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .btn-toggle:hover { background: #2563EB; }
    
    .btn-delete {
        background: #E24B4A;
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .btn-delete:hover { background: #C0392B; }
    
    .btn-primary {
        background: #0D9488;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-primary:hover { 
        background: #0F766E; 
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
</style>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- HEADER -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">🚚 Manage Drivers</div>
            <div style="font-size:12px; color:#64748B;">View and manage all drivers in the system</div>
        </div>
        <div style="display:flex; gap:8px;">
            <span style="background:#CCFBF1; color:#0D9488; padding:4px 12px; border-radius:12px; font-size:12px;">
                <i class="fas fa-users" style="font-size:10px;"></i> Total: <?php echo e(isset($drivers) ? $drivers->count() : 0); ?>

            </span>
            <button onclick="openAddDriverModal()" class="btn-primary">
                <i class="fas fa-plus"></i> Add Driver
            </button>
        </div>
    </div>

    <!-- STATS SUMMARY -->
    <?php
        $totalDrivers = isset($drivers) ? $drivers->count() : 0;
        $activeDrivers = isset($drivers) ? $drivers->where('is_available', true)->count() : 0;
        $offlineDrivers = isset($drivers) ? $drivers->where('is_available', false)->count() : 0;
        $withVehicle = isset($drivers) ? $drivers->whereNotNull('vehicle_id')->count() : 0;
    ?>

    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Total Drivers</div>
            <div style="font-size:16px; font-weight:600; color:#0F172A;"><?php echo e($totalDrivers); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;"><i class="fas fa-check-circle" style="color:#10B981;"></i> Active</div>
            <div style="font-size:16px; font-weight:600; color:#10B981;"><?php echo e($activeDrivers); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;"><i class="fas fa-clock" style="color:#F59E0B;"></i> Offline</div>
            <div style="font-size:16px; font-weight:600; color:#94A3B8;"><?php echo e($offlineDrivers); ?></div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;"><i class="fas fa-car" style="color:#3B82F6;"></i> With Vehicle</div>
            <div style="font-size:16px; font-weight:600; color:#3B82F6;"><?php echo e($withVehicle); ?></div>
        </div>
    </div>

    <!-- DRIVERS TABLE -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        
        <?php if(isset($drivers) && $drivers->count() > 0): ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Driver</th>
                        <th style="padding:8px; text-align:left;">Email</th>
                        <th style="padding:8px; text-align:left;">Vehicle</th>
                        <th style="padding:8px; text-align:left;">License</th>
                        <th style="padding:8px; text-align:left;">Phone</th>
                        <th style="padding:8px; text-align:left;">Status</th>
                        <th style="padding:8px; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500; color:#0F172A;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:#0D9488;">
                                    <?php echo e(strtoupper(substr($driver->name ?? 'N/A', 0, 2))); ?>

                                </div>
                                <?php echo e($driver->name ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td style="padding:8px; color:#64748B;"><?php echo e($driver->email ?? 'N/A'); ?></td>
                        <td style="padding:8px; color:#64748B;">
                            <?php if($driver->vehicle): ?>
                                <i class="fas fa-truck" style="color:#3B82F6; font-size:10px;"></i>
                                <?php echo e($driver->vehicle->model ?? 'Vehicle'); ?> 
                                <span style="font-size:10px; color:#94A3B8;">(<?php echo e($driver->vehicle->plate_number ?? 'N/A'); ?>)</span>
                            <?php else: ?>
                                <span style="color:#94A3B8;"><i class="fas fa-times-circle"></i> No vehicle</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:8px; color:#64748B;">
                            <i class="fas fa-id-card" style="font-size:10px;"></i>
                            <?php echo e($driver->license_number ?? 'N/A'); ?>

                        </td>
                        <td style="padding:8px; color:#64748B;">
                            <i class="fas fa-phone" style="font-size:10px;"></i>
                            <?php echo e($driver->phone ?? 'N/A'); ?>

                        </td>
                        <td style="padding:8px;">
                            <?php if($driver->is_available): ?>
                                <span class="status-active" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    <i class="fas fa-circle" style="font-size:8px; color:#10B981;"></i> Active
                                </span>
                            <?php else: ?>
                                <span class="status-inactive" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    <i class="fas fa-circle" style="font-size:8px; color:#E24B4A;"></i> Offline
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:8px; text-align:center;">
                            <div style="display:flex; gap:4px; justify-content:center; flex-wrap:wrap;">
                                <!-- Edit Button -->
                                <button data-driver-id="<?php echo e($driver->id); ?>" 
                                        class="edit-driver-btn btn-edit" 
                                        title="Edit Driver">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                
                                <!-- Toggle Status Button -->
                                <button data-driver-id="<?php echo e($driver->id); ?>" 
                                        class="toggle-driver-btn btn-toggle" 
                                        title="Toggle Status">
                                    <i class="fas fa-toggle-on"></i> Toggle
                                </button>
                                
                                <!-- Delete Button -->
                                <button data-driver-id="<?php echo e($driver->id); ?>" 
                                        class="delete-driver-btn btn-delete" 
                                        title="Delete Driver">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:40px 20px; color:#94A3B8;">
            <i class="fas fa-truck" style="font-size:48px; display:block; margin-bottom:12px; color:#D7DEE6;"></i>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">No Drivers Yet</div>
            <p style="font-size:13px; margin-top:4px;">Click <strong>"Add Driver"</strong> to register a new driver.</p>
            <button onclick="openAddDriverModal()" class="btn-primary" style="margin-top:12px;">
                <i class="fas fa-plus"></i> Add Driver
            </button>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ADD DRIVER MODAL -->
<div id="addDriverModal" style="display:none;">
    <div class="modal-overlay">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:18px; font-weight:600; color:#0F172A;">
                    <i class="fas fa-user-plus" style="color:#0D9488;"></i> Add New Driver
                </span>
                <button onclick="closeAddDriverModal()" class="modal-close">✕</button>
            </div>
            <form id="addDriverForm" method="POST" action="<?php echo e(route('admin.drivers.store')); ?>">
                <?php echo csrf_field(); ?>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label class="form-label"><i class="fas fa-user"></i> Full Name <span style="color:#E24B4A;">*</span></label>
                        <input type="text" name="name" class="form-input" required placeholder="John Doe">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-envelope"></i> Email <span style="color:#E24B4A;">*</span></label>
                        <input type="email" name="email" class="form-input" required placeholder="driver@example.com">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-lock"></i> Password <span style="color:#E24B4A;">*</span></label>
                        <input type="password" name="password" class="form-input" required placeholder="Minimum 8 characters">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                        <input type="text" name="phone" class="form-input" placeholder="+237 690000000">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-id-card"></i> License Number</label>
                        <input type="text" name="license_number" class="form-input" placeholder="LIC-2024-001">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-car"></i> Vehicle Model</label>
                        <input type="text" name="vehicle_model" class="form-input" placeholder="Toyota Hilux">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-license-plate"></i> Plate Number</label>
                        <input type="text" name="plate_number" class="form-input" placeholder="LT-1234">
                    </div>
                    <div style="display:flex; align-items:center;">
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:flex; align-items:center; gap:6px;">
                            <input type="checkbox" name="is_available" value="1" checked> 
                            <i class="fas fa-check-circle" style="color:#10B981;"></i> Available now
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; margin-top:12px; justify-content:center;">
                    <i class="fas fa-user-plus"></i> Create Driver
                </button>
            </form>
        </div>
    </div>
</div>

<!-- EDIT DRIVER MODAL -->
<div id="editDriverModal" style="display:none;">
    <div class="modal-overlay">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:18px; font-weight:600; color:#0F172A;">
                    <i class="fas fa-edit" style="color:#14B8A6;"></i> Edit Driver
                </span>
                <button onclick="closeEditDriverModal()" class="modal-close">✕</button>
            </div>
            <form id="editDriverForm" method="POST" action="">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="driver_id" id="edit_driver_id">
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label class="form-label"><i class="fas fa-user"></i> Full Name <span style="color:#E24B4A;">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-envelope"></i> Email <span style="color:#E24B4A;">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-input">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-id-card"></i> License Number</label>
                        <input type="text" name="license_number" id="edit_license" class="form-input">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-car"></i> Vehicle Model</label>
                        <input type="text" name="vehicle_model" id="edit_vehicle_model" class="form-input">
                    </div>
                    <div>
                        <label class="form-label"><i class="fas fa-license-plate"></i> Plate Number</label>
                        <input type="text" name="plate_number" id="edit_plate_number" class="form-input">
                    </div>
                </div>
                
                <div style="margin-top:12px;">
                    <label style="font-size:12px; font-weight:500; color:#0F172A; display:flex; align-items:center; gap:6px;">
                        <input type="checkbox" name="is_available" id="edit_is_available" value="1"> 
                        <i class="fas fa-check-circle" style="color:#10B981;"></i> Available now
                    </label>
                </div>
                
                <button type="submit" class="btn-primary" style="width:100%; margin-top:12px; justify-content:center;">
                    <i class="fas fa-check"></i> Update Driver
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Font Awesome CDN check - if not loaded, load it
    if (!document.querySelector('link[href*="font-awesome"]')) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';
        document.head.appendChild(link);
    }

    // EVENT LISTENERS
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-driver-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var driverId = this.getAttribute('data-driver-id');
                editDriver(driverId);
            });
        });
        
        document.querySelectorAll('.toggle-driver-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var driverId = this.getAttribute('data-driver-id');
                toggleDriverStatus(driverId);
            });
        });
        
        document.querySelectorAll('.delete-driver-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var driverId = this.getAttribute('data-driver-id');
                deleteDriver(driverId);
            });
        });
    });

    // ADD DRIVER
    function openAddDriverModal() {
        document.getElementById('addDriverModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAddDriverModal() {
        document.getElementById('addDriverModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // EDIT DRIVER
    function editDriver(id) {
        var btn = document.querySelector('.edit-driver-btn[data-driver-id="' + id + '"]');
        var originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading';
            btn.disabled = true;
        }
        
        fetch('/admin/drivers/' + id + '/edit-data', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openEditDriverModal(data.driver);
            } else {
                alert('Error loading driver data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading driver data. Please try again.');
        })
        .finally(() => {
            if (btn) {
                btn.innerHTML = originalHtml || '<i class="fas fa-edit"></i> Edit';
                btn.disabled = false;
            }
        });
    }

    function openEditDriverModal(driver) {
        document.getElementById('editDriverForm').action = '/admin/drivers/' + driver.id;
        document.getElementById('edit_driver_id').value = driver.id;
        document.getElementById('edit_name').value = driver.name || '';
        document.getElementById('edit_email').value = driver.email || '';
        document.getElementById('edit_phone').value = driver.phone || '';
        document.getElementById('edit_license').value = driver.license_number || '';
        document.getElementById('edit_vehicle_model').value = driver.vehicle?.model || '';
        document.getElementById('edit_plate_number').value = driver.vehicle?.plate_number || '';
        document.getElementById('edit_is_available').checked = driver.is_available === 1 || driver.is_available === true;
        
        document.getElementById('editDriverModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEditDriverModal() {
        document.getElementById('editDriverModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // TOGGLE DRIVER STATUS
    function toggleDriverStatus(id) {
        if (confirm('Toggle driver status?')) {
            var btn = document.querySelector('.toggle-driver-btn[data-driver-id="' + id + '"]');
            var originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
            }
            
            fetch('/admin/drivers/' + id + '/toggle-status', {
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
                    alert('Error toggling driver status');
                    if (btn) {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error toggling driver status');
                if (btn) {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
        }
    }

    // DELETE DRIVER
    function deleteDriver(id) {
        if (confirm('Are you sure you want to delete this driver?')) {
            var btn = document.querySelector('.delete-driver-btn[data-driver-id="' + id + '"]');
            var originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
            }
            
            fetch('/admin/drivers/' + id, {
                method: 'DELETE',
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
                    alert('Error deleting driver: ' + (data.message || 'Unknown error'));
                    if (btn) {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting driver');
                if (btn) {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
        }
    }

    // CLOSE MODALS
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddDriverModal();
            closeEditDriverModal();
        }
    });

    document.addEventListener('click', function(e) {
        var addModal = document.getElementById('addDriverModal');
        if (e.target === addModal || e.target.classList.contains('modal-overlay')) {
            closeAddDriverModal();
        }
        var editModal = document.getElementById('editDriverModal');
        if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
            closeEditDriverModal();
        }
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/admin/drivers.blade.php ENDPATH**/ ?>