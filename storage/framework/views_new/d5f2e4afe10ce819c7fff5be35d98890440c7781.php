

<?php $__env->startSection('title', 'My Profile - Tracklane'); ?>
<?php $__env->startSection('page-title', 'My Profile'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width:800px; margin:0 auto;">

        <!-- ============================================================
             HEADER
             ============================================================ -->
        <div style="margin-bottom:16px;">
            <div style="font-size:16px; font-weight:500; color:#0F172A;">👤 My Profile</div>
            <div style="font-size:12px; color:#64748B;">View and update your personal information</div>
        </div>

        <!-- ============================================================
             SUCCESS MESSAGE
             ============================================================ -->
        <?php if(session('success')): ?>
            <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="ti ti-check-circle" style="font-size:18px;"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="ti ti-alert-circle" style="font-size:18px;"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        <!-- ============================================================
             PROFILE CARD
             ============================================================ -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:24px;">

            <!-- Avatar & Name -->
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:0.5px solid #E2E8F0;">
                <div style="width:64px; height:64px; border-radius:50%; background:#14B8A6; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:600; color:#FFFFFF;">
                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                </div>
                <div>
                    <div style="font-size:18px; font-weight:600; color:#0F172A;"><?php echo e(Auth::user()->name); ?></div>
                    <div style="font-size:13px; color:#64748B;">
                        <span style="background:#CCFBF1; color:#0D9488; padding:2px 12px; border-radius:20px; font-size:11px;">
                            <?php echo e(ucfirst(Auth::user()->role)); ?>

                        </span>
                        <span style="margin-left:8px;">
                            Member since <?php echo e(optional(Auth::user()->created_at)->format('M d, Y') ?? 'N/A'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 PROFILE FORM
                 ============================================================ -->
            <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Name -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-user me-1" style="color:#14B8A6;"></i> Full Name
                    </label>
                    <input type="text" 
                           name="name" 
                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           value="<?php echo e(Auth::user()->name); ?>"
                           required
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <?php $__errorArgs = ['name'];
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

                <!-- Email -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-mail me-1" style="color:#14B8A6;"></i> Email Address
                    </label>
                    <input type="email" 
                           name="email" 
                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           value="<?php echo e(Auth::user()->email); ?>"
                           required
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <?php $__errorArgs = ['email'];
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

                <!-- Role (Read Only) -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-user-tag me-1" style="color:#14B8A6;"></i> Role
                    </label>
                    <input type="text" 
                           value="<?php echo e(ucfirst(Auth::user()->role)); ?>" 
                           disabled
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; background:#F8FAFC; color:#64748B;">
                </div>

                <!-- ============================================================
                     CHANGE PASSWORD SECTION
                     ============================================================ -->
                <div style="margin-top:24px; padding-top:20px; border-top:0.5px solid #E2E8F0;">
                    <div style="font-size:14px; font-weight:500; color:#0F172A; margin-bottom:12px;">
                        🔒 Change Password
                    </div>

                    <!-- Current Password -->
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                            Current Password
                        </label>
                        <div style="position:relative;">
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter current password"
                                   style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; padding-right:40px;">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"
                                  onclick="togglePassword('current_password')">
                                <i class="ti ti-eye" id="current_password-icon" style="color:#94A3B8;"></i>
                            </span>
                        </div>
                        <?php $__errorArgs = ['current_password'];
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

                    <!-- New Password -->
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                            New Password
                        </label>
                        <div style="position:relative;">
                            <input type="password" 
                                   name="new_password" 
                                   id="new_password"
                                   class="form-control <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter new password (min 8 characters)"
                                   style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; padding-right:40px;">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"
                                  onclick="togglePassword('new_password')">
                                <i class="ti ti-eye" id="new_password-icon" style="color:#94A3B8;"></i>
                            </span>
                        </div>
                        <?php $__errorArgs = ['new_password'];
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

                    <!-- Confirm Password -->
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                            Confirm New Password
                        </label>
                        <div style="position:relative;">
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   id="confirm_password"
                                   class="form-control" 
                                   placeholder="Confirm new password"
                                   style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px; padding-right:40px;">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"
                                  onclick="togglePassword('confirm_password')">
                                <i class="ti ti-eye" id="confirm_password-icon" style="color:#94A3B8;"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     SUBMIT BUTTONS
                     ============================================================ -->
                <div style="display:flex; gap:12px; margin-top:24px;">
                    <button type="submit" 
                            style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:10px 32px; font-size:14px; font-weight:500; cursor:pointer;">
                        <i class="ti ti-save me-2"></i> Update Profile
                    </button>
                    <a href="<?php echo e(route('dashboard')); ?>" 
                       style="background:#FFFFFF; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:10px 24px; font-size:14px; text-decoration:none;">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            field.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/profile.blade.php ENDPATH**/ ?>