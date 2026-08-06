

<?php $__env->startSection('title', 'Settings - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Settings'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<style>
    .settings-card {
        background: #FFFFFF;
        border: 0.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 16px;
    }
    .settings-card .card-title {
        font-size: 14px;
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 16px;
    }
    .settings-card .card-title i {
        color: #14B8A6;
        margin-right: 8px;
    }
    .settings-divider {
        border-top: 0.5px solid #E2E8F0;
        margin: 16px 0;
    }
    .form-control-sm-custom {
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        border: 0.5px solid #E2E8F0;
        font-family: 'Inter', sans-serif;
        width: 100%;
        transition: border-color 0.2s;
    }
    .form-control-sm-custom:focus {
        border-color: #14B8A6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20,184,166,0.1);
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider-btn {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #CBD5E1;
        transition: .3s;
        border-radius: 22px;
    }
    .toggle-slider-btn:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background: white;
        transition: .3s;
        border-radius: 50%;
    }
    .toggle-switch input:checked + .toggle-slider-btn {
        background: #0D9488;
    }
    .toggle-switch input:checked + .toggle-slider-btn:before {
        transform: translateX(18px);
    }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width: 900px; margin: 0 auto;">

        <!-- ============================================================
             HEADER
             ============================================================ -->
        <div style="margin-bottom:20px;">
            <div style="font-size:20px; font-weight:600; color:#0F172A;">⚙️ Settings</div>
            <div style="font-size:13px; color:#64748B; margin-top:4px;">Manage your account preferences and system settings</div>
        </div>

        <?php if(session('success')): ?>
            <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="ti ti-check-circle" style="font-size:18px;"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <i class="ti ti-alert-circle" style="font-size:16px;"></i>
                        <span><?php echo e($error); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <!-- ============================================================
             SECTION 1: PROFILE SETTINGS
             ============================================================ -->
        <div class="settings-card">
            <div class="card-title"><i class="ti ti-user"></i> Profile Settings</div>
            
            <form method="POST" action="<?php echo e(route('settings.update-profile')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Full Name</label>
                        <input type="text" name="name" class="form-control-sm-custom" value="<?php echo e(Auth::user()->name); ?>" required>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Email Address</label>
                        <input type="email" name="email" class="form-control-sm-custom" value="<?php echo e(Auth::user()->email); ?>" required>
                    </div>
                </div>

                <div class="settings-divider"></div>

                <div style="font-size:13px; font-weight:500; color:#0F172A; margin-bottom:12px;">Change Password</div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Current Password</label>
                        <input type="password" name="current_password" class="form-control-sm-custom" placeholder="Enter current password">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">New Password</label>
                        <input type="password" name="new_password" class="form-control-sm-custom" placeholder="Enter new password">
                    </div>
                </div>

                <button type="submit" style="margin-top:12px; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-save me-1"></i> Update Profile
                </button>
            </form>
        </div>

        <!-- ============================================================
             SECTION 2: NOTIFICATION PREFERENCES
             ============================================================ -->
        <div class="settings-card">
            <div class="card-title"><i class="ti ti-bell"></i> Notification Preferences</div>
            
            <form method="POST" action="<?php echo e(route('settings.update-notifications')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:4px 0;">
                        <div>
                            <div style="font-size:13px; color:#0F172A;">Order Updates</div>
                            <div style="font-size:11px; color:#64748B;">Receive notifications about your orders</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="order_updates" checked>
                            <span class="toggle-slider-btn"></span>
                        </label>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:4px 0;">
                        <div>
                            <div style="font-size:13px; color:#0F172A;">Promotions & Offers</div>
                            <div style="font-size:11px; color:#64748B;">Receive promotional emails and offers</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="promotions">
                            <span class="toggle-slider-btn"></span>
                        </label>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:4px 0;">
                        <div>
                            <div style="font-size:13px; color:#0F172A;">System Announcements</div>
                            <div style="font-size:11px; color:#64748B;">Important system updates and announcements</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="system_announcements" checked>
                            <span class="toggle-slider-btn"></span>
                        </label>
                    </div>
                </div>

                <button type="submit" style="margin-top:12px; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-save me-1"></i> Save Preferences
                </button>
            </form>
        </div>

        <!-- ============================================================
             SECTION 3: LANGUAGE & REGION
             ============================================================ -->
        <div class="settings-card">
            <div class="card-title"><i class="ti ti-world"></i> Language & Region</div>
            
            <form method="POST" action="<?php echo e(route('settings.update-language')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Language</label>
                        <select name="language" class="form-control-sm-custom">
                            <option value="en" <?php echo e(session('locale') == 'en' ? 'selected' : ''); ?>>🇬🇧 English</option>
                            <option value="fr" <?php echo e(session('locale') == 'fr' ? 'selected' : ''); ?>>🇫🇷 Français</option>
                            <option value="es" <?php echo e(session('locale') == 'es' ? 'selected' : ''); ?>>🇪🇸 Español</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Timezone</label>
                        <select name="timezone" class="form-control-sm-custom">
                            <option value="UTC">UTC</option>
                            <option value="Africa/Douala">Africa/Douala</option>
                            <option value="Africa/Lagos">Africa/Lagos</option>
                            <option value="Europe/Paris">Europe/Paris</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Currency</label>
                        <select name="currency" class="form-control-sm-custom">
                            <option value="FCFA">FCFA (Franc CFA)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="margin-top:12px; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-save me-1"></i> Save Preferences
                </button>
            </form>
        </div>

        <!-- ============================================================
             SECTION 4: DRIVER SETTINGS (Driver Only)
             ============================================================ -->
        <?php if(Auth::user()->isDriver() && Auth::user()->driver): ?>
        <div class="settings-card" style="border-left: 3px solid #14B8A6;">
            <div class="card-title"><i class="ti ti-truck"></i> 🚚 Driver Settings</div>
            
            <form method="POST" action="<?php echo e(route('settings.update-driver')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Phone Number</label>
                        <input type="text" name="phone" class="form-control-sm-custom" value="<?php echo e(Auth::user()->driver->phone ?? ''); ?>" placeholder="6 77 12 34 56">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Vehicle Model</label>
                        <input type="text" name="vehicle_model" class="form-control-sm-custom" value="<?php echo e(Auth::user()->driver->vehicle->model ?? ''); ?>" placeholder="Honda CB125">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Plate Number</label>
                        <input type="text" name="vehicle_plate" class="form-control-sm-custom" value="<?php echo e(Auth::user()->driver->vehicle->plate_number ?? ''); ?>" placeholder="KJ 412 X">
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px; margin-top:12px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_available" <?php echo e(Auth::user()->driver->is_available ? 'checked' : ''); ?>>
                        <span class="toggle-slider-btn"></span>
                    </label>
                    <span style="font-size:13px; color:#0F172A;">Available for deliveries</span>
                </div>

                <button type="submit" style="margin-top:12px; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-save me-1"></i> Save Driver Settings
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- ============================================================
             SECTION 5: ADMIN SETTINGS (Admin Only)
             ============================================================ -->
        <?php if(Auth::user()->isAdmin()): ?>
        <div class="settings-card" style="border-left: 3px solid #E24B4A;">
            <div class="card-title"><i class="ti ti-crown"></i> 👑 Admin Settings</div>
            
            <form method="POST" action="<?php echo e(route('settings.update-admin')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">System Name</label>
                        <input type="text" name="system_name" class="form-control-sm-custom" value="Tracklane" placeholder="System Name">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">Default Currency</label>
                        <select name="default_currency" class="form-control-sm-custom">
                            <option value="FCFA">FCFA</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="margin-top:12px; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-save me-1"></i> Save Admin Settings
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/settings/index.blade.php ENDPATH**/ ?>